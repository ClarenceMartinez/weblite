<!DOCTYPE html>
<html lang="en"  class="root-text-sm">
<head>
        <html lang="en"  class="root-text-sm"> 
        <title>
            PangeaCo | BANDEJA APROBACIÓN VR
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
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"> -->
        <style>
          
			.swal2-container {
                z-index: 9000;
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
                                                BANDEJA DE VALIDACION DE OBRAS
                                        </h2>
                                        
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">             
                                            <!--                        
                                            <div class="row" style="-20px;">                                              
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-6">                                                            
                                                    <div class="form-group">
                                                        <label class="form-label" for="txt_nom_pro">CODIGO SOLICITUD</label>
                                                        <input type="text" class="form-control" id="txt_codigo_solicitud" placeholder="Ingrese Codigo de Obra" required>
                                                        <div class="invalid-feedback">Ingrese codigo de Solicitud</div>
                                                    </div>                                       
                                                    </p>
                                                </div>  
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-6">                                                            
                                                    <div class="form-group">
                                                        <label class="form-label" for="txt_nom_pro">SISEGO</label>
                                                        <input type="text" class="form-control" id="txt_cod_obra" placeholder="Ingrese Codigo de Sisego" required>
                                                        <div class="invalid-feedback">Ingrese codigo de Sisego</div>
                                                    </div>                                       
                                                    </p>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-6">  
                                                    <label>ESTADO</label>
                                                    <select class="select2" id="selectEstado" aria-label="usertype">
                                                        <option value="">TODO</option>
                                                        <option selected value="0">PENDIENTE COTIZAR</option>
                                                        <option value="1">PDT APROBAR</option>
                                                        <option value="2">APROBADO</option>
                                                        <option value="3">RECHAZADO</option>
                                                     </select>
                                                    <div class="invalid-tooltip">
                                                        Seleccionar Estado.
                                                    </div>
                                                </div>  
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-6">
                                                    <a style="/*margin-left: 15px;*/ margin-top: 25px;/* height: 50%;*/" class="btn btn-md btn-outline-primary waves-effect waves-themed searchT">
                                                            <span class="fal fa-search mr-1"></span>
                                                            Buscar
                                                    </a> 
                                                </div>                                          
                                            </div>
                                            -->
                                            <table id="dt-basic-example" class="table table-bordered table-sm table-hover table-striped w-100">
                                                    <thead class="bg-primary-600"">
                                                        <th>ACCIÓN</th>  
                                                        <th style="width:80px">FILES</th>
                                                        <th>ITEMPLAN</th>                            
                                                        <th>PROYECTO</th>
                                                        <th>SUBPROYECTO</th>
                                                        <th>INDICADOR</th>
                                                        <th>EECC</th>
                                                        <th>JEFATURA</th>
                                                        <th>UIP</th>
                                                        <th>COSTO INICIAL</th>
                                                        <th>COSTO TOTAL</th>
                                                        <th>FECHA REGISTRO</th>
                                                        <th>USUARIO REGISTRO</th>
                                                        <th>ESTADO</th>
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

 

		<div class="modal fade modal-fullscreen" id="modalValidarEstacion" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="overflow: scroll;">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white" id="titModalValidarEstacion">
                            ESTACIONES
                            <small class="m-0 text-center color-white">
                                ItemPlan: 
                            </small>
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="settings-panel">
                            <div id="contTablaPdt">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mdlFiltrar" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">FILTRAR</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formFiltrar">
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="txtItemplan">Itemplan</label>
                                    <input type="text" class="form-control" id="txtItemplan" name="txtItemplan" placeholder="" data-inputmask="'mask': '99-9999999999'" maxlength="13" required>
                                    <div class="valid-feedback">
                                        Correcto!
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="selectEmpresColab">Empresa Colaboradora</label>
                                    <select id="selectEmpresColab" name="selectEmpresColab" class="select2 form-control w-100">
                                        <option value="">Seleccionar</option>
                                        <?php //echo isset($cmbEmpresaColab) ? $cmbEmpresaColab : null ?>
                                    </select>
                                    <div class="valid-feedback">
                                        Correcto!
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnFiltro" onclick="filtrarTabla();">Aceptar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
       
        <!-- END Page Settings -->     
        <script src="<?php echo base_url(); ?>public/js/vendors.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/app.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/select2/select2.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/inputmask/inputmask.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.export.js"></script>
        <script src="<?php echo base_url(); ?>public/js/loading/jquery.loading.min.js"></script>
        <script src="<?php echo base_url(); ?>public/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/Utils.js?v=<?php echo time();?>"></script>
        <!-- <script src="<?php echo base_url(); ?>public/js/js_consulta/jsConsulta.js?v=<?php echo time();?>"></script> -->
        <script>
            $(document).ready(function()
            {
               // initDataTable('tbBandejaValObra',3);
               initTableLight(<?php echo json_encode($json_bandeja)?>);
                $('.select2').select2();
            //    $('#txtItemplan').inputmask();
            });

            function openModalFiltrar() {
                $('#formFiltrar').trigger("reset");
                modal('mdlFiltrar');
            }


            function filtrarTabla(){
			
                var itemplan = $('#txtItemplan').val();
                itemplan = itemplan.replace(/_/g, '');
                var idEECC = $('#selectEmpresColab').val();

                $.ajax({
                    type  :	'POST',
                    url   :	'getBandejaValObrasByFiltros',
                    data  :	{ 
                        itemplan : itemplan,
                        idEECC: idEECC
                    },
                    beforeSend: () => {
                        $('#btnFiltro').attr("disabled", true);
                    }
                }).done(function(data){
                    var data = JSON.parse(data);
                    if(data.error == 0){
                        $('#contTabla').html(data.tbBandejaAprob);
                        initDataTable('tbBandejaValObra',3);

                    }else{
                        mostrarNotificacion('error','Aviso',data.msj);
                    }

                }).always(() => {
                    $('#btnFiltro').removeAttr("disabled");
                });  
            }


            function viewDetallePartidas(component){
                console.log('entro');
            	var jsonData = $(component).data();
                console.log(jsonData);
                var subtitulo = $('#titModalValidarEstacion').children().eq(0);
                subtitulo.text('ItemPlan: '+jsonData.itemplan);
                $.ajax({
                    type : 'POST',
                    url  : 'getContPartPndtVal',
                    data : { 
                        itemplan : jsonData.itemplan,
                        idEstacion : jsonData.esta,
                        idSol : jsonData.idsol
                    }
                }).done(function(data){
                    data = JSON.parse(data);
                    console.log(data);
                    if(data.error == 0) { 
                        $('#contTablaPdt').html(data.tablaPdt);
                        initValNivel1();
                        initValNivel2();
                        initRejectSol();
                        
                        modal('modalValidarEstacion');
                    } else {
                        swal.fire('Error!',data.msj,'error');
                    }
                })
            }

            function initValNivel1(){
                $('.valNi1').click(function(e){
                    var jsonData = $(this).data();
                    console.log(jsonData);
                    var component = $(this);
                    var formData = new FormData();
                    formData.append('idEstacion', jsonData.ides);
                    formData.append('itemplan', jsonData.item);
                    formData.append('idSolicitud', jsonData.idsol);

                    Swal.queue([
                    {
                        title: "Está seguro de validar las partidas??",
                        text: "Asegurese de validar la información!!",
                        icon: 'question',
                        confirmButtonText: "SI",
                        showCancelButton: true,
                        cancelButtonText: 'NO',
                        allowOutsideClick: false,
                        showLoaderOnConfirm: true,
                        preConfirm: function preConfirm()
                        {
                            return validarNivel1Promise(formData,component).then(function (data) { 
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
                });

                $('.getRechazado2Bucles').click(function(e){
                    var item       = $(this).attr('data-item');
                    var idEstacion = $(this).attr('data-esta');

                    $.ajax({
                        type : 'POST',
                        url  : 'getInfoRech2BuclesRe',
                        data : { itemplan   : item,
                                idEstacion : idEstacion}
                    }).done(function(data){
                        data = JSON.parse(data);
                        if(data.error == 0) { 
                        console.log('bbbb');
                            $('#contTablaRechazo').html(data.tablaRechazado);//reutilizamos el modal
                            modal('modalRechazado');//reutilizamos el modal
                        } else {
                            mostrarNotificacion(1,'error', data.msj, 'error');
                        }
                    })
                });
            }

            function validarNivel1Promise(formData,component){
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        type  :	'POST',
                        url   :	'validarNivel1',
                        data  :	formData,
                        contentType: false,
                        processData: false,
                        cache: false,
                        beforeSend: () => {
                            $(component).attr("disabled", true);
                        }
                    }).done(function(data){
                        var data = JSON.parse(data);
                        if(data.error == 0){
                            $('#contTabla').html(data.tablaValidacionObra);
                            modal('modalValidarEstacion');
                            location.reload();
                            resolve(data);
                        }else{
                            reject(data);
                        }
                    }).always(() => {
                        $(this).removeAttr("disabled");
                    });
                });
            }

            function initRejectSol(){
                $('.rejectSol').click(function(e){
                    var jsonData = $(this).data();
                    console.log(jsonData);
                    var component = $(this);
                    var formData = new FormData();
                    formData.append('idEstacion', jsonData.ides);
                    formData.append('itemplan', jsonData.item);
                    formData.append('idSolicitud', jsonData.idsol);
                    formData.append('from', jsonData.from);//1= validacion 1, 2 = validacion 2
                    modal('modalValidarEstacion');
                    Swal.queue([
                    {
                        title: "Está seguro de rechazar la solicitud??",
                        // text: "Asegurese de validar la información!!",
                        html : '<div class="form-group">'+
                                    '<a>Ingrese comentario de rechazo</a>'+
                                '</div>'+
                                '<div>'+
                                    '<textarea class="col-md-12 form-control" placeholder="Ingresar Comentario..." style="background:#F9F8CF" rows="4" id="comentarioText"></textarea>'+
                                '</div>',
                        icon: 'question',
                        confirmButtonText: "SI",
                        showCancelButton: true,
                        cancelButtonText: 'NO',
                        allowOutsideClick: false,
                        showLoaderOnConfirm: true,
                        preConfirm: function preConfirm()
                        {
                            return rejectSolAdPromise(formData,component).then(function (data) { 
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
                });
            }

            function rejectSolAdPromise(formData,component){
                var comentario = $('#comentarioText').val();
                if(comentario == null || comentario == '' || comentario == undefined){
                    swal.fire('Aviso!','Debe ingresar un comentario!!','warning');
                    return;
                }
                formData.append('comentario',comentario);
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        type  :	'POST',
                        url   :	'rejectSolAdPqt',
                        data  :	formData,
                        contentType: false,
                        processData: false,
                        cache: false,
                        beforeSend: () => {
                            $(component).attr("disabled", true);
                        }
                    }).done(function(data){
                        var data = JSON.parse(data);
                        if(data.error == 0){
                            // modal('modalValidarEstacion');
                            //resolve(data);
                            location.reload();
                        }else{
                            reject(data);
                        }
                    }).always(() => {
                        $(this).removeAttr("disabled");
                    });
                });
            }

            function initValNivel2(){
                $('.gpoMo').click(function(e){
                    var jsonData = $(this).data();
                    console.log(jsonData);
                    var component = $(this);
                    var formData = new FormData();
                    formData.append('idEstacion', jsonData.ides);
                    formData.append('itemplan', jsonData.item);
                    formData.append('idSolicitud', jsonData.idsol);

                    Swal.queue([
                    {
                        title: "Está seguro de aprobar la porpuesta??",
                        text: "Asegurese de validar la información!!",
                        icon: 'question',
                        confirmButtonText: "SI",
                        showCancelButton: true,
                        cancelButtonText: 'NO',
                        allowOutsideClick: false,
                        showLoaderOnConfirm: true,
                        preConfirm: function preConfirm()
                        {
                            return validarNivel2Promise(formData,component).then(function (data) { 
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
                });

                    $('.valNi2NoPqt').click(function(e){
                     
                    var id_estacion = $(this).attr('data-idEs');
                    var item_sa     = $(this).attr('data-item');
                    var id_sol      = $(this).attr('data-idsol');
                    $(this).prop('disabled', true);  
                    Swal.fire({
                            title: 'Esta seguro aprobar la propuesta?',
                            text: 'Asegurese de que la informacion sea la correta.',
                            type: 'warning',
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonClass: 'btn btn-primary',
                            confirmButtonText: 'Si, guardar los datos!',
                            cancelButtonClass: 'btn btn-secondary',
                            allowOutsideClick: false
                        }).then(function(){
                            console.log('ok');
                            
                                    $.ajax({
                                        type	:	'POST',
                                        'url'	:	'valProNiv2NoPqt',
                                        data	:	{   itemplan    :   item_sa,
                                                        idEstacion  :   id_estacion,
                                                        idSolicitud :   id_sol},
                                        'async'	:	false
                                    })
                                    .done(function(data){
                                    console.log('ok.....');
                                        $(this).prop('disabled', false);
                                        var data	=	JSON.parse(data);
                                        console.log(data);
                                        if(data.error == 0){
                                            Swal.fire({
                                                        title: 'Se aprobo La propuesta',
                                                        text: 'Asegurese de validar la informacion!',
                                                        type: 'success',
                                                        buttonsStyling: false,
                                                        confirmButtonClass: 'btn btn-primary',
                                                        confirmButtonText: 'OK!'            
                                            }).then(function () {
                                                console.log('gogogogo');
                                                location.reload();
                                            });
                                                
                                        }else if(data.error == 1){
                                            $(this).prop('disabled', false);
                                            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
								            return;
                                        }else if(data.error == 3){
                                            $(this).prop('disabled', false);
                                            mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
								            return;
                                        }
                                    });
                        }) 
                });
            }

            function validarNivel2Promise(formData,component){
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        type  :	'POST',
                        url   :	'validarNivel2',
                        data  :	formData,
                        contentType: false,
                        processData: false,
                        cache: false,
                        beforeSend: () => {
                            $(component).attr("disabled", true);
                        }
                    }).done(function(data){
                        var data = JSON.parse(data);
                        if(data.error == 0){
                            $('#contTabla').html(data.tablaValidacionObra);
                            modal('modalValidarEstacion');
                            location.reload();
                            resolve(data);
                        }else{
                            reject(data);
                        }
                    }).always(() => {
                        $(this).removeAttr("disabled");
                    });
                });
            }            
            
        function expedienteLiqui(itemplan, idEstacion) {
			console.log('here');
            $.ajax({
                type: 'POST',
                dataType: "JSON",
                'url': 'getExpeLiqui',
                data: {itemplan: itemplan,
                	idEstacion :   idEstacion}
            }).done(function (data) {
                //console.log('herer2');
				//data = JSON.parse(data);
                if (data.path == '1') {
                    location.href = utf8_decode(data.ruta);
                } else {
                    mostrarNotificacion(1,'warning', 'Mensaje', 'Sin datos para descargar');
                }

            });
        }

        function liquidacion(itemPlan) {
            $.ajax({
                type: 'POST',
                dataType: "JSON",
                'url': 'liquidacion',
                data: {itemPlan: itemPlan}
            }).done(function (data) {
                console.log(data);
                if (data.path == '1') {
                    location.href = 'liquidacion_download?' + 'itemPlan=' + itemPlan;
                } else {
                    mostrarNotificacion(1,'warning', 'Mensaje', 'Sin datos para descargar');
                }

            });
        }

        function disenho(itemPlan) {
            $.ajax({
                type: 'POST',
                dataType: "JSON",
                'url': 'disenho',
                data: {itemPlan: itemPlan}
            }).done(function (data) {
                console.log(data);
                if (data.path == '1') {
                    location.href = 'disenho_download?' + 'itemPlan=' + itemPlan;
                } else {
                    mostrarNotificacion(1, 'warning', 'Mensaje', 'Sin datos para descargar');
                }

            });
        }

        function licencias(itemPlan) {
            $.ajax({
                type: 'POST',
                dataType: "JSON",
                'url': 'licencias',
                data: {itemPlan: itemPlan}
            }).done(function (data) {
                console.log(data);
                if (data.path == '1') {
                    location.href = 'licencias_download?' + 'itemPlan=' + itemPlan;
                } else {
                    mostrarNotificacion(1, 'warning', 'Mensaje', 'Sin datos para descargar');
                }

            });
        }

        function utf8_decode (strData) {  
            
            const tmpArr = []
            let i = 0
            let c1 = 0
            let seqlen = 0
            strData += ''
            while (i < strData.length) {
            c1 = strData.charCodeAt(i) & 0xFF
            seqlen = 0
            // https://en.wikipedia.org/wiki/UTF-8#Codepage_layout
            if (c1 <= 0xBF) {
                c1 = (c1 & 0x7F)
                seqlen = 1
            } else if (c1 <= 0xDF) {
                c1 = (c1 & 0x1F)
                seqlen = 2
            } else if (c1 <= 0xEF) {
                c1 = (c1 & 0x0F)
                seqlen = 3
            } else {
                c1 = (c1 & 0x07)
                seqlen = 4
            }
            for (let ai = 1; ai < seqlen; ++ai) {
                c1 = ((c1 << 0x06) | (strData.charCodeAt(ai + i) & 0x3F))
            }
            if (seqlen === 4) {
                c1 -= 0x10000
                tmpArr.push(String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF)))
                tmpArr.push(String.fromCharCode(0xDC00 | (c1 & 0x3FF)))
            } else {
                tmpArr.push(String.fromCharCode(c1))
            }
            i += seqlen
            }
            return tmpArr.join('')
        }
        
        </script>
    </body>
    <!-- END Body -->
</html>
