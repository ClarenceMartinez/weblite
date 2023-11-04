<!DOCTYPE html>
<html lang="en"  class="root-text-sm">
<head>
        <meta charset="utf-8">
        <title>
            <?php echo TITULO_CONSULTA?>
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
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/datagrid/datatables/datatables.bundle.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/loading/jquery.loading.min.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/notifications/sweetalert2/sweetalert2.bundle.css">
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
 
			#modalDetallePO,#modalEditPO,#modalPartAdicIntegral{
				background-color: rgba(0,0,0,2) !important;
			}

			.swal2-container {
                z-index: 10000;
            }

			.select2-dropdown {
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
                                        <h2>
                                               BOLSA PEP
                                        </h2>
                                        
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <!--------------------TABLA EVALUACION EECC------------------------>
                                            <div class="row">

                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="txtPep1">PEP 1</label>
                                                    <input type="text" class="form-control" id="txtPep1" placeholder="" maxlength="20">
                                                    <div class="valid-feedback">
                                                        Correcto!
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label" for="txtPep2">PEP 2</label>
                                                    <input type="text" class="form-control" id="txtPep2" placeholder="" data-inputmask="'mask': '99-9999999999'" maxlength="24">
                                                    <div class="valid-feedback">
                                                        Correcto!
                                                    </div>
                                                </div>

                                               
                                                <div class="col-sm-3 col-md-3">
                                                    <div class="form-group">
                                                        <label>PROYECTO</label>
                                                        <select class="select2" id="cmbProyecto" name="cmbProyecto" aria-label="usertype" onchange="getCmbSubProyectoByProyectoReg();">
                                                            <?php echo $cmbProyecto ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-sm-3 col-md-3">
                                                    <div class="form-group">
                                                        <label>SUB PROYECTO</label>
                                                        <select id="cmbSubProyecto" name="cmbSubProyecto" class="select2" >                                                                
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3 col-md-3">
                                                    <div class="form-group">
                                                    <label>FASE</label>                                                
                                                    <select class="select2" id="selectFase" ame="selectFase" aria-label="usertype">
                                                            <?php echo $cmbFase ?>
                                                    </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3 col-md-3">
                                                    <div class="form-group">
                                                        <label>ESTADO</label>

                                                        <select id="selectEstado" name="selectEstado" class="select2" >
                                                                <option selected value="1">ACTIVO</option>
                                                                <option value="2">INACTIVO</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-md-3">
                                                    <div style="margin-top: 20px;">
                                                        <div class="form-group">
                                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                                <button class="btn btn-success" style="margin-right: 20px;" type="button" onclick="filtrarTabla()" id="btnProceso1">Buscar</button>
                                                                <img id="loadGif1" src="<?php echo base_url();?>public/img/gif/indicator.gif" style="width: 25px;height: 25px; display: none;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                             
                                            <!--CIERRE EVA EECC-->
                                            <br>
                                            <table id="dt-basic-example" class="table table-bordered table-sm table-hover table-striped w-100">
                                                <thead class="bg-primary-600"">
                                                    <th>ACCIÓN</th>                                                        
                                                    <th>PEP1</th>                            
                                                    <th>PEP2</th>
                                                    <th>SUBPROYECTO</th>
                                                    <th>FASE</th>
                                                    <th>MES</th>
                                                    <th>TIPO</th>
                                                    <th>ESTADO</th>
                                                    <th>FECHA REGISTRO</th>
                                                    <th>USUA REGISTRO</th>                                                     
                                                </thead>                                               
                                            </table>  
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        
        <div class="modal fade" id="mod_new_config" tabindex="-1" role="dialog" aria-hidden="true"  data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Configuracion Bolsa Pep
                            <small class="m-0 text-muted">
                                
                            </small>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                       
                            <div class="form-group row">                                
                                <div class="col-3 pr-1">
                                    <label class="col-xl-12 form-label" for="txtNombres">PEP 1</label>
                                    <input type="text" id="txtpep1" name="txtpep1" class="form-control" placeholder="" required maxlength="20">
                                    <div class="invalid-feedback">No, you missed this one.</div>
                                </div>
                                <div class="col-3 pl-1">
                                    <label class="col-xl-12 form-label" for="txtNombres">PEP 2</label>
                                    <input type="text" id="txtpep2" name="txtpep2" class="form-control" placeholder="" required maxlength="24">
                                    <div class="invalid-feedback">No, you missed this one.</div>
                                </div>
                                <div class="col-3 pr-1">
                                    <label class="col-xl-12 form-label" for="txtNombres">TIPO PEP</label>
                                    <select id="selectTipoPepM" name="selectTipoPepM" class="select2">
                                            <option selected value ="">Seleccionar Fase</option>
                                            <option value ="1">MAT</option>
                                            <option value ="2">MO</option>
                                            <option value ="3">MAT Y MO</option>
                                    </select>
                                    <div class="invalid-feedback">No, you missed this one.</div>
                                </div>
                                <div class="col-3 pl-1">
                                    <label class="col-xl-12 form-label" for="txtNombres">PROYECTO</label>
                                    <select class="select2" id="cmbProyectoM" name="cmbProyectoM" aria-label="usertype" onchange="getCmbSubProyectoByModal();">
                                        <?php echo $cmbProyecto ?>
                                    </select>
                                    <div class="invalid-feedback">No, you missed this one.</div>
                                </div>                              
                            </div>  
                            <div class="form-group row">
                                <div class="col-6 pl-1">
                                    <label class="col-xl-12 form-label" for="txtNombres">SUB PROYECTO</label>
                                    <select id="cmbSubProyectoM" name="cmbSubProyectoM" class="select2" multiple >                                                                
                                    </select>
                                    <div class="invalid-feedback">No, you missed this one.</div>
                                </div>
                                <div class="col-3 pr-1">
                                    <label class="col-xl-12 form-label" for="txtNombres">FASE</label>
                                    <select class="select2" id="selectFaseM" ame="selectFaseM" aria-label="usertype">                                       
                                        <?php echo $cmbFase ?>
                                    </select>
                                    <div class="invalid-feedback">No, you missed this one.</div>
                                </div>
                                <div class="col-3 pr-1">
                                    <label class="col-xl-12 form-label" for="txtNombres">MES</label>
                                    <select class="select2" id="selectMesM" ame="selectMesM" aria-label="usertype">
                                            <option selected value ="">Seleccionar Mes</option>
                                            <option value ="1">ENERO</option>
                                            <option value ="2">FEBRERO</option>
                                            <option value ="3">MARZO</option>
                                            <option value ="4">ABRIL</option>
                                            <option value ="5">MAYO</option>
                                            <option value ="6">JUNIO</option>
                                            <option value ="7">JULIO</option>
                                            <option value ="8">AGOSTO</option>
                                            <option value ="9">SETIEMBRE</option>
                                            <option value ="10">OCTUBRE</option>
                                            <option value ="11">NOVIEMBRE</option>
                                            <option value ="12">DICIEMBRE</option>
                                    </select>
                                    <div class="invalid-feedback">No, you missed this one.</div>
                                </div>                                
                            </div>
                            
                            <div class="row no-gutters">
                                <div class="col-md-4 ml-auto text-right">
                                    <button id="js-login-btn" type="submit" style="color:white" class="btn btn-block btn-primary btn-md mt-3 addConfig">Registrar</button>
                                </div>
                            </div>
                         
                    </div>                    
                </div>
            </div>
        </div>

        <!-- END Page Settings -->     
        <script src="<?php echo base_url(); ?>public/js/vendors.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/app.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/select2/select2.bundle.js?v=<?php echo time();?>"></script>
		<script src="<?php echo base_url(); ?>public/js/formplugins/inputmask/inputmask.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.export.js"></script>
        <script src="<?php echo base_url(); ?>public/js/loading/jquery.loading.min.js"></script>
        <script src="<?php echo base_url(); ?>public/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/Utils.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/js_itemplan_madre/jsConsultaItmeplanMadre.js?v=<?php echo time();?>"></script>
        <script>    

            function initTableBolsaPep(ejemplo_json){		                             
                table   =  $('#dt-basic-example').DataTable(
                    {   
                        "data": ejemplo_json,
                        "fnDrawCallback": function( oSettings ) {
                            $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                        },
                        responsive: true,
                        dom:"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                        buttons: [   
                            {
                                text: '+ Nueva Configuracion',
                                className: 'btn-outline-default',
                                titleAttr: 'Descargar Destalle Stock',
                                action: function ( e, dt, node, config ) {
                                    $('#txtpep1').val('');
                                    $('#txtpep2').val('');                                   
                                    $('#selectTipoPepM').val('').trigger('change');
                                    $('#cmbProyectoM').val('').trigger('change');
                                    $('#cmbSubProyectoM').val('').trigger('change');
                                    $('#selectFaseM').val('').trigger('change');
                                    $('#selectMesM').val('').trigger('change');
                                    modal('mod_new_config');
                                }
                            },
                            {
                                    extend: 'excelHtml5',
                                    text: 'Exportar Excel',
                                    titleAttr: 'Exportar Excel',
                                    className: 'btn-outline-success btn-sm mr-1',
                                    charset: 'UTF-8',
                                    bom: true
                                }                       
                            
                        ] 
                    });
            }
            $(document).ready(function()
            {   
                initTableBolsaPep(<?php echo json_encode($json_bandeja)?>);
            	$('.select2').select2();
				$('#txtItemplan').inputmask();
            });
            
            function getCmbSubProyectoByProyectoReg() {
                var idProyecto = $('#cmbProyecto option:selected').val();

                if(idProyecto == null || idProyecto == '') {
                    return;
                }

                $.ajax({
                    type : 'POST',
                    url  : 'getSubProyectoByProyecto',
                    data : { idProyecto : idProyecto }
                }).done(function(data){
                    data = JSON.parse(data);

                    if(data.error == 0) {
                        $('#cmbSubProyecto').html(data.cmbSubProyecto);
                    }
                });
            }

            function getCmbSubProyectoByModal() {
                var idProyecto = $('#cmbProyectoM option:selected').val();

                if(idProyecto == null || idProyecto == '') {
                    return;
                }

                $.ajax({
                    type : 'POST',
                    url  : 'getSubProyectoByProyecto',
                    data : { idProyecto : idProyecto }
                }).done(function(data){
                    data = JSON.parse(data);

                    if(data.error == 0) {
                        $('#cmbSubProyectoM').html(data.cmbSubProyecto);
                    }
                });
            }           

            function filtrarTabla(){
                        
                var pep1     = $.trim($('#txtPep1').val());
                var pep2     = $.trim($('#txtPep2').val());
                var proyecto = $('#cmbProyecto').val();
                var subpro   = $('#cmbSubProyecto').val();
                var fase     = $('#selectFase').val();
                var estado   = $('#selectEstado').val();
                $.ajax({
                    type  :	'POST',
                    url   :	'filBoPep',
                    data  :	{pep1     :   pep1,
                             pep2     :   pep2,
                             proyecto :   proyecto,
                             subpro   :   subpro,
                             fase     :   fase,
                             estado   :   estado},
                    beforeSend: () => {
                        $('#btnFiltro').attr("disabled", true);
                    }
                }).done(function(data){
                    var data = JSON.parse(data);
                    if(data.error == 0){
                       // reInitTableLight(data.json_bandeja);   
                       table.destroy();
                       initTableBolsaPep(data.json_bandeja);
                    }else{
                        mostrarNotificacion('error','Aviso',data.msj);
                    }                                   
                }).always(() => {
                    $('#btnFiltro').removeAttr("disabled");
                });  
            }

                 
            $(".addConfig").on("click", function()
            {  
                var txtpep1         = $.trim($('#txtpep1').val());
                var txtpep2         = $.trim($('#txtpep2').val());
                var selectTipoPepM  = $('#selectTipoPepM').val();
                var cmbProyectoM    = $('#cmbProyectoM').val();
                var cmbSubProyectoM = $('#cmbSubProyectoM').val();
                var selectFaseM     = $('#selectFaseM').val(); 
                var selectMesM      = $('#selectMesM').val(); 
                
                if(txtpep1  ==  null    ||  txtpep1 ==  ''){
                    Swal.fire('Debe Ingresar PEP 1.');
                    return;
                }

                if(txtpep2  ==  null    ||  txtpep2 ==  ''){
                    Swal.fire('Debe Ingresar PEP 2.');
                    return;
                }

                if(selectTipoPepM  ==  null    ||  selectTipoPepM ==  ''){
                    Swal.fire('Debe Seleccionar Tipo Pep.');
                    return;
                }

                if(cmbProyectoM  ==  null    ||  cmbProyectoM ==  ''){
                    Swal.fire('Debe Seleccionar Proyecto.');
                    return;
                }

                if(cmbSubProyectoM  ==  null    ||  cmbSubProyectoM ==  ''){
                    Swal.fire('Debe Seleccionar SubProyecto.');
                    return;
                }

                if(selectFaseM  ==  null    ||  selectFaseM ==  ''){
                    Swal.fire('Debe Seleccionar Fase.');
                    return;
                }

                if(selectMesM  ==  null    ||  selectMesM ==  ''){
                    Swal.fire('Debe Seleccionar Mes.');
                    return;
                }

                $.ajax({
                    type	:	'POST',
                    'url'	:	'newConfBoPep',
                    'data'  :   {   txtpep1         : txtpep1,
                                    txtpep2         : txtpep2,
                                    selectTipoPepM  : selectTipoPepM,
                                    cmbProyectoM    : cmbProyectoM,
                                    cmbSubProyectoM : cmbSubProyectoM,
                                    selectFaseM     : selectFaseM,
                                    selectMesM      : selectMesM},
                    'async'	:	false
                }).done(function (data) {
                    var data = JSON.parse(data);
                    if (data.error == 0) {                           
                        Swal.fire('Operacion exitosa, Configuracion registrada.');
                        modal('mod_new_config');
                        filtrarTabla();
                    }else{
                        alert('error intern, refresque y vuelva a intentarlo.');
                    }
                })                  
             }); // ... and by passing a parameter, you can execute something else for "Cancel".
                
function inactivarConfig(component){
    var jsonData = $(component).data();    
    var formData = new FormData();   
    formData.append('id', jsonData.id);
    Swal.queue([
    {
        title: "Está seguro de INACTIVAR la configuracion??",
        text: "Asegurese de validar la información!!",
        icon: 'question',
        confirmButtonText: "SI",
        showCancelButton: true,
        cancelButtonText: 'NO',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function preConfirm()
        {
            return inactivarConfigPromise(formData).then(function (data) { 
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

function inactivarConfigPromise(formData){
    return new Promise(function (resolve, reject) {
        $.ajax({
            type  :	'POST',
            url   :	'updBoPepCon',
            data  :	formData,
            contentType: false,
            processData: false,
            cache: false
        }).done(function(data){
            var data = JSON.parse(data);
            if(data.error == 0){
                filtrarTabla();
                resolve(data);
            }else{
                reject(data);
            }
            
        });
    });
}
        </script>
    </body>
    <!-- END Body -->
</html>
