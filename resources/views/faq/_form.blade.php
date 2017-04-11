<div class="row mbs">
    <div class="col-sm-12">
        @include('modules.input', [
            'title' => 'вопрос',
            'model' => 'question',
            'textarea' => true
        ])
    </div>
</div>

<div class="row mbs">
    <div class="col-sm-12">
        @include('modules.input', [
            'title' => 'ответ',
            'model' => 'answer',
            'textarea' => true
        ])
    </div>
</div>