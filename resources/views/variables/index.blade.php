@extends('app')
@section('title', 'Переменные')
@section('controller', 'VariablesIndex')

@section('title-right')
    {{ link_to_route('variables.create', 'добавить переменную') }}
@endsection

@section('content')
    <span ng-init='groups = {{ \App\Models\VariableGroup::getIds() }}'></span>
    <div ng-repeat="group in groups">
        <div>
            <h4 class='inline-block' editable='@{{ group.id }}' ng-class="{'disable-events': !group.id}">@{{ group.title }}</h4>
            <a ng-if='group.id' class='link-like text-danger show-on-hover' ng-click='removeGroup(group)'>удалить</a>
        </div>
        <div class='droppable-table' ondragover="allowDrop(event)"
            ng-dragenter="dnd.over = group.id" ng-dragleave="dnd.over = undefined" ng-drop="drop(group.id)"
            ng-class="{'over': dnd.over === group.id && dnd.over != getVariable(dnd.variable_id).group_id}">
            <table class="table droppable-table">
                <tr ng-repeat="variable in getVariables(group.id)" draggable="true"
                     ng-dragstart="dragStart(variable.id)" ng-dragend='dnd.variable_id = null'>
                    <td>
                        <a href='variables/@{{ variable.id }}/edit'>@{{ variable.name }}</a>
                    </td>
                    <td>
                        @{{ model.desc }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div>
    <div ng-show='dnd.variable_id > 0'>
        <h4>{{ \App\Models\VariableGroup::DEFAULT_TITLE }}</h4>
        <div class='droppable-table' ondragover="allowDrop(event)"
            ng-dragenter="dnd.over = -1" ng-dragleave="dnd.over = undefined" ng-drop="drop(-1)"
            ng-class="{'over': dnd.over == -1}">
            <table class="table">
                <tr ng-repeat="i in [1, 2, 3, 4]">
                    <td>
                        <div class='fake-info'></div>
                    </td>
                    <td>
                        <div class='fake-info' style='width: 50px'></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @include('modules.pagination')
@stop
