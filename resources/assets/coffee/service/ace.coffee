angular.module 'Egecms'
    .service 'AceService', ->
        this.editors = {}
        this.initEditor = (FormService, minLines = 30, id = 'editor', mode = 'ace/mode/html') ->
            this.editor = ace.edit(id)
            this.editor.getSession().setMode(mode)
            this.editor.getSession().setUseWrapMode(true)
            this.editor.setOptions
                minLines: minLines
                maxLines: Infinity
            this.editor.commands.addCommand
                name: 'save',
                bindKey:
                    win: 'Ctrl-S'
                    mac: 'Command-S'
                exec: (editor) ->
                    FormService.edit()
            this.editors[id] = this.editor

        this.getEditor = (id = 'editor') ->
            this.editors[id]

        this.show = (id = 'editor') ->
            this.shown_editor = id
            localStorage.setItem 'shown_editor', id

        this.isShown = (id = 'editor') ->
            this.show 'editor' if not localStorage.getItem 'shown_editor'
            id is localStorage.getItem 'shown_editor'

        this
