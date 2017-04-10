angular
    .module 'Egecms'
    .config (ngImageGalleryOptsProvider) ->
        ngImageGalleryOptsProvider.setOpts
            thumbnails: true
            inline    : false
            imgBubbles: false
            bgClose   : true
            imgAnim   : 'fadeup'
    .controller 'PhotosIndex', ($scope, $attrs, IndexService, Photo) ->
        bindArguments($scope, arguments)
        angular.element(document).ready ->
            IndexService.init(Photo, $scope.current_page, $attrs)