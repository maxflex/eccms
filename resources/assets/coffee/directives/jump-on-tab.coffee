angular.module('Egecms').directive 'jumpOnTab', ->
    restrict: 'A'
    link: ($scope, $element) ->
        $element.on 'keydown', (event) ->
            if event.keyCode is 9
                event.preventDefault()
                $(event.target).parent('h2').find('.lesson_count').trigger('click').trigger('focus');
