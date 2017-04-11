@extends('app')
@section('controller', 'FaqForm')
@section('title', 'Добавление FAQ')
@section('title-center')
    <span ng-click="!FormService.saving && FormService.create()">добавить</span>
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        @include('faq._form')
    </div>
</div>
@stop
