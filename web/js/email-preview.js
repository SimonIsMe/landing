$(document).ready(function(){
    var editor = ace.edit("email-editor");
    editor.setTheme("/js/lib/ace/theme/monokai");
    editor.getSession().setMode("ace/mode/javascript");
});