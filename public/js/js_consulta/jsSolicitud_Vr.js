var idEmpresaColabGlobal = null;
var idJefaturaGlobal     = null;
var itemplanGlobal       = null;
var vrGlobal             = null;

$('#txtItemplan').keyup(function(){
                
    var itemplan = $(this).val();
    itemplan = itemplan.replace(/_/g, '');
    $(this).removeClass('is-valid is-invalid');
    var divPadre = $(this).parent().parent();
    var divMsj = divPadre.children().eq(2);
    divMsj.removeClass('valid-feedback invalid-feedback');
    if((itemplan).length >= 12){
        $.ajax({
            type  :	'POST',
            url   :	'getInfoComboPoByIP',
            data  :	{
                itemplan: itemplan
            },
            beforeSend: () => {
                $(this).attr("disabled", true);
            }
        }).done(function(data){
            var data = JSON.parse(data);
            // console.log(data);
            if(data.error == 0){
                objSolGlob.itemplan = itemplan;
                objSolGlob.idEmpresaColab = data.idEmpresaColab;
                objSolGlob.idJefaturaSap = data.idJefatura;

                idEmpresaColabGlobal = data.idEmpresaColab;
                idJefaturaGlobal = data.idJefatura;
                itemplanGlobal = itemplan;
                $('#selectPO').html(data.cmbPO);
                // $('#txtVR').val(data.vr);
                $('#txtEECC').val(data.empresacolab);
                $('#txtJefatura').val(data.jefatura);
                $('#txtCentro').val(data.codCentro);
                $('#txtAlmacen').val(data.codAlmacen);
                $(this).addClass('is-valid');
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
            }else{
                // $('#txtSubProyecto').val(data.subproyecto);
                // $('#txtEECC').val(data.eecc);
                $(this).addClass('is-invalid');
                divMsj.addClass('invalid-feedback');
                divMsj.text('No existe el itemplan!');
            }
            
        }).always(() => {
            $(this).removeAttr("disabled");
        });
    }else{
        $(this).addClass('is-invalid');
        divMsj.addClass('invalid-feedback');
        divMsj.text('Ingrese un itemplan válido!');
    }
    divMsj.css('display','block');

});

var POGlobal = null;
var idEstacionGlob = null;
var objSolGlob = {};
function getInfoPo(component){
    var jsonData = $(component).data();
    var POGlobal =  $(component).val();
    var dataOption = $('#'+jsonData.select2Id+' option:selected').data();
    idEstacionGlob = dataOption.id_estacion;
    vrGlobal = dataOption.vale_reserva;
    // console.log(jsonData);
     console.log('beee:'+POGlobal);
    // console.log(dataOption);
    if(POGlobal != null && POGlobal != '' && POGlobal != undefined){
        $.ajax({
            type  :	'POST',
            url   :	'getDetallePoMatForSolVR',
            data  :	{
                itemplan: itemplanGlobal,
                codigoPO : POGlobal,
                idEstacion : idEstacionGlob
            },
            beforeSend: () => {
                $(this).attr("disabled", true);
            }
        }).done(function(data){
            var data = JSON.parse(data);
            console.log(data);
            if(data.error == 0){
                objSolGlob.codigo_po = POGlobal;
                objSolGlob.vale_reserva = vrGlobal;
                objSolGlob.costoTotalAnt = data.costoTotal;
                arrayDataMatGlobal = JSON.parse(data.arrayDataMat);
                $('#txtVR').val(vrGlobal);
                $('#contTabla').html(data.tablaMat);
                $('.select2').select2();
                $('.soloEntero').numeric({
                    decimal: false,
                    negative: false
                });
                $('.soloDecimal').numeric({
                    negative: false,
                    decimalPlaces: 2
                });
                $('#contTabla').css('display','block');
            }else{
                mostrarNotificacion(1,'error', 'Aviso', data.msj);
            }
            
        }).always(() => {
            $(this).removeAttr("disabled");
        });
    }else{
        $('#txtVR').val(null);
        $('#contTabla').html(null);
        $('#contTabla').css('display','none');
    }
}

