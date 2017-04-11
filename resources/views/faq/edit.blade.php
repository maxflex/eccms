@extends('app')
@section('title', 'Редактирование FAQ')
@section('title-center')
    <span ng-click="FormService.edit()">сохранить</span>
@stop
@section('title-right')
    <span ng-click="FormService.delete($event)">удалить</span>
@stop
@section('content')
@section('controller', 'FaqForm')
<div class="row">
    <div class="col-sm-12">
        @include('faq._form')
    </div>
</div>
@stop
