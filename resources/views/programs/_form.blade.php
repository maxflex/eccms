<div class="row mb">
    <div class="col-sm-12">
        @include('modules.input', ['title' => 'название программы', 'model' => 'title'])
    </div>
</div>
<div class="row mb">
    <div class="col-sm-12 program-container">
        <program-item item="FormService.model" class="root"></program-item>
    </div>
</div>