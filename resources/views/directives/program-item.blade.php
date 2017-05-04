<div ng-if="level == 1" class="mbb">Общее количество часов: @{{ childLessonSum(item) }} ч.</div>

<h2 ng-if="level > 1" ng-mouseover="show_controls = true" ng-mouseleave="show_controls = false">
    <span class="levelstring">@{{ levelstring }}</span>
    <span>
        <span editable="title"
              jump-on-tab="editing-lesson-count"
              ng-click="editFocus()"
              ng-blur="editBlur()"
              ng-focus="editFocus()"
              ng-bind-html=" item.title || 'не указано' "
        ></span>
        <span class="no-wrap">
            <input class="editing-lesson-count"
                   ng-model="item.lesson_count"
                   data-input-digits-only="true"
                   ng-show="!item.content.length && (editing || item.lesson_count > 0)"
                   ng-focus="editFocus()"
                   placeholder="количество часов"
                   ng-blur="editBlur()"
                   ng-attr-size="@{{ item.lesson_count > 0 ? item.lesson_count.length : 'auto' }}"
                   maxlength="4"
                   ng-keydown="editKeydown($event)"
            >
            <plural class="nearer" ng-show="+(item.lesson_count) > 0" type="hour" count="item.lesson_count" text-only hide-zero></plural>
        </span>
    </span>

    <span ng-click="addChild($event)" class="link-like show-on-hover">добавить</span>
    <span ng-click="delete()" class="link-like text-danger show-on-hover">удалить</span>
</h2>
<ul>
    <li ng-repeat="child in item.content">
        <program-item item="child" level="level ? level + 1 : 0" levelstring="getChildLevelString($index)" delete="deleteChild(child)"></program-item>
    </li>
    <li>
        <span ng-show="level == 1 && !is_adding" ng-click="addChild($event)" class="link-like add-child">добавить</span>
        <input placeholder="подпункт программы" class="add-item-title"
               ng-model="new_item.title"
               ng-show="is_adding"
               ng-keydown="createChild($event)"
               ng-blur="blur($event);"
               ng-attr-size="@{{ new_item.title.length > 20 ? new_item.title.length + 5 : 25 }}"
               ng-focus="focus()"
        >
        <input placeholder="количество часов" class="add-item-lesson-count"
               digits-only
               maxlength="5"
               ng-model="new_item.lesson_count"
               ng-show="is_adding && !child.length"
               ng-keydown="createChild($event)"
               ng-blur="blur($event)"
               ng-focus="focus()"
        >
        <span class="new-item-hour" ng-show="is_adding && !child.length" ng-class="{'invisible': !new_item.lesson_count}"><plural type="hour" count="new_item.lesson_count" text-only hide-zero></plural>
    </li>
</ul>
