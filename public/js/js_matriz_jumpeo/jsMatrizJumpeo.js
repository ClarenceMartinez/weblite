jQuery(document).on('click', '#btnBuscarMatrizJumpeo',function(e)
  {
    e.preventDefault();
    var codigo_solicitud    = $("#input_codigo_solicitud");

    $("#v-tabs-tab").addClass('d-none');
    $("#v-tabs-tabContent").addClass('d-none');
    $("#btnVerLogsMatriz").addClass('d-none');

    if ($.trim(codigo_solicitud.val()) == "")
    {
      mostrarNotificacion(1, 'error', 'Ingrese Código Solicitud', 'Incorrecto');
      return false;
    }
    $("#v-tabs-tabContent").removeClass('d-none');
})



//btn-atender-sol
jQuery(document).on('click', '.btn-log',function(e)
{
	e.preventDefault();
	var idx = $(this).attr('data-idx');
    var base = $(this).attr('data-base');
	$("#modalxlog").modal('show');

	$.ajax({
	        type : 'POST',
	        url  : 'getLogByMatrizJumpeo',
	        data : {id : idx},
	        dataType: 'json',
	    }).done(function(response)
	    {
	    	if (response.error == 1)
	    	{

	    	}

	    	var html = '';
	    	if (response.TOTAL > 0)
	    	{
		    	$.each(response.lista, function( index, item )
	      		{
                    evidencia = (item.evidencia == "") ? '' : '<a href="'+base+'uploads/matriz_jumpeo/'+item.evidencia+'">'+item.evidencia+'</a>';
	      			html += '<tr>';
	      			html += '<td>'+item.proceso+'</td>';
	      			html += '<td>'+item.nombre_completo+'</td>';
	      			html += '<td>'+item.fecha_registro+'</td>';
	      			html += '<td>'+item.comentario+'</td>';
	      			html += '<td>'+evidencia+'</td>';
	      			html += '</tr>';
	      		})
	    	}

	    	$("#tblModalHistory").find('tbody').html(html);
	    });
})

jQuery(document).on('click', '.btn-verAll',function(e)
{
    e.preventDefault();
    var idx = $(this).attr('data-idx');
    var base = $(this).attr('data-base');
    $("#modalAll").modal('show');
    // console.log("Hola");
    $(".lblx").html("-");
    // return false;

    $.ajax({
            type : 'POST',
            url  : 'getInfoMatrizJumpeoById',
            data : {id : idx},
            dataType: 'json',
        }).done(function(response)
        {
            if (response.error == 1)
            {

            }
            // console.log(response);
            var html = '';
            
            if (response.TOTAL > 0)
            {
                $.each(response.LISTA, function( index, item )
                {
                    $("#tblModalAll").find('#lbl_gestor').html(item.gestor);
                    $("#tblModalAll").find('#lbl_zonal').html(item.zonal);
                    $("#tblModalAll").find('#lbl_fase').html(item.fase);
                    $("#tblModalAll").find('#lbl_solicitud').html(item.fechaSolicitud);
                    $("#tblModalAll").find('#lbl_etapa').html(item.etapa);
                    $("#tblModalAll").find('#lbl_proyecto').html(item.proyecto);
                    $("#tblModalAll").find('#lbl_itemplan').html(item.itemplan);
                    $("#tblModalAll").find('#lbl_cto').html(item.puertoCto);
                    $("#tblModalAll").find('#lbl_odf').html(item.odf);
                    $("#tblModalAll").find('#lbl_modulo').html(item.modulo);
                    $("#tblModalAll").find('#lbl_bandeja').html(item.bandeja);
                    $("#tblModalAll").find('#lbl_cable').html(item.cable);
                    $("#tblModalAll").find('#lbl_hilo').html(item.hilo);
                    $("#tblModalAll").find('#lbl_armario').html(item.armario);
                    $("#tblModalAll").find('#lbl_divicau').html(item.divicau);
                    $("#tblModalAll").find('#lbl_distrito').html(item.distrito);
                    $("#tblModalAll").find('#lbl_edificio_proyecto').html(item.edifProyecto);
                    $("#tblModalAll").find('#lbl_x_edificio').html(item.edificioX);
                    $("#tblModalAll").find('#lbl_y_edificio').html(item.edificioY);
                    $("#tblModalAll").find('#lbl_contrata_pext').html(item.contrataPext);
                    $("#tblModalAll").find('#lbl_codigo_cms').html(item.codigoCMS);

                    $("#tblModalAll").find('#lbl_olt').html(item.nombre_olt);
                    $("#tblModalAll").find('#lbl_pshelf').html(item.shelf);
                    $("#tblModalAll").find('#lbl_slot').html(item.slot);
                    $("#tblModalAll").find('#lbl_port').html(item.port);
                    $("#tblModalAll").find('#lbl_eecc_pint').html(item.eeccpint);
                    $("#tblModalAll").find('#lbl_estado').html(item.estado);
                    $("#tblModalAll").find('#lbl_fecha_despacho').html(item.fechaDespachoEECC);
                    $("#tblModalAll").find('#lbl_fecha_termino').html(item.fechaTermino);
                    $("#tblModalAll").find('#lbl_itemplan_olt').html(item.itemplanPinOt);
                    $("#tblModalAll").find('#lbl_order_isp').html(item.workOrderISP);
                    console.log(item);
                })
            }

        });
})

