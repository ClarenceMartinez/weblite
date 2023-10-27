var objDetallePo = {};
var arrayKitPartida = [];
var montoTotal = 0.00;


var itemplanGlobal = null;
var idEmpresaColabGlobal = null;
var objPo = {};
function openModalMaterial(btn) {
    itemplanGlobal = btn.data('itemplan');
    idEmpresaColabGlobal = btn.data('id_empresacolab');

    if(itemplanGlobal == null || itemplanGlobal == '' || idEmpresaColabGlobal == null || idEmpresaColabGlobal == '') {
        return;
    }

    objPo.itemplan       = itemplanGlobal;
    objPo.idEmpresaColab = idEmpresaColabGlobal;

    $.ajax({
        type : 'POST',
        url  : 'getDataKitPartida',
        data : { itemplan : itemplanGlobal }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {           
            arrayKitPartida = [];
            $("#tBodyActividades").html('');

            $('#contTablaKitPartida').html(data.tablaKitPartida);            
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
    
   

    // $("#tBodyActividades").html('');
    // arrayKitPartida.forEach(function(data, key){


        $("#tBodyActividades").append(tr);
    // });

    objDetallePo = {};
}

function guardarKitPartidaPin() {

    if(arrayKitPartida.length!=0){

        $.ajax({
            type : 'POST',
            url  : 'registrarKitPartidaPin',
            data : { 
                        arrayKitPartida : arrayKitPartida,
                        objPoFinal      : JSON.stringify(objPo)
                    }
        }).done(function(data) {

        
            data = JSON.parse(data);

            if(data.error == 0) {
                modal('mdlCotizacionPin');
                mostrarNotificacion(2, 'success', data.msj, 'Correcto');
            } else {
                mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
            }
        });
    
    }else{
       
        mostrarNotificacion(1, 'error', "Para almacenar la partida tiene que completar los datos", 'Incorrecto');
    }
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
        console.log(arrayKitPartida);
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