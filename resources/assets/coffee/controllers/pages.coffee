angular
    .module 'Egecms'
    .controller 'PagesIndex', ($scope, $attrs, $rootScope, $timeout, IndexService, Page, Published, ExportService, PageGroup) ->
        bindArguments($scope, arguments)
        ExportService.init({controller: 'pages'})

        $scope.sortablePageConf =
            animation: 150
            onUpdate: (event) ->
                angular.forEach event.models, (obj, index) ->
                    Page.update({id: obj.id, position: index})

        $scope.sortableGroupConf =
            animation: 150
            onStart: (event) ->
                $scope.group_sorting = true
            onUpdate: (event) ->
                $scope.group_sorting = false
                angular.forEach event.models, (obj, index) ->
                    PageGroup.update({id: obj.id, position: index})

        $scope.dnd = {}

        $scope.dragStart = (page_id) ->
            $timeout ->
                console.log('drag start', page_id)
                $scope.dnd.page_id = page_id

        $scope.drop = (group_id) ->
            page_id = $scope.dnd.page_id
            if page_id and group_id and (group_id isnt $scope.getGroup(page_id).id)
                if group_id is -1
                    PageGroup.save {page_id: page_id}, (response) ->
                        $scope.groups.push(response)
                        moveToGroup(page_id, response.id)
                else if group_id
                    Page.update({id: $scope.dnd.page_id, group_id: group_id})
                    moveToGroup(page_id, group_id)
            $scope.dnd = {}

        # переместить в группу
        moveToGroup = (page_id, group_id) ->
            group_to = $rootScope.findById($scope.groups, group_id)
            group_from = $scope.getGroup(page_id)
            page = $rootScope.findById(group_from.page, page_id)
            page.group_id = group_id
            group_from.page = removeById(group_from.page, page_id)
            page.group_id = group_id
            group_to.page.push(page)



        $scope.getGroup = (page_id) ->
            group_found = null
            $scope.groups.forEach (group) ->
                return if group_found isnt null
                group.page.forEach (page) ->
                    if page.id is parseInt(page_id)
                        group_found = group
                        return
            group_found

        $scope.getPage = (page_id) ->
            $rootScope.findById($scope.getGroup(page_id).page, page_id)

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    PageGroup.remove {id: group.id}
                    $scope.groups = removeById($scope.groups, group.id)

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