function getTipoSolVr(component){
    var jsonData = $(component).data();
    var inputComponent = $('#inputCantidad_'+jsonData.codigo_material);
    var idTipoSolicitud = $(component).val();
    getDataDetSol(inputComponent);
}

var arrayDataMatGlobal = [];

function getDataDetSol(component){
    var jsonData = $(component).data();
    //console.log(component);
    // return;
    var codigoMaterial = jsonData.codigo_material;
    var idTipoSolicitud = $('#selectTipoSolVr_'+jsonData.codigo_material).val();
    var cantidadIngresoAnt = $('#cantidadObra_'+codigoMaterial).val();
    var cantidad = $(component).val();

    var send_rpa = 0;
    var cantidadQueda = 0;
    //console.log('idTipoSolicitud:',idTipoSolicitud);
    //console.log('cantidadIngresoAnt:',cantidadIngresoAnt);
    //console.log('cantidad:',cantidad);

    if(cantidad == null || cantidad == '' || cantidad == undefined){
        // mostrarNotificacion(1,'warning', 'Aviso', '');
        return;
    }
    if(idTipoSolicitud == null || idTipoSolicitud == '' || idTipoSolicitud == undefined){
        mostrarNotificacion(1,'warning', 'Aviso', 'Debe seleccionar el tipo de solicitud.');
        return;
    }

    if(codigoMaterial == null || codigoMaterial == '' || codigoMaterial == undefined){
        mostrarNotificacion(1,'warning', 'Aviso', 'Hubo un error al detectar el material.');
        return;
    }

    if(idTipoSolicitud == 3) { //DEVOLUCION
		send_rpa = 1;
        $(component).prop("disabled", false);
        cantidadQueda = (Number(cantidadIngresoAnt) - Number(cantidad)).toFixed(2);
    } else {
        if(idTipoSolicitud == 1) {//ADICIÓN
			$(component).prop("disabled", false);
			cantidadQueda = (Number(cantidadIngresoAnt) + Number(cantidad)).toFixed(2);
		}else if(idTipoSolicitud == 2) {//ANULACIÓN 
			$(component).prop("disabled", true);
			cantidadQueda = 0;
			cantidad      = 0;
			$(component).val(0);
		} else if(idTipoSolicitud == 4) { // MODIFICACIÓN
			send_rpa = 1;
			$(component).prop("disabled", false);
			cantidadQueda = Number(cantidad);
		}
    }

    $('#cantidadIngreso_'+codigoMaterial).val(cantidadQueda);
    var jsonDataMat = {};
    var contador = 0;
    //console.log('contador1:'+contador);
    arrayDataMatGlobal.forEach(function(data, key){
        //console.log('data.codigoMaterial:'+data.codigoMaterial);
        //console.log('concodigoMaterialtador1:'+codigoMaterial);
        if(data.codigoMaterial == codigoMaterial) {
            //console.log('for1:'+codigoMaterial);
            //console.log(data);
            contador++;
            // data.codigoMaterial  = codigoMaterial;
            data.idTipoSolicitudVr  = idTipoSolicitud;
            data.cantidadInicio     = cantidad;
            data.cantidadFin        = cantidadQueda;
            data.costoMaterial      = jsonData.costo_material;
            if(data.flg_adicion ==  1){
                data.flg_adicion        = 1;
            }else{
                data.flg_adicion        = null;
            }
            //data.flg_adicion        = null;
			data.send_rpa           = send_rpa;
            return;
            // arrayDataKitGlobal.splice(key, 1, jsonDataKitGlobal);
            // jsonDataKitGlobal = {};
        }
    });
    //console.log('contador2:'+contador);
    if(contador == 0) {
        //console.log('for0:'+codigoMaterial);
        jsonDataMat.codigoMaterial     = codigoMaterial;
        jsonDataMat.idTipoSolicitudVr  = idTipoSolicitud;
        jsonDataMat.cantidadInicio     = cantidad;
        jsonDataMat.cantidadFin        = cantidadQueda;
        jsonDataMat.costoMaterial      = jsonData.costo_material;
        //jsonDataMat.flg_adicion        = (idTipoSolicitud == 1) ? 1 : null;
		jsonDataMat.send_rpa           = send_rpa;
        // arrayData.push(jsonData);
        arrayDataMatGlobal.splice(arrayDataMatGlobal.length, 0, jsonDataMat);
        jsonDataMat = {};
    }

    //console.log('ini array::::::::');
    //console.log(arrayDataMatGlobal);
    //console.log('fin array::::::::');
    getTotalPo(objSolGlob.costoTotalAnt);

}