//btn-atender-sol
jQuery(document).on('click', '.btn-atender-sol',function(e)
{
	e.preventDefault();
	$("#modalx").modal('show');

	var __this 	= $(this);
	var __idx	= __this.attr('data-idx');

	var slot	= __this.attr('data-slot');
	var shelf	= __this.attr('data-shelf');
	var port	= __this.attr('data-port');
	var olt		= __this.attr('data-olt');

    $("#frmMatrizJumpeoUpdate").find("#slot").attr('readonly', 'readonly');
    $("#frmMatrizJumpeoUpdate").find("#shelf").attr('readonly', 'readonly');
    $("#frmMatrizJumpeoUpdate").find("#port").attr('readonly', 'readonly');
    $("#frmMatrizJumpeoUpdate").find("#nombre_olt").attr('readonly', 'readonly');

    if (slot == "")
    {
        $("#frmMatrizJumpeoUpdate").find("#slot").removeAttr('readonly');
    }

    if (shelf == "")
    {
        $("#frmMatrizJumpeoUpdate").find("#shelf").removeAttr('readonly');
    }

    if (port == "")
    {
        $("#frmMatrizJumpeoUpdate").find("#port").removeAttr('readonly');
    }

    if (olt == "")
    {
        $("#frmMatrizJumpeoUpdate").find("#nombre_olt").removeAttr('readonly');
    }

	$("#frmMatrizJumpeoUpdate").find("#id").val(__idx);

	$("#frmMatrizJumpeoUpdate").find("#slot").val(slot);
	$("#frmMatrizJumpeoUpdate").find("#shelf").val(shelf);
	$("#frmMatrizJumpeoUpdate").find("#port").val(port);
	$("#frmMatrizJumpeoUpdate").find("#nombre_olt").val(olt);

})


//btn-atender-sol
jQuery(document).on('keyup', '#itemplanPinOt',function(e)
{
	e.preventDefault();
	var __this = $(this);

	if ($.trim(__this.val()) != "")
	{

				$.ajax({
			        type : 'POST',
			        url  : 'getEECCByItemPlan',
			        data : {itemplan : __this.val()},
			        dataType: 'json',
			    }).done(function(data)
			    {
			    	$("#eeccpint").val("");
			    	if (data.TOTAL > 0)
			    	{
			    		$("#eeccpint").val(data.LISTA[0].empresaColabDesc);
			    		$("#workOrderISP").val(data.LISTA[0].ult_codigo_sirope);
			    	}

                    if (data.CMS.length > 0)
                    {
                        $("#codigoCMS").val(data.CMS[0].cms);                        
                    }
			    })



	}
})

jQuery(document).on('click', '.btn-atender-sol3',function(e)
{
	e.preventDefault();
	$("#modalx3").modal('show');

	var __this 	= $(this);
	var __idx	= __this.attr('data-idx');
	$("#frmMatrizJumpeoUpdate3").find("#id").val(__idx);

})


