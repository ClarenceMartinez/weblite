function registrarItemplan() {
    var longitud        = $('#txtLongitud').val();
    var latitud         = $('#txtLatitud').val();
    var nombrePlan      = $('#txtNombrePlan').val();
    var idSubProyecto   = $('#cmbSubProyecto option:selected').val();
    var idProyecto      = $('#cmbProyecto option:selected').val();
    var idFase          = $('#cmbFase option:selected').val();
    var fechaRecepcion  = $('#fecha_recepcion').val();
    var cant_uip        = $('#txtUip').val();
    var departamento    = $('#txtDepa').val();
    var provincia       = $('#txtProv').val();
    var distrito        = $('#txtDis').val();
    var nomCliente      = $('#txtCliente').val();

    var fecha1=fechaRecepcion;
    var nueva=fecha1.split(" ")[0].split("-").reverse().join("-");
    var fechaRecepcion =nueva+' 00:00:00';


    var codigoInversion = $('#cmbInversion option:selected').val();

    if(longitud == null || longitud == '' || latitud == null || latitud == '' || idFase == null || idFase == '') {
        return;
    }

    if(nombrePlan == null || nombrePlan == '') {
        return;
    }

    if(idSubProyecto == null || idSubProyecto == '') {
        return;
    }

    if(codigoInversion == null || codigoInversion == '') {
        return;
    }

    objetoDataRegistro.longitud           = longitud;
    objetoDataRegistro.latitud            = latitud;
    objetoDataRegistro.nombrePlan         = nombrePlan;
    objetoDataRegistro.idProyecto         = idProyecto;
    objetoDataRegistro.idSubProyecto      = idSubProyecto;
    objetoDataRegistro.idFase             = idFase;
    objetoDataRegistro.fechaRecepcion     = fechaRecepcion;
    objetoDataRegistro.codigoInversion    = codigoInversion;
    objetoDataRegistro.cantidad_uip       = cant_uip;
    objetoDataRegistro.cliente            = nomCliente;

    $.ajax({
        type : 'POST',
        url  : 'registrarItemplanMadre',
        data : { objJson : JSON.stringify(objetoDataRegistro) }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            mostrarNotificacion(2, 'success', 'SE CREO EL ITEMPLAN: ', data.itemplan_m);
            // if(alert('Se registró el itemplan : '+data.itemplan)){}
            // else window.location.reload(); 
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}