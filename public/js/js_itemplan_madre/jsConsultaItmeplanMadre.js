function  modalItemplanHijos(itemplanMadre) {
    $('body').loading({
        message: 'Espere por favor...'
    });

    $.ajax({
        type: 'POST',
        'url': 'getDataHijosItemValida',
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