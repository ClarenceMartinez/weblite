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
                                                    <div class="card mb-3">

                                                          <h5 class="card-header bg-primary text-white">Filtro</h5>
                                                          <div class="card-body">
                                                            <form method="POST" name="frmBuscarMatrizSeguimiento" id="frmBuscarMatrizSeguimiento">
                                                                <div class="row">
                                                                    <div class="col-sm-12 col-md-12 mb-3" data-select2-id="18">
                                                                        <label>ItemPlan</label>
                                                                        

                                                                        <div class="input-group">
                                                                           
                                                                            <input type="text" class="form-control" name="input_itemplan" id="input_itemplan">
                                                                            <div class="input-group-append">
                                                                                <!-- <a href=""></a> -->
                                                                                <span class="input-group-text fs-xl" style="cursor: pointer;" id="btnSearchItemPlan">
                                                                                    <i class="fal fa-search-plus"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 col-md-12 mb-3" data-select2-id="18">
                                                                        <label>DIVICAU</label>
                                                                        <select class="form-control select2" name="cbodivicau" id="cbodivicau">
                                                                            <option value="0">-- Seleccione --</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="mt-3">
                                                                    <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnBuscarMatrizSeguimiento">Buscar</button>
                                                                    <!-- <a href="getLoadMatSeg" class="btn btn-default ml-auto waves-effect waves-themed">Cargar Matriz Excel</a> -->
                                                                    <a href="javascript:;" class="btn btn-warning ml-auto waves-effect waves-themed d-none" id="btnVerLogsMatriz" data-origin="Ver Logs" data-chenge="Ocultar Logs">Ver Logs</a>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>




                                                    <!-- Tab navs -->
                                                    <div
                                                      class="nav flex-column nav-tabs text-center d-none" id="v-tabs-tab" role="tablist" aria-orientation="vertical">



                                                      <a class="nav-link active" data-section="section-1" id="v-tabs-home-tab" data-mdb-toggle="tab" href="#v-tabs-home" role="tab" aria-controls="v-tabs-home" aria-selected="true" >Diseño</a>
                                                      <a class="nav-link" data-section="section-2" id="v-tabs-profile-tab" data-mdb-toggle="tab" href="#v-tabs-profile" role="tab" aria-controls="v-tabs-profile" aria-selected="false" >Económicos</a>
                                                      <a class="nav-link" data-section="section-3" id="v-tabs-messages-tab" data-mdb-toggle="tab" href="#v-tabs-messages" role="tab" aria-controls="v-tabs-messages" aria-selected="false"
                                                        >Licencias</a>


                                                        <a class="nav-link" data-section="section-4" id="v-tabs-messages-tab" data-mdb-toggle="tab" href="#v-tabs-messages" role="tab" aria-controls="v-tabs-messages" aria-selected="false"
                                                        >Logística</a>

                                                        <a class="nav-link" data-section="section-5" id="v-tabs-messages-tab" data-mdb-toggle="tab" href="#v-tabs-messages" role="tab" aria-controls="v-tabs-messages" aria-selected="false"
                                                        >PIN</a>

                                                        <a class="nav-link" data-section="section-6" id="v-tabs-messages-tab" data-mdb-toggle="tab" href="#v-tabs-messages" role="tab" aria-controls="v-tabs-messages" aria-selected="false"
                                                        >Censado</a>

                                                        <a class="nav-link" data-section="section-7" id="v-tabs-messages-tab" data-mdb-toggle="tab" href="#v-tabs-messages" role="tab" aria-controls="v-tabs-messages" aria-selected="false"
                                                        >Despliegue</a>

                                                        <a class="nav-link" data-section="section-8" id="v-tabs-messages-tab" data-mdb-toggle="tab" href="#v-tabs-messages" role="tab" aria-controls="v-tabs-messages" aria-selected="false"
                                                        >HGU</a>

                                                        <a class="nav-link" data-section="section-9" id="v-tabs-messages-tab" data-mdb-toggle="tab" href="#v-tabs-messages" role="tab" aria-controls="v-tabs-messages" aria-selected="false"
                                                        >Status</a>
                                                    </div>
                                                    <!-- Tab navs -->
                                                  </div>



                                                  <div class="col-9">
                                                    <!-- Tab content -->
                                                    <div id="v-tabs-tabContent" class="d-none">
                                                            
                                                        <div class="tab-pane section-log fade d-none" id="v-tabs-logs" role="tabpanel" aria-labelledby="v-tabs-home-tab">
                                                          <div class="">
                                                              <div class="card">
                                                                  <h5 class="card-header bg-primary text-white">Logs</h5>
                                                                  <div class="card-body">
                                                                      <div class="row">
                                                                          <div class="col-sm-12">
                                                                                <div class="content-table-dt" style="overflow: auto;">
                                                                                    
                                                                                
                                                                              <table id="dt-basic-example" class="table table-bordered table-sm table-hover table-striped w-100">
                                                                                  <thead>
                                                                                      <tr>
                                                                                          <th>N°</th>
                                                                                          <th>Módulo</th>
                                                                                          <th>Usuario</th>
                                                                                          <th>Fecha registro</th>
                                                                                          <th>Datos Impactados</th>
                                                                                      </tr>
                                                                                  </thead>
                                                                                  <tbody>
                                                                                      
                                                                                  </tbody>
                                                                              </table>
                                                                                </div>
                                                                                
                                                                            </div>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                        </div>





                                                      <div class="tab-pane section-1 fade show active" id="v-tabs-home" role="tabpanel" aria-labelledby="v-tabs-home-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Diseño</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoDiseno" id="frmMatrizSeguimientoDiseno">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">

                                                                                  <div class="row mt-3"> 
                                                                                      <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Año del Proyecto</label>
                                                                                        <input type="text" class="form-control" name="anio" id="anio">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Código único del Divicau</label>
                                                                                        <input type="text" class="form-control" name="divicau" id="divicau">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Plan del proyecto</label>
                                                                                        <input type="text" class="form-control" name="plan" id="plan">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Numero de ItemPlan</label>
                                                                                        <input type="text" class="form-control" name="itemplan" id="itemplan" readonly="">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Nombre de Nodo</label>
                                                                                        <input type="text" class="form-control" name="nodo" id="nodo" readonly>
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>EECC que ejecutara el diseño</label>
                                                                                        <input type="text" class="form-control" name="empresaColab" id="empresaColab" readonly>
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Modelo de Divicau</label>
                                                                                        <select class="form-control select2" name="modelo" id="modelo">
                                                                                            <option value="">Seleccione</option>
                                                                                            <option value="FUS">FUS</option>
                                                                                            <option value="PREC">PREC</option>
                                                                                        </select>
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Nombre de Cable</label>
                                                                                        <input type="text" class="form-control" name="cable" id="cable">
                                                                                    </div>

                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Tipo de Divicau</label>
                                                                                        <select class="form-control select2" name="tipo" id="tipo">
                                                                                            <option value="">Seleccione</option>
                                                                                            <option value="DIVICAU">DIVICAU</option>
                                                                                            <option value="HUB-BOX">HUB-BOX</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Nombre de Troba</label>
                                                                                        <input type="text" class="form-control" name="troba" id="troba">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Cantidad de UIP Horizontal</label>
                                                                                        <input type="text" min="0" class="form-control" name="uipHorizonal" id="uipHorizonal" readonly>
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Distrito de Divicau</label>
                                                                                        <input type="text"class="form-control" name="distrito" id="distrito" readonly>
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Provincia de Divicau</label>
                                                                                        <input type="text"class="form-control" name="provincia" id="provincia" readonly>
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Departamento de Divicau</label>
                                                                                        <input type="text"class="form-control" name="departamento" id="departamento" readonly>
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Fecha de adjudicacion de diseño</label>
                                                                                        <input type="date" class="form-control" name="fechaAdjudicaDiseno" id="fechaAdjudicaDiseno">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Fecha de Cierre en OSP</label>
                                                                                        <input type="date" class="form-control" name="fechaCierreDisenoExpediente" id="fechaCierreDisenoExpediente">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Fecha de entrega de Diseño</label>
                                                                                        <input type="date" class="form-control" name="fechaEntregaDiseno" id="fechaEntregaDiseno">
                                                                                    </div>
                                                                                    <div class="col-sm-3 col-md-3 mb-3" data-select2-id="18">
                                                                                        <label>Estatus de Diseño</label>
                                                                                        <select class="form-control select2" name="estadoDiseno" id="estadoDiseno">
                                                                                            <option value="">Seleccione</option>
                                                                                            <option value="Diseño/Expediente">Diseño/Expediente</option>
                                                                                            <option value="Carga en OSP">Carga en OSP</option>
                                                                                            <option value="Liquidacion en Weblight">Liquidacion en Weblight</option>
                                                                                            <option value="Pediente">Pediente</option>
                                                                                            <option value="Terminado">Terminado</option>
                                                                                            <option value="Trunco">Trunco</option>
                                                                                        </select>
                                                                                    </div>
                                                                                  </div>
                                                                                <hr>
                                                                                <div class="">
                                                                                    <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarDiseno">Guardar Cambios</button>
                                                                                </div>
                                                                        </div>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>
                                                      <div class="tab-pane fade section-2" id="v-tabs-profile" role="tabpanel" aria-labelledby="v-tabs-profile-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Económicos</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoEconomico" id="frmMatrizSeguimientoEconomico">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Confirmacion de aprobacion de ppto</label>
                                                                                <select class="form-control select2" name="pptoAprobado" id="pptoAprobado">
                                                                                    <option value="">Seleccione</option>
                                                                                    <option value="1">Si</option>
                                                                                    <option value="0">No</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Numero de Pep de Mano de Obra de cada Item Plan</label>
                                                                                <input type="text" class="form-control" name="pep" id="pep">
                                                                            </div>
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Numero de Orden de compra de la Construccion Horizontal ItemPlan</label>
                                                                                <input type="text" class="form-control" name="ocConstruccionH" id="ocConstruccionH">
                                                                            </div>
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Generacion de VR</label>
                                                                                <select class="form-control select2" name="generacionVR" id="generacionVR">
                                                                                    <option value="">Seleccione</option>
                                                                                    <option value="COMPLETO">Completo</option>
                                                                                    <option value="PARCIAL">Parcial</option>
                                                                                    <option value="PENDIENTE DE STOCK">Pendiente de Stock</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Estatus de OC</label>
                                                                                <select class="form-control select2" name="estadoOC" id="estadoOC">
                                                                                    <option value="">Seleccione</option>
                                                                                    <option value="ATENDIDO">Atendido</option>
                                                                                    <option value="EN GETEC">En GETEC</option>
                                                                                    <option value="PENDIENTE">Pendiente</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Certificacion de la OC Horizontal</label>
                                                                                <select class="form-control select2" name="estadoCertificaOC" id="estadoCertificaOC">
                                                                                    <option value="PENDIENTE">Seleccione</option>
                                                                                    <option value="CERTIFICADO">Certificado</option>
                                                                                    <option value="EN EJECUCION">En Ejecución</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="">
                                                                            <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarEconomico">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>
                                                      <div class="tab-pane fade section-3" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Licencias</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoLicencias" id="frmMatrizSeguimientoLicencias">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Fecha de presentacion de expediente a la entidad</label>
                                                                                <input type="date" class="form-control" name="fechaPresentaLicencia" id="fechaPresentaLicencia" value="" min="">
                                                                            </div>
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Fecha de inicio para los trabajos horizontal</label>
                                                                                <input type="date" class="form-control" name="fechaInicioLicencia" id="fechaInicioLicencia" value="" min="">
                                                                            </div>
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Estatus Licencia</label>
                                                                                <select class="form-control select2" name="estadoLicencia" id="estadoLicencia">
                                                                                    <option value="">Seleccione</option>
                                                                                    <option value="PENDIENTE">Pendiente de Diseño</option>
                                                                                    <option value="EN GESTION">En Gestión de licencias</option>
                                                                                    <option value="CON LICENCIA">Con Licencia</option>
                                                                                </select>
                                                                            </div>
                                                                            
                                                                        </div>
                                                                        <hr>
                                                                        <div class="">
                                                                            <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarLicencia">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-4" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Logística</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoLogistica" id="frmMatrizSeguimientoLogistica">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Confirmacion de entrega de materiales</label>
                                                                                <select class="form-control select2" name="entregaMateriales" id="entregaMateriales">
                                                                                    <option value="">Seleccione</option>
                                                                                    <option value="DESPACHADO">DESPACHADO</option>
                                                                                    <option value="ENTREGADO">ENTREGADO</option>
                                                                                </select>
                                                                            </div>
                                                                            

                                                                        </div>
                                                                        <hr>
                                                                        <div class="">
                                                                            <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarLogistica">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-5" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">PIN</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoPIN" id="frmMatrizSeguimientoPIN">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Cantidad de Hilos o Puertos OLT , se extrae por nombre de Cable</label>
                                                                                <input type="number" class="form-control" name="numHilosPuertoOLT" id="numHilosPuertoOLT">
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Fecha de Jumpeo en la central por parte del area de PIN</label>
                                                                                <input type="date" class="form-control" name="FechaJumpeoCentral" id="FechaJumpeoCentral">
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Estatus Planta Interna</label>
                                                                                <select class="form-control select2" name="estadoPin" id="estadoPin">
                                                                                    <option value="PENDIENTE">Seleccione</option>
                                                                                    <option value="JUMPEADO Y PINTADO EN ISP">JUMPEADO Y PINTADO EN ISP</option>
                                                                                    <option value="JUMPEADO">JUMPEADO</option>
                                                                                    <option value="PENDIENTE">PENDIENTE</option>
                                                                                    <option value="PENDIENTE CUADRO DE ASIGNACIONES">PENDIENTE CUADRO DE ASIGNACIONES</option>
                                                                                    <option value="PENDIENTE INSTALACION DE BANDEJA">PENDIENTE INSTALACION DE BANDEJA</option>
                                                                                    <option value="PENDIENTE INSTALACION ODF">PENDIENTE INSTALACION ODF</option>
                                                                                </select>
                                                                            </div>
                                                                            

                                                                        </div>
                                                                        <hr>
                                                                        <div class="">
                                                                            <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarPIN">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-6" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Censado</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoCensado" id="frmMatrizSeguimientoCensado">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Fecha de Censado</label>
                                                                                <input type="date" class="form-control" name="fechaCensado" id="fechaCensado">
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Cantidad de UIPs luego del Censo</label>
                                                                                <input type="number" class="form-control" name="UIPHorizontalCenso" id="UIPHorizontalCenso">
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Estado del censo</label>
                                                                                <select class="form-control select2" name="estadoCenso" id="estadoCenso">
                                                                                    <option value=""0>Seleccione</option>
                                                                                    <option value="PENDIENTE">PENDIENTE</option>
                                                                                    <option value="TERMINADO">TERMINADO</option>
                                                                                </select>
                                                                            </div>
                                                                            

                                                                        </div>
                                                                        <hr>
                                                                        <div class="">
                                                                            <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarCensado">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-7" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Despliegue</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoDespliegue" id="frmMatrizSeguimientoDespliegue">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Confirmacion por parte de Despliegue de instalacion de ODF/BANDEJA</label>
                                                                                <input type="date" class="form-control"  name="bandejaODF" id="bandejaODF">
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Fecha de instalacion de Odf</label>
                                                                                <input type="date" class="form-control" name="fechaInstalacionODF" id="fechaInstalacionODF" value="">
                                                                            </div>
                                                                            
                                                                            
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Fecha de inicio de Contruccion</label>
                                                                                <input type="date" class="form-control" name="fechaInicioConstruccion" id="fechaInicioConstruccion" value="">
                                                                            </div>
                                                                            
                                                                            
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Fecha proyectada de entrega</label>
                                                                                <input type="date" class="form-control" name="fechaProyectadaEntrega" id="fechaProyectadaEntrega" value="">
                                                                            </div>
                                                                            
                                                                            
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Fecha final de entrega de Divicau</label>
                                                                                <input type="date" class="form-control" name="fechaFinalEntregaDivicau" id="fechaFinalEntregaDivicau" value="">
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-6 col-md-6 mb-3" data-select2-id="18">
                                                                                <label>Estatus de Despliegue</label>
                                                                                <select class="form-control select2" name="estadoDespliegue" id="estadoDespliegue">
                                                                                    <option value="0">Seleccione</option>
                                                                                    <option value="En construccion">En construccion</option>
                                                                                    <option value="Sin inicio">Sin inicio</option>
                                                                                    <option value="Ejecucion">Ejecucion</option>
                                                                                    <option value="Etapa de Pasivos">Etapa de Pasivos</option>
                                                                                    <option value="Mediciones">Mediciones</option>
                                                                                    <option value="Pendiente Materiales">Pendiente Materiales</option>
                                                                                    <option value="Terminado">Terminado</option>
                                                                                </select>
                                                                            </div>
                                                                            

                                                                        </div>
                                                                        <hr>
                                                                        <div class="">
                                                                            <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarDespliegue">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-8" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">HGU</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoHGU" id="frmMatrizSeguimientoHGU">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Fecha de Pruebas HGU</label>
                                                                                <input type="date" class="form-control" name="fechaPruebaHGU" id="fechaPruebaHGU" value="">
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Avance de Pruebas HGU</label>
                                                                                <input type="text" class="form-control" name="comodinAvanceHGU" id="comodinAvanceHGU">
                                                                            </div>
                                                                            
                                                                            
                                                                            <div class="col-sm-4 col-md-4 mb-3" data-select2-id="18">
                                                                                <label>Confirmacion de ejecucion de prubas HGU</label>
                                                                                <select class="form-control select2" name="estadoHGU" id="estadoHGU">
                                                                                    <option value="0">Seleccione</option>
                                                                                    <option value="PENDIENTE">PENDIENTE</option>
                                                                                    <option value="COMPLETO">COMPLETO</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="">
                                                                            <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarHGU">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                          </div>
                                                      </div>

                                                      <div class="tab-pane fade section-9" id="v-tabs-messages" role="tabpanel" aria-labelledby="v-tabs-messages-tab">
                                                          <div class="">
                                                              <div class="card">

                                                                  <h5 class="card-header bg-primary text-white">Status</h5>
                                                                  <div class="card-body">
                                                                    <form method="POST" name="frmMatrizSeguimientoStatus" id="frmMatrizSeguimientoStatus">
                                                                        <input type="hidden" name="_id" id="_id" value="0">
                                                                        <div class="row">
                                                                            <div class="col-sm-12 col-md-12 mb-3" data-select2-id="18">
                                                                                <label>Estatus detallado</label>
                                                                                <select class="form-control select2" name="estadoFinal" id="estadoFinal">
                                                                                    <option value="0">Seleccione</option>
                                                                                    <option value="En Diseño">En Diseño</option>
                                                                                    <option value="En Despliegue">En Despliegue</option>
                                                                                    <option value="En Licencia">En Licencia</option>
                                                                                    <option value="Terminado">Terminado</option>
                                                                                    <option value="Paralizado">Paralizado</option>
                                                                                    <option value="Entregado Comercial">Entregado Comercial</option>
                                                                                </select>
                                                                            </div>
                                                                            
                                                                            <div class="col-sm-12 col-md-12 mb-3" data-select2-id="18">
                                                                                <label>Estatus Global</label>
                                                                                <select class="form-control select2" name="estadoGlobal" id="estadoGlobal">
                                                                                    <option value="0">Seleccione</option>
                                                                                    <option value="En ejecucion">En ejecucion</option>
                                                                                    <option value="Terminado">Terminado</option>
                                                                                </select>
                                                                            </div>

                                                                        </div>
                                                                        <hr>
                                                                        <div class="">
                                                                            <button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" id="btnGuardarStatus">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
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
        <script src="<?php echo base_url(); ?>public/js/js_matriz_seguimiento/jsMatrizSeguimiento.js?v=<?php echo time();?>"></script>
        
    </body>
    <!-- END Body -->
</html>
<script type="text/javascript">
     $('.select2').select2();
                $(document).ready(function() {
            });

</script>