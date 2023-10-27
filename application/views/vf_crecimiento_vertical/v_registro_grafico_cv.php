<!DOCTYPE html>
<html lang="en">    
<head>
        <meta charset="utf-8">
        <title>
            PANGEACO
        </title>
        <meta name="description" content="Server Error">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
        <!-- Call App Mode on ios devices -->
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <!-- Remove Tap Highlight on Windows Phone IE -->
        <meta name="msapplication-tap-highlight" content="no">
        <!-- base css -->
        <link id="vendorsbundle" rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/vendors.bundle.css">
        <link id="appbundle" rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/app.bundle.css">
        <link id="mytheme" rel="stylesheet" media="screen, print" href="#">
        <link id="myskin" rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/skins/skin-master.css">
        <!-- Place favicon.ico in the root directory -->
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url();?>public/img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url();?>public/img/favicon/favicon-32x32.png">
        <link rel="mask-icon" href="<?php echo base_url();?>public/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/notifications/sweetalert2/sweetalert2.bundle.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/formplugins/select2/select2.bundle.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/datagrid/datatables/datatables.bundle.css">

        <style type="text/css">
            .avatar-char {
                line-height: 3rem;
                font-size: 1.2rem;
                text-align: center;
                color: #FFF;
                font-style: normal;
            }
            .avatar-char, .avatar-img {
                border-radius: 50%;
                width: 3rem;
                height: 3rem;
                margin-right: 1.5rem;
            }

            table.dataTable thead tr {
                background-color: #7a59ad;
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
    <body class="mod-bg-1 mod-nav-link ">
        <!-- DOC: script to save and load page settings -->
        <script>
            /**
             *	This script should be placed right after the body tag for fast execution 
             *	Note: the script is written in pure javascript and does not depend on thirdparty library
             **/
            'use strict';

            var classHolder = document.getElementsByTagName("BODY")[0],
                /** 
                 * Load from localstorage
                 **/
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
                    <header class="page-header" role="banner">
                        <!-- we need this logo when user switches to nav-function-top -->
                        <div class="page-logo">
                            <a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative" data-toggle="modal" data-target="#modal-shortcut">
                                <img src="<?php echo base_url(); ?>public/img/logo.png" alt="SmartAdmin WebApp" aria-roledescription="logo">
                                <span class="page-logo-text mr-1">SmartAdmin WebApp</span>
                                <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
                                <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
                            </a>
                        </div>
                        <!-- DOC: nav menu layout change shortcut -->
                        <div class="hidden-md-down dropdown-icon-menu position-relative">
                            <a href="#" class="header-btn btn js-waves-off" data-action="toggle" data-class="nav-function-hidden" title="Hide Navigation">
                                <i class="ni ni-menu"></i>
                            </a>
                            <ul>
                                <li>
                                    <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-minify" title="Minify Navigation">
                                        <i class="ni ni-minify-nav"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-fixed" title="Lock Navigation">
                                        <i class="ni ni-lock-nav"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- DOC: mobile button appears during mobile width -->
                        <div class="hidden-lg-up">
                            <a href="#" class="header-btn btn press-scale-down" data-action="toggle" data-class="mobile-nav-on">
                                <i class="ni ni-menu"></i>
                            </a>
                        </div>
                     
                        <div class="ml-auto d-flex">
                            <!-- activate app search icon (mobile) 
                            <div class="hidden-sm-up">
                                <a href="#" class="header-icon" data-action="toggle" data-class="mobile-search-on" data-focus="search-field" title="Search">
                                    <i class="fal fa-search"></i>
                                </a>
                            </div>-->
                            <!-- app settings -->
                            <div class="hidden-md-down">
                                <a href="#" class="header-icon" data-toggle="modal" data-target=".js-modal-settings">
                                    <i class="fal fa-cog"></i>
                                </a>
                            </div>                        
                            
                            <!-- app user menu -->
                            <div>
                                <a href="#" data-toggle="dropdown" title="Administrar Cuenta" class="header-icon d-flex align-items-center justify-content-center ml-2">
                                    <img src="<?php echo base_url(); ?>public/img/demo/avatars/avatar-m.png" class="profile-image rounded-circle">
                                    <!-- you can also add username next to the avatar with the codes below:
									<span class="ml-1 mr-1 text-truncate text-truncate-header hidden-xs-down">Me</span>
									<i class="ni ni-chevron-down hidden-xs-down"></i> -->
                                </a>
                                <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
                                    <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
                                        <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                                            <span class="mr-2">
                                                <img src="<?php echo base_url(); ?>public/img/demo/avatars/avatar-m.png" class="rounded-circle profile-image">
                                            </span>
                                            <div class="info-card-text">
                                                <div class="fs-lg text-truncate text-truncate-lg"><?php echo $this->session->userdata('usernameSessionPan') ?></div>
                                                <span class="text-truncate text-truncate-md opacity-80"><?php echo $this->session->userdata('descPerfilSessionPan') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-divider m-0"></div>                                    
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target=".js-modal-settings">
                                        <span data-i18n="drpdwn.settings">Configuracion</span>
                                    </a>
                                    <a class="dropdown-item fw-500 pt-3 pb-3" href="page_login.html">
                                        <span data-i18n="drpdwn.page-logout">Administrar Cuenta</span> 
                                    </a>
                                    <div class="dropdown-divider m-0"></div>
                                    <a class="dropdown-item fw-500 pt-3 pb-3" href="logOut">
                                        <span data-i18n="drpdwn.page-logout">Cerrar Sesion</span> 
                                    </a>
                                </div>
                            </div>
                        </div>
                    </header>
                    <!-- END Page Header -->
                    <!-- BEGIN Page Content -->
                    <!-- the #js-page-content id is needed for some plugins to initialize -->


                    <main id="js-page-content" role="main" class="page-content"> 
                        <div class="subheader">
                            <h1 class="subheader-title">
                                <i class='subheader-icon fal fa-table'></i> BANDEJA REGISTRO GRÁFICO CV
                                <!-- <small>
                                    Create headache free searching, sorting and pagination tables without any complex configuration
                                </small> -->
                            </h1>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div id="panel-1" class="panel">
                                    <div class="panel-hdr">
                                        <!-- <h2>
                                            Example <span class="fw-300"><i>Table</i></span>
                                        </h2> -->
                                        <div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <!-- <div class="panel-tag">
                                                <p>
                                                    The RowGroup extension for DataTables provides an easy to use row grouping feature for DataTables (the clue is in the name!). It can be configured via the <code>rowGroup</code> parameter and you will almost always wish to use the <code>rowGroup.dataSrc</code> option to tell the software what data point in the table's source data to use to get the grouping information.
                                                </p>
                                            </div> -->
                                            <!-- datatable start -->
                                            <div id="contTabla">
                                                <?php echo isset($tablaData) ? $tablaData : null; ?>
                                            <!-- datatable end -->
                                            </div>
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
                                 <!--   <div class="col-6 pl-1">
                                        <a href="#" class="btn btn-danger fw-500 btn-block" data-action="factory-reset">Reinicio de Fabrica</a>
                                    </div>
        -->
                                </div>
                            </div>
                        </div> <span id="saving"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mdlFiltrar" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">FILTRAR</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="settings-panel">
                            <label>Itemplan</label>
                            <input type="text" class="form-control"  id="txtItemplan" name="txtItemplan" placeholder="" data-inputmask="'mask': '99-9999999999'" maxlength="13">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnFiltro" onclick="filtrarTabla();">Aceptar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalRegGrafCV" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white" id="titModalRegGrafCV">
                            CARGAR EVIDENCIA
                            <small class="m-0 text-center color-white">
                                
                            </small>
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="formRegGrafVC">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="archivo">SUBIR EVIDENCIA (.rar,.zip)<span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file border-left-0 bg-transparent pl-0" name="archivo" id="archivo"
                                    accept="application/zip,application/x-zip,application/x-zip-compressed,application/x-7z-compressed,application/x-rar-compressed,.rar">
                                    <div class="invalid-feedback">
                                        Seleccione evidencia.
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnSave" onclick="saveIPxRegistroGrafico(this)">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        

        <nav class="shortcut-menu d-none d-sm-block">
            <input type="checkbox" class="menu-open" name="menu-open" id="menu_open" />
            <label for="menu_open" class="menu-open-button ">
                <span class="app-shortcut-icon d-block"></span>
            </label>
            <a href="#" class="menu-item btn" data-toggle="tooltip" data-placement="left" title="Scroll Top">
                <i class="fal fa-arrow-up"></i>
            </a>
            <a href="#" class="menu-item btn" data-action="app-fullscreen" data-toggle="tooltip" data-placement="left" title="Full Screen">
                <i class="fal fa-expand"></i>
            </a>
            <a href="#" class="menu-item btn" data-action="app-print" data-toggle="tooltip" data-placement="left" title="Print page">
                <i class="fal fa-print"></i>
            </a>
            <a class="menu-item btn" data-toggle="tooltip" data-placement="left" title="Logout">
                <i class="fal fa-sign-out"></i>
            </a>
            <a class="menu-item btn" data-toggle="tooltip" data-placement="left" title="Filtrar" onclick="openModalFiltrar();">
                <i class="fal fa-filter"></i>
            </a>
        </nav>

        <!-- END Page Settings -->    
        <script src="<?php echo base_url(); ?>public/js/vendors.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/app.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/select2/select2.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/inputmask/inputmask.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>

        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.export.js"></script>
        <script src="<?php echo base_url(); ?>public/js/Utils.js?v=<?php echo time();?>"></script>

        <script type="text/javascript">

            // var controls = {
            //     leftArrow: '<i class="fal fa-angle-left" style="font-size: 1.25rem"></i>',
            //     rightArrow: '<i class="fal fa-angle-right" style="font-size: 1.25rem"></i>'
            // };

            // $('#txtFechaCita').datepicker(
            // {
            //     todayBtn: "linked",
            //     clearBtn: true,
            //     todayHighlight: true,
            //     templates: controls,
            //     format: 'dd-mm-yyyy'
            // });
            
            // 
            
            $(document).ready(function()
            {
                var tbGlob = initDataTable('tb_bandeja_reg_graf');
                $('.select2').select2();
                $('#txtItemplan').inputmask();
            });

            function openModalFiltrar() {
                $('#txtItemplan').val(null);
                modal('mdlFiltrar');
            }


            function filtrarTabla(){
			
                var itemplan = $('#txtItemplan').val();
                $.ajax({
                    type  :	'POST',
                    url   :	'getBandejaRegGrafCVByFiltros',
                    data  :	{ 
                        itemplan : itemplan
                    },
                    beforeSend: () => {
                        $('#btnFiltro').attr("disabled", true);
                    }
                }).done(function(data){
                    var data = JSON.parse(data);
                    if(data.error == 0){
                        $('#contTabla').html(data.tbReporte);
                        initDataTable('tb_bandeja_reg_graf');

                    }else{
                        mostrarNotificacion('error','Aviso',data.msj);
                    }

                }).always(() => {
                    $('#btnFiltro').removeAttr("disabled");
                });  
            }

            function openModalUpdate(component){
                jsonData = $(component).data();
                $('#formRegGrafVC').trigger("reset");
                $('#btnSave').data('itemplan',jsonData.itemplan);
                $('#btnSave').data('estacion',jsonData.estacion);
                $('#btnSave').data('siropefo',jsonData.siropefo);
                $('#btnSave').data('siropecoax',jsonData.siropecoax);
                $('#btnSave').data('estadesc',jsonData.estadesc);
                $(".invalid-feedback").css('display','none');
                $(".valid-feedback").css('display','none');
                $('.is-valid').removeClass("is-valid");
                $('.is-invalid').removeClass("is-invalid");
                $('#titModalRegGrafCV').text('Itemplan : '+jsonData.itemplan);
                $('#titModalRegGrafCV').append(
                '<small class="m-0 text-muted">'+
                    'Estación: '+jsonData.estadesc+
                '</small>');
                modal('modalRegGrafCV');

            }

            function saveIPxRegistroGrafico(component){

                var params = $("#formRegGrafVC").serializeArray();//(lee todos los inputs select con name y que no sean disabled)
                var formData = new FormData();
                var jsonData = $(component).data();

                console.log(jsonData);

                var comprobar = $('#archivo').val().length;
                if(comprobar == 0){
                    var divPadre = $('#archivo').parent();
                    var divMsj = divPadre.children().eq(2);
                    divMsj.removeClass('valid-feedback invalid-feedback');
                    $('#archivo').removeClass('is-valid is-invalid');
                    $('#archivo').addClass('form-control is-invalid');
                    divMsj.addClass('invalid-feedback');
                    divMsj.text('Seleccione un archivo.');
                    divMsj.css('display','block');
                    swal.fire('Verificar!','Debe subir un archivo para guardar!!','warning');
                    return;
                }

                var file = $('#archivo')[0].files[0];

                formData.append('file', file);
                formData.append('itemplan', jsonData.itemplan);
                formData.append('idEstacion', jsonData.estacion);
                formData.append('has_sirope_fo', jsonData.siropefo);
                formData.append('has_sirope_coax', jsonData.siropecoax);
                formData.append('descEstacion', jsonData.estadesc);

                Swal.queue([
                {
                    title: "Está seguro de realizar la operación??",
                    text: "Asegurese de validar la información!!",
                    icon: 'question',
                    confirmButtonText: "SI",
                    showCancelButton: true,
                    cancelButtonText: 'NO',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function preConfirm()
                    {
                        return saveIPxRegistroGrafPromise(formData,jsonData.itemplan).then(function (data) { 
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

            function saveIPxRegistroGrafPromise(formData,itemplan){
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        type  :	'POST',
                        url   :	'saveIPxRegGraficoCV',
                        data  :	formData,
                        contentType: false,
                        processData: false,
                        cache: false
                    }).done(function(data){
                        var data = JSON.parse(data);
                        if(data.error == 0){
                            $('#contTabla').html(data.tbReporte);
                            initDataTable('tb_bandeja_reg_graf');
                            modal('modalRegGrafCV');
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
