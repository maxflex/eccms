@extends('app')
@section('title', 'Галлерея')
@section('controller', 'PhotosIndex')

@section('content')
    @include('photos._form')

    <div class="row">
        <div class="col-sm-12">
            <ng-image-gallery images="IndexService.page.data"></ng-image-gallery>
        </div>
    </div>
@stop
