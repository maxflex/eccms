 <script>
    angular.module('Egecms')
        .value('PhotosUploadDir', {!! json_encode(\App\Models\Photo::UPLOAD_DIR) !!})
 </script>
