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
    <div ng-sortable='sortableGroupConf' class="nested-dnd">
        <div class="layer group" ng-repeat="group in groups">
            <div class="group-title">
                <h4 class='inline-block' editable='@{{ group.id }}' ng-class="{'disable-events': !group.id}">@{{ group.title }}</h4>
                <a ng-if='group.id' class='link-like text-danger show-on-hover' ng-click='removeGroup(group)'>удалить</a>
            </div>
            <ul ng-sortable="sortablePageConf"
                ng-class="{'ng-hide': dnd.type == 'group', 'hovered': dnd.old_group_id != group.id && dnd.group_id == group.id }"
                ng-dragenter="dragOver(group, $event)"
                class="group-list"
            >
                <li class="group-item"
                    ng-repeat="page in group.page"
                    ng-dragstart="dnd.page_id = page.id; dnd.old_group_id = group.id;"
                >
                    <a style="width:35%;" class="group-item-title" href="pages/@{{ page.id }}/edit">@{{ page.keyphrase }}</a>
                    <span style="width:20%;" class="link-like" ng-class="{'link-gray': 0 == +page.published}" ng-click="toggleEnumServer(page, 'published', Published, Page)">@{{ Published[page.published].title }}</span>
                    <span style="width:20%;">@{{ formatDateTime(page.updated_at) }}</span>
                    <a style="width:23%;" href="{{ config('app.web-url') }}@{{ page.url }}" target="_blank">просмотреть страницу на сайте</a>
                </li>
            </ul>
        </div>

        <div class="layer group" ng-show="dnd.page_id > 0">
            <div class="group-title">
                <h4 class="inline-block">{{ \App\Models\PageGroup::DEFAULT_TITLE }}</h4>
            </div>
            <ul ng-hide="dnd.type == 'group'" ng-sortable="sortablePageConf" class="group-list" ng-class="{'hovered': dnd.group_id == -1 }" ng-dragover="dnd.group_id = -1">
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

    @include('modules._export_dialog')
@stop
