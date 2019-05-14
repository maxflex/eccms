<div class="row mb">
    <div class="col-sm-4">
        @include('modules.input', ['title' => 'название переменной', 'model' => 'name'])
    </div>
    <div class="col-sm-4">
        @include('modules.input', ['title' => 'краткое описание переменной', 'model' => 'desc'])
    </div>
    <div class="col-sm-4">
        <label class="no-margin-bottom label-opacity">группа</label>
        <ng-select-new model='FormService.model.group_id' object="groups" label="title" convert-to-number></ng-select-new>
    </div>
</div>
<div class="row mb">
    <div class="col-sm-12">
        <label class="label-opacity">содержание переменной</label>
        <div id='editor' style="height: 500px">@{{ FormService.model.html }}</div>
    </div>
</div>

{{-- @include('docs.commands') --}}
