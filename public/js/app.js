(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module("Egecms", ['ngSanitize', 'ngResource', 'ngAnimate', 'ui.bootstrap', 'angular-ladda', 'angularFileUpload', 'angucomplete-alt', 'ngDrag', 'ngAnimate', 'thatisuday.ng-image-gallery', 'ng-sortable']).config([
    '$compileProvider', function($compileProvider) {
      return $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|chrome-extension|sip):/);
    }
  ]).filter('cut', function() {
    return function(value, wordwise, max, nothing, tail) {
      var lastspace;
      if (nothing == null) {
        nothing = '';
      }
      if (!value) {
        return nothing;
      }
      max = parseInt(max, 10);
      if (!max) {
        return value;
      }
      if (value.length <= max) {
        return value;
      }
      value = value.substr(0, max);
      if (wordwise) {
        lastspace = value.lastIndexOf(' ');
        if (lastspace !== -1) {
          if (value.charAt(lastspace - 1) === '.' || value.charAt(lastspace - 1) === ',') {
            lastspace = lastspace - 1;
          }
          value = value.substr(0, lastspace);
        }
      }
      return value + (tail || '…');
    };
  }).filter('hideZero', function() {
    return function(item) {
      if (item > 0) {
        return item;
      } else {
        return null;
      }
    };
  }).directive('convertToNumber', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, ngModel) {
        ngModel.$parsers.push(function(val) {
          return parseInt(val, 10);
        });
        return ngModel.$formatters.push(function(val) {
          if (val || val === 0) {
            return '' + val;
          } else {
            return '';
          }
        });
      }
    };
  }).run(function($rootScope, $q) {
    $rootScope.dataLoaded = $q.defer();
    $rootScope.frontendStop = function(rebind_masks) {
      if (rebind_masks == null) {
        rebind_masks = true;
      }
      $rootScope.frontend_loading = false;
      $rootScope.dataLoaded.resolve(true);
      if (rebind_masks) {
        return rebindMasks();
      }
    };
    $rootScope.range = function(min, max, step) {
      var i, input;
      step = step || 1;
      input = [];
      i = min;
      while (i <= max) {
        input.push(i);
        i += step;
      }
      return input;
    };
    $rootScope.toggleEnum = function(ngModel, status, ngEnum, skip_values, allowed_user_ids, recursion) {
      var ref, ref1, ref2, status_id, statuses;
      if (skip_values == null) {
        skip_values = [];
      }
      if (allowed_user_ids == null) {
        allowed_user_ids = [];
      }
      if (recursion == null) {
        recursion = false;
      }
      if (!recursion && (ref = parseInt(ngModel[status]), indexOf.call(skip_values, ref) >= 0) && (ref1 = $rootScope.$$childHead.user.id, indexOf.call(allowed_user_ids, ref1) < 0)) {
        return;
      }
      statuses = Object.keys(ngEnum);
      status_id = statuses.indexOf(ngModel[status].toString());
      status_id++;
      if (status_id > (statuses.length - 1)) {
        status_id = 0;
      }
      ngModel[status] = statuses[status_id];
      if (indexOf.call(skip_values, status_id) >= 0 && (ref2 = $rootScope.$$childHead.user.id, indexOf.call(allowed_user_ids, ref2) < 0)) {
        return $rootScope.toggleEnum(ngModel, status, ngEnum, skip_values, allowed_user_ids, true);
      }
    };
    $rootScope.toggleEnumServer = function(ngModel, status, ngEnum, Resource) {
      var status_id, statuses, update_data;
      statuses = Object.keys(ngEnum);
      status_id = statuses.indexOf(ngModel[status].toString());
      status_id++;
      if (status_id > (statuses.length - 1)) {
        status_id = 0;
      }
      update_data = {
        id: ngModel.id
      };
      update_data[status] = status_id;
      return Resource.update(update_data, function() {
        return ngModel[status] = statuses[status_id];
      });
    };
    $rootScope.formatDateTime = function(date) {
      return moment(date).format("DD.MM.YY в HH:mm");
    };
    $rootScope.formatDate = function(date, full_year) {
      if (full_year == null) {
        full_year = false;
      }
      if (!date) {
        return '';
      }
      return moment(date).format("DD.MM.YY" + (full_year ? "YY" : ""));
    };
    $rootScope.dialog = function(id) {
      $("#" + id).modal('show');
    };
    $rootScope.closeDialog = function(id) {
      $("#" + id).modal('hide');
    };
    $rootScope.ajaxStart = function() {
      ajaxStart();
      return $rootScope.saving = true;
    };
    $rootScope.ajaxEnd = function() {
      ajaxEnd();
      return $rootScope.saving = false;
    };
    $rootScope.findById = function(object, id) {
      return _.findWhere(object, {
        id: parseInt(id)
      });
    };
    $rootScope.total = function(array, prop, prop2) {
      var sum;
      if (prop2 == null) {
        prop2 = false;
      }
      sum = 0;
      $.each(array, function(index, value) {
        var v;
        v = value[prop];
        if (prop2) {
          v = v[prop2];
        }
        return sum += v;
      });
      return sum;
    };
    $rootScope.deny = function(ngModel, prop) {
      return ngModel[prop] = +(!ngModel[prop]);
    };
    return $rootScope.formatBytes = function(bytes) {
      if (bytes < 1024) {
        return bytes + ' Bytes';
      } else if (bytes < 1048576) {
        return (bytes / 1024).toFixed(1) + ' KB';
      } else if (bytes < 1073741824) {
        return (bytes / 1048576).toFixed(1) + ' MB';
      } else {
        return (bytes / 1073741824).toFixed(1) + ' GB';
      }
    };
  });

}).call(this);

(function() {
  Vue.config.devtools = true;

  $(document).ready(function() {
    var viewVue;
    $('#searchModalOpen').click(function() {
      var delayFunction;
      $('#searchModal').modal({
        keyboard: true
      });
      delayFunction = function() {
        return $('#searchQueryInput').focus();
      };
      setTimeout(delayFunction, 500);
      $($('body.modal-open .row')[0]).addClass('blur');
      return false;
    });
    $('#searchModal').on('hidden.bs.modal', function() {
      var delayFnc;
      delayFnc = function() {
        return $('.blur').removeClass('blur');
      };
      return setTimeout(delayFnc, 500);
    });
    return viewVue = new Vue({
      el: '#searchModal',
      data: {
        lists: [],
        links: {},
        results: -1,
        active: 0,
        query: '',
        oldquery: '',
        all: 0,
        loading: false
      },
      methods: {
        loadData: _.debounce(function() {
          return this.$http.post('api/search', {
            query: this.query
          }).then((function(_this) {
            return function(success) {
              var i, item, j, k, len, len1, ref, ref1, results;
              _this.loading = false;
              _this.active = 0;
              _this.all = 0;
              _this.lists = [];
              if (success.body.results > 0) {
                _this.results = success.body.results;
                if (success.body.variables.length > 0) {
                  ref = success.body.variables;
                  for (i = j = 0, len = ref.length; j < len; i = ++j) {
                    item = ref[i];
                    console.log(item);
                    item.type = 'variable';
                    _this.all++;
                    _this.links[_this.all] = 'variables/' + item.id + '/edit';
                    item.link = _this.links[_this.all];
                    _this.lists.push(item);
                  }
                }
                if (success.body.pages.length > 0) {
                  ref1 = success.body.pages;
                  results = [];
                  for (i = k = 0, len1 = ref1.length; k < len1; i = ++k) {
                    item = ref1[i];
                    item.type = 'page';
                    _this.all++;
                    _this.links[_this.all] = 'pages/' + item.id + '/edit';
                    item.link = _this.links[_this.all];
                    results.push(_this.lists.push(item));
                  }
                  return results;
                }
              } else {
                _this.active = 0;
                _this.all = 0;
                _this.lists = [];
                return _this.results = 0;
              }
            };
          })(this), (function(_this) {
            return function(error) {
              _this.active = 0;
              _this.all = 0;
              _this.lists = [];
              return _this.results = 0;
            };
          })(this));
        }, 150),
        scroll: function() {
          return $('#searchResult').scrollTop((this.active - 4) * 30);
        },
        getStateClass: function(state) {
          var obj;
          obj = {};
          obj["tutor-state-" + state] = true;
          return obj;
        },
        keyup: function(e) {
          var url;
          if (e.code === 'ArrowUp') {
            e.preventDefault();
            if (this.active > 0) {
              this.active--;
            }
            this.scroll();
          } else if (e.code === 'ArrowDown') {
            e.preventDefault();
            if (this.active < this.results) {
              this.active++;
            }
            if (this.active > 4) {
              this.scroll();
            }
          } else if (e.code === 'Enter' && this.active > 0) {
            url = this.links[this.active];
            window.location = url;
          } else {
            if (this.query !== '') {
              if (this.oldquery !== this.query && this.query.length > 2) {
                this.loadData();
              }
              this.oldquery = this.query;
            } else {
              this.active = 0;
              this.all = 0;
              this.lists = [];
              this.results = -1;
            }
          }
          return null;
        }
      }
    });
  });

}).call(this);

