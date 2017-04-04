@extends('app')
@section('title', 'Редактирование файла')
@section('title-center')
    <span ng-click="FormService.edit()">сохранить</span>
@stop

@section('content')
    @section('controller', 'SassForm')
        <div class="row">
            <div class="col-sm-12">
                @include('sass._form')
            </div>
        </div>
    @stop
