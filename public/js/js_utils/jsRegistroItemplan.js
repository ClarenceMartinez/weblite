var idFactorMedicionGlb = null;

function getCmbSubProyectoByProyectoReg() {
    var idProyecto = $('#cmbProyecto option:selected').val();

    if(idProyecto == null || idProyecto == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'getSubProyectoByProyecto',
        data : { idProyecto : idProyecto }
    }).done(function(data){
        data = JSON.parse(data);
        $("#cmbItemplanMadre").show();
        $("#cmbItemplanMadre").removeClass('d-none');

		if(data.error == 0) {
            $('#cmbSubProyecto').html(data.cmbSubProyecto); 
            $('#cmbCentral').removeAttr('onchange');
            $('#cmbSubProyecto').removeAttr('onchange');
            $('#cmbInversion').attr('disabled',false);
            //init(); 
            if(idProyecto == 52) {// SI ES FTTH PANGEACO
                $('#cmbSubProyecto').attr('onchange', 'getItemplanMadre();');
                // $('#cmbEecc').attr('disabled',true);     //Clarence
                $('#cmbEecc').removeAttr('disabled');//Clarence
                $("#cmbEecc").trigger('change');    //Clarence
                $("#cmbItemplanMadre").hide();
                $("#cmbItemplanMadre").addClass('d-none');

                // $('#cmbCentral').attr('disabled',true);//Clarence
                $('#cmbCentral').removeAttr('disabled');//Clarence
                $("#cmbCentral").trigger('change');    //Clarence
                $('#cmbCentral').attr('onchange', 'changeCentralRegItem();'); //Clarence
                $('#contCostoMoPro').css('display', 'none');
                $('#contCostoMatPro').css('display', 'none');
                $('#contImpacto').css('display', 'none');
                $('#contDivCau').css('display', 'none');

            }  else {
                $('#contCmbItemMadre').css('display', 'none');
                $('#cmbItemplanMadre').html(null);
                $('#cmbItemplanMadre').removeAttr('onchange');

                $('#contCmbEmpElec').css('display', 'none');
                $('#cmbEmpElec').html(null);

                $('#contIndicador').css('display', 'none');
                //$('#contUip').css('display', 'none');
                $('#contFechaPrevFo').css('display', 'none');               

                $('#txtIndicador').val(null);
                $('#txtUip').val(null);
                $('#fecha_prev_fo').val(null);
                $('#cmbSubProyecto').removeAttr('onchange');
                if(idProyecto == 21) {// CABLEADO DE EDIFICIOS CV
                    $('#contUip').css('display', 'block');
                    $('#contImpacto').css('display', 'none');
                    $('#contDivCau').css('display', 'none');
                    $('#cmbEecc').attr('disabled',false);
                    $('#cmbCentral').attr('disabled',true);
                    $('#contCostoMoPro').css('display', 'none');
                    $('#contCostoMatPro').css('display', 'none');
                } else if (idProyecto == 32){//PLANTA INTERNA
                    $('#contUip').css('display', 'none');
                    $('#contImpacto').css('display', 'none');
                    $('#contDivCau').css('display', 'none');
                    $('#cmbEecc').attr('disabled',true);   
                    $('#cmbCentral').attr('disabled',false);
                    $('#cmbCentral').attr('onchange', 'changeCentralRegItem();');
                    $('#contCostoMoPro').css('display', 'none');
                    $('#contCostoMatPro').css('display', 'none');
                }else if(idProyecto == 54){//PROYECTOS VARIOS
                    $('#cmbSubProyecto').attr('onchange', 'getCmbSubProyectoReforzamientoExpressOption();');
                    $('#contCostoMoPro').css('display', 'block');
                    $('#contCostoMatPro').css('display', 'block');
                    $('#contUip').css('display', 'block');//$('#contUip').css('display', 'none');
                    $('#cmbEecc').attr('disabled',true);    
                    $('#cmbCentral').attr('disabled',true);
                    $('#contImpacto').css('display', 'none');
                    $('#contDivCau').css('display', 'none');
                }else if(idProyecto == 55){//PROYECTOS MANTENIMIENTO
                    $('#cmbSubProyecto').attr('onchange', 'getCmbSubProyectoOption();');
                    $('#contUip').css('display', 'none');
                    $('#contImpacto').css('display', 'block');    
                    $('#contDivCau').css('display', 'block');       
                    $('#cmbDivCau').html(data.cmbDivCayOym);    
					$('#cmbCentral').attr('onchange', 'changeCentralRegItem();');					
                }else{
                    $('#contImpacto').css('display', 'none');
                    $('#contDivCau').css('display', 'none');
                    $('#contUip').css('display', 'none');
                    $('#cmbEecc').attr('disabled',true);    
                    $('#cmbCentral').attr('disabled',true);
                    $('#contCostoMoPro').css('display', 'none');
                    $('#contCostoMatPro').css('display', 'none');
                }
            }
            
        }
    });
}

