{{-- ПЕРЕМЕЩЕНИЕ ЗАЯВКИ --}}
<div id="link-manager" class="modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Добавиь ссылку</h4>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-sm-6">
                  <input class='form-control' placeholder='текст ссылки' type="text" ng-model='link_text'>
              </div>
              <div class="col-sm-6">
                  <div angucomplete-alt id='page-search'
                        placeholder="номер раздела"
                        pause="500"
                        selected-object="searchSelected"
                        remote-api-handler='search'
                        title-field="title"
                        minlength="3"
                        text-searching='поиск...'
                        text-no-results='не найдено'
                        input-class="form-control form-control-small"
                        match-class="highlight"></div>
                  {{-- <input class='form-control' placeholder='номер раздела' type="text" ng-model='link_page_id'> --}}
              </div>
          </div>
      </div>
      <div class="modal-footer center">
        <button type="button" class="btn btn-primary" ng-click="addLink()"
            ng-disabled="!link_page_id">Добавить</button>
      </div>
    </div>
  </div>
</div>
