 <script>
    angular.module('Egecms')
        .value('PhotosUploadDir', {!! json_encode('storage/' . \App\Models\Photo::UPLOAD_DIR) !!})
 </script>
