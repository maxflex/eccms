@extends('app')
@section('title', 'Программы')
@section('controller', 'ProgrammsIndex')

@section('title-right')
    {{ link_to_route('programms.create', 'добавить программу') }}
@endsection

@section('content')
    <table class="table">
        <tr ng-repeat="model in IndexService.page.data">
            <td>
                <a href='programms/@{{ model.id }}/edit'>@{{ model.title }}</a>
            </td>
            <td>
                @{{ model.desc }}
            </td>
        </tr>
    </table>
    @include('modules.pagination')
@stop
