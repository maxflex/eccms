@extends('app')
@section('title', 'Галлерея <span ng-show="IndexService.page.total">(<plural class="gallery-count" type="photo" count="IndexService.page.total"></plural>)</spanq>')
@section('controller', 'PhotosIndex')

@section('title-right')
    <span onclick='upload()' ng-disabled='PhotoService.Uploader.isUploading' id="upload-photo">добавить фото</span>
@endsection

@section('content')
    <input class="ng-hide" type='file' multiple name='photos' nv-file-select='' uploader='PhotoService.Uploader'>

    <table class="table reverse-borders photos-table">
        <tbody ng-sortable='sortablePhotosConf'>
        <tr ng-repeat="model in IndexService.page.data">
            <td width="5%">
                <span class="link-like-text">@{{ model.id }}</span>
            </td>
            <td width="120px">
                <img width="100px" ng-src="@{{ model.thumbUrl }}">
            </td>
            <td width="100px">
                @{{ model.info.width + 'x' + model.info.height }}
            </td>
            <td>
                @{{ PhotoService.filesize(model.info.size) }}
            </td>
            <td width="10%">
                <span class="link link-like" ng-click="upload(model)">редактировать</span>
            </td>
            <td width="10%">
                <span class="link link-like" ng-click="delete($event, model)">удалить</span>
            </td>
        </tr>
        </tbody>
    </table>
@stop
