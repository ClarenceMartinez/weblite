function descargarPlanobraCV(){
    $.ajax({
            type  :	'POST',
            url   :	'getReportePlanobraCv',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte de Obras Pangeaco.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });
}

function descargarDetallePoMo(){
    $.ajax({
            type  :	'POST',
            url   :	'getReportePoMoCv',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","PLANOBRA_MO_PAN_CV.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });
}

function descargarDetallePoMat(){
    $.ajax({
            type  :	'POST',
            url   :	'getReportePoMatCv',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","PLANOBRA_MAT_PAN_CV.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });
}

function descargarDetallePlanCV(){
    $.ajax({
            type  :	'POST',
            url   :	'getReporteDetallePlanCv',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detalle Po de Obras Pangeaco.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });
}

function descargarSolicitudOc(){
    $.ajax({
            type  :	'POST',
            url   :	'getReporteSolicitudOc',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","SOLICITUD_OC.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });
}

function descargarPlanobraDetHijosCV(){
    $.ajax({
            type  :	'POST',
            url   :	'getReportHijosCv',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detalle Seguimiento CV.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });
}

function descargarPlanobraDetHijosB2b(){
    $.ajax({
            type  :	'POST',
            url   :	'getReportHijosB2b',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detalle Seguimiento B2B.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });
}
 
function decargarDetalleMat(){
    $.ajax({
            type  :	'POST',
            url   :	'getDetallePoMat',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detalle PO Mat.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });    
}

function decargarCotizacionesB2b(){
    $.ajax({
            type  :	'POST',
            url   :	'getRepCotizaB2b',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Cotizaciones B2B.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });     
}

function decargarDetLicencias(){
    $.ajax({
            type  :	'POST',
            url   :	'getDetLicenciasRep',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detalle Licencias.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });     
}

function decargarDetFormRefCto(){
    $.ajax({
            type  :	'POST',
            url   :	'getDetFormRefCto',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detalle Formulario Reforzamiento CTO.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });     
}

function decargarDetalleMo(){
    $.ajax({
            type  :	'POST',
            url   :	'getReportePoMoAll',
            data  :	{ },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detalle PO Mo.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });     
}


function decargarDetMatrizSeguimiento(){
    $.ajax({
            type  : 'POST',
            url   : 'getDetMatrizSeg',
            data  : { },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detallado de Matriz Seguimiento.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });     
}




function decargarDetMatrizJumpeo(){
    $.ajax({
            type  : 'POST',
            url   : 'getDetMatrizJum',
            data  : { },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","Reporte Detallado de Matriz Jumpeo.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });     
}

function decargarDetMatrizPinPex(){
    $.ajax({
            type  : 'POST',
            url   : 'getDetMatrizPinPex',
            data  : { },
            dataType:"html",//html
            contentType:"application/x-www-form-urlencoded",
            beforeSend: () => {
                $('body').loading({
                    message: 'Espere por favor...'
                });
            }
    }).done(function(data){
        var data = JSON.parse(data);
        if(data.error == 0){
            if(data.archivo != null && data.archivo != undefined){
                var $a = $("<a>");
                $a.attr("href",data.archivo);
                $("body").append($a);
                $a.attr("download","ReporteMatrizPinPex.xls");
                $a[0].click();
                $a.remove();
            }
        }else{
            mostrarNotificacion('error','Aviso',data.msj);
        }

    }).always(() => {
        $('body').loading('destroy')
    });     
}