function getTotalPo(costoPo){

    arrayDataMatGlobal.forEach(function(data, key){
        var cantidadIngresoAnt = $('#cantidadObra_'+data.codigoMaterial).val();
        var costoTemp = 0;
        if(data.idTipoSolicitudVr == 4) {//MODIFICACION
            if(Number(cantidadIngresoAnt) > Number(data.cantidadInicio)) {
				//console.log("COST1: "+costoPo);
                costoTemp = ((Number(cantidadIngresoAnt) - Number(data.cantidadInicio)) * Number(data.costoMaterial)).toFixed(2);
                costoPo = (Number(costoPo) - Number(costoTemp)).toFixed(2);
				//console.log("COST2: "+costoPo);
            } else {
                costoTemp = ((Number(data.cantidadInicio) - Number(cantidadIngresoAnt)) * Number(data.costoMaterial)).toFixed(2);
                costoPo = (Number(costoPo) + Number(costoTemp)).toFixed(2);
            }
        }else {
            costoTemp = (Number(data.cantidadInicio) * Number(data.costoMaterial)).toFixed(2);
            if(data.idTipoSolicitudVr == 1) {//ADICIÓN
				flgGlbSoloDev = 0;
                //console.log(costoPo+' + '+costoTemp);
                costoPo = (Number(costoPo) + Number(costoTemp)).toFixed(2);
            } else if(data.idTipoSolicitudVr == 3) {//DEVOLUCIÓN
                //console.log(costoPo+' - '+costoTemp);
                costoPo = (Number(costoPo) - Number(costoTemp)).toFixed(2);
            }
        }
    });

    objSolGlob.costoTotalNew = costoPo;
    $("#costoTolalPo").text('S/.'+formatearNumeroComas(costoPo));
     //console.log(objSolGlob);
     //console.log(arrayDataMatGlobal);
}

var arrayKitMatGlob = [];

