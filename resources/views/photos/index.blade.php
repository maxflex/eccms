@extends('app')
@section('title', 'Галлерея')
@section('controller', 'PhotosIndex')

@section('title-right')
    {{ link_to_route('photos.create', 'добавить фото') }}
@endsection

@section('content')
    <table class="table reverse-borders">
        <tbody ng-sortable='sortablePhotosConf'>
        <tr ng-repeat="model in IndexService.page.data">
            <td class="photo-id">
                <a ng-href="photos/@{{ model.id }}/edit">@{{ model.id }}</a>
            </td>
            <td>
                <img width="100px" ng-src="@{{ model.thumbUrl }}">
            </td>
        </tr>
        </tbody>
    </table>
@stop
