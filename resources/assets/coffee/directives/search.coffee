angular.module 'Egecms'
    .directive 'search', ->
        restrict: 'E'
        templateUrl: 'directives/search'
        scope: {}
        link: ->
            $('.search-icon').on 'click', -> $('#search-app').modal('show')
        controller: ($scope, $timeout, $http, Published, FactoryService) ->
            bindArguments($scope, arguments)
            $scope.conditions = []
            $scope.options = [
                {title: 'ключевая фраза', value: 'keyphrase', type: 'text'},
                {title: 'отображаемый URL', value: 'url', type: 'text'},
                {title: 'title', value: 'title', type: 'text'},
                {title: 'публикация', value: 'published', type: 'published'},
                {title: 'сео (стационар)', value: 'seo_desktop', type: 'seo_desktop'},
                {title: 'сео (мобильная)', value: 'seo_mobile', type: 'seo_mobile'},
                {title: 'h1 вверху', value: 'h1', type: 'text'},
                {title: 'h1 внизу', value: 'h1_bottom', type: 'text'},
                {title: 'meta keywords', value: 'keywords', type: 'text'},
                {title: 'meta description', value: 'desc', type: 'text'},
                {title: 'предметы', value: 'subjects', type: 'subjects'},
                {title: 'выезд', value: 'place', type: 'place'},
                {title: 'метро', value: 'station_id', type: 'station_id'},
                {title: 'сортировка', value: 'sort', type: 'sort'},
                {title: 'скрытый фильтр', value: 'hidden_filter', type: 'text'},
                {title: 'содержание раздела', value: 'html', type: 'textarea'}
            ]

            $scope.getOption = (condition) -> $scope.options[condition.option]

            $scope.addCondition = ->
                $scope.conditions.push({option: 0})
                $timeout -> $('.selectpicker').selectpicker()

            $scope.addCondition()

            $scope.selectControl = (condition) ->
                condition.value = null
                switch $scope.getOption(condition).type
                    when 'published', 'seo_desktop', 'seo_mobile' then condition.value = 0
                    when 'subjects'
                        if $scope.subjects is undefined then FactoryService.get('subjects', 'name').then (response) ->
                            $scope.subjects = response.data
                    when 'place'
                        if $scope.places is undefined then FactoryService.get('places', 'serp').then (response) ->
                            $scope.places = response.data
                    when 'station_id'
                        if $scope.stations is undefined then FactoryService.get('stations', 'title', 'title').then (response) ->
                            $scope.stations = response.data
                    when 'sort'
                        if $scope.sort is undefined then FactoryService.get('sort').then (response) ->
                            $scope.sort = response.data
                # $('.search-value-control').selectpicker('refresh')

            $scope.search = ->
                search = {}
                $scope.conditions.forEach (condition) ->
                    search[$scope.getOption(condition).value] = condition.value
                # long input bug fix – cookie can only store N bytes
                search.html = search.html.substr(0, 200) if search.hasOwnProperty('html')

                $.cookie('search', JSON.stringify(search), { expires: 365, path: '/' })
                ajaxStart()
                $scope.searching = true
                window.location = 'search'