(function() {


}).call(this);

(function() {
  angular.module('Egecms').controller('FaqIndex', function($scope, $rootScope, $attrs, $timeout, Faq, FaqGroup) {
    var dragEnd, l, moveToGroup, updatePositions;
    l = function(e) {
      return console.log(e);
    };
    angular.element(document).ready(function() {
      return $(document).scroll(function(event) {
        if ($(document).scrollTop() + $(window).height() === $(document).height()) {
          $(document).scrollTop($(document).height() - 50);
          return l('scrolled back');
        }
      });
    });
    $scope.$watchCollection('dnd', function(newVal) {
      return l($scope.dnd);
    });
    bindArguments($scope, arguments);
    updatePositions = function(group_ids) {
      if (!_.isArray(group_ids)) {
        group_ids = [group_ids];
      }
      return angular.forEach(group_ids, function(group_id) {
        var group;
        group = $rootScope.findById($scope.groups, group_id);
        return angular.forEach(group.faq, function(faq, index) {
          return Faq.update({
            id: faq.id,
            position: index
          });
        });
      });
    };
    dragEnd = function() {
      return $scope.dnd = {};
    };
    $scope.sortableFaqConf = {
      animation: 150,
      group: {
        name: 'variable',
        put: 'variable'
      },
      fallbackTolerance: 5,
      onUpdate: function(event) {
        return updatePositions($scope.dnd.group_id);
      },
      onAdd: function(event) {
        var faq_id;
        faq_id = $scope.dnd.faq_id;
        if ($scope.dnd.group_id && $scope.dnd.faq_id && ($scope.dnd.group_id !== $scope.dnd.old_group_id)) {
          if ($scope.dnd.group_id === -1) {
            return FaqGroup.save({
              faq_id: $scope.dnd.faq_id
            }, function(response) {
              $scope.groups.push(response);
              moveToGroup($scope.dnd.faq_id, response.id, $scope.dnd.old_group_id, true);
              return dragEnd();
            });
          } else if ($scope.dnd.group_id) {
            moveToGroup($scope.dnd.faq_id, $scope.dnd.group_id, $scope.dnd.old_group_id);
            return updatePositions([$scope.dnd.group_id, $scope.dnd.old_group_id]);
          }
        }
      },
      onEnd: function(event) {
        if ($scope.dnd.group_id !== -1) {
          return dragEnd();
        }
      }
    };
    $scope.dragOver = function(group) {
      if ($scope.dnd.type !== 'group') {
        return $scope.dnd.group_id = group.id;
      }
    };
    $scope.sortableGroupConf = {
      animation: 150,
      handle: '.group-title',
      dragClass: 'dragging-group',
      onUpdate: function(event) {
        return angular.forEach($scope.groups, function(group, index) {
          group.position = index;
          return FaqGroup.update({
            id: group.id,
            position: index
          });
        });
      },
      onStart: function(event) {
        return $scope.dnd.type = 'group';
      },
      onEnd: function(event) {
        return $scope.dnd = {};
      }
    };
    $scope.dnd = {};
    moveToGroup = function(faq_id, group_id, old_group_id, copy_item) {
      var faq, group_from, group_to;
      if (copy_item == null) {
        copy_item = false;
      }
      Faq.update({
        id: faq_id,
        group_id: group_id
      });
      group_from = _.find($scope.groups, {
        id: old_group_id
      });
      faq = _.clone(findById(group_from.faq, faq_id));
      faq.group_id = group_id;
      group_from.faq = removeById(group_from.faq, faq_id);
      group_to = _.find($scope.groups, {
        id: group_id
      });
      if (copy_item) {
        return group_to.faq.push(faq);
      } else {
        faq = $rootScope.findById(group_to.faq, faq_id);
        return faq.group_id = group_id;
      }
    };
    $scope.removeGroup = function(group) {
      return bootbox.confirm("Вы уверены, что хотите удалить группу «" + group.title + "»", function(response) {
        var new_group_id;
        if (response === true) {
          FaqGroup.remove({
            id: group.id
          });
          new_group_id = (_.max(_.without($scope.groups, group), function(group) {
            return group.position;
          })).id;
          if (group.faq) {
            angular.forEach(group.faq, function(faq) {
              return moveToGroup(faq.id, new_group_id, faq.group_id, true);
            });
            updatePositions(new_group_id);
          }
          return $scope.groups = removeById($scope.groups, group.id);
        }
      });
    };
    return $scope.onEdit = function(id, event) {
      return FaqGroup.update({
        id: id,
        title: $(event.target).text()
      });
    };
  }).controller('FaqForm', function($scope, $attrs, $timeout, FormService, AceService, Faq) {
    bindArguments($scope, arguments);
    return angular.element(document).ready(function() {
      return FormService.init(Faq, $scope.id, $scope.model);
    });
  });

}).call(this);

