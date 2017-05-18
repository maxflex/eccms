angular
    .module 'Egecms'
    .config (ngImageGalleryOptsProvider) ->
        ngImageGalleryOptsProvider.setOpts
            thumbnails: true
            inline    : false
            imgBubbles: false
            bgClose   : true
            imgAnim   : 'fadeup'
    .controller 'PhotosIndex', ($scope, $rootScope, $attrs, Photo, PhotoGroup, PhotoService, FormService) ->
    .controller 'PhotosIndex', ($scope, $rootScope, $attrs, Photo, PhotoGroup, PhotoService, FormService) ->
        l = (e) -> console.log e

        angular.element(document).ready ->
            PhotoService.init $scope.groups

            $(document).scroll (event) ->
                if $(document).scrollTop() + $(window).height() is $(document).height()
                    $(document).scrollTop($(document).height() - 50)
                    l 'scrolled back'

        $scope.$watchCollection 'dnd', (newVal) ->
            l $scope.dnd

        bindArguments($scope, arguments)

        updatePositions = (group_ids) ->
            group_ids = [group_ids] if not _.isArray group_ids
            angular.forEach group_ids, (group_id) ->
                group = $rootScope.findById $scope.groups, group_id
                angular.forEach group.photo, (photo, index) ->
                    Photo.update({id: photo.id, position: index})

        dragEnd = ->
            $scope.dnd = {}

        $scope.sortablePhotosConf =
            animation: 150
            group:
                name:   'variable'
                put:    'variable'
            fallbackTolerance: 5

            onUpdate: (event) ->
                updatePositions $scope.dnd.group_id

            onAdd: (event) ->
                photo_id = $scope.dnd.photo_id
                if $scope.dnd.group_id and $scope.dnd.photo_id and ($scope.dnd.group_id isnt $scope.dnd.old_group_id)
                   if $scope.dnd.group_id is -1
                       PhotoGroup.save {photo_id: $scope.dnd.photo_id}, (response) ->
                           $scope.groups.push(response)
                           moveToGroup $scope.dnd.photo_id, response.id, $scope.dnd.old_group_id, true
                           dragEnd()
                   else if $scope.dnd.group_id
                       moveToGroup $scope.dnd.photo_id, $scope.dnd.group_id, $scope.dnd.old_group_id
                       updatePositions [$scope.dnd.group_id, $scope.dnd.old_group_id]

            onEnd: (event) ->
                dragEnd() if $scope.dnd.group_id isnt -1

        $scope.dragOver = (group) ->
            $scope.dnd.group_id = group.id if $scope.dnd.type isnt 'group'

        $scope.sortableGroupConf =
            animation: 150
            handle:     '.group-title'
            dragClass:  'dragging-group'

            onUpdate: (event) ->
                angular.forEach $scope.groups, (group, index) ->
                    group.position = index
                    PhotoGroup.update({id: group.id, position: index})

            onStart: (event) ->
                $scope.dnd.type = 'group'

            onEnd: (event) ->
                $scope.dnd = {}

        $scope.dnd = {}

        # переместить в группу
        moveToGroup = (photo_id, group_id, old_group_id, copy_item = false) ->
            Photo.update id: photo_id, group_id: group_id

            group_from = _.find $scope.groups, id: old_group_id
            photo = _.clone findById(group_from.photo, photo_id)
            photo.group_id = group_id

            group_from.photo = removeById(group_from.photo, photo_id)
            group_to = _.find $scope.groups, id: group_id

            if copy_item
                group_to.photo.push photo
            else
                photo = $rootScope.findById(group_to.photo, photo_id)
                photo.group_id = group_id

        $scope.removeGroup = (group) ->
            bootbox.confirm "Вы уверены, что хотите удалить группу «#{group.title}»", (response) ->
                if response is true
                    PhotoGroup.remove {id: group.id}
                    new_group_id = (_.max _.without($scope.groups, group), (group) -> group.position).id

                    if group.photo
                        angular.forEach group.photo, (photo) ->
                            moveToGroup photo.id, new_group_id, photo.group_id, true
                        updatePositions new_group_id

                    $scope.groups = removeById $scope.groups, group.id

        $scope.onEdit = (id, event) ->
            PhotoGroup.update {id: id, title: $(event.target).text()}

        $scope.delete = (event, model) ->
            FormService.model = new Photo model
            FormService.delete event, =>
                _.each $scope.groups, (group) ->
                    group.photo = _.without group.photo, model

        $scope.upload = (model) ->
            PhotoService.editing_model = model
            window.upload()

        $scope.totalPhotos = ->
            return if not $scope.groups
            _.reduce $scope.groups, (sum, group) ->
                sum += group.photo.length
            , 0

        PhotoService.init $scope.groups
