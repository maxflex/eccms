angular.module('Egecms')
    .factory 'Variable', ($resource) ->
        $resource apiPath('variables'), {id: '@id'}, updatable()

    .factory 'VariableGroup', ($resource) ->
        $resource apiPath('variables/groups'), {id: '@id'}, updatable()

    .factory 'Sass', ($resource) ->
        $resource apiPath('sass'), {id: '@id'}, updatable()

    .factory 'Page', ($resource) ->
        $resource apiPath('pages'), {id: '@id'},
            update:
                method: 'PUT'
            checkExistance:
                method: 'POST'
                url: apiPath('pages', 'checkExistance')

    .factory 'Program', ($resource) ->
        $resource apiPath('programs'), {id: '@id'}, updatable()

    .factory 'Photo', ($resource) ->
        $resource apiPath('photos'), {id: '@id'}, updatable()

apiPath = (entity, additional = '') ->
    "api/#{entity}/" + (if additional then additional + '/' else '') + ":id"


updatable = ->
    update:
        method: 'PUT'
countable = ->
    count:
        method: 'GET'
