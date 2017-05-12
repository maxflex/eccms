<div class="row mbs">
    <div class="col-sm-3">
        <ng-select-new model="FormService.model.group_id" object="{{ \App\Models\FaqGroup::orderBy('position')->get()->toJSON() }}" label="title" field="id"></ng-select-new>
    </div>
</div>

<div class="row mbs">
    <div class="col-sm-12">
        @include('modules.input', [
            'title' => 'вопрос',
            'model' => 'question',
            'textarea' => true
        ])
    </div>
</div>

<div class="row mbs faq-answer">
    <div class="col-sm-12">
        @include('modules.input', [
            'title' => 'ответ',
            'rows' => '15',
            'model' => 'answer',
            'textarea' => true
        ])
    </div>
</div>

<style>
    .faq-answer textarea {
        height: 400px;
    }
</style>
