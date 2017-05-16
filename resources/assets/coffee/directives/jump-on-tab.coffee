angular.module('Egecms').directive 'jumpOnTab', ->
    restrict: 'A'
    link: ($scope, $element, $attr) ->
        $element.on 'keydown', (event) ->
            if event.keyCode is 9
                event.preventDefault()
                next_node = $(event.target).parents('h2').first().find('.' + $attr.jumpOnTab);
                next_node = next_node.trigger('click').trigger('focus')
                focused_item = next_node[0]
                if focused_item.childNodes.length
                    range = document.createRange()
                    range.setStart focused_item.childNodes[0], focused_item.innerText.length
                    range.collapse true
                    window.getSelection().removeAllRanges()
                    window.getSelection().addRange range