function getKitMaterial(component){
    var jsonData = $(component).data();
    // console.log(jsonData);
    var subtitulo = $('#titModalKitMaterial').children().eq(0);
    subtitulo.text('Itemplan: '+jsonData.itemplan);
    arrayKitMatGlob = [];
    $.ajax({
        type : 'POST',
        url  : 'getKitMaterialForSolVr',
        data : { 
			itemplan : jsonData.itemplan,
			idEstacion: jsonData.idestacion
		},
		beforeSend: () => {
			$('body').loading({
				message: 'Espere por favor...'
			});
		}
    }).done(function(data){
        data = JSON.parse(data);
        if(data.error == 0) {
			arrayKitMatGlob = JSON.parse(data.arrayKitMat);
            $('#contTablaKitMat').html(data.tablaKitMat);
            modal('modalKitMaterial');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    })
}

$('#modalKitMaterial').on('shown.bs.modal', function(){ 
    initDataTablePrueba('tbKitMat');
});

function agregarKitMat(component){
    var jsonData = $(component).data();
    // console.log(jsonData);
    var codigoMaterial = jsonData.codigo_material;
    var hasMaterial = false;
    jsonDataMat = {};
    arrayDataMatGlobal.forEach(function(data, key){
        if(data.codigoMaterial == codigoMaterial) {
            hasMaterial = true;
            return;
        }
    });

    if(hasMaterial){
        swal.fire(
            'Verificar!',
            'Ya existe el material en el detalle!!',
            'warning'
        );
        return;
    }
    var tr = "<tr>";
        tr += "<td>" + (arrayDataMatGlobal.length + 1) + "</td>";
        tr += "<td>"+codigoMaterial+"</td>";
        tr += "<td>" + jsonData.descrip_material + "</td>";
        tr += "<td>" + jsonData.costo_material + "</td>";
        tr += "<td>" + "<input id='cantidadObra_"+codigoMaterial+"' data-cantidad='' class='form-control soloEntero' type='text' value='' readonly />" + "</td>";
        tr += "<td>" + "<input id='cantidadIngreso_"+codigoMaterial+"' class='form-control soloDecimal' type='text' value='' readonly />" + "</td>";
        tr += "<td>" 
                + "<select id='selectTipoSolVr_"+codigoMaterial+"' name='selectTipoSolVr_"+codigoMaterial+"' class='select2 form-control w-100'"+
                    "data-itemplan='"+objSolGlob.itemplan+"' data-codigo_material='"+codigoMaterial+"'  onchange='getTipoSolVr(this);' disabled>"+
                    "<option value='1' selected>ADICIÓN</option>"+
                " </select>" +
              "</td>";
        tr += "<td>" + "<input id='inputCantidad_"+codigoMaterial+"' data-codigo_material='"+codigoMaterial+"' data-costo_material='"+jsonData.costo_material+"' class='form-control soloEntero' type='text' value='' placeholder='Ingresar cantidad' onkeyup='getDataDetSol(this)' />" + "</td>";
        tr += "</tr>";
    $("#BodyDetalle").append(tr);
    jsonDataMat.codigoMaterial     = codigoMaterial;
    jsonDataMat.idTipoSolicitudVr  = 1;
    jsonDataMat.cantidadInicio     = null;
    jsonDataMat.cantidadFin        = null;
    jsonDataMat.costoMaterial      = jsonData.costo_material;
    jsonDataMat.flg_adicion        = 1;
    jsonDataMat.send_rpa           = 0;
    arrayDataMatGlobal.splice(arrayDataMatGlobal.length, 0, jsonDataMat);
    $('.select2').select2();
    $('.soloEntero').numeric({
        decimal: false,
        negative: false
    });
    $('.soloDecimal').numeric({
        negative: false,
        decimalPlaces: 2
    });
    modal('modalKitMaterial');
}

var flgGlbSoloDev = null;
function guardarSolicitudVR() {

    //console.log('objSolGlob:',objSolGlob);
    //console.log('arrayDataMatGlobal:',arrayDataMatGlobal);

    if(objSolGlob.itemplan == null || objSolGlob.itemplan == '' || objSolGlob.itemplan == undefined){
		return;
    }
    if(objSolGlob.idEmpresaColab == null || objSolGlob.idEmpresaColab == '' || objSolGlob.idEmpresaColab == undefined){
		return;
    }
    if(objSolGlob.idJefaturaSap == null || objSolGlob.idJefaturaSap == '' || objSolGlob.idJefaturaSap == undefined){
		return;
    }
    if(objSolGlob.codigo_po == null || objSolGlob.codigo_po == '' || objSolGlob.codigo_po == undefined){
		return;
    }
    if(objSolGlob.vale_reserva == null || objSolGlob.vale_reserva == '' || objSolGlob.vale_reserva == undefined){
		return;
    }
    flgGlbSoloDev = 1;
    var arrayDataFinal = [];
    //console.log('11111 inicio:',arrayDataFinal);
    arrayDataMatGlobal.forEach(function(data, key){
        //console.log('222data original iter:',data);
        if(data.idTipoSolicitudVr != null  && data.idTipoSolicitudVr != undefined && data.idTipoSolicitudVr != '') {
			if(data.idTipoSolicitudVr == 4){//MODIFICACION
                //console.log('333 MODIFICACION>>>>>>>>',data);               
				if(Number(data.cantidadFin) != null && Number(data.cantidadFin) != undefined && data.cantidadFin != '' && Number(data.cantidadFin) >= 0){
					arrayDataFinal.splice(arrayDataFinal.length, 0, data);
				}
                flgGlbSoloDev = 0
			}else if(data.idTipoSolicitudVr == 3){//DEVOLUCION
                //console.log('333 DEVOLUCION>>>>>>>',data);          
				if(Number(data.cantidadFin) != null && Number(data.cantidadFin) != undefined && data.cantidadFin != '' && Number(data.cantidadFin) >= 0){
					arrayDataFinal.splice(arrayDataFinal.length, 0, data);
				}
			}else{//ADICION
                //console.log('333 ADICION>>>>>>>>:',data);
				if(Number(data.cantidadFin) != null && Number(data.cantidadFin) != undefined && data.cantidadFin != '' && Number(data.cantidadFin) != 0){
					arrayDataFinal.splice(arrayDataFinal.length, 0, data);
				}
                flgGlbSoloDev = 0
			}
        }
        //console.log('4444iteracion n:', arrayDataFinal);
     });

    if(arrayDataFinal.length == 0){  
        mostrarNotificacion(1, 'warning', 'Aviso' , "Para guardar debe tener como mínimo un material!!");
		return;
    }
    var msj = '';
    arrayDataFinal.forEach(function(data, key){
        if(Number(data.cantidadFin) < 0) {
            msj = 'cantidad ingresada supera a la cantidad con la que cuenta el material';
            // $('#tr_'+data.codigoPartida).addClass('bg-danger-100');
            return;
        }

    });
    if(msj != ''){
        mostrarNotificacion(1, 'warning', 'Aviso' , msj);
        return;
    }
    //console.log('final:', arrayDataFinal);
    //console.log('here:'+'guardarSolicitudVR');
    //console.log('costo_total:'+objSolGlob.costoTotalNew);
    //console.log('POGlobal:'+POGlobal);
    //console.log('POGlobal:'+objSolGlob.codigo_po);
    if(flgGlbSoloDev == 1) {//SI SOLO TIENE DEVOLUCION SE EJECUTA NORMAL.
        Swal.queue([
            {
                title: "Está seguro de registrar la Solicitud de VR??",
                text: "Asegurese de validar la información!!",
                icon: 'question',
                confirmButtonText: "SI",
                showCancelButton: true,
                cancelButtonText: 'NO',
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: function preConfirm()
                {
                    return regSolVrPromise(arrayDataFinal, objSolGlob).then(function (data) { 
                        return swal.fire({
                                    icon: 'success',
                                    title: 'Exitoso!',
                                    text: data.msj,
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK!',
                                    showCancelButton: false,
                                    allowOutsideClick: false
                                }).then((result) => {
                                    location.reload();
                                });
                    }).catch(function(e) {
                        return Swal.insertQueueStep({
                            icon: "error",
                            title: e.msj
                        });
                    });
                }
            }]);
    }else{

        jsonCreateSol = { origen       		  : 3,
                            tipo_po_dato 	  : 1, 
                            accion_dato  	  : 2, 
                            codigo_po_dato    : objSolGlob.codigo_po, 
                            itemplan_dato  	  : itemplanGlobal, 
                            costoTotalPo_dato : objSolGlob.costoTotalNew, 
                            data_json         : arrayDataFinal,
                            idEstacion        : idEstacionGlob };

        canCreateEditPOByCostoUnitario(jsonCreateSol, function() {
            Swal.queue([
                {
                    title: "Está seguro de registrar la Solicitud de VR??",
                    text: "Asegurese de validar la información!!",
                    icon: 'question',
                    confirmButtonText: "SI",
                    showCancelButton: true,
                    cancelButtonText: 'NO',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function preConfirm()
                    {
                        return regSolVrPromise(arrayDataFinal, objSolGlob).then(function (data) { 
                            return swal.fire({
                                        icon: 'success',
                                        title: 'Exitoso!',
                                        text: data.msj,
                                        showConfirmButton: true,
                                        confirmButtonText: 'OK!',
                                        showCancelButton: false,
                                        allowOutsideClick: false
                                    }).then((result) => {
                                        location.reload();
                                    });
                        }).catch(function(e) {
                            return Swal.insertQueueStep({
                                icon: "error",
                                title: e.msj
                            });
                        });
                    }
                }]);
        });
    }
    

}

function regSolVrPromise(arrayDetalleSol, objSolGlob){
	return new Promise(function (resolve, reject) {
		$.ajax({
			type : 'POST',
			url  : 'regSolicitudVr',
			data : { 
				arrayDetalleSol : JSON.stringify(arrayDetalleSol),
				objSolicitud     : JSON.stringify(objSolGlob)
			}
		}).done(function(data) {
			data = JSON.parse(data);
			if(data.error == 0) {
				resolve(data);
			} else {
				reject(data);
			}
		});
	});
}



function initDataTablePrueba (id_tabla) {
    var jsonTB = {
                    responsive: true,
                    // paging: false,
                    fixedColumns: false,
                    language: {
                        searchPlaceholder: 'Buscar registros...',
                        sProcessing: 'Procesando...',
                        sZeroRecords: 'No se encontraron resultados',
                        sEmptyTable: 'Ning\u00fan dato disponible en esta tabla',
                        sInfoEmpty: 'Mostrando 0 de 0 de un total de 0 registros',
                        sLoadingRecords: 'Cargando...',
                        sInfo: 'Mostrando _START_ de _END_ de un total de _TOTAL_ registros'
                    },
                    dom:
                            "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                            buttons: [
                                {
                                    extend: 'pdfHtml5',
                                    text: 'PDF',
                                    titleAttr: 'Generar PDF',
                                    className: 'btn-outline-danger btn-sm mr-1'
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    titleAttr: 'Generar Excel',
                                    className: 'btn-outline-success btn-sm mr-1',
                                    charset: 'UTF-8',
                                    bom: true,
                                    customizeData: function ( data ) {
                                        for (var i=0; i< data.body.length; i++){
                                            for (var j=0; j< data.body[i].length; j++ ){
                                                data.body[i][j] = '\u200C' + data.body[i][j];
                                            }
                                        }
                                    }
                                },
                                {
                                    extend: 'csvHtml5',
                                    text: 'CSV',
                                    titleAttr: 'Generar CSV',
                                    className: 'btn-outline-success btn-sm mr-1',
                                    charset: 'UTF-8',
                                    bom: true,
                                    customizeData: function ( data ) {
                                        for (var i=0; i< data.body.length; i++){
                                            for (var j=0; j< data.body[i].length; j++ ){
                                                data.body[i][j] = '\u200C' + data.body[i][j];
                                            }
                                        }
                                    }
                                },
                                {
                                    extend: 'copyHtml5',
                                    text: 'Copiar',
                                    titleAttr: 'Copiar al Portapapeles',
                                    className: 'btn-outline-primary btn-sm mr-1'
                                },
                                {
                                    extend: 'print',
                                    text: 'Imprimir',
                                    titleAttr: 'Imprimir Tabla',
                                    className: 'btn-outline-primary btn-sm mr-1'
                                }

                            ]
                };
    var tabla = $('#'+id_tabla).dataTable(jsonTB);
    return tabla;
}