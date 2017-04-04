angular
    .module 'Egecms'
    .controller 'SearchIndex', ($scope, $attrs, $timeout, IndexService, Page, Published, ExportService) ->
        bindArguments($scope, arguments)
        ExportService.init({controller: 'pages'})

        angular.element(document).ready ->
            IndexService.init(Page, $scope.current_page, $attrs, false)
