
//nav-link-tab
jQuery(document).on('click', '.nav-link-tab',function(e)
  {
    e.preventDefault();
    $('.nav-link-tab').removeClass('active');
    $(this).addClass('active');
    idx = $(this).attr('data-section');
    $(".section-log").addClass('d-none');
    console.log("Ocultar");

    //tab-panex
    $(".tab-panex").addClass('d-none');
    $(".tab-panex").removeClass('show');
    $(".tab-panex").removeClass('active');
    $("#"+idx).removeClass('d-none');
    // $("#"+idx).addClass('d-none');
    $("#"+idx).addClass('show');
    $("#"+idx).addClass('active');

  })

jQuery(document).on('click', '#btnSearchItemPlan',function(e)
  {
    e.preventDefault();
    buscarItemPlan();
    $("#v-tabs-tab").addClass('d-none');
    $("#v-tabs-tabContent").addClass('d-none');
    $("#frmMatrizSeguimientoDiseno").find("#_id").val("0");
    
  })

jQuery(document).on('click', '#btnSearchItemPlan2',function(e)
  {
    e.preventDefault();
    var _this = $(this);
    var form  = _this.attr('data-form');
    buscarItemPlan2(form);
    
  })


jQuery(document).on('click', '#btnBuscarMatrizSeguimiento',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    $("#v-tabs-tab").addClass('d-none');
    $("#v-tabs-tabContent").addClass('d-none');
    $("#btnVerLogsMatriz").addClass('d-none');

    if ($.trim(itemplan.val()) == "")
    {
      mostrarNotificacion(1, 'error', 'Ingrese Item Plan', 'Incorrecto');
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      mostrarNotificacion(1, 'error', 'Seleccione DIVICAU', 'Incorrecto');
      return false;
    }


    $.ajax({
        type : 'POST',
        url  : 'getInfoMatrizSegByItemPlan',
        data : { itemplan : itemplan.val(),  cbodivicau: cbodivicau.val()},
        dataType: 'json',
    }).done(function(data)
    {
        if (data.error == 1)
        {
          itemplan.val("");
          mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
          return false;
        }

        if (data.lista.length == 0)
        {
          itemplan.val("");
          itemplan.focus();
          mostrarNotificacion(1, 'error', "Los datos ingresados no existen", 'Incorrecto');
          return false;
        }

        $("#btnVerLogsMatriz").removeClass('d-none');

        var info = data.lista[0];
        var log = data.log;
        // console.log(info);
        // console.log(info.anio);
        $("#frmMatrizSeguimientoDiseno").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoDiseno").find("#anio").val(info.anio);
        $("#frmMatrizSeguimientoDiseno").find("#divicau").val(info.divicau);
        $("#frmMatrizSeguimientoDiseno").find("#tipo").val(info.tipo);
        $("#frmMatrizSeguimientoDiseno").find("#tipo").trigger('change');

        $("#frmMatrizSeguimientoDiseno").find("#modelo").val(info.modelo);
        $("#frmMatrizSeguimientoDiseno").find("#modelo").trigger('change');
        $("#frmMatrizSeguimientoDiseno").find("#plan").val(info.plan);
        $("#frmMatrizSeguimientoDiseno").find("#itemplan").val(info.itemplan);
        $("#frmMatrizSeguimientoDiseno").find("#nodo").val(info.centralDesc);
        $("#frmMatrizSeguimientoDiseno").find("#empresaColab").val(info.empresaColabDesc);
        $("#frmMatrizSeguimientoDiseno").find("#cable").val(info.cable);
        $("#frmMatrizSeguimientoDiseno").find("#troba").val(info.troba);
        $("#frmMatrizSeguimientoDiseno").find("#uipHorizonal").val(info.cantfactorplanificado);
        $("#frmMatrizSeguimientoDiseno").find("#fechaAdjudicaDiseno").val(info.fechaAdjudicaDiseno);
        $("#frmMatrizSeguimientoDiseno").find("#fechaCierreDisenoExpediente").val(info.fechaCierreDisenoExpediente);
        $("#frmMatrizSeguimientoDiseno").find("#fechaEntregaDiseno").val(info.fechaEntregaDiseno);
        $("#frmMatrizSeguimientoDiseno").find("#departamento").val(info.departamento);
        $("#frmMatrizSeguimientoDiseno").find("#provincia").val(info.provincia);
        $("#frmMatrizSeguimientoDiseno").find("#distrito").val(info.distrito);



        $("#v-tabs-tab").removeClass('d-none');
        $("#v-tabs-tabContent").removeClass('d-none');


        $("#frmMatrizSeguimientoDiseno").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoLogistica").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoPIN").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoCensado").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoDespliegue").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoHGU").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoStatus").find("#_id").val(info.id);

        $("#frmMatrizSeguimientoEconomico").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoEconomico").find("#pptoAprobado").val(info.pptoAprobado);
        $("#frmMatrizSeguimientoEconomico").find("#pep").val(info.pep);
        $("#frmMatrizSeguimientoEconomico").find("#ocConstruccionH").val(info.ocConstruccionH);
        $("#frmMatrizSeguimientoEconomico").find("#generacionVR").val(info.generacionVR);
        $("#frmMatrizSeguimientoEconomico").find("#estadoOC").val(info.estadoOC);
        $("#frmMatrizSeguimientoEconomico").find("#estadoOC").trigger('change');
        $("#frmMatrizSeguimientoEconomico").find("#estadoCertificaOC").val(info.estadoCertificaOC);
        $("#frmMatrizSeguimientoEconomico").find("#estadoCertificaOC").trigger('change');


        $("#frmMatrizSeguimientoLicencias").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoLicencias").find("#fechaPresentaLicencia").val(info.fechaPresentaLicencia);
        $("#frmMatrizSeguimientoLicencias").find("#fechaInicioLicencia").val(info.fechaInicioLicencia);
        $("#frmMatrizSeguimientoLicencias").find("#estadoLicencia").val(info.estadoLicencia);
        $("#frmMatrizSeguimientoLicencias").find("#estadoLicencia").trigger('change');

        $("#frmMatrizSeguimientoLogistica").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoLogistica").find("#entregaMateriales").val(info.entregaMateriales);

        $("#frmMatrizSeguimientoPIN").find("#numHilosPuertoOLT").val(info.numHilosPuertoOLT);
        $("#frmMatrizSeguimientoPIN").find("#FechaJumpeoCentral").val(info.FechaJumpeoCentral);
        $("#frmMatrizSeguimientoPIN").find("#estadoPin").val(info.estadoPin);
        $("#frmMatrizSeguimientoPIN").find("#estadoPin").trigger('change');

        $("#frmMatrizSeguimientoCensado").find("#fechaCensado").val(info.fechaCensado);
        $("#frmMatrizSeguimientoCensado").find("#UIPHorizontalCenso").val(info.UIPHorizontalCenso);
        $("#frmMatrizSeguimientoCensado").find("#estadoCenso").val(info.estadoCenso);
        $("#frmMatrizSeguimientoCensado").find("#estadoCenso").trigger('change');

        $("#frmMatrizSeguimientoDespliegue").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoDespliegue").find("#bandejaODF").val(info.fechaInstalacionODF);
        $("#frmMatrizSeguimientoDespliegue").find("#fechaInstalacionODF").val(info.fechaInstalacionODF);
        $("#frmMatrizSeguimientoDespliegue").find("#fechaInicioConstruccion").val(info.fechaInicioConstruccion);
        $("#frmMatrizSeguimientoDespliegue").find("#fechaProyectadaEntrega").val(info.fechaProyectadaEntrega);
        $("#frmMatrizSeguimientoDespliegue").find("#fechaFinalEntregaDivicau").val(info.fechaFinalEntregaDivicau);
        $("#frmMatrizSeguimientoDespliegue").find("#estadoDespliegue").val(info.estadoDespliegue);
        $("#frmMatrizSeguimientoDespliegue").find("#estadoDespliegue").trigger('change');


        $("#frmMatrizSeguimientoHGU").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoHGU").find("#fechaPruebaHGU").val(info.fechaPruebaHGU);
        $("#frmMatrizSeguimientoHGU").find("#comodinAvanceHGU").val(info.comodinAvanceHGU);
        $("#frmMatrizSeguimientoHGU").find("#estadoHGU").val(info.estadoHGU);  
        $("#frmMatrizSeguimientoHGU").find("#estadoHGU").trigger('change');

        $("#frmMatrizSeguimientoStatus").find("#_id").val(info.id);
        $("#frmMatrizSeguimientoStatus").find("#estadoFinal").val(info.estadoFinal); 
        $("#frmMatrizSeguimientoStatus").find("#estadoFinal").trigger('change'); 
        $("#frmMatrizSeguimientoStatus").find("#estadoGlobal").val(info.estadoGlobal); 
        $("#frmMatrizSeguimientoStatus").find("#estadoGlobal").trigger('change'); 



        var dataJson = log;
        initTableLight(dataJson);
        // reInitTableLight(dataJson);




    })


  })


