function exportFormatoSeguimientoRefoCto(component){
    $.ajax({
            type: 'POST',
            url: 'getExcelCargaRefoCto',
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
        text: "Asegurese de seleccionar un archivo de tipo (.xls)",
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

var arrayDataGlob = [];
var arrayItemList = [];
function cargarExcelPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'proCarRefor',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('#contTabla').html(data.tbReporte);
                initExistDataTableLight('tbRegistroRefo');
                $('#tituTbObs1').text(data.titulo1);
                $('#tituTbObs1').css('display', 'block'); 
                $('#tituTbObs2').text(data.titulo2);
                $('#tituTbObs2').css('display', 'block');                 
                //arrayDataGlob = data.jsonDataFileUpd;       
                arrayDataGlob = JSON.parse(data.jsonDataFileUpd);
                arrayItemList = JSON.parse(data.jsonItemList);
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

function cargarFile(){

    if(arrayDataGlob.length == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe haber al menos 1 dato valido para registrar!!');
        return;
    }   
    //console.log(arrayDataGlob);
    var formData = new FormData();     
    formData.append('arrayDetToFormu', JSON.stringify(arrayDataGlob));
    formData.append('arrayItemList', JSON.stringify(arrayItemList));
    console.log('item:'+arrayItemList);
    Swal.queue([
    {
        title: "Está seguro de registrar la información??",
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

}

function registrarPoPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'regSeguiFormuCto',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDataGlob = [];               
                $('#archivo').val(null);
                $('#contTabla').html(null);
                $('#tituTbObs1').text('');
                $('#tituTbObs1').text('');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}