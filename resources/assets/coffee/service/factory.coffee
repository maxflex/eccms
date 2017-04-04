angular.module 'Egecms'
    .service 'FactoryService', ($http) ->
        this.get = (table, select = null, orderBy = null)->
            $http.post 'api/factory',
                table: table
                select: select
                orderBy: orderBy

        this
