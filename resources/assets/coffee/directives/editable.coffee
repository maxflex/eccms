angular.module('Egecms').directive 'editable', ->
    restrict: 'A'
    link: ($scope, $element, $attrs) ->
        $element.on 'click', (event) ->
            $element.attr('contenteditable', 'true').focus()
        .on 'keydown', (event) ->
            if event.keyCode in [13, 27]
                event.preventDefault()
                $element.blur()
        .on 'blur', (event) ->
            $scope.onEdit($element.attr('editable'), event)
            $element.removeAttr('contenteditable')
