@extends('app')
@section('title', 'Страницы')
@section('controller', 'PagesIndex')

@section('title-right')
    <span ng-click='ExportService.exportDialog()'>экспорт</span>
    {{ link_to_route('pages.import', 'импорт', [], ['ng-click'=>'ExportService.import($event)']) }}
    {{ link_to_route('pages.create', 'добавить страницу') }}
@stop

@section('content')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
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
                <li class="group-item" style='display: flex'
                    ng-repeat="page in group.page"
                    ng-dragstart="dnd.page_id = page.id; dnd.old_group_id = group.id;"
                >
                    <span style="width: 40px;">@{{ page.id }}</span>
                    <a style="width:50%;" class="group-item-title" href="pages/@{{ page.id }}/edit">@{{ page.keyphrase }}</a>
                    <div class='page-index-icons'>
                        <i class="fa fa-file"
                            ng-repeat='(field, value) in page.filled' 
                            title="@{{ field }}"
                            ng-class="{
                                'bold color-green': value === true,
                                'font-weight-normal color-gray': value === false
                            }"
                            aria-hidden="true"></i>
                    </div>
                    <div class='page-index-icons' style='justify-content: flex-end; flex: 1'>
                        <i class="far fa-star star-control"
                            ng-click="toggleEnumServer(page, 'is_ready', Published, Page)"
                            ng-class="{'star-control--filled': page.is_ready == 1}"
                        ></i>
                        <i class="fa fa-globe pointer" 
                            ng-click="toggleEnumServer(page, 'published', Published, Page)" 
                            ng-class="{
                            'color-gray': 0 == +page.published,
                            'color-green': 1 == +page.published,
                        }" aria-hidden="true"></i>
                        <a href="{{ config('app.web-url') }}@{{ page.url }}" target="_blank">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                    </div>
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
