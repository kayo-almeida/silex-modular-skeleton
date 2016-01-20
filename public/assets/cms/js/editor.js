$(document).ready(function(){

    // CONFIGURAÇÕES DO CKEDITOR
    CKEDITOR.editorConfig = function(config) {
        config.language = 'pt-br';
    };

    // INSTANCIAÇÃO DO CKEDITOR
    $('.editor').each( function () {
        console.log($(this).attr("id"));
        CKEDITOR.replace(this.id, {});
    });

});