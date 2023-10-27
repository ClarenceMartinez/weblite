function expotarFormatoCargaPO(){
    $.ajax({
        type: 'POST',
        url: 'detPoLiquiMo',
        data: {
            itemplan: itemplanGlob,
            idEstacion: idEstacionGlob,
            codigo_po : codigo_poGlob
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
            url   :	'procesLiquiPoMo',
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
        mostrarNotificacion(1,'warning', 'Aviso','Debe cargar partidas para poder Editar!!');
        return;
    }
    if(costoTotalGlob == null || costoTotalGlob == 0 || costoTotalGlob == '' || costoTotalGlob == undefined){
        mostrarNotificacion(1,'warning', 'Aviso','Debe haber un costo mayor a 0!!');
        return;
    }

    jsonCreateSol = { origen       		: 4,//EDITAR PO MI
                    tipo_po_dato 		: 2, 
                    accion_dato  		: 2, 
                    codigo_po_dato      : codigo_poGlob, 
                    itemplan_dato  	    : itemplanGlob, 
                    costoTotalPo_dato   : costoTotalGlob, 
                    data_json           : arrayDetPoGlob,
                    idEstacion          : idEstacionGlob };

    canCreateEditPOByCostoUnitario(jsonCreateSol, function() {
        var formData = new FormData();
        formData.append('idEstacion', idEstacionGlob);
        formData.append('itemplan', itemplanGlob);
        formData.append('costoTotalPo', costoTotalGlob);
        formData.append('codigo_po', codigo_poGlob);
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
                    //return swal.fire('Exitoso!',data.msj,'success');
                    mostrarNotificacion(2,'success', 'Exitoso!',data.msj);
                }).catch(function(e) {
                    return Swal.insertQueueStep(
                    {
                        icon: "error",
                        title: e.msj
                    });
                });
            }
        }]);
    })
}

function registrarPoPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'saveLiquiPoMo',
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


function liquidarPoMo(){

    

    var formData = new FormData();
     formData.append('itemplan', itemplanGlob);
     formData.append('codigo_po', codigo_poGlob);
     Swal.queue([
        {
            title: "Está seguro de liquidar la po??",
            text: "Asegurese de validar la información",
            icon: 'question',
            confirmButtonText: "SI",
            showCancelButton: true,
            cancelButtonText: 'NO',
            allowOutsideClick: false,
            showLoaderOnConfirm: true,
            preConfirm: function preConfirm()
            {
                return liquidarPoPromise(formData).then(function (data) { 
                    //return swal.fire('Exitoso!',data.msj,'success');
                    mostrarNotificacion(2,'success', 'Exitoso!','Se liquido la PO!');
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

function liquidarPoPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'liquiPoMo',
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