jQuery(document).on('click', '.btn-atender-sol2',function(e)
{
	e.preventDefault();
	$("#modalx").modal('show');

	var __this 	= $(this);
	var __idx	= __this.attr('data-idx');
	$("#frmMatrizJumpeoUpdate").find("#id").val(__idx);

})


jQuery(document).on('click', '#btn-guardar-x',function(e)
{
	e.preventDefault();
	var frm 	= $("#frmMatrizJumpeoUpdate");
	var item 	= frm.find('#itemplanPinOt');
    var nombre_olt      = frm.find('#nombre_olt');
    var slot            = frm.find('#slot');

	if ($.trim(item.val()) == "")
	{
		mostrarNotificacion(1, 'error', 'Ingrese ItemPlan', 'Incorrecto');
		item.focus();
      	return false;
	}

    if ($.trim(nombre_olt.val()) == "")
    {
        mostrarNotificacion(1, 'error', 'Ingrese Nombre OLT', 'Incorrecto');
        nombre_olt.focus();
        return false;
    }

    if ($.trim(slot.val()) == "")
    {
        mostrarNotificacion(1, 'error', 'Ingrese Nombre SLOT', 'Incorrecto');
        slot.focus();
        return false;
    }




	$.ajax({
        type : 'POST',
        url  : 'saveMatrizJumpeoById',
        data : frm.serialize(),
        dataType: 'json',
    }).done(function(data)
    {
    	if (data.error == 1)
    	{
			mostrarNotificacion(1, 'error', data.msj, 'Verifique');
	    	return false;

    	}

    	mostrarNotificacion(2, 'success', data.msj, 'Perfecto');
		$("#modalx").modal('hide');
	    return false;
    })

})

jQuery(document).on('click', '.btn-dismis-modal',function(e)
{
	e.preventDefault();
	$(".modal").modal('hide');
})



jQuery(document).on('click', '#btnObservar',function(e)
{
	e.preventDefault();

	var id 			= $("#frmMatrizJumpeoUpdate3").find("#id").val();
	var comentario 	= $("#frmMatrizJumpeoUpdate3").find("#comentario").val();

	$.ajax({
        type : 'POST',
        url  : 'saveMatrizJumpeoByIdObservado',
        data : {id: id, comentario : comentario},
        dataType: 'json',
    }).done(function(response)
    {
    	if (response.error == 1)
    	{
    		mostrarNotificacion(1, 'error', response.msj, 'Incorrecto');
    		return false;
    	}

    	mostrarNotificacion(2, 'success', 'El Código de Solicitud ha sido observado correctamente', 'Perfecto');
		$("#modalx").modal('hide');

    })


})

jQuery(document).on('click', '#btnrechazar',function(e)
{
	e.preventDefault();


	var id = $("#frmMatrizJumpeoUpdate").find("#id").val();

	$.ajax({
        type : 'POST',
        url  : 'saveMatrizJumpeoByIdRechazado',
        data : {id: id},
        dataType: 'json',
    }).done(function(data)
    {

    })

	mostrarNotificacion(2, 'success', 'El Código de Solicitud ha sido rechazado correctamente', 'Perfecto');
	// mostrarNotificacion(1, 'error', 'Ingrese Código Solicitud', 'Incorrecto');
	$("#modalx").modal('hide');
})


jQuery(document).on('click', '#btn-guardar-x2',function(e)
{
	e.preventDefault();
	//frmMatrizJumpeoUpdate

	var url = 'saveEvidenciaMatrizJumpeo';
	var formData 	= new FormData($("#frmMatrizJumpeoUpdate")[0]);

	var _token = $('meta[name="csrf_token"]').attr('content');

	$.ajax({
			url:url,
			method:"POST",
			dataType:'json',
			headers: {'X-CSRF-TOKEN': _token},
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
           success:function(response)
           {
           		if (response.error == 1)
		        {
		          mostrarNotificacion(1, 'error', response.msj, 'Incorrecto');
		          return false;
		        }

	            	mostrarNotificacion(2, 'success', response.msj, 'Perfecto');
					$("#modalx").modal('hide');
           }
       })

})



