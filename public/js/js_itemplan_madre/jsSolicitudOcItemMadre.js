function filtrarSolicitudOC() {
    var itemplan = $('#txtItemplan').val();
	itemplan = itemplan.replace(/_/g, '');
	
    $.ajax({
        type : 'POST',
        url  : 'filtrarSolicitudOCItemMadre',
        data : { itemplan : itemplan },
		beforeSend: () => {
            $('#divLoading').css('display', 'block');
        }
    }).done(function(data){
        data = JSON.parse(data);
        $('#contTablaSolicitudOc').html(data.tablaSolicitudOc);
        initDataTable('tbSolicitudOc',3);
    }).always(() => {
        $('#divLoading').css('display', 'none');
    });
}

function openModalDetalleSolicitudOc(btn) {
    var codigoSolicitud = btn.data('codigo_solicitud');

    if(codigoSolicitud == null || codigoSolicitud == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'getDetalleSolicitudOcItemMadre',
        data : { codigoSolicitud : codigoSolicitud }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            $('#contTablDetalleSolicitudOc').html(data.tablaDetalleSolicitudOc);
            // initDataTable('tbDetalleSolicitudOc', 2);
            
            modal('mdlDetalleSolicitud');
        } else {
            return;
        }
    });
}

function openModalAtenderSolicitud(btn) {
    var codigoSolicitud = btn.data('codigo_solicitud');

    if(codigoSolicitud == null || codigoSolicitud == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'getDataAtenderSolicitudOcItemMadre',
        data : { codigoSolicitud : codigoSolicitud }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            $('#contTablAtenderSolicitudOc').html(data.tablaDetalleSolicitudOc);
            // initDataTable('tbDetalleSolicitudOc', 2);
            
            modal('mdlAtenderSolicitud');
        } else {
            return;
        }
    });
}

function atenderSolicitudCreaOc() {
    var jsonSolicitud = {};
    var arraySolicitud = [];
    $("#tbAtenderSolicitudOc tr").each(function(i) {
        if(i > 0) {
            var codigoSolicitud = $(this).find('td:eq(0)').text();
            var itemplan        = $(this).find('td:eq(1)').text();
            var cesta           = $(this).find('td:eq(3) input').val();
            var oc              = $(this).find('td:eq(4) input').val();
            var posicion        = $(this).find('td:eq(5) input').val();
            var costo_sap       = $(this).find('td:eq(6) input').val();

            jsonSolicitud.codigoSolicitud = codigoSolicitud;
            jsonSolicitud.itemplan_m      = itemplan;
            jsonSolicitud.cesta           = cesta;
            jsonSolicitud.orden_compra    = oc;
            jsonSolicitud.posicion        = posicion;
            jsonSolicitud.costo_sap       = costo_sap;

            arraySolicitud.push(jsonSolicitud);
            jsonSolicitud = {};
        }

    }); 

        console.log(arraySolicitud);
    $.ajax({
        type : 'POST',
        url  : 'atenderSolicitudCreaOcItemMadre',
        data : { arraySolicitud : arraySolicitud }
    }).done(function(data){
        data = JSON.parse(data);
        console.log(data);
        if(data.error == 0) {
            modal('mdlAtenderSolicitud');
            mostrarNotificacion(2, 'success', data.msj, 'Correcto');
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}