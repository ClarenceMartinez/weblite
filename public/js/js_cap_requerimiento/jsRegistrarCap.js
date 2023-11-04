function getMotivoResponsableCap() {
    var idAreaReq = $('#cmbTipoAreaReq option:selected').val();

    $.ajax({
        type : 'POST',
        url  : 'getMotivoResponsableCap',
        data : { idAreaReq : idAreaReq }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            $('#cmbMotivo').html(data.cmbMotivo);
        }
    });
}

var idUsuarioGlobal = null;
function getResponsableCap() {
    var idMotivo = $('#cmbMotivo option:selected').val();

    $.ajax({
        type : 'POST',
        url  : 'getResponsableCap',
        data : { idMotivo : idMotivo }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            console.log(data.objUsuario);

            $('#txtResponsable').val(data.objUsuario.nombre_completo);
            idUsuarioGlobal = data.objUsuario.idUsuario;
        }
    });
}
 
function registrarRequerimiento() {
    var objetoDataRegistro = {};

    var idTipoReq      = $('#cmbTipoReq option:selected').val();
    var idTipoProyecto = $('#cmbTipoProy option:selected').val();
    var idAreaReq      = $('#cmbTipoAreaReq option:selected').val();
    var idMotivo       = $('#cmbMotivo option:selected').val();
    var descReq        = $('#txtDescReq').val();

    if(idTipoReq == null || idTipoReq == '' || idTipoProyecto  == null || idTipoProyecto  == '' || idAreaReq == null || idAreaReq == '') {
        mostrarNotificacion(1, 'error', 'Llenar todos los campos.', 'Incorrecto');
        return;
    }

    if(idMotivo == null || idMotivo == '' || descReq  == null || descReq  == '' || idUsuarioGlobal == '' || idUsuarioGlobal == null) {
        mostrarNotificacion(1, 'error', 'Llenar todos los campos.', 'Incorrecto');
        return;
    }
    
    objetoDataRegistro.id_tipo_requerimiento   = idTipoReq;
    objetoDataRegistro.id_tipo_proyecto        = idTipoProyecto;
    objetoDataRegistro.id_area_requerimiento   = idAreaReq;
    objetoDataRegistro.id_motivo_requerimiento = idMotivo;
    objetoDataRegistro.comentario_registro     = descReq;
    objetoDataRegistro.idUsuarioResponsable    = idUsuarioGlobal;


    $.ajax({
        type : 'POST',
        url  : 'registrarRequerimiento',
        data : { objJson : JSON.stringify(objetoDataRegistro) }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            mostrarNotificacion(2, 'success', 'GENERÓ EL TICKET NRO: ', data.codigoReq);
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}