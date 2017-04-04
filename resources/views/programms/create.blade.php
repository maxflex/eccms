@extends('app')
@section('controller', 'ProgrammsForm')
@section('title', 'Добавление программы')
@section('title-center')
    <span ng-click="FormService.create()">добавить</span>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @include('programms._form')
        </div>
    </div>
@stop
