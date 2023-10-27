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
                                        <h2>MATRIZ DE SEGUIMIENTO</h2>
                                        
                                    </div>
                                    <div class="panel-container show">     
                                        <div class="panel-content">
                                            <div class="row">
                                                <div class="row w-100">
                                                  <div class="col-3">
                                                    <?php require_once 'application/views/vf_matriz_seguimiento/partials/tabs-index2.php';?> 
                                                    <!--  -->
                                                    <!-- Tab navs -->
                                                  </div>
                                                  <div class="col-9">
                                                    <!-- Tab content -->
                                                    <div id="v-tabs-tabContent">
                                                            
                                                      <div class="tab-pane section-1 fade show active" id="v-tabs-home" role="tabpanel" aria-labelledby="v-tabs-home-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Diseño</h5>
                                                                  <div class="card-body">


                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_direction-1" role="tab">Busqueda Individual</a></li>
                                                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_direction-2" role="tab">Carga Masiva</a></li>
                                                                    </ul>
                                                                    <div class="tab-content p-3" id="content-diseno-wrap" style="border: 1px solid #6ab5b4; margin-top: -1px; border-radius: 3px;">
                                                                        <div class="tab-pane fade show active" id="tab_direction-1" role="tabpanel">


                                                                            <div class="card mb-3 p-0">

                                                                                  <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                                                  <div class="card-body">
                                                                                    <form method="POST" name="frmBuscarMatrizSeguimientoDiseno" id="frmBuscarMatrizSeguimientoDiseno">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                                <label>ItemPlan</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control" name="input_itemplan" id="input_itemplan" autocomplete="off">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-3 nm-3">
                                                                                                <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltro" 
                                                                                                data-form="frmBuscarMatrizSeguimientoDiseno" data-context="contentwrap-diseno">Buscar</button>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-diseno">
                                                                                                <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-diseno">Log</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mt-3">
                                                                                
                                                                            
                                                                                <div id="contentwrap-diseno" class="d-none">
                                                                                    <input type="hidden" name="_id" id="_id" value="0">
                                                                                    <div class="">

                                                                                              <div class="row mt-3"> 
                                                                                                  <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                                        <label>ItemPlan</label>
                                                                                                        <input type="text" class="form-control" name="itemplan" id="itemplan" readonly>
                                                                                                    </div>
                                                                                                  <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                                    <label>Año del Proyecto</label>
                                                                                                    <input type="text" class="form-control" name="anio" id="anio" readonly>
                                                                                                </div>
                                                                                                <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                                    <label>Código único</label>
                                                                                                    <input type="text" class="form-control" name="divicau" id="divicau" readonly>
                                                                                                </div>
                                                                                                <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                                    <label>Plan del proyecto</label>
                                                                                                    <input type="text" class="form-control" name="plan" id="plan" readonly>
                                                                                                </div>
                                                                                                <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                                    <label>Nombre de Nodo</label>
                                                                                                    <input type="text" class="form-control" name="nodo" id="nodo" readonly>
                                                                                                </div>
                                                                                                <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                                    <label>EECC</label>
                                                                                                    <input type="text" class="form-control" name="empresaColab" id="empresaColab" readonly>
                                                                                                </div>



                                                                                              </div>
                                                                                            <hr>
                                                                                            
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mt-3">
                                                                                <table class="table table-bordered d-none" id="tblDiseno">
                                                                                    <thead class="bg-primary text-white">
                                                                                        <tr>
                                                                                            <th>DIVICAU</th>
                                                                                            <th>UIP</th>
                                                                                            <th>MODELO</th>
                                                                                            <th>CABLE</th>
                                                                                            <th>NODO</th>
                                                                                            <th>FECHA TERMINO</th>
                                                                                            <th>NOMBRE TROBA</th>
                                                                                            <th>ESTATUS DISEÑO</th>
                                                                                            <th>DEPARTAMENTO</th>
                                                                                            <th>PROVINCIA</th>
                                                                                            <th>DISTRITO</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                            <td>-</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>

                                                                        </div>
                                                                        <div class="tab-pane fade" id="tab_direction-2" role="tabpanel">
                                                                            


                                                                            <div class="row">
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                                    <label class="form-label" for="inputGroupFile01">CARGA MASIVA MATRIZ SEGUIMIENTO (DISEÑO)</label>                                        
                                                                                    
                                                                                    <div class="input-group">
                                                                                        <div class="custom-file">
                                                                                            <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo" accept=".xls">
                                                                                            <label class="custom-file-label" for="archivo">Seleccione Archivo</label>
                                                                                        </div>
                                                                                        <div class="input-group-append">
                                                                                            
                                                                                            <button type="button" class="btn btn-outline-primary waves-effect waves-themed" onclick="procesarFileMatriz('DISENO')" id="btnProcesar">
                                                                                                <span class="fal fa-eject mr-1"></span>
                                                                                                Procesar
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" style="text-align: center;">
                                                                                    <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="cargarFileMatriz('DISENO')" id="btnRegPO" style="margin-top: 25px;color:white">REGISTRAR VALORES</button>
                                                                                </div>
                                                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    (*) La estructura del archivo debe en formato .csv delimitado por ";" Puede descargar un ejemplo de la estructura.
                                                                                    <a onclick="exportarFormatoCargaMatrizJumpeo('DISEÑO')" style="color: #e9426e;cursor: pointer;">Aquí</a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <h5 id="tituTbObs" style="color:red; display: none; text-align: center;">Cantidad de registros a subir: </h5>
                                                                                        <div id="contTabla" class="table-responsive">
                                                                                        <!-- inicio cont tb po-->
                                                                                            <?php echo isset($tbObservacion) ? $tbObservacion : null ?>
                                                                                        <!-- fin cont tb po -->
                                                                                            
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-12">
                                                                                        <article class="mt-4" style="width:500px">
                                                                                            <center>
                                                                                                <h3>LEYENDA</h3>
                                                                                            </center>
                                                                                            <ul style="margin-left: 100px;">
                                                                                                <li>AÑO DEL PROYECTO (Debe ser solo número)</li>
                                                                                                <li>MODELO, Los valores permitidos son (FUS, PREC)</li>
                                                                                                <li>TIPO DIVICAU, los valores permitidos son (DIVICAU,HUB-BOX)</li>
                                                                                                <li>ESTATUS DISEÑO, los valores permitidos son ()</li>
                                                                                            </ul>
                                                                                        </article>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>
                                                      <div class="tab-pane fade section-2" id="v-tabs-profile" role="tabpanel" aria-labelledby="v-tabs-profile-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Económicos</h5>
                                                                  <div class="card-body">
                                                                        <ul class="nav nav-tabs" role="tablist">
                                                                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_direction-ec1" role="tab">Busqueda Individual</a></li>
                                                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_direction-ec2" role="tab">Carga Masiva</a></li>
                                                                        </ul>
                                                                        <div class="tab-content p-3" id="content-economico-wrap" style="border: 1px solid #6ab5b4; margin-top: -1px; border-radius: 3px;">
                                                                            <div class="tab-pane fade show active" id="tab_direction-ec1" role="tabpanel">




                                                                                <div class="card mb-3 p-0">

                                                                                  <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                                                  <div class="card-body">
                                                                                        <!-- <form></form> -->
                                                                                        <form method="POST" name="frmBuscarMatrizSeguimientoeconomico" id="frmBuscarMatrizSeguimientoeconomico">
                                                                                            <div class="row">
                                                                                                <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                                    <label>ItemPlan</label>
                                                                                                    <div class="input-group">
                                                                                                        <input type="text" class="form-control" name="input_itemplan" id="input_itemplan" autocomplete="off">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="mt-4 col-sm-3 nm-3">
                                                                                                    <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltro" 
                                                                                                data-form="frmBuscarMatrizSeguimientoeconomico" data-context="contentwrap-economico">Buscar</button>
                                                                                                </div>
                                                                                                <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-economico">
                                                                                                    <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-economico">Log</button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <div class="mt-4">
                                                                                    <div id="contentwrap-economico" class="d-none">
                                                                                        <table class="table table-bordered">
                                                                                            <thead class="bg-primary-600">
                                                                                                <tr>
                                                                                                    <th>Confirmacion de aprobacion de ppto</th>
                                                                                                    <th>N° PEP</th>
                                                                                                    <th>N° OC</th>
                                                                                                    <th>Generacion de VR</th>
                                                                                                    <th>Estatus de OC</th>
                                                                                                    <th>Certificacion OC</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody></tbody>
                                                                                        </table>
                                                                                        
                                                                                    </div>
                                                                                </div>


                                                                            </div>
                                                                            <div class="tab-pane fade" id="tab_direction-ec2" role="tabpanel">
                                                                                <div class="row">
                                                                                    <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                                        <label class="form-label" for="inputGroupFile01">CARGA MASIVA MATRIZ SEGUIMIENTO (ECONOMICO)</label>                                        
                                                                                        
                                                                                        <div class="input-group">
                                                                                            <div class="custom-file">
                                                                                                <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo" accept=".xls">
                                                                                                <label class="custom-file-label" for="archivo">Seleccione Archivo</label>
                                                                                            </div>
                                                                                            <div class="input-group-append">
                                                                                                
                                                                                                <button type="button" class="btn btn-outline-primary waves-effect waves-themed" onclick="procesarFileMatriz('ECONOMICO')" id="btnProcesar">
                                                                                                    <span class="fal fa-eject mr-1"></span>
                                                                                                    Procesar
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" style="text-align: center;">
                                                                                        <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="cargarFileMatriz('ECONOMICO')" id="btnRegPO" style="margin-top: 25px;color:white">REGISTRAR VALORES</button>
                                                                                    </div>
                                                                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                        (*) La estructura del archivo debe en formato .csv delimitado por ";" Puede descargar un ejemplo de la estructura.
                                                                                        <a onclick="exportarFormatoCargaMatrizJumpeo('ECONOMICO')" style="color: #e9426e;cursor: pointer;">Aquí</a>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="">
                                                                                    <div class="row">
                                                                                        <div class="col-sm-12">
                                                                                            <h5 id="tituTbObs" style="color:red; display: none; text-align: center;">Cantidad de registros a subir: </h5>
                                                                                            <div id="contTabla" class="table-responsive">
                                                                                            <!-- inicio cont tb po-->
                                                                                                <?php echo isset($tbObservacion) ? $tbObservacion : null ?>
                                                                                            <!-- fin cont tb po -->
                                                                                            
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-12">
                                                                                            <article class="mt-4" style="width:500px">
                                                                                                <center>
                                                                                                    <h3>LEYENDA</h3>
                                                                                                </center>
                                                                                                <ul style="margin-left: 100px;">
                                                                                                    <li>CONFIRMACION PPTO(Los valores permitidos deben ser SI, NO)</li>
                                                                                                    <li>STATUS DE OC, Los valores permitidos son (PENDIENTE, ATENDIDO, EN GETEC)</li>
                                                                                                    <li>N° PEP, debe ser un valor númerico</li>
                                                                                                    <li>N° OC, debe ser un valor único no repetido()</li>
                                                                                                    <li>GENERACION VR, Los valores permitidos son(PENDIENTE DE STOCK, COMPLETO, PARCIAL)</li>
                                                                                                    <li>CERTIFICACION OC, Los valores permitidos son(PENDIENTE, EN EJECUCION, CERTIFICADO)</li>
                                                                                                </ul>
                                                                                            </article>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>
                                                      <div class="tab-pane fade section-3" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Licencias</h5>
                                                                  <div class="card-body">
                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_licencia-ec1" role="tab">Busqueda Individual</a></li>
                                                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_licencia-ec2" role="tab">Carga Masiva</a></li>
                                                                    </ul>
                                                                    <div class="tab-content p-3" id="content-licencia-wrap" style="border: 1px solid #6ab5b4; margin-top: -1px; border-radius: 3px;">
                                                                        <div class="tab-pane fade show active" id="tab_licencia-ec1" role="tabpanel">
                                                                            <div class="card mb-3 p-0">

                                                                              <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                                              <div class="card-body">
                                                                                    <form method="POST" name="frmBuscarMatrizSeguimientoLicencia" id="frmBuscarMatrizSeguimientoLicencia">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                                <label>ItemPlan</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control" name="input_itemplan" id="input_itemplan" autocomplete="off">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-3 nm-3">
                                                                                                <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltro" 
                                                                                                data-form="frmBuscarMatrizSeguimientoLicencia" data-context="contentwrap-licencia">Buscar</button>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-licencia">
                                                                                                <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-licencia">Log</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>

                                                                            <div class="mt-4">
                                                                                <div id="contentwrap-licencia" class="d-none">
                                                                                    <table class="table table-bordered">
                                                                                        <thead class="bg-primary-600">
                                                                                            <tr>
                                                                                                <th>Divicau</th>
                                                                                                <th>Fecha de presentacion de expediente</th>
                                                                                                <th>Fecha de inicio para los trabajos horizontal</th>
                                                                                                <th>Estatus Licencia</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="tab_licencia-ec2" role="tabpanel">
                                                                            


                                                                            <div class="row">
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                                    <label class="form-label" for="inputGroupFile01">CARGA MASIVA MATRIZ SEGUIMIENTO (LICENCIA)</label>                                        
                                                                                    
                                                                                    <div class="input-group">
                                                                                        <div class="custom-file">
                                                                                            <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo" accept=".xls">
                                                                                            <label class="custom-file-label" for="archivo">Seleccione Archivo</label>
                                                                                        </div>
                                                                                        <div class="input-group-append">
                                                                                            
                                                                                            <button type="button" class="btn btn-outline-primary waves-effect waves-themed" onclick="procesarFileMatriz('LICENCIA')" id="btnProcesar">
                                                                                                <span class="fal fa-eject mr-1"></span>
                                                                                                Procesar
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" style="text-align: center;">
                                                                                    <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="cargarFileMatriz('LICENCIA')" id="btnRegPO" style="margin-top: 25px;color:white">REGISTRAR VALORES</button>
                                                                                </div>
                                                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    (*) La estructura del archivo debe en formato .csv delimitado por ";" Puede descargar un ejemplo de la estructura.
                                                                                    <a onclick="exportarFormatoCargaMatrizJumpeo('LICENCIA')" style="color: #e9426e;cursor: pointer;">Aquí</a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <h5 id="tituTbObs" style="color:red; display: none; text-align: center;">Cantidad de registros a subir: </h5>
                                                                                        <div id="contTabla" class="table-responsive">
                                                                                        <!-- inicio cont tb po-->
                                                                                            <?php echo isset($tbObservacion) ? $tbObservacion : null ?>
                                                                                        <!-- fin cont tb po -->
                                                                                            
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-12">
                                                                                        <article class="mt-4" style="width:500px">
                                                                                            <center>
                                                                                                <h3>LEYENDA</h3>
                                                                                            </center>
                                                                                            <ul style="margin-left: 100px;">
                                                                                                <li>FECHA PRESENTACION, (Debe ser formato fecha)</li>
                                                                                                <li>FECHA DE INICIO, (Debe ser formato fecha)</li>
                                                                                                <li>STATUS LICENCIA, los valores permitidos son (PENDIENTE,EN GESTION, CON LICENCIA)</li>>
                                                                                            </ul>
                                                                                        </article>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-4" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Logística</h5>
                                                                  <div class="card-body">


                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_logistica-ec1" role="tab">Busqueda Individual</a></li>
                                                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_logistica-ec2" role="tab">Carga Masiva</a></li>
                                                                    </ul>
                                                                    <div class="tab-content p-3" id="content-logistica-wrap" style="border: 1px solid #6ab5b4; margin-top: -1px; border-radius: 3px;">
                                                                        <div class="tab-pane fade show active" id="tab_logistica-ec1" role="tabpanel">
                                                                            <div class="card mb-3 p-0">

                                                                              <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                                              <div class="card-body">
                                                                                    <form method="POST" name="frmBuscarMatrizSeguimientoLogistica" id="frmBuscarMatrizSeguimientoLogistica">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                                <label>ItemPlan</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control" name="input_itemplan" id="input_itemplan" autocomplete="off">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-3 nm-3">
                                                                                                <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltro" 
                                                                                                data-form="frmBuscarMatrizSeguimientoLogistica" data-context="contentwrap-logistica">Buscar</button>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-logistica">
                                                                                                <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-logistica">Log</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="mt-4">
                                                                                <div id="contentwrap-logistica" class="d-none">
                                                                                    <table class="table table-bordered">
                                                                                        <thead class="bg-primary-600">
                                                                                            <tr>
                                                                                                <th>Divicau</th>
                                                                                                <th>Confirmacion de entrega de materiales</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="tab_logistica-ec2" role="tabpanel">
                                                                            

                                                                            <div class="row">
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                                    <label class="form-label" for="inputGroupFile01">CARGA MASIVA MATRIZ SEGUIMIENTO (LOGISTICA)</label>                                        
                                                                                    
                                                                                    <div class="input-group">
                                                                                        <div class="custom-file">
                                                                                            <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo" accept=".xls">
                                                                                            <label class="custom-file-label" for="archivo">Seleccione Archivo</label>
                                                                                        </div>
                                                                                        <div class="input-group-append">
                                                                                            
                                                                                            <button type="button" class="btn btn-outline-primary waves-effect waves-themed" onclick="procesarFileMatriz('LOGISTICA')" id="btnProcesar">
                                                                                                <span class="fal fa-eject mr-1"></span>
                                                                                                Procesar
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" style="text-align: center;">
                                                                                    <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="cargarFileMatriz('LOGISTICA')" id="btnRegPO" style="margin-top: 25px;color:white">REGISTRAR VALORES</button>
                                                                                </div>
                                                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    (*) La estructura del archivo debe en formato .csv delimitado por ";" Puede descargar un ejemplo de la estructura.
                                                                                    <a onclick="exportarFormatoCargaMatrizJumpeo('LOGISTICA')" style="color: #e9426e;cursor: pointer;">Aquí</a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <h5 id="tituTbObs" style="color:red; display: none; text-align: center;">Cantidad de registros a subir: </h5>
                                                                                        <div id="contTabla" class="table-responsive">
                                                                                        <!-- inicio cont tb po-->
                                                                                            <?php echo isset($tbObservacion) ? $tbObservacion : null ?>
                                                                                        <!-- fin cont tb po -->
                                                                                            
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-12">
                                                                                        <article class="mt-4" style="width:500px">
                                                                                            <center>
                                                                                                <h3>LEYENDA</h3>
                                                                                            </center>
                                                                                            <ul style="margin-left: 100px;">
                                                                                                <li>CONFIRMACION ENTREGA los valores permitidos son (DESPACHADO,  PENDIENTE, FALTA DE STOCK)</li>
                                                                                            </ul>
                                                                                        </article>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>




                                                                    
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-5" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">PIN</h5>
                                                                  <div class="card-body">
                                                                            <div class="mb-3">
                                                                                <form method="POST" name="frmBuscarMatrizSeguimientoPin" id="frmBuscarMatrizSeguimientoPin">
                                                                                    <div class="row">
                                                                                        <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                            <label>NODO</label>
                                                                                            <div class="input-group">
                                                                                                <select class="form-control select2 cboNodoMatriz" name="cbo_nodo" id="cbo_nodo">
                                                                                                    <option value="">Seleccione</option>
                                                                                                    <?php  
                                                                                                    if (count($nodos)> 0)
                                                                                                    {
                                                                                                        foreach ($nodos as $nodo)
                                                                                                        {
                                                                                                            echo '<option value="'.$nodo['nodo'].'">'.$nodo['nodo'].'</option>';
                                                                                                        }
                                                                                                    }
                                                                                                    ?>

                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                            <label>CABLE</label>
                                                                                            <div class="input-group">
                                                                                                <select class="form-control select2" name="cbo_cable" id="cbo_cable">
                                                                                                    <option value="">Seleccione</option>

                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="mt-4 col-sm-3 nm-3">
                                                                                            <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltroPIN" 
                                                                                            data-form="frmBuscarMatrizSeguimientoPin" data-context="contentwrap-pin">Buscar</button>
                                                                                        </div>
                                                                                        <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-status">
                                                                                            <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-status">Log</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>

                                                                            </div>

                                                                            <div class="mt-4">
                                                                                <div id="contentwrap-pin" class="d-none">
                                                                                    <div class="row">
                                                                                        <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                            <label>CANT HILOS</label>
                                                                                            <div class="input-group">
                                                                                                <input type="number" min="1"  autocomplete="off" class="form-control" name="numHilos" id="numHilos">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                            <label>FECHA JUMPEO</label>
                                                                                            <div class="input-group">
                                                                                                <input type="date" autocomplete="off" class="form-control" name="FechaJumpeoCentral" id="FechaJumpeoCentral">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                            <label>STATUS PIN</label>
                                                                                            <div class="input-group">
                                                                                                <select class="form-control select2" name="statusPin" id="statusPin">
                                                                                                    <option>Seleccione</option>
                                                                                                    <option value="JUMPEADO Y PINTADO EN ISP">JUMPEADO Y PINTADO EN ISP</option>
                                                                                                    <option value="JUMPEADO">JUMPEADO</option>
                                                                                                    <option value="PENDIENTE">PENDIENTE</option>
                                                                                                    <option value="PENDIENTE CUADRO DE ASIGNACIONES">PENDIENTE CUADRO DE ASIGNACIONES</option>
                                                                                                    <option value="PENDIENTE INSTALACION DE BANDEJA">PENDIENTE INSTALACION DE BANDEJA</option>
                                                                                                    <option value="PENDIENTE INSTALACION ODF">PENDIENTE INSTALACION ODF</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-2 col-md-2 mb-3" data-select2-id="18">
                                                                                            <div class="input-group mt-4">
                                                                                                <button class="btn btn-primary text-white ml-auto waves-effect waves-themed" type="button" id="btnGuardarInfoPIN">Guardar</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <table class="table table-bordered">
                                                                                        <thead class="bg-primary-600">
                                                                                            <tr>
                                                                                                <th>DIVICAU</th>
                                                                                                <th>UIP HORIZONTAL DISEÑO</th>
                                                                                                <th>DISTRITO</th>
                                                                                                <th>PROVINCIA</th>
                                                                                                <th>DEPARTAMENTO</th>
                                                                                                <th>CANT HILOS O PUERTO OLT</th>
                                                                                                <th>FECHA JUMPEO CENTRAL</th>
                                                                                                <th>STATUS PIN</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>










                                                                    
                                                                    
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-6" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Censado</h5>
                                                                  <div class="card-body">






                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_censado1" role="tab">Busqueda Individual</a></li>
                                                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_censado2" role="tab">Carga Masiva</a></li>
                                                                    </ul>
                                                                    <div class="tab-content p-3" id="content-censado-wrap" style="border: 1px solid #6ab5b4; margin-top: -1px; border-radius: 3px;">
                                                                        <div class="tab-pane fade show active" id="tab_censado1" role="tabpanel">
                                                                            <div class="card mb-3 p-0">

                                                                                  <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                                                  <div class="card-body">
                                                                                    <form method="POST" name="frmBuscarMatrizSeguimientoCensado" id="frmBuscarMatrizSeguimientoCensado">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                                <label>ItemPlan</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control" name="input_itemplan" id="input_itemplan" autocomplete="off">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-3 nm-3">
                                                                                                <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltro" 
                                                                                                data-form="frmBuscarMatrizSeguimientoCensado" data-context="contentwrap-censado">Buscar</button>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-censado">
                                                                                                <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-censado">Log</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>


                                                                            <div class="mt-4">
                                                                                <div id="contentwrap-censado" class="d-none">
                                                                                    <table class="table table-bordered">
                                                                                        <thead class="bg-primary-600">
                                                                                            <tr>
                                                                                                <th>Divicau</th>
                                                                                                <th>Fecha de censado</th>
                                                                                                <th>Cantidad UIP</th>
                                                                                                <th>Estado Censo</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="tab_censado2" role="tabpanel">
                                                                            


                                                                            <div class="row">
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                                    <label class="form-label" for="inputGroupFile01">CARGA MASIVA MATRIZ SEGUIMIENTO (CENSADO)</label>                                        
                                                                                    
                                                                                    <div class="input-group">
                                                                                        <div class="custom-file">
                                                                                            <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo" accept=".xls">
                                                                                            <label class="custom-file-label" for="archivo">Seleccione Archivo</label>
                                                                                        </div>
                                                                                        <div class="input-group-append">
                                                                                            
                                                                                            <button type="button" class="btn btn-outline-primary waves-effect waves-themed" onclick="procesarFileMatriz('CENSADO')" id="btnProcesar">
                                                                                                <span class="fal fa-eject mr-1"></span>
                                                                                                Procesar
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" style="text-align: center;">
                                                                                    <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="cargarFileMatriz('CENSADO')" id="btnRegPO" style="margin-top: 25px;color:white">REGISTRAR VALORES</button>
                                                                                </div>
                                                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    (*) La estructura del archivo debe en formato .csv delimitado por ";" Puede descargar un ejemplo de la estructura.
                                                                                    <a onclick="exportarFormatoCargaMatrizJumpeo('CENSADO')" style="color: #e9426e;cursor: pointer;">Aquí</a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <h5 id="tituTbObs" style="color:red; display: none; text-align: center;">Cantidad de registros a subir: </h5>
                                                                                        <div id="contTabla" class="table-responsive">
                                                                                        <!-- inicio cont tb po-->
                                                                                            <?php echo isset($tbObservacion) ? $tbObservacion : null ?>
                                                                                        <!-- fin cont tb po -->
                                                                                        
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-12">
                                                                                        <article class="mt-4" style="width:500px">
                                                                                            <center>
                                                                                                <h3>LEYENDA</h3>
                                                                                            </center>
                                                                                            <ul style="margin-left: 100px;">
                                                                                                <li>FECHA CENSADO (Debe ser formato fecha)</li>
                                                                                                <li>CANTIDAD UIP, Debe ser solo número</li>
                                                                                                <li>ESTADO DE CENSO, los valores permitidos son (PENDIENTE,TERMINADO)</li>
                                                                                            </ul>
                                                                                        </article>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>



                                                                    
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-7" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Despliegue</h5>
                                                                  <div class="card-body">




                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_despliegue1" role="tab">Busqueda Individual</a></li>
                                                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_despliegue2" role="tab">Carga Masiva</a></li>
                                                                    </ul>
                                                                    <div class="tab-content p-3" id="content-despliegue-wrap" style="border: 1px solid #6ab5b4; margin-top: -1px; border-radius: 3px;">
                                                                        <div class="tab-pane fade show active" id="tab_despliegue1" role="tabpanel">
                                                                            <div class="card mb-3 p-0">

                                                                                  <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                                                  <div class="card-body">
                                                                                    <form method="POST" name="frmBuscarMatrizSeguimientoDespliegue" id="frmBuscarMatrizSeguimientoDespliegue">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                                <label>ItemPlan</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control" name="input_itemplan" id="input_itemplan" autocomplete="off">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-3 nm-3">
                                                                                                <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltro" 
                                                                                                data-form="frmBuscarMatrizSeguimientoDespliegue" data-context="contentwrap-despliegue">Buscar</button>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-despliegue">
                                                                                                <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-despliegue">Log</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>

                                                                            <div class="mt-4">
                                                                                <div id="contentwrap-despliegue" class="d-none">
                                                                                    <table class="table table-bordered">
                                                                                        <thead class="bg-primary-600">
                                                                                            <tr>
                                                                                                <th>Divicau</th>
                                                                                                <!-- <th>Bandeja ODF</th> -->
                                                                                                <th>Fecha Instalación ODF</th>
                                                                                                <th>Fecha inicio construcción</th>
                                                                                                <th>Fecha Proyectada Entrega</th>
                                                                                                <th>Fecha final de entrega divicau</th>
                                                                                                <th>Estado Despliegue</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="tab_despliegue2" role="tabpanel">
                                                                            

                                                                            <div class="row">
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                                    <label class="form-label" for="inputGroupFile01">CARGA MASIVA MATRIZ SEGUIMIENTO (DESPLIEGUE)</label>                                        
                                                                                    
                                                                                    <div class="input-group">
                                                                                        <div class="custom-file">
                                                                                            <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo" accept=".xls">
                                                                                            <label class="custom-file-label" for="archivo">Seleccione Archivo</label>
                                                                                        </div>
                                                                                        <div class="input-group-append">
                                                                                            
                                                                                            <button type="button" class="btn btn-outline-primary waves-effect waves-themed" onclick="procesarFileMatriz('DESPLIEGUE')" id="btnProcesar">
                                                                                                <span class="fal fa-eject mr-1"></span>
                                                                                                Procesar
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" style="text-align: center;">
                                                                                    <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="cargarFileMatriz('DESPLIEGUE')" id="btnRegPO" style="margin-top: 25px;color:white">REGISTRAR VALORES</button>
                                                                                </div>
                                                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    (*) La estructura del archivo debe en formato .csv delimitado por ";" Puede descargar un ejemplo de la estructura.
                                                                                    <a onclick="exportarFormatoCargaMatrizJumpeo('DESPLIEGUE')" style="color: #e9426e;cursor: pointer;">Aquí</a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <h5 id="tituTbObs" style="color:red; display: none; text-align: center;">Cantidad de registros a subir: </h5>
                                                                                        <div id="contTabla" class="table-responsive">
                                                                                        <!-- inicio cont tb po-->
                                                                                            <?php echo isset($tbObservacion) ? $tbObservacion : null ?>
                                                                                        <!-- fin cont tb po -->
                                                                                            
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-12">
                                                                                        <article class="mt-4" style="width:500px">
                                                                                            <center>
                                                                                                <h3>LEYENDA</h3>
                                                                                            </center>
                                                                                            <ul style="margin-left: 100px;">
                                                                                                <li>CONFIRMACION PART DESPLIEGUE, (Debe ser formato fecha)</li>
                                                                                                <li>FECHA INSTALACION ODF, (Debe ser formato fecha)</li>
                                                                                                <li>FECHA DE INICIO CONSTRUCCION, (Debe ser formato fecha)</li>
                                                                                                <li>FECHA PROYECTADA ENTREGA, (Debe ser formato fecha)</li>
                                                                                                <li>FECHA ENTREGA DIVICAU, (Debe ser formato fecha)</li>
                                                                                                <li>STATUS DESPLIEGUE, los valores permitidos son (Sin inicio, En construccion, Ejecucion, Etapa de Pasivos, Mediciones, Pendiente Materiales, Terminado)</li>
                                                                                            </ul>
                                                                                        </article>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>




                                                                    
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-8" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">HGU</h5>
                                                                  <div class="card-body">



                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_hgu1" role="tab">Busqueda Individual</a></li>
                                                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_hgu2" role="tab">Carga Masiva</a></li>
                                                                    </ul>
                                                                    <div class="tab-content p-3" id="content-hgu-wrap" style="border: 1px solid #6ab5b4; margin-top: -1px; border-radius: 3px;">
                                                                        <div class="tab-pane fade show active" id="tab_hgu1" role="tabpanel">

                                                                            <div class="card mb-3 p-0">

                                                                                  <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                                                  <div class="card-body">
                                                                                    <form method="POST" name="frmBuscarMatrizSeguimientoHGU" id="frmBuscarMatrizSeguimientoHGU">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                                <label>ItemPlan</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control" name="input_itemplan" id="input_itemplan" autocomplete="off">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-3 nm-3">
                                                                                                <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltro" 
                                                                                                data-form="frmBuscarMatrizSeguimientoHGU" data-context="contentwrap-hgu">Buscar</button>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-hgu">
                                                                                                <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-hgu">Log</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mt-4">
                                                                                <div id="contentwrap-hgu" class="d-none">
                                                                                    <table class="table table-bordered">
                                                                                        <thead class="bg-primary-600">
                                                                                            <tr>
                                                                                                <th>Divicau</th>
                                                                                                <th>Fecha de Pruebas HGU</th>
                                                                                                <th>Avance de Pruebas HGU</th>
                                                                                                <th>Confirmación de ejecución de pruebas HGU</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="tab_hgu2" role="tabpanel">
                                                                            

                                                                            <div class="row">
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                                    <label class="form-label" for="inputGroupFile01">CARGA MASIVA MATRIZ SEGUIMIENTO (HGU)</label>                                        
                                                                                    
                                                                                    <div class="input-group">
                                                                                        <div class="custom-file">
                                                                                            <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo" accept=".xls">
                                                                                            <label class="custom-file-label" for="archivo">Seleccione Archivo</label>
                                                                                        </div>
                                                                                        <div class="input-group-append">
                                                                                            
                                                                                            <button type="button" class="btn btn-outline-primary waves-effect waves-themed" onclick="procesarFileMatriz('HGU')" id="btnProcesar">
                                                                                                <span class="fal fa-eject mr-1"></span>
                                                                                                Procesar
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" style="text-align: center;">
                                                                                    <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="cargarFileMatriz('HGU')" id="btnRegPO" style="margin-top: 25px;color:white">REGISTRAR VALORES</button>
                                                                                </div>
                                                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    (*) La estructura del archivo debe en formato .csv delimitado por ";" Puede descargar un ejemplo de la estructura.
                                                                                    <a onclick="exportarFormatoCargaMatrizJumpeo('HGU')" style="color: #e9426e;cursor: pointer;">Aquí</a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <h5 id="tituTbObs" style="color:red; display: none; text-align: center;">Cantidad de registros a subir: </h5>
                                                                                        <div id="contTabla" class="table-responsive">
                                                                                        <!-- inicio cont tb po-->
                                                                                            <?php echo isset($tbObservacion) ? $tbObservacion : null ?>
                                                                                        <!-- fin cont tb po -->
                                                                                            
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-12">
                                                                                        <article class="mt-4" style="width:500px">
                                                                                            <center>
                                                                                                <h3>LEYENDA</h3>
                                                                                            </center>
                                                                                            <ul style="margin-left: 100px;">
                                                                                                <li>FECHA DE PRUEBA DE HGU (Debe ser formato fecha)</li>
                                                                                                <li>AVANCE DE PRUEBAS HGU, (Debe ser solo número)</li>
                                                                                                <li>CONFIRMACION DE EJECUCION DE PRUEBAS HGU, los valores permitidos son (PENDIENTE,COMPLETO)</li>
                                                                                            </ul>
                                                                                        </article>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>





                                                                    
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-9" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Status</h5>
                                                                  <div class="card-body">



                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_status1" role="tab">Busqueda Individual</a></li>
                                                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_status2" role="tab">Carga Masiva</a></li>
                                                                    </ul>
                                                                    <div class="tab-content p-3" id="content-status-wrap" style="border: 1px solid #6ab5b4; margin-top: -1px; border-radius: 3px;">
                                                                        <div class="tab-pane fade show active" id="tab_status1" role="tabpanel">


                                                                            <div class="card mb-3 p-0">

                                                                                  <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                                                  <div class="card-body">
                                                                                    <form method="POST" name="frmBuscarMatrizSeguimientoStatus" id="frmBuscarMatrizSeguimientoStatus">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                                <label>ItemPlan</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control" name="input_itemplan" id="input_itemplan" autocomplete="off">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-3 nm-3">
                                                                                                <button type="button" class="btn btn-primary  text-white ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimientoFiltro" 
                                                                                                data-form="frmBuscarMatrizSeguimientoStatus" data-context="contentwrap-status">Buscar</button>
                                                                                            </div>
                                                                                            <div class="mt-4 col-sm-5 nm-3 d-none" id="content-log-status">
                                                                                                <button type="button" class="btn btn-warning  text-white ml-auto waves-effect waves-themed pr  btn-event-log btn-log-status">Log</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>

                                                                            <div class="mt-4">
                                                                                <div id="contentwrap-status" class="d-none">
                                                                                    <table class="table table-bordered">
                                                                                        <thead class="bg-primary-600">
                                                                                            <tr>
                                                                                                <th>Divicau</th>
                                                                                                <th>status Detallado</th>
                                                                                                <th>Estatus Global</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="tab_status2" role="tabpanel">
                                                                            


                                                                            <div class="row">
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                                                                    <label class="form-label" for="inputGroupFile01">CARGA MASIVA MATRIZ SEGUIMIENTO (STATUS)</label>                                        
                                                                                    
                                                                                    <div class="input-group">
                                                                                        <div class="custom-file">
                                                                                            <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo" accept=".xls">
                                                                                            <label class="custom-file-label" for="archivo">Seleccione Archivo</label>
                                                                                        </div>
                                                                                        <div class="input-group-append">
                                                                                            
                                                                                            <button type="button" class="btn btn-outline-primary waves-effect waves-themed" onclick="procesarFileMatriz('STATUS')" id="btnProcesar">
                                                                                                <span class="fal fa-eject mr-1"></span>
                                                                                                Procesar
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12" style="text-align: center;">
                                                                                    <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="cargarFileMatriz('STATUS')" id="btnRegPO" style="margin-top: 25px;color:white">REGISTRAR VALORES</button>
                                                                                </div>
                                                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    (*) La estructura del archivo debe en formato .csv delimitado por ";" Puede descargar un ejemplo de la estructura.
                                                                                    <a onclick="exportarFormatoCargaMatrizJumpeo('STATUS')" style="color: #e9426e;cursor: pointer;">Aquí</a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <h5 id="tituTbObs" style="color:red; display: none; text-align: center;">Cantidad de registros a subir: </h5>
                                                                                        <div id="contTabla" class="table-responsive">
                                                                                        <!-- inicio cont tb po-->
                                                                                            <?php echo isset($tbObservacion) ? $tbObservacion : null ?>
                                                                                        <!-- fin cont tb po -->
                                                                                            
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-12">
                                                                                        <article class="mt-4" style="width:500px">
                                                                                                <center>
                                                                                                    <h3>LEYENDA</h3>
                                                                                                </center>
                                                                                                <ul style="margin-left: 100px;">
                                                                                                    <li>STATUS FINAL, los valores permitidos son (Ejecucion , pendiente , pasivos , potencia , sin inicio y Terminado)</li>
                                                                                                    <li>ESTATUS GLOBAL, los valores permitidos son (En despliegue , sin inicio y terminado)</li>
                                                                                                </ul>
                                                                                            </article>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
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
            <div class="modal fade" id="modalDatosImpactados" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title text-center" id="title_vali">Datos Impactados</h1>
                        </div>
                        <br>
                        <div class="modal-body">
                            <div class="row">
                                <table class="table table-bordered" id="tblCamposImpactados">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>Campo</th>
                                            <th>Anterior</th>
                                            <th>Actual</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button id="botonAceptarModal"  class="btn btn-success">Perfecto</button>
                            <!-- <button class="btn btn-danger" data-dismiss="modal">Cancelar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'application/views/vf_matriz_seguimiento/modales/modalLog.php';?> 

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
        <!-- <script src="<?php echo base_url(); ?>public/js/js_orden_compra/jsBandejaSolicitudOc.js?v=<?php echo time();?>"></script> -->
        <script src="<?php echo base_url(); ?>public/js/js_matriz_seguimiento/jsMatrizSeguimiento.js?v=<?php echo time();?>"></script>
        
    </body>
    <!-- END Body -->
</html>
<script type="text/javascript">
     $('.select2').select2();
                $(document).ready(function() {
            });

</script>