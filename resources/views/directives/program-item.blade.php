<div ng-if="level == 1" class="mbb">Общее количество часов: @{{ childLessonSum(item) }} ч.</div>

<h2 ng-if="level > 1" ng-mouseover="show_controls = true" ng-mouseleave="show_controls = false">
    @{{ levelstring }}
    <span class="item-title" data-field="title" editable="item" ng-click="showLesson()" jump-on-tab="lesson_count">@{{ item.title }}</span>
    <span ng-show="!item.content.length">
        <span editable="item"
              data-field="lesson_count"
              data-not-required="true"
              data-input-digits-only="true"
              class="lesson_count"
              ng-class="{'is-zero': !item.lesson_count}"
              ng-show="isShownLesson() || item.lesson_count > 0"
              ng-bind="item.lesson_count > 0 ? item.lesson_count : ''">@{{ item.lesson_count }}</span>
    </span>

    <span class="show-on-hover">
        <span ng-click="addChild($event)" class="link-like">добавить</span>
        <span ng-click="delete()" class="link-like text-danger">удалить</span>
    </span>
</h2>
<ul>
    <li ng-repeat="child in item.content">
        <program-item item="child" level="level ? level + 1 : 0" levelstring="getChildLevelString($index)" delete="deleteChild(child)"></program-item>
    </li>
    <li>
        <span ng-show="level == 1 && !is_adding" ng-click="addChild($event)" class="link-like add-child">добавить</span>
        <input placeholder="подпункт программы"
               ng-class="'new-' + item.id + ' add-item title'"
               ng-model="new_item.title"
               ng-show="is_adding"
               ng-keydown="createChild($event)"
               ng-blur="blur($event)"
        >
        <span class="pull-right" ng-show="is_adding && !child.length">
            <input placeholder="количество часов" ng-class="'new-' + item.id + ' add-item lesson-count'"
                   digits-only
                   maxlength="5"
                   ng-model="new_item.lesson_count"
                   ng-keydown="createChild($event)"
                   ng-blur="blur($event)"
            >
            <span class="new-item-hour" ng-class="{'invisible': !new_item.lesson_count}"><plural type="hour" count="new_item.lesson_count" text-only hide-zero></plural></span>
        </span>
    </li>
</ul>