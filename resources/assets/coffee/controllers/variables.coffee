angular
    .module 'Egecms'
    .controller 'VariablesIndex', ($scope, $attrs, $rootScope, $timeout, IndexService, Variable, VariableGroup) ->
        bindArguments($scope, arguments)
        $scope.sortableVariableConf =
            animation: 150
            onUpdate: (event) ->
                angular.forEach event.models, (obj, index) ->
                    Variable.update({id: obj.id, position: index})

        $scope.sortableGroupConf =
            animation: 150
            onStart: (event) ->
                $scope.group_sorting = true
            onUpdate: (event) ->
                $scope.group_sorting = false
                angular.forEach event.models, (obj, index) ->
                    VariableGroup.update({id: obj.id, position: index})

        $scope.dnd = {}

        $scope.dragStart = (variable_id) ->
            $timeout ->
                console.log('drag start', variable_id)
                $scope.dnd.variable_id = variable_id

        $scope.drop = (group_id) ->
            variable_id = $scope.dnd.variable_id
            if group_id is -1
                VariableGroup.save {variable_id: variable_id}, (response) ->
                    $scope.groups.push(response)
                    moveToGroup(variable_id, response.id)
            else if group_id
                Variable.update({id: $scope.dnd.variable_id, group_id: group_id})
                moveToGroup(variable_id, group_id)
            $scope.dnd = {}

        # переместить в группу
        moveToGroup = (variable_id, group_id) ->
            group_to = $rootScope.findById($scope.groups, group_id)
            group_from = $scope.getGroup(variable_id)
            variable = $rootScope.findById(group_from.variable, variable_id)
            group_from.variable = removeById(group_from.variable, variable_id)
            group_to.variable.push(variable)

        $scope.getGroup = (variable_id) ->
            group_found = null
            $scope.groups.forEach (group) ->
                return if group_found isnt null
                group.variable.forEach (variable) ->
                    if variable.id is parseInt(variable_id)
                        group_found = group
                        return
            group_found

        $scope.getVariable = (variable_id) ->
            $rootScope.findById($scope.getGroup(variable_id).variable, variable_id)

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    VariableGroup.remove {id: group.id}
                    $scope.groups = removeById($scope.groups, group.id)

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
