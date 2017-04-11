@extends('app')
@section('title', 'Редактирование фото')
@section('title-center')
    <span ng-click="FormService.edit()">сохранить</span>
@stop
@section('title-right')
    <span ng-click="FormService.delete($event)">удалить</span>
@stop
@section('content')
@section('controller', 'PhotosForm')
    <div class="row">
        <div class="col-sm-12">
            @include('photos._form')
        </div>
    </div>
@stop