(function() {
  angular.module('Egecms').controller('LoginCtrl', function($scope, $http) {
    angular.element(document).ready(function() {
      var login_data;
      $scope.l = Ladda.create(document.querySelector('#login-submit'));
      login_data = $.cookie("login_data");
      if (login_data !== void 0) {
        login_data = JSON.parse(login_data);
        $scope.login = login_data.login;
        $scope.password = login_data.password;
        $scope.sms_verification = true;
        return $scope.$apply();
      }
    });
    $scope.enter = function($event) {
      if ($event.keyCode === 13) {
        return $scope.checkFields();
      }
    };
    $scope.goLogin = function() {
      ajaxStart();
      return $http.post('login', {
        login: $scope.login,
        password: $scope.password,
        code: $scope.code,
        captcha: grecaptcha.getResponse()
      }).then(function(response) {
        grecaptcha.reset();
        if (response.data === true) {
          $.removeCookie('login_data');
          return location.reload();
        } else if (response.data === 'sms') {
          ajaxEnd();
          $scope.in_process = false;
          $scope.l.stop();
          $scope.sms_verification = true;
          return $.cookie("login_data", JSON.stringify({
            login: $scope.login,
            password: $scope.password
          }), {
            expires: 1 / (24 * 60) * 2,
            path: '/'
          });
        } else {
          $scope.in_process = false;
          ajaxEnd();
          $scope.l.stop();
          return notifyError("Неправильная пара логин-пароль");
        }
      });
    };
    return $scope.checkFields = function() {
      $scope.l.start();
      $scope.in_process = true;
      if (grecaptcha.getResponse() === '') {
        return grecaptcha.execute();
      } else {
        return $scope.goLogin();
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').controller('PagesIndex', function($scope, $attrs, $rootScope, $timeout, IndexService, Page, Published, ExportService, PageGroup) {
    var dragEnd, l, moveToGroup, updatePositions;
    l = function(e) {
      return console.log(e);
    };
    angular.element(document).ready(function() {
      return $(document).scroll(function(event) {
        if ($(document).scrollTop() + $(window).height() === $(document).height()) {
          $(document).scrollTop($(document).height() - 50);
          return l('scrolled back');
        }
      });
    });
    $scope.$watchCollection('dnd', function(newVal) {
      return l($scope.dnd);
    });
    bindArguments($scope, arguments);
    ExportService.init({
      controller: 'pages'
    });
    updatePositions = function(group_ids) {
      if (!_.isArray(group_ids)) {
        group_ids = [group_ids];
      }
      return angular.forEach(group_ids, function(group_id) {
        var group;
        group = $rootScope.findById($scope.groups, group_id);
        return angular.forEach(group.page, function(page, index) {
          return Page.update({
            id: page.id,
            position: index
          });
        });
      });
    };
    dragEnd = function() {
      return $scope.dnd = {};
    };
    $scope.sortablePageConf = {
      animation: 150,
      group: {
        name: 'variable',
        put: 'variable'
      },
      fallbackTolerance: 5,
      onUpdate: function(event) {
        return updatePositions($scope.dnd.group_id);
      },
      onAdd: function(event) {
        var page_id;
        page_id = $scope.dnd.page_id;
        if ($scope.dnd.group_id && $scope.dnd.page_id && ($scope.dnd.group_id !== $scope.dnd.old_group_id)) {
          if ($scope.dnd.group_id === -1) {
            return PageGroup.save({
              page_id: $scope.dnd.page_id
            }, function(response) {
              $scope.groups.push(response);
              moveToGroup($scope.dnd.page_id, response.id, $scope.dnd.old_group_id, true);
              return dragEnd();
            });
          } else if ($scope.dnd.group_id) {
            moveToGroup($scope.dnd.page_id, $scope.dnd.group_id, $scope.dnd.old_group_id);
            return updatePositions([$scope.dnd.group_id, $scope.dnd.old_group_id]);
          }
        }
      },
      onEnd: function(event) {
        if ($scope.dnd.group_id !== -1) {
          return dragEnd();
        }
      }
    };
    $scope.dragOver = function(group) {
      if ($scope.dnd.type !== 'group') {
        return $scope.dnd.group_id = group.id;
      }
    };
    $scope.sortableGroupConf = {
      animation: 150,
      handle: '.group-title',
      dragClass: 'dragging-group',
      onUpdate: function(event) {
        return angular.forEach($scope.groups, function(group, index) {
          group.position = index;
          return PageGroup.update({
            id: group.id,
            position: index
          });
        });
      },
      onStart: function(event) {
        return $scope.dnd.type = 'group';
      },
      onEnd: function(event) {
        return $scope.dnd = {};
      }
    };
    $scope.dnd = {};
    moveToGroup = function(page_id, group_id, old_group_id, copy_item) {
      var group_from, group_to, page;
      if (copy_item == null) {
        copy_item = false;
      }
      Page.update({
        id: page_id,
        group_id: group_id
      });
      group_from = _.find($scope.groups, {
        id: old_group_id
      });
      page = _.clone(findById(group_from.page, page_id));
      page.group_id = group_id;
      group_from.page = removeById(group_from.page, page_id);
      group_to = _.find($scope.groups, {
        id: group_id
      });
      if (copy_item) {
        return group_to.page.push(page);
      } else {
        page = $rootScope.findById(group_to.page, page_id);
        return page.group_id = group_id;
      }
    };
    $scope.removeGroup = function(group) {
      return bootbox.confirm("Вы уверены, что хотите удалить группу «" + group.title + "»", function(response) {
        var new_group_id;
        if (response === true) {
          PageGroup.remove({
            id: group.id
          });
          new_group_id = (_.max(_.without($scope.groups, group), function(group) {
            return group.position;
          })).id;
          if (group.page) {
            angular.forEach(group.page, function(page) {
              return moveToGroup(page.id, new_group_id, page.group_id, true);
            });
            updatePositions(new_group_id);
          }
          return $scope.groups = removeById($scope.groups, group.id);
        }
      });
    };
    return $scope.onEdit = function(id, event) {
      return PageGroup.update({
        id: id,
        title: $(event.target).text()
      });
    };
  }).controller('PagesForm', function($scope, $http, $attrs, $timeout, FormService, AceService, Page, Published, UpDown) {
    var empty_useful;
    bindArguments($scope, arguments);
    empty_useful = {
      text: null,
      page_id_field: null
    };
    angular.element(document).ready(function() {
      FormService.init(Page, $scope.id, $scope.model);
      FormService.dataLoaded.promise.then(function() {
        if (!FormService.model.useful || !FormService.model.useful.length) {
          FormService.model.useful = [angular.copy(empty_useful)];
        }
        return ['html', 'html_mobile', 'seo_text'].forEach(function(field) {
          return AceService.initEditor(FormService, 15, "editor--" + field);
        });
      });
      return FormService.beforeSave = function() {
        return ['html', 'html_mobile', 'seo_text'].forEach(function(field) {
          return FormService.model[field] = AceService.getEditor("editor--" + field).getValue();
        });
      };
    });
    $scope.generateUrl = function(event) {
      return $http.post('/api/translit/to-url', {
        text: FormService.model.keyphrase
      }).then(function(response) {
        FormService.model.url = response.data;
        return $scope.checkExistance('url', {
          target: $(event.target).closest('div').find('input')
        });
      });
    };
    $scope.checkExistance = function(field, event) {
      return Page.checkExistance({
        id: FormService.model.id,
        field: field,
        value: FormService.model[field]
      }, function(response) {
        var element;
        element = $(event.target);
        if (response.exists) {
          FormService.error_element = element;
          return element.addClass('has-error').focus();
        } else {
          FormService.error_element = void 0;
          return element.removeClass('has-error');
        }
      });
    };
    $scope.checkUsefulExistance = function(field, event, value) {
      return Page.checkExistance({
        id: FormService.model.id,
        field: field,
        value: value
      }, function(response) {
        var element;
        element = $(event.target);
        if (!value || response.exists) {
          FormService.error_element = void 0;
          return element.removeClass('has-error');
        } else {
          FormService.error_element = element;
          return element.addClass('has-error').focus();
        }
      });
    };
    $scope.addUseful = function() {
      return FormService.model.useful.push(angular.copy(empty_useful));
    };
    $scope.addLinkDialog = function() {
      $scope.link_text = AceService.editor.getSelectedText();
      return $('#link-manager').modal('show');
    };
    $scope.search = function(input, promise) {
      return $http.post('api/pages/search', {
        q: input
      }, {
        timeout: promise
      }).then(function(response) {
        return response;
      });
    };
    $scope.searchSelected = function(selectedObject) {
      $scope.link_page_id = selectedObject.originalObject.id;
      return $scope.$broadcast('angucomplete-alt:changeInput', 'page-search', $scope.link_page_id.toString());
    };
    $scope.addLink = function() {
      var link;
      link = "<a href='[link|" + $scope.link_page_id + "]'>" + $scope.link_text + "</a>";
      $scope.link_page_id = void 0;
      $scope.$broadcast('angucomplete-alt:clearInput');
      AceService.editor.session.replace(AceService.editor.selection.getRange(), link);
      return $('#link-manager').modal('hide');
    };
    return $scope.$watch('FormService.model.station_id', function(newVal, oldVal) {
      return $timeout(function() {
        return $('#sort').selectpicker('refresh');
      });
    });
  });

}).call(this);

(function() {
  angular.module('Egecms').config(function(ngImageGalleryOptsProvider) {
    return ngImageGalleryOptsProvider.setOpts({
      thumbnails: true,
      inline: false,
      imgBubbles: false,
      bgClose: true,
      imgAnim: 'fadeup'
    });
  }).controller('PhotosIndex', function($scope, $rootScope, $attrs, Photo, PhotoGroup, FormService, FileUploader) {
    var dragEnd, l, moveToGroup, updatePositions;
    l = function(e) {
      return console.log(e);
    };
    angular.element(document).ready(function() {
      return $(document).scroll(function(event) {
        if ($(document).scrollTop() + $(window).height() === $(document).height()) {
          $(document).scrollTop($(document).height() - 50);
          return l('scrolled back');
        }
      });
    });
    $scope.$watchCollection('dnd', function(newVal) {
      return l($scope.dnd);
    });
    bindArguments($scope, arguments);
    updatePositions = function(group_ids) {
      if (!_.isArray(group_ids)) {
        group_ids = [group_ids];
      }
      return angular.forEach(group_ids, function(group_id) {
        var group;
        group = $rootScope.findById($scope.groups, group_id);
        return angular.forEach(group.photo, function(photo, index) {
          return Photo.update({
            id: photo.id,
            position: index
          });
        });
      });
    };
    dragEnd = function() {
      return $scope.dnd = {};
    };
    $scope.sortablePhotosConf = {
      animation: 150,
      group: {
        name: 'variable',
        put: 'variable'
      },
      fallbackTolerance: 5,
      onUpdate: function(event) {
        return updatePositions($scope.dnd.group_id);
      },
      onAdd: function(event) {
        var photo_id;
        photo_id = $scope.dnd.photo_id;
        if ($scope.dnd.group_id && $scope.dnd.photo_id && ($scope.dnd.group_id !== $scope.dnd.old_group_id)) {
          if ($scope.dnd.group_id === -1) {
            return PhotoGroup.save({
              photo_id: $scope.dnd.photo_id
            }, function(response) {
              $scope.groups.push(response);
              moveToGroup($scope.dnd.photo_id, response.id, $scope.dnd.old_group_id, true);
              return dragEnd();
            });
          } else if ($scope.dnd.group_id) {
            moveToGroup($scope.dnd.photo_id, $scope.dnd.group_id, $scope.dnd.old_group_id);
            return updatePositions([$scope.dnd.group_id, $scope.dnd.old_group_id]);
          }
        }
      },
      onEnd: function(event) {
        if ($scope.dnd.group_id !== -1) {
          return dragEnd();
        }
      }
    };
    $scope.dragOver = function(group) {
      if ($scope.dnd.type !== 'group') {
        return $scope.dnd.group_id = group.id;
      }
    };
    $scope.sortableGroupConf = {
      animation: 150,
      handle: '.group-title',
      dragClass: 'dragging-group',
      onUpdate: function(event) {
        return angular.forEach($scope.groups, function(group, index) {
          group.position = index;
          return PhotoGroup.update({
            id: group.id,
            position: index
          });
        });
      },
      onStart: function(event) {
        return $scope.dnd.type = 'group';
      },
      onEnd: function(event) {
        return $scope.dnd = {};
      }
    };
    $scope.dnd = {};
    moveToGroup = function(photo_id, group_id, old_group_id, copy_item) {
      var group_from, group_to, photo;
      if (copy_item == null) {
        copy_item = false;
      }
      Photo.update({
        id: photo_id,
        group_id: group_id
      });
      group_from = _.find($scope.groups, {
        id: old_group_id
      });
      photo = _.clone(findById(group_from.photo, photo_id));
      photo.group_id = group_id;
      group_from.photo = removeById(group_from.photo, photo_id);
      group_to = _.find($scope.groups, {
        id: group_id
      });
      if (copy_item) {
        return group_to.photo.push(photo);
      } else {
        photo = $rootScope.findById(group_to.photo, photo_id);
        return photo.group_id = group_id;
      }
    };
    $scope.removeGroup = function(group) {
      return bootbox.confirm("Вы уверены, что хотите удалить группу «" + group.title + "»", function(response) {
        var new_group_id;
        if (response === true) {
          PhotoGroup.remove({
            id: group.id
          });
          new_group_id = (_.max(_.without($scope.groups, group), function(group) {
            return group.position;
          })).id;
          if (group.photo) {
            angular.forEach(group.photo, function(photo) {
              return moveToGroup(photo.id, new_group_id, photo.group_id, true);
            });
            updatePositions(new_group_id);
          }
          return $scope.groups = removeById($scope.groups, group.id);
        }
      });
    };
    $scope.onEdit = function(id, event) {
      return PhotoGroup.update({
        id: id,
        title: $(event.target).text()
      });
    };
    $scope["delete"] = function(event, model) {
      FormService.model = new Photo(model);
      return FormService["delete"](event, (function(_this) {
        return function() {
          return _.each($scope.groups, function(group) {
            return group.photo = _.without(group.photo, model);
          });
        };
      })(this));
    };
    $scope.upload = function(model) {
      $scope.editing_model = model;
      return window.upload();
    };
    $scope.totalPhotos = function() {
      if (!$scope.groups) {
        return;
      }
      return _.reduce($scope.groups, function(sum, group) {
        return sum += group.photo.length;
      }, 0);
    };
    $scope.Uploader = new FileUploader({
      url: 'api/photos/upload',
      alias: 'file',
      filters: [
        {
          name: 'imageFilter',
          fn: function(file, options) {
            var type;
            type = "|" + (file.type.slice(file.type.lastIndexOf('/') + 1)) + "|";
            return '|jpg|png|jpeg|'.indexOf(type) !== -1;
          }
        }
      ],
      autoUpload: true,
      removeAfterUpload: true
    });
    $scope.Uploader.onSuccessItem = (function(_this) {
      return function(item, response) {
        var group, index;
        if ($scope.editing_model) {
          group = _.find($scope.groups, {
            id: response.group_id
          });
          index = _.findIndex(group.photo, {
            id: response.id
          });
          group.photo[index] = response;
        } else {
          group = _.find($scope.groups, {
            id: response.group_id
          });
          group.photo.push(response);
        }
        $scope.editing_model = null;
        if (typeof $scope.onSuccessItemCallback === 'function') {
          return $scope.onSuccessItemCallback();
        }
      };
    })(this);
    $scope.Uploader.onBeforeUploadItem = (function(_this) {
      return function(item) {
        var ref;
        return item.formData.push({
          old_file: (ref = $scope.editing_model) != null ? ref.filename : void 0
        });
      };
    })(this);
    return $scope.filesize = function(size) {
      var unit, units;
      units = ['B', 'Kb', 'Mb', 'Gb'];
      unit = 0;
      while (size > 1024) {
        size = size / 1024;
        unit++;
      }
      return size.toFixed(1) + units[unit];
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').controller('ProgramsIndex', function($scope, $attrs, IndexService, Program) {
    bindArguments($scope, arguments);
    angular.element(document).ready(function() {
      return IndexService.init(Program, $scope.current_page, $attrs);
    });
    return $scope.childLessonSum = function(model) {
      if (!(model && model.content)) {
        return 0;
      }
      if (!model.content.length) {
        return +model.lesson_count || 0;
      }
      return _.reduce(model.content, function(sum, value) {
        return sum + parseInt($scope.childLessonSum(value));
      }, 0);
    };
  }).controller('ProgramsForm', function($scope, $attrs, $timeout, FormService, Program) {
    bindArguments($scope, arguments);
    return angular.element(document).ready(function() {
      return FormService.init(Program, $scope.id, $scope.model);
    });
  });

}).call(this);

(function() {
  angular.module('Egecms').controller('SassIndex', function($scope, $attrs, $http, IndexService, Sass) {
    bindArguments($scope, arguments);
    $scope.getName = function(path) {
      return path.split('/').slice(-1)[0];
    };
    return angular.element(document).ready(function() {
      return $http.get("api" + $scope.current_path, {}).then(function(response) {
        return $scope.data = response.data;
      });
    });
  }).controller('SassForm', function($scope, $rootScope, $http, $timeout, FormService, AceService, Sass) {
    bindArguments($scope, arguments);
    $rootScope.frontend_loading = true;
    angular.element(document).ready(function() {
      return $http.get('api/sass/' + $scope.file).then(function(response) {
        $scope.text = response.data;
        return $timeout(function() {
          $rootScope.frontend_loading = false;
          $scope.editor = ace.edit('editor');
          $scope.editor.getSession().setMode('ace/mode/css');
          $scope.editor.getSession().setUseWrapMode(true);
          return $scope.editor.setOptions({
            minLines: 20,
            maxLines: 2e308
          });
        });
      });
    });
    return $scope.save = function() {
      ajaxStart();
      return $http.post('api/sass/' + $scope.file, {
        text: $scope.editor.getValue()
      }).then(function() {
        return ajaxEnd();
      });
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').controller('SearchIndex', function($scope, $attrs, $timeout, IndexService, Page, Published, ExportService) {
    bindArguments($scope, arguments);
    ExportService.init({
      controller: 'pages'
    });
    return angular.element(document).ready(function() {
      return IndexService.init(Page, $scope.current_page, $attrs, false);
    });
  });

}).call(this);

(function() {
  angular.module('Egecms').controller('VariablesIndex', function($scope, $attrs, $rootScope, $timeout, IndexService, Variable, VariableGroup) {
    var dragEnd, l, moveToGroup, updatePositions;
    l = function(e) {
      return console.log(e);
    };
    angular.element(document).ready(function() {
      return $(document).scroll(function(event) {
        if ($(document).scrollTop() + $(window).height() === $(document).height()) {
          $(document).scrollTop($(document).height() - 50);
          return l('scrolled back');
        }
      });
    });
    $scope.$watchCollection('dnd', function(newVal) {
      return l($scope.dnd);
    });
    bindArguments($scope, arguments);
    updatePositions = function(group_ids) {
      if (!_.isArray(group_ids)) {
        group_ids = [group_ids];
      }
      return angular.forEach(group_ids, function(group_id) {
        var group;
        group = $rootScope.findById($scope.groups, group_id);
        return angular.forEach(group.variable, function(variable, index) {
          return Variable.update({
            id: variable.id,
            position: index
          });
        });
      });
    };
    dragEnd = function() {
      return $scope.dnd = {};
    };
    $scope.sortableVariableConf = {
      animation: 150,
      group: {
        name: 'variable',
        put: 'variable'
      },
      fallbackTolerance: 5,
      onUpdate: function(event) {
        return updatePositions($scope.dnd.group_id);
      },
      onAdd: function(event) {
        var variable_id;
        variable_id = $scope.dnd.variable_id;
        if ($scope.dnd.group_id && $scope.dnd.variable_id && ($scope.dnd.group_id !== $scope.dnd.old_group_id)) {
          if ($scope.dnd.group_id === -1) {
            return VariableGroup.save({
              variable_id: $scope.dnd.variable_id
            }, function(response) {
              $scope.groups.push(response);
              moveToGroup($scope.dnd.variable_id, response.id, $scope.dnd.old_group_id, true);
              return dragEnd();
            });
          } else if ($scope.dnd.group_id) {
            moveToGroup($scope.dnd.variable_id, $scope.dnd.group_id, $scope.dnd.old_group_id);
            return updatePositions([$scope.dnd.group_id, $scope.dnd.old_group_id]);
          }
        }
      },
      onEnd: function(event) {
        if ($scope.dnd.group_id !== -1) {
          return dragEnd();
        }
      }
    };
    $scope.dragOver = function(group) {
      if ($scope.dnd.type !== 'group') {
        return $scope.dnd.group_id = group.id;
      }
    };
    $scope.sortableGroupConf = {
      animation: 150,
      handle: '.group-title',
      dragClass: 'dragging-group',
      onUpdate: function(event) {
        return angular.forEach($scope.groups, function(group, index) {
          group.position = index;
          return VariableGroup.update({
            id: group.id,
            position: index
          });
        });
      },
      onStart: function(event) {
        return $scope.dnd.type = 'group';
      },
      onEnd: function(event) {
        return $scope.dnd = {};
      }
    };
    $scope.dnd = {};
    moveToGroup = function(variable_id, group_id, old_group_id, copy_item) {
      var group_from, group_to, variable;
      if (copy_item == null) {
        copy_item = false;
      }
      Variable.update({
        id: variable_id,
        group_id: group_id
      });
      group_from = _.find($scope.groups, {
        id: old_group_id
      });
      variable = _.clone(findById(group_from.variable, variable_id));
      variable.group_id = group_id;
      group_from.variable = removeById(group_from.variable, variable_id);
      group_to = _.find($scope.groups, {
        id: group_id
      });
      if (copy_item) {
        return group_to.variable.push(variable);
      } else {
        variable = $rootScope.findById(group_to.variable, variable_id);
        return variable.group_id = group_id;
      }
    };
    $scope.removeGroup = function(group) {
      return bootbox.confirm("Вы уверены, что хотите удалить группу «" + group.title + "»", function(response) {
        var new_group_id;
        if (response === true) {
          VariableGroup.remove({
            id: group.id
          });
          new_group_id = (_.max(_.without($scope.groups, group), function(group) {
            return group.position;
          })).id;
          if (group.variable) {
            angular.forEach(group.variable, function(variable) {
              return moveToGroup(variable.id, new_group_id, variable.group_id, true);
            });
            updatePositions(new_group_id);
          }
          return $scope.groups = removeById($scope.groups, group.id);
        }
      });
    };
    return $scope.onEdit = function(id, event) {
      return VariableGroup.update({
        id: id,
        title: $(event.target).text()
      });
    };
  }).controller('VariablesForm', function($scope, $attrs, $timeout, FormService, AceService, Variable) {
    bindArguments($scope, arguments);
    return angular.element(document).ready(function() {
      FormService.init(Variable, $scope.id, $scope.model);
      FormService.dataLoaded.promise.then(function() {
        AceService.initEditor(FormService, 30);
        if (FormService.model.html[0] === '{') {
          return AceService.editor.getSession().setMode('ace/mode/json');
        }
      });
      return FormService.beforeSave = function() {
        return FormService.model.html = AceService.editor.getValue();
      };
    });
  });

}).call(this);

(function() {
  angular.module('Egecms').value('Published', [
    {
      id: 0,
      title: 'не опубликовано'
    }, {
      id: 1,
      title: 'опубликовано'
    }
  ]).value('UpDown', [
    {
      id: 1,
      title: 'вверху'
    }, {
      id: 2,
      title: 'внизу'
    }
  ]);

}).call(this);

(function() {
  var apiPath, countable, updatable;

  angular.module('Egecms').factory('Variable', function($resource) {
    return $resource(apiPath('variables'), {
      id: '@id'
    }, updatable());
  }).factory('VariableGroup', function($resource) {
    return $resource(apiPath('variables/groups'), {
      id: '@id'
    }, updatable());
  }).factory('PageGroup', function($resource) {
    return $resource(apiPath('pages/groups'), {
      id: '@id'
    }, updatable());
  }).factory('Sass', function($resource) {
    return $resource(apiPath('sass'), {
      id: '@id'
    }, updatable());
  }).factory('Page', function($resource) {
    return $resource(apiPath('pages'), {
      id: '@id'
    }, {
      update: {
        method: 'PUT'
      },
      checkExistance: {
        method: 'POST',
        url: apiPath('pages', 'checkExistance')
      }
    });
  }).factory('Program', function($resource) {
    return $resource(apiPath('programs'), {
      id: '@id'
    }, updatable());
  }).factory('Photo', function($resource) {
    return $resource(apiPath('photos'), {
      id: '@id'
    }, {
      update: {
        method: 'PUT'
      },
      updateAll: {
        method: 'POST',
        url: apiPath('photos', 'updateAll')
      }
    });
  }).factory('PhotoGroup', function($resource) {
    return $resource(apiPath('photos/groups'), {
      id: '@id'
    }, updatable());
  }).factory('Faq', function($resource) {
    return $resource(apiPath('faq'), {
      id: '@id'
    }, updatable());
  }).factory('FaqGroup', function($resource) {
    return $resource(apiPath('faq/groups'), {
      id: '@id'
    }, updatable());
  });

  apiPath = function(entity, additional) {
    if (additional == null) {
      additional = '';
    }
    return ("api/" + entity + "/") + (additional ? additional + '/' : '') + ":id";
  };

  updatable = function() {
    return {
      update: {
        method: 'PUT'
      }
    };
  };

  countable = function() {
    return {
      count: {
        method: 'GET'
      }
    };
  };

}).call(this);

(function() {


}).call(this);

(function() {


}).call(this);

(function() {
  angular.module('Egecms').directive('ngCounter', function($timeout) {
    return {
      restrict: 'A',
      link: function($scope, $element, $attrs) {
        var counter;
        $($element).parent().append("<span class='input-counter'></span>");
        counter = $($element).parent().find('.input-counter');
        $($element).on('keyup', function() {
          return counter.text($(this).val().length || '');
        });
        return $timeout(function() {
          return $($element).keyup();
        }, 500);
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').directive('digitsOnly', function() {
    return {
      restricts: 'A',
      require: 'ngModel',
      link: function($scope, $element, $attr, $ctrl) {
        var filter, ref;
        filter = function(value) {
          var new_value;
          if (!value) {
            return void 0;
          }
          new_value = value.replace(/[^0-9]/g, '');
          if (new_value !== value) {
            $ctrl.$setViewValue(new_value);
            $ctrl.$render();
          }
          return value;
        };
        return (ref = $ctrl.$parsers) != null ? ref.push(filter) : void 0;
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').directive('editable', function() {
    return {
      restrict: 'A',
      link: function($scope, $element, $attrs) {
        return $element.on('click', function(event) {
          return $element.attr('contenteditable', 'true').focus();
        }).on('keydown', function(event) {
          var ref;
          if ((ref = event.keyCode) === 13 || ref === 27) {
            event.preventDefault();
            $element.blur();
          }
          if ($element.data('input-digits-only')) {
            if (!(event.keyCode < 57)) {
              return event.preventDefault();
            }
          }
        }).on('blur', function(event) {
          $scope.onEdit($element.attr('editable'), event);
          return $element.removeAttr('contenteditable');
        });
      }
    };
  });

}).call(this);

(function() {


}).call(this);

(function() {


}).call(this);

(function() {
  angular.module('Egecms').directive('jumpOnTab', function() {
    return {
      restrict: 'A',
      link: function($scope, $element, $attr) {
        return $element.on('keydown', function(event) {
          var focused_item, next_node, range;
          if (event.keyCode === 9) {
            event.preventDefault();
            next_node = $(event.target).parents('h2').first().find('.' + $attr.jumpOnTab);
            next_node = next_node.trigger('click').trigger('focus');
            focused_item = next_node[0];
            if (focused_item.childNodes.length) {
              range = document.createRange();
              range.setStart(focused_item.childNodes[0], focused_item.innerText.length);
              range.collapse(true);
              window.getSelection().removeAllRanges();
              return window.getSelection().addRange(range);
            }
          }
        });
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').directive('ngMulti', function() {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        object: '=',
        model: '=',
        label: '@',
        noneText: '@'
      },
      templateUrl: 'directives/ngmulti',
      controller: function($scope, $element, $attrs, $timeout) {
        $element.selectpicker({
          noneSelectedText: $scope.noneText
        });
        return $scope.$watchGroup(['model', 'object'], function(newVal) {
          if (newVal) {
            return $element.selectpicker('refresh');
          }
        });
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').directive('orderBy', function() {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        options: '='
      },
      templateUrl: 'directives/order-by',
      link: function($scope, $element, $attrs) {
        var IndexService, local_storage_key, syncIndexService;
        IndexService = $scope.$parent.IndexService;
        local_storage_key = 'sort-' + IndexService.controller;
        syncIndexService = function(sort) {
          IndexService.sort = sort;
          IndexService.current_page = 1;
          return IndexService.loadPage();
        };
        $scope.setSort = function(sort) {
          $scope.sort = sort;
          localStorage.setItem(local_storage_key, sort);
          return syncIndexService(sort);
        };
        $scope.sort = localStorage.getItem(local_storage_key);
        if ($scope.sort === null) {
          return $scope.setSort(0);
        } else {
          return syncIndexService($scope.sort);
        }
      }
    };
  });

}).call(this);

(function() {


}).call(this);

(function() {


}).call(this);

(function() {
  angular.module('Egecms').directive('plural', function() {
    return {
      restrict: 'E',
      scope: {
        count: '=',
        type: '@',
        noneText: '@'
      },
      templateUrl: 'directives/plural',
      controller: function($scope, $element, $attrs, $timeout) {
        $scope.textOnly = $attrs.hasOwnProperty('textOnly');
        $scope.hideZero = $attrs.hasOwnProperty('hideZero');
        return $scope.when = {
          'age': ['год', 'года', 'лет'],
          'student': ['ученик', 'ученика', 'учеников'],
          'minute': ['минуту', 'минуты', 'минут'],
          'hour': ['час', 'часа', 'часов'],
          'day': ['день', 'дня', 'дней'],
          'meeting': ['встреча', 'встречи', 'встреч'],
          'score': ['балл', 'балла', 'баллов'],
          'rubbles': ['рубль', 'рубля', 'рублей'],
          'lesson': ['занятие', 'занятия', 'занятий'],
          'client': ['клиент', 'клиента', 'клиентов'],
          'mark': ['оценки', 'оценок', 'оценок'],
          'request': ['заявка', 'заявки', 'заявок'],
          'hour': ['час', 'часа', 'часов'],
          'photo': ['фотография', 'фотографии', 'фотографий']
        };
      }
    };
  });

}).call(this);

(function() {


}).call(this);

(function() {
  var TAB;

  TAB = 9;

  angular.module('Egecms').directive('programItem', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/program-item',
      scope: {
        item: '=',
        level: '=?',
        levelstring: '=',
        "delete": '&delete'
      },
      controller: function($timeout, $element, $scope) {
        var resetNewItem;
        $scope.edit = function() {
          return $scope.editing = true;
        };
        $scope.fake_id = 0;
        $scope.onEdit = function(field, event) {
          var elem, value;
          elem = $(event.target);
          value = elem.text().trim();
          return $scope.$apply(function() {
            return $scope.item[field] = value;
          });
        };
        $scope.editKeydown = function(event) {
          var elem, ref;
          elem = $(event.target);
          if ((ref = event != null ? event.keyCode : void 0) === 13 || ref === 27) {
            event.preventDefault();
            elem.blur();
          }
          if (elem.data('input-digits-only')) {
            if (!(event.keyCode < 57)) {
              return event.preventDefault();
            }
          }
        };
        $scope.addChild = function(event) {
          $scope.is_adding = true;
          return $timeout(function() {
            return $(event.target).parents('li').first().find('input.add-item-title').last().focus();
          });
        };
        $scope.createChild = function(event) {
          if ((event != null ? event.keyCode : void 0) === 13) {
            event.preventDefault();
            if ($scope.new_item.title) {
              if (!$scope.item.content) {
                $scope.item.content = [];
              }
              if ($scope.new_item.title.length) {
                $scope.item.content.push($scope.new_item);
                resetNewItem(event);
                $scope.addChild(event);
              }
            }
          }
          if (event.keyCode === 27) {
            event.preventDefault();
            $(event.target).blur();
          }
          if (event.keyCode === TAB) {
            if ($(event.target).is(':not(.add-item-lesson-count)')) {
              return $scope.is_tabbing = true;
            }
          }
        };
        $scope.deleteChild = function(child) {
          return $scope.item.content = _.without($scope.item.content, child);
        };
        $scope.blur = function(event) {
          if ($scope.is_tabbing) {
            $scope.is_tabbing = false;
            return event.preventDefault();
          }
          $scope.is_adding = false;
          return $scope.is_editing = false;
        };
        $scope.focus = function() {
          return $scope.is_adding = true;
        };
        $scope.getChildLevelString = function(child_index) {
          var str;
          str = $scope.levelstring ? $scope.levelstring : '';
          return str + (child_index + 1) + '.';
        };
        $scope.getLessonCount = function() {
          return $scope.item.lesson_count;
        };
        $scope.childLessonSum = function(item) {
          if (!(item && item.content)) {
            return 0;
          }
          if (!item.content.length) {
            return +item.lesson_count || 0;
          }
          return _.reduce(item.content, function(sum, value) {
            return sum + parseInt($scope.childLessonSum(value));
          }, 0);
        };
        resetNewItem = function(event) {
          $scope.new_item = {
            title: '',
            lesson_count: '',
            child_lesson_sum: '',
            content: [],
            fake_id: $scope.fake_id
          };
          return $scope.fake_id++;
        };
        if (!$scope.level) {
          $scope.level = 1;
        }
        if (!$scope.lesson_count) {
          $scope.lesson_count = 0;
        }
        resetNewItem();
        $scope.editBlur = function() {
          return $scope.editing = false;
        };
        return $scope.editFocus = function() {
          return $scope.editing = true;
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').directive('search', function() {
    return {
      restrict: 'E',
      templateUrl: 'directives/search',
      scope: {},
      link: function() {
        return $('.search-icon').on('click', function() {
          return $('#search-app').modal('show');
        });
      },
      controller: function($scope, $timeout, $http, Published, FactoryService) {
        bindArguments($scope, arguments);
        $scope.conditions = [];
        $scope.options = [
          {
            title: 'ключевая фраза',
            value: 'keyphrase',
            type: 'text'
          }, {
            title: 'отображаемый URL',
            value: 'url',
            type: 'text'
          }, {
            title: 'title',
            value: 'title',
            type: 'text'
          }, {
            title: 'публикация',
            value: 'published',
            type: 'published'
          }, {
            title: 'h1 вверху',
            value: 'h1',
            type: 'text'
          }, {
            title: 'meta keywords',
            value: 'keywords',
            type: 'text'
          }, {
            title: 'meta description',
            value: 'desc',
            type: 'text'
          }, {
            title: 'предметы',
            value: 'subjects',
            type: 'subjects'
          }, {
            title: 'выезд',
            value: 'place',
            type: 'place'
          }, {
            title: 'метро',
            value: 'station_id',
            type: 'station_id'
          }, {
            title: 'сортировка',
            value: 'sort',
            type: 'sort'
          }, {
            title: 'скрытый фильтр',
            value: 'hidden_filter',
            type: 'text'
          }, {
            title: 'содержание раздела',
            value: 'html',
            type: 'textarea'
          }
        ];
        $scope.getOption = function(condition) {
          return $scope.options[condition.option];
        };
        $scope.addCondition = function() {
          $scope.conditions.push({
            option: 0
          });
          return $timeout(function() {
            return $('.selectpicker').selectpicker();
          });
        };
        $scope.addCondition();
        $scope.selectControl = function(condition) {
          condition.value = null;
          switch ($scope.getOption(condition).type) {
            case 'published':
              return condition.value = 0;
            case 'subjects':
              if ($scope.subjects === void 0) {
                return FactoryService.get('subjects', 'name').then(function(response) {
                  return $scope.subjects = response.data;
                });
              }
              break;
            case 'place':
              if ($scope.places === void 0) {
                return FactoryService.get('places', 'serp').then(function(response) {
                  return $scope.places = response.data;
                });
              }
              break;
            case 'station_id':
              if ($scope.stations === void 0) {
                return FactoryService.get('stations', 'title', 'title').then(function(response) {
                  return $scope.stations = response.data;
                });
              }
              break;
            case 'sort':
              if ($scope.sort === void 0) {
                return FactoryService.get('sort').then(function(response) {
                  return $scope.sort = response.data;
                });
              }
          }
        };
        return $scope.search = function() {
          var search;
          search = {};
          $scope.conditions.forEach(function(condition) {
            return search[$scope.getOption(condition).value] = condition.value;
          });
          if (search.hasOwnProperty('html')) {
            search.html = search.html.substr(0, 200);
          }
          $.cookie('search', JSON.stringify(search), {
            expires: 365,
            path: '/'
          });
          ajaxStart();
          $scope.searching = true;
          return window.location = 'search';
        };
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').directive('ngSelectNew', function() {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        object: '=',
        model: '=',
        noneText: '@',
        label: '@',
        field: '@'
      },
      templateUrl: 'directives/select-new',
      controller: function($scope, $element, $attrs, $timeout) {
        var value;
        if (!$scope.noneText) {
          value = _.first(Object.keys($scope.object));
          if ($scope.field) {
            value = $scope.object[value][$scope.field];
          }
          if (!$scope.model) {
            $scope.model = value;
          }
        }
        $timeout(function() {
          return $element.selectpicker({
            noneSelectedText: $scope.noneText
          });
        }, 100);
        return $scope.$watchGroup(['model', 'object'], function(newVal) {
          if (newVal) {
            return $timeout(function() {
              return $element.selectpicker('refresh');
            });
          }
        });
      }
    };
  });

}).call(this);

(function() {
  angular.module('Egecms').directive('ngSelect', function() {
    return {
      restrict: 'E',
      replace: true,
      scope: {
        object: '=',
        model: '=',
        noneText: '@',
        label: '@',
        field: '@'
      },
      templateUrl: 'directives/ngselect',
      controller: function($scope, $element, $attrs, $timeout) {
        if (!$scope.noneText) {
          if ($scope.field) {
            $scope.model = $scope.object[_.first(Object.keys($scope.object))][$scope.field];
          } else {
            $scope.model = _.first(Object.keys($scope.object));
          }
        }
        return $timeout(function() {
          return $($element).selectpicker();
        }, 150);
      }
    };
  });

}).call(this);

(function() {


}).call(this);

(function() {


}).call(this);

(function() {


}).call(this);

(function() {


}).call(this);

(function() {
  angular.module('Egecms').service('AceService', function() {
    this.editors = {};
    this.initEditor = function(FormService, minLines, id, mode) {
      if (minLines == null) {
        minLines = 30;
      }
      if (id == null) {
        id = 'editor';
      }
      if (mode == null) {
        mode = 'ace/mode/html';
      }
      this.editor = ace.edit(id);
      this.editor.getSession().setMode(mode);
      this.editor.getSession().setUseWrapMode(true);
      this.editor.setOptions({
        minLines: minLines,
        maxLines: 2e308
      });
      this.editor.commands.addCommand({
        name: 'save',
        bindKey: {
          win: 'Ctrl-S',
          mac: 'Command-S'
        },
        exec: function(editor) {
          return FormService.edit();
        }
      });
      return this.editors[id] = this.editor;
    };
    this.getEditor = function(id) {
      if (id == null) {
        id = 'editor';
      }
      return this.editors[id];
    };
    this.show = function(id) {
      if (id == null) {
        id = 'editor';
      }
      this.shown_editor = id;
      return localStorage.setItem('shown_editor', id);
    };
    this.isShown = function(id) {
      if (id == null) {
        id = 'editor';
      }
      if (!localStorage.getItem('shown_editor')) {
        this.show('editor');
      }
      return id === localStorage.getItem('shown_editor');
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egecms').service('IndexService', function($rootScope) {
    this.filter = function() {
      $.cookie(this.controller, JSON.stringify(this.search), {
        expires: 365,
        path: '/'
      });
      this.current_page = 1;
      return this.pageChanged();
    };
    this.max_size = 10;
    this.init = function(Resource, current_page, attrs, load_page) {
      if (load_page == null) {
        load_page = true;
      }
      $rootScope.frontend_loading = true;
      this.Resource = Resource;
      this.current_page = parseInt(current_page);
      this.controller = attrs.ngController.toLowerCase().slice(0, -5);
      this.search = $.cookie(this.controller) ? JSON.parse($.cookie(this.controller)) : {};
      if (load_page) {
        return this.loadPage();
      }
    };
    this.loadPage = function() {
      var params;
      params = {
        page: this.current_page
      };
      if (this.sort !== void 0) {
        params.sort = this.sort;
      }
      return this.Resource.get(params, (function(_this) {
        return function(response) {
          _this.page = response;
          return $rootScope.frontend_loading = false;
        };
      })(this));
    };
    this.pageChanged = function() {
      $rootScope.frontend_loading = true;
      this.loadPage();
      return this.changeUrl();
    };
    this.changeUrl = function() {
      return window.history.pushState('', '', this.controller + '?page=' + this.current_page);
    };
    return this;
  }).service('FormService', function($rootScope, $q, $timeout) {
    var beforeSave, modelLoaded, modelName;
    this.init = function(Resource, id, model) {
      this.dataLoaded = $q.defer();
      $rootScope.frontend_loading = true;
      this.Resource = Resource;
      this.saving = false;
      if (id) {
        return this.model = Resource.get({
          id: id
        }, (function(_this) {
          return function() {
            return modelLoaded();
          };
        })(this));
      } else {
        this.model = new Resource(model);
        return modelLoaded();
      }
    };
    modelLoaded = (function(_this) {
      return function() {
        $rootScope.frontend_loading = false;
        return $timeout(function() {
          _this.dataLoaded.resolve(true);
          return $('.selectpicker').selectpicker('refresh');
        });
      };
    })(this);
    beforeSave = (function(_this) {
      return function() {
        if (_this.error_element === void 0) {
          ajaxStart();
          if (_this.beforeSave !== void 0) {
            _this.beforeSave();
          }
          _this.saving = true;
          return true;
        } else {
          $(_this.error_element).focus();
          return false;
        }
      };
    })(this);
    modelName = function() {
      var l, model_name;
      l = window.location.pathname.split('/');
      model_name = l[l.length - 2];
      if ($.isNumeric(model_name)) {
        model_name = l[l.length - 3];
      }
      return model_name;
    };
    this["delete"] = function(event, callback) {
      if (callback == null) {
        callback = false;
      }
      return bootbox.confirm("Вы уверены, что хотите " + ($(event.target).text()) + " #" + this.model.id + "?", (function(_this) {
        return function(result) {
          if (result === true) {
            beforeSave();
            return _this.model.$delete().then(function() {
              if (callback) {
                callback();
                _this.saving = false;
                return ajaxEnd();
              } else {
                return redirect(modelName());
              }
            }, function(response) {
              return notifyError(response.data.message);
            });
          }
        };
      })(this));
    };
    this.edit = function() {
      if (!beforeSave()) {
        return;
      }
      return this.model.$update().then((function(_this) {
        return function() {
          _this.saving = false;
          return ajaxEnd();
        };
      })(this), function(response) {
        notifyError(response.data.message);
        this.saving = false;
        return ajaxEnd();
      });
    };
    this.create = function() {
      if (!beforeSave()) {
        return;
      }
      return this.model.$save().then((function(_this) {
        return function(response) {
          return redirect(modelName() + ("/" + response.id + "/edit"));
        };
      })(this), (function(_this) {
        return function(response) {
          notifyError(response.data.message);
          _this.saving = false;
          ajaxEnd();
          return _this.onCreateError(response);
        };
      })(this));
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egecms').service('ExportService', function($rootScope, FileUploader) {
    bindArguments(this, arguments);
    this.init = function(options) {
      var onWhenAddingFileFailed;
      this.controller = options.controller;
      this.FileUploader.FileSelect.prototype.isEmptyAfterSelection = function() {
        return true;
      };
      return this.uploader = new this.FileUploader({
        url: this.controller + "/import",
        alias: 'imported_file',
        autoUpload: true,
        method: 'post',
        removeAfterUpload: true,
        onCompleteItem: function(i, response, status) {
          if (status === 200) {
            notifySuccess('Импортировано');
          }
          if (status !== 200) {
            return notifyError(response.message);
          }
        }
      }, onWhenAddingFileFailed = function(item, filter, options) {
        if (filter.name === "queueLimit") {
          this.clearQueue();
          return this.addToQueue(item);
        }
      });
    };
    this["import"] = function(e) {
      e.preventDefault();
      $('#import-button').trigger('click');
    };
    this.exportDialog = function() {
      $('#export-modal').modal('show');
      return false;
    };
    this["export"] = function() {
      window.location = "/" + this.controller + "/export?field=" + this.export_field;
      $('#export-modal').modal('hide');
      return false;
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egecms').service('FactoryService', function($http) {
    this.get = function(table, select, orderBy) {
      if (select == null) {
        select = null;
      }
      if (orderBy == null) {
        orderBy = null;
      }
      return $http.post('api/factory', {
        table: table,
        select: select,
        orderBy: orderBy
      });
    };
    return this;
  });

}).call(this);

(function() {
  angular.module('Egecms').service('PhotoService', function($http, Photo, FileUploader) {
    setInterval((function(_this) {
      return function() {
        return console.log(_this.groups && _this.groups.length);
      };
    })(this), 2000);
    this.init = function(groups) {
      return this.groups = groups;
    };
    this.Uploader = new FileUploader({
      url: 'api/photos/upload',
      alias: 'file',
      filters: [
        {
          name: 'imageFilter',
          fn: function(file, options) {
            var type;
            type = "|" + (file.type.slice(file.type.lastIndexOf('/') + 1)) + "|";
            return '|jpg|png|jpeg|'.indexOf(type) !== -1;
          }
        }
      ],
      autoUpload: true,
      removeAfterUpload: true
    });
    this.Uploader.onSuccessItem = (function(_this) {
      return function(item, response) {
        var group, photo;
        if (_this.editing_model) {
          group = _.find(_this.groups, {
            id: response.group_id
          });
          photo = _.find(_this.photo, {
            id: response.id
          });
          _.extend(photo, response);
        } else {
          group = _.find(_this.groups, {
            id: response.group_id
          });
          group.photo.push(response);
        }
        _this.editing_model = null;
        if (typeof _this.onSuccessItemCallback === 'function') {
          return _this.onSuccessItemCallback();
        }
      };
    })(this);
    this.Uploader.onBeforeUploadItem = (function(_this) {
      return function(item) {
        var ref;
        return item.formData.push({
          old_file: (ref = _this.editing_model) != null ? ref.filename : void 0
        });
      };
    })(this);
    this["delete"] = function(model) {
      return Photo["delete"]({
        id: model.id
      });
    };
    this.filesize = function(size) {
      var unit, units;
      units = ['B', 'Kb', 'Mb', 'Gb'];
      unit = 0;
      while (size > 1024) {
        size = size / 1024;
        unit++;
      }
      return size.toFixed(1) + units[unit];
    };
    return this;
  });

}).call(this);

//# sourceMappingURL=app.js.map
