function openModalRequerimiento(elem) {
    let id = $(elem).data('id');
    $.ajax({
        type: 'POST',
        url: 'getFormRespuesta',
        data: { codigoRequerimiento: id }
    }).done(function (data) {
        data = JSON.parse(data);
        $('#respuestaRequerimiento').html(data.htmlFormRespuesta);
        modal('mdlRequerimientoRes');

    });

}

function ingresarRespuesta() {
    let codigoRequerimiento = $('#txtCodigoRequerimiento').val();
    let horasEsfuerzo = $('#txtHoraEsfuerzo').val();
    let cmbEstadoReq = $('#cmbEstadoReq').val();
    let comentarioValida = $('#txtComentarioValida').val();

    if (horasEsfuerzo == '' || cmbEstadoReq == 'Seleccione') {
        console.log('Error');
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'actualizarRequerimiento',
        data: {
            codigoRequerimiento: codigoRequerimiento,
            horasEsfuerzo: horasEsfuerzo,
            cmbEstadoReq: cmbEstadoReq,
            comentarioValida: comentarioValida
        }
    }).done(function (data) {
        data = JSON.parse(data);

        if (data.error == 0) {
            mostrarNotificacion(2, 'success', data.msj, 'Éxitoso');
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}