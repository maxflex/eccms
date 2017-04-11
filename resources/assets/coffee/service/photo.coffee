angular.module 'Egecms'
    .service 'PhotoService', ($http, Photo, FileUploader, FormService, PhotosUploadDir) ->
        this.getUrl = (model) ->
            return '' if !model || !model.filename
            PhotosUploadDir + model.filename

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
            FormService.model.filename = response

            if typeof this.onSuccessItemCallback is 'function'
                this.onSuccessItemCallback()

        this.Uploader.onBeforeUploadItem = (item) ->
            item.formData.push
                old_file: FormService.model.filename

        this.getImages = ->
            images = []
            IndexService.page.datas.forEach (image) ->
                images.push url: image.url
            images

        this.delete = ->
            Photo.delete
                id: scope.id

            redirect '/photos'

        this
