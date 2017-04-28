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
        $scope.fake_id = 0
        $scope.onEdit = (item, event) ->
            elem = $(event.target)
            value = elem.text().trim()
            console.log value
            field = elem.data 'field'
            if value or elem.data 'not-required'
                if elem.data('positive')
                    value = value.replace /[^0-9]/g, ''
                    console.log 'entered' + value
                    value = '' if not value or not valu e > 0
                $scope.item[field] = value
            else
                $(event.target).text $scope.item.title

            $scope.hideEmptyLesson()
            require_update and $timeout -> $scope.$apply()

        $scope.addChild = (event)->
            $scope.is_adding = true
            $timeout ->
                if $scope.item.content?.length
                    $(event.target).parents('li').first().find('input.title').last().focus()
                else
                    $(event.target).parents('li').first().find('input.title').last().focus()

        $scope.createChild = (event) ->
            if event?.keyCode is 13
                event.preventDefault()
                if $scope.new_item.title
                    $scope.item.content = [] if not $scope.item.content
                    if $scope.new_item.title.length
                        $scope.item.content.push $scope.new_item
                        resetNewItem(event)
                        if $(event.target).is(':not(.title)')
                            nextField(event)

            if event?.keyCode is 27
                event.preventDefault()
                $(event.target).blur()

            if event?.keyCode is TAB
                event.preventDefault()
                nextField event

        $scope.deleteChild = (child) ->
            $scope.item.content = _.without $scope.item.content, child

        $scope.blur = (event)->
            if $scope.is_tabbing
                $scope.is_tabbing = false
                return event.preventDefault()

            return event.preventDefault() if event and event.keyCode is TAB and not force

            console.log 'blurring'
            $scope.is_adding = false
            $scope.is_editing = false

        nextField = (event) ->
            $scope.is_tabbing = true

            elem = $(event.target)
            context = $(elem.parents('li')[0])
            if elem.is('.title')
                $('.lesson-count', context).focus()
            else
                $('.title', context).focus()
            elem.is('.lesson-count')

        $scope.getChildLevelString = (child_index) ->
            str = if $scope.levelstring then $scope.levelstring else ''
            str + (child_index + 1) + '.'

        $scope.childLessonSum = (item) ->
            return 0 if not (item and item.content)

            return +item.lesson_count || 0 if not item.content.length

            return _.reduce item.content, (sum, value) ->
                        sum + parseInt $scope.childLessonSum value
                    , 0

        resetNewItem = (event) ->
            $scope.new_item = {title: '', lesson_count: '', child_lesson_sum: '', content: [], fake_id: $scope.fake_id}
            $scope.fake_id++

        $scope.showLesson = ->
            $scope.show_lessons = [] if not $scope.show_lessons
            $scope.show_lessons[$scope.item.id] = true
            console.log 'be' + $scope.show_lessons

        $scope.hideEmptyLesson = ->
            $scope.show_lessons and (delete $scope.show_lessons[$scope.item.id || $scope.item.fake_id])

        $scope.isShownLesson = ->
            return $scope.show_lessons?[$scope.item.id]

        $scope.level = 1 if not $scope.level
        $scope.lesson_count = 0 if not $scope.lesson_count
        resetNewItem()