//

jQuery(document).on('click', '#btnVerLogsMatriz',function(e)
{
  e.preventDefault();
  //tab-pane section-log
  $(".tab-pane").removeClass('active');
  $(".tab-pane").removeClass('show');
  $(".tab-pane").removeClass('d-none');

  $(".section-log").addClass('active');
  $(".section-log").addClass('show');

})


function buscarItemPlan()
{
  var itemplan = $("#input_itemplan");

    if ($.trim(itemplan.val()) == "")
    {
      mostrarNotificacion(1, 'error', "Ingrese Item Plan", 'Incorrecto');
      return false;
    }


    $.ajax({
        type : 'POST',
        url  : 'getMatrizSegByItemPlan',
        data : { itemplan : itemplan.val() },
        dataType: 'json',
    }).done(function(data)
    {
      if (data.error == 1)
      {
        itemplan.val("");
        mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        return false;
      }

      if (data.lista.length == 0)
      {
        itemplan.val("");
        itemplan.focus();
        alert("El item plan ingresado no existe");
        return false;
      }
      var html = '';
        html += '<option value="0">-- Seleccione --</option>';
      $.each(data.lista, function( index, value )
      {
        html += '<option value="'+value.divicau+'">'+value.divicau+'</option>';
      });

      $("#cbodivicau").html(html);
    })
}

