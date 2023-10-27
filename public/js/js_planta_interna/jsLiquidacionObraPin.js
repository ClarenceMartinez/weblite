var itemplanGbl = null;
function openMdlLiquidarObraPin(btn) {
    itemplanGbl = btn.data('itemplan');

    if(itemplanGbl == null || itemplanGbl == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'openMdlLiquidarObraPin',
        data : { itemplan : itemplanGbl }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            $('#contTabLiquidacion').html(data.TabVerticalEstacion);
            modal('mdlLiquidacionObraPin');
        } else {
            return;
        }
    });
}

var idEstacionGlb = null;
function getEstacion(idEstacion) {
    idEstacionGlb = idEstacion;
    console.log("ID: "+idEstacionGlb);
}

function liquidarObra(){
    var comprobar = $('#evidencia').val().length;
    if(comprobar == 0){
        swal.fire('Verificar!','Debe subir un archivo a procesar!!','warning');
        return;
    }

    if(itemplanGbl == null || itemplanGbl == '' || idEstacionGlb == null || idEstacionGlb == '') {
        return;
    }
    var file = $('#evidencia').val()			
    var ext = file.substring(file.lastIndexOf("."));

    // if(ext != ".xls" && ext != ".xlsx"){
    //     swal.fire('Verificar!','Formato de archivo inválido!!','warning');
    //     return;
    // }
    mostrarNotificacion(3, 'question', 'Está seguro de liquidar la obra?', 'Asegurese de validar la información!!')
        .then((result) => {
            if (result.value) {
                var formData = new FormData();
                var files = $('#evidencia')[0].files[0];
                formData.append('file', files);
                formData.append('itemplan', itemplanGbl);
                formData.append('idEstacion', idEstacionGlb);
				console.log(files);
                $.ajax({
                    type  :	'POST',
                    url   :	'liquidarObra',
                    data  :	formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    beforeSend: () => {
                        $('#btnProcesar').attr("disabled", true);
                    }
                }).done(function(data){
                    var data = JSON.parse(data);
                    
                    console.log(data);
                    if(data.error == 0){
                        mostrarNotificacion(2, 'success', 'Se liquidó correctamente', 'Verificar');
                    }else{
                        mostrarNotificacion(1, 'error', data.msj, 'Verificar');
                    }
                    
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    swal.fire('Aviso',errorThrown + '. Estado: ' + textStatus,'error');
                    return;
                });
                
            }
        });
}


function liquidarObraPin(){
    var comprobar = $('#evidenciaPin').val().length;
    if(comprobar == 0){
        swal.fire('Verificar!','Debe subir un archivo a procesar!!','warning');
        return;
    }

    if(itemplanGbl == null || itemplanGbl == '' || idEstacionGlb == null || idEstacionGlb == '') {
        return;
    }
    var file = $('#evidenciaPin').val()			
    var ext = file.substring(file.lastIndexOf("."));
    // if(ext != ".xls" && ext != ".xlsx"){
    //     swal.fire('Verificar!','Formato de archivo inválido!!','warning');
    //     return;
    // }
    mostrarNotificacion(3, 'question', 'Está seguro de liquidar la obra?', 'Asegurese de validar la información!!')
        .then((result) => {
            if (result.value) {
                var formData = new FormData();
                var files = $('#evidenciaPin')[0].files[0];
                formData.append('file', files);
                formData.append('itemplan', itemplanGbl);
                formData.append('idEstacion', idEstacionGlb);
                $.ajax({
                    type  :	'POST',
                    url   :	'liquidarObra',
                    data  :	formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    beforeSend: () => {
                        $('#btnProcesar').attr("disabled", true);
                    }
                }).done(function(data){
                    var data = JSON.parse(data);
                    
                    console.log(data);
                    if(data.error == 0){
                        mostrarNotificacion(2, 'success', 'Se liquidó correctamente', 'Verificar');
                        $('.searchT').click();
                    }else{
                        mostrarNotificacion(1, 'error', data.msj, 'Verificar');
                    }
                    
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    swal.fire('Aviso',errorThrown + '. Estado: ' + textStatus,'error');
                    return;
                });
                
            }
        });
}