angular
.module 'Egecms'
    .controller 'ProgramsIndex', ($scope, $attrs, IndexService, Program) ->
        bindArguments $scope, arguments
        angular.element(document).ready ->
            IndexService.init(Program, $scope.current_page, $attrs)

        $scope.childLessonSum = (model) ->
            return 0 if not (model and model.content)

            return +model.lesson_count || 0 if not model.content.length

            return _.reduce model.content, (sum, value) ->
                sum + parseInt $scope.childLessonSum value
            , 0


    .controller 'ProgramsForm', ($scope, $attrs, $timeout, FormService, Program) ->
        bindArguments $scope, arguments
        angular.element(document).ready ->
            FormService.init(Program, $scope.id, $scope.model)