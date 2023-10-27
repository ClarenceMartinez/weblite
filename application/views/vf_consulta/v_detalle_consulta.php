<!DOCTYPE html>
<html lang="en"  class="root-text-sm"> 
<head>
        <meta charset="utf-8">
        <title>
            PangeaCo
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
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/notifications/sweetalert2/sweetalert2.bundle.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/formplugins/dropzone/dropzone.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/loading/jquery.loading.min.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/utils/select2_theme.css">
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"> -->
        <style>
            #divMapCoordenadas{
            	height: 450px;    
                width: 1650px;  
            }

            /* .modal {
                right: 150px;
                left : auto;
            } */
            
            
            .swal2-container {
                z-index: 10000;
            }

            .select2-dropdown {
              z-index: 10000;
            }

            #modalSubirEvidencia{
				background-color: rgba(0,0,0,2) !important;
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
                        <div class="fs-lg fw-300 p-5 bg-white border-faded rounded mb-g">
                                
                                <div class="row" style="margin-top: -20px; margin-bottom: -35px;">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-7">
                                                                        
                                        <div class="form-group">
                                            <label class="form-label" for="txt_nom_pro">CONSULTA DE OBRA</label>
                                            <input type="text" class="form-control" id="txt_cod_obra" placeholder="Ingrese Codigo de Obra" required>
                                            <div class="invalid-feedback">Ingrese codigo de Obra</div>
                                        </div>                                       
                                        </p>
                                    </div>  
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-5">
                                        <a style="/*margin-left: 15px;*/ margin-top: 25px;/* height: 50%;*/" class="btn btn-md btn-outline-primary waves-effect waves-themed searchT">
                                                <span class="fal fa-search mr-1"></span>
                                                Buscar
                                        </a> 
                                    </div>
                                </div>   
                        </div>

                        <div class="row">

                            <!--    INICIO INFO DE OBRA -->
                            <div class="col-lg-5 col-xl-5 order-lg-1 order-xl-1">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div id="panel-12" class="panel">
                                                <div class="panel-hdr">
                                                    <h2>
                                                        Informacion de Obra
                                                    </h2>
                                                    <div class="panel-toolbar">
                                                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>                                                       
                                                    </div>
                                                </div>
                                                <div class="panel-container show">
                                                    <div class="panel-content">
                                                       <ul class="nav nav-tabs nav-fill" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-toggle="tab" href="#tab_detalle" role="tab">
                                                                    <i class="fal fa-layer-group mr-1"></i>
                                                                    DETALLE 
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#tab_log_estados" role="tab">
                                                                    <i class="fal fa-house-leave mr-1"></i>
                                                                    LOG ESTADOS
                                                                </a>
                                                            </li>
															<li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#tab_log_expediente" role="tab">
                                                                    <i class="fal fa-house-leave mr-1"></i>
                                                                    LOG VALIDACION EXPEDIENTE
                                                                </a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content p-3">
                                                            <div class="tab-pane fade show active" id="tab_detalle" role="tabpanel">
                                                                <div class="row no-gutters" style="font-weight: bold;margin-left: 10px;">                                                                     
                                                                    <div class="col-12 text-left py-1">
                                                                        <a class="text-muted mb-0">CODIGO DE OBRA : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_itemplan"></a>
                                                                    </div>                                             
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                                                        <a class="text-muted mb-0">ESTADO PLAN : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_estadoplan"></a>
                                                                    </div>
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1"> 
                                                                        <a class="text-muted mb-0">FASE : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_fase"></a>
                                                                    </div>                                        
                                                                    <div class="col-12 text-left py-1"> 
                                                                        <a class="text-muted mb-0">SUBPROYECTO : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_subproyecto"></a>
                                                                    </div>  
                                                                    <div class="col-12 text-left py-1"> 
                                                                        <a class="text-muted mb-0">NOMBRE PLAN : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_nombreplan"></a>
                                                                    </div>                                                                             
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                                                        <a class="text-muted mb-0">CÓDIGO CENTRAL : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_central"></a>
                                                                    </div>
                                                                    <div class="col-lg-6 col-xl-6 col-12  text-left py-1"">  
                                                                        <a class="text-muted mb-0">ZONAL : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_zonal"></a>
                                                                    </div>
                                                                    <div class="col-lg-6 col-xl-6 col-12  text-left py-1">
                                                                        <a class="text-muted mb-0">EECC. : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_eecc"></a>
                                                                    </div>  
                                                                    <div class="col-lg-6 col-xl-6 col-12  text-left py-1">  
                                                                        <a class="text-muted mb-0">UIP : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_uip"></a>
                                                                    </div>                                                                            
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">  
                                                                        <a class="text-muted mb-0">COD. INVERSIÓN : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_codigo_inversion"></a>
                                                                    </div>
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1"> 
                                                                        <a class="text-muted mb-0">ORDEN DE COMPRA : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_orden_compra"></a>
                                                                    </div>
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">  
                                                                        <a class="text-muted mb-0">LONG. : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_longitud"></a>                                           
                                                                    </div>
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                                                        <a class="text-muted mb-0">LAT. : </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_latitud"></a>
                                                                    </div>         
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1" id="divIpMadre">
                                                                        <a class="text-muted mb-0">IP MADRE: </a>
                                                                        <a style="color: var(--theme-primary);" id="txt_ip_madre"></a>
                                                                    </div>  
                                                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1" id="divCancelarIp">
                                                                        
                                                                    </div>                                   
                                                                </div> 

                                                                <div class="d-flex flex-row w-100 py-4">
                                                                    <div id="divMapCoordenadas"></div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="tab_log_estados" role="tabpanel">
                                                                    <div id="cont_tab_log" class="table-responsive">


                                                                    </div>
                                                            </div>
															<div class="tab-pane fade" id="tab_log_expediente" role="tabpanel">
                                                                    <div id="cont_tab_log_expediente" class="table-responsive">


                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        
                            <!-- FIN DE INFO DE OBRA -->

                            <!--    INICIO GESTIONAR OBRA -->
                            <div class="col-lg-7 col-xl-7 order-lg-3 order-xl-2">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div id="panel-12" class="panel">
                                            <div class="panel-hdr">
                                                <h2>
                                                    Gestion de Obra
                                                </h2>
                                                <div class="panel-toolbar">
                                                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>                                                       
                                                </div>
                                            </div>
                                            <div class="panel-container show">
                                                <div class="panel-content">
                                                    <div class="row" id="contGesObraDiv">
                                                        
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>       
                                
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div id="panel-12" class="panel">
                                            <div class="panel-hdr">
                                                <h2>
                                                    Estaciones
                                                </h2>
                                                <div class="panel-toolbar">
                                                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>                                                       
                                                </div>
                                            </div>
                                            <div class="panel-container show">
                                                <div class="panel-content">
                                                    <div class="row" id="contPosDiv">

                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>                                 
                            </div>               
                            <!--  FIN GESIONAR OBRA -->   
                            <!--  INICIO SEGUIMIENTO CV -->   
                            <div class="col-lg-12 col-xl-12 order-lg-12 order-xl-12">
                                <div class="row" style="display:none" id="divSeguimientoCV">
                                    <div class="col-xl-12">
                                        <div id="panel-12" class="panel">
                                            <div class="panel-hdr">
                                                <h2>
                                                    Seguimiento Obra
                                                    <!--
                                                    <a id="btnAddNewSeguimiento" class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver log seguimiento" 
                                                         onclick="getLogSeguimientoCV(this)" style="margin-left: 15px;">
                                                        <i class="fal fa-plus"></i>
                                                    </a>-->
                                                </h2>

                                               
                                                <div class="panel-toolbar">
                                                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>                                                       
                                                </div>
                                            </div>
                                            <div class="panel-container show">
                                                <div class="panel-content">
                                                    <div id="contSeguimientoCV">
                                                        
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            </diV>          
                            <!--  FIN SEGUIMIENTO CV -->                 
                        </div>       
                    </main>
                    <!-- this overlay is activated only when mobile menu is triggered -->
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div> <!-- END Page Content -->
                   
                    <!-- BEGIN Shortcuts -->
                    <div class="modal fade modal-backdrop-transparent" id="modal-shortcut" tabindex="-1" role="dialog" aria-labelledby="modal-shortcut" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-top modal-transparent" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <ul class="app-list w-auto h-auto p-0 text-left">
                                        <li>
                                            <a href="intel_introduction.html" class="app-list-item text-white border-0 m-0">
                                                <div class="icon-stack">
                                                    <i class="base base-7 icon-stack-3x opacity-100 color-primary-500 "></i>
                                                    <i class="base base-7 icon-stack-2x opacity-100 color-primary-300 "></i>
                                                    <i class="fal fa-home icon-stack-1x opacity-100 color-white"></i>
                                                </div>
                                                <span class="app-list-name">
                                                    Home
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="page_inbox_general.html" class="app-list-item text-white border-0 m-0">
                                                <div class="icon-stack">
                                                    <i class="base base-7 icon-stack-3x opacity-100 color-success-500 "></i>
                                                    <i class="base base-7 icon-stack-2x opacity-100 color-success-300 "></i>
                                                    <i class="ni ni-envelope icon-stack-1x text-white"></i>
                                                </div>
                                                <span class="app-list-name">
                                                    Inbox
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="intel_introduction.html" class="app-list-item text-white border-0 m-0">
                                                <div class="icon-stack">
                                                    <i class="base base-7 icon-stack-2x opacity-100 color-primary-300 "></i>
                                                    <i class="fal fa-plus icon-stack-1x opacity-100 color-white"></i>
                                                </div>
                                                <span class="app-list-name">
                                                    Add More
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Shortcuts -->
                    <!-- BEGIN Color profile -->
                    <!-- this area is hidden and will not be seen on screens or screen readers -->                    
                </div>
            </div>
        </div>
        <!-- END Page Wrapper -->
           
        <!-- BEGIN Page Settings -->
        <div class="modal fade js-modal-settings modal-backdrop-transparent" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-right modal-md">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100">
                        <h4 class="m-0 text-center color-white">
                            Opciones de Dise&ntilde;o
                            <small class="mb-0 opacity-80">Configuraci&oacute;n de interfaz de usuario</small>
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="settings-panel">
                            <div class="mt-4 d-table w-100 px-5">
                                <div class="d-table-cell align-middle">
                                    <h5 class="p-0">
                                        Dise&ntilde;o de Aplicaci&oacute;n
                                    </h5>
                                </div>
                            </div>                            
                            <div class="list" id="nfm">
                                <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-minify"></a>
                                <span class="onoffswitch-title">Minimizar Navegador</span>
                                <span class="onoffswitch-title-desc">Posicione el puntero en el navegador para ver las opciones</span>
                            </div>
                            <div class="list" id="nfh">
                                <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-hidden"></a>
                                <span class="onoffswitch-title">Ocultar Navegador</span>
                                <span class="onoffswitch-title-desc">Posicione el puntero en el borde para revelar el navegador</span>
                            </div>
                            <div class="list" id="nft">
                                <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-top"></a>
                                <span class="onoffswitch-title">Navegador Superior</span>
                                <span class="onoffswitch-title-desc">Colocar el navegador en la parte superior de la interfaz</span>
                            </div>
                            <div class="expanded">
                                <ul class="mb-3 mt-1">
                                    <li>
                                        <div class="bg-fusion-50" data-action="toggle" data-class="mod-bg-1"></div>
                                    </li>
                                    <li>
                                        <div class="bg-warning-200" data-action="toggle" data-class="mod-bg-2"></div>
                                    </li>
                                    <li>
                                        <div class="bg-primary-200" data-action="toggle" data-class="mod-bg-3"></div>
                                    </li>
                                    <li>
                                        <div class="bg-success-300" data-action="toggle" data-class="mod-bg-4"></div>
                                    </li>
                                    <li>
                                        <div class="bg-white border" data-action="toggle" data-class="mod-bg-none"></div>
                                    </li>
                                </ul>
                                <div class="list" id="mbgf">
                                    <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-fixed-bg"></a>
                                    <span class="onoffswitch-title">Fixed Background</span>
                                </div>
                            </div>                            
                            <div class="mt-4 d-table w-100 px-5">
                                <div class="d-table-cell align-middle">
                                    <h5 class="p-0">
                                        Modificaciones Globales
                                    </h5>
                                </div>
                            </div>                            
                            <div class="list" id="mdn">
                                <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-nav-dark"></a>
                                <span class="onoffswitch-title">Navegador Oscuro</span>
                                <span class="onoffswitch-title-desc">El navegador tendra un fondo oscuro</span>
                            </div>
                            <hr class="mb-0 mt-4">
                            <div class="mt-4 d-table w-100 pl-5 pr-3">
                                <div class="d-table-cell align-middle">
                                    <h5 class="p-0">
                                        Tama&ntilde;o de fuente Global
                                    </h5>
                                </div>
                            </div>
                            <div class="list mt-1">
                                <div class="btn-group btn-group-sm btn-group-toggle my-2" data-toggle="buttons">
                                    <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-sm" data-target="html">
                                        <input type="radio" name="changeFrontSize"> SM
                                    </label>
                                    <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text" data-target="html">
                                        <input type="radio" name="changeFrontSize" checked=""> MD
                                    </label>
                                    <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-lg" data-target="html">
                                        <input type="radio" name="changeFrontSize"> LG
                                    </label>
                                    <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-xl" data-target="html">
                                        <input type="radio" name="changeFrontSize"> XL
                                    </label>
                                </div>
                                <span class="onoffswitch-title-desc d-block mb-0">Cambiar el tama&ntilde;o de la fuente del aplicativo (Se reiniciara con el refresh de la pagina)</span>
                            </div>
                            <hr class="mb-0 mt-4">
                            <div class="mt-4 d-table w-100 pl-5 pr-3">
                                <div class="d-table-cell align-middle">
                                    <h5 class="p-0 pr-2 d-flex">
                                        Color de Aplicativo
                                    </h5>
                                </div>
                            </div>
                            <div class="expanded theme-colors pl-5 pr-3">
                                <ul class="m-0">
                                    <li>
                                        <a href="#" id="myapp-0" data-action="theme-update" data-themesave data-theme="" data-toggle="tooltip" data-placement="top" title="Wisteria (base css)" data-original-title="Wisteria (base css)"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-1" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-1.css" data-toggle="tooltip" data-placement="top" title="Tapestry" data-original-title="Tapestry"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-2" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-2.css" data-toggle="tooltip" data-placement="top" title="Atlantis" data-original-title="Atlantis"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-3" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-3.css" data-toggle="tooltip" data-placement="top" title="Indigo" data-original-title="Indigo"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-4" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-4.css" data-toggle="tooltip" data-placement="top" title="Dodger Blue" data-original-title="Dodger Blue"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-5" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-5.css" data-toggle="tooltip" data-placement="top" title="Tradewind" data-original-title="Tradewind"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-6" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-6.css" data-toggle="tooltip" data-placement="top" title="Cranberry" data-original-title="Cranberry"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-7" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-7.css" data-toggle="tooltip" data-placement="top" title="Oslo Gray" data-original-title="Oslo Gray"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-8" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-8.css" data-toggle="tooltip" data-placement="top" title="Chetwode Blue" data-original-title="Chetwode Blue"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-9" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-9.css" data-toggle="tooltip" data-placement="top" title="Apricot" data-original-title="Apricot"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-10" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-10.css" data-toggle="tooltip" data-placement="top" title="Blue Smoke" data-original-title="Blue Smoke"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-11" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-11.css" data-toggle="tooltip" data-placement="top" title="Green Smoke" data-original-title="Green Smoke"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-12" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-12.css" data-toggle="tooltip" data-placement="top" title="Wild Blue Yonder" data-original-title="Wild Blue Yonder"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-13" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-13.css" data-toggle="tooltip" data-placement="top" title="Emerald" data-original-title="Emerald"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-14" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-14.css" data-toggle="tooltip" data-placement="top" title="Supernova" data-original-title="Supernova"></a>
                                    </li>
                                    <li>
                                        <a href="#" id="myapp-15" data-action="theme-update" data-themesave data-theme="<?php echo base_url(); ?>public/css/themes/cust-theme-15.css" data-toggle="tooltip" data-placement="top" title="Hoki" data-original-title="Hoki"></a>
                                    </li>
                                </ul>
                            </div>
                            <hr class="mb-0 mt-4">
                            <div class="mt-4 d-table w-100 pl-5 pr-3">
                                <div class="d-table-cell align-middle">
                                    <h5 class="p-0 pr-2 d-flex">
                                        Temas de Aplicativo
                                    </h5>
                                </div>
                            </div>
                            <div class="pl-5 pr-3 py-3">                                
                                <div class="row no-gutters">
                                    <div class="col-4 pr-2 text-center">
                                        <div id="skin-default" data-action="toggle-replace" data-replaceclass="mod-skin-light mod-skin-dark" data-class="" data-toggle="tooltip" data-placement="top" title="" class="d-flex bg-white border border-primary rounded overflow-hidden text-success js-waves-on" data-original-title="Default Mode" style="height: 80px">
                                            <div class="bg-primary-600 bg-primary-gradient px-2 pt-0 border-right border-primary"></div>
                                            <div class="d-flex flex-column flex-1">
                                                <div class="bg-white border-bottom border-primary py-1"></div>
                                                <div class="bg-faded flex-1 pt-3 pb-3 px-2">
                                                    <div class="py-3" style="background:url('<?php echo base_url(); ?>public/img/demo/s-1.png') top left no-repeat;background-size: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        Default
                                    </div>
                                    <div class="col-4 px-1 text-center">
                                        <div id="skin-light" data-action="toggle-replace" data-replaceclass="mod-skin-dark" data-class="mod-skin-light" data-toggle="tooltip" data-placement="top" title="" class="d-flex bg-white border border-secondary rounded overflow-hidden text-success js-waves-on" data-original-title="Light Mode" style="height: 80px">
                                            <div class="bg-white px-2 pt-0 border-right border-"></div>
                                            <div class="d-flex flex-column flex-1">
                                                <div class="bg-white border-bottom border- py-1"></div>
                                                <div class="bg-white flex-1 pt-3 pb-3 px-2">
                                                    <div class="py-3" style="background:url('<?php echo base_url(); ?>public/img/demo/s-1.png') top left no-repeat;background-size: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        Light
                                    </div>
                                    <div class="col-4 pl-2 text-center">
                                        <div id="skin-dark" data-action="toggle-replace" data-replaceclass="mod-skin-light" data-class="mod-skin-dark" data-toggle="tooltip" data-placement="top" title="" class="d-flex bg-white border border-dark rounded overflow-hidden text-success js-waves-on" data-original-title="Dark Mode" style="height: 80px">
                                            <div class="bg-fusion-500 px-2 pt-0 border-right"></div>
                                            <div class="d-flex flex-column flex-1">
                                                <div class="bg-fusion-600 border-bottom py-1"></div>
                                                <div class="bg-fusion-300 flex-1 pt-3 pb-3 px-2">
                                                    <div class="py-3 opacity-30" style="background:url('<?php echo base_url(); ?>public/img/demo/s-1.png') top left no-repeat;background-size: 100%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        Dark
                                    </div>
                                </div>
                            </div>
                            <hr class="mb-0 mt-4">
                            <div class="pl-5 pr-3 py-3 bg-faded">
                                <div class="row no-gutters">
                                    <div class="col-12 pr-1">
                                        <a href="#" class="btn btn-outline-danger fw-500 btn-block" data-action="app-reset">Reiniciar Configuracion</a>
                                    </div>
                                </div>
                            </div>
                        </div> <span id="saving"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAgregarEntidad" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-right" role="document">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white">
                            Agregar Entidad
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
						<div id="contTablaEntidad">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnProcesar" type="button" class="btn btn-primary" onclick="registrarEntidad();">Aceptar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="modalRegistrarExpLic" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white">
                            Licencia
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="formRegExpLic">
                            <label class="form-label" for="inputGroupFile01">Cargar Evidencia</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="archivo" aria-describedby="archivo">
                                    <label class="custom-file-label" for="archivo" id="lblarchivo"></label>
                                </div>
                                <div class="valid-feedback">
                                    Correcto!
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="btnProcesar" type="button" class="btn btn-primary" onclick="guardarExpedienteEntidad();">Aceptar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-fullscreen" id="modalComprobante" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white">
                            Comprobante
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div id="contTablaComprobante" class="table-responsive">
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnComprobante" type="button" class="btn btn-primary" onclick="registrarComprobante();">Aceptar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEjecucionDiseno" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white" id="titModalEjecDiseno">
                            EJECUTAR DISEÑO
                            <small class="m-0 text-center color-white">
                                
                            </small>
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="formEjecDiseno">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="selectEntidad">ENTIDADES<span class="text-danger">*</span></label>                                
                                    <select id="selectEntidad" name="selectEntidad" class="select2 form-control w-100" multiple="multiple">
                                    </select>
                                    <div class="valid-feedback">
                                        Correcto!!
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3" style="display:none" id="divUipDise">                                
                                        <label class="form-label" for="txt_uip_dise">UIP<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control classuip" id="txt_uip_dise" placeholder="Ingrese UIP" required>
                                        <div class="invalid-feedback">
                                            Ingrese UIP
                                        </div>                                       
                                 </div>
								  <div class="col-md-12 mb-3" style="display:none" id="divTipoDise">                                
                                        <label>TIPO DISE&Ntilde;O</label>
                                        <select id="cmbTipoDiseno" name="cmbTipoDiseno" class="select2 form-control">
                                            <option value=""></option>  
                                            <?php foreach($arrayTipoDiseno AS $row) {
                                                echo '<option value="'.$row['id_tipo_diseno'].'">'.$row['descripcion'].'</option>';
                                            } ?>                                          
                                        </select>                          
                                 </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="archivo2">EXPEDIENTE DISEÑO: Archivo .rar,.zip (Archivo de metrados, planos, fotos y documentos)<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo2" id="archivo2" accept="application/zip,application/x-zip,application/x-zip-compressed,application/x-7z-compressed,application/x-rar-compressed,.rar">
                                    <div class="invalid-feedback">
                                        Seleccione evidencia.
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                  <!--  <label class="form-label" for="defaultInline1">Datos para PO Automático<span class="text-danger">*</span></label>   -->
                                    <div class="frame-wrap" style="display:none">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" id="chxOTAC" name="chxOTAC">
                                            <label class="custom-control-label" for="chxOTAC">Requiere OT de Actualización?</label>
                                        </div>
                                    </div> 
                                    <div class="invalid-feedback">
                                        Correcto!!
                                    </div>
                                </div>
                            </div>
                        </form>
						
						<div class="frame-wrap w-100"  style="display:none" id="divFormulariosRefCTO">
                            <div class="form-row">
                                <div class="col-md-6 mb-6" style="text-align: center">        
                                    <button type="button" class="btn btn-md btn-outline-success waves-effect waves-themed" onclick="addFormToReforzamientoCto()">
                                        <span class="fal fa-plus mr-1"></span>
                                        Agregar 1 Formulario
                                    </button>
                                </div>
                                <div class="col-md-6 mb-6" style="text-align: center">     
                                    <button type="button" class="btn btn-md btn-outline-danger waves-effect waves-themed" onclick="deleteFormToReforzamientoCto()">
                                        <span class="fal fa-times mr-1"></span>
                                        Eliminar Formulario
                                    </button>    
                                </div>  
                            </div>             
                            <div class="accordion" id="accordionExample">
                                <div class="card" id="card_form_ref_1">
                                    <div class="card-header" id="headingOne">
                                        <a href="javascript:void(0);" class="card-title" id="titulo_form_1" data-toggle="collapse" data-target="#form_refo_1" aria-expanded="true" aria-controls="form_refo_1">
                                            Formulario Reforzamiento CTO #1
                                            <span class="ml-auto">
                                                <span class="collapsed-reveal">
                                                    <i class="fal fa-minus-circle text-danger"></i>
                                                </span>
                                                <span class="collapsed-hidden">
                                                    <i class="fal fa-plus-circle text-success"></i>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                    <div id="form_refo_1" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">                                
                                                        <label class="form-label" for="txt_cto_adjudi_1">CTO Adjudicado<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="txt_cto_adjudi_1" placeholder="Ingrese CTO Ajudicado" required>
                                                        <div class="invalid-feedback">
                                                            CTO Adjudicado
                                                        </div>                                       
                                                </div>
                                                <div class="col-md-6 mb-3">                                
                                                        <label class="form-label" for="txt_divCau_1">Divicau<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="txt_divCau_1" placeholder="Ingrese Divicau" required>
                                                        <div class="invalid-feedback">
                                                            Divicau
                                                        </div>                                       
                                                </div>
                                                <!--<div class="col-md-6 mb-3">                                
                                                        <label class="form-label" for="txt_tip_refor_1">Tipo de Reforzamiento<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="txt_tip_refor_1" placeholder="Ingrese Tipo de Reforzamiento" required>
                                                        <div class="invalid-feedback">
                                                            Tipo de Reforzamiento
                                                        </div>                                       
                                                </div>-->
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="txt_tip_refor_1">Tipo Reforzamiento<span class="text-danger">*</span></label>                                
                                                    <select id="txt_tip_refor_1"  class="form-label select2">
                                                        <option value=""></option>
                                                        <option value="NUEVO CTO">NUEVO CTO</option>
                                                        <option value="2DO SPLITTER">2DO SPLITTER</option>
														<option value="NO REQUIERE">NO REQUIERE</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Tipo de Reforzamiento
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">                                
                                                        <label class="form-label" for="txt_cod_2_splitter_1">Reforzamiento CTO Final<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="txt_cod_2_splitter_1" placeholder="Ingrese CTO Final" required>
                                                        <div class="invalid-feedback">
                                                            Nuevo Codigo 2do Splitter
                                                        </div>                                       
                                                </div> <!--
                                                <div class="col-md-6 mb-3">                                
                                                        <label class="form-label" for="txt_cod_splitter_cto_1">Nuevo Codigo Splitter (NUEVO CTO)<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="txt_cod_splitter_cto_1" placeholder="Ingrese Codigo Splitter (nuevo cto)" required>
                                                        <div class="invalid-feedback">
                                                            Nuevo Codigo Splitter (NUEVO CTO)
                                                        </div>                                       
                                                </div>
                                                <div class="col-md-6 mb-3">                                
                                                        <label class="form-label" for="txt_nuevo_cod_cto_1">Nuevo Codigo CTO<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="txt_nuevo_cod_cto_1" placeholder="Ingrese Codigo Nuevo CTO" required>
                                                        <div class="invalid-feedback">
                                                            Nuevo Codigo CTO
                                                        </div>                                       
                                                </div>-->
                                                <div class="col-md-6 mb-3">                                
                                                        <label class="form-label" for="txt_observacion_1">Observacion<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="txt_observacion_1" placeholder="Ingrese Observacion" required>
                                                        <div class="invalid-feedback">
                                                            Observacion
                                                        </div>                                       
                                                </div>                                                                                             
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnEjecutarDiseno" onclick="ejecutarDiseno(this)">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-fullscreen" id="modalEnObraPreliqui" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="overflow: scroll;">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white" id="titModalObraPreliqui">
                            PORCENTAJE ESTACIONES
                            <small class="m-0 text-center color-white">
                                
                            </small>
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-row" id="contPanel">
                        
                        </div>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn" onclick="ejecutarDiseno(this)">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div> -->
                </div>
            </div>
        </div>

        
        <div class="modal fade" id="modalSubirEvidencia" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white" id="titModalSubirEvidencia">
                            CARGAR EVIDENCIAS, Itemplan:
                            <small class="m-0 text-center color-white">
                                
                            </small>
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formSubirEviLiqui">
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="archivo_reflec">PRUEBAS REFLECTROMÉTRICAS(.pdf)<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo_reflec" id="archivo_reflec"
                                    accept=".pdf">
                                    <div class="invalid-feedback">
                                        Seleccione evidencia.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="archivo_perfil">PERFIL(.pdf)<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo_perfil" id="archivo_perfil"
                                    accept=".pdf">
                                    <div class="invalid-feedback">
                                        Seleccione evidencia.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-6">
                                    <label class="form-label" for="archivo_hgu">PRUEBAS HGU<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo_hgu" id="archivo_hgu"
                                    accept=".zip,.rar">
                                    <div class="invalid-feedback">
                                        Seleccione Zip.
                                    </div>
                                </div>
								<div class="col-md-6 mb-6">
                                    <label class="form-label" for="archivo_otros">OTROS DOCS.(.ZIP)(Opcional)<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo_otros" id="archivo_otros"
                                    accept=".zip,.rar">
                                    <div class="invalid-feedback">
                                        Seleccione Zip.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnRegEvi" onclick="registrarEviForLiqui(this)">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalDetallePO" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                       
                        <h4 class="m-0 text-center color-white" id="titModalDetPO">                           
                            <small class="m-0 text-center color-white">
                                PO: 
                            </small>
                        </h4>
                        
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
						<ul class="nav nav-tabs justify-content-center" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_direction-2" role="tab">DETALLE</a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_direction-1" role="tab">LOG PO</a></li>
                        </ul>
						<div class="tab-content p-3">
                            <div class="tab-pane fade show active" id="tab_direction-2" role="tabpanel">
								<div id="cont_tb_detalle_po" class="form-group table-responsive" >
                                </div>
							</div>
							<div class="tab-pane fade" id="tab_direction-1" role="tabpanel">
								<div id="cont_tb_log_po" class="form-group">

								</div>
							</div>							
						</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-fullscreen" id="modalPartAdicIntegral" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="overflow: scroll;">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white" id="titModalPartAdicIntegral">
                            EDITAR PARTIDAS ADICIONALES INTEGRAL
                            <small class="m-0 text-center color-white">
                                PO: 
                            </small>
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="settings-panel">
							<div id="contTablaPartidaAdicInte" class="form-group">
                            </div>
                            <div id="contTablaPoDetalleMo">   
                            </div>
                        </div>
						<span id="saving"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCancelarIP" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="titModalCancelarIP">
                            CANCELAR / TRUNCAR / SUSPENDER
                            <small class="m-0 text-muted">
                                Below is a static modal example
                            </small>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formCancelarIP">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="selectEstadoUpd">ESTADO<span class="text-danger">*</span></label>
                                        <select class="select2" id="selectEstadoUpd" name="selectEstadoUpd" aria-label="usertype">
                                                <option selected="">Seleccionar Estado</option>
                                        </select>
                                    <div class="valid-feedback">
                                        Correcto!!
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="selectMotivoCance">MOTIVO<span class="text-danger">*</span></label>
                                        <select class="select2" id="selectMotivoCance" name="selectMotivoCance" aria-label="usertype">
                                                <option selected="">Seleccionar Motivo</option>
                                        </select>
                                    <div class="valid-feedback">
                                        Correcto!!
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="txtComentario2">COMENTARIO<span class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control" placeholder="Ingrese comentario" id="txtComentario2" name="txtComentario2" rows="5" required></textarea>
                                    <div class="invalid-feedback">
                                        Ingrese un comentario.
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="archivo3">CARGAR EVIDENCIA(.zip,.rar)<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo3" id="archivo3"
                                    accept="application/zip, application/x-7z-compressed, application/x-rar-compressed, .rar, .zip">
                                    <div class="invalid-feedback">
                                        Seleccione evidencia.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnCancelarIP" onclick="cancelarItemplan(this)">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal agregar nuevo seguimiento cv -->
        <div class="modal fade" id="modalRegSeguimientoCV" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content" style="/*overflow: scroll;*/">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white" id="titModalLogSeguiCV">
                           LOG DE CAMBIOS DE STITUACION                          
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="settings-panel">
                            
                                <div id="contTbSeguimiento" class="form-group">
                                </div>
                                                              
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalMotivoPrecancelacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ingrese el motivo de la Pre Cancelacion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-12">
                            <div class="form-group">
                                <label class="control-label">MOTIVO</label>
                                <select id="idSelectMotivo" name="responsable" class="select2 form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-12">
                            <div class="form-group">
                                <label class="control-label">OBSERVACION</label>
                                <input id="txtObservacion" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btnConfPreCan" class="btn btn-success" onclick="preCancelarPO(this)">Aceptar</button>
                </div>
                </div>
            </div>
        </div>
		
		<div class="modal fade" id="modalRechazado"  tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 style="margin: auto" class="modal-title">EXPEDIENTE RECHAZADO</h3>
                        </div>
                        
                        <div class="modal-body">                       
                            <div id="contTablaRechazo">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>      
                    </div>
                </div>
            </div> 

            <div class="modal fade" id="modQuiebreComercial" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="titModalQuiebreCV">
                            QUIEBRES
                            <small class="m-0 text-muted">
                                Below is a static modal example
                            </small>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formQuiebreCV">
                            <div class="form-row">                             
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="archivo3">CARGAR EVIDENCIA(.zip,.rar)<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo4" id="archivo4"
                                    accept="application/zip, application/x-7z-compressed, application/x-rar-compressed, .rar, .zip">
                                    <div class="invalid-feedback">
                                        Seleccione evidencia.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnQuiebreSave" onclick="saveQuiebreCV(this)">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="modal fade" id="modalCierreLicencia" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white">
                        Termino Licencia
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="formRegExpLic">
                            <label class="form-label" for="inputGroupFile01">Cargar Evidencia</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="archivolf" aria-describedby="archivo">
                                    <label class="custom-file-label" for="archivolf" id="lblarchivofl"></label>
                                </div>
                                <div class="valid-feedback">
                                    Correcto!
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="btnFinLice" type="button" class="btn btn-primary" onclick="regFinalizaLicencia();">Aceptar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- END Page Settings -->     
        <script src="<?php echo base_url(); ?>public/js/vendors.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/app.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/select2/select2.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.export.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/notifications/sweetalert2/sweetalert2.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/dropzone/dropzone.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/loading/jquery.loading.min.js"></script>
        <script src="<?php echo base_url(); ?>public/js/Utils.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/js_consulta/jsDetalleConsulta.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/js_planta_interna/jsLiquidacionObraPin.js?v=<?php echo time();?>"></script>
        
        <script type="text/javascript">
            $('.select2').select2();

            $(document).ready(function()
            {
                // tableEntidadGlob = initDataTable('tbEntidad');
                initDataTableResponsive('tbSolicitudOc');
            });

            $('.searchT').on('click', function() {
             
                var cod_obra = $.trim($('#txt_cod_obra').val());
                 console.log(cod_obra);
                $.ajax({
                        'type'  :   'post',
                        'url'   :   'findObra',
                        'data'  :   {cod_obra : cod_obra},
                        'async' :   false
                }).done(function (data){
					//console.log(data);
                    var data = JSON.parse(data);
					if (data.error == 0) {
                        $('#txt_itemplan').html(data.dataObra.itemplan);
                        $('#txt_estadoplan').html(data.dataObra.estadoPlanDesc);
                        $('#txt_fase').html(data.dataObra.faseDesc);
                        $('#txt_subproyecto').html(data.dataObra.subproyectoDesc);
                        $('#txt_nombreplan').html(data.dataObra.nombrePlan);
                        $('#txt_central').html(data.dataObra.codigo);
                        $('#txt_zonal').html(data.dataObra.zonalDesc);
                        $('#txt_eecc').html(data.dataObra.empresaColabDesc);
                        $('#txt_uip').html(data.dataObra.cantFactorPlanificado);
                        $('#txt_codigo_inversion').html(data.dataObra.codigoInversion);
                        $('#txt_orden_compra').html(data.dataObra.orden_compra);
                        $('#txt_longitud').html(data.dataObra.longitud);
                        $('#txt_latitud').html(data.dataObra.latitud); 
                        $('#txt_ip_madre').html(data.dataObra.itemplan_m)                                                                                  
                        initmap(data.dataObra.latitud, data.dataObra.longitud);

                        //LOG

                        $('#cont_tab_log').html(data.tablaLog);
						$('#cont_tab_log_expediente').html(data.tablaLogExpe);
                        $('#contPosDiv').html(data.htmlPos);
                        $('#contGesObraDiv').html(data.htmlTabGestion);
                        $('#divCancelarIp').html(data.btnCancelar);
                        $('.select2').select2();

                        $('.date_picker').datepicker(
                        {
                            orientation: "bottom right",
                            todayHighlight: true,
                            templates: controls,
                            format: 'dd-mm-yyyy'
                        });

                        initSendVali();

                        itemplanGbl         =   cod_obra;       
                        idProyectoGlobal    =   data.dataObra.idProyecto;    
						idSubProyectoGlobal =   data.dataObra.idSubProyecto;						
                        initFilePINLiqui();

                        //console.log('idPro:'+data.dataObra.idProyecto);
						tipoDiseGeneral =   null;
                        if(data.dataObra.idProyecto == 21   ||  data.dataObra.idProyecto == 3   ||  data.dataObra.idSubProyecto ==  734 ||  data.dataObra.idSubProyecto ==  748){//solo edificios o sisegos o reforzamiento
                            //console.log('es cv');
                            $('#contSeguimientoCV').html(data.tbLogSegui);
                            initExistDataTableLight('tb_log_segui_cv');
                            $('#btnAddNewSeguimiento').attr('data-itemplan', data.dataObra.itemplan);
                            $('#divSeguimientoCV').show();
							if(data.dataObra.idProyecto == 3){
                                tipoDiseGeneral =   data.infoCotiTipoDise;
                            }
                        }else{
                            $('#divSeguimientoCV').hide();
                        }
                      
                        $('#selectMotivoCance').html(data.cmbMotiCance);
                        $('#selectEstadoUpd').html(data.combEstadosTrunCansus);
                        $('.select2').select2();
						
						//nuevo reforzamiento cto
                        if(numFormRefCto >= 1){
                            for(var i = 2; i <= numFormRefCto; i++) {
                                $('#card_form_ref_'+i).remove();                                 
                            }
                            numFormRefCto = 1;
                            $('#txt_cto_adjudi_1').val('');
                            $('#txt_divCau_1').val('');
                            $('#txt_tip_refor_1').val('');
                            $('#txt_cod_2_splitter_1').val('');
                            $('#txt_cod_splitter_cto_1').val('');
                            $('#txt_nuevo_cod_cto_1').val('');
                            $('#txt_observacion_1').val('');
                        }
                        //fin nuevo reforzamiento cto
                    }else{
						//alert(data.msj);
                        mostrarNotificacion(1,'warning','No se pudo realizar la accion',data.msj);
                        //alert(data.msj);
                    }                    
                });

                //initmap('-12.097822319125317', '-77.04136848449707');
                
            });

            function initmap(latitud_, longitud_) {
                console.log(latitud_);
                console.log(longitud_);
                infoWindow = new google.maps.InfoWindow();
                var geocoder = new google.maps.Geocoder();
                var map;
                var latitude = parseFloat(latitud_); // YOUR LATITUDE VALUE
                var longitude = parseFloat(longitud_); // YOUR LONGITUDE VALUE


                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        latitude = position.coords.latitude;
                        longitude = position.coords.longitude;
                    });
                }

                var myLatLng = {lat: latitude, lng: longitude};
		
                map = new google.maps.Map(document.getElementById('divMapCoordenadas'), {
                    center: myLatLng,
                    zoom: 14
                });

                new google.maps.Marker({
                    position: myLatLng,
                    map,
                    title: "Hello World!",
                });
                
            }

            function initFilePINLiqui(){
                $('#evidenciaPin').on('change',function(e){                   
                    var fileName = e.target.files[0].name;                    
                    $(this).next('.custom-file-label').html(fileName);        

                }) 
            }

            function openModalMotivoPreCancelacion(component) {
                var codigoPO = $(component).attr('data-codigo_po');
                var itemplan = $(component).attr('data-itemplan');
                console.log('codigo_po:'+codigoPO);
                $.ajax({
                    type: 'POST',
                    'url': 'getCmbMotPreCancela',
                    data: {
                        codigoPO: codigoPO
                    },
                    'async': false
                }).done(function (data) {
                    var data = JSON.parse(data);
                    if (data.error == 0) {   
                        $('#idSelectMotivo').html(data.comboMotivo);
                        $('#btnConfPreCan').attr('data-codigo_po', codigoPO);
                        $('#btnConfPreCan').attr('data-itemplan', itemplan);
                        modal('modalMotivoPrecancelacion');                      
                    } else {
                        mostrarNotificacion(1,'error', 'Error', data.msj);
                    }
                });           
            }

            function preCancelarPO(component) {
                var codigoPO = $(component).attr('data-codigo_po');
                var itemplan = $(component).attr('data-itemplan');
                console.log('codigo_po:'+codigoPO);
                console.log('itemplan:'+itemplan);
                var motivo = $.trim($('#idSelectMotivo').val());
                var observacion = $.trim($('#txtObservacion').val());

                if (motivo == null || motivo == '' || motivo == undefined || observacion == null || observacion == '' || observacion == undefined) {
                    mostrarNotificacion(1,'error', 'Error', 'Debe ingresar el motivo y observacion!!');
                    return;
                }

                swal.fire({
                    title: 'Está seguro de Pre cancelar el PO?',
                    text: 'Recuerde que esta PO pasara a la bandeja de cancelacion!',
                    type: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Si, pre cancelar PO!',
                    cancelButtonClass: 'btn btn-secondary',
                }).then(function () {
                    $.ajax({
                        type: 'POST',
                        'url': 'preCancelPO',
                        data: {
                            itemplan: itemplan,
                            codigoPO: codigoPO,                           
                            motivo: motivo,
                            observacion: observacion
                        },
                        'async': false
                    }).done(function (data) {
                        var data = JSON.parse(data);
                        if (data.error == 0) {                          
                            modal('modalMotivoPrecancelacion');
                            mostrarNotificacion(1,'success', 'Operacion Exitosa', data.msj);
                            $('.searchT').click();
                        } else {
                            mostrarNotificacion(1, 'error', 'Error', data.msj);
                        }

                    });
                });
            
        }			
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJI8anPrwrko7m0FLm-BOjD43b5d8xQIw&libraries=places&callback=init"></script>
    </body>
    <!-- END Body -->
</html>
