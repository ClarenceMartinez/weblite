function exportarFormatoCargaMasivaSolOcCrea(component){
    $.ajax({
            type: 'POST',
            url: 'getExcelFmtAtenMasivaSolOcCreaPan',
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


function procesarFile(){
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
        text: "Asegurese de seleccionar un archivo de tipo (.csv)",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return cargarExcelPromise(formData).then(function (data) { 
                //console.log('1');
                return swal.fire('Exitoso!',data.msj,'success');
            }).catch(function(e) {
                //console.log('2');
                return Swal.insertQueueStep(
                {
                    icon: "error",
                    title: e.msj
                });
            });
        }
    }]);
}

var arrayDataGlob = 0;
function cargarExcelPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'proFiSiroEs',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('#contTabla').html(data.tbObservacion);
                initExistDataTableLight('tablaResultado');
                $('#tituTbObs').text(data.titulo);
                $('#tituTbObs').css('display', 'block');      
                arrayDataGlob = data.total_upd;       
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}


function cargarFile(){

    if(arrayDataGlob == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe tener registros válidos para cargar!!');
        return;
    } 
   
    Swal.queue([
    {
        title: "Está seguro de actualizar??",
        text: "Asegurese de validar la información",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return atenderSolPromise().then(function (data) { 
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

function atenderSolPromise(){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'updSiropeEsta',
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