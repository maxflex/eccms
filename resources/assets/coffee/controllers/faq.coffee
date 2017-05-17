angular
    .module 'Egecms'
    .controller 'FaqIndex', ($scope, $rootScope, $attrs, $timeout, Faq, FaqGroup) ->
        l = (e) -> console.log e

        angular.element(document).ready ->
            $(document).scroll (event) ->
                if $(document).scrollTop() + $(window).height() is $(document).height()
                    $(document).scrollTop($(document).height() - 50)
                    l 'scrolled back'

        $scope.$watchCollection 'dnd', (newVal) ->
            l $scope.dnd

        bindArguments($scope, arguments)

        updatePositions = (group_ids) ->
            group_ids = [group_ids] if not _.isArray group_ids
            angular.forEach group_ids, (group_id) ->
                group = $rootScope.findById $scope.groups, group_id
                angular.forEach group.faq, (faq, index) ->
                    Faq.update({id: faq.id, position: index})

        dragEnd = ->
            $scope.dnd = {}

        $scope.sortableFaqConf =
            animation:  150
            group:
                name:   'variable'
                put:    'variable'
            fallbackTolerance: 5

            onUpdate: (event) ->
                updatePositions $scope.dnd.group_id

            onAdd: (event) ->
                faq_id = $scope.dnd.faq_id
                if $scope.dnd.group_id and $scope.dnd.faq_id and ($scope.dnd.group_id isnt $scope.dnd.old_group_id)
                   if $scope.dnd.group_id is -1
                       FaqGroup.save {faq_id: $scope.dnd.faq_id}, (response) ->
                           $scope.groups.push(response)
                           moveToGroup $scope.dnd.faq_id, response.id, $scope.dnd.old_group_id, true
                           dragEnd()
                   else if $scope.dnd.group_id
                       moveToGroup $scope.dnd.faq_id, $scope.dnd.group_id, $scope.dnd.old_group_id
                       updatePositions [$scope.dnd.group_id, $scope.dnd.old_group_id]

            onEnd: (event) ->
                dragEnd() if $scope.dnd.group_id isnt -1

        $scope.dragOver = (group) ->
            $scope.dnd.group_id = group.id if $scope.dnd.type isnt 'group'

        $scope.sortableGroupConf =
            animation: 150
            handle: '.group-title'
            dragClass: 'dragging-group'
            onUpdate: (event) ->
                angular.forEach $scope.groups, (group, index) ->
                    group.position = index
                    FaqGroup.update({id: group.id, position: index})

            onStart: (event) ->
                $scope.dnd.type = 'group'

            onEnd: (event) ->
                $scope.dnd = {}

        $scope.dnd = {}

        # переместить в группу
        moveToGroup = (faq_id, group_id, old_group_id, copy_item = false) ->
            Faq.update id: faq_id, group_id: group_id

            group_from = _.find $scope.groups, id: old_group_id
            faq = _.clone findById(group_from.faq, faq_id)
            faq.group_id = group_id

            group_from.faq = removeById(group_from.faq, faq_id)
            group_to = _.find $scope.groups, id: group_id

            if copy_item
                group_to.faq.push faq
            else
                faq = $rootScope.findById(group_to.faq, faq_id)
                faq.group_id = group_id

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    FaqGroup.remove {id: group.id}
                    new_group_id = (_.max _.without($scope.groups, group), (group) -> group.position).id

                    if group.faq
                        angular.forEach group.faq, (faq) ->
                            moveToGroup faq.id, new_group_id, faq.group_id, true
                        updatePositions new_group_id

                    $scope.groups = removeById $scope.groups, group.id

        $scope.onEdit = (id, event) ->
            FaqGroup.update {id: id, title: $(event.target).text()}

    .controller 'FaqForm', ($scope, $attrs, $timeout, FormService, AceService, Faq) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Faq, $scope.id, $scope.model)
