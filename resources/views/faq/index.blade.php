@extends('app')
@section('title', 'FAQ')
@section('controller', 'FaqIndex')

@section('title-right')
    {{ link_to_route('faq.create', 'добавить вопрос') }}
@stop

@section('content')
    <table class="table reverse-borders">
        <tbody>
            <tr ng-repeat="model in IndexService.page.data">
                <td>
                    <a href="faq/@{{ model.id }}/edit">@{{ model.question }}</a>
                </td>
            </tr>
        </tbody>
    </table>
@stop
