@extends('app')
@section('controller', 'PagesForm')
@section('title', 'Добавление страницы')
@section('title-center')
    <span ng-click="!FormService.saving && FormService.create()">добавить</span>
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        @include('pages._form')
    </div>
</div>
@stop
