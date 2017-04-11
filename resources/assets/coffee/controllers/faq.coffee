angular
    .module 'Egecms'
    .controller 'FaqIndex', ($scope, $attrs, $timeout, IndexService, Faq, FaqGroup) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(Faq, $scope.current_page, $attrs)

        $scope.dnd = {}

        $scope.dragStart = (faq_id) ->
            $timeout ->
                console.log('drag start', faq_id)
                $scope.dnd.faq_id = faq_id

        $scope.drop = (group_id) ->
            if group_id is -1
                faq_id = $scope.dnd.faq_id
                FaqGroup.save {faq_id: faq_id}, (response) ->
                    $scope.groups.push(response)
                    IndexService.page.data.find (faq) ->
                        faq.id is faq_id
                    .group_id = response.id
            else if group_id
                Faq.update({id: $scope.dnd.faq_id, group_id: group_id})
                IndexService.page.data.find (faq) ->
                        faq.id is $scope.dnd.faq_id
                    .group_id = group_id
            $scope.dnd = {}

        $scope.getFaqs = (group_id) ->
            if IndexService.page then IndexService.page.data.filter (d) ->
                d.group_id is group_id

        $scope.getFaq = (faq_id) ->
            _.findWhere(IndexService.page.data, {id: parseInt(faq_id)})

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    FaqGroup.remove {id: group.id}
                    $scope.groups = removeById($scope.groups, group.id)
                    _.where(IndexService.page.data, {group_id: group.id}).forEach (faq) ->
                        faq.group_id = null

        $scope.onEdit = (id, event) ->
            FaqGroup.update {id: id, title: $(event.target).text()}

    .controller 'FaqForm', ($scope, $attrs, $timeout, FormService, AceService, Faq) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Faq, $scope.id, $scope.model)
