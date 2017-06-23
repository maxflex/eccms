<div class="row mbs">
    <div class="col-sm-6">
        @include('modules.input', ['title' => 'ключевая фраза', 'model' => 'keyphrase'])
    </div>
    <div class="col-sm-6">
        <div class="field-container">
            <div class="input-group">
                <input ng-keyup="checkExistance('url', $event)" type="text" class="field form-control" required
                       placeholder="отображаемый URL" ng-model='FormService.model.url'
                       ng-model-options="{ allowInvalid: true }">
               <label class="floating-label">отображаемый URL</label>
               <span class="input-group-btn">
                   <button class="btn btn-default" type="button" ng-disabled="!FormService.model.keyphrase" ng-click="generateUrl($event)">
                       <span class="glyphicon glyphicon-refresh no-margin-right"></span>
                   </button>
               </span>
            </div>
        </div>
    </div>
</div>

<div class="row mbs">
    <div class="col-sm-12">
        @include('modules.input', [
            'title' => 'title',
            'model' => 'title',
            'attributes' => [
                'ng-counter' => true,
            ]
        ])
    </div>
</div>

<div class="row mbs">
    <div class="col-sm-12">
        <label class="no-margin-bottom label-opacity">публикация</label>
        <ng-select-new model='FormService.model.published' object="Published" label="title" convert-to-number></ng-select-new>
    </div>
</div>

<div class="row mbs">
    <div class="col-sm-12">
        @include('modules.input', [
            'title' => 'h1 вверху',
            'model' => 'h1',
            'attributes' => [
                'ng-counter' => true,
            ]
        ])
    </div>
</div>
<div class="row mbs">
    <div class="col-sm-12">
        @include('modules.input', ['title' => 'meta keywords', 'model' => 'keywords'])
    </div>
</div>
<div class="row mbs">
    <div class="col-sm-12">
        @include('modules.input', [
            'title' => 'meta description',
            'model' => 'desc',
            'textarea' => true,
            'attributes' => [
                'ng-counter' => true,
            ]
        ])
    </div>
</div>

<div class="row mbb">
    <div class="col-sm-12">
        <label>сео текст</label>
        <div id='editor--seo_text' style="height: 300px">@{{ FormService.model.seo_text }}</div>
    </div>
</div>

<div class="row mbb editors">
    <div class="col-sm-12">
        <label ng-class="{'active link-like': !AceService.isShown('editor')}" ng-click="AceService.show('editor')">стационар</label>
        <label ng-class="{'active link-like': !AceService.isShown('editor-mobile')}" ng-click="AceService.show('editor-mobile')">мобильная</label>
        <label class="pull-right" style='top: 3px; position: relative'>
            <span class='link-like' ng-click='addLinkDialog()'>добавить ссылку</span>
        </label>
        <div id='editor--html' ng-show="AceService.isShown('editor')" style="height: 500px">@{{ FormService.model.html }}</div>
        <div id='editor--html_mobile' ng-show="AceService.isShown('editor-mobile')" style="height: 500px">@{{ FormService.model.html_mobile }}</div>
    </div>
</div>
@include('pages._modals')
