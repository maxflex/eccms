@extends('app')
@section('controller', 'PhotosForm')
@section('title', 'Добавление фото')
@section('title-center')
    <span ng-click="!FormService.saving && FormService.create()">добавить</span>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @include('photos._form')
        </div>
    </div>
@stop
