angular
    .module 'Egecms'
    .controller 'VariablesIndex', ($scope, $attrs, $rootScope, $timeout, IndexService, Variable, VariableGroup) ->
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
                angular.forEach group.variable, (variable, index) ->
                    Variable.update({id: variable.id, position: index})

        dragEnd = ->
            $scope.dnd = {}

        $scope.sortableVariableConf =
            animation:  150
            group:
                name:   'variable'
                put:    'variable'
            fallbackTolerance: 5

            onUpdate: (event) ->
                updatePositions $scope.dnd.group_id

            onAdd: (event) ->
                variable_id = $scope.dnd.variable_id
                if $scope.dnd.group_id and $scope.dnd.variable_id and ($scope.dnd.group_id isnt $scope.dnd.old_group_id)
                   if $scope.dnd.group_id is -1
                       VariableGroup.save {variable_id: $scope.dnd.variable_id}, (response) ->
                           $scope.groups.push(response)
                           moveToGroup $scope.dnd.variable_id, response.id, $scope.dnd.old_group_id, true
                           dragEnd()
                   else if $scope.dnd.group_id
                       moveToGroup $scope.dnd.variable_id, $scope.dnd.group_id, $scope.dnd.old_group_id
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
                    VariableGroup.update({id: group.id, position: index})

            onStart: (event) ->
                $scope.dnd.type = 'group'

            onEnd: (event) ->
                $scope.dnd = {}

        $scope.dnd = {}

        # переместить в группу
        moveToGroup = (variable_id, group_id, old_group_id, copy_item = false) ->
            Variable.update id: variable_id, group_id: group_id

            group_from = _.find $scope.groups, id: old_group_id
            variable = _.clone findById(group_from.variable, variable_id)
            variable.group_id = group_id

            group_from.variable = removeById(group_from.variable, variable_id)
            group_to = _.find $scope.groups, id: group_id

            if copy_item
                group_to.variable.push variable
            else
                variable = $rootScope.findById(group_to.variable, variable_id)
                variable.group_id = group_id

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    VariableGroup.remove {id: group.id}
                    new_group_id = (_.max _.without($scope.groups, group), (group) -> group.position).id

                    if group.variable
                        angular.forEach group.variable, (variable) ->
                            moveToGroup variable.id, new_group_id, variable.group_id, true
                        updatePositions new_group_id

                    $scope.groups = removeById $scope.groups, group.id

        $scope.onEdit = (id, event) ->
            VariableGroup.update {id: id, title: $(event.target).text()}

    .controller 'VariablesForm', ($scope, $attrs, $timeout, FormService, AceService, Variable) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Variable, $scope.id, $scope.model)
            FormService.dataLoaded.promise.then ->
                AceService.initEditor(FormService, 30)
                AceService.editor.getSession().setMode('ace/mode/json') if FormService.model.html[0] is '{'
            FormService.beforeSave = ->
                FormService.model.html = AceService.editor.getValue()