function getCmbSubProyectoOption(){
    var idSubProyecto = $('#cmbSubProyecto option:selected').val();
    
    if(idSubProyecto == null || idSubProyecto == '') {
        return;
    }
    // alert("Hola");
    if(idSubProyecto    ==  738 ||  idSubProyecto    ==  756){//MANTENIMIENTO SIN OC       
        $('#contUip').css('display', 'none');
        $('#cmbEecc').attr('disabled',false);    
        $('#cmbCentral').attr('disabled',false);
        $('#cmbInversion').attr('disabled',true);
        $('#contCostoMoPro').css('display', 'none');
        $('#contCostoMatPro').css('display', 'none');
    }else if(idSubProyecto    ==  739 || idSubProyecto    ==  755 || idSubProyecto    ==  756 || idSubProyecto    ==  757 || idSubProyecto    ==  759){
        $('#contUip').css('display', 'none');
        $('#cmbEecc').attr('disabled',false);    
        $('#contCostoMoPro').css('display', 'block');
        $('#contCostoMatPro').css('display', 'block');
        $('#cmbInversion').attr('disabled',false);
        $('#cmbCentral').attr('disabled',false);
    }else if(idSubProyecto    ==  747){//REFORZAMIENTO IP MADRE      
        $('#cmbEecc').attr('disabled',false);     
    }

    console.log('idSubProyecto:'+idSubProyecto);

}

function getCmbSubProyectoReforzamientoExpressOption(){
    var idSubProyecto = $('#cmbSubProyecto option:selected').val();
    
    if(idSubProyecto == null || idSubProyecto == '') {
        return;
    }

    if(idSubProyecto    ==  747){//REFORZAMIENTO IP MADRE      
        $('#cmbEecc').attr('disabled',false);
        $('#contCostoMoPro').css('display', 'none');
        $('#contCostoMatPro').css('display', 'none');
        $('#contCmbItemMadre').css('display', 'none');
    }else if(idSubProyecto    ==  748){//REFORZAMIENTO IP HIJO

        $('#contUip').css('display', 'none');
        $('#cmbEecc').attr('disabled',true);    
        $('#cmbCentral').attr('disabled',true);
        $('#cmbInversion').attr('disabled',true);
        $('#contCostoMoPro').css('display', 'none');
        $('#contCostoMatPro').css('display', 'none');

        $.ajax({
            type : 'POST',
            url  : 'getItemplanMadreRefo',
            data : { idSubProyecto : idSubProyecto }
        }).done(function(data){
            data = JSON.parse(data);    
            if(data.error == 0) {
                $('#contCmbItemMadre').css('display', 'block');
                $('#cmbItemplanMadre').html(data.cmbItemMadre);     
                $('#cmbItemplanMadre').attr('onchange', 'selectItemMadreRefo();');          
            } else {
                mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
            }
        });
    }else{
        $('#cmbEecc').attr('disabled',true);
        $('#contCostoMoPro').css('display', 'block');
        $('#contCostoMatPro').css('display', 'block');
        $('#contCmbItemMadre').css('display', 'none');
    }
    console.log('idSubProyecto:'+idSubProyecto);
}

