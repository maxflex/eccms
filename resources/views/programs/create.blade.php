@extends('app')
@section('controller', 'ProgramsForm')
@section('title', 'Добавление программы')
@section('title-center')
    <span ng-click="FormService.create()">добавить</span>
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            @include('programs._form')
        </div>
    </div>
@stop
