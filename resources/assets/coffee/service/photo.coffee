angular.module 'Egecms'
    .service 'PhotoService', ($http, Photo, FileUploader, IndexService) ->
        this.Uploader = new FileUploader
            url: 'api/photos/upload'
            alias: 'file'
            filters: [
                name: 'imageFilter',
                fn: (file, options) ->
                    type = "|#{file.type.slice(file.type.lastIndexOf('/') + 1)}|"
                    '|jpg|png|jpeg|'.indexOf(type) isnt -1
            ]
            autoUpload: true
            removeAfterUpload: true

        this.Uploader.onSuccessItem = (item, response) =>
            if this.editing_model
                index = _.findIndex IndexService.page.data, filename: this.editing_model.filename
                _.extend IndexService.page.data[index], response
            else
                IndexService.page.data.push response
                IndexService.page.total++

            this.editing_model = null
            if typeof this.onSuccessItemCallback is 'function'
                this.onSuccessItemCallback()

        this.Uploader.onBeforeUploadItem = (item) =>
            item.formData.push
                old_file: this.editing_model?.filename

        this.delete = (model) ->
            Photo.delete
                id: model.id

        this.filesize = (size) ->
            units = ['B', 'Kb', 'Mb', 'Gb']
            unit = 0
            while size > 1024
                size = size / 1024
                unit++
            size.toFixed(1) + units[unit]

        this
