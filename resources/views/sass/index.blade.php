@extends('app')
@section('title', 'Файлы стилей')
@section('controller', 'SassIndex')

@section('content')
    <table class="table">
        <tr ng-repeat="model in IndexService.page.data">
            <td>
                <a href='sass/@{{ model }}/edit'>@{{ model }}</a>
            </td>
        </tr>
    </table>
@stop
