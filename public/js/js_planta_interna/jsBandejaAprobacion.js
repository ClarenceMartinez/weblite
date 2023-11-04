function openConfirmarAprobacion(btn) {
    var itemplan = btn.data('itemplan');

    if(itemplan == null || itemplan == '') {
        return;
    }

    mostrarNotificacion(3, 'question', 'Esta seguro de aprobar este itemplan', 'Al confirmar se creara la solicitud oc.')
    .then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.value) {
            $.ajax({
                type : 'POST',
                url  : 'aprobarObraPin',
                data : { itemplan : itemplan }
            }).done(function(data){
                data = JSON.parse(data);

                if(data.error == 0) {
                    mostrarNotificacion(2, 'success', 'Se confirmo correctamente', 'Verificar');
                } else {
                    mostrarNotificacion(1, 'error', data.msj, 'Verificar');
                }
            });
        } else if (result.isDenied) {
          Swal.fire('Changes are not saved', '', 'info')
        }
    });
}

var itemplanGlobal = null;

var objPo = {};
function openModalDetalle(btn) {
    itemplanGlobal = btn.data('itemplan');
    idEmpresaColabGlobal = btn.data('id_empresacolab');

    modal('mdlDetalleItemPlan');

    if(itemplanGlobal == null || itemplanGlobal == '') {
        return;
    }

    objPo.itemplan= itemplanGlobal;
  

    $.ajax({
        type : 'POST',
        url  : 'getDataKitPartidadetalle',
        data : { itemplan : itemplanGlobal }
    }).done(function(data){
        data = JSON.parse(data);

        if(data.error == 0) {
            $('#contTablaKitPartidadetalle').html(data.tablaKitPartida);
            
            modal('mdlDetalleItemPlan');
        } else {
            return;
        }
    });
    
}