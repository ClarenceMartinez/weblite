var itemplanGbl = null;
function validarItemplanPin(btn) {
    itemplanGbl = btn.data('itemplan');
    var has_sirope = btn.data('sirope');
    var texto_alerta_sirope = 'La OT no se encuentra en estado 4 - Con Datos Permanentes, Desea generar la Solicitud de Certificacion?';
    if(has_sirope==1){
        texto_alerta_sirope = 'Se generará la solicitud de certificación!';
    }

    if(itemplanGbl == null || itemplanGbl == '') {
        return;
    }

    Swal.fire({        
        title: 'Validar Itemplan',
        icon: 'question',
        html: '<div class="row form-group">'+
                        '<div class="col-md-12">'+
                            '<label style="color: red;font-weight: 700;" id="adver_txt"></label>'+
                            '<label id="adver_txt_n"></label>'+                              
                        '</div>'+
                    '</div>',
        showCancelButton: true,        
        confirmButtonText: 'Si, Validar!',
        cancelButtonClass: 'btn btn-secondary',
        allowOutsideClick: false,
        onOpen: function () {         
            if(has_sirope==1){
                $('#adver_txt_n').html(texto_alerta_sirope);        
            }else{
                $('#adver_txt').html(texto_alerta_sirope);        
            }
                                   
        }
        }).then((result) => {
            if (result.value) {
                var formData = new FormData();
                formData.append('itemplan', itemplanGbl);

                $.ajax({
                    type  :	'POST',
                    url   :	'validarObraUtils',
                    data  :	formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    beforeSend: () => {
                        $('#btnProcesar').attr("disabled", true);
                    }
                }).done(function(data){
                    var data = JSON.parse(data);
                    
                 //   console.log(data);
                    if(data.error == 0){
                        mostrarNotificacion(2, 'success', 'Se validó correctamente', 'Verificar');
                    }else{
                        mostrarNotificacion(1, 'error', data.msj, 'Verificar');
                    }
                    
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    swal.fire('Aviso',errorThrown + '. Estado: ' + textStatus,'error');
                    return;
                });
                
            }

        });
    
    /*
    mostrarNotificacion(3, 'question', 'Está seguro de validar la obra?', texto_alerta_sirope)
        .then((result) => {
            if (result.value) {
                var formData = new FormData();
                formData.append('itemplan', itemplanGbl);

                $.ajax({
                    type  :	'POST',
                    url   :	'validarObraUtils',
                    data  :	formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    beforeSend: () => {
                        $('#btnProcesar').attr("disabled", true);
                    }
                }).done(function(data){
                    var data = JSON.parse(data);
                    
                 //   console.log(data);
                    if(data.error == 0){
                        mostrarNotificacion(2, 'success', 'Se validó correctamente', 'Verificar');
                    }else{
                        mostrarNotificacion(1, 'error', data.msj, 'Verificar');
                    }
                    
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    swal.fire('Aviso',errorThrown + '. Estado: ' + textStatus,'error');
                    return;
                });
                
            }
        });*/
}

