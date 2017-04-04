angular
.module 'Egecms'
    .controller 'ProgrammsIndex', ($scope, $attrs, IndexService, Programm) ->
        bindArguments $scope, arguments
        angular.element(document).ready ->
            IndexService.init(Programm, $scope.current_page, $attrs)

    .controller 'ProgrammsForm', ($scope, $attrs, $timeout, FormService, Programm) ->
        bindArguments $scope, arguments
        angular.element(document).ready ->
            FormService.init(Programm, $scope.id, $scope.model)