angular
.module 'Egecms'
.controller 'SassIndex', ($scope, $attrs, $http, IndexService, Sass) ->
    bindArguments $scope, arguments

    $scope.getName = (path) ->
        path.split('/').slice(-1)[0]

    angular.element(document).ready ->
        $http.get "api" + $scope.current_path, {}
        .then (response) ->
            $scope.data = response.data
        # IndexService.init Sass, $scope.current_page, $attrs

.controller 'SassForm', ($scope, $rootScope, $http, $timeout, FormService, AceService, Sass) ->
    bindArguments $scope, arguments
    $rootScope.frontend_loading = true
    angular.element(document).ready ->
        $http.get('api/sass/' + $scope.file).then (response) ->
            $scope.text = response.data
            $timeout ->
                $rootScope.frontend_loading = false
                $scope.editor = ace.edit('editor')
                $scope.editor.getSession().setMode('ace/mode/css')
                $scope.editor.getSession().setUseWrapMode(true)
                $scope.editor.setOptions
                    minLines: 20
                    maxLines: Infinity


    $scope.save = ->
        ajaxStart()
        $http.post('api/sass/' + $scope.file, {text: $scope.editor.getValue()}).then -> ajaxEnd()