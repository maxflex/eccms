angular
.module 'Egecms'
    .controller 'ProgramsIndex', ($scope, $attrs, IndexService, Program) ->
        bindArguments $scope, arguments
        angular.element(document).ready ->
            IndexService.init(Program, $scope.current_page, $attrs)

    .controller 'ProgramsForm', ($scope, $attrs, $timeout, FormService, Program) ->
        bindArguments $scope, arguments
        angular.element(document).ready ->
            FormService.init(Program, $scope.id, $scope.model)