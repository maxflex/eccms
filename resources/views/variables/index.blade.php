@extends('app')
@section('title', 'Переменные')
@section('controller', 'VariablesIndex')

@section('title-right')
    {{ link_to_route('variables.create', 'добавить переменную') }}
@endsection

@section('content')
    <span ng-init='groups = {{ json_encode(\App\Models\VariableGroup::get()) }}'></span>

    <div class="container" ng-sortable="sortableGroupConf">
        <div class="layer group" ng-repeat="group in groups">
            <div class="group-title">@{{ group.title }}</div>
            <div class="tile__list">
                <ul ng-sortable="sortableVariableConf">
                    <li ng-repeat="variable in group.variable" class="group-item">@{{ variable.name }}</li>
                </ul>
            </div>
        </div>
    </div>
@stop
