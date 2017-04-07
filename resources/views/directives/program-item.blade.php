<h2 ng-if="level > 1" ng-mouseover="show_controls = true" ng-mouseleave="show_controls = false">
    @{{ levelstring }} <span class="item-title" editable="item">@{{ item.title }}</span>
    <span class="show-on-hover">
        <span ng-click="addChild()" class="link-like">добавить</span>
        <span ng-click="delete()" class="link-like text-danger">удалить</span>
    </span>
</h2>
<ul>
    <li ng-repeat="child in item.content">
        <program-item item="child" level="level ? level + 1 : 0" levelstring="getChildLevelString($index)" delete="deleteChild(child)"></program-item>
    </li>
    <li>
        <span ng-show="level == 1 && !is_adding" ng-click="addChild()" class="link-like">добавить</span>
        <input class="add-item" placeholder="подпункт программы"
               ng-model="new_item.title"
               ng-show="is_adding"
               ng-keyup="createChild($event)"
               ng-blur="blur()"
        >
    </li>
</ul>

