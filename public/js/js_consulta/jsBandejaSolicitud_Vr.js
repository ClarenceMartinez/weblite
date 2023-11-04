
var itemplanGlobal = null;
var POGlobal = null;
var codigoSolVrGlob = null;
var arrayDetalleSolGlob = [];
function viewDetalleSolVr(component){
    var jsonData = $(component).data();
    var subtitulo = $('#titModalDetalleSolVr').children().eq(0);
    subtitulo.text(jsonData.codigo);
    itemplanGlobal = jsonData.itemplan;
    POGlobal = jsonData.codigo_po;
    codigoSolVrGlob = jsonData.codigo;

    console.log(jsonData);
    // modal('modalDetalleSolVr');
    $.ajax({
        type  :	'POST',
        url   :	'getDetalleSolVrByCod',
        data  :	{
            itemplan: itemplanGlobal,
            codigoPO : POGlobal,
            codigoSolVr : codigoSolVrGlob
        },
        beforeSend: () => {
            $(this).attr("disabled", true);
        }
    }).done(function(data){
        var data = JSON.parse(data);
        console.log(data);
        if(data.error == 0){
            arrayDetalleSolGlob = JSON.parse(data.arrayDetalleSol);
            $('#contTablaDetSolVr').html(data.tablaDetalleSolVr);
            modal('modalDetalleSolVr');
        }else{
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
        
    }).always(() => {
        $(this).removeAttr("disabled");
    });
}


function viewDetalleConSolVr(component){
    var jsonData = $(component).data();
    var subtitulo = $('#titModalDetalleSolVr').children().eq(0);
    subtitulo.text(jsonData.codigo);
    itemplanGlobal = jsonData.itemplan;
    POGlobal = jsonData.codigo_po;
    codigoSolVrGlob = jsonData.codigo;

    console.log(jsonData);
    // modal('modalDetalleSolVr');
    $.ajax({
        type  :	'POST',
        url   :	'getDetalleConSolVrByCod',
        data  :	{
            itemplan: itemplanGlobal,
            codigoPO : POGlobal,
            codigoSolVr : codigoSolVrGlob
        },
        beforeSend: () => {
            $(this).attr("disabled", true);
        }
    }).done(function(data){
        var data = JSON.parse(data);
        console.log(data);
        if(data.error == 0){
            arrayDetalleSolGlob = JSON.parse(data.arrayDetalleSol);
            $('#contTablaDetSolVr').html(data.tablaDetalleSolVr);
            modal('modalDetalleSolVr');
        }else{
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
        
    }).always(() => {
        $(this).removeAttr("disabled");
    });
}

$('#modalDetalleSolVr').on('shown.bs.modal', function(){
    initDataTablePrueba('tbDetalleSolVr');
});

function setRechazo(component){
    var jsonData = $(component).data();
    console.log(jsonData);
    var comentario = $.trim($(component).val());
    console.log($('#chckBx_'+jsonData.pos).val());

	var checkValida = $('#selAteVr_'+jsonData.pos).val();
   
    if(checkValida  ==  1){//ATENDIDO        
        $('#chckBx_'+jsonData.pos).prop('checked',true);
        $('#chckBx_'+jsonData.pos).prop('disabled',false);
        arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 1;//validado
        arrayDetalleSolGlob[jsonData.pos]['comentario'] = comentario;
    }else{		
		if(comentario != null && comentario != '' && comentario != undefined){
			$('#chckBx_'+jsonData.pos).prop('checked',false);
			$('#chckBx_'+jsonData.pos).prop('disabled',true);
			arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 3;//rechazado
			arrayDetalleSolGlob[jsonData.pos]['comentario'] = comentario;
		}else{
			comentario = null;
			$('#chckBx_'+jsonData.pos).prop('checked',true);
			$('#chckBx_'+jsonData.pos).prop('disabled',false);
			arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 1;//validado
			arrayDetalleSolGlob[jsonData.pos]['comentario'] = comentario;
			$(component).prop('disabled',true);
		}
	}
    console.log(arrayDetalleSolGlob);
}

function validarDetalle(component){
    var jsonData = $(component).data();
    console.log(jsonData);
    var checkValida = $(component).prop('checked');
    console.log(checkValida);
    console.log(arrayDetalleSolGlob);
    if(checkValida){
        $('#txtRechazo_'+jsonData.pos).val(null);
        $('#txtRechazo_'+jsonData.pos).prop('disabled',true);
        arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 1;//validado
        arrayDetalleSolGlob[jsonData.pos]['comentario'] = null;
    }else{
        comentario = null;
        $('#txtRechazo_'+jsonData.pos).val(null);
        $('#txtRechazo_'+jsonData.pos).prop('disabled',false);
        arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 0;
        arrayDetalleSolGlob[jsonData.pos]['comentario'] = null;
        // $(component).prop('disabled',true);
    }
    console.log(arrayDetalleSolGlob);
}

function validarDetalle2(component){
    var jsonData = $(component).data();
    //console.log(jsonData);
    //var checkValida = $(component).prop('checked');
    //console.log(checkValida);
    //console.log(arrayDetalleSolGlob);
    var checkValida = $(component).val();
    if(checkValida  ==  1){//ATENDIDO
        var tipo_soli =   jsonData.tpsol;
		console.log(tipo_soli);
        if(tipo_soli    ==  3){//DEVOLUCION
            comentario = null;
            $('#txtRechazo_'+jsonData.pos).val(null);
            $('#txtRechazo_'+jsonData.pos).prop('disabled',false);
            arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 1;//validado
            arrayDetalleSolGlob[jsonData.pos]['comentario'] = comentario;
        }else{
            $('#txtRechazo_'+jsonData.pos).val(null);
            $('#txtRechazo_'+jsonData.pos).prop('disabled',true);
            arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 1;//validado
            arrayDetalleSolGlob[jsonData.pos]['comentario'] = null;           
        }
    }else if(checkValida    ==  3){//RECHAZADO       
        comentario = null;
        $('#txtRechazo_'+jsonData.pos).val(null);
        $('#txtRechazo_'+jsonData.pos).prop('disabled',false);
        arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 3;//rechazado
        arrayDetalleSolGlob[jsonData.pos]['comentario'] = comentario;
    }else{//no selecciono nada
        comentario = null;
        $('#txtRechazo_'+jsonData.pos).val(null);
        $('#txtRechazo_'+jsonData.pos).prop('disabled',false);
        arrayDetalleSolGlob[jsonData.pos]['flg_estado'] = 0;
        arrayDetalleSolGlob[jsonData.pos]['comentario'] = null;
    }
    console.log(arrayDetalleSolGlob);
}

function enviarRobotRpa(component){
    var jsonData = $(component).data();
    console.log(jsonData);
    var objData = {
        itemplan : jsonData.itemplan,
        codigo_po: jsonData.codigo_po,
        codigoSolVr : jsonData.codigo,
        codigo_material : jsonData.codigo_material,
    };
    Swal.queue([
    {
        title: "Está seguro de validar el envio del material por el robot??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return updateFlgRpaPromise(objData).then(function (data) { 
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

function updateFlgRpaPromise(objData){
	return new Promise(function (resolve, reject) {
		$.ajax({
			type : 'POST',
			url  : 'actualizarDetalleSolVr',
			data : objData
		}).done(function(data) {
			data = JSON.parse(data);
			if(data.error == 0) {
			    arrayDetalleSolGlob = JSON.parse(data.arrayDetalleSol);
                $('#contTablaDetSolVr').html(data.tablaDetalleSolVr);
                initDataTablePrueba('tbDetalleSolVr');
				resolve(data);
			} else {
				reject(data);
			}
		});
	});
}

function atenderDetalleSolVr(){

    console.log(arrayDetalleSolGlob);
    var arrayDataFinal = [];
    arrayDetalleSolGlob.forEach(function(data, key){
        if(data.flg_estado != 0 && data.flg_estado != '0' && (data.flg_evaluar == 1 || data.flg_evaluar == '1')) {
            arrayDataFinal.splice(arrayDataFinal.length, 0, data);
        }
    });
    console.log('arrayDataFinal:',arrayDataFinal);
    if(arrayDataFinal.length == 0){  
        mostrarNotificacion(1, 'warning', 'Aviso' , "Para guardar debe tener como mínimo un registro validado o rechazado!!");
		return;
    }

    var objData = {
        arrayDetalleSol : JSON.stringify(arrayDataFinal),
        itemplan : itemplanGlobal,
        codigo_po: POGlobal,
        codigoSolVr : codigoSolVrGlob
    };

    console.log(objData);
    Swal.queue([
    {
        title: "Está seguro de realizar la siguiente operación??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return atenderDetalleSolVrPromise(objData).then(function (data) { 
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

function atenderDetalleSolVrPromise(objData){
	return new Promise(function (resolve, reject) {
		$.ajax({
			type : 'POST',
			url  : 'atenderDetalleSolVr',
			data : objData
		}).done(function(data) {
			data = JSON.parse(data);
			if(data.error == 0) {
			    // arrayDetalleSolGlob = JSON.parse(data.arrayDetalleSol);
                $('#contTabla').html(data.tablaBandejaSolVr);
                initDataTable('tbBandejaSolVr',4);
                modal('modalDetalleSolVr');
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
                                }
                            ]
                };
    var tabla = $('#'+id_tabla).dataTable(jsonTB);
    return tabla;
}