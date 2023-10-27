<!DOCTYPE html>
<html lang="en"  class="root-text-sm">    
<head>
        <meta charset="utf-8">
        <title>
            PANGEACO    |   PLANTA INTERNA
        </title>  
        <meta name="description" content="Server Error">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="msapplication-tap-highlight" content="no">
        <link id="vendorsbundle" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/vendors.bundle.css">
        <link id="appbundle" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/app.bundle.css">
        <link id="mytheme" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/themes/<?php echo THEME_COLOR ?>">
        <link id="myskin" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/skins/skin-master.css">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url(); ?>public/img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>public/img/favicon/favicon-32x32.png">
        <link rel="mask-icon" href="<?php echo base_url(); ?>public/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/formplugins/select2/select2.bundle.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">

        <link rel="stylesheet" href="public/webfonts/font-awesome-4.7.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="<?php echo base_url();?>public/css/fa-duotone.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/datagrid/datatables/datatables.bundle.css">


        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.export.js"></script>


        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/loading/jquery.loading.min.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/notifications/sweetalert2/sweetalert2.bundle.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/utils/select2_theme.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/internas.css">

        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"> -->
        <style>
          
            .swal2-container {
                z-index: 10000;
            }
        </style>
    </head>
    <!-- BEGIN Body -->    
    <body class="<?php echo ESTILO_BODY ?>">
        <script>
            'use strict';

            var classHolder = document.getElementsByTagName("BODY")[0],
                themeSettings = (localStorage.getItem('themeSettings')) ? JSON.parse(localStorage.getItem('themeSettings')) :
                {},
                themeURL = themeSettings.themeURL || '',
                themeOptions = themeSettings.themeOptions || '';
            /** 
             * Load theme options
             **/
            if (themeSettings.themeOptions)
            {
                classHolder.className = themeSettings.themeOptions;
                console.log("%c✔ Theme settings loaded", "color: #148f32");
            }
            else
            {
                console.log("%c✔ Heads up! Theme settings is empty or does not exist, loading default settings...", "color: #ed1c24");
            }
            if (themeSettings.themeURL && !document.getElementById('mytheme'))
            {
                var cssfile = document.createElement('link');
                cssfile.id = 'mytheme';
                cssfile.rel = 'stylesheet';
                cssfile.href = themeURL;
                document.getElementsByTagName('head')[0].appendChild(cssfile);

            }
            else if (themeSettings.themeURL && document.getElementById('mytheme'))
            {
                document.getElementById('mytheme').href = themeSettings.themeURL;
            }
            /** 
             * Save to localstorage 
             **/
            var saveSettings = function()
            {
                themeSettings.themeOptions = String(classHolder.className).split(/[^\w-]+/).filter(function(item)
                {
                    return /^(nav|header|footer|mod|display)-/i.test(item);
                }).join(' ');
                if (document.getElementById('mytheme'))
                {
                    themeSettings.themeURL = document.getElementById('mytheme').getAttribute("href");
                };
                localStorage.setItem('themeSettings', JSON.stringify(themeSettings));
            }
            /** 
             * Reset settings
             **/
            var resetSettings = function()
            {
                localStorage.setItem("themeSettings", "");
            }

        </script>
        <!-- BEGIN Page Wrapper -->
        <div class="page-wrapper">
            <div class="page-inner">
                <!-- BEGIN Left Aside -->
                <aside class="page-sidebar">               
                    <!-- BEGIN PRIMARY NAVIGATION -->
                    <nav id="js-primary-nav" class="primary-nav" role="navigation">
                        <div class="nav-filter">
                            <div class="position-relative">
                                <input type="text" id="nav_filter_input" placeholder="Filter menu" class="form-control" tabindex="0">
                                <a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
                                    <i class="fal fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="info-card">
                            <img src="<?php echo base_url(); ?>public/img/demo/avatars/avatar-m.png" class="profile-image rounded-circle">
                            <div class="info-card-text">
                                <a href="#" class="d-flex align-items-center text-white">
                                    <span class="text-truncate text-truncate-sm d-inline-block">
                                        <?php echo $this->session->userdata('usernameSessionPan') ?>
                                    </span>
                                </a>
                                <span class="d-inline-block text-truncate text-truncate-sm"><?php echo $this->session->userdata('descPerfilSessionPan') ?></span>
                            </div>
                            <img src="<?php echo base_url(); ?>public/img/card-backgrounds/cover-2-lg.png" class="cover" alt="cover">
                            <a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
                                <i class="fal fa-angle-down"></i>
                            </a>
                        </div>
                        <?php echo $opciones ?>
                        <div class="filter-message js-filter-message bg-success-600"></div>
                    </nav>
                    <!-- END PRIMARY NAVIGATION -->
                   
                </aside>
                <!-- END Left Aside -->
                <div class="page-content-wrapper">
                    <!-- BEGIN Page Header -->
                    <?php echo $header ?>
                    <!-- END Page Header -->
                    <!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->
                    <main id="js-page-content" role="main" class="page-content">
                   
                    <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-1" class="panel">
                                    <div class="panel-hdr form-group">
                                        <h2>Programación / Aprobación de JUMPEOS</h2>
                                        
                                    </div>
                                    <div class="panel-container show">     
                                        <div class="panel-content">
                                            <div class="row mb-4" style="-20px;">                                              
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-6">                                                            
                                                    <div class="form-group">
                                                        <label class="form-label" for="txt_nom_pro">CODIGO SOLICITUD</label>
                                                        <input type="text" class="form-control" id="txt_codigo_solicitud" placeholder="Ingrese Codigo de Obra" required>
                                                        <div class="invalid-feedback">Ingrese codigo de Solicitud</div>
                                                    </div> 
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-6">  
                                                    <label>ESTADO</label>
                                                    <select class="select2" id="selectEstado" aria-label="usertype">
                                                        <option value="ALLx">SELECCIONE</option>
                                                        <option value="ALL">TODO</option>
                                                        <option value="PROGRAMADO">PROGRAMADO</option>
                                                        <option value="EJECUTADO">EJECUTADO</option>
                                                        <option value="JUMPEADO">JUMPEADO</option>
                                                        <option value="OBSERVADO">OBSERVADO</option>
                                                    </select>
                                                    <div class="invalid-tooltip">
                                                        Seleccionar Estado.
                                                    </div>
                                                </div>  
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-6">
                                                    <a style="" class="btn btn-md btn-outline-primary waves-effect waves-themed searchT mt-4" id="btnBuscarCodigoSolicitudbanPlIntJum">
                                                        <span class="fal fa-search mr-1"></span>
                                                        Buscar
                                                    </a> 
                                                </div>                                          
                                            </div>
                                            <div class="row">
                                                <div class="row w-100">
                                                  <div class="col-12">
                                                    <!-- Tab content -->
                                                    <div>
                                                      <div class="tab-pane section-1 fade show active" id="v-tabs-home" role="tabpanel" aria-labelledby="v-tabs-home-tab">
                                                          <div class>
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Detalle</h5>
                                                                  <div class="card-body">
                                                                        <!--
                                                                        <div class="row">

                                                                                  <div class="row mt-3"> 
                                                                                      <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Gestor</label>
                                                                                        <input type="text" class="form-control" name="gestor" id="gestor">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Zonal</label>
                                                                                        <input type="text" class="form-control" name="zonal" id="zonal">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Base</label>
                                                                                        <input type="text" class="form-control" name="base" id="base">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Fecha Solicitud</label>
                                                                                        <input type="date" class="form-control" name="fechaSolicitud" id="fechaSolicitud" >
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Etapa</label>
                                                                                        <input type="text" class="form-control" name="etapa" id="etapa" >
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>EECC</label>
                                                                                        <input type="text" class="form-control" name="empresaColab" id="empresaColab" >
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Proyecto</label>
                                                                                        <input type="text" class="form-control" name="proyecto" id="proyecto" >
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Itemplan</label>
                                                                                        <input type="text" class="form-control" name="itemplan" id="itemplan">
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Puerto Cto</label>
                                                                                        <input type="text" class="form-control" name="puertoCto" id="puertoCto">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>ODF</label>
                                                                                        <input type="text" class="form-control" name="odf" id="odf">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Bandeja</label>
                                                                                        <input type="text" min="0" class="form-control" name="bandeja" id="bandeja" >
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Cable</label>
                                                                                        <input type="text"class="form-control" name="cable" id="cable" >
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Hilo</label>
                                                                                        <input type="text"class="form-control" name="hilo" id="hilo" >
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Armario</label>
                                                                                        <input type="text"class="form-control" name="armario" id="armario" >
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Divicau</label>
                                                                                        <input type="text" class="form-control" name="divicau" id="divicau">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Secundario</label>
                                                                                        <input type="date" class="form-control" name="secundario" id="secundario">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Hilo Secundario</label>
                                                                                        <input type="text" class="form-control" name="hiloSecundario" id="hiloSecundario">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Spliter</label>
                                                                                        <input type="text" class="form-control" name="spliter" id="spliter">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>CTO</label>
                                                                                        <input type="text" class="form-control" name="cto" id="cto">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Cto X</label>
                                                                                        <input type="text" class="form-control" name="ctoX" id="ctoX">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Cto Y</label>
                                                                                        <input type="text" class="form-control" name="ctoY" id="ctoY">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Distrito</label>
                                                                                        <input type="text" class="form-control" name="distrito" id="distrito">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Edificios</label>
                                                                                        <input type="text" class="form-control" name="edificios" id="edificios">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Edificios/ Proyectos</label>
                                                                                        <input type="text" class="form-control" name="edifProyecto" id="edifProyecto">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Número</label>
                                                                                        <input type="text" class="form-control" name="numero" id="numero">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Edificio X</label>
                                                                                        <input type="text" class="form-control" name="edificioX" id="edificioX">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Edificio Y</label>
                                                                                        <input type="text" class="form-control" name="edificioY" id="edificioY">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Nodo</label>
                                                                                        <input type="text" class="form-control" name="nodo" id="nodo">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Contrata Pext</label>
                                                                                        <input type="text" class="form-control" name="contrataPext" id="contrataPext">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Codigo CMS</label>
                                                                                        <input type="text" class="form-control" name="codigoCMS" id="codigoCMS">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Nombre OLT</label>
                                                                                        <input type="text" class="form-control" name="nombre_olt" id="nombre_olt">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Shelf</label>
                                                                                        <input type="text" class="form-control" name="shelf" id="shelf">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Slot</label>
                                                                                        <input type="text" class="form-control" name="slot" id="slot">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Port</label>
                                                                                        <input type="text" class="form-control" name="port" id="port">
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>EECC PINT</label>
                                                                                        <input type="text" class="form-control" name="eeccpint" id="eeccpint">
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Estado</label>
                                                                                        <input type="text" class="form-control" name="estado" id="estado">
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Fecha Despacho EECC</label>
                                                                                        <input type="date" class="form-control" name="fechaDespachoEECC" id="fechaDespachoEECC">
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Fecha Termino</label>
                                                                                        <input type="date" class="form-control" name="fechaTermino" id="fechaTermino">
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>ItemPlan Pin OT</label>
                                                                                        <input type="text" class="form-control" name="itemplanPinOt" id="itemplanPinOt">
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Work Order ISP</label>
                                                                                        <input type="text" class="form-control" name="workOrderISP" id="workOrderISP">
                                                                                    </div>
                                                                                  </div>
                                                                                <hr>
                                                                                <div class>
                                                                                    <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarDiseno">Guardar Cambios</button>
                                                                                </div>
                                                                        </div>-->
                                                                    
                                                                    <table  id="dt-basic-example" class="table table-bordered table-sm table-hover table-striped w-100">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>N°</th>
                                                                                <th>Codigo Solicitud</th>
                                                                                <th>Item Plan Pext</th>
                                                                                <th>Sub Proyecto Pext</th>
                                                                                <th>Nodo</th>
                                                                                <th>EECC Pext</th>
                                                                                <th>Itemplan PINT</th>
                                                                                <th>Sub Proyecto PINT</th>
                                                                                <th>EECC PINT</th>
                                                                                <th>Estado</th>
                                                                                <th>Evidencia</th>
                                                                                <th>Acciones</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody></tbody>
                                                                    </table>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>
                                                    </div>
                                                    <!-- Tab content -->
                                                  </div>
                                                </div>
                                            </div>
                                            <div id="contTablaSolicitudOc">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                    <!-- this overlay is activated only when mobile menu is triggered -->

                    <!-- END Shortcuts -->
                    <!-- BEGIN Color profile -->
                    <!-- this area is hidden and will not be seen on screens or screen readers -->                    
                </div>
            </div>
        </div>


        <div id="modales">
            <div class="modal fade" id="modalx" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title text-center" id="title_vali">Datos Impactados</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                        </div>
                        <br>
                        <div class="modal-body">
                            <form name="frmMatrizJumpeoUpdate" id="frmMatrizJumpeoUpdate" method="POST">
                                <input type="hidden" name="id" id="id" value="0">
                                <div class="row">
                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>Código CMS</label>
                                        <input type="text" class="form-control" name="codigoCMS" id="codigoCMS" readonly>
                                    </div>
                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>Shelf</label>
                                        <input type="text" class="form-control" name="shelf" id="shelf" readonly>
                                    </div>
                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>Slot</label>
                                        <input type="text" class="form-control" name="slot" id="slot" readonly>
                                    </div>
                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>Port</label>
                                        <input type="text" class="form-control" name="port" id="port" readonly>
                                    </div>
                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>Nombre OLT</label>
                                        <input type="text" class="form-control" name="nombre_olt" id="nombre_olt" readonly>
                                    </div>

                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>EECC PINT</label>
                                        <input type="text" class="form-control" name="eeccpint" id="eeccpint" readonly>
                                    </div>

                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>Fecha Despacho EECC</label>
                                        <input type="date" class="form-control" name="fechaDespachoEECC" id="fechaDespachoEECC" readonly>
                                    </div>

                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>Fecha Termino</label>
                                        <input type="date" class="form-control" name="fechaTermino" id="fechaTermino">
                                    </div>

                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>ItemPlan Pin OT</label>
                                        <input type="text" class="form-control" name="itemplanPinOt" id="itemplanPinOt">
                                    </div>

                                    <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                        <label>Work Order ISP</label>
                                        <input type="text" class="form-control" name="workOrderISP" id="workOrderISP" readonly>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button id="btn-guardar-x"  class="btn btn-success">Programar</button>
                            <button class="btn-dismis-modal btn btn-danger" id="btnrechazar" data-dismiss="modal">Rechazar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalx3" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title text-center" id="title_vali">Datos Impactados</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                        </div>
                        <br>
                        <div class="modal-body">
                            <form name="frmMatrizJumpeoUpdate3" id="frmMatrizJumpeoUpdate3" method="POST">
                                <input type="hidden" name="id" id="id" value="0">
                                <div class="row">

                                    <div class="col-sm-12 col-md-12 mb-3" data-select2-id="18">
                                        <label>Comentario Observado</label>
                                        <textarea class="form-control" name="comentario" id="comentario" rows="4" cols="10"></textarea>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button id="btn-guardar-x3"  class="btn btn-success">Jumpear</button>
                            <button class="btn btn-danger" id="btnObservar" data-dismiss="modal">Observar</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once 'application/views/vf_matriz_jumpeo/modals/modalLog.php';?> 
            <?php require_once 'application/views/vf_matriz_jumpeo/modals/v_modal-all-table.php';?> 
        </div>


        <!-- END Page Wrapper -->
         

        <!-- END Page Settings -->     
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

        <script src="<?php echo base_url(); ?>public/js/vendors.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/app.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/select2/select2.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.export.js"></script>
        <script src="<?php echo base_url(); ?>public/js/loading/jquery.loading.min.js"></script>
        <script src="<?php echo base_url(); ?>public/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/Utils.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/js_orden_compra/jsBandejaSolicitudOc.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/js_matriz_jumpeo/jsMatrizJumpeo.js?v=<?php echo time();?>"></script>
        
    </body>
    <!-- END Body -->
</html>
<script type="text/javascript">
     $(document).ready(function()
    {
        initTableLight(<?php echo json_encode($matriz)?>);
        $('.select2').select2();
    });
</script>