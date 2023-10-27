var idProyectoGlobal   = null;
var idSubProyectoGlobal   = null;
var itemplanGbl   = null;
var idEstacionGbl = null;
var arrayDataEntidad = [];
var tipoDiseGeneral = null;
function openMdlAgregarEntidad(btn) {
    arrayDataEntidad = [];
    itemplanGbl = btn.data('itemplan');
    idEstacionGbl = btn.data('id_estacion');

    $.ajax({
        type : 'POST',
        url  : 'getTablaEntidad',
        data : { itemplan   : itemplanGbl, 
                 idEstacion : idEstacionGbl }
    }).done(function(data){
        data = JSON.parse(data)

        if(data.error == 0) {
            $('#contTablaEntidad').html(data.tablaEntidad);
            modal('modalAgregarEntidad');
        } else {
            return;
        }
    });
}

function agregarEntidad(btn) {
    var idEntidad = btn.data('id_entidad');
    var cant      = btn.data('cant');
    var jsonEntidad = {};

    if( $('#check_'+cant).prop('checked') ) {
        jsonEntidad.idEntidad = idEntidad;
        jsonEntidad.itemplan  = itemplanGbl;
        jsonEntidad.idEstacion  = idEstacionGbl;

        arrayDataEntidad.splice(arrayDataEntidad.length, 0, jsonEntidad);
        flg_estado = 1;
    } else {
        arrayDataEntidad.forEach(function(data, key) {
            if(data.idEntidad == idEntidad) {
                arrayDataEntidad.splice(key, 1);
            }
        });
        
    }
}

