function openModalAtenderSolicitud(btn) {
    var codigoSolicitud = btn.data('codigo_solicitud');

    if(codigoSolicitud == null || codigoSolicitud == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'getDataAtenderSolicitudOcDiseno',
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
            var cesta     = $(this).find('td:eq(4) input').val();
            var oc        = $(this).find('td:eq(5) input').val();
            var posicion  = $(this).find('td:eq(6) input').val();
            var costo_sap = $(this).find('td:eq(7) input').val();

            jsonSolicitud.codigoSolicitud = codigoSolicitud;
            jsonSolicitud.itemplan        = itemplan;
            jsonSolicitud.cesta           = cesta;
            jsonSolicitud.orden_compra    = oc;
            jsonSolicitud.posicion        = posicion;
            jsonSolicitud.costo_sap       = costo_sap;

            arraySolicitud.push(jsonSolicitud);
            jsonSolicitud = {};
        }

    }); 


    $.ajax({
        type : 'POST',
        url  : 'atenderSolicitudCreaOcDiseno',
        data : { arraySolicitud : arraySolicitud }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            modal('mdlAtenderSolicitud');
            mostrarNotificacion(2, 'success', data.msj, 'Correcto');
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}

function validarEdicionOc(component){
    var jsonData = $(component).data();
    var formData = new FormData();
    formData.append('codigo_solicitud', jsonData.codigo_solicitud);

    Swal.queue([
    {
        title: "Está seguro de validar la solicitud de Edición de OC??",
        // text: "Asegurese de validar la información!!",
        html : '<div class="form-group">'+
                    '<a>COSTO SAP:</a>'+
                '</div>'+
                '<div class="form-group" align="center">'+
                    '<input type="text" class="form-control col-md-4" placeholder="Ingresar costo sap.." style="background:#F9F8CF" id="costoSap"/>'+
                '</div>',
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return validarEdicionOcPromise(formData,component).then(function (data) { 
                return swal.fire('Exitoso!',data.msj,'success');
            }).catch(function(e) {
                return Swal.insertQueueStep(
                {
                    icon: "error",
                    title: e.msj
                });
            });
        }
    }]);
}

function validarEdicionOcPromise(formData,component){
    var costoSap = $('#costoSap').val();
    if(costoSap == null || costoSap == '' || costoSap == undefined){
        swal.fire('Aviso!','Debe ingresar un costo!!','warning');
        return;
    }
    formData.append('costoSap',costoSap);
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  : 'POST',
            url   : 'valSolEdiOCDiseno',
            data  : formData,
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: () => {
                $(component).attr("disabled", true);
            }
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('.searchT').click();
                resolve(data);
            }else{
                reject(data);
            }
        }).always(() => {
            $(this).removeAttr("disabled");
        });
    });
}

function actualizarToPdt(component){
    var jsonData = $(component).data();
    Swal.queue([
    {
        title:'Está seguro de pasar a pendiente'+'<br>'+'la solicitud : '+ jsonData.codigo_solicitud +'??',
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return actualizarToPdtPromise(jsonData.codigo_solicitud).then(function (data) { 
                return swal.fire('Exitoso!',data.msj,'success');
            }).catch(function(e) {
                return Swal.insertQueueStep({
                    icon: "error",
                    title: e.msj
                });
            });
        }
    }]);
}

function actualizarToPdtPromise(codigo_solicitud){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type : 'POST',
            url  : 'updateSolCertiToPdtDiseno',
            data : { 
                codigoSolicitud : codigo_solicitud
            }
        }).done(function(data) {
    
            data = JSON.parse(data);
            if(data.error == 0) {
                //$('#contTablaSolicitudOc').html(data.tablaSolicitudOc);
                //initDataTable('tbSolicitudOc',3);
                $('.searchT').click();
               // modal('modalEditPO');
                resolve(data);
            } else {
                reject(data);
            }
        });
    });
}

var codigoSolicitudCerti = null;
function openModalAtenderSolicitudCerti(btn) {
    codigoSolicitudCerti = btn.data('codigo_solicitud');
    modal('mdlAtenderSolicitudCerti');
}


function atenderSolicitudCertiOc() {
    var codigoCertificacion = $('#codCertificacion').val();
    
    if(codigoSolicitudCerti == null || codigoSolicitudCerti == '') {
        return;
    }

    if(codigoCertificacion == null || codigoCertificacion == '') {
        mostrarNotificacion(1, 'error', 'Ingresar Código Certificación', 'Verificar');
        return;
    }
    
    $.ajax({
        type : 'POST',
        url  : 'atenderSolicitudCertiOcDiseno',
        data : { codigoSolicitud     : codigoSolicitudCerti,
                 codigoCertificacion : codigoCertificacion }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            mostrarNotificacion(2, 'success', data.msj, 'Correcto');
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}