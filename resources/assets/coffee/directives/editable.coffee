angular.module('Egecms').directive 'editable', ->
    restrict: 'A'
    link: ($scope, $element, $attrs) ->
        $element.on 'click', (event) ->
            $element.attr('contenteditable', 'true').focus()
        .on 'dblclick', (event) ->
            $element.attr('contenteditable', 'true').focus()
            if $attrs.hasOwnProperty 'withDblclick'
                elem = $ event.target
                text = elem.text()

                rng = document.createRange();
                rng.selectNode elem[0]
                sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange rng

        .on 'keydown', (event) ->
            if event.keyCode in [13, 27]
                event.preventDefault()
                $element.blur()

            if $element.data 'input-digits-only'
                event.preventDefault() if not (event.keyCode < 57)

        .on 'blur', (event) ->
            $scope.onEdit($element.attr('editable'), event)
            $element.removeAttr('contenteditable')
