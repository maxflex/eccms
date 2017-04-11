angular
    .module 'Egecms'
    .controller 'FaqIndex', ($scope, $attrs, $timeout, IndexService, Faq, VariableGroup) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(Faq, $scope.current_page, $attrs)

    .controller 'FaqForm', ($scope, $attrs, $timeout, FormService, AceService, Faq) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Faq, $scope.id, $scope.model)
