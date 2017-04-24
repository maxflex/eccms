@extends('app')
@section('title', 'Страницы')
@section('controller', 'PagesIndex')

@section('title-right')
    <span ng-click='ExportService.exportDialog()'>экспорт</span>
    {{ link_to_route('pages.import', 'импорт', [], ['ng-click'=>'ExportService.import($event)']) }}
    {{ link_to_route('pages.create', 'добавить страницу') }}
@stop

@section('content')
    <span ng-init='groups = {{ json_encode(\App\Models\PageGroup::get()) }}'></span>
    <div ng-sortable='sortableGroupConf'>
        <div ng-repeat="group in groups">
            <div>
                <h4 class='inline-block' editable='@{{ group.id }}' ng-class="{'disable-events': !group.id}">@{{ group.title }}</h4>
                <a ng-if='group.id' class='link-like text-danger show-on-hover' ng-click='removeGroup(group)'>удалить</a>
            </div>
            <div class='droppable-table' ondragover="allowDrop(event)"
                ng-dragenter="dnd.over = group.id" ng-dragleave="dnd.over = undefined" ng-drop="drop(group.id)"
                ng-class="{'over': dnd.page_id && dnd.over === group.id && dnd.over != getPage(dnd.page_id).group_id}">
                <table class="table droppable-table">
                    <tbody ng-sortable='sortablePageConf'>
                        <tr ng-repeat="page in group.page" draggable="true"
                             ng-dragstart="dragStart(page.id)" ng-dragend='dnd.page_id = null'>
                             <td width='35%'>
                                 <a href="pages/@{{ page.id }}/edit">@{{ page.keyphrase }}</a>
                             </td>
                             <td width='20%'>
                                 <span class="link-like" ng-class="{'link-gray': 0 == +page.published}" ng-click="toggleEnumServer(page, 'published', Published, Page)">@{{ Published[page.published].title }}</span>
                             </td>
                             <td width='20%'>
                                 @{{ formatDateTime(page.updated_at) }}
                             </td>
                             <td style="text-align: right; width: 25%">
                                 <a href="{{ config('app.web-url') }}@{{ page.url }}" target="_blank">просмотреть страницу на сайте</a>
                             </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>
        <div ng-show='dnd.page_id > 0'>
            <h4>{{ \App\Models\PageGroup::DEFAULT_TITLE }}</h4>
            <div class='droppable-table' ondragover="allowDrop(event)"
                ng-dragenter="dnd.over = -1" ng-dragleave="dnd.over = undefined" ng-drop="drop(-1)"
                ng-class="{'over': dnd.over == -1}">
                <table class="table">
                    <tr ng-repeat="i in [1, 2, 3, 4]">
                        <td style='width: 30%'>
                            <div class='fake-info'></div>
                        </td>
                        <td style='width: 70%'>
                            <div class='fake-info' style='width: 300px'></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    {{-- @include('modules.pagination') --}}
    @include('modules._export_dialog')
@stop
