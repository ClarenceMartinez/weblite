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
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/datagrid/datatables/datatables.bundle.css?v=<?php echo time();?>">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/notifications/sweetalert2/sweetalert2.bundle.css">
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url();?>public/css/utils/select2_theme.css">
        <link rel="stylesheet" href="<?php echo base_url();?>public/css/bootstrap-validator/bootstrapValidator.min.css"></link>
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"> -->
        <style>

            .swal2-container {
                z-index: 10000;
            }
            #divMapCoordenadas{
            	height: 450px;    
                width: 1650px;  
            }

            .modal {
                right: 150px;
                left : auto;
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
                                                COTIZACION B2B - <?php echo $codigo ?> <?php echo ($flg_principal == 0) ? '(SISEGO PRINCIPAL)' : '(SISEGO RESPALDO)';  ?>
                                        </h2>
                                        
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <form id="formAddPlanobra" method="post" class="form-horizontal"  enctype="multipart/form-data"> 
                                                <div class="row">
                                                
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group">
                                                            <label>TIPO RED</label>
                                                            <select id="selectTipoRed" name="selectTipoRed" class="select2 form-control" onchange="getCentral(this);">
                                                                <option value="">Seleccionar Tipo de Red</option>
                                                                <option value="1">RED EXISTENTE</option>
                                                                <option value="2">RED NUEVA</option>
                                                                    
                                                            </select>
                                                            <div id="mensajeTipoRed"></div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group">
                                                            <label>NODO PRINCIPAL</label>
                                                            <select id="selectCentral" name="selectCentral" class="select2 form-control" onchange="getDataSeiaMtc(3);">
                                                                <option value="">&nbsp;</option>  
                                                            </select>
                                                            <div id="mensajeNodoPrincipal"></div>
                                                        </div>
                                                    </div> 

                                                    <div class="col-sm-3 col-md-3" style="display: none">
                                                        <div class="form-group">
                                                            <label>NODO RESPALDO</label>
                                                            <select disabled id="selectCentral2" name="selectCentral2" class="select2 form-control" onchange="getDataSeiaMtc(1);">
                                                                <option value="">&nbsp;</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contSelecOtroOperador" style="display:none;">
                                                        <div class="form-group">
                                                            <label>&#191;NODO OTRO OPERADOR?</label>
                                                            <select id="selecElegirNodoOtroOperador" name="selecElegirNodoOtroOperador" class="select2 form-control" onchange="getNodoOtroOperador();">
                                                                <option value="">Seleccionar SI/NO</option>
                                                                <option value="1">SI</option>
                                                                <option value="2">NO</option>                                                                
                                                            </select>
                                                            <div id="mensajeOptionEbc"></div>
                                                        </div>
                                                    </div>
                                                    <div id="contNodoOtroOperador" class="col-sm-3 col-md-3" style="display:none;">
                                                        <div class="form-group">
                                                            <label>SELECCIONAR NODO OTRO OPERADOR</label>
                                                            <select id="cmbNodoOtroOperador" name="cmbNodoOtroOperador" class="select2 form-control">
                                                                                                            
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contSelectEbc" style="display:none">
                                                        <div class="form-group">
                                                            <label>&#191;EBC?</label>
                                                            <select disabled id="selecElegirEbc" name="selecElegirEbc" class="select2 form-control" onchange="getEbcByDistritoByDistrito();">
                                                                <option value="">Seleccionar SI/NO</option>
                                                                <option value="1">SI</option>
                                                                <option value="2">NO</option>                                                                
                                                            </select>
                                                            <div id="mensajeOptionEbc"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div id="contEbcs" class="col-sm-3 col-md-3" style="display:none;">
                                                        <div class="form-group">
                                                            <label>SELECCIONAR EBC</label>
                                                            <select id="cmbEbc" name="cmbEbc" class="select2 form-control">
                                                                                                            
                                                            </select>
                                                        </div>
                                                    </div>                                                    
                                          
                                                    <div id="contEbcs" class="col-sm-3 col-md-3">
                                                        <div class="form-group">
                                                            <label>SELECCIONAR FACILIDAD RED</label>
                                                            <select id="cmbFacilidades" name="cmbFacilidades" class="select2 form-control">
                                                                                                            
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contTroba" style="display:none">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>TROBA</label>
                                                            <input id="inputTroba" name="inputTroba" type="text" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeInputTroba"></div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="col-sm-3 col-md-3" id="contCantTroba" style="display:none">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANTIDAD TAP'S</label>
                                                            <input id="inputCantTroba" name="inputCantTroba" type="text" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeInputCantTroba"></div>
                                                        </div>
                                                    </div>                                               
                                                 
                                                    <div class="col-sm-3 col-md-3" id="contKickoff">
                                                        <div class="form-group">
                                                            <label>REQUIERE SEIA</label>
                                                            <div style="background:#FEFAF9">
                                                                <select id="selectRequeSeia" name="selectRequeSeia" class="select2 form-control" onchange="getDiasMatriz();" disabled>
                                                                    <option value="">Seleccionar</option>  
                                                                    <option value="NO">NO</option>     
                                                                    <option value="SI">SI</option>                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-sm-3 col-md-3" id="contKickoff">
                                                        <div class="form-group">
                                                            <label>REQUIERE APROBACION MML, MTC</label>
                                                            <div style="background:#FEFAF9">
                                                    
                                                                <input id="selectRequeAproMmlMtc" name="selectRequeAproMmlMtc" class="form-control" style="background:#FEFAF9" disabled>
                                                                <div id="mensajeMtc"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contKickoff">
                                                        <div class="form-group">
                                                            <label>REQUIERE APROBACION INC(PMA)</label>
                                                            <select id="selectRequeAprobINC" name="selectRequeAprobINC" class="select2 form-control" onchange="getDiasMatriz();">
                                                                <option value="">Seleccionar</option>  
                                                                <option value="NO">NO</option>     
                                                                <option value="SI">SI</option>                                                    
                                                            </select>
                                                        </div>
                                                    </div>   
                                                
                                                <div class="col-sm-3 col-md-3" id="contKickoff">
                                                        <div class="form-group">
                                                            <label>DURACI&Oacute;N (D&Iacute;AS)</label>
                                                            <input id="inputDias" name="inputDias" class="form-control" style="background:#FEFAF9" disabled>
                                                         
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeInputDias"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contKickoff">
                                                        <div class="form-group">
                                                            <label>TIPO DISE&Ntilde;O</label>
                                                            <select id="cmbTipoDiseno" name="cmbTipoDiseno" class="select2 form-control" onchange="setQuinceDias();">
                                                                <option value=""></option>
                                                                <?php foreach($arrayTipoDiseno AS $row) {
                                                                    echo '<option value="'.$row['id_tipo_diseno'].'">'.$row['descripcion'].'</option>';
                                                                } ?>
                                                            </select>
                                                            <div id="mensajeTipoDiseno"></div>
                                                        </div>    
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group">
                                                            <label>TIPO DE ENLACE</label>
                                                            <select id="cmbTipoEnlace" name="cmbTipoEnlace" class="select2 form-control">
                                                                <option value=""></option>
                                                                <option value="TIPO 1">TIPO 1</option>
                                                                <option value="TIPO 2">TIPO 2</option>
                                                            </select>
                                                            <div id="mensajeTipoEnlace"></div>
                                                        </div>    
                                                    </div>                                                                                                   
                                                </div>

                                                <div class="row" style="margin-top: 20px;"><!-- INICIO AEREO-->
                                                    <div class="col-sm-12 col-md-12" style="padding-bottom: 15px;">
                                                        <b>AEREO</b>
                                                    </div>                                                    
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>METROS TENDIDO A&Eacute;REO</label>
                                                            <input id="inputMetroTenAereo" step="0.01" name="inputMetroTenAereo" type="number" class="form-control" onchange="getDataSeiaMtc();"><i class="form-control-feedback" data-bv-icon-for="inputCorreP"></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>                 
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANT. POSTES NUEVOS</label>
                                                            <input id="inputPostNue" name="inputPostNue" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>  
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANT. POSTES DE APOYO</label>
                                                            <input id="inputCantPostApo" name="inputCantPostApo" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANTIDAD CTO</label>
                                                            <input id="inputCantCTO" name="inputCantCTO" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANTIDAD DIVICAU</label>
                                                            <input id="txtDivcau" name="txtDivcau" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANTIDAD EMPALMES 16F/32F</label>
                                                            <input id="txtEmpal1632" name="txtEmpal1632" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANTIDAD EMPALMES 64F</label>
                                                            <input id="txtEmpal64" name="txtEmpal64" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANTIDAD EMPALMES 128F</label>
                                                            <input id="txtEmpal128" name="txtEmpal128" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANTIDAD EMPALMES 256F</label>
                                                            <input id="txtEmpal256" name="txtEmpal256" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANTIDAD CRUCETA</label>
                                                            <input id="txtCruceta" name="txtCruceta" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CABLES APOYADOS POSTE TELEFONICO</label>
                                                            <input id="txtcapotel" name="txtcapotel" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contKickoff">
                                                        <div class="form-group">
                                                            <label>OPERADOR</label>
                                                            <select id="selectOperador" name="selectOperador" class="select2 form-control">
                                                                <option value="">Seleccionar</option>  
                                                                <option value="TELEFONICA">TELEFONICA</option>     
                                                                <option value="CLARO">CLARO</option>                                                    
                                                            </select>
                                                        </div>
                                                    </div>   
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CABLES APOYADOS POSTE ELECTRICO</label>
                                                            <input id="txtcaPosElec" name="txtcaPosElec" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contKickoff">
                                                        <div class="form-group">
                                                            <label>EMPRESA ELECTRICA</label>
                                                            <select id="selectEmpresaElec" name="selectEmpresaElec" class="select2 form-control">
                                                                <option value="">Seleccionar</option>  
                                                                <option value="LUZ DEL SUR">LUZ DEL SUR</option>     
                                                                <option value="ENEL">ENEL</option>                                                    
                                                            </select>
                                                        </div>
                                                    </div>  
                                                </div><!--FIN AEREO-->
                                                <div class="row" style="margin-top: 20px;"><!-- INICIO SUBTERRANEO-->
                                                    <div class="col-sm-12 col-md-12" style="padding-bottom: 15px;">
                                                        <b>SUBTERRANEO</b>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>METROS TENDIDO SUBTERRANEO</label>
                                                            <input id="inputMetroTenSubt" step="0.01" name="inputMetroTenSubt" type="number" class="form-control" onchange="getDataSeiaMtc();"><i class="form-control-feedback" data-bv-icon-for="inputCorreP"></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>METROS NUEVA CANALIZACI&Oacute;N</label>
                                                            <input id="inputMetroCana" name="inputMetroCana" type="text" class="form-control" onchange="getDiasMatriz();"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANT. CAMARAS NUEVAS</label>
                                                            <input id="cantCamaNue" name="cantCamaNue" type="text" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>  
                                                    
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>CANT. APERTURA DE C&Aacute;MARA</label>
                                                            <input id="inputCantAperCamara" name="inputCantAperCamara" type="text" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>  

                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>DUCTO 2" (Metros)</label>
                                                            <input id="txtDuctoN2" name="txtDuctoN2" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>DUCTO 3" (Metros)</label>
                                                            <input id="txtDuctoN3" name="txtDuctoN3" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contCantCto">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>DUCTO 4" (Metros)</label>
                                                            <input id="txtDuctoN4" name="txtDuctoN4" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3" id="contKickoff">
                                                        <div class="form-group">
                                                            <label>OPERADOR</label>
                                                            <select id="selectOperaSubte" name="selectOperaSubte" class="select2 form-control">
                                                                <option value="">Seleccionar</option>  
                                                                <option value="TELEFONICA">TELEFONICA</option>     
                                                                <option value="CLARO">CLARO</option>                                                    
                                                            </select>
                                                        </div>
                                                    </div>  
                                                </div><!--FIN SUBTERRANEO-->
                                                <div class="row" style="margin-top: 20px;">
                                                    <div class="col-sm-12 col-md-12" style="padding-bottom: 15px;">
                                                            <b>COSTOS</b>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>COSTO OBRA CIVIL</label>
                                                            <input onchange="getcalculos()" id="inputCostoOc" name="inputCostoOc" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeCostoMat"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>COSTO MATERIALES</label>
                                                            <input onchange="getcalculos()" id="inputCostoMat" name="inputCostoMat" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeCostoMat"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>COSTO MANO DE OBRA</label>
                                                            <input onchange="getcalculos()" id="inputCostMo" value="<?php echo isset($costo_pqt_mo) ? $costo_pqt_mo : NULL; ?>" name="inputCostMo" type="number" class="form-control"><i class="form-control-feedback" data-bv-icon-for="inputCorreP"></i>
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeCostoMo"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>COSTO EXPEDIENTE EIA,CIRA,PMEA S./</label>
                                                            <select id="cmbMontoEIA" name="cmbMontoEIA" class="select2 form-control" onchange="getcalculos()">
                                                                <option value="">Seleccionar monto</option>
                                                                <option value="0" selected>0</option>
                                                            </select>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>COSTO ADICIONALES ZONA RURAL S./</label>
                                                            <input onchange="getcalculos()" id="inputCostoAdicZona" name="inputCostoAdicZona" type="number" class="form-control" disabled><i class="form-control-feedback" data-bv-icon-for="inputCorreP" ></i>
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeCostoAdicional"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-3">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>COSTO TOTAL S./</label>
                                                            <input id="inputCostoTotal" name="inputCostoTotal" type="text" class="form-control" disabled><i class="form-control-feedback" data-bv-icon-for="inputCorreP"></i>
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeCostoTotal"></div>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                                <div class="row" style="margin-top: 20px;">
                                                    <div class="col-sm-12 col-md-12" style="padding-bottom: 15px;">
                                                            <b>FILES</b>
                                                    </div>
                                                    <div class="col-sm-4 col-md-4">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>PERFIL</label>
                                                            <input id="perfil" name="perfil" type="file" required accept=".pdf" class="form-control input-mask">
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajePerfil"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 col-md-4">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>SISEGO COTIZADO</label>
                                                            <input id="sisegoCotizado" name="sisegoCotizado" type="file" required accept=".pdf" class="form-control input-mask">
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeCotizacion"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 col-md-4">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>RUTAS (KMZ)</label>
                                                            <input id="rutas" name="rutas" type="file" required accept=".kmz" class="form-control input-mask">
                                                            <i class="form-group__bar"></i>
                                                            <div id="mensajeRutas"></div>
                                                        </div>
                                                    </div>     
                                                    <div class="col-sm-6 col-md-12">
                                                        <div class="form-group has-feedback" style="">
                                                            <label>COMENTARIO</label>
                                                            <textarea id="textareaComentario" name="textareaComentario" class="form-control"></textarea>
                                                            <i class="form-group__bar"></i>
                                                        </div>
                                                    </div>                                             
                                                    <div class="col-sm-12 col-md-12" style="text-align: center;">
                                                        <div id="mensajeForm"></div>
                                                    </div>  <br>
                                                    <div class="col-sm-12 col-md-12" style="text-align: center;">
                                                        <div class="form-group" style="text-align: center;">
                                                            <div class="col-sm-12">                                      
                                                                <button data-cod="<?php echo $codigo?>" id="btnSave"  class="btn btn-primary" style="color: white;">ENVIAR COTIZACION</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
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


        <div id="mdlCotizacionPin" class="modal fade" role="dialog" aria-hidden="true" style="padding-right: 150px;">
            <div class="modal-dialog modal-lg ">
                <div class="modal-content " style="width: 80em;">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100 form-group">
                        <h4 class="m-0 text-center color-white">
                            INGRESAR PARTIDAS
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body container">
                        <div class="settings-panel">
                            <div id="contTablaKitPartida" class="form-group">
                            </div>
                            <div>
                                <table class="table table-borderedtable-sm">
                                    <thead class="bg-primary-600 ">
                                    <th style="font-weight: bolder; color: white; text-align: center">Partida</th>
                                    <th style="font-weight: bolder; color: white; text-align: center">Precio</th>
                                    <th style="font-weight: bolder; color: white; text-align: center">Baremo</th>
                                    <th style="font-weight: bolder; color: white; text-align: center; max-width: 100px">Cantidad</th>
                                    <th style="font-weight: bolder; color: white; text-align: center">Costo MO</th>
                                    <th style="font-weight: bolder; color: white; text-align: center">Precio kit</th>
                                    <th style="font-weight: bolder; color: white; text-align: center">Costo MAT</th>
                                    <th style="font-weight: bolder; color: white; text-align: center">Total</th>
                                    <th></th>
                                    </thead>
                                    <tbody id="tBodyActividades" >
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-md-9">
                                        <button class="btn btn-success" onclick="guardarKitPartidaPin()">Guardar</button>
                                    </div>
                                    <div class="col-md-3">
                                    <strong>TOTAL:S/.</strong> <label id="montoTotalGeneral"></label>
                                    </div>

                                </div>
                            </div>
                        </div> <span id="saving"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- END Page Settings -->     
        <script src="<?php echo base_url(); ?>public/js/vendors.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/app.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/select2/select2.bundle.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js?v=<?php echo time();?>"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/datagrid/datatables/datatables.export.js"></script>
        <script src="<?php echo base_url(); ?>public/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/bootstrap-validator/bootstrapValidator.min.js"></script>
        <script src="<?php echo base_url(); ?>public/js/Utils.js?v=<?php echo time();?>"></script>
        <script>

        var flgPrincipalGlobal = <?php echo $flg_principal;?>;
        var flg_catvGlobal = <?php echo $flg_catv;?>;
        var costoPqtGlobal     = <?php echo $costo_pqt_mo; ?>;
        var flg_distancia_lineal_global = <?php echo $flg_distancia_lineal; ?>;

        
            $(document).ready(function()
            {

                $('.select2').select2();                    
                if(flg_distancia_lineal_global == 1) {
                    $('#contSelecOtroOperador').css('display', 'block');
                } else {
                    $('#contSelecOtroOperador').css('display', 'none');
                }

            });

            function getCentral(component){
                var tipoRed = $(component).val();
                $('#selecElegirEbc').val(null).trigger('change');
                var padre = $('#mensajeOptionEbc').parent();
                var hijo = padre.children().get(5);
                $(hijo).css('display','none');

                if(tipoRed != null && tipoRed != '' && tipoRed != undefined){
                    $.ajax({
                        type : 'POST',
                        url  : 'getCentralByTipoRed',
                        data :  {
                                    tipoRed : tipoRed
                                },
                        beforeSend: () => {
                            $(component).attr("disabled", true);
                            $('#selectCentral').attr("disabled",true);
                            $('#selectCentra2').attr("disabled",true);
                        }
                    }).done(function(data){
                        data = JSON.parse(data);
                        $('#selectCentral').html(data.cmbCentral);
                        $('#selectCentral2').html(data.cmbCentral);
                    }).always(() => {
                        $(component).removeAttr("disabled");
                        $('#selectCentral').removeAttr("disabled");
                    //    $('#selectCentral2').removeAttr("disabled");
                    });

                }else{
                    $('#selectCentral').html('<option>&nbsp;</option>');
                    $('#selectCentral2').html('<option>&nbsp;</option>');
                }
            }

            
            function getEbcByDistritoByDistrito() {
                var option = $('#selecElegirEbc option:selected').val();
                if(flgPrincipalGlobal == 0) {
                    var idCentral = $('#selectCentral  option:selected').val();
                } else if(flgPrincipalGlobal == 1) {
                    var idCentral = $('#selectCentral2  option:selected').val()
                }

                if(idCentral == null || idCentral == '') {
                    return;
                }

                if(option == 1) {
                    
                    $.ajax({
                        type : 'POST',
                        url  : 'getEbcByDistritoByDistrito',
                        data :  {
                                    idCentral : idCentral
                                }
                    }).done(function(data){
                        data = JSON.parse(data);
                        $('#cmbEbc').html(data.cmbEbc);
                        console.log("ENTRO123");
                        $('#contEbcs').css('display', 'block');
                        $('#contFacRed').css('display', 'none');
                    });
                } else {
                    /* HFC DESCOMENTAR CUANDO SE PIDA SUBIR */
                    if(flg_catvGlobal == 1) {
                        $('#contFacRed').css('display', 'none');
                        $('#contEbcs').css('display', 'none');
                    } else {
                        $('#contFacRed').css('display', 'block');
                        $('#contEbcs').css('display', 'none');
                    }
                    
                    
                }
            }

            function getFacilidadesRedByNodo() {
                var idCentral = $('#selectCentral option:selected').val();
                console.log('central:'+idCentral);
           
                $.ajax({
                    type : 'POST',
                    url  : 'getFacByCent',
                    data :  {
                                idCentral : idCentral
                            }
                }).done(function(data){
                    data = JSON.parse(data);
                    $('#cmbFacilidades').html(data.cmbFacilidades);
                 });
                 
            }

            function getDataSeiaMtc(flgNodo=null) {console.log("123112");
                var metTenAereo = $('#inputMetroTenAereo').val();
                var metTenSubt  = $('#inputMetroTenSubt').val();              
                if(flgPrincipalGlobal == 0) {
                    var idCentral = $('#selectCentral  option:selected').val();
                    if(flgNodo == 3) {//czavala camado
                       getFacilidadesRedByNodo();

                    }
                } else if(flgPrincipalGlobal == 1) {
                    var idCentral = $('#selectCentral2  option:selected').val();
                    if(flgNodo == 1) {
                       // getEbcByDistritoByDistrito();
                    }
                }
                
                var totalMetros = Number(metTenAereo)+Number(metTenSubt);
                logicaMayorCincoMil(totalMetros);
                if(totalMetros == null || idCentral == null || idCentral == '') {
                    return;
                }    

                $.ajax({
                    type : 'POST',
                    url  : 'getDataSeiaMtc',
                    data : { totalMetros : totalMetros,
                            idCentral   : idCentral } 
                }).done(function(data){
                    data = JSON.parse(data);
                    // $('#selectRequeSeia').val(data.seia).trigger("change");
                    // $('#selectRequeAproMmlMtc').val(data.mtc).trigger("change");
                    console.log("seia: "+data.seia);
                    console.log("mtc: "+data.mtc);
                    $('#selectRequeSeia option[value="'+data.seia+'"]').prop("selected", "selected").trigger("change");
                    $('#selectRequeAproMmlMtc').val(data.mtc);
                    //$('#selectRequeAproMmlMtc option[value="'+data.mtc+'"]').prop("selected", "selected").trigger("change");
                    
                });
            }

            function logicaMayorCincoMil(totalMetros) {
		// if(flg_catvGlobal == 0) {
			if(totalMetros > 4000) {// SI ES MAYOR A 4000
				$('#inputCostMo').prop('disabled', false);
				$('#inputCostMo').val(0);
			} else if(costoPqtGlobal != null && costoPqtGlobal != 0 && codigo_coti_global != 'CL-324702') {
				$('#inputCostMo').prop('disabled', true);
				$('#inputCostMo').val(costoPqtGlobal);
			}
		// } else {
			// $('#inputCostMo').val(null);
		// }			
		
	    }

        function getDiasMatriz() {
            var seia = $('#selectRequeSeia option:selected').val();
            var mtc  = $('#selectRequeAproMmlMtc').val();
            var inc  = $('#selectRequeAprobINC option:selected').val();
            var metTenAereo = $('#inputMetroTenAereo').val();
            var metTenSubt  = $('#inputMetroTenSubt').val();
            var metOc       = $('#inputMetroCana').val();
            if(flgPrincipalGlobal == 0) {
                var idCentral = $('#selectCentral  option:selected').val();
            } else if(flgPrincipalGlobal == 1) {
                var idCentral = $('#selectCentral2  option:selected').val()
            }

            if(metTenSubt == null || idCentral == '' || idCentral == null || metTenSubt == '' || seia == null || seia == '' || mtc == '' || mtc == null || 
            inc == null || inc == '' || metTenAereo == null || metTenAereo == '') {console.log("eNTRO1");
                $('#inputDias').val("");
                return; 
            }
            console.log(inc);
            console.log(metOc);
            if(inc == 'SI' && Number(metOc) > 0) {
                $('#inputCostoAdicZona').val(25000);
            } else {
                $('#inputCostoAdicZona').val(0);
            }
            
            var totalMetros = Number(metTenAereo)+Number(metTenSubt);
            $.ajax({
                type : 'POST',
                url  : 'getDiasMatriz',
                data : { seia : seia,
                        mtc  : mtc,
                        inc  : inc,
                        totalMetros : totalMetros,
                        idCentral   : idCentral } 
            }).done(function(data){
                data = JSON.parse(data);
                getcalculos();
                idTipoDiseno = $('#cmbTipoDiseno option:selected').val();
                if(idTipoDiseno == 2 && totalMetros == 0) {
                    $('#inputDias').val(15);
                } else {
                    $('#inputDias').val(data.dia);
                }
            });
        }

        function setQuinceDias() {
            idTipoDiseno = $('#cmbTipoDiseno option:selected').val();
            
            if(idTipoDiseno == 4 || idTipoDiseno == 8) {
                $('#inputDias').val(15);
            } else {
                getDiasMatriz();
            }
            
            // if(idTipoDiseno == 8 || idTipoDiseno == 4 || idTipoDiseno == 9) {
                // $('#inputCostMo').prop('disabled', false);
                // $('#inputCostMo').val(0);
            // } else {
                 var metTenAereo = $('#inputMetroTenAereo').val();
                 var metTenSubt  = $('#inputMetroTenSubt').val();
                var totalMetros = Number(metTenAereo)+Number(metTenSubt);
                // console.log("totalMetros: "+totalMetros);
                logicaMayorCincoMil(totalMetros);
            // }
            
            getcalculos();
        }

        function getcalculos(){
            var costoMat   = $('#inputCostoMat').val();
            var costoMo    = $('#inputCostMo').val();
            // var costoDise  = $('#inputCostoDiseno').val();
            var costoExpe  = $('#cmbMontoEIA option:selected').val();
            var costoAdic  = $('#inputCostoAdicZona').val();
            var costoOc    = $('#inputCostoOc').val();

            var inputCostoTotal = Number(costoMat)+Number(costoMo)+Number(costoExpe)+Number(costoAdic)+Number(costoOc);        	
            $('#inputCostoTotal').val(inputCostoTotal.toFixed(2));            
        }

        $('#formAddPlanobra')
            .bootstrapValidator({
                //container: '#mensajeForm',
                feedbackIcons: {
                    valid      : 'glyphicon glyphicon-ok',
                    invalid    : 'glyphicon glyphicon-remove',
                    validating : 'glyphicon glyphicon-refresh'
                },
                excluded: ':disabled',
                fields: {
                    inputCostMo :       {
                                            validators: {
                                                            container : '#mensajeCostoMo',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) Debe Tener costo Mo.</p>'
                                                                        }
                                                        }
                                        },
                    inputCostoMat :     {
                                            validators: {
                                                            container : '#mensajeCostoMat',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) Debe Tener costo Mat.</p>'
                                                                        }
                                                        }
                                        },

                    inputCostoTotal :   {
                                            validators: {
                                                            container : '#mensajeCostoTotal',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) Debe Tener costo total.</p>'
                                                                        }
                                                        }
                                        },
                    perfil :            {
                                            validators: {
                                                            container : '#mensajePerfil',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) Debe subir el archivo.</p>'
                                                                        }
                                                        }
                                        },
                    sisegoCotizado :    {
                                            validators: {
                                                            container : '#mensajeCotizacion',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) Debe subir el archivo.</p>'
                                                                        }
                                                        }
                                        },
                    rutas:              {
                                            validators: {
                                                            container : '#mensajeRutas',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) Debe subir el archivo.</p>'
                                                                        }
                                                        }
                                        },
                    cmbTipoDiseno   :   {
                                            validators: {
                                                            container : '#mensajeTipoDiseno',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) Debe Seleccionar.</p>'
                                                                        }
                                                        }
                                        },

                    cmbTipoEnlace   :   {
                                            validators: {
                                                            container : '#mensajeTipoEnlace',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) Debe Seleccionar.</p>'
                                                                        }
                                                        }
                                        },                   
                    inputDias       :   {
                                            validators: {
                                                            container : '#mensajeInputDias',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) campo obligatorio.</p>'
                                                                        }
                                                        }
                                        }, 
                    inputMetroTenAereo  :   {
                                            validators: {
                                                            container : '#mensajeInputDias',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) campo obligatorio.</p>'
                                                                        }
                                                        }
                                        },
                    inputMetroTenSubt   :   {
                                            validators: {
                                                            container : '#mensajeInputDias',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) campo obligatorio.</p>'
                                                                        }
                                                        }
                                        },
                    selectCentral   :   {
                                            validators: {
                                                            container : '#mensajeNodoPrincipal',
                                                            notEmpty:  {
                                                                            message: '<p style="color:red">(*) campo obligatorio.</p>'
                                                                        }
                                                        }
                                        },
                    selectRequeAprobINC :   {
                                                validators: {
                                                                container : '#mensajeInputDias',
                                                                notEmpty:  {
                                                                                message: '<p style="color:red">(*) campo obligatorio.</p>'
                                                                            }
                                                            }
                                            },
                    selectRequeAproMmlMtc :  {
                                                validators: {
                                                                container : '#mensajeMtc',
                                                                notEmpty:  {
                                                                                message: '<p style="color:red">(*) campo obligatorio.</p>'
                                                                            }
                                                            }
                                            }						
                }
            }).on('success.form.bv', function(e) {
                e.preventDefault();       		

            swal.fire({
                    title: 'Est&aacute; seguro de enviar la Cotizacion?',
                    text: 'Asegurese de que la informacion llenada sea la correta.',
                    type: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Si, guardar los datos!',
                    cancelButtonClass: 'btn btn-secondary',
                    allowOutsideClick: false
                }).then(function(){
                    var $form = $(e.target),
                    formData  = new FormData(),
                    params    = $form.serializeArray(),
                    bv        = $form.data('bootstrapValidator');	 
                    var codigo = $('#btnSave').attr('data-cod');
                    formData.append('codigo', codigo);
                    $.each(params, function(i, val) {
                        formData.append(val.name, val.value);
                    });
                    
                    var input = document.getElementById('perfil');
                    var filePerfil = input.files[0];
                    
                    formData.append('filePerfil', filePerfil);
                    formData.append('nodoPrincipal', $('#selectCentral  option:selected').text());
                    formData.append('nodoRespaldo' , $('#selectCentral2 option:selected').text());
                    formData.append('flgPrincipal' , flgPrincipalGlobal)
                    formData.append('costoEIA'     , $('#cmbMontoEIA option:selected').val());
                    formData.append('costoAdicZon' , $('#inputCostoAdicZona').val());

                    formData.append('facilidad_n', $('#cmbFacilidades option:selected').val());
                    formData.append('reqSia'     , $('#selectRequeSeia option:selected').val());
                    formData.append('reqMtc'     , $('#selectRequeAproMmlMtc').val());
                    formData.append('reqInc'     , $('#selectRequeAprobINC option:selected').val());
                    formData.append('codEbc'     , $('#cmbEbc option:selected').val());
                    formData.append('flg_ebc'    , $('#selecElegirEbc option:selected').val());
                    formData.append('costoOc'    , $('#inputCostoOc').val());
                    
                    formData.append('costoMoPqt' , $('#inputCostMo').val());
                    formData.append('flg_catv'   , flg_catvGlobal);
                    
                    var flg_nodo_otro_op = null;
                    var nodo_otro_op     = null;
                    
                    console.log("fLG: "+flg_distancia_lineal_global);
                    if(flg_distancia_lineal_global == 1) {
                        flg_nodo_otro_op = $('#selecElegirNodoOtroOperador').val();
                        
                        if(flg_nodo_otro_op == 1) {
                            nodo_otro_op = $('#cmbNodoOtroOperador option:selected').val();
                            
                            formData.append('nodo_otro_op'     , nodo_otro_op);
                        }
                        
                        formData.append('flg_nodo_otro_op' , flg_nodo_otro_op);
                    }

                    //formData.append('duracion'     , $('#cmbDuracion option:selected').val());
                    formData.append('duracion', $('#inputDias').val());
                    var input2 = document.getElementById('sisegoCotizado');
                    var fileSisegoCot = input2.files[0];
                    
                    formData.append('fileSisego', fileSisegoCot);

                    var input3 = document.getElementById('rutas');
                    var fileRutas = input3.files[0];

                    formData.append('fileRutas', fileRutas);
                    
                    var mtc = $('#selectRequeAproMmlMtc').val();
                    if(mtc == null || mtc == '') {
                        return;
                    }
                    var dias = $('#inputDias').val();
                    
                    if(dias == null || dias == '') {
                        $('#mensajeInputDias').html('<p style="color:red">(*) campo obligatorio.</p>');
                        return;
                    }
                    var troba = null;
                    var cantTroba = null;
                    /* HFC DESCOMENTAR CUANDO SE PIDA SUBIR */
                    if(flg_catvGlobal == 1) {
                        troba = $('#inputTroba').val();
                        cantTroba = $('#inputCantTroba').val();
                        
                        if(troba == null || troba == '') {
                            $('#mensajeInputTroba').html('<p style="color:red">(*) campo obligatorio.</p>');
                            return;
                        }
                        
                        if(cantTroba == null || cantTroba == '') {
                            $('#mensajeInputCantTroba').html('<p style="color:red">(*) campo obligatorio.</p>');
                            return;
                        }
                    }
                    formData.append('troba'    , troba);
                    formData.append('cantTroba', cantTroba);
                    console.log('ok!!!');
                    
                    $.ajax({
                        data: formData,
                        url: "sendCotizacionIndividual",
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST'
                    })
                    .done(function(data) {  
                            data = JSON.parse(data);
                            console.log(data.error);
                            if(data.error == 0){
                                var codigo = data.codigo;                     
                                swal.fire({
                                        title: 'Se envio corecctamente la Cotizacion',
                                        text: codigo,
                                        type: 'success',
                                        showCancelButton: false,                    	            
                                        allowOutsideClick: false
                                    }).then(function(){
                                        window.location.href = "cotib2b";
                                    });
                            }else if(data.error == 1){
                                mostrarNotificacion(1,'error','Verificar', data.msj);
                            }
                        });
                
                }, function(dismiss) {
                    console.log('cancelado');
                    // dismiss can be "cancel" | "close" | "outside"
                    $('#formAddPlanobra').bootstrapValidator('revalidateField', 'selectCotizacion');
                    //$('#formAddPlanobra').bootstrapValidator('resetForm', true); 
                });
                    
            });

        </script>
    </body>
    <!-- END Body -->
</html>
