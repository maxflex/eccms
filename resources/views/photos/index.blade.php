@extends('app')
@section('title', 'Галерея <span ng-show="totalPhotos()">(<plural class="gallery-count" type="photo" count="totalPhotos()"></plural>)</span>')
@section('controller', 'PhotosIndex')

@section('title-right')
    <span onclick='upload()' ng-disabled='Uploader.isUploading' id="upload-photo">добавить фото</span>
@endsection

@section('content')
    <input class="ng-hide" type='file' multiple name='photos' nv-file-select='' uploader='Uploader'>
    <span ng-init='groups = {{ json_encode(\App\Models\PhotoGroup::get()) }}'></span>

    <div ng-sortable="sortableGroupConf" class="nested-dnd">
        <div ng-repeat="group in groups">
            <div class="group-title">
                <h4 class='inline-block' editable='@{{ group.id }}' ng-class="{'disable-events': !group.id}">@{{ group.title }}</h4>
                <a ng-if='group.id && groups.length > 1' class='link-like text-danger show-on-hover' ng-click='removeGroup(group)'>удалить</a>
            </div>
            <ul ng-sortable="sortablePhotosConf"
                ng-class="{'ng-hide': dnd.type == 'group', 'hovered': dnd.old_group_id != group.id && dnd.group_id == group.id }"
                ng-dragover="dragOver(group)"
                class="group-list"
            >
                <li class="group-item"
                    ng-repeat="photo in group.photo"
                    ng-dragstart="dnd.photo_id = photo.id; dnd.old_group_id = group.id;"
                >
                    <span style="width:5%;" class="link-like-text">@{{ photo.id }}</span>
                    <span style="width:120px">
                        <img width="100px" async ng-src="@{{ photo.thumbUrl }}">
                    </span>
                    <span style="width:15%;">@{{ photo.info.width + 'x' + photo.info.height }}</span>
                    <span style="width:calc(60% - 85px)">@{{ filesize(photo.info.size) }}</span>
                    <a style="width:10%;" class="link link-like" ng-click="upload(photo)">редактировать</a>
                    <a style="width:5%;" class="link link-like" ng-click="delete($event, photo)">удалить</a>
                </li>
            </ul>
        </div>

        <div class="layer group" ng-show="dnd.photo_id > 0">
            <div class="group-title">
                <h4 class="inline-block">{{ \App\Models\PhotoGroup::DEFAULT_TITLE }}</h4>
            </div>
            <ul ng-hide="dnd.type == 'group'" ng-sortable="sortablePhotosConf" class="group-list" ng-class="{'hovered': dnd.group_id == -1 }" ng-dragover="dnd.group_id = -1">
                <li class="group-item"
                    ng-repeat="i in [1, 2, 3]"
                >
                    <a class="group-item-title">
                        <div class="fake-info"></div>
                    </a>
                    <span class="group-item-desc">
                        <div class="fake-info"></div>
                    </span>
                </li>
            </ul>
        </div>
    </div>
@stop
