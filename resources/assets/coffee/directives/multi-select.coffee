angular.module 'Egecms'
    .directive 'ngMulti', ->
        restrict: 'E'
        replace: true
        scope:
            object: '='
            model: '='
            label: '@'
            noneText: '@'
        templateUrl: 'directives/ngmulti'
        controller: ($scope, $element, $attrs, $timeout) ->
            $element.selectpicker
                noneSelectedText: $scope.noneText

            $scope.$watchGroup ['model', 'object'], (newVal) ->
                $element.selectpicker 'refresh' if newVal
