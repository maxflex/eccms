angular.module 'Egecms'
.directive 'programmItem', ->
    restrict: 'E'
    templateUrl: 'directives/programm-item'
    scope:
        item:   '='
        level:  '=?'
        levelstring: '='
        delete: '&delete'
    controller: ($timeout, $element, $scope) ->
        $scope.onEdit = (item, event) ->
            value = $(event.target).text().trim()
            if value
                $scope.item.title = value
            else
                $(event.target).text $scope.item.title

        $scope.addChild = ->
            $scope.is_adding = true
            $timeout ->
                $element.find("input").last().focus()

        $scope.createChild = (event) ->
            if $scope.new_item.title and event?.keyCode is 13
                $scope.item.content = [] if not $scope.item.content
                if $scope.new_item.title.length
                    $scope.item.content.push $scope.new_item
                    resetNewItem()

            if event?.keyCode is 27
                $(event.target).blur()

        $scope.deleteChild = (child) ->
            $scope.item.content = _.without $scope.item.content, child

        $scope.blur = ->
            $scope.is_adding = false
            $scope.is_editing = false

        $scope.getChildLevelString = (child_index) ->
            str = if $scope.levelstring then $scope.levelstring else ''
            str + (child_index + 1) + '.'

        resetNewItem = ->
            $scope.new_item = {title: '', content: []}

        $scope.level = 1 if not $scope.level
        resetNewItem()