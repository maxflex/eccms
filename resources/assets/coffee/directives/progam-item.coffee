TAB = 9

angular.module 'Egecms'
.directive 'programItem', ->
    restrict: 'E'
    templateUrl: 'directives/program-item'
    scope:
        item:   '='
        level:  '=?'
        levelstring: '='
        delete: '&delete'
    controller: ($timeout, $element, $scope) ->
        $scope.edit = ->
            $scope.editing = true


        $scope.fake_id = 0
        $scope.onEdit = (field, event) ->
            elem = $(event.target)
            value = elem.text().trim()
            $scope.$apply ->
                $scope.item[field] = value

        $scope.editKeydown = (event)->
            elem = $(event.target)
            if event?.keyCode in [13, 27]
                event.preventDefault()
                elem.blur()

            if elem.data 'input-digits-only'
                event.preventDefault() if not (event.keyCode < 57)

        $scope.addChild = (event)->
            $scope.is_adding = true
            $timeout ->
                $(event.target).parents('li').first().find('input.add-item-title').last().focus()

        $scope.createChild = (event) ->
            if event?.keyCode is 13
                event.preventDefault()
                if $scope.new_item.title
                    $scope.item.content = [] if not $scope.item.content
                    if $scope.new_item.title.length
                        $scope.item.content.push $scope.new_item
                        resetNewItem(event)

                        $scope.addChild(event)

            if event.keyCode is 27
                event.preventDefault()
                $(event.target).blur()

            if event.keyCode is TAB
                $scope.is_tabbing = true if $(event.target).is ':not(.add-item-lesson-count)'

        $scope.deleteChild = (child) ->
            $scope.item.content = _.without $scope.item.content, child

        $scope.blur = (event)->
            if $scope.is_tabbing
                $scope.is_tabbing = false
                return event.preventDefault()

            $scope.is_adding = false
            $scope.is_editing = false

        $scope.focus = ->
            $scope.is_adding = true

        $scope.getChildLevelString = (child_index) ->
            str = if $scope.levelstring then $scope.levelstring else ''
            str + (child_index + 1) + '.'

        $scope.getLessonCount = ->
            return $scope.item.lesson_count

        $scope.childLessonSum = (item) ->
            return 0 if not (item and item.content)

            return +item.lesson_count || 0 if not item.content.length

            return _.reduce item.content, (sum, value) ->
                        sum + parseInt $scope.childLessonSum value
                    , 0

        resetNewItem = (event) ->
            $scope.new_item = {title: '', lesson_count: '', child_lesson_sum: '', content: [], fake_id: $scope.fake_id}
            $scope.fake_id++

        $scope.level = 1 if not $scope.level
        $scope.lesson_count = 0 if not $scope.lesson_count
        resetNewItem()

        $scope.editBlur = ->
            $scope.editing = false

        $scope.editFocus = ->
            $scope.editing = true
