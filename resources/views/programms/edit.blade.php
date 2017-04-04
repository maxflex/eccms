@extends('app')
@section('title', 'Редактирование программы')
@section('title-center')
    <span ng-click="FormService.edit()">сохранить</span>
@stop

@section('title-right')
    <span ng-click="FormService.delete($event)">удалить программу</a>
@stop

@section('content')
    @section('controller', 'ProgrammsForm')
    <div class="row">
        <div class="col-sm-12">
            @include('programms._form')
        </div>
    </div>
@stop
