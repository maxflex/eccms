@extends('app')
@section('title', 'Переменные')
@section('controller', 'VariablesIndex')

@section('title-right')
    {{ link_to_route('variables.create', 'добавить переменную') }}
@endsection

@section('content')
    <span ng-init='groups = {{ json_encode(\App\Models\VariableGroup::get()) }}'></span>
    <div ng-sortable="sortableGroupConf" class="nested-dnd">
        <div class="layer group" ng-repeat="group in groups">
            <div class="group-title">
                <h4 class='inline-block' editable='@{{ group.id }}' ng-class="{'disable-events': !group.id}">@{{ group.title }}</h4>
                <a ng-if='group.id' class='link-like text-danger show-on-hover' ng-click='removeGroup(group)'>удалить</a>
            </div>
            <ul ng-sortable="sortableVariableConf"
                ng-class="{'ng-hide': dnd.type == 'group', 'hovered': dnd.old_group_id != group.id && dnd.group_id == group.id }"
                ng-dragover="dragOver(group)"
                class="group-list"
            >
                <li class="group-item"
                    ng-repeat="variable in group.variable"
                    ng-dragstart="dnd.variable_id = variable.id; dnd.old_group_id = group.id;"
                >
                    <a class="group-item-title" href='variables/@{{ variable.id }}/edit'>@{{ variable.name }}</a>
                    <span class="group-item-desc">@{{ variable.desc }}</span>
                </li>
            </ul>
        </div>

        <div class="layer group" ng-show="dnd.variable_id > 0">
            <div class="group-title">
                <h4 class="inline-block">{{ \App\Models\VariableGroup::DEFAULT_TITLE }}</h4>
            </div>
            <ul ng-hide="dnd.type == 'group'" ng-sortable="sortableVariableConf" class="group-list" ng-class="{'hovered': dnd.group_id == -1 }" ng-dragover="dnd.group_id = -1">
                <li class="group-item"
                    ng-repeat="i in [1, 2, 3]"
                >
                    <a class="group-item-title">
                        <div class="fake-info"></div>
                    </a>
                    <span class="group-item-desc">
                        <div class="fake-info"></div>
                    </span>
                </li>
            </ul>
        </div>
    </div>
@stop
