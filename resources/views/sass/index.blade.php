@extends('app')
@section('title', 'Файлы стилей')
@section('controller', 'SassIndex')

@section('content')
    <table class="table">
        <tr ng-repeat="directory in data.directories">
            <td>
                <i class="fa fa-folder text-success" aria-hidden="true"></i>
                <a href='/sass/@{{ directory }}'>@{{ getName(directory) }}</a>
            </td>
        </tr>
        <tr ng-repeat="file in data.files">
            <td>
                <a href='sass/@{{ file }}'>@{{ getName(file) }}</a>
            </td>
        </tr>
    </table>
@stop
