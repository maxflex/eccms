angular
    .module 'Egecms'
    .config (ngImageGalleryOptsProvider) ->
        ngImageGalleryOptsProvider.setOpts
            thumbnails: true
            inline    : false
            imgBubbles: false
            bgClose   : true
            imgAnim   : 'fadeup'
    .controller 'PhotosIndex', ($scope, $attrs, IndexService, Photo, PhotoService) ->
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



    .controller 'PhotosForm', ($scope, $attrs, FormService, Photo, PhotoService) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            FormService.init(Photo, $scope.id, $scope.model)

        $scope.$watchCollection 'FormService.model.photos', (newVal, oldVal) ->
            $scope.images = PhotoService.getImages() if newVal isnt undefined