jQuery(document).on('click', '#btn-guardar-x3',function(e)
{
	e.preventDefault();
	//frmMatrizJumpeoUpdate

	var url = 'saveMatrizJumpeo_Jum';
	var formData 	= new FormData($("#frmMatrizJumpeoUpdate3")[0]);

	var _token = $('meta[name="csrf_token"]').attr('content');

	$.ajax({
			url:url,
			method:"POST",
			dataType:'json',
			headers: {'X-CSRF-TOKEN': _token},
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
           success:function(response)
           {
           		if (response.error == 1)
		        {
		          mostrarNotificacion(1, 'error', response.msj, 'Incorrecto');
		          return false;
		        }

	            	mostrarNotificacion(2, 'success', response.msj, 'Perfecto');
					$(".modal").modal('hide');
           }
       })

})



jQuery(document).on('click', '.btn-dismis-modal-x',function(e)
{
	e.preventDefault();
	$("#modalx").modal('hide');
})



jQuery(document).on('click', '.btn-cancelar-sol',function(e)
{
    e.preventDefault();
    var idx = $(this).attr('data-idx');
    // alert(idx);


                Swal.queue([
                {
                    title: "Está seguro de cancelar esta solicitud?",
                    // text: "Asegurese de seleccionar un archivo de tipo (.xls,.xlsx)",
                    icon: 'question',
                    confirmButtonText: "SI",
                    showCancelButton: true,
                    cancelButtonText: 'NO',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function preConfirm()
                    {
                        return cancelarSolicitud(idx).then(function (data) { 
                            return swal.fire('Exitoso!',data.msj,'success');
                        }).catch(function(e) {
                            return Swal.insertQueueStep(
                            {
                                icon: "error",
                                title: e.msj
                            });
                        });
                    }
                }])
})


function cancelarSolicitud(id)
{
    return new Promise(function (resolve, reject)
    {
        var ruta = 'cancelarSolicitudJumpeo';
        $.ajax({
                type: 'POST',
                url: ruta,
                data: {id:id},
                dataType: 'json',
                beforeSend: () => {
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                }
        }).done(function (response)
        {
            if(response.error == 0)
            {
                resolve(response);
            }
            else
            {
                reject(response);
            }



        })
    })

}


function exportarFormatoCargaMatrizJumpeo()
{
  var ruta = 'getExcelFmtAtenMatrizJumpeo';
  

    $.ajax({
            type: 'POST',
            url: ruta,
            data: {},
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function (data) {
        data = JSON.parse(data);
        if (data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download",data.nombreArchivo);
                $a[0].click();
                $a.remove();
            }
        } else {
            swal.fire('Aviso!', data.msj, 'error');
        }
    }).always(() => {
        $('body').loading('destroy');
    });
}






function procesarFile()
{
    var comprobar = $('#archivo').val().length;
    if(comprobar == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe subir un archivo a procesar!!');
        return;
    }

    var formData = new FormData();
    var files = $('#archivo')[0].files[0];
    formData.append('file', files);
    Swal.queue([
    {
        title: "Está seguro de procesar el archivo??",
        text: "Asegurese de seleccionar un archivo de tipo (.xls,.xlsx)",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return cargarExcelPromise(formData).then(function (data) { 
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

function procesarFilePinPex()
{
    var comprobar = $('#archivo').val().length;
    if(comprobar == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe subir un archivo a procesar!!');
        return;
    }

    var formData = new FormData();
    var files = $('#archivo')[0].files[0];
    formData.append('file', files);
    Swal.queue([
    {
        title: "Está seguro de procesar el archivo??",
        text: "Asegurese de seleccionar un archivo de tipo (.xls,.xlsx)",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return cargarExcelPromisePINPex(formData).then(function (data) { 
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

var arrayDataGlob = [];
function cargarExcelPromise(formData){
    return new Promise(function (resolve, reject) {

      var ruta = 'procesarFileMatJum';
     

        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDataGlob = JSON.parse(data.jsonDataFile);
                console.log('arrayDataGlob: ',arrayDataGlob);
                $('#contTabla').html(data.tbObservacion);
                $('#tituTbObs').text(data.titulo);
                $('#tituTbObs').css('display', 'block');
                initDataTableResponsive('tbObservacion');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}


function cargarExcelPromisePINPex(formData){
    return new Promise(function (resolve, reject) {

      var ruta = 'procesarFileMatPinxPex';
     

        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data)
        {   
            var data = JSON.parse(data);
            if (data.error == 0)
            {
                resolve(data);
                depurarDuplicadosMatrizPinPex();
            }
            else
            {
                reject(data);
            }
            
        });
    });
}


function depurarDuplicadosMatrizPinPex()
{
    var ruta = 'depDupliMatPinPex';
    $.ajax({
            type  : 'POST',
            url   : ruta,
            dataType : 'json',
        }).done(function(data)
        {

        })

}


function cargarFile()
{

    if(arrayDataGlob.length == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe tener registros válidos para cargar!!');
        return;
    }

    var formData = new FormData();
    formData.append('arrayDataFile', JSON.stringify(arrayDataGlob));
  
    var files = $('#archivo')[0].files[0];
    formData.append('file', files)
  
    Swal.queue([
    {
        title: "Está seguro de atender las solicitudes??",
        text: "Asegurese de validar la información",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return atenderSolPromise(formData).then(function (data) { 
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




function atenderSolPromise(formData)
{
    return new Promise(function (resolve, reject)
    {
      var ruta = 'cargaAtenMasivaMatrizJumpeo';


        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDataGlob = [];
                $('#archivo').val(null);
                $('#contTabla').html(null);
                $('#tituTbObs').text('');
                $('#tituTbObs').css('display','none');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}



function exportarEvidencia(a)
{
  	var a =  $(a).attr('download');
  	$(a)[0].click();
  	return false;
}


jQuery(document).on('click', '#btnBuscarCodigoSolicitud',function(e)
{
	e.preventDefault();
	var txt_codigo_solicitud 	= $("#txt_codigo_solicitud").val();
	var selectEstado 			= $("#selectEstado").val();


	var ruta = 'searchMatrizJumpeoByFilters';


        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : {codigo_solicitud : txt_codigo_solicitud, estado: selectEstado},
            dataType : 'json',
        }).done(function(response)
        { 
        	if (response.error == 0)
        	{
        		reInitTableLight(response.matriz);
        	}    
        });

})


jQuery(document).on('click', '#btnBuscarCodigoSolicitudbanPlIntJum',function(e)
{
    e.preventDefault();
    var txt_codigo_solicitud    = $("#txt_codigo_solicitud").val();
    var selectEstado            = $("#selectEstado").val();


    var ruta = 'searchMatrizJumpeoByFiltersbanPlIntJum';


        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : {codigo_solicitud : txt_codigo_solicitud, estado: selectEstado},
            dataType : 'json',
        }).done(function(response)
        { 
            if (response.error == 0)
            {
                reInitTableLight(response.matriz);
            }    
        });

})



jQuery(document).on('click', '#btnBuscarCodigoSolicitudJumEECCPEXT',function(e)
{
    e.preventDefault();
    var txt_codigo_solicitud    = $("#txt_codigo_solicitud").val();
    var selectEstado            = $("#selectEstado").val();


    var ruta = 'searchMatrizJumpeoByFiltersgetSolJumEECCPEXT';


        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : {codigo_solicitud : txt_codigo_solicitud, estado: selectEstado},
            dataType : 'json',
        }).done(function(response)
        { 
            if (response.error == 0)
            {
                reInitTableLight(response.matriz);
            }    
        });

})




jQuery(document).on('click', '#btnBuscarCodigoSolicitudEECCPIN',function(e)
{
    e.preventDefault();
    var txt_codigo_solicitud    = $("#txt_codigo_solicitud").val();
    var selectEstado            = $("#selectEstado").val();


    var ruta = 'searchMatrizJumpeoByFiltersgetSolJumEECCPIN';


        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : {codigo_solicitud : txt_codigo_solicitud, estado: selectEstado},
            dataType : 'json',
        }).done(function(response)
        { 
            if (response.error == 0)
            {
                reInitTableLight(response.matriz);
            }    
        });

})


