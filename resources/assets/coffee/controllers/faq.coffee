angular
    .module 'Egecms'
    .controller 'FaqIndex', ($scope, $rootScope, $attrs, $timeout, Faq, FaqGroup) ->
        bindArguments($scope, arguments)

        $scope.sortableFaqConf =
            animation: 150
            onUpdate: (event) ->
                angular.forEach event.models, (obj, index) ->
                    Faq.update({id: obj.id, position: index})

        $scope.sortableGroupConf =
            animation: 150
            onStart: (event) ->
                $scope.group_sorting  = true
            onUpdate: (event) ->
                $scope.group_sorting  = false
                angular.forEach event.models, (obj, index) ->
                    FaqGroup.update({id: obj.id, position: index})

        $scope.dnd = {}

        $scope.dragStart = (faq_id) ->
            $timeout ->
                console.log('drag start', faq_id)
                $scope.dnd.faq_id = faq_id

        $scope.drop = (group_id) ->
            faq_id = $scope.dnd.faq_id
            if group_id isnt $scope.getGroup(faq_id).id
                if group_id is -1
                    FaqGroup.save {faq_id: faq_id}, (response) ->
                        $scope.groups.push(response)
                        moveToGroup(faq_id, response.id)
                else if group_id
                    Faq.update({id: faq_id, group_id: group_id})
                    moveToGroup(faq_id, group_id)
            $scope.dnd = {}

        # переместить в группу
        moveToGroup = (faq_id, group_id) ->
            group_to = $rootScope.findById($scope.groups, group_id)
            group_from = $scope.getGroup(faq_id)
            faq = $rootScope.findById(group_from.faq, faq_id)
            group_from.faq = removeById(group_from.faq, faq_id)
            group_to.faq.push(faq)



        $scope.getGroup = (faq_id) ->
            group_found = null
            $scope.groups.forEach (group) ->
                return if group_found isnt null
                group.faq.forEach (faq) ->
                    if faq.id is parseInt(faq_id)
                        group_found = group
                        return
            group_found

        $scope.getFaq = (faq_id) ->
            $rootScope.findById($scope.getGroup(faq_id).faq, faq_id)

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    FaqGroup.remove {id: group.id}
                    $scope.groups = removeById($scope.groups, group.id)

        $scope.onEdit = (id, event) ->
            FaqGroup.update {id: id, title: $(event.target).text()}

    .controller 'FaqForm', ($scope, $attrs, $timeout, FormService, AceService, Faq) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Faq, $scope.id, $scope.model)