function buscarItemPlan2(form)
{
  var itemplan = $("#"+form).find("#input_itemplan");

    if ($.trim(itemplan.val()) == "")
    {
      mostrarNotificacion(1, 'error', "Ingrese Item Plan", 'Incorrecto');
      return false;
    }


    $.ajax({
        type : 'POST',
        url  : 'getMatrizSegByItemPlan',
        data : { itemplan : itemplan.val() },
        dataType: 'json',
    }).done(function(data)
    {
      if (data.error == 1)
      {
        itemplan.val("");
        mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        return false;
      }

      if (data.lista.length == 0)
      {
        itemplan.val("");
        itemplan.focus();
        alert("El item plan ingresado no existe");
        return false;
      }
      var html = '';
        html += '<option value="0">-- Seleccione --</option>';
      $.each(data.lista, function( index, value )
      {
        html += '<option value="'+value.divicau+'">'+value.divicau+'</option>';
      });

      $("#"+form).find("#cbodivicau").html(html);
    })
}

//
jQuery(document).on('click', '#btnGuardarDiseno',function(e)
  {
    e.preventDefault();
    var frm = $("#frmMatrizSeguimientoDiseno").serialize();



    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoDiseno',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
      if (data.error == 1)
      {
        mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        return false;
      }

      mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-'); 
    })
  })





jQuery(document).on('click', '#btnGuardarEconomico',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    if ($.trim(itemplan.val()) == "")
    {
      alert("Ingrese Item Plan");
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      alert("Seleccione DIVICAU");
      return false;
    }



    var frm = $("#frmMatrizSeguimientoEconomico").serialize();

    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoEconomico',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
        if (data.error == 1)
        {
          mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
          return false;
        }

        mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-');
    })
  })


jQuery(document).on('click', '#btnGuardarLicencia',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    if ($.trim(itemplan.val()) == "")
    {
      alert("Ingrese Item Plan");
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      alert("Seleccione DIVICAU");
      return false;
    }



    var frm = $("#frmMatrizSeguimientoLicencias").serialize();

    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoLicencia',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
        if (data.error == 1)
        {
          mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
          return false;
        }

        mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-');
    })
  })



jQuery(document).on('click', '#btnGuardarLogistica',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    if ($.trim(itemplan.val()) == "")
    {
      alert("Ingrese Item Plan");
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      alert("Seleccione DIVICAU");
      return false;
    }



    var frm = $("#frmMatrizSeguimientoLogistica").serialize();

    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoLogistica',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
        if (data.error == 1)
        {
          mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
          return false;
        }

        mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-');
    })
  })


jQuery(document).on('click', '#btnGuardarPIN',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    if ($.trim(itemplan.val()) == "")
    {
      alert("Ingrese Item Plan");
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      alert("Seleccione DIVICAU");
      return false;
    }



    var frm = $("#frmMatrizSeguimientoPIN").serialize();

    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoPIN',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
        if (data.error == 1)
        {
          mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
          return false;
        }

        mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-');
    })
  })


jQuery(document).on('click', '#btnGuardarCensado',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    if ($.trim(itemplan.val()) == "")
    {
      alert("Ingrese Item Plan");
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      alert("Seleccione DIVICAU");
      return false;
    }



    var frm = $("#frmMatrizSeguimientoCensado").serialize();

    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoCensado',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
        if (data.error == 1)
        {
          mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
          return false;
        }

        mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-');  
    })
  })


jQuery(document).on('click', '#btnGuardarDespliegue',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    if ($.trim(itemplan.val()) == "")
    {
      alert("Ingrese Item Plan");
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      alert("Seleccione DIVICAU");
      return false;
    }



    var frm = $("#frmMatrizSeguimientoDespliegue").serialize();

    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoDespliegue',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
        if (data.error == 1)
        {
          mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
          return false;
        }

        mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-');
    })
  })


jQuery(document).on('click', '#btnGuardarHGU',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    if ($.trim(itemplan.val()) == "")
    {
      alert("Ingrese Item Plan");
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      alert("Seleccione DIVICAU");
      return false;
    }



    var frm = $("#frmMatrizSeguimientoHGU").serialize();

    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoHGU',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
        if (data.error == 1)
        {
          mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
          return false;
        }

        mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-');
    })
  })

jQuery(document).on('click', '#btnGuardarStatus',function(e)
  {
    e.preventDefault();
    var itemplan    = $("#input_itemplan");
    var cbodivicau  = $("#cbodivicau");

    if ($.trim(itemplan.val()) == "")
    {
      alert("Ingrese Item Plan");
      return false;
    }

    if ($.trim(cbodivicau.val()) == "0")
    {
      alert("Seleccione DIVICAU");
      return false;
    }



    var frm = $("#frmMatrizSeguimientoStatus").serialize();

    $.ajax({
        type : 'POST',
        url  : 'postUpdateMatrizSeguimientoStatus',
        data : frm,
        dataType: 'json',
    }).done(function(data)
    {
      if (data.error == 1)
      {
        mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
        return false;
      }

      mostrarNotificacion(4, 'success', 'SE ACTUALIZO EL ITEMPLAN CORRECTAMENTE', '-');
    })
  })




