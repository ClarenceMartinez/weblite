function getLogPlanobra(component){
    var jsonData = $(component).data();
    var subtitulo = $('#titModalItemplanLog').children().eq(0);
    subtitulo.text('ItemPlan: '+jsonData.itemplan);

    $.ajax({
        type: 'POST',
        url: 'getLogPlanobra',
        data: {
            itemplan: jsonData.itemplan
        },
        beforeSend: () => {
            $('body').loading({
                message: 'Espere por favor...'
            });
        }
    }).done(function (data) {
        data = JSON.parse(data);
        if (data.error == 0){
            $('#cont_tb_log_ip').html(data.tbLog);
            $('#modalLogItemplan').modal('toggle');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    });
    
    
}

function getTabEstaciones(component){
    var jsonData = $(component).data();
    var subtitulo = $('#titModalEstaciones').children().eq(0);
    subtitulo.text('ItemPlan: '+jsonData.itemplan);

    $.ajax({
        type: 'POST',
        url: 'getTabEstacionPoUtils',
        data: {
            itemplan: jsonData.itemplan
        },
        beforeSend: () => {
            $('body').loading({
                message: 'Espere por favor...'
            });
        }
    }).done(function (data) {
        data = JSON.parse(data);
        if (data.error == 0){
            $('#contEstaciones').html(data.TabVerticalEstacion);
            $('#modalEstaciones').modal('toggle');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    });
    
    
}

$('#modalLogItemplan').on('shown.bs.modal', function(){ 
    initDataTableRow('tb_log_itemplan');
});


function verDetallePO(component){
	var jsonData = $(component).data();
	var subtitulo = $('#titModalDetPO').children().eq(0);
    subtitulo.text('PO: '+jsonData.codigo_po);

	$.ajax({
        type: 'POST',
        url: 'getModalDetallePO',
        data: {
            codigo_po: jsonData.codigo_po
        },
        beforeSend: () => {
            $('body').loading({
                message: 'Espere por favor...'
            });
        }
    }).done(function (data) {
        data = JSON.parse(data);
        if (data.error == 0){
            $('#cont_tb_log_po').html(data.tb_log_po);
            $('#cont_tb_detalle_po').html(data.tb_detalle);
            $('#modalDetallePO').modal('toggle');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    });
	
}

$('#modalDetallePO').on('shown.bs.modal', function(){ 
    initDataTableRow('tb_log_po');
});

var arrayKitPartida = [];
var montoTotal = 0;
var objDetallePo = {};
var objPo = {};

function editarPO(component){
	var jsonData = $(component).data();
	var subtitulo = $('#titModalEditPO').children().eq(0);
    subtitulo.text('PO: '+jsonData.codigo_po);
	objPo.itemplan = jsonData.itemplan;
	objPo.idEstacion = jsonData.estacion;
	objPo.codigoPO = jsonData.codigo_po;

	$.ajax({
        type : 'POST',
        url  : 'getDataKitPartida',
        data : { 
			itemplan : jsonData.itemplan,
			codigo_po: jsonData.codigo_po,
			origen : 1
		},
		beforeSend: () => {
			$('body').loading({
				message: 'Espere por favor...'
			});
		}
    }).done(function(data){
        data = JSON.parse(data);
        if(data.error == 0) {
			arrayKitPartida = data.arrayDetallePo;
            $('#contTablaKitPartida').html(data.tablaKitPartida);
            $('#contTablaPoDetalle').html(data.tablaDetallePo);
            modal('modalEditPO');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    })
	
}

$('#modalEditPO').on('shown.bs.modal', function(){ 
	initDataTableRow('tbKitPartida');
});


function agregarPartida(btn) {

    var codigoPartida   = btn.data('codigo_partida');
    var baremo          = btn.data('baremo');
    var costo_material  = btn.data('costo_material');
    var costoPreciario  = btn.data('costo_preciario');
    var nomPartida      = btn.data('nom_partida');

    objDetallePo.codigoPartida  = codigoPartida;
    objDetallePo.baremo         = baremo;
    objDetallePo.precioKit      = costo_material;
    objDetallePo.costoPreciario = costoPreciario;
    objDetallePo.nomPartida     = nomPartida;
    objDetallePo.cantidad       = 0;
    objDetallePo.costoMO        = 0;
    objDetallePo.costoMAT       = 0;
    objDetallePo.total          = 0;

    if(arrayKitPartida.length == 0) {
        arrayKitPartida.splice(arrayKitPartida.length , 0, objDetallePo);

        var tr = "<tr id='actividad" + objDetallePo.codigoPartida + "'>";
                    tr += "<td>" + objDetallePo.nomPartida + "</td>";
                    tr += "<td id='costo"+objDetallePo.codigoPartida+"'>" + objDetallePo.costoPreciario+ "</td>";
                    tr += "<td id=\"baremo" + objDetallePo.codigoPartida + "\">" + objDetallePo.baremo + "</td>";
                    tr += "<td style='max-width: 100px'><input type='text' class='form-control'  id='cantidad" + objDetallePo.codigoPartida + "' data-codigo_partida='"+objDetallePo.codigoPartida+"' onkeyup='calculaTotal($(this))' value='"+objDetallePo.cantidad+"' style=' border-style: ridge; border-width: 4px; text-align: center'></td>";
                    tr += "<td id='totalBaremo" + objDetallePo.codigoPartida + "'>"+objDetallePo.costoMO+"</td>";
                    tr += "<td id='precioKit" + objDetallePo.codigoPartida + "'>" + objDetallePo.precioKit+ "</td>";
                    tr += "<td id='totalMaterial" + objDetallePo.codigoPartida + "'>"+objDetallePo.costoMAT+"</td>";
                    tr += "<td id='total" + objDetallePo.codigoPartida + "'>"+objDetallePo.total+"</td>";
                    tr += "<td><a data-codigo_partida='"+objDetallePo.codigoPartida+"' class='btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1' title='Eliminar' onclick='eliminarPartidaKit("+ JSON.stringify(objDetallePo) +")'><i class='fal fa-times'></i></a></td>";
            tr += "</tr>";
    } else {
        var flgIgual = 0;
        var contador = 0;
        arrayKitPartida.forEach(function(data, key){
            if(data.codigoPartida == codigoPartida) {
                flgIgual = 1;
				//return false;
                // arrayKitPartida.splice(key , 1, objDetallePo);
            }
            contador++;
        });

        if(contador == arrayKitPartida.length) {
            if(flgIgual == 0) {
                arrayKitPartida.splice(arrayKitPartida.length , 0, objDetallePo);
                
                var tr = "<tr id='actividad" + objDetallePo.codigoPartida + "'>";
                            tr += "<td>" + objDetallePo.nomPartida + "</td>";
                            tr += "<td id='costo"+objDetallePo.codigoPartida+"'>" + objDetallePo.costoPreciario+ "</td>";
                            tr += "<td id=\"baremo" + objDetallePo.codigoPartida + "\">" + objDetallePo.baremo + "</td>";
                            tr += "<td style='max-width: 100px'><input type='text' class='form-control'  id='cantidad" + objDetallePo.codigoPartida + "' data-codigo_partida='"+objDetallePo.codigoPartida+"' onkeyup='calculaTotal($(this))' value='"+objDetallePo.cantidad+"' style=' border-style: ridge; border-width: 4px; text-align: center'></td>";
                            tr += "<td id='totalBaremo" + objDetallePo.codigoPartida + "'>"+objDetallePo.costoMO+"</td>";
                            tr += "<td id='precioKit" + objDetallePo.codigoPartida + "'>" + objDetallePo.precioKit+ "</td>";
                            tr += "<td id='totalMaterial" + objDetallePo.codigoPartida + "'>"+objDetallePo.costoMAT+"</td>";
                            tr += "<td id='total" + objDetallePo.codigoPartida + "'>"+objDetallePo.total+"</td>";
                            tr += "<td><a data-codigo_partida='"+objDetallePo.codigoPartida+"' class='btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1' title='Eliminar' onclick='eliminarPartidaKit("+ JSON.stringify(objDetallePo) +")'><i class='fal fa-times'></i></a></td>";
                    tr += "</tr>";
            }
        }
        
    }

    $("#tBodyActividades").append(tr);

    objDetallePo = {};
}


function calculaTotal(btn) {
    var codigoPartida = btn.data('codigo_partida');
    var lastValMO =  $("#totalBaremo"+codigoPartida).html();
    var lastValMat =  $("#totalMaterial"+codigoPartida).html();

    // montoTotal = (Number(montoTotal) - (Number(lastValMO) + Number(lastValMat))).toFixed(2);
    $("#totalBaremo" + codigoPartida).text('0.00');
    $("#totalMaterial" + codigoPartida).text('0.00');
    $("#total" + codigoPartida).text('0.00');
    $('#montoTotalGeneral').html('0.00');
    var cantidad = $("#cantidad" + codigoPartida).val();

    if(Number(cantidad) >= 1) {
        var baremo = $("#baremo" + codigoPartida).text();
        var precioKit = $("#precioKit" + codigoPartida).text().replace(',','');
        var costo = $("#costo" + codigoPartida).text();
        var totalMO = cantidad * Number(baremo) * Number(costo);
        console.log("totalMO: "+totalMO);
        totalMO = Number(totalMO).toFixed(2);


        var totalMAT = cantidad * Number(precioKit);
        totalMAT = Number(totalMAT).toFixed(2);

        var totalFinal = (Number(totalMO) + Number(totalMAT)).toFixed(2);
        var montoTotal = 0;
        console.log(arrayKitPartida);
        arrayKitPartida.forEach(function(data, key){
            if(codigoPartida == data.codigoPartida) {
                data.total     = totalFinal;
                data.costoMO   = totalMO;
                data.costoMAT  = totalMAT;
                data.cantidad  = cantidad;
                data.precioKit = precioKit;
            }

            montoTotal = Number(data.total) + montoTotal;
        });

        $("#totalBaremo" + codigoPartida).text(totalMO);
        $("#totalMaterial" + codigoPartida).text(totalMAT);

        $("#total" + codigoPartida).text(totalFinal);

        // montoTotal = (Number(montoTotal) + Number(totalFinal)).toFixed(2);
        $('#montoTotalGeneral').html(montoTotal.toFixed(2));

        objPo.total = montoTotal;
    }
}

function eliminarPartidaKit(objKit) {
    console.log(objKit);
    //objKit = JSON.parse(objKit);
    var codigoPartida = objKit.codigoPartida;
    var contador = 0;

    arrayKitPartida.forEach(function(data, key){
        if(data.codigoPartida == codigoPartida) {
            arrayKitPartida.splice(key , 1);
        }
        
        contador++;
    });
    var totalFinal = 0;
    $("#tBodyActividades").html('');
    arrayKitPartida.forEach(function(data, key){
        
        var tr = "<tr id='actividad" + data.codigoPartida + "'>";
                    tr += "<td>" + data.nomPartida + "</td>";
                    tr += "<td id='costo"+data.codigoPartida+"'>" + data.costoPreciario+ "</td>";
                    tr += "<td id=\"baremo" + data.codigoPartida + "\">" + data.baremo + "</td>";
                    tr += "<td style='max-width: 100px'><input type='text' class='form-control'  id='cantidad" + data.codigoPartida + "' data-codigo_partida='"+data.codigoPartida+"' onkeyup='calculaTotal($(this))' value='"+data.cantidad+"' style=' border-style: ridge; border-width: 4px; text-align: center'></td>";
                    tr += "<td id='totalBaremo" + data.codigoPartida + "'>"+data.costoMO+"</td>";
                    tr += "<td id='precioKit" + data.codigoPartida + "'>" + data.precioKit+ "</td>";
                    tr += "<td id='totalMaterial" + data.codigoPartida + "'>"+data.costoMAT+"</td>";
                    tr += "<td id='total" + data.codigoPartida + "'>"+data.total+"</td>";
                    tr += "<td><a data-codigo_partida='"+data.codigoPartida+"' class='btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1' title='Eliminar' onclick='eliminarPartidaKit("+ JSON.stringify(data) +")'><i class='fal fa-times'></i></a></td>";
            tr += "</tr>";

        $("#tBodyActividades").append(tr);
        totalFinal = totalFinal + Number(data.total);
    });

	totalFinal = totalFinal.toFixed(2);
    $('#montoTotalGeneral').html(totalFinal);
}


function guardarKitPartidaPin() {

    if(arrayKitPartida.length == 0){  
        mostrarNotificacion(1, 'warning', 'Aviso' , "Para guardar debe añadir como mínimo una partida!!");
		return;
    }

	Swal.queue([
	{
		title: "Está seguro de editar la PO??",
		text: "Asegurese de validar la información!!",
		icon: 'question',
		confirmButtonText: "SI",
		showCancelButton: true,
		cancelButtonText: 'NO',
		allowOutsideClick: false,
		showLoaderOnConfirm: true,
		preConfirm: function preConfirm()
		{
			return regEditPOPromise(arrayKitPartida, objPo).then(function (data) { 
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


function regEditPOPromise(arrayKitPartida, objPo){
	return new Promise(function (resolve, reject) {
		$.ajax({
			type : 'POST',
			url  : 'regEditPO',
			data : { 
				arrayKitPartida : arrayKitPartida,
				objPoFinal      : JSON.stringify(objPo)
			}
		}).done(function(data) {
	
			data = JSON.parse(data);
			if(data.error == 0) {
				modal('modalEditPO');
				resolve(data);
			} else {
				reject(data);
			}
		});
	});
}

function getLogSeguimientoCV(component){
    var jsonData = $(component).data();
    $('#formSeguimiento').trigger("reset");
    var subtitulo = $('#titModalLogSeguiCV').children().eq(0);
    subtitulo.text('ItemPlan: '+jsonData.itemplan);
    $('#btnSeguimiento').data('itemplan',jsonData.itemplan);
    $(".invalid-feedback").css('display','none');
    $(".valid-feedback").css('display','none');
    $('.is-valid').removeClass("is-valid");
    $('.is-invalid').removeClass("is-invalid");

    $.ajax({
        type: 'POST',
        url: 'getLogSeguimientoCV',
        data: {
            itemplan: jsonData.itemplan
        },
        beforeSend: () => {
            $('body').loading({
                message: 'Espere por favor...'
            });
        }
    }).done(function (data) {
        data = JSON.parse(data);
        if (data.error == 0){
            $('#selectMotivoSegui').html(data.cmbMotivoSegui);
            $('#contTbSeguimiento').html(data.tbLog);
			$('#ctnButtonLogSegui').html(data.btnSaveSegui);
            $('#formSeguimiento').children().eq(0).css('display',data.style);
            $('#formSeguimiento').children().eq(1).css('display',data.style);
			
            modal('modalLogSeguimientoCV');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    }); 
}

$('#modalLogSeguimientoCV').on('shown.bs.modal', function(){ 
    initDataTableRow('tb_log_segui_cv');
});

function regLogSeguimientoCV(component){
    var jsonData = $(component).data();
    var params = $("#formSeguimiento").serializeArray();//(lee todos los inputs select con name y que no sean disabled)
    console.log(jsonData);
    var msj = '';
    // var jsonData = {};
    var formData = new FormData();
    $.each(params, function (i, val) {
        if(val['value'] == null || val['value'] == '' || val['value'] == undefined){
            if(val['name'] == 'selectMotivoSegui'){
                msj = 'Debe seleccionar un motivo!!';
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(3);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#select2-'+val['name']+'-container').removeClass('is-valid is-invalid');
                $('#select2-'+val['name']+'-container').addClass('form-control is-invalid');
                divMsj.addClass('invalid-feedback');
                divMsj.text('Seleccione un motivo.');
                divMsj.css('display','block');
                return false;

            }else if(val['name'] == 'txtComentario'){
                msj = 'Debe ingresar un comentario!!';
                
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#'+val['name']).removeClass('is-valid is-invalid');
                $('#'+val['name']).addClass('form-control is-invalid');
                divMsj.addClass('invalid-feedback');
                divMsj.text('Ingrese un comentario.');
                divMsj.css('display','block');
                return false;
            }
            
        }else{
            if(val['name'] == 'selectMotivoSegui'){
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(3);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#select2-'+val['name']+'-container').removeClass('is-valid is-invalid');
                $('#select2-'+val['name']+'-container').addClass('form-control is-valid');
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }else if(val['name'] == 'txtComentario'){
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#'+val['name']).removeClass('is-valid is-invalid');
                $('#'+val['name']).addClass('form-control is-valid');
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }

            formData.append(val.name, val.value);
        }
    });

    if(msj != ''){
        swal.fire('Verificar!',msj,'warning');
        return;
    }
    
    formData.append('itemplan', jsonData.itemplan);
    Swal.queue([
    {
        title: "Está seguro de registrar el motivo de seguimiento??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return registrarLogSeguiPromise(formData).then(function (data) { 
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

function registrarLogSeguiPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'regLogSeguiCV',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $(".invalid-feedback").css('display','none');
                $(".valid-feedback").css('display','none');
                $('.is-valid').removeClass("is-valid");
                $('.is-invalid').removeClass("is-invalid");
                $('#selectMotivoSegui').html(data.cmbMotivoSegui);
                $('#contTbSeguimiento').html(data.tbLog);
                $('#formSeguimiento').trigger("reset");
                initDataTableRow('tb_log_segui_cv');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

function cancelarPO(component){

    var jsonData = $(component).data();
    
    Swal.queue([
    {
        title: "Está seguro de cancelar la PO??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return cancelarPOPromise(jsonData).then(function (data) { 
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

function cancelarPOPromise(jsonData){
	return new Promise(function (resolve, reject) {
		$.ajax({
			type : 'POST',
			url  : 'cancelarPO',
			data : jsonData
		}).done(function(data) {
			data = JSON.parse(data);
			if(data.error == 0) {
				modal('modalEstaciones');
				resolve(data);
			} else {
				reject(data);
			}
		});
	});
}

var arrayDetallePoGlob = [];
function editarPartidaAdicPqt(component){
	var jsonData = $(component).data();
	var subtitulo = $('#titModalPartAdicIntegral').children().eq(0);
    subtitulo.text('PO: '+jsonData.codigo_po);
	objPo.itemplan = jsonData.itemplan;
	objPo.idEstacion = jsonData.estacion;
	objPo.codigoPO = jsonData.codigo_po;


	$.ajax({
        type : 'POST',
        url  : 'getDataPartidaAdicIntegral',
        data : { 
			itemplan : jsonData.itemplan,
			codigo_po: jsonData.codigo_po,
			origen : 1
		},
		beforeSend: () => {
			$('body').loading({
				message: 'Espere por favor...'
			});
		}
    }).done(function(data){
        data = JSON.parse(data);
        if(data.error == 0) {
			arrayDetallePoGlob = data.arrayDetallePo;
            $('#contTablaPartidaAdicInte').html(data.tablaPartidaAdic);
            $('#contTablaPoDetalleMo').html(data.tablaDetallePo);
			objPo.total = data.totalDetPO;
            console.log(objPo);
            modal('modalPartAdicIntegral');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    })
}

$('#modalPartAdicIntegral').on('shown.bs.modal', function(){ 
    initDataTableRow('tbPartidaAdicIntegral');
});


function agregarPartidaAdicIntegral(component) {
    var jsonData = $(component).data();

    objDetallePo.codigoPartida    = jsonData.codigo_partida;
    objDetallePo.baremo           = jsonData.baremo;
    objDetallePo.preciario        = jsonData.precio;
    objDetallePo.cantidadInicial  = 0;
    objDetallePo.montoInicial     = 0;
    objDetallePo.cantidadFinal    = 0;
    objDetallePo.montoFinal       = 0;
    objDetallePo.costoMo          = 0;

    var hasPartida = false;
    $.each(arrayDetallePoGlob, function (index, value) {
        if (value['codigoPartida'] == jsonData.codigo_partida) {
            hasPartida = true;
            return false;
        }
    });

    if(hasPartida){
        swal.fire(
            'Verificar!',
            'Ya existe la partida en el detalle de la po!!',
            'warning'
        );
        return;
    }

    var tr = "<tr id='tr_" + objDetallePo.codigoPartida + "'>";
        tr += "<td><a data-codigo_partida='"+objDetallePo.codigoPartida+"' class='btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1' title='Eliminar' onclick='eliminarPartidaAdicIntegral(this)'><i class='fal fa-trash'></i></a></td>";
        tr += "<td>" + objDetallePo.codigoPartida + "</td>";
        tr += "<td>" + jsonData.nom_partida + "</td>";
        tr += "<td id=\"baremo" + objDetallePo.codigoPartida + "\">" + objDetallePo.baremo + "</td>";
        tr += "<td id='costo"+objDetallePo.codigoPartida+"'>" + objDetallePo.preciario+ "</td>";
        tr += "<td id='cantidadIni"+objDetallePo.codigoPartida+"'>" + objDetallePo.cantidadInicial+ "</td>";
        tr += "<td><input type='text' class='form-control'  id='cantidadFinal" + objDetallePo.codigoPartida + "' data-codigo_partida='"+objDetallePo.codigoPartida+"' onkeyup='calculaTotalPartAdic(this)' value='"+objDetallePo.cantidadFinal+"' style=' border-style: ridge; border-width: 4px; text-align: center'></td>";
        tr += "<td id='total" + objDetallePo.codigoPartida + "'>"+objDetallePo.montoFinal+"</td>";
        tr += "</tr>";

    arrayDetallePoGlob.splice(arrayDetallePoGlob.length, 0, objDetallePo);
    $("#tBodyPoDetalle").append(tr);
    objDetallePo = {};
}

function calculaTotalPartAdic(component) {
    var jsonData = $(component).data();
    var cantidad = $(component).val();
    var codigoPartida = jsonData.codigo_partida;
    $("#total" + codigoPartida).text('0.00');
    $('#costoTolalPo').text('S/.0.00');

    if(Number(cantidad) >= 1) {

        var montoTotal = 0;
        arrayDetallePoGlob.forEach(function(data, key){
            if(codigoPartida == data.codigoPartida) {
                if(data.cantidadInicial == 0){
                    data.cantidadInicial = cantidad;
                    data.montoInicial = (data.preciario * data.baremo * cantidad).toFixed(2);
                    $("#cantidadIni" + codigoPartida).text(cantidad);
                }
                data.cantidadFinal = cantidad;
                data.montoFinal = (data.preciario * data.baremo * cantidad).toFixed(2);
                data.costoMo   = (data.preciario * data.baremo * cantidad).toFixed(2);
                $("#total" + codigoPartida).text(data.montoFinal);
            }

            montoTotal = Number(data.montoFinal) + montoTotal;
        });
        $("#costoTolalPo").text('S/.'+formatearNumeroComas(montoTotal));
        objPo.total = (montoTotal).toFixed(2);
    }
}

function eliminarPartidaAdicIntegral(component) {
    
    var jsonData = $(component).data();
    var codigoPartida = jsonData.codigo_partida;

    $('#tr_'+codigoPartida).remove();
    var montoTotal = 0;
    arrayDetallePoGlob.forEach(function(data, key){
        if(data.codigoPartida == codigoPartida) {
            arrayDetallePoGlob.splice(key , 1);
        }else{
            montoTotal = Number(data.montoFinal) + montoTotal;
        }
    });
    $("#costoTolalPo").text('S/.'+formatearNumeroComas(montoTotal));
    objPo.total = (montoTotal).toFixed(2);
}

function guardarPartidaAdicIntegral() {

    if(arrayDetallePoGlob.length == 0){  
        mostrarNotificacion(1, 'warning', 'Aviso' , "Para guardar debe tener como mínimo una partida!!");
		return;
    }
    if(objPo.total == 0 || objPo.total == '' || objPo.total == undefined){
        mostrarNotificacion(1, 'warning', 'Aviso' , "Para guardar la po debe tener un monto válido (mayor a 0)!!");
		return;
    }

    arrayDetallePoGlob.forEach(function(data, key){
        if(data.montoFinal == 0 || data.montoFinal == '' || data.montoFinal == undefined) {
            mostrarNotificacion(1, 'warning', 'Aviso' , "Debe ingresar un monto mayor a 0 para guardar!!");
            $('#tr_'+data.codigoPartida).addClass('bg-danger-100');
		    return;
        }else{
            $('#tr_'+data.codigoPartida).removeClass('bg-danger-100');
        }
    });

	Swal.queue([
	{
		title: "Está seguro de editar la PO??",
		text: "Asegurese de validar la información!!",
		icon: 'question',
		confirmButtonText: "SI",
		showCancelButton: true,
		cancelButtonText: 'NO',
		allowOutsideClick: false,
		showLoaderOnConfirm: true,
		preConfirm: function preConfirm()
		{
			return regEditPartidaAdicIntegralPromise(arrayDetallePoGlob, objPo).then(function (data) { 
				return swal.fire({
                            icon: 'success',
                            title: 'Exitoso!',
                            text: data.msj,
                            showConfirmButton: true,
                            confirmButtonText: 'OK!',
                            showCancelButton: false,
                            allowOutsideClick: false
                        }).then((result) => {
                            modal('modalEstaciones');
                        });
			}).catch(function(e) {
				return Swal.insertQueueStep({
					icon: "error",
					title: e.msj
				});
			});
		}
	}]);

}

function regEditPartidaAdicIntegralPromise(arrayDetallePO, objPo){
	return new Promise(function (resolve, reject) {
		$.ajax({
			type : 'POST',
			url  : 'regEditPartidaAdicIntegral',
			data : { 
				arrayDetallePO : JSON.stringify(arrayDetallePO),
				objPoFinal     : JSON.stringify(objPo)
			}
		}).done(function(data) {
	
			data = JSON.parse(data);
			if(data.error == 0) {
				modal('modalPartAdicIntegral');
				resolve(data);
			} else {
				reject(data);
			}
		});
	});
}

function openModalCancelarIP(component){
    var jsonData = $(component).data();
    $('#formCancelarIP').trigger("reset");
    $('#selectMotivoCance').val(null).trigger("change");
    var subtitulo = $('#titModalCancelarIP').children().eq(0);
    subtitulo.text('ItemPlan: '+jsonData.itemplan);
    $('#btnCancelarIP').data('itemplan',jsonData.itemplan);
    $(".invalid-feedback").css('display','none');
    $(".valid-feedback").css('display','none');
    $('.is-valid').removeClass("is-valid");
    $('.is-invalid').removeClass("is-invalid");

    modal('modalCancelarIP');
}


function cancelarItemplan(component){
    var jsonData = $(component).data();
    var params = $("#formCancelarIP").serializeArray();//(lee todos los inputs select con name y que no sean disabled)
    console.log(jsonData);
    console.log(params);
    var msj = '';
    // var jsonData = {};
    var formData = new FormData();
    $.each(params, function (i, val) {
        if(val['value'] == null || val['value'] == '' || val['value'] == undefined){
            if(val['name'] == 'selectMotivoCance'){
                msj = 'Debe seleccionar un motivo!!';
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(3);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#select2-'+val['name']+'-container').removeClass('is-valid is-invalid');
                $('#select2-'+val['name']+'-container').addClass('form-control is-invalid');
                divMsj.addClass('invalid-feedback');
                divMsj.text('Seleccione un motivo.');
                divMsj.css('display','block');
                return false;

            }else if(val['name'] == 'txtComentario2'){
                msj = 'Debe ingresar un comentario!!';
                
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#'+val['name']).removeClass('is-valid is-invalid');
                $('#'+val['name']).addClass('form-control is-invalid');
                divMsj.addClass('invalid-feedback');
                divMsj.text('Ingrese un comentario.');
                divMsj.css('display','block');
                return false;
            }
            
        }else{
            if(val['name'] == 'selectMotivoCance'){
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(3);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#select2-'+val['name']+'-container').removeClass('is-valid is-invalid');
                $('#select2-'+val['name']+'-container').addClass('form-control is-valid');
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }else if(val['name'] == 'txtComentario2'){
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#'+val['name']).removeClass('is-valid is-invalid');
                $('#'+val['name']).addClass('form-control is-valid');
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }

            formData.append(val.name, val.value);
        }
    });

    if(msj != ''){
        swal.fire('Verificar!',msj,'warning');
        return;
    }
    var comprobar = $('#archivo').val().length;
    if(comprobar == 0){
        var divPadre = $('#archivo').parent();
        var divMsj = divPadre.children().eq(2);
        divMsj.removeClass('valid-feedback invalid-feedback');
        $('#archivo').removeClass('is-valid is-invalid');
        $('#archivo').addClass('form-control is-invalid');
        divMsj.addClass('invalid-feedback');
        divMsj.text('Seleccione un archivo.');
        divMsj.css('display','block');
        swal.fire('Verificar!','Debe subir un archivo para cancelar el itemplan!!','warning');
        return;
    }
    var file = $('#archivo')[0].files[0];
    formData.append('file', file);
    formData.append('itemplan', jsonData.itemplan);
    Swal.queue([
    {
        title: "Está seguro de cancelar el itemplan??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return cancelarItemplanPromise(formData).then(function (data) { 
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

function cancelarItemplanPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'cancelarItemplan',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('#contTabla').html(data.tbConsulta);
                initDataTable('tbPlanObra',2);
                modal('modalCancelarIP');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}


function openModalReenviarTramaMN(btn) {
	
	var itemplan 	= btn.data('item');		   
	console.log('itemplan:'+itemplan);
	
	Swal.queue([{
		title: 'Est&aacute; seguro de generar OT Mantenimiento?',
		text: 'Asegurese de que la informacion sea la correta.',
		type: 'warning',
		showCancelButton: true,
		buttonsStyling: false,
		confirmButtonClass: 'btn btn-primary',
		confirmButtonText: 'Si, enviar los datos!',
		cancelButtonClass: 'btn btn-secondary',
		allowOutsideClick: false,
        showLoaderOnConfirm: true,
		
		preConfirm: function preConfirm()
		{
			console.log('reenviar...');
			modal('modal_detalle_sirope');
			$.ajax({
				type	:	'POST',
				'url'	:	'reenviarTramaSiropeMN',
				data	:	{itemplan	:  itemplan},
				'async'	:	false
			})
			  .done(function(data) {  
					data = JSON.parse(data);
					if(data.error == 0){		    		
						swal.fire({
							title: 'Se realizo el reenvio para el Itemplan: ' + itemplan + '',
							text: 'Asegurese de validar la informacion!',
							type: 'success',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'OK!'
						}).then(function(){
							location.reload();
						});				    		
					}else if(data.error == 1){
						mostrarNotificacion('warning','No se pudo enviar la Trama', data.msj);
					}
			  })
			  .fail(function(jqXHR, textStatus, errorThrown) {
				mostrarNotificacion('error','Error','Comuníquese con alguna persona a cargo :(');
			  })
			  .always(function() {
				 
			});
		
		}
	}]).then(function(){
		
		
	});	
}