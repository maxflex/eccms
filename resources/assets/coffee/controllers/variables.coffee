angular
    .module 'Egecms'
    .controller 'VariablesIndex', ($scope, $attrs, $timeout, IndexService, Variable, VariableGroup) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(Variable, $scope.current_page, $attrs)

        $scope.dnd = {}

        $scope.dragStart = (variable_id) ->
            $timeout ->
                console.log('drag start', variable_id)
                $scope.dnd.variable_id = variable_id

        $scope.drop = (group_id) ->
            if group_id is -1
                variable_id = $scope.dnd.variable_id
                VariableGroup.save {variable_id: variable_id}, (response) ->
                    $scope.groups.push(response)
                    IndexService.page.data.find (variable) ->
                        variable.id is variable_id
                    .group_id = response.id
            else if group_id
                Variable.update({id: $scope.dnd.variable_id, group_id: group_id})
                IndexService.page.data.find (variable) ->
                    variable.id is $scope.dnd.variable_id
                .group_id = group_id
            $scope.dnd = {}

        $scope.getVariables = (group_id) ->
            if IndexService.page then IndexService.page.data.filter (d) ->
                d.group_id is group_id

        $scope.getVariable = (variable_id) ->
            _.findWhere(IndexService.page.data, {id: parseInt(variable_id)})

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    VariableGroup.remove {id: group.id}
                    $scope.groups = removeById($scope.groups, group.id)
                    _.where(IndexService.page.data, {group_id: group.id}).forEach (variable) ->
                        variable.group_id = null

        $scope.onEdit = (id, event) ->
            VariableGroup.update {id: id, title: $(event.target).text()}

    .controller 'VariablesForm', ($scope, $attrs, $timeout, FormService, AceService, Variable) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Variable, $scope.id, $scope.model)
            FormService.dataLoaded.promise.then ->
                AceService.initEditor(FormService, 30)
            FormService.beforeSave = ->
                FormService.model.html = AceService.editor.getValue()