function rechazarLiquidacionPin(btn) {
    itemplanGbl = btn.data('itemplan');

    if(itemplanGbl == null || itemplanGbl == '') {
        return;
    }   
 
    Swal.fire({        
        title: 'Rechazar Liquidacion',
        html: 'Esta seguro de rechazar la liquidacion?',
        icon: 'warning',
        html: '<div class="form-group">'+
                    '<textarea class="col-md-12 form-control" placeholder="Ingresar Comentario..." style="height:80px;background:#F9F8CF" id="comentarioText"></textarea>'+
                '</div>',
        showCancelButton: true,        
        confirmButtonText: 'Si, rechazar Liquidacion!',
        cancelButtonClass: 'btn btn-secondary',
        allowOutsideClick: false
    }).then((result) => {
            if (result.value) {
                
                var formData = new FormData();
                formData.append('itemplan', itemplanGbl);
                var comentario = $('#comentarioText').val();
                formData.append('comentario', comentario);
                $.ajax({
                    type  :	'POST',
                    url   :	'rechazarLiquiPin',
                    data  :	formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    beforeSend: () => {
                        $('#btnProcesar').attr("disabled", true);
                    }
                }).done(function(data){
                    var data = JSON.parse(data);
                    
                 //   console.log(data);
                    if(data.error == 0){
                        mostrarNotificacion(2, 'success', 'Se validó correctamente', 'Verificar');
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

// function getPoObra(btn) {
//     var itemplanGbl = null;
//     itemplanGbl = btn.data('itemplan');

//     if(itemplanGbl == null || itemplanGbl == '') {
//         return;
//     }

//     $.ajax({
//         type : 'POST',
//         url  : 'getTabEstacionPoUtils',
//         data : { itemplan : itemplanGbl }
//     }).done(function(data){
//         data = JSON.parse(data);

//         if(data.error == 0) {
//             $('#contTabLiquidacion').html(data.TabVerticalEstacion);
//             modal('mdlLiquidacionObraPin');
//         } else {
//             return;
//         }
//     });
// }
var itemplanEditGlobal = null;
var codigoPoEditGlobal = null;
function openModalPO(btn) {
    itemplanEditGlobal = btn.data('itemplan');

    if(itemplanEditGlobal == null || itemplanEditGlobal == '') {
        return;
    }

    objPo.itemplan       = itemplanEditGlobal;

    $.ajax({
        type : 'POST',
        url  : 'getTablaPoValidacion',
        data : { itemplan : itemplanEditGlobal }
    }).done(function(data){
        data = JSON.parse(data);
        arrayKitPartida = data.arrayDetallePo;

        if(data.error == 0) {
            $('#contPo').html(data.tablaPo);
            modal('mdlPoPin');
        } else {
            return;
        }
    });
}


var idEmpresaColabGlobal = null;
var objPo = {};

var objDetallePo = {};
var arrayKitPartida = [];
var montoTotal = 0;
var codigoPoEditGlb = null;
function openModalCotizacion(btn) {
    itemplanGlobal  = btn.data('itemplan');
    codigoPoEditGlb = btn.data('codigo_po');

    if(itemplanGlobal == null || itemplanGlobal == '' || codigoPoEditGlb == null || codigoPoEditGlb == '') {
        return;
    }

    objPo.itemplan       = itemplanGlobal;

    $.ajax({
        type : 'POST',
        url  : 'getDataKitPartida',
        data : { itemplan  : itemplanGlobal,
                 codigo_po : codigoPoEditGlb }
    }).done(function(data){
        data = JSON.parse(data);
        arrayKitPartida = data.arrayDetallePo;

        if(data.error == 0) {
            $('#contTablaKitPartida').html(data.tablaKitPartida);
            $('#contTablaPoDetalle').html(data.tablaDetallePo);
            modal('mdlCotizacionPin');
        } else {
            return;
        }
    });
    
}

function agregarPartida(btn) {

    var codigoPartida   = btn.data('codigo_partida');
    var baremo          = btn.data('baremo');
    var costo_material  = btn.data('costo_material');
    var costoPreciario  = btn.data('costo_preciario');
    var nomPartida      = btn.data('nom_partida');

    if(itemplanGlobal == null || itemplanGlobal == '') {
        return;
    }

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
                return false;
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
    $('#montoTotalGeneral').html(totalFinal);
}

function guardarKitPartidaPin() {
    if(codigoPoEditGlb == null || codigoPoEditGlb == '') {
        return;
    }
    
    if(arrayKitPartida.length == 0) { 
        mostrarNotificacion(1, 'error', "Para almacenar la partida tiene que completar los datos", 'Incorrecto');
        return;
    }

    $.ajax({
        type : 'POST',
        url  : 'actualizarKitPartidaPin',
        data : { 
                    arrayKitPartida : arrayKitPartida,
                    codigo_po       : codigoPoEditGlb
                }
    }).done(function(data) {

       
        data = JSON.parse(data);

        if(data.error == 0) {
            modal('mdlCotizacionPin');
            modal('mdlPoPin');
            mostrarNotificacion(2, 'success', data.msj, 'Correcto');
        } else {
            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        }
    });
}