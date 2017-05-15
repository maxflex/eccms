<div class='row mb'>
    <div id='upload-photo' class='col-sm-3'>
        <button onclick='upload()' type="button" class='btn btn-primary' ng-disabled='PhotoService.Uploader.isUploading'>
            добавить фотографию
        </button>
        <input class="ng-hide" type='file' multiple name='photos' nv-file-select='' uploader='PhotoService.Uploader'>
    </div>
</div>

<div ng-show="PhotoService.getUrl(FormService.model)">
    <img class="gallery-image" ng-src="@{{ PhotoService.getUrl(FormService.model) }}">
</div>
