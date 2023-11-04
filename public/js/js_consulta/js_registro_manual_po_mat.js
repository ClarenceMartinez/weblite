function expotarFormatoCargaPO(){
    $.ajax({
        type: 'POST',
        url: 'exportFormatoCargaPoMat',
        data: {
            itemplan: itemplanGlob,
            idEstacion: idEstacionGlob
        },
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

function cargarExcelPO(){
    var comprobar = $('#archivo').val().length;
    if(comprobar == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe subir un archivo a procesar!!');
        return;
    }

    var formData = new FormData();
    var files = $('#archivo')[0].files[0];
    formData.append('file', files);
    formData.append('idEstacion', idEstacionGlob);
    formData.append('itemplan', itemplanGlob);

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
            return cargarExcelPOPromise(formData).then(function (data) { 
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

var arrayDetPoGlob = [];
var costoTotalGlob = null;

function cargarExcelPOPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'procesarExcelPoMat',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDetPoGlob = JSON.parse(data.jsonDataFile);
                console.log('arrayDetPoGlob: ',arrayDetPoGlob);
                costoTotalGlob = data.costoTotal;
                $('#cont_tb_po').html(data.tbReporte);
                $('#costoTolalPo').text(data.costoTotalFormat);
                $('#tituloCarga').text(data.titulo);
                $('#ctnDetalleCosto').css('display','block');
                mostrarNotificacion('success','Aviso',data.msj);
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

function registrarPO(){

    if(arrayDetPoGlob.length == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe cargar materiales para registrar!!');
        return;
    }
    if(costoTotalGlob == null || costoTotalGlob == 0 || costoTotalGlob == '' || costoTotalGlob == undefined){
        mostrarNotificacion(1,'warning', 'Aviso','Debe haber un costo mayor a 0!!');
        return;
    }

    jsonCreateSol = { origen       		: 1,
                    tipo_po_dato 		: 1, 
                    accion_dato  		: 1, 
                    codigo_po_dato    : null, 
                    itemplan_dato  	: itemplanGlob, 
                    costoTotalPo_dato : costoTotalGlob, 
                    data_json         : arrayDetPoGlob,
                    idEstacion        : idEstacionGlob };
                                
            canCreateEditPOByCostoUnitario(jsonCreateSol, function() {
      
                console.log('itemplanGlob:'+itemplanGlob);
        var formData = new FormData();
        formData.append('idEstacion', idEstacionGlob);
        formData.append('itemplan', itemplanGlob);
        formData.append('costoTotalPo', costoTotalGlob);
        formData.append('arrayDetPo', JSON.stringify(arrayDetPoGlob));
        Swal.queue([
        {
            title: "Está seguro de registrar la po??",
            text: "Asegurese de validar la información",
            icon: 'question',
            confirmButtonText: "SI",
            showCancelButton: true,
            cancelButtonText: 'NO',
            allowOutsideClick: false,
            showLoaderOnConfirm: true,
            preConfirm: function preConfirm()
            {
                return registrarPoPromise(formData).then(function (data) { 
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
    });
}

function registrarPoPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'registrarPoMat',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDetPoGlob = [];
                costoTotalGlob = null;
                $('#archivo').val(null);
                $('#cont_tb_po').html(null);
                $('#costoTolalPo').text('');
                $('#tituloCarga').text('');
                $('#ctnDetalleCosto').css('display','none');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

