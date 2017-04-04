angular.module 'Egecms'
    .service 'AceService', ->
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
        this
