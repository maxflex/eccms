{{-- ПОИСК --}}
<div id="search-app" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
          {{-- <div class="row">
              <div class="col-sm-12">@{{conditions}}</div>
          </div> --}}
          <div class="row" ng-class="{'mb': !$last}" ng-repeat="condition in conditions">
              <div class="col-sm-6">
                  <select class="selectpicker" ng-model='condition.option' ng-change='selectControl(condition)'>
                      <option ng-repeat="option in options" ng-value='$index'>@{{ option.title }}</option>
                  </select>
              </div>
              <div class="col-sm-6">
                  <div ng-if="getOption(condition).type == 'text'">
                      <input type='text' class='form-control' ng-model='condition.value' placeholder="@{{ getOption(condition).title }}">
                  </div>
                  <div ng-if="getOption(condition).type == 'textarea'">
                      <textarea rows='5' class='form-control' ng-model='condition.value' placeholder="@{{ getOption(condition).title }}"></textarea>
                  </div>
                  <div ng-if="getOption(condition).type == 'published'">
                      <ng-select-new model='condition.value' object="Published" label="title" convert-to-number class='search-value-control'></ng-select-new>
                  </div>
                  <div ng-if="getOption(condition).type == 'subjects'">
                      <ng-multi object='subjects' label='name' model='condition.value' none-text='выберите предметы'></ng-multi>
                  </div>
                  <div ng-if="getOption(condition).type == 'place'">
                      <ng-select-new model='condition.value' object="places" label='serp' none-text='неважно где заниматься' convert-to-number></ng-select-new>
                  </div>
                  <div ng-if="getOption(condition).type == 'station_id'">
                      <ng-select-new model='condition.value' object="stations" label='title' none-text='не указано' convert-to-number></ng-select-new>
                  </div>
                  <div ng-if="getOption(condition).type == 'sort'">
                      <ng-select-new model='condition.value' object="sort" label='title' none-text='не указано' convert-to-number></ng-select-new>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal-footer center">
          <div class='center' style='margin-bottom: 5px'>
              <span class="link-like link-gray small" ng-click='addCondition()'>добавить условие</span>
          </div>
        <button type="button" class="btn btn-primary" ng-click="search()" ng-disabled="searching">Поиск</button>
      </div>
    </div>
  </div>
</div>
