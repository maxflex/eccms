@extends('app')
@section('title', 'Галлерея')
@section('controller', 'PhotosIndex')

@section('title-right')
    {{ link_to_route('photos.create', 'добавить фото') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a ng-href="@{{ 'photos/' + image.id + '/edit' }}" ng-repeat="image in IndexService.page.data">@{{ image.title ? image.title : 'нет описания' }}</a>
        </div>
    </div>
@stop
