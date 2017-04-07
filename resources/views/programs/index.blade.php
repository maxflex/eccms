@extends('app')
@section('title', 'Программы')
@section('controller', 'ProgramsIndex')

@section('title-right')
    {{ link_to_route('programs.create', 'добавить программу') }}
@endsection

@section('content')
    <table class="table">
        <tr ng-repeat="model in IndexService.page.data">
            <td>
                <a href='programs/@{{ model.id }}/edit'>@{{ model.title }}</a>
            </td>
            <td>
                @{{ model.desc }}
            </td>
        </tr>
    </table>
    @include('modules.pagination')
@stop