function registrarEntidad() {
    if(arrayDataEntidad.length == 0) {
        mostrarNotificacion(1, 'error', 'Debe Agregar Entidades', 'Verificar');
        return;
    }

    if(idEstacionGbl == null || idEstacionGbl == '' || itemplanGbl == null || itemplanGbl == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'registrarEntidad',
        data : { arrayDataEntidad : arrayDataEntidad,
                 idEstacion       : idEstacionGbl,
                 itemplan         : itemplanGbl }
    }).done(function(data){
        data = JSON.parse(data);
        console.log(data);
        if(data.error == 0) {
            $('#contTablaLicencia_'+itemplanGbl+'_'+idEstacionGbl).html(data.tablaEntidadItemplanEstacion);
            $('.select2').select2();
            $('.date_picker').datepicker(
            {
                orientation: "bottom right",
                todayHighlight: true,
                templates: controls,
                format: 'dd-mm-yyyy'
            });
            modal('modalAgregarEntidad');
            mostrarNotificacion(1, 'success', data.msj, 'Correcto');
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}

var jsonDataLicencia = {};
var itemplanGlbExp = null;
var idEstacionGlbExp = null;
var idGlobExp = null;
function openModalRegExp1(btn) {
    var idEntidad  = btn.data('id_entidad');
    idEstacionGlbExp = btn.data('id_estacion');
    itemplanGlbExp = btn.data('itemplan');
    idGlobExp = btn.data('id');

    $('#archivo').val(null);
    $('#lblarchivo').text('');

    if(idEstacionGlbExp == null || idEstacionGlbExp == '' || idEntidad == null || idEntidad == '' || idGlobExp == null || idGlobExp == '') {
        return;
    }

    var nroExpediente = $('#exp_'+idEstacionGlbExp+'_'+idGlobExp).val();
    var idTipoEntidad = $('#cmbTipo_'+idEstacionGlbExp+'_'+idGlobExp+' option:selected').val();
    var idDistrito    = $('#cmbDistrito_'+idEstacionGlbExp+'_'+idGlobExp+' option:selected').val();
    var fechaInicio   = $('#fechaIn_'+idEstacionGlbExp+'_'+idGlobExp).val();
    var fechaFin      = $('#fechaFin_'+idEstacionGlbExp+'_'+idGlobExp).val();
    console.log("idDistrito: "+idDistrito);
    
    if(nroExpediente == null || nroExpediente == '') {
        mostrarNotificacion(1, 'error', 'Ingresar nro. Expediente', 'Verificar');
        return;
    }

    if(idTipoEntidad == null || idTipoEntidad == '') {
        mostrarNotificacion(1, 'error', 'Seleccionar tipo de entidad.', 'Verificar');
        return;
    }

    if(idDistrito == null || idDistrito == '') {
        mostrarNotificacion(1, 'error', 'Seleccionar Distrito.', 'Verificar');
        return;
    }

    if(fechaInicio == null || fechaInicio == '') {
        mostrarNotificacion(1, 'error', 'Ingresar fecha Inicio.', 'Verificar');
        return;
    }

    if(fechaFin == null || fechaFin == '') {
        mostrarNotificacion(1, 'error', 'Ingresar fecha fin.', 'Verificar');
        return;
    }
    var nueva=fechaInicio.split(" ")[0].split("-").reverse().join("-");
    fechaInicio = nueva+' 00:00:00';

    var nueva=fechaFin.split(" ")[0].split("-").reverse().join("-");
    fechaFin = nueva+' 00:00:00';

    jsonDataLicencia.id            = idGlobExp;
    jsonDataLicencia.itemplan      = itemplanGlbExp;
    jsonDataLicencia.idEstacion    = idEstacionGlbExp;
    jsonDataLicencia.idEntidad     = idEntidad;
    jsonDataLicencia.nroExpediente = nroExpediente;
    jsonDataLicencia.idTipoEntidad = idTipoEntidad;
    jsonDataLicencia.idDistrito    = idDistrito;
    jsonDataLicencia.fechaInicio   = fechaInicio;
    jsonDataLicencia.fechaFin      = fechaFin;
    
    modal('modalRegistrarExpLic');
}

function guardarExpedienteEntidad(){
    var comprobar = $('#archivo').val().length;
    if(comprobar == 0){
        swal.fire('Verificar!','Debe subir un archivo a procesar!!','warning');
        return;
    }
    var file = $('#archivo').val()			
    var ext = file.substring(file.lastIndexOf("."));

    var formData = new FormData();
    var files = $('#archivo')[0].files[0];
    formData.append('file', files);
    formData.append('jsonDataLicencia', JSON.stringify(jsonDataLicencia));

    Swal.queue([
    {
        title: "Está seguro de cargar el achivo??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return regExpLicenciaPromise(formData).then(function (data) { 
                return swal.fire('Verificar','Se actualizó el expediente correctamente','success');
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


function regExpLicenciaPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'registrarExpLicencia',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('#contTablaLicencia_'+jsonDataLicencia.itemplan+'_'+jsonDataLicencia.idEstacion).html(data.tablaEntidadItemplanEstacion);
                $('.select2').select2();
                $('.date_picker').datepicker(
                {
                    orientation: "bottom right",
                    todayHighlight: true,
                    templates: controls,
                    format: 'dd-mm-yyyy'
                });
                modal('modalRegistrarExpLic');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

function uploadFileTerminoLicencia(btn){
    var idEntidad  = btn.data('id_entidad');
    idEstacionGlbExp = btn.data('id_estacion');
    itemplanGlbExp = btn.data('itemplan');
    idGlobExp = btn.data('id');

    $('#archivolf').val(null);
    $('#lblarchivofl').text('');

    jsonDataLicencia.id            = idGlobExp;
    jsonDataLicencia.itemplan      = itemplanGlbExp;
    jsonDataLicencia.idEstacion    = idEstacionGlbExp;
    jsonDataLicencia.idEntidad     = idEntidad;


    modal('modalCierreLicencia');
}

function regFinalizaLicencia(){
    var comprobar = $('#archivolf').val().length;
    if(comprobar == 0){
        swal.fire('Verificar!','Debe subir un archivo a procesar!!','warning');
        return;
    }
    var file = $('#archivolf').val()			
    var ext = file.substring(file.lastIndexOf("."));

    var formData = new FormData();
    var files = $('#archivolf')[0].files[0];
    formData.append('file', files);
    formData.append('jsonDataLicencia', JSON.stringify(jsonDataLicencia));

    Swal.queue([
    {
        title: "Está seguro de cargar el achivo de Termino Licencia??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return regExpTerminoLicenciaPromise(formData).then(function (data) { 
                return swal.fire('Verificar','Se actualizó el expediente correctamente','success');
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

function regExpTerminoLicenciaPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'registrarExpTerminoLicencia',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('#contTablaLicencia_'+jsonDataLicencia.itemplan+'_'+jsonDataLicencia.idEstacion).html(data.tablaEntidadItemplanEstacion);
                $('.select2').select2();
                $('.date_picker').datepicker(
                {
                    orientation: "bottom right",
                    todayHighlight: true,
                    templates: controls,
                    format: 'dd-mm-yyyy'
                });
                modal('modalCierreLicencia');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

var idEntidadCompGlb = null;
var idEstacionCompGlb = null;
var itemplanCompGlb = null;
var idCompGlb = null;
function openModalComprobante(btn) {
    idEntidadCompGlb = btn.data('id_entidad');
    idEstacionCompGlb = btn.data('id_estacion');
    itemplanCompGlb = btn.data('itemplan');
    idCompGlb = btn.data('id');
    estado = btn.data('estado');

    if(idEntidadCompGlb == null || idEntidadCompGlb == ''|| idEstacionCompGlb == null || idEstacionCompGlb == '' ||
       itemplanCompGlb == null || itemplanCompGlb == '' || idCompGlb == null || idCompGlb == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'getTablaComprobanteLic',
        data : {
            idEntidad  : idEntidadCompGlb,
            idEstacion : idEstacionCompGlb,
            itemplan   : itemplanCompGlb,
            id         : idCompGlb
         }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            if(estado == 2) {
                $('#btnComprobante').css('display', 'none');
            } else {
                $('#btnComprobante').css('display', 'block');
            }

            $('#contTablaComprobante').html(data.tablaEntidadItemplanEstacion);
            
            $('#fechaEmision_'+idEstacionCompGlb+'_'+idCompGlb).datepicker(
                {
                    orientation: "bottom right",
                    todayHighlight: true,
                    templates: controls,
                    format: 'dd-mm-yyyy',
                    language: 'es'
                });
            
            modal('modalComprobante');
        } else {
            return;
        }
    });
}

var objComprobante = {};
function validarComprobanteCheck(btn) {
    var flg_tipo = btn.data('flg_tipo');
    var cant     = btn.data('cant');
    var idEstacion = btn.data('idestacion');
    var id = btn.data('id');

    if(flg_tipo == 1) {
        if( $('#checkValida_'+cant).prop('checked') ) {
            objComprobante.flgAccionComp = 1;
            $('#cont_checkPreliq_'+cant).css('display', 'none');
        } else {
            objComprobante.flgAccionComp = null;
            $('#cont_checkPreliq_'+cant).css('display', 'block');
        }
    } else {
        if( $('#checkPreliq_'+cant).prop('checked') ) {
            objComprobante.flgAccionComp = 2;
            $('#cont_checkValida_'+cant).css('display', 'none');
            // $('#nro_comp_'+idEstacion+'_'+id).val('XXXX');
            // $('#nro_comp_'+idEstacion+'_'+id).val('XXXX');
        } else {
            objComprobante.flgAccionComp = null;
            $('#cont_checkValida_'+cant).css('display', 'block');
        }
    }
}

function registrarComprobante() {
    var nroComp      = $('#nro_comp_'+idEstacionCompGlb+'_'+idCompGlb).val();
    var fechaEmision = $('#fechaEmision_'+idEstacionCompGlb+'_'+idCompGlb).val();
    var montoComp    = $('#monto_'+idEstacionCompGlb+'_'+idCompGlb).val();

    var nueva=fechaEmision.split(" ")[0].split("-").reverse().join("-");
    fechaEmision = nueva+' 00:00:00';

    if(nroComp == null || nroComp == '') {
        mostrarNotificacion(1, 'error', 'Debe Agregar nro comprobante', 'Verificar');
        return;
    }

    if(fechaEmision == null || fechaEmision == '') {
        mostrarNotificacion(1, 'error', 'Debe Agregar fecha de emisión', 'Verificar');
        return;
    }

    if(montoComp == null || montoComp == '') {
        mostrarNotificacion(1, 'error', 'Debe el monto', 'Verificar');
        return;
    }

    if(itemplanCompGlb == null || itemplanCompGlb == '' || idEstacionCompGlb == null || idEstacionCompGlb == ''
      || idEntidadCompGlb == null || idEntidadCompGlb == '' || idCompGlb == null || idCompGlb == '') {
        return;
    }

    objComprobante.nroComprobante   = nroComp;
    objComprobante.fechaEmisionComp = fechaEmision;
    objComprobante.montoComp        = montoComp;
    objComprobante.itemplan         = itemplanCompGlb;
    objComprobante.idEstacion       = idEstacionCompGlb;
    objComprobante.idEntidad        = idEntidadCompGlb;
    objComprobante.id               = idCompGlb;

    var comprobante = $('#archivo_comp').val().length;
    if(comprobante == 0){
        swal.fire('Verificar!','Debe subir un archivo a procesar!!','warning');
        return;
    }
    var file = $('#archivo_comp').val()			
    var ext = file.substring(file.lastIndexOf("."));

    swal.fire({
        icon: 'warning',
        title: 'Está seguro de liquidar el comprobante??',
        text: 'Asegurese de validar la información!!',
        showConfirmButton: true,
        confirmButtonText: 'SI',
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false
    }).then((result) => {
        if (result.value) {
            var formData = new FormData();
            var files = $('#archivo_comp')[0].files[0];

            formData.append('file', files);
            formData.append('objComprobante', JSON.stringify(objComprobante));

            $.ajax({
                type  :	'POST',
                url   :	'registrarCompLicencia',
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
                    $('#contTablaLicencia_'+objComprobante.itemplan+'_'+objComprobante.idEstacion).html(data.tablaEntidadItemplanEstacion);
                    $('.select2').select2();
                    $('.date_picker').datepicker(
                    {
                        orientation: "bottom right",
                        todayHighlight: true,
                        templates: controls,
                        format: 'dd-mm-yyyy'
                    });

                    modal('modalComprobante');
                    $('.searchT').click();
                    mostrarNotificacion(1, 'success', 'Se actualizó el comprobante correctamente', 'Verificar');
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

function eliminarEntidad(component) {
    var jsonData  = $(component).data();
    var idEntidad  = jsonData.id_entidad;
    idEstacionGlbExp = jsonData.id_estacion;
    itemplanGlbExp = jsonData.itemplan;
    idGlobExp = jsonData.id;
    var posEntidad = $(component).parent().parent().parent().index();
    var trEntidad = $(component).parent().parent().parent();
    console.log('posEntidad:',posEntidad);
    console.log('trEntidad:',trEntidad);
    if(idEstacionGlbExp == null || idEstacionGlbExp == '' || idEntidad == null || idEntidad == '' || idGlobExp == null || idGlobExp == '') {
        return;
    }
    // tableEntidadGlob.fnDeleteRow(trEntidad);eliminar fila con datable
    // trEntidad.remove(); eliminar fila tabla normal
    var formData = new FormData();
    jsonDataLicencia.id            = idGlobExp;
    jsonDataLicencia.itemplan      = itemplanGlbExp;
    jsonDataLicencia.idEstacion    = idEstacionGlbExp;
    jsonDataLicencia.idEntidad     = idEntidad;
    console.log(jsonDataLicencia);
    formData.append('jsonDataLicencia', JSON.stringify(jsonDataLicencia));
    Swal.queue([
    {
        title: "Está seguro de eliminar la entidad??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return eliminarEntidadPromise(formData).then(function (data) { 
                return swal.fire('Verificar','Se eliminó la entidad correctamente','success');
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

function eliminarEntidadPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'eliminarEntidadLicencia',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('#contTablaLicencia_'+jsonDataLicencia.itemplan+'_'+jsonDataLicencia.idEstacion).html(data.tablaEntidadItemplanEstacion);
                $('.select2').select2();
                $('.date_picker').datepicker(
                {
                    orientation: "bottom right",
                    todayHighlight: true,
                    templates: controls,
                    format: 'dd-mm-yyyy'
                });
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

function openModalEjecEstacion(component){
    var jsonData = $(component).data();
    $('#formEjecDiseno').trigger("reset");
	$('#cmbTipoDiseno').val(tipoDiseGeneral).trigger("change");
    $('#btnEjecutarDiseno').data('id',jsonData.id);
    $('#btnEjecutarDiseno').data('itemplan',jsonData.itemplan);
    $('#btnEjecutarDiseno').data('idEstacion',jsonData.id_estacion);
    $('#titModalEjecDiseno').text('ESTACIÓN : '+jsonData.estacion);
    $('#titModalEjecDiseno').append(
    '<small class="m-0 text-center color-white">'+
        'CODIGO DE OBRA: '+jsonData.itemplan+
    '</small>');
   
    $.ajax({
        type  :	'POST',
        url   :	'getEntidadesForEjecDiseno',
        data  :	{
            itemplan: jsonData.itemplan,
            idEstacion: jsonData.id_estacion
        },
        beforeSend: () => {
            $('body').loading({
                message: 'Espere por favor...'
            });
        }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(idProyectoGlobal ==  52  ||  idProyectoGlobal    ==  21){                 
                $('#divUipDise').show();
                $('#divTipoDise').hide();
                $('#divFormulariosRefCTO').hide(); 
            }else if(idSubProyectoGlobal ==   734   ||  idSubProyectoGlobal ==   741){
                $('#divUipDise').show();
                $('#divFormulariosRefCTO').show(); 
                $('#divTipoDise').hide();
            }else if(idProyectoGlobal ==  3){
                $('#divUipDise').hide();
                $('#divTipoDise').show();
                $('#divFormulariosRefCTO').hide(); 
            }else{
                 $('#divUipDise').hide();
                 $('#divTipoDise').hide();
                 $('#divFormulariosRefCTO').hide(); 
            }             
            $('#txt_uip_dise').val(data.uip);
            $('#selectEntidad').html(data.cmbEntidad);
            $('.select2').select2();
            soloDigitos('classuip');
            modal('modalEjecucionDiseno');
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

function ejecutarDiseno(component){

    var params = $("#formEjecDiseno").serializeArray();//(lee todos los inputs select con name y que no sean disabled)
    var formData = new FormData();
    var chxOTAC = $('#chxOTAC:checked').val();
    var arrayEntidad = $('#selectEntidad').val();
    var uip_dise = $('#txt_uip_dise').val();
	var tipoDisenoB2b = $('#cmbTipoDiseno').val();
    
	var listFormulariosRefCto = [];
	 
	var jsonData = $(component).data();
	
    if(chxOTAC){
        chxOTAC = 1;
        console.log('es verdadero');
    }else{
        chxOTAC = 2;
        console.log('es falso');
    }

    if(arrayEntidad.length == 0){
        var divPadre = $('#selectEntidad').parent();
        var divMsj = divPadre.children().eq(3);
        divMsj.removeClass('valid-feedback invalid-feedback');      
        divMsj.addClass('invalid-feedback');
        divMsj.text('Debe seleccionar una opción.');
        divMsj.css('display','block');
        swal.fire('Verificar!','Debe seleccionar almenos una opción para guardar','warning');
        return;
    }else{
        var divPadre = $('#selectEntidad').parent();
        var divMsj = divPadre.children().eq(3);
        divMsj.removeClass('valid-feedback invalid-feedback');     
        divMsj.addClass('valid-feedback');
        divMsj.text('Correcto!');
        divMsj.css('display','block');
    }

    //console.log('idProyectoGlobal:'+idProyectoGlobal);
    if(idProyectoGlobal ==  52  ||  idProyectoGlobal    ==  21){
        if(uip_dise ==  null    ||  uip_dise    ==  ''  ||  uip_dise    <=  0){//error
            var divPadre = $('#txt_uip_dise').parent();
            var divMsj = divPadre.children().eq(2);
            divMsj.removeClass('valid-feedback invalid-feedback');      
            divMsj.addClass('invalid-feedback');
            divMsj.text('Debe ingresar UIP valido.');
            divMsj.css('display','block');
            swal.fire('Verificar!','Debe Ingresar UIP valido para guardar','warning');
            return;
        }else{
            var divPadre = $('#txt_uip_dise').parent();
            var divMsj = divPadre.children().eq(2);
            divMsj.removeClass('valid-feedback invalid-feedback');     
            divMsj.addClass('valid-feedback');
            divMsj.text('Correcto!');
            divMsj.css('display','block');
        }
    }else if(idSubProyectoGlobal ==   734   ||  idSubProyectoGlobal ==   741){ 
        
        if(uip_dise ==  null    ||  uip_dise    ==  ''  ||  uip_dise    <=  0){//error        
            var divPadre = $('#txt_uip_dise').parent();
            var divMsj = divPadre.children().eq(2);
            divMsj.removeClass('valid-feedback invalid-feedback');      
            divMsj.addClass('invalid-feedback');
            divMsj.text('Debe ingresar UIP valido.');
            divMsj.css('display','block');
            swal.fire('Verificar!','Debe ingresar UIP valido.','warning');
            return;
        }else{
            var divPadre = $('#txt_uip_dise').parent();
            var divMsj = divPadre.children().eq(2);
            divMsj.removeClass('valid-feedback invalid-feedback');     
            divMsj.addClass('valid-feedback');
            divMsj.text('Correcto!');
            divMsj.css('display','block');
        }
        
        for(var i = 1; i <= numFormRefCto; i++) {

            var cto_ajudi       = $('#txt_cto_adjudi_'+i).val();
            var divcau          = $('#txt_divCau_'+i).val();
            var tipo_refo       = $('#txt_tip_refor_'+i).val();
            var do_splitter     = $('#txt_cod_2_splitter_'+i).val();
            var nuevo_splitter  = $('#txt_cod_splitter_cto_'+i).val();
            var nuevo_cod_cto   = $('#txt_nuevo_cod_cto_'+i).val();
            var observacion     = $('#txt_observacion_'+i).val();
            
            if(cto_ajudi ==  null    ||  cto_ajudi    ==  ''){
                var divPadre = $('#txt_cto_adjudi_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');      
                divMsj.addClass('invalid-feedback');
                divMsj.text('Debe ingresar Cto Adjudicado.');
                divMsj.css('display','block');
                swal.fire('Verificar!','Debe ingresar Cto Adjudicado.','warning');
                return;            
            }else{
                var divPadre = $('#txt_cto_adjudi_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');     
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }           
            if(divcau ==  null    ||  divcau    ==  ''){
                var divPadre = $('#txt_divCau_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');      
                divMsj.addClass('invalid-feedback');
                divMsj.text('Debe ingresar Divicau.');
                divMsj.css('display','block');
                swal.fire('Verificar!','Debe ingresar Divicau.','warning');
                return;            
            }else{
                var divPadre = $('#txt_divCau_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');     
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }
            if(tipo_refo ==  null    ||  tipo_refo    ==  ''){           
                var divPadre = $('#txt_tip_refor_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');      
                divMsj.addClass('invalid-feedback');
                divMsj.text('Debe ingresar Tipo Reforzamiento.');
                divMsj.css('display','block');
                swal.fire('Verificar!','Debe ingresar Tipo Reforzamiento.','warning');
                $('.select2').select2();
                return;            
            }else{
                var divPadre = $('#txt_tip_refor_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');     
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
                $('.select2').select2();
            }   
            if(do_splitter ==  null    ||  do_splitter    ==  ''){
                var divPadre = $('#txt_cod_2_splitter_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');      
                divMsj.addClass('invalid-feedback');
                divMsj.text('Debe ingresar Nuevo Codigo 2do Splitter.');
                divMsj.css('display','block');
                swal.fire('Verificar!','Debe ingresar Nuevo Codigo 2do Splitter.','warning');
                return;            
            }else{
                var divPadre = $('#txt_cod_2_splitter_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');     
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }      
            /*      
            if(nuevo_splitter ==  null    ||  nuevo_splitter    ==  ''){
                var divPadre = $('#txt_cod_splitter_cto_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');      
                divMsj.addClass('invalid-feedback');
                divMsj.text('Debe ingresar Nuevo Codigo Splitter (Nuevo CTO).');
                divMsj.css('display','block');
                swal.fire('Verificar!','Debe ingresar Nuevo Codigo Splitter (Nuevo CTO).','warning');
                return;            
            }else{
                var divPadre = $('#txt_cod_splitter_cto_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');     
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }   
            if(nuevo_cod_cto ==  null    ||  nuevo_cod_cto    ==  ''){
                var divPadre = $('#txt_nuevo_cod_cto_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');      
                divMsj.addClass('invalid-feedback');
                divMsj.text('Debe ingresar Codigo Nuevo CTO.');
                divMsj.css('display','block');
                swal.fire('Verificar!','Debe ingresar Codigo Nuevo CTO.','warning');
                return;            
            }else{
                var divPadre = $('#txt_nuevo_cod_cto_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');     
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }*/
            if(observacion ==  null    ||  observacion    ==  ''){
                var divPadre = $('#txt_observacion_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');      
                divMsj.addClass('invalid-feedback');
                divMsj.text('Debe ingresar Una Observacion');
                divMsj.css('display','block');
                swal.fire('Verificar!','Debe ingresar Una Observacion','warning');
                return;            
            }else{
                var divPadre = $('#txt_observacion_'+i).parent();
                var divMsj = divPadre.children().eq(2);
                divMsj.removeClass('valid-feedback invalid-feedback');     
                divMsj.addClass('valid-feedback');
                divMsj.text('Correcto!');
                divMsj.css('display','block');
            }  

            var formRefCto = {};
            formRefCto['cto_ajudi']  = cto_ajudi;
            formRefCto['divcau']  = divcau;
            formRefCto['tipo_refo']  = tipo_refo;
            formRefCto['do_splitter']  = do_splitter;
            formRefCto['nuevo_splitter']  = nuevo_splitter;
            formRefCto['nuevo_cod_cto']  = nuevo_cod_cto;
            formRefCto['observacion']  = observacion;
            
            listFormulariosRefCto.push(formRefCto);
        }
       
    }else if(idProyectoGlobal ==  3){
        if(tipoDisenoB2b ==  null    ||  tipoDisenoB2b    ==  ''  ||  tipoDisenoB2b    <=  0){//error
            var divPadre = $('#tipoDisenoB2b').parent();
            var divMsj = divPadre.children().eq(2);
            divMsj.removeClass('valid-feedback invalid-feedback');      
            divMsj.addClass('invalid-feedback');
            divMsj.text('Debe Seleccionar Tipo Diseno.');
            divMsj.css('display','block');
            swal.fire('Verificar!','Debe  Seleccionar Tipo Diseno valido para guardar','warning');
            return;
        }else{
            var divPadre = $('#tipoDisenoB2b').parent();
            var divMsj = divPadre.children().eq(2);
            divMsj.removeClass('valid-feedback invalid-feedback');     
            divMsj.addClass('valid-feedback');
            divMsj.text('Correcto!');
            divMsj.css('display','block');
        }       
    }

    var comprobar = $('#archivo2').val().length;
    if(comprobar == 0){
        var divPadre = $('#archivo2').parent();
        var divMsj = divPadre.children().eq(2);
        divMsj.removeClass('valid-feedback invalid-feedback');
        $('#archivo2').removeClass('is-valid is-invalid');
        $('#archivo2').addClass('form-control is-invalid');
        divMsj.addClass('invalid-feedback');
        divMsj.text('Seleccione un archivo.');
        divMsj.css('display','block');
        swal.fire('Verificar!','Debe subir un archivo para ejecutar la estación!!','warning');
        return;
    }
    var file = $('#archivo2')[0].files[0];

    formData.append('file', file);
    formData.append('chxOTAC', chxOTAC);
    formData.append('arrayEntidad', JSON.stringify(arrayEntidad));
    formData.append('idEstacion', jsonData.idEstacion);
    formData.append('itemplan', jsonData.itemplan);
    formData.append('uip', uip_dise);
    formData.append('id', jsonData.id);
	formData.append('tipoDisenoB2b', tipoDisenoB2b);
	formData.append('formulariosRefCto', JSON.stringify(listFormulariosRefCto));     
    Swal.queue([
    {
        title: "Está seguro de ejecutar la estación??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return ejecutarDisenoPromise(formData).then(function (data) { 
                return swal.fire({
                            icon: 'success',
                            title: 'Exitoso!',
                            text: data.msj,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            showCancelButton: false,
                            allowOutsideClick: false
                        }).then((result) => {
                           // location.reload();
                           $('.searchT').click();
                        });
                
                // swal.fire('Exitoso!',data.msj,'success');
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

function ejecutarDisenoPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'ejecutarDiseno',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('#contTablaDiseno').html(data.tbDiseno);
                modal('modalEjecucionDiseno');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

function openModalPorcentaje(component){
    var jsonData = $(component).data();
    $.ajax({
        type  :	'POST',
        url   :	'getEstacionesForLiquidacion',
        data  :	{
            itemplan: jsonData.itemplan
        },
        beforeSend: () => {
            $('body').loading({
                message: 'Espere por favor...'
            });
        }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            $('#contPanel').html(data.htmlEstaciones);
            $('.select2').select2();
            modal('modalEnObraPreliqui');
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

function ingresarPorcentajeLiqui(component){
    var jsonData = $(component).data();
    var porcentaje = $(component).val();
    var formData = new FormData();
    formData.append('idEstacion', jsonData.idestacion);
    formData.append('itemplan', jsonData.itemplan);
    formData.append('porcentaje', porcentaje);

    Swal.queue([
    {
        title: "Está seguro de actualizar el porcentaje??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return ingresarPorcentajeLiquiPromise(formData).then(function (data) { 
                return swal.fire({
                            icon: 'success',
                            title: 'Exitoso!',
                            text: data.msj,
                            showConfirmButton: true,
                            confirmButtonText: 'OK!',
                            showCancelButton: false,
                            allowOutsideClick: false
                        }).then((result) => {
                            //location.reload();
                        });
                
                // swal.fire('Exitoso!',data.msj,'success');
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

function ingresarPorcentajeLiquiPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'ingresarPorcentajeLiqui',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
               // $('#contPanel').html(data.htmlEstaciones);
               //$('#contTablaObraPreliqui').html(data.htmlEstaciones);               
               //$('.select2').select2();
               $('.searchT').click();
               resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}


function openModalEvidencia(component){
    var jsonData = $(component).data();
    $('#formSubirEviLiqui').trigger("reset");
    $(".invalid-feedback").css('display','none');
    $(".valid-feedback").css('display','none');
    $('.is-valid').removeClass("is-valid");
    $('.is-invalid').removeClass("is-invalid");

    $('#btnRegEvi').data('itemplan', jsonData.itemplan);
    $('#btnRegEvi').data('idestacion', jsonData.idestacion);
    $('#btnRegEvi').data('desc_estacion', jsonData.desc_estacion);
    $('#titModalSubirEvidencia').text('CARGA DE EVIDENCIAS ESTACIÓN '+jsonData.desc_estacion);
    $('#titModalSubirEvidencia').append(
    '<small class="m-0 text-center color-white">'+
        'ItemPlan: '+jsonData.itemplan+
    '</small>');

    modal('modalSubirEvidencia');
}

function registrarEviForLiqui(component){
    var jsonData = $(component).data();
    var params = $("#formSubirEviLiqui").serializeArray();//(lee todos los inputs select con name y que no sean disabled)

    var formData = new FormData();

    var comprobar1 = $('#archivo_reflec').val().length;
    if(comprobar1 == 0){
        var divPadre = $('#archivo_reflec').parent();
        var divMsj = divPadre.children().eq(2);
        divMsj.removeClass('valid-feedback invalid-feedback');
        $('#archivo_reflec').removeClass('is-valid is-invalid');
        $('#archivo_reflec').addClass('form-control is-invalid');
        divMsj.addClass('invalid-feedback');
        divMsj.text('Seleccione un archivo.');
        divMsj.css('display','block');
        swal.fire('Verificar!','Debe subir el pdf de pruebas reflectrométricas para guardar!!','warning');
        return;
    }else{
        var divPadre = $('#archivo_reflec').parent();
        var divMsj = divPadre.children().eq(2);
        divMsj.removeClass('valid-feedback invalid-feedback');
        $('#archivo_reflec').removeClass('is-valid is-invalid');
        $('#archivo_reflec').addClass('form-control is-valid');
        divMsj.addClass('valid-feedback');
        divMsj.text('Correcto.');
        divMsj.css('display','block');
    }

    var comprobar2 = $('#archivo_perfil').val().length;
    if(comprobar2 == 0){
        var divPadre = $('#archivo_perfil').parent();
        var divMsj = divPadre.children().eq(2);
        divMsj.removeClass('valid-feedback invalid-feedback');
        $('#archivo_perfil').removeClass('is-valid is-invalid');
        $('#archivo_perfil').addClass('form-control is-invalid');
        divMsj.addClass('invalid-feedback');
        divMsj.text('Seleccione un archivo.');
        divMsj.css('display','block');
        swal.fire('Verificar!','Debe subir el pdf de perfil para guardar!!','warning');
        return;
    }else{
        var divPadre = $('#archivo_perfil').parent();
        var divMsj = divPadre.children().eq(2);
        divMsj.removeClass('valid-feedback invalid-feedback');
        $('#archivo_perfil').removeClass('is-valid is-invalid');
        $('#archivo_perfil').addClass('form-control is-valid');
        divMsj.addClass('valid-feedback');
        divMsj.text('Correcto.');
        divMsj.css('display','block');
    }

    if(idProyectoGlobal !=  21){
        var comprobar3 = $('#archivo_hgu').val().length;
        if(comprobar3 == 0){
            var divPadre = $('#archivo_hgu').parent();
            var divMsj = divPadre.children().eq(2);
            divMsj.removeClass('valid-feedback invalid-feedback');
            $('#archivo_hgu').removeClass('is-valid is-invalid');
            $('#archivo_hgu').addClass('form-control is-invalid');
            divMsj.addClass('invalid-feedback');
            divMsj.text('Seleccione un archivo.');
            divMsj.css('display','block');
            swal.fire('Verificar!','Debe subir el zip de pruebas HGU para guardar!!','warning');
            return;
        }else{
            var divPadre = $('#archivo_hgu').parent();
            var divMsj = divPadre.children().eq(2);
            divMsj.removeClass('valid-feedback invalid-feedback');
            $('#archivo_hgu').removeClass('is-valid is-invalid');
            $('#archivo_hgu').addClass('form-control is-valid');
            divMsj.addClass('valid-feedback');
            divMsj.text('Correcto.');
            divMsj.css('display','block');
        }
        var file4 = $('#archivo_hgu')[0].files[0];
        formData.append('archivo_hgu', file4);
    }
    var file1 = $('#archivo_reflec')[0].files[0];
    var file2 = $('#archivo_perfil')[0].files[0];
	var file3 = $('#archivo_otros')[0].files[0];
    
    formData.append('archivo_reflec', file1);
    formData.append('archivo_perfil', file2);
	formData.append('archivo_otros', file3);    
    formData.append('itemplan', jsonData.itemplan);
    formData.append('idEstacion', jsonData.idestacion);
    formData.append('desc_estacion', jsonData.desc_estacion);


    Swal.queue([
    {
        title: "Está seguro de guardar las evidencias??",
        text: "Asegúrese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return registrarEviForLiquiPromise(formData).then(function (data) { 
                return swal.fire({
                            icon: 'success',
                            title: 'Exitoso!',
                            text: data.msj,
                            showConfirmButton: true,
                            confirmButtonText: 'OK!',
                            showCancelButton: false,
                            allowOutsideClick: false
                        }).then((result) => {
                            //location.reload();
                            $('.searchT').click();
                        });
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

function registrarEviForLiquiPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'ingresarEvidenciaLiqui',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('#contPanel').html(data.htmlEstaciones);
                $('.select2').select2();
                modal('modalSubirEvidencia');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}


function initSendVali(){
    $('.sendVali').click(function(e){
 
        var jsonData = $(this).data();
    
        var comprobar = $('#fileEvidencia').val().length;
        if(comprobar == 0){
            swal.fire('Verificar!','Debe adjuntar el expediente validar propuesta!!','warning');
            return;
        }
    
        var file = $('#fileEvidencia')[0].files[0];
        var formData = new FormData();
        formData.append('file', file);
        formData.append('itemplan', jsonData.itemplan);
        formData.append('idEstacion', jsonData.idestacion);
        formData.append('codigo_po', jsonData.codigo_po);
        formData.append('estaciondesc', jsonData.estaciondesc);
        formData.append('costo_total', jsonData.costo_total);
        formData.append('costo_inicial', jsonData.tot_pqt);
        formData.append('costo_adicional', jsonData.tot_padic)
    
        Swal.queue([
            {
                title: "Está seguro de enviar a validar la propuesta??",
                text: "Asegúrese de validar la información!!",
                icon: 'question',
                confirmButtonText: "SI",
                showCancelButton: true,
                cancelButtonText: 'NO',
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: function preConfirm()
                {
                    return sendValidatePartidasTerminadoPromise(formData).then(function (data) { 
                        return swal.fire({
                                    icon: 'success',
                                    title: 'Exitoso!',
                                    text: data.msj,
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK!',
                                    showCancelButton: false,
                                    allowOutsideClick: false
                                }).then((result) => {
                                   // location.reload();
                                   $('.searchT').click();
                                });
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


    $('.sendValiNoPqt').click(function(e){
 
        var jsonData = $(this).data();
    
        var comprobar = $('#fileEvidencia').val().length;
        if(comprobar == 0){
            swal.fire('Verificar!','Debe adjuntar el expediente validar propuesta!!','warning');
            return;
        }
    
        var file = $('#fileEvidencia')[0].files[0];
        var formData = new FormData();
        formData.append('file', file);
        formData.append('itemplan', jsonData.itemplan);
        formData.append('costo_total', jsonData.costo_total);
    
        Swal.queue([
            {
                title: "Está seguro de enviar a validar la propuesta??",
                text: "Asegúrese de validar la información!!",
                icon: 'question',
                confirmButtonText: "SI",
                showCancelButton: true,
                cancelButtonText: 'NO',
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: function preConfirm()
                {
                    return sendValidatePartidasTerminadoPromiseNoPqt(formData).then(function (data) { 
                        return swal.fire({
                                    icon: 'success',
                                    title: 'Exitoso!',
                                    text: data.msj,
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK!',
                                    showCancelButton: false,
                                    allowOutsideClick: false
                                }).then((result) => {
                                   // location.reload();
                                   $('.searchT').click();
                                });
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
	
	$('.getRechazado2Bucles').click(function(e){
		console.log('aaa');
		var item       = $(this).attr('data-item');
		var idEstacion = $(this).attr('data-esta');

		$.ajax({
			type : 'POST',
			url  : 'getInfoRech2Bucles',
			data : { itemplan   : item,
					 idEstacion : idEstacion}
		}).done(function(data){
			data = JSON.parse(data);
			if(data.error == 0) { 
			console.log('bbbb');
				$('#contTablaRechazo').html(data.tablaRechazado);//reutilizamos el modal
				modal('modalRechazado');//reutilizamos el modal
			} else {
				mostrarNotificacion(1,'error', data.msj, 'error');
			}
		})
	});
    
}



function sendValidatePartidasTerminadoPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'sendValidatePartAdic',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

function sendValidatePartidasTerminadoPromiseNoPqt(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'sendValNoPqt',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

//nuevo de consulta 

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

var arrayKitPartida = [];
var montoTotal = 0;
var objDetallePo = {};
var objPo = {};
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
                           // modal('modalEstaciones');
                           $('.searchT').click();
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
				//modal('modalEstaciones');
				resolve(data);
                $('.searchT').click();
			} else {
				reject(data);
			}
		});
	});
}


function liquidacionByEsta(itemPlan, estacion) {
    console.log(itemPlan+'descargando...'+estacion);
    $.ajax({
        type: 'POST',
        dataType: "JSON",
        'url': 'downloadLiquiEsta',
        data: {itemPlan: itemPlan, 
                estacion    :   estacion}
    }).done(function (data) {
        console.log(data);
        if (data.path == '1') {
            location.href = 'liquidacion_download_by_esta?' + 'itemPlan=' + itemPlan + '&&estacion=' + estacion;
        } else {
            mostrarNotificacion(1, 'warning', 'Mensaje', 'Sin datos para descargar');
        }

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

function openModalQuiebreCV(component){
    var jsonData = $(component).data();
    $('#formQuiebreCV').trigger("reset");    
    var subtitulo = $('#titModalQuiebreCV').children().eq(0);
    subtitulo.text('ItemPlan: '+jsonData.itemplan);
    $('#btnQuiebreSave').data('itemplan',jsonData.itemplan);
    $(".invalid-feedback").css('display','none');
    $(".valid-feedback").css('display','none');
    $('.is-valid').removeClass("is-valid");
    $('.is-invalid').removeClass("is-invalid");

    modal('modQuiebreComercial');
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
            }else if(val['name'] == 'selectEstadoUpd'){
                msj = 'Debe seleccionar un estado!!';
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(3);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#select2-'+val['name']+'-container').removeClass('is-valid is-invalid');
                $('#select2-'+val['name']+'-container').addClass('form-control is-invalid');
                divMsj.addClass('invalid-feedback');
                divMsj.text('Seleccione un estado.');
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
            }else if(val['name'] == 'selectEstadoUpd'){
                var divPadre = $('#'+val['name']).parent();
                var divMsj = divPadre.children().eq(3);
                divMsj.removeClass('valid-feedback invalid-feedback');
                $('#select2-'+val['name']+'-container').removeClass('is-valid is-invalid');
                $('#select2-'+val['name']+'-container').addClass('form-control is-valid');
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
   
    var file = $('#archivo3')[0].files[0];
    formData.append('file', file);
    formData.append('itemplan', jsonData.itemplan);
    Swal.queue([
    {
        title: "Está seguro de actualizar el itemplan??",
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
                //$('#contTabla').html(data.tbConsulta);
                //initDataTable('tbPlanObra',2);
                modal('modalCancelarIP');
                $('.searchT').click();
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}


function getLogSeguimientoCV(component){
    var jsonData = $(component).data();
    $('#formSeguimiento').trigger("reset");    
    $('#btnSeguimiento').data('itemplan',jsonData.itemplan);
    $(".invalid-feedback").css('display','none');
    $(".valid-feedback").css('display','none');
    $('.is-valid').removeClass("is-valid");
    $('.is-invalid').removeClass("is-invalid");

    $.ajax({
        type: 'POST',
        url: 'getLogSeguimientoCVDet',
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
            console.log('all ok');
            //$('#selectMotivoSegui').html(data.cmbMotivoSegui);
            $('#contTbSeguimiento').html(data.tbLog);
			//$('#ctnButtonLogSegui').html(data.btnSaveSegui);
            //$('#formSeguimiento').children().eq(0).css('display',data.style);
            //$('#formSeguimiento').children().eq(1).css('display',data.style);			
            modal('modalRegSeguimientoCV');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    }); 
}

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
            url   :	'regLogSeguiCVDet',
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
                $('#contSeguimientoCV').html(data.tbLog);
                $('#formSeguimiento').trigger("reset");
                initExistDataTableLight('tb_log_segui_cv');
                modal('modalRegSeguimientoCV');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}

function getLogSeguimientoB2b(component){
    var jsonData = $(component).data();
    $('#formSeguimiento').trigger("reset");    
    $('#btnSeguimiento').data('itemplan',jsonData.itemplan);
    $(".invalid-feedback").css('display','none');
    $(".valid-feedback").css('display','none');
    $('.is-valid').removeClass("is-valid");
    $('.is-invalid').removeClass("is-invalid");

    $.ajax({
        type: 'POST',
        url: 'getLogSeguimientoB2bDet',
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
            console.log('all ok');
            //$('#selectMotivoSegui').html(data.cmbMotivoSegui);
            $('#contTbSeguimiento').html(data.tbLog);
			//$('#ctnButtonLogSegui').html(data.btnSaveSegui);
            //$('#formSeguimiento').children().eq(0).css('display',data.style);
            //$('#formSeguimiento').children().eq(1).css('display',data.style);			
            modal('modalRegSeguimientoCV');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    }); 
}

function getLogSeguimientoReforzamiento(component){
    var jsonData = $(component).data();
    $('#formSeguimiento').trigger("reset");    
    $('#btnSeguimiento').data('itemplan',jsonData.itemplan);
    $(".invalid-feedback").css('display','none');
    $(".valid-feedback").css('display','none');
    $('.is-valid').removeClass("is-valid");
    $('.is-invalid').removeClass("is-invalid");

    $.ajax({
        type: 'POST',
        url: 'getLogSeguimientoReforzaDet',
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
            console.log('all ok');
            //$('#selectMotivoSegui').html(data.cmbMotivoSegui);
            $('#contTbSeguimiento').html(data.tbLog);
			//$('#ctnButtonLogSegui').html(data.btnSaveSegui);
            //$('#formSeguimiento').children().eq(0).css('display',data.style);
            //$('#formSeguimiento').children().eq(1).css('display',data.style);			
            modal('modalRegSeguimientoCV');
        } else {
            mostrarNotificacion(1,'error', 'Aviso', data.msj);
        }
    }).always(() => {
        $('body').loading('destroy');
    }); 
}

function saveQuiebreCV(component){
    var jsonData = $(component).data();
    var params = $("#btnQuiebreSave").serializeArray();//(lee todos los inputs select con name y que no sean disabled)
    console.log(jsonData);
    console.log(params);
    
    var formData = new FormData();     
   
    var file = $('#archivo4')[0].files[0];
    formData.append('file', file);
    formData.append('itemplan', jsonData.itemplan);
    formData.append('itemplanGen', jsonData.itemplanGen);
    Swal.queue([
    {
        title: "Está seguro de actualizar el itemplan??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return saveQuiebrePromise(formData).then(function (data) { 
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

function saveQuiebrePromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'saveQuiebreCVR',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                //$('#contTabla').html(data.tbConsulta);
                //initDataTable('tbPlanObra',2);
                modal('modQuiebreComercial');
                $('.searchT').click();
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}
 
var jsonFormRefoCto = [];
var numFormRefCto = 1;
function addFormToReforzamientoCto(){     
    for(var i = 1; i <= numFormRefCto; i++) {
        $("#form_refo_"+i).removeClass("show");
        $('#titulo_form_'+numFormRefCto).addClass( "collapsed" );
    }
    numFormRefCto = numFormRefCto+1;
   $('#accordionExample').append(getHtmlCardFormRefCto(numFormRefCto)); 
   $('.select2').select2();
}
 
function deleteFormToReforzamientoCto(){
    if(numFormRefCto > 1){
        $('#card_form_ref_'+numFormRefCto).remove();
        numFormRefCto = numFormRefCto-1;
    }else{
        swal.fire('Verificar!','No hay formularios a eliminar!!','warning');
    }
}

function getHtmlCardFormRefCto(num_form){
    var html = '<div class="card" id="card_form_ref_'+num_form+'">'+
                    '<div class="card-header" id="headingOne_'+num_form+'">'+
                        '<a href="javascript:void(0);" class="card-title" id="titulo_form_'+num_form+'" data-toggle="collapse" data-target="#form_refo_'+num_form+'" aria-expanded="true" aria-controls="form_refo_'+num_form+'">'+
                            'Formulario Reforzamiento CTO #'+num_form+''+
                            '<span class="ml-auto">'+
                                '<span class="collapsed-reveal">'+
                                    '<i class="fal fa-minus-circle text-danger"></i>'+
                                '</span>'+
                                '<span class="collapsed-hidden">'+
                                    '<i class="fal fa-plus-circle text-success"></i>'+
                                '</span>'+
                            '</span>'+
                        '</a>'+
                    '</div>'+
                    '<div id="form_refo_'+num_form+'" class="collapse show" aria-labelledby="headingOne_'+num_form+'" data-parent="#accordionExample">'+
                        '<div class="card-body">'+
                            '<div class="form-row">'+
                                '<div class="col-md-6 mb-3">'+                                
                                        '<label class="form-label" for="txt_cto_adjudi_'+num_form+'">CTO Adjudicado<span class="text-danger">*</span></label>'+
                                        '<input type="text" class="form-control" id="txt_cto_adjudi_'+num_form+'" placeholder="Ingrese CTO Ajudicado" required>'+
                                        '<div class="invalid-feedback">'+
                                            'CTO Adjudicado'+
                                        '</div>'+                                       
                                '</div>'+
                                '<div class="col-md-6 mb-3">'+                                
                                        '<label class="form-label" for="txt_divCau_'+num_form+'">Divicau<span class="text-danger">*</span></label>'+
                                        '<input type="text" class="form-control" id="txt_divCau_'+num_form+'" placeholder="Ingrese Divicau" required>'+
                                        '<div class="invalid-feedback">'+
                                            'Divicau'+
                                        '</div>'+                                      
                                '</div>'+
                                '<div class="col-md-6 mb-3">'+                                
                                        '<label class="form-label" for="txt_tip_refor_'+num_form+'">Tipo de Reforzamiento<span class="text-danger">*</span></label>'+
                                        '<select id="txt_tip_refor_'+num_form+'"  class="form-label select2">'+
                                            '<option value=""></option>'+
                                            '<option value="NUEVO CTO">NUEVO CTO</option>'+
                                            '<option value="2DO SPLITTER">2DO SPLITTER</option>'+
											'<option value="NO REQUIERE">NO REQUIERE</option>'+
                                        '</select>'+
                                        '<div class="invalid-feedback">'+
                                            'Tipo de Reforzamiento'+
                                        '</div>'+                                    
                                '</div>'+
                                '<div class="col-md-6 mb-3">'+                                
                                        '<label class="form-label" for="txt_cod_2_splitter_'+num_form+'">Reforzamiento CTO Final<span class="text-danger">*</span></label>'+
                                        '<input type="text" class="form-control" id="txt_cod_2_splitter_'+num_form+'" placeholder="Ingrese CTO Final" required>'+
                                        '<div class="invalid-feedback">'+
                                            'Nuevo Codigo 2do Splitter'+
                                        '</div>'+                                       
                                '</div>'+ 
                                '<!--<div class="col-md-6 mb-3">'+                                
                                        '<label class="form-label" for="txt_cod_splitter_cto_'+num_form+'">Nuevo Codigo Splitter (NUEVO CTO)<span class="text-danger">*</span></label>'+
                                        '<input type="text" class="form-control" id="txt_cod_splitter_cto_'+num_form+'" placeholder="Ingrese Codigo Splitter (nuevo cto)" required>'+
                                        '<div class="invalid-feedback">'+
                                            'Nuevo Codigo Splitter (NUEVO CTO)'+
                                        '</div>'+                                       
                                '</div>'+
                                '<div class="col-md-6 mb-3">'+                                
                                        '<label class="form-label" for="txt_nuevo_cod_cto_'+num_form+'">Nuevo Codigo CTO<span class="text-danger">*</span></label>'+
                                        '<input type="text" class="form-control" id="txt_nuevo_cod_cto_'+num_form+'" placeholder="Ingrese Codigo Nuevo CTO" required>'+
                                        '<div class="invalid-feedback">'+
                                            'Nuevo Codigo CTO'+
                                        '</div>'+                                       
                                '</div>-->'+
                                '<div class="col-md-6 mb-3">'+                                
                                        '<label class="form-label" for="txt_observacion_'+num_form+'">Observacion<span class="text-danger">*</span></label>'+
                                        '<input type="text" class="form-control" id="txt_observacion_'+num_form+'" placeholder="Ingrese Observacion" required>'+
                                        '<div class="invalid-feedback">'+
                                            'Observacion'+
                                        '</div>'+                                       
                                '</div>'+                                                                            
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>';
                
        return html;
}

function instaladoReforza(component){
    var jsonData = $(component).data();    
    var formData = new FormData();   
    formData.append('idSeguimiento', jsonData.id);
    Swal.queue([
    {
        title: "Está seguro de actualizar la situacion a INSTALADO??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return instaladoReforzaPromise(formData).then(function (data) { 
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

function instaladoReforzaPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'saveInstaladoRefor',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                $('.searchT').click();
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}