function selectItemMadreRefo(){
    var itemplan_madre = $('#cmbItemplanMadre option:selected').val();
    
    if(itemplan_madre == null || itemplan_madre == '') {
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'getInfoItemMadreToHijoRefo',
        data : { itemplan_madre : itemplan_madre }
    }).done(function(data){
        data = JSON.parse(data);    
        if(data.error == 0) {
            console.log('gaaaaaaaaaaaaaaaaaaaaaaaaaaa');      
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
    
 }



function changeEeccRegItem(){
    var idEECCSelect = $('#cmbEecc option:selected').val();
    objetoDataRegistro.idEmpresaColab = idEECCSelect;
    $.ajax({
        type : 'POST',
        url  : 'getCodInvToCho',
        data : { idEecc : idEECCSelect }
    }).done(function(data){
        data = JSON.parse(data);
        if(data.error == 0) {
            console.log(data.cmbInversion);
            $('#cmbInversion').html(data.cmbInversion);
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}

function changeCentralRegItem(){
    console.log(map);
    var idCentralSelect = $('#cmbCentral option:selected').val();
    objetoDataRegistro.idCentral = idCentralSelect;

    $.ajax({
        type: 'POST',
        url : 'getDataCentralByIdCentral',
        data: { 
                idCentral : idCentralSelect 
              }
        }).done(function(data) {  
                data = JSON.parse(data);
                if(data.error == 0){                 
                    $('#cmbEecc').val(data.idEmpresaColab);
                    $('#cmbEecc').val(data.idEmpresaColab).trigger('change');            
                    $('#cmbInversion').html(data.cmbInversion);
                    objetoDataRegistro.idEmpresaColab = data.idEmpresaColab;
                    objetoDataRegistro.idZonal        = data.idZonal;
                    objetoDataRegistro.idCentral      = data.idCentral;
                    var longitud_ = parseFloat(data.longitud);
                    var latitud_ = parseFloat(data.latitud);
                    $("#txtLatitud").val(latitud_);
			        $("#txtLongitud").val(longitud_);
                    var position_ = new google.maps.LatLng(latitud_, longitud_);
                    if ( marker ) {
                        marker.setPosition(position_);
                    }else{
                        marker = new google.maps.Marker({
                            position: position_, 
                            map: map,                        
                            draggable: false,
                            animation: google.maps.Animation.DROP
                        });
                    }
                    map.setCenter(position_); 
                }else if(data.error == 1){
                    mostrarNotificacion('error','Error','No se inserto el Plan de obra:'+data.msj);
                }
        })
}



function getItemplanMadre() {
    var idSubProyecto = $('#cmbSubProyecto option:selected').val();

    if(idSubProyecto == null || idSubProyecto == '') {
        return;
    }
   
    $.ajax({
        type : 'POST',
        url  : 'getItemplanMadreFactorMed',
        data : { idSubProyecto : idSubProyecto }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            idFactorMedicionGlb = data.idFactorMedicion;
            $('#contCmbItemMadre').css('display', 'block');
            $('#cmbItemplanMadre').html(data.cmbItemMadre);

            //$('#contIndicador').css('display', 'block');
            $('#contUip').css('display', 'block');
            //$('#contFechaPrevFo').css('display', 'block');
            
            $('#contCmbEmpElec').css('display', 'block');
            $('#cmbEmpElec').html(data.cmbEeccElec);
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}

function registrarItemplan() {
    var longitud        = $('#txtLongitud').val();
    var latitud         = $('#txtLatitud').val();
    var nombrePlan      = $('#txtNombrePlan').val();
    var idSubProyecto   = $('#cmbSubProyecto option:selected').val();
    var idProyecto      = $('#cmbProyecto option:selected').val();
    var idFase          = $('#cmbFase option:selected').val();
    var fechaInicio_temp     = $('#fecha_inicio').val();

    var itemplan_m    = $('#cmbItemplanMadre option:selected').val();
    var idEmpresaElec = $('#cmbEmpElec option:selected').val();
    var indicador     = $('#txtIndicador').val();
    var uip           = $('#txtUip').val();
    var fechaPrevFo   = $('#fecha_prev_fo').val();
    var costo_mo      = $('#txtCostoMo').val();
    var costo_mat     = $('#txtCostoMat').val();

    var nuevaPrevFo = fechaPrevFo.split(" ")[0].split("-").reverse().join("-");
    fechaPrevFo = nuevaPrevFo+' 00:00:00';

    var fecha1=fechaInicio_temp;
    var nueva=fecha1.split(" ")[0].split("-").reverse().join("-");
    var fechaInicio=nueva+' 00:00:00';


    var fechaPrevEjec_temp   = $('#fechaPrevEjec').val();
    var fecha2=fechaPrevEjec_temp;
    var nueva2=fecha2.split(" ")[0].split("-").reverse().join("-");
    var fechaPrevEjec=nueva2+' 00:00:00';
	
    var codigoInversion = $('#cmbInversion option:selected').val();
	
	var divCauOyM = $('#cmbDivCau option:selected').val(); 
	
    if(nombrePlan == null || nombrePlan == '') {
        mostrarNotificacion(1, 'warning', 'Ingrese Nombre Plan', '');
        return;
    }

    if(idSubProyecto == null || idSubProyecto == '') {
        mostrarNotificacion(1, 'warning', 'Seleccione un Sub Proyecto', '');
        return;
    }

    // if(longitud == null || longitud == '' || latitud == null || latitud == '' || idFase == null || idFase == '') {
    //     mostrarNotificacion(1, 'warning', 'Seleccione Ubicacion', '');
    //     return;
    // }
   

    if((codigoInversion == null || codigoInversion == '') && idSubProyecto != 738 && idSubProyecto != 756) {
        mostrarNotificacion(1, 'warning', 'Seleccione un codigo de Inversion', '');
        return;
    }

    if(idProyecto   ==  21){//CABLEADO DE EDIFICIOS
        if(uip  ==  null    ||  uip <=  0){
            mostrarNotificacion(1, 'warning', 'Ingrese Cantidad de UIP', '');
            return;
        }
    }

    if(idProyecto   ==  54 && idSubProyecto   !=  747/* ||  idSubProyecto   ==  739*/){//PROYECTO VARIOS Y  MANTENIMIENTO CON OC
        if(costo_mo  ==  null    ||  costo_mo <=  0){
            mostrarNotificacion(1, 'warning', 'Ingrese Costo MO >  S./ 0 para la generacion de Orden de Compra', '');
            return;
        }
        if(costo_mat  ==  null    ||  costo_mat <=  0){
            mostrarNotificacion(1, 'warning', 'Ingrese Costo Mat >  S./ 0', '');
            return;
        }        
        if(uip  ==  null    ||  uip <=  0){
            mostrarNotificacion(1, 'warning', 'Ingrese Cantidad de UIP', '');
            return;
        }
    }
	
	if(idProyecto   ==   55){        
        uip = $('#txtImpacto').val();
        if(uip  ==  null    ||  uip <=  0){
            mostrarNotificacion(1, 'warning', 'Ingrese Impacto', '');
            return;
        }
        if(divCauOyM == null || divCauOyM == ''   ){
            mostrarNotificacion(1, 'warning', 'Seleccione Divicau', '');
            return;
        }else{
            objetoDataRegistro.divcauoym       = divCauOyM;
        }
        
    }

    objetoDataRegistro.longitud           = longitud;
    objetoDataRegistro.latitud            = latitud;
    objetoDataRegistro.nombrePlan         = nombrePlan;
    objetoDataRegistro.idSubProyecto      = idSubProyecto;
    objetoDataRegistro.idProyecto         = idProyecto;
    objetoDataRegistro.idFase             = idFase;
    objetoDataRegistro.fechaInicio        = fechaInicio;
    objetoDataRegistro.fechaPrevEjecucion = fechaPrevEjec;
    objetoDataRegistro.codigoInversion    = codigoInversion;

    objetoDataRegistro.itemplan_m              = itemplan_m;
    objetoDataRegistro.idEmpresaElec           = idEmpresaElec;
    objetoDataRegistro.indicador               = indicador;
    objetoDataRegistro.cantFactorPlanificado   = uip;
    objetoDataRegistro.fechaPrevFo             = fechaPrevFo;
    objetoDataRegistro.costo_unitario_mo       = costo_mo;
    objetoDataRegistro.costo_unitario_mat      = costo_mat;
    objetoDataRegistro.idPqtTipoFactorMedicion = idFactorMedicionGlb;
    $.ajax({
        type : 'POST',
        url  : 'registrarItemplan',
        data : { objJson : JSON.stringify(objetoDataRegistro) }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            mostrarNotificacion(2, 'success', 'SE CREO EL ITEMPLAN: ', data.itemplan);
            // if(alert('Se registró el itemplan : '+data.itemplan)){}
            // else window.location.reload(); 
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}