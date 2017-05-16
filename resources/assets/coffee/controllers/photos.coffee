angular
    .module 'Egecms'
    .config (ngImageGalleryOptsProvider) ->
        ngImageGalleryOptsProvider.setOpts
            thumbnails: true
            inline    : false
            imgBubbles: false
            bgClose   : true
            imgAnim   : 'fadeup'
    .controller 'PhotosIndex', ($scope, $attrs, IndexService, Photo, PhotoService, FormService) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(Photo, $scope.current_page, $attrs)

        $scope.sortablePhotosConf =
            animation: 150
            onUpdate: (event) ->
                positions = {}
                angular.forEach event.models, (obj, index) ->
                    positions[obj.id] = index
                Photo.updateAll
                    positions: positions

        $scope.delete = (event, model) ->
            FormService.model = new Photo model
            FormService.delete event, =>
                IndexService.page.total--
                IndexService.page.data = _.without IndexService.page.data, model

        $scope.upload = (model) ->
            PhotoService.editing_model = model
            window.upload()

        $scope.$watchCollection 'FormService.model.photos', (newVal, oldVal) ->
            $scope.images = PhotoService.getImages() if newVal isnt undefined
