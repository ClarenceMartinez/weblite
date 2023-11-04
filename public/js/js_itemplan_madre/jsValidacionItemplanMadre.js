function  modalItemplanHijos(itemplanMadre) {
    $('body').loading({
        message: 'Espere por favor...'
    });

    $.ajax({
        type: 'POST',
        'url': 'getHijosValIpMadre',
        data: {itemplanMadre: itemplanMadre},
        'async': false
    }).done(function (data) {
        var data = JSON.parse(data);
        if (data.error == 0) {
            $('body').loading('destroy')
            $('#contTablaItemsHijos').html(data.tablaItemHijos);
            $("#modalItemplanHijos").modal('show');
            $("#itemPlanPE").val(itemplanMadre);
            $("#titel_hijos").html('IP MADRE: ' + itemplanMadre);
            //initDataTable('#data-table2');
        } else if (data.error == 1) {
            mostrarNotificacion('error', 'Hubo problemas al mostrar datos solicitados');
        }
    });
}

var itemplanGlobal = null;
function  modalValiOpe(itemPlan, number) {
    $("#selectEstado").select2("val", "0");
    $('#textComentario').val('');
    $('#selectEstado').val('');
	
	itemplanGlobal = itemPlan;
	$("#title_vali").html('VALIDACION DE DISE\u00D1O');
    if (number == '1') {
        $("#title_vali").html('VALIDACION DE DISE\u00D1O');
        $("#modalVali").modal('show');
        $('#boton_multiuso').attr("onclick", 'validarItemplan("' + itemPlan + '",1)');

    } else {

        $("#title_vali").html('VALIDACION DE OPERACION');
        $("#modalVali").modal('show');
        $('#boton_multiuso').attr("onclick", 'validarItemplan("' + itemPlan + '",2)');
    }
}

function validarItemplan(itemPlan, flg_tipo) {
    console.log(itemPlan);
    var textComentario = $('#textComentario').val();
    var selectEstado = $('#selectEstado').val();
    var itemPlanPE = $("#itemPlanPE").val();

    if (textComentario === '' || textComentario === null) {
        Swal.fire('Mensaje', 'Ingrese comentario', 'warning');
        $("#inputCecoAdd").focus();
        return false;
    }
    if (selectEstado === '' || selectEstado === 0   || selectEstado === null) {
        Swal.fire('Mensaje', 'Selecione accion', 'warning');
        $("#selectEstado").focus();
        return false;
    } else {
        $.ajax({
            url: 'validarItemplanPan',
            type: "POST",
            data: {itemPlan: itemPlan, textComentario: textComentario, selectEstado: selectEstado, flg_tipo: flg_tipo, itemPlanPE: itemPlanPE},
            dataType: "JSON",
            success: function (data)
            {
                if (data.error == 0) {
                    $('#modalVali').modal('hide');
                    $('#modalItemplanHijos').modal('hide');
                    Swal.fire('Mensaje', 'Se valido correctamente', 'success');                   
                } else {
                    Swal.fire('Mensaje', data.msj, 'error');                    
                }
            }
        });

    }
}