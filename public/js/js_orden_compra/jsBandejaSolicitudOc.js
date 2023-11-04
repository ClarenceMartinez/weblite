function openModalDetalleSolicitudOc(btn) {
    var codigoSolicitud = btn.data('codigo_solicitud');

    if(codigoSolicitud == null || codigoSolicitud == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'getDataDetalleSolicitud',
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
        url  : 'getDataAtenderSolicitudOc',
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
        url  : 'atenderSolicitudCreaOc',
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
        url  : 'atenderSolicitudCertiOc',
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

function openModalFiltrar() {
    var itemplan = $('#txtItemplan').val(null);
    modal('mdlFiltrar');
}

function filtrarSolicitudOC() {
    var itemplan = $('#txtItemplan').val();
	itemplan = itemplan.replace(/_/g, '');
	
    $.ajax({
        type : 'POST',
        url  : 'filtrarSolicitudOC',
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

function openModalCargaExcelPdtCerti() {
    modal('mdlCargarExcelPdteCerti');
}

function procesarFile(){
    var comprobar = $('#archivo').val().length;
    if(comprobar == 0){
        swal.fire('Verificar!','Debe subir un archivo a procesar!!','warning');
        return;
    }
    var file = $('#archivo').val()			
    var ext = file.substring(file.lastIndexOf("."));

    if(ext != ".xls" && ext != ".xlsx"){
        swal.fire('Verificar!','Formato de archivo inválido!!','warning');
        return;
    }

    swal.fire({
        icon: 'warning',
        title: 'Está seguro de cargar el achivo??',
        text: 'Asegurese de validar la información!!',
        showConfirmButton: true,
        confirmButtonText: 'SI',
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false
    }).then((result) => {
        if (result.value) {
            var formData = new FormData();
            var files = $('#archivo')[0].files[0];
            formData.append('file', files);

            $.ajax({
                type  :	'POST',
                url   :	'cargarArchivoCertiPdt',
                data  :	formData,
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: () => {
                    $('body').loading({
                        message: 'Espere por favor...'
                    });
                    $('#btnProcesar').attr("disabled", true);
                }
            }).done(function(data){
                var data = JSON.parse(data);
                if(data.error == 0){
                    $('#btnProcesar').removeAttr("disabled");
                    mostrarNotificacion(2, 'success', 'Se actualizo el estado correctamente', 'Verificar');
                }else{
                    mostrarNotificacion(1, 'error', data.msj, 'Verificar');
                }
                
            }).fail(function (jqXHR, textStatus, errorThrown) {
                swal.fire('Aviso',errorThrown + '. Estado: ' + textStatus,'error');
                return;
            }).always(() => {
                $('body').loading('destroy');
            });
            
        }
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
			url  : 'updateSolCertiToPdt',
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
            type  :	'POST',
            url   :	'valSolEdiOC',
            data  :	formData,
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

function validarAnulacionOc(component){
    var jsonData = $(component).data();
    var formData = new FormData();
    formData.append('codigo_solicitud', jsonData.codigo_solicitud);

    Swal.queue([
    {
        title: "Está seguro de validar la solicitud de Anulacion de OC??",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return validarAnulacionOcPromise(formData,component).then(function (data) { 
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

function validarAnulacionOcPromise(formData,component){/*
    var costoSap = $('#costoSap').val();
    if(costoSap == null || costoSap == '' || costoSap == undefined){
        swal.fire('Aviso!','Debe ingresar un costo!!','warning');
        return;
    }
    formData.append('costoSap',costoSap);*/
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'valSolAnulOC',
            data  :	formData,
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