function exportarFormatoCargaMatrizSeguimiento(tipo)
{
  var ruta = 'getExcelFmtAtenMatrizSeguimiento';
  if (tipo == 2)
  {
    ruta = 'getExcelFmtAtenMatrizSeguimientoMin';
  }

    $.ajax({
            type: 'POST',
            url: ruta,
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




function procesarFile(tipo)
{
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
        text: "Asegurese de seleccionar un archivo de tipo (.xls,.xlsx)",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return cargarExcelPromise(formData, tipo).then(function (data) { 
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

var arrayDataGlob = [];
function cargarExcelPromise(formData, tipo){
    return new Promise(function (resolve, reject) {

      var ruta = 'procesarFileMatSeg';
      if (tipo == 2)
      {
        ruta = 'procesarFileMatSegMin';
      }


        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDataGlob = JSON.parse(data.jsonDataFile);
                console.log('arrayDataGlob: ',arrayDataGlob);
                $('#contTabla').html(data.tbObservacion);
                $('#tituTbObs').text(data.titulo);
                $('#tituTbObs').css('display', 'block');
                initDataTableResponsive('tbObservacion');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}



function cargarExcelPromise_old(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  : 'POST',
            url   : 'procesarExcelMassiveParticle',
            data  : formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayArchivoGlob = JSON.parse(data.jsonDataFile);
                console.log('arrayArchivoGlob: ',arrayArchivoGlob);
                $('#cont_tb').html(data.tbReporte);
                $('#tituloCarga').text(data.titulo);
                toastr["success"](data.msj, "Aviso");
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}





function cargarFile(tipo){

    if(arrayDataGlob.length == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe tener registros válidos para cargar!!');
        return;
    }

    var formData = new FormData();
    formData.append('arrayDataFile', JSON.stringify(arrayDataGlob));
  
    var files = $('#archivo')[0].files[0];
    formData.append('file', files)
  
    Swal.queue([
    {
        title: "Está seguro de atender las solicitudes??",
        text: "Asegurese de validar la información",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return atenderSolPromise(formData, tipo).then(function (data) { 
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

$(".btn-dismis-modal").on('click', function(){
  $(".modal").modal('hide');
});


function atenderSolPromise(formData, tipo)
{
    return new Promise(function (resolve, reject)
    {
      var ruta = 'cargaAtenMasivaMatrizSeguimiento';

      if (tipo == 2)
      {
        ruta = 'cargaAtenMasivaMatrizSeguimientoMin';
      }

        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDataGlob = [];
                $('#archivo').val(null);
                $('#contTabla').html(null);
                $('#tituTbObs').text('');
                $('#tituTbObs').css('display','none');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}


function openModalDetalleLogMatrizSeg(btn) {
    $(".modal").modal('hide');
    var newData = JSON.parse(atob(btn.data('new')));
    var oldData = JSON.parse(atob(btn.data('old')));

    $("#modalDatosImpactados").modal('show');

    // console.log(newData);
    // console.log(newData.length);
    var table =  '';
    var indice = 0;
    $.each(newData, function( campo, value )
    {
      oldx = (oldData[campo] == null) ? '' : oldData[campo];

      var UpperCaseCharacters = campo.match(/([A-Z]?[^A-Z]*)/g).slice(0,-1);
      // console.log(UpperCaseCharacters);
      campo = UpperCaseCharacters.join(" ");

      estilo = (oldx != value) ? ' background-color: rgb(255 194 65 / 15%);' : '';
      
      if (indice > 0)
      {
        table += '<tr style="'+estilo+'">';
        table += '<td style="text-transform: capitalize;">'+campo.toLowerCase()+'</td>';
        table += '<td>'+oldx+'</td>';
        table += '<td>'+value+'</td>';
        table += '</tr>';
      }

      indice++;
    });

    $("#tblCamposImpactados").find('tbody').html(table);
}

//botonAceptarModal


jQuery(document).on('click', '#botonAceptarModal',function(e)
  {
    e.preventDefault();
    $("#modalDatosImpactados").modal('hide');

  })




function procesarFileMatriz(tipo)
{
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
        text: "Asegurese de seleccionar un archivo de tipo (.xls,.xlsx)",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return cargarExcelPromiseByTipo(formData, tipo).then(function (data) { 
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



function cargarExcelPromiseByTipo(formData, tipo){
    return new Promise(function (resolve, reject) {

      var ruta = '';
      var context = '';
      if (tipo == 'DISENO')
      {
        ruta = 'procesarFileMatSegDiseno';
        context = 'content-diseno-wrap';

      }

      if (tipo == 'ECONOMICO')
      {
        ruta = 'procesarFileMatSegEconomico';
        context = 'content-economico-wrap';
      }

      if (tipo == 'LICENCIA')
      {
        ruta = 'procesarFileMatSegLicencia';
        context = 'content-licencia-wrap';
      }

      if (tipo == 'LOGISTICA')
      {
        ruta = 'procesarFileMatSegLogistica';
        context = 'content-logistica-wrap';
      }

      if (tipo == 'PIN')
      {
        ruta = 'procesarFileMatSegPin';
        context = 'content-pin-wrap';
      }

      if (tipo == 'CENSADO')
      {
        ruta = 'procesarFileMatSegCensado';
        context = 'content-censado-wrap';
      }

      if (tipo == 'DESPLIEGUE')
      {
        ruta = 'procesarFileMatSegDespliegue';
        context = 'content-despliegue-wrap';
      }

      if (tipo == 'HGU')
      {
        ruta = 'procesarFileMatSegHGU';
        context = 'content-hgu-wrap';
      }

      if (tipo == 'STATUS')
      {
        ruta = 'procesarFileMatSegStatus';
        context = 'content-status-wrap';
      }


        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDataGlob = JSON.parse(data.jsonDataFile);
                console.log('arrayDataGlob: ',arrayDataGlob);
                console.log(context);
                $('#'+context).find('#contTabla').html(data.tbObservacion);
                $('#'+context).find('#tituTbObs').text(data.titulo);
                $('#'+context).find('#tituTbObs').css('display', 'block');
                initDataTableResponsiveParent(context, 'tbObservacion');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}
function exportarFormatoCargaMatrizJumpeo(tipo)
{
  var ruta = '';
  if (tipo == 'DISEÑO')
  {
    ruta = 'getExcelFmtAtenMatrizJumpeoDiseno';
  }

  if (tipo == 'ECONOMICO')
  {
    ruta = 'getExcelFmtAtenMatrizJumpeoEconomico';
  }

  if (tipo == 'LICENCIA')
  {
    ruta = 'getExcelFmtAtenMatrizJumpeoLicencia';
  }

  if (tipo == 'LOGISTICA')
  {
    ruta = 'getExcelFmtAtenMatrizJumpeoLogistica';
  }

  if (tipo == 'CENSADO')
  {
    ruta = 'getExcelFmtAtenMatrizJumpeoCensado';
  }

  if (tipo == 'DESPLIEGUE')
  {
    ruta = 'getExcelFmtAtenMatrizJumpeoDespliegue';
  }

  if (tipo == 'HGU')
  {
    ruta = 'getExcelFmtAtenMatrizJumpeoHGU';
  }

  if (tipo == 'STATUS')
  {
    ruta = 'getExcelFmtAtenMatrizJumpeoStatus';
  }

  if (ruta == "")
  {
    
    mostrarNotificacion(1, 'error', 'Estamos trabajando', 'Incorrecto');
    return false;
  }

    $.ajax({
            type: 'POST',
            url: ruta,
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




jQuery(document).on('click', '.btn-event-log',function(e)
{
  e.preventDefault();
  var _this       = $(this);
  $("#modalxlog").modal("show");
})


jQuery(document).on('click', '#btnBuscarMatrizSeguimientoFiltro',function(e)
{
  e.preventDefault();
  var _this       = $(this);
  var form        = _this.attr('data-form');
  var context     = _this.attr('data-context');
  var itemplan    =  $("#"+form).find("#input_itemplan");

  $("#"+context).addClass('d-none');
  
  if (context == 'contentwrap-diseno')
  {
    $("#tblDiseno").addClass('d-none');
    $("#content-log-diseno").addClass('d-none');
  }


  $("#content-log-economico").addClass('d-none');
  $("#content-log-licencia").addClass('d-none');
  $("#content-log-logistica").addClass('d-none');
  $("#content-log-pin").addClass('d-none');
  $("#content-log-censado").addClass('d-none');
  $("#content-log-despliegue").addClass('d-none');
  $("#content-log-hgu").addClass('d-none');
  $("#content-log-status").addClass('d-none');

  if (itemplan.val() == "")
  {
      mostrarNotificacion(1, 'error', "Ingrese Itemplan", 'Incorrecto');
      return false;
  }

  $.ajax({
        type : 'POST',
        url  : 'getInfoMatSegByItemplan',
        data : { itemplan : itemplan.val()},
        dataType: 'json',
    }).done(function(response)
    {
      if (response.error == 1)
      {
        $("#content-log-diseno").addClass('d-none');
        mostrarNotificacion(1, 'error', response.msj, 'Incorrecto');
        return false;
      }

      item    = response.lista[0];
      var logx= response.log;

      //tblModalHistory 
      var tablex = '';
      if (response.log.length == "0")
      {
        $("#tblModalHistory").find('tbody').html(tablex);
      }
      $.each(logx, function( indice, log )
      {
        tablex += '<tr>';
        tablex += '<td>'+log[1]+'</td>';
        tablex += '<td>'+log[2]+'</td>';
        tablex += '<td>'+log[3]+'</td>';
        tablex += '<td>'+log[4]+'</td>';
        tablex += '</tr>';
      })
        $("#tblModalHistory").find('tbody').html(tablex);


      $("#"+context).removeClass('d-none');
      if (context == 'contentwrap-diseno')
      {
        $("#tblDiseno").removeClass('d-none');
      }


      $("#"+context).find('tbody').html("");

      // console.log("context: "+context);
      // console.log("length: "+response.lista.length);
      $("#tblDiseno").find('tbody').html("");
      if (context == 'contentwrap-diseno' && response.lista.length > 0)
      {
        $("#tblDiseno").find('tbody').html("");
        $("#"+context).find("#itemplan").val(item.itemplan);
        $("#"+context).find("#anio").val(item.anio);
        $("#"+context).find("#divicau").val(item.divicau);
        $("#"+context).find("#plan").val(item.plan);
        $("#"+context).find("#nodo").val(item.nodo);
        $("#"+context).find("#empresaColab").val(item.empresaColabDesc);
        $("#content-log-diseno").removeClass('d-none');



        var table = '';

        $.each(response.lista, function( index, value )
        {
          var uipHorizontalDiseno     = (value.uipHorizontalDiseno == null) ? '' : value.uipHorizontalDiseno;
          var fechaEntregaDiseno      = (value.fechaEntregaDiseno == null) ? '' : value.fechaEntregaDiseno;
          var troba                   = (value.troba == null) ? '' : value.troba;
          var estadoDiseno            = (value.estadoDiseno == null) ? '' : value.estadoDiseno;
          var departamento            = (value.departamento == null) ? '' : value.departamento;
          var provincia               = (value.provincia == null) ? '' : value.provincia;
          var distrito                = (value.distrito == null) ? '' : value.distrito;

          var modelo                = (value.modelo == null) ? '' : value.modelo;
          var cable                = (value.cable == null) ? '' : value.cable;
          var nodo                = (value.nodo == null) ? '' : value.nodo;

          table += '<tr>';
          table +=    '<td>'+value.divicau+'</td>';
          table +=    '<td>'+uipHorizontalDiseno+'</td>';
          table +=    '<td>'+modelo+'</td>';
          table +=    '<td>'+cable+'</td>';
          table +=    '<td>'+nodo+'</td>';
          table +=    '<td>'+fechaEntregaDiseno+'</td>';
          table +=    '<td>'+troba+'</td>';
          table +=    '<td>'+estadoDiseno+'</td>';
          table +=    '<td>'+departamento+'</td>';
          table +=    '<td>'+provincia+'</td>';
          table +=    '<td>'+distrito+'</td>';
          table += '</tr>';
        });

        $("#tblDiseno").find('tbody').html(table);
      }

      console.log(context);
      if (context == 'contentwrap-licencia')
      {

        $("#content-log-licencia").removeClass('d-none');

        $("#content-log-licencia").addClass('d-none');
        if(response.lista.length > 0)
        {
          $("#content-log-licencia").removeClass('d-none');
        }

        var table = '';
        $.each(response.lista, function( index, value )
        {
          var fechaPresentaLicencia = (value.fechaPresentaLicencia == null) ? '' : value.fechaPresentaLicencia;
          var fechaInicioLicencia   = (value.fechaInicioLicencia == null) ? '' : value.fechaInicioLicencia;
          var estadoLicencia        = (value.estadoLicencia == null) ? '' : value.estadoLicencia;
          table += '<tr>';
          table +=    '<td>'+value.divicau+'</td>';
          table +=    '<td>'+fechaPresentaLicencia+'</td>';
          table +=    '<td>'+fechaInicioLicencia+'</td>';
          table +=    '<td>'+estadoLicencia+'</td>';
          table += '</tr>';
        });

        $("#"+context).find('tbody').html(table);
      }

      if (context == 'contentwrap-economico')
      {   
          $("#content-log-economico").addClass('d-none');
          if(response.lista.length > 0)
          {
            $("#content-log-economico").removeClass('d-none');
          }

          var table = '';
          var indice = 0;
          $.each(response.lista, function( index, value )
          {
            if (indice == 0)
            {
              var ppto                = (value.pptoAprobado == 0) ? 'NO' : 'SI';
                  ppto                = (ppto == null) ? '' : ppto;
              var pep                 = (value.pep == null) ? '' : value.pep;
              var ocConstruccionH     = (value.ocConstruccionH == null) ? '' : value.ocConstruccionH;
              var generacionVR        = (value.generacionVR == null) ? '' : value.generacionVR;
              var estadoOC            = (value.estadoOC == null) ? '' : value.estadoOC;
              var estadoCertificaOC   = (value.estadoCertificaOC == null) ? '' : value.estadoCertificaOC;
              table += '<tr>';
              table +=    '<td>'+ppto+'</td>';
              table +=    '<td>'+pep+'</td>';
              table +=    '<td>'+ocConstruccionH+'</td>';
              table +=    '<td>'+generacionVR+'</td>';
              table +=    '<td>'+estadoOC+'</td>';
              table +=    '<td>'+estadoCertificaOC+'</td>';
              table += '</tr>';

            }
            

            indice ++;
          });

          $("#"+context).find('tbody').html(table);
      }

      if (context == 'contentwrap-logistica')
      {
          $("#content-log-logistica").addClass('d-none');
          if(response.lista.length > 0)
          {
            $("#content-log-logistica").removeClass('d-none');
          }
          var table = '';
          $.each(response.lista, function( index, value )
          {
            var ppto = (value.pptoAprobado == 0) ? 'No' : 'Si';
            var entregaMateriales = (value.entregaMateriales == null) ? '' : value.entregaMateriales;
            table += '<tr>';
            table +=    '<td>'+value.divicau+'</td>';
            table +=    '<td>'+entregaMateriales+'</td>';
            table += '</tr>';
          });

          $("#"+context).find('tbody').html(table);
      }

      if (context == 'contentwrap-censado')
      {
          $("#content-log-censado").addClass('d-none');
          if(response.lista.length > 0)
          {
            $("#content-log-censado").removeClass('d-none');
          }
          var table = '';
          $.each(response.lista, function( index, value )
          {
            var fechaCensado        = (value.fechaCensado == null) ? '' : value.fechaCensado;
            var UIPHorizontalCenso  = (value.UIPHorizontalCenso == null) ? '' : value.UIPHorizontalCenso;
            var estadoCenso         = (value.estadoCenso == null) ? '' : value.estadoCenso;

            table += '<tr>';
            table +=    '<td>'+value.divicau+'</td>';
            table +=    '<td>'+fechaCensado+'</td>';
            table +=    '<td>'+UIPHorizontalCenso+'</td>';
            table +=    '<td>'+estadoCenso+'</td>';
            table += '</tr>';
          });

          $("#"+context).find('tbody').html(table);
      }

      if (context == 'contentwrap-despliegue')
      {
          $("#content-log-despliegue").addClass('d-none');
          if(response.lista.length > 0)
          {
            $("#content-log-despliegue").removeClass('d-none');
          }

          var table = '';
          $.each(response.lista, function( index, value )
          {
            var bandejaODF  = (value.bandejaODF == null) ? '' : value.bandejaODF;
            var fechaInstalacionODF      = (value.fechaInstalacionODF == null) ? '' : value.fechaInstalacionODF;
            var fechaInicioConstruccion  = (value.fechaInicioConstruccion == null) ? '' : value.fechaInicioConstruccion;
            var fechaProyectadaEntrega   = (value.fechaProyectadaEntrega == null) ? '' : value.fechaProyectadaEntrega;
            var fechaFinalEntregaDivicau = (value.fechaFinalEntregaDivicau == null) ? '' : value.fechaFinalEntregaDivicau;
            var estadoDespliegue         = (value.estadoDespliegue == null) ? '' : value.estadoDespliegue;
            
            table += '<tr>';
            table +=    '<td>'+value.divicau+'</td>';
            // table +=    '<td>'+bandejaODF+'</td>';
            table +=    '<td>'+fechaInstalacionODF+'</td>';
            table +=    '<td>'+fechaInicioConstruccion+'</td>';
            table +=    '<td>'+fechaProyectadaEntrega+'</td>';
            table +=    '<td>'+fechaFinalEntregaDivicau+'</td>';
            table +=    '<td>'+estadoDespliegue+'</td>';
            table += '</tr>';
          });

          $("#"+context).find('tbody').html(table);
      }

      if (context == 'contentwrap-hgu')
      {
          $("#content-log-hgu").addClass('d-none');
          if(response.lista.length > 0)
          {
            $("#content-log-hgu").removeClass('d-none');
          }
          var table = '';
          $.each(response.lista, function( index, value )
          {
            var fechaPruebaHGU    = (value.fechaPruebaHGU == null) ? '' : value.fechaPruebaHGU;
            var estadoHGU         = (value.estadoHGU == null) ? '' : value.estadoHGU;
            var comodinAvanceHGU  = (value.comodinAvanceHGU == null) ? '' : value.comodinAvanceHGU;
            
            table += '<tr>';
            table +=    '<td>'+value.divicau+'</td>';
            table +=    '<td>'+fechaPruebaHGU+'</td>';
            table +=    '<td>'+comodinAvanceHGU+'</td>';
            table +=    '<td>'+estadoHGU+'</td>';
            table += '</tr>';
          });

          $("#"+context).find('tbody').html(table);
      }

      if (context == 'contentwrap-status')
      {
          $("#content-log-status").addClass('d-none');
          if(response.lista.length > 0)
          {
            $("#content-log-status").removeClass('d-none');
          }

          var table = '';
          $.each(response.lista, function( index, value )
          {
            var estadoFinal     = (value.estadoFinal == null) ? '' : value.estadoFinal;
            var estadoGlobal    = (value.estadoGlobal == null) ? '' : value.estadoGlobal;
            
            table += '<tr>';
            table +=    '<td>'+value.divicau+'</td>';
            table +=    '<td>'+estadoFinal+'</td>';
            table +=    '<td>'+estadoGlobal+'</td>';
            table += '</tr>';
          });

          $("#"+context).find('tbody').html(table);
      }


    })

})




function cargarFileMatriz(tipo){

    if(arrayDataGlob.length == 0){
        mostrarNotificacion(1,'warning', 'Aviso','Debe tener registros válidos para cargar!!');
        return;
    }

    var formData = new FormData();
    formData.append('arrayDataFile', JSON.stringify(arrayDataGlob));
    
    var context = '';
    var url     = '';
    if (tipo == 'DISENO')
    {
      context = 'content-diseno-wrap';
      url     = 'cargaAtenMasivMatSegDis'; 
    }

    if (tipo == 'ECONOMICO')
    {
      context = 'content-economico-wrap';
      url     = 'cargaAtenMasivMatSegEco';
    }

    if (tipo == 'LICENCIA')
    {
      context = 'content-licencia-wrap';
      url     = 'cargaAtenMasivMatSegLic';
    }

    if (tipo == 'LOGISTICA')
    {
      context = 'content-logistica-wrap';
      url     = 'cargaAtenMasivMatSegLog';
    }

    if (tipo == 'PIN')
    {
      context = 'content-pin-wrap';
      url     = 'cargaAtenMasivMatSegPIN';
    }

    if (tipo == 'CENSADO')
    {
      context = 'content-censado-wrap';
      url     = 'cargaAtenMasivMatSegCen';
    }

    if (tipo == 'DESPLIEGUE')
    {
      context = 'content-despliegue-wrap';
      url     = 'cargaAtenMasivMatSegDesp';
    }

    if (tipo == 'HGU')
    {
      context = 'content-hgu-wrap';
      url     = 'cargaAtenMasivMatSegHGU';
    }

    if (tipo == 'STATUS')
    {
      context = 'content-status-wrap';
      url     = 'cargaAtenMasivMatSegST';
    }









    console.log(context);
    var files = $('#'+context).find('#archivo')[0].files[0];
    formData.append('file', files)
  
    Swal.queue([
    {
        title: "Está seguro de atender las solicitudes??",
        text: "Asegurese de validar la información",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return atenderSolPromiseMatriz(formData, url, context).then(function (data) { 
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




function atenderSolPromiseMatriz(formData, ruta, context)
{


    return new Promise(function (resolve, reject)
    {
      // var ruta = 'cargaAtenMasivaMatrizSeguimiento';

      

        $.ajax({
            type  : 'POST',
            url   : ruta,
            data  : formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                arrayDataGlob = [];
                $('#'+context).find('#archivo').val(null);
                $('#'+context).find('#contTabla').html(null);
                $('#'+context).find('#tituTbObs').text('');
                $('#'+context).find('#tituTbObs').css('display','none');
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}


//



jQuery(document).on('change', '.cboNodoMatriz',function(e)
  {
    e.preventDefault();
    var nodo = $(this).val();

    $.ajax({
        type : 'POST',
        url  : 'getCableByNodo',
        data : {nodo: nodo},
        dataType: 'json',
    }).done(function(response)
    {
        // console.log(response);
        if (response.total > 0)
        {
          var html = '';
          html += '<option value="0">Seleccione</option>';
          $.each(response.lista, function( index, value )
          {
            html += '<option value="'+value.cable+'">'+value.cable+'</option>';
          });

          $("#cbo_cable").html(html);
          $("#cbo_cable").trigger('change');
        }
    })

  })






jQuery(document).on('click', '#btnGuardarInfoPIN',function(e)
{
  e.preventDefault();

  var numHilos            = $("#numHilos").val(); 
  var FechaJumpeoCentral  = $("#FechaJumpeoCentral").val(); 
  var statusPin           = $("#statusPin").val(); 
  var nodo                = $(".cboNodoMatriz").val();
  var cable               = $("#cbo_cable").val();
  var divicauPin               = $("#divicauPin").val();


  $.ajax({
        type : 'POST',
        url  : 'saveMatSegInfoPIN',
        data : {numHilos: numHilos, FechaJumpeoCentral: FechaJumpeoCentral, statusPin: statusPin, cable:cable, nodo:nodo, divicau: divicauPin},
        dataType: 'json',
    }).done(function(response)
    {
        if (response.error == 0)
        {
          $("#btnBuscarMatrizSeguimientoFiltroPIN").click();
        }
    })


})

jQuery(document).on('click', '#btnBuscarMatrizSeguimientoFiltroPIN',function(e)
{
  e.preventDefault();
  var nodo = $(".cboNodoMatriz").val();
  var cable = $("#cbo_cable").val();


  $.ajax({
        type : 'POST',
        url  : 'getInfoCableNodo',
        data : {nodo: nodo, cable: cable},
        dataType: 'json',
    }).done(function(response)
    {
        var table = '';

        if (response.total == 0)
        {
          $("#contentwrap-pin").addClass('d-none');
          return false;
        }

        $("#contentwrap-pin").removeClass('d-none');
        $.each(response.lista, function( index, value )
        {

          var numHilosPuertoOLT   = (value.numHilosPuertoOLT == null)   ? '' : value.numHilosPuertoOLT;
          var FechaJumpeoCentral  = (value.FechaJumpeoCentral == null)  ? '' : value.FechaJumpeoCentral;
          var estadoPin           = (value.estadoPin == null)           ? '' : value.estadoPin;

          table += '<tr>';
          table += '<td>'+value.divicau+'</td>';
          table += '<td>'+value.uipHorizontalDiseno+'</td>';
          table += '<td>'+value.distrito+'</td>';
          table += '<td>'+value.provincia+'</td>';
          table += '<td>'+value.departamento+'</td>';
          table += '<td>'+numHilosPuertoOLT+'</td>';
          table += '<td>'+FechaJumpeoCentral+'</td>';
          table += '<td>'+estadoPin+'</td>';
          table += '</tr>';
        });

        $("#contentwrap-pin").find('tbody').html(table);
        var option = '<option value="">Seleccione</option>';
        $.each(response.divicau, function( index, value )
        {

          option += '<option value="'+value+'">'+value+'</option>';
        });

        $("#divicauPin").html(option);
    })
})