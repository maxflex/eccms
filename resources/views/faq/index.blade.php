@extends('app')
@section('title', 'FAQ')
@section('controller', 'FaqIndex')

@section('title-right')
    {{ link_to_route('faq.create', 'добавить вопрос') }}
@stop

@section('content')
    <span ng-init='groups = {{ json_encode(\App\Models\FaqGroup::get()) }}'></span>
    <div ng-sortable='sortableGroupConf'>
        <div ng-repeat="group in groups">
            <div>
                <h4 class='inline-block' editable='@{{ group.id }}' ng-class="{'disable-events': !group.id}">@{{ group.title }}</h4>
                <a ng-if='group.id' class='link-like text-danger show-on-hover' ng-click='removeGroup(group)'>удалить</a>
            </div>
            <div class='droppable-table relative' ondragover="allowDrop(event)" ng-show="! group_sorting"
                 ng-dragenter="dnd.over = group.id" ng-dragleave="dnd.over = undefined" ng-drop="drop(group.id)"
                 ng-class="{'over-parent': dnd.faq_id && dnd.over === group.id && dnd.over != getFaqs(dnd.faq_id).group_id}">
                <table class="table droppable-table">
                    <tbody ng-sortable='sortableFaqConf'>
                        <tr ng-repeat="faq in group.faq" draggable="true"
                            ng-dragstart="dragStart(faq.id)" ng-dragend='dnd.faq_id = null'>
                            <td>
                                <a href='faq/@{{ faq.id }}/edit'>@{{ faq.question }}</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="droppable-table pad" ng-class="{padded: !group.faq.length}" ondragenter="setClass(event, 'over')" ondragleave="unsetClass(event, 'over')"
                     ng-show="dnd.faq_id && (group.id != getFaq(dnd.faq_id).group_id)"
                ></div>
            </div>
        </div>
    </div>
    <div>
        <div ng-show='1 || dnd.faq_id > 0'>
            <h4>{{ \App\Models\FaqGroup::DEFAULT_TITLE }}</h4>
            <div class='droppable-table relative' ondragover="allowDrop(event)"
                 ng-dragenter="dnd.over = -1" ng-dragleave="dnd.over = undefined" ng-drop="drop(-1)"
                 ng-class="{'over-parent': dnd.over == -1}">
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
                <div class="droppable-table pad" ondragenter="setClass('over')" ondragleave="unsetClass('over')"></div>
            </div>
        </div>
    </div>
@stop
