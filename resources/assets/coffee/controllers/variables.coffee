angular
    .module 'Egecms'
    .controller 'VariablesIndex', ($scope, $attrs, $rootScope, $timeout, IndexService, Variable, VariableGroup) ->
        l = (e) -> console.log e
#        $scope.$watchCollection 'dnd', (newVal) ->
#            console.log newVal
        bindArguments($scope, arguments)

        $scope.sortableVariableConf =
            animation:  150
            group:
                name:   'variable'
                put:    'variable'

            onUpdate: (event) ->
                angular.forEach event.models, (obj, index) ->
                    Variable.update({id: obj.id, position: index})
            onAdd: (event) ->
                l 'add', event

            onRemove: (event) ->
                l 'rem', event

            onMove: (event) ->
                l 'move'

        $scope.sortableGroupConf =
            animation: 150
            handle: '.group-title'
            onUpdate: (event) ->
                l 'g upd'

            onStart: (event) ->
                l 'g start'

            onAdd: (event) ->
                l 'g add'

            onRemove: (event) ->
                l 'g rem'

            onMove: (event) ->
                l 'g move'

        $scope.dnd = {}

        $scope.dragStart = (variable_id) ->
#            $timeout ->
#                console.log('drag start', variable_id)
#                $scope.dnd.variable_id = variable_id

        $scope.drop = (group_id) ->
#            variable_id = $scope.dnd.variable_id
#            if group_id and variable_id and (group_id isnt $scope.getGroup(variable_id).id)
#                if group_id is -1
#                    VariableGroup.save {variable_id: variable_id}, (response) ->
#                        $scope.groups.push(response)
#                        moveToGroup(variable_id, response.id)
#                else if group_id
#                    Variable.update({id: $scope.dnd.variable_id, group_id: group_id})
#                    moveToGroup(variable_id, group_id)
#            $scope.dnd = {}
#            console.log 'handy'

        # переместить в группу
        moveToGroup = (variable_id, group_id) ->
            group_to = $rootScope.findById($scope.groups, group_id)
            group_from = $scope.getGroup(variable_id)
            variable = $rootScope.findById(group_from.variable, variable_id)
            group_from.variable = removeById(group_from.variable, variable_id)
            variable.group_id = group_id
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
