<div class="row mb">
    <div class="col-sm-12">
        @include('modules.input', ['title' => 'описание', 'model' => 'title'])
    </div>
</div>
<div id='photos'>
    <div class='row mb'>
        <div id='upload-photo' class='col-sm-3'>
            <button onclick='upload()' type="button" class='btn btn-primary' ng-disabled='PhotoService.Uploader.isUploading'>
                добавить фотографии
            </button>
            <input type='file' multiple name='photos' nv-file-select='' uploader='PhotoService.Uploader'>
        </div>
    </div>
</div>
