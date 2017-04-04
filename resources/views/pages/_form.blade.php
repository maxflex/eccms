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
                'ng-keyup' => 'checkExistance(\'title\', $event)',
            ]
        ])
    </div>
</div>

<div class="row mbs">
    <div class="col-sm-11">
        <label class="no-margin-bottom label-opacity">публикация</label>
        <ng-select-new model='FormService.model.published' object="Published" label="title" convert-to-number></ng-select-new>
    </div>
    <div class="col-sm-1">
        <div class='burger seo-desktop'>
            <div class='selectable' ng-class="{'selected': FormService.model.seo_desktop == 1}" ng-click='FormService.model.seo_desktop = 1'></div>
            <div></div>
            <div></div>
            <div class='selectable' ng-class="{'selected': FormService.model.seo_desktop == 0}" ng-click='FormService.model.seo_desktop = 0'></div>
        </div>
        <div class='burger seo-mobile'>
            <div class='selectable' ng-class="{'selected': FormService.model.seo_mobile == 1}" ng-click='FormService.model.seo_mobile = 1'></div>
            <div></div>
            <div></div>
            <div class='selectable' ng-class="{'selected': FormService.model.seo_mobile == 0}" ng-click='FormService.model.seo_mobile = 0'></div>
        </div>
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
        @include('modules.input', [
            'title' => 'h1 внизу',
            'model' => 'h1_bottom',
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
        <label>содержание раздела</label>
        <label class="pull-right" style='top: 3px; position: relative'>
            <span class='link-like' ng-click='addLinkDialog()'>добавить ссылку</span>
        </label>
        <div class="top-links pull-right">
            <span ng-repeat="option in options" class="link-like ng-binding ng-scope" ng-class="{'active': $index == sort}" ng-click="setSort($index)">по алфавиту</span>
            <span ng-repeat="option in options" class="link-like ng-binding ng-scope active" ng-class="{'active': $index == sort}" ng-click="setSort($index)">по времени сохранения</span>
        </div>
        <div id='editor' style="height: 500px">@{{ FormService.model.html }}</div>
    </div>
</div>
@include('pages._modals')
