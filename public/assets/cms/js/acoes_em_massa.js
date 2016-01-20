$(function(){

    $(document).on("change", "#acoes-em-massa", function(){

        var _this   = $(this);
        var action  = _this.val();

        if( action == "" || action == undefined ) return false;

        var marked = $("input[name=marked]:checked");

        if( !marked.length ) {
            bootbox.alert("Marque um ou mais registros para realizar ação.");
            _this.val("");
            return false;
        }

        var plural = marked.length > 1 ? "s" : "";

        bootbox.dialog({
            title: "Atenção!",
            message: "Tem certeza que deseja " + action + " " + marked.length + " registro" + plural + "?",
            buttons: {
                sim: {
                    label: "Sim",
                    className: "btn-panda",
                    callback: function() {
                        bootbox.dialog({
                            title: "Aguarde...",
                            message: '<div class="progress"><div class="progress-bar" role="progressbar" id="progress-acao-em-massa" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div>'
                        });

                        var ajaxUrl = false;
                        if( action == "excluir" ) ajaxUrl = action_trash;
                        if( action == "desativar" ) ajaxUrl = action_inactive;
                        if( action == "ativar" ) ajaxUrl = action_active;
                        if( action == "restaurar" ) ajaxUrl = action_restore;
                        if( action == "excluir definitivamente" ) ajaxUrl = action_delete;

                        if( !ajaxUrl ) {
                            console.error("URL ajax não definida!");
                            bootbox.hideAll();
                            return false;
                        }

                        var percent     = 0;
                        var percent_add = (marked.length / 100) * 1000;

                        marked.each(function(){
                            var actionUrl = ajaxUrl.replace("THE_ID", $(this).val());
                            $.ajax({
                                url: actionUrl,
                                type: "get",
                                async: true,
                                success: function(){
                                    percent = percent + percent_add;
                                    $("#progress-acao-em-massa").attr("aria-valuenow", percent).width(percent + "%");
                                }
                            });
                        });
                        $("#progress-acao-em-massa").attr("aria-valuenow", 100).width("100%");
                        bootbox.hideAll();
                        location.reload();
                    }
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn-default",
                    callback: function() {
                        _this.val("");
                    }
                }
            }
        });

    });

});