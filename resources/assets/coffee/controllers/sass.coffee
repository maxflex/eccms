angular
.module 'Egecms'
.controller 'SassIndex', ($scope, $attrs, IndexService, Sass) ->
    bindArguments $scope, arguments
    angular.element(document).ready ->
        IndexService.init Sass, $scope.current_page, $attrs

.controller 'SassForm', ($scope, FormService, AceService, Sass) ->
    bindArguments $scope, arguments
    angular.element(document).ready ->
        FormService.init Sass, $scope.id, $scope.model
        FormService.dataLoaded.promise.then ->
            AceService.initEditor FormService, 30, 'editor', 'ace/mode/css'
        FormService.beforeSave = ->
            FormService.model.text = AceService.editor.getValue()
