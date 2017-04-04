@extends('app')
@section('controller', 'VariablesForm')
@section('title', 'Добавление переменной')
@section('title-center')
    <span ng-click="FormService.create()">добавить</span>
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        @include('variables._form')
    </div>
</div>
@stop
