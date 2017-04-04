@extends('app')
@section('title', 'Разделы')
@section('controller', $ngController . 'Index')

@section('title-right')
    <span ng-click='ExportService.exportDialog()'>экспорт</span>
    {{ link_to_route('pages.import', 'импорт', [], ['ng-click'=>'ExportService.import($event)']) }}
    {{ link_to_route('pages.create', 'добавить страницу') }}
@stop

@section('content')
    <table class="table reverse-borders">
        <div class="row mbs">
            <div class='col-sm-12'>
                <order-by options="['по алфавиту', 'по времени сохранения']"></order-by>
            </div>
        </div>
        {{-- <tbody ui-sortable='sortableOptions' ng-model="IndexService.page.data" > --}}
        <tbody>
            <tr ng-repeat="model in IndexService.page.data">
                <td width='35%'>
                    <a href="pages/@{{ model.id }}/edit">@{{ model.keyphrase }}</a>
                </td>
                <td width='20%'>
                    <span class="link-like" ng-class="{'link-gray': 0 == +model.published}" ng-click="toggleEnumServer(model, 'published', Published, Page)">@{{ Published[model.published].title }}</span>
                </td>
                <td width='20%'>
                    @{{ formatDateTime(model.updated_at) }}
                </td>
                <td style="text-align: right; width: 25%">
                    <a href="{{ config('app.web-url') }}@{{ model.url }}" target="_blank">просмотреть страницу на сайте</a>
                </td>
            </tr>
        </tbody>
    </table>
    @include('modules.pagination')
    @include('modules._export_dialog')
@stop
