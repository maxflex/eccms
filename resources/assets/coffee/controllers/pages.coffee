angular
    .module 'Egecms'
    .controller 'PagesIndex', ($scope, $attrs, $rootScope, $timeout, IndexService, Page, Published, ExportService, PageGroup) ->
        l = (e) -> console.log e

        angular.element(document).ready ->
            $(document).scroll (event) ->
                if $(document).scrollTop() + $(window).height() is $(document).height()
                    $(document).scrollTop($(document).height() - 50)
                    l 'scrolled back'

        $scope.$watchCollection 'dnd', (newVal) ->
            l $scope.dnd

        bindArguments($scope, arguments)
        ExportService.init({controller: 'pages'})

        updatePositions = (group_ids) ->
            group_ids = [group_ids] if not _.isArray group_ids
            angular.forEach group_ids, (group_id) ->
                group = $rootScope.findById $scope.groups, group_id
                angular.forEach group.page, (page, index) ->
                    Page.update({id: page.id, position: index})

        dragEnd = ->
            $scope.dnd = {}

        $scope.sortablePageConf =
            animation: 150
            group:
                name:   'variable'
                put:    'variable'
            fallbackTolerance: 5

            onUpdate: (event) ->
                updatePositions $scope.dnd.group_id

            onAdd: (event) ->
                page_id = $scope.dnd.page_id
                if $scope.dnd.group_id and $scope.dnd.page_id and ($scope.dnd.group_id isnt $scope.dnd.old_group_id)
                   if $scope.dnd.group_id is -1
                       PageGroup.save {page_id: $scope.dnd.page_id}, (response) ->
                           $scope.groups.push(response)
                           moveToGroup $scope.dnd.page_id, response.id, $scope.dnd.old_group_id, true
                           dragEnd()
                   else if $scope.dnd.group_id
                       moveToGroup $scope.dnd.page_id, $scope.dnd.group_id, $scope.dnd.old_group_id
                       updatePositions [$scope.dnd.group_id, $scope.dnd.old_group_id]

            onEnd: (event) ->
                dragEnd() if $scope.dnd.group_id isnt -1

        $scope.dragOver = (group) ->
            $scope.dnd.group_id = group.id if $scope.dnd.type isnt 'group'

        $scope.sortableGroupConf =
            animation: 150
            handle: '.group-title'
            dragClass: 'dragging-group'
            onUpdate: (event) ->
                angular.forEach $scope.groups, (group, index) ->
                    group.position = index
                    PageGroup.update({id: group.id, position: index})

            onStart: (event) ->
                $scope.dnd.type = 'group'

            onEnd: (event) ->
                $scope.dnd = {}

        $scope.dnd = {}

        # переместить в группу
        moveToGroup = (page_id, group_id, old_group_id, copy_item = false) ->
            Page.update id: page_id, group_id: group_id

            group_from = _.find $scope.groups, id: old_group_id
            page = _.clone findById(group_from.page, page_id)
            page.group_id = group_id

            group_from.page = removeById(group_from.page, page_id)
            group_to = _.find $scope.groups, id: group_id

            if copy_item
                group_to.page.push page
            else
                page = $rootScope.findById(group_to.page, page_id)
                page.group_id = group_id

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    PageGroup.remove {id: group.id}
                    new_group_id = (_.max _.without($scope.groups, group), (group) -> group.position).id

                    if group.page
                        angular.forEach group.page, (page) ->
                            moveToGroup page.id, new_group_id, page.group_id, true
                        updatePositions new_group_id

                    $scope.groups = removeById $scope.groups, group.id

        $scope.onEdit = (id, event) ->
            PageGroup.update {id: id, title: $(event.target).text()}

    .controller 'PagesForm', ($scope, $http, $attrs, $timeout, FormService, AceService, Page, Published, UpDown) ->
        bindArguments($scope, arguments)

        empty_useful = {text: null, page_id_field: null}

        angular.element(document).ready ->
            FormService.init(Page, $scope.id, $scope.model)
            FormService.dataLoaded.promise.then ->
                FormService.model.useful = [angular.copy(empty_useful)] if (not FormService.model.useful or not FormService.model.useful.length)
                ['html', 'html_mobile', 'seo_text'].forEach (field) -> AceService.initEditor(FormService, 15, "editor--#{field}")
            FormService.beforeSave = ->
                ['html', 'html_mobile', 'seo_text'].forEach (field) -> FormService.model[field] = AceService.getEditor("editor--#{field}").getValue()

        $scope.generateUrl = (event) ->
            $http.post '/api/translit/to-url',
                text: FormService.model.keyphrase
            .then (response) ->
                FormService.model.url = response.data
                $scope.checkExistance 'url',
                    target: $(event.target).closest('div').find('input')

        $scope.checkExistance = (field, event) ->
            Page.checkExistance
                id: FormService.model.id
                field: field
                value: FormService.model[field]
            , (response) ->
                element = $(event.target)
                if response.exists
                    FormService.error_element = element
                    element.addClass('has-error').focus()
                else
                    FormService.error_element = undefined
                    element.removeClass('has-error')

        # @todo: объединить с checkExistance
        $scope.checkUsefulExistance = (field, event, value) ->
            Page.checkExistance
                id: FormService.model.id
                field: field
                value: value
            , (response) ->
                element = $(event.target)
                if not value or response.exists
                    FormService.error_element = undefined
                    element.removeClass('has-error')
                else
                    FormService.error_element = element
                    element.addClass('has-error').focus()

        $scope.addUseful = ->
            FormService.model.useful.push(angular.copy(empty_useful))

        $scope.addLinkDialog = ->
            $scope.link_text = AceService.editor.getSelectedText()
            $('#link-manager').modal 'show'

        $scope.search = (input, promise)->
            $http.post('api/pages/search', {q: input}, {timeout: promise})
                .then (response) ->
                    return response

        $scope.searchSelected = (selectedObject) ->
            $scope.link_page_id = selectedObject.originalObject.id
            $scope.$broadcast('angucomplete-alt:changeInput', 'page-search', $scope.link_page_id.toString())

        $scope.addLink = ->
            link = "<a href='[link|#{$scope.link_page_id}]'>#{$scope.link_text}</a>"
            $scope.link_page_id = undefined
            $scope.$broadcast('angucomplete-alt:clearInput')
            AceService.editor.session.replace(AceService.editor.selection.getRange(), link)
            $('#link-manager').modal 'hide'

        $scope.$watch 'FormService.model.station_id', (newVal, oldVal) ->
            $timeout -> $('#sort').selectpicker('refresh')
