<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_detalle_consulta extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_consulta/m_detalle_consulta');
        $this->load->model('mf_diseno/m_diseno');
        $this->load->model('mf_consulta/m_bandeja_aprobacion_po_mat');
        $this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
        $this->load->model('mf_servicios/m_integracion_sirope');
        $this->load->model('mf_orden_compra/m_bandeja_solicitud_oc');
        $this->load->model('mf_crecimiento_vertical/m_agenda_cv');
        $this->load->model('mf_consulta/m_bandeja_valida_obra');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        $this->load->library('zip');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        //$idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
        //$itemplan = $this->input->get('itemplan');
	    if($idUsuario != null){
            $permisos = $this->session->userdata('permisosArbolPan');    
          //  $data['cmbMotivoCancelaIP'] = __cmbHTML2(__buildComboMotivoCancelaIP(), 'selectMotivoCance', null, 'select2 form-control w-100', 'Motivo Cancelación', null, null);
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_OBRA_PADRE, null, ID_CONSULTA_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['arrayTipoDiseno'] = $this->m_utils->getTipoDiseno(NULL);
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_consulta/v_detalle_consulta',$data);        	  
    	 }else{
            //redirect(RUTA_OBRA2, 'refresh');
        	redirect('login','refresh');
	    }     
    }

    function getInfoITemplanByCod(){
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            if($idUsuario == null){
                throw new Exception('Su session ha terminado, vuelva a logear!');
            }

            $itemplan           = $this->input->post('cod_obra'); 
            $dataObra = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($dataObra    ==  null){
                throw new Exception('El codigo de obra no existe!');
            }

            $iddEECC = $this->session->userdata('idEmpresaColabSesion');
            if($iddEECC    <> 6){
                if($dataObra['idEmpresaColab'] <> $iddEECC){
                    throw new Exception('El codigo de obra no pertenece a su contrata!');
                }                
            }

            if($dataObra['idTipoPlanta'] ==	1){//SOLO PLANTA EXTERNA
                $beTerminado = $this->m_utils->evaluarPaseTerminadoItemplan($itemplan);
                $fechaActual = $this->m_utils->fechaActual();		 
                if($beTerminado && $dataObra['idEstadoPlan'] == ID_ESTADO_PLAN_PRE_LIQUIDADO){
                    $arrayUpdatePO = array(
                        "idEstadoPlan" => ID_ESTADO_PLAN_TERMINADO,
                        "idUsuarioLog" => 112,
                        "fechaLog" => $fechaActual, 
                        "descripcion" => 'REQUERIMIENTOS COMPLETOS'
                    );
                    $this->m_utils->actualizarPlanObra($itemplan, $arrayUpdatePO);	
                    $dataObra = $this->m_utils->getPlanObraByItemplan($itemplan);		

					if($dataObra['idProyecto'] ==	21){
						$rsp = $this->m_utils->fnCreatePartidasIntegralesByItemplan($itemplan);
						if($rsp != 1) {
							$data['error'] = EXIT_ERROR;
							throw new Exception('Hubo un error al ejecutar la función de partidas integrales');
						}
					}					
                }
            }
            $data['dataObra']       = $dataObra; 
            $data['tablaLog']       = $this->getHTMLTablaLog($itemplan);
			$data['tablaLogExpe']   = $this->getHTMLTablaLogExpe($itemplan);
            $data['htmlTabGestion'] = $this->getTabEstadosItemplan($itemplan, $dataObra); 
            $data['htmlPos']        = $this->getTabEstacionPo($itemplan);     
            $data['btnCancelar']    = $this->gethtmlBtnCancelarIp($dataObra); 
           
            if($dataObra['idProyecto']  ==  3){
                $data['cmbMotiCance']   = __buildComboMotivCancelar(7);
                $dataCoti               = $this->m_utils->getInfoClusterB2bByItemplan($itemplan);                
                if($dataCoti    !=  null){
                    $data['infoCotiTipoDise']   =   $dataCoti['id_tipo_diseno'];  
                }else{
                    $data['infoCotiTipoDise'] = '';
                }                  
            }else{
                $data['cmbMotiCance']   = __buildComboMotivCancelar(null);
            }
            if($dataObra['idProyecto']  ==  21){//cableado de edificios
                $data['tbLogSegui'] = $this->getHTMLTablaLogSeguimientoCV($itemplan);
            }else if($dataObra['idProyecto']  ==  3){//B2B
                $data['tbLogSegui'] = $this->getHTMLTablaLogSeguimientoB2b($itemplan);
            }else if($dataObra['idSubProyecto']  ==  734	||	$dataObra['idSubProyecto']  ==  748){//REFORZAMIENTOS
				#log_message('error','is_reforzamiento....');
                $data['tbLogSegui'] = $this->getHTMLTablaLogReforzamientoCTO($itemplan);
				#log_message('error',$data['tbLogSegui']);
            }

            $logIpToEsta = $this->m_utils->getLogPlanobraToRevertSus($itemplan, $dataObra['idEstadoPlan']);
            $idEstadoAnte = 0;
            if($logIpToEsta!=null){
                if($logIpToEsta['idEstadoPlanAnt']!=null){
                        $idEstadoAnte   =   $logIpToEsta['idEstadoPlanAnt'];
                }
            }
            $data['combEstadosTrunCansus']   = __buildComboEstadosToCanTrunSus($dataObra['idEstadoPlan'], $idEstadoAnte, $dataObra['idTipoPlanta'], $dataObra['idSubProyecto']);
            $data['error']    = EXIT_SUCCESS;

        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function gethtmlBtnCancelarIp($dataObra){
        $html = null;
        if(in_array($dataObra['idEstadoPlan'],array(24,7, ID_ESTADO_PLAN_PRE_REGISTRO,ID_ESTADO_PLAN_DISENIO, ID_ESTADO_PLAN_EN_LICENCIA, ID_ESTADO_PLAN_EN_APROBACION, ID_ESTADO_PLAN_EN_OBRA,18))){
            $html = '<a style="color:white" class="btn btn-sm btn-danger waves-effect waves-themed" aria-expanded="true" title="Cancelar Itemplan" data-itemplan="'.$dataObra['itemplan'].'"
                                data-id_empresacolab="'.$dataObra['idEmpresaColab'].'" onclick="openModalCancelarIP(this)">
                                <i class="fal fa-ban"></i> CANCELAR / TRUNCAR / SUSPENDER
                            </a>';

        }        
        return $html;
    }
    function getTabEstadosItemplan($itemplan, $dataObra =null) {
        $infoITem = $this->m_utils->getPlanObraByItemplan($itemplan);
        $navActivePreReg = '';
        $navActiveDiseno = '';
        $navActiveLic = '';
        $navActiveAprob = '';
        $navActiveObraPreliq = '';
        $navActiveTerminado = '';
        $navActiveEnCertificacion = '';
        $navActiveEnCertificado = '';
        $tabActivePreReg = '';
        $tabActiveDiseno = '';
        $tabActiveLic = '';
        $tabActiveAprob = '';
        $tabActiveObraPreliqui = '';
        $tabActiveTerminado = '';
        $tabActiveEnCertificacion = '';
        $tabActiveEnCertificado   = '';
        $htmlContTerminado = '';
        $htmlContEnCertificacion = '';
        $contVerificacion = array(
           0 => '',
           1 => ''
        );

        if($infoITem['idEstadoPlan'] == ID_ESTADO_PLAN_PRE_REGISTRO){
            $navActivePreReg = 'active';
            $tabActivePreReg = 'show active';
        }else if($infoITem['idEstadoPlan'] == ID_ESTADO_PLAN_DISENIO){
            $navActiveDiseno = 'active';
            $tabActiveDiseno = 'show active';
        }else if($infoITem['idEstadoPlan'] == ID_ESTADO_PLAN_EN_LICENCIA){
            $navActiveLic = 'active';
            $tabActiveLic = 'show active';
        }else if($infoITem['idEstadoPlan'] == ID_ESTADO_PLAN_EN_APROBACION){
            $navActiveAprob = 'active';
            $tabActiveAprob = 'show active';
        }else if(in_array($infoITem['idEstadoPlan'],array(ID_ESTADO_PLAN_EN_OBRA,ID_ESTADO_PLAN_PRE_LIQUIDADO))){
            $navActiveObraPreliq = 'active';
            $tabActiveObraPreliqui = 'show active';
        }else if(in_array($infoITem['idEstadoPlan'],array(ID_ESTADO_PLAN_TERMINADO))){
            $navActiveTerminado = 'active';
            $tabActiveTerminado = 'show active';
            $htmlContTerminado = $this->getContenidoTerminado($itemplan);
			//log_message('error','TERMINADO:'.$htmlContTerminado );
        }else if(in_array($infoITem['idEstadoPlan'],array(ID_ESTADO_PLAN_EN_CERTIFICACION, ID_ESTADO_PLAN_CERTIFICADO))){
            $htmlContEnCertificacion = $this->getContenidoEnCertificacion($itemplan);
            $navActiveEnCertificacion = 'active';
            $tabActiveEnCertificacion = 'show active';
 
            $infoExpediente = $this->m_bandeja_valida_obra->getInfoExpedienteLiquidacionNoPqtByItem($itemplan);  
		 	if($infoExpediente	!=	null){			
            $htmlContTerminado = '<div class="row">
                                    <div class="col-md-4 mb-4">
                                    </div>
                                    <div class="col-md-4 mb-4" style="display:display">
                                        <label class="form-label" style="color: white;">-</label>
                                        <a class="btn btn-outline-primary ml-auto waves-effect waves-themed form-control" download type="button" href="'.$infoExpediente['path_expediente'].'">
                                            <span class="fal fa-download mr-1"></span> EXPEDIENTE 
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                    </div>
                                </div>'; 
			} 
        }

        if(in_array($infoITem['idEstadoPlan'],array(ID_ESTADO_PLAN_PRE_LIQUIDADO,ID_ESTADO_PLAN_TERMINADO,ID_ESTADO_PLAN_EN_CERTIFICACION,ID_ESTADO_PLAN_CERTIFICADO))){
            $contVerificacion  = $this->getContenidoChecks($itemplan);
        }
        
        
        $tabDiseno = $this->getTabDiseno($itemplan);
        $tabEstacion = $this->getTabVerticalEstacion($itemplan,$infoITem);
        $tabAprobacion = $this->getTabAprobacion($itemplan);
        $tabObraPreliqui = $this->getTabObraPreliqui($itemplan);


        $classpending   = '';
        $classpending2  = '';
        $bloquearTab    = '';
        $message        = '';

        if($infoITem['idEstadoPlan']    ==  1){//DISENO EJECUTADO
            $bloquearTab = ' disabled';
            $classpending = ' classpending';
            $message = 'El Itemplan actual, está en estado Prediseño';
        }

        if($dataObra['orden_compra']  ==  ""){//NO TIENE ORDEN DE COMPRA OP
            $bloquearTab = ' disabled';
            $classpending2 = ' classpending';
            $message = 'Se requiere de una OC de Operaciones para realizar esta acción';  //licencia, en aprob, en obra, terminado
        }

        $html = '<div class="col-auto">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link '.$navActivePreReg.'" id="v-pills-home-tab" data-toggle="pill" href="#cont_tab_pre_reg" role="tab" aria-controls="v-pills-home" aria-selected="true">
                            <i class="fal fa-chevron-right"> PRE REGISTRO</i>                        
                        </a>
                        <a class="nav-link '.$navActiveDiseno.' '.$classpending.'" data-msg="'.$message.'" id="v-pills-profile-tab" data-toggle="pill" href="#cont_tab_diseno'.$bloquearTab.'" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                            <i class="fal fa-chevron-right"> DISEÑO</i>                        
                        </a>
                        <a class="nav-link '.$navActiveLic.' '.$classpending.' '.$classpending2.'" data-msg="'.$message.'" id="v-pills-messages-tab" data-toggle="pill" href="#cont_tab_lic'.$bloquearTab.'" role="tab" aria-controls="v-pills-messages" aria-selected="false">
                            <i class="fal fa-chevron-right"> LICENCIA</i>                            
                        </a>
                        <a class="nav-link '.$navActiveAprob.' '.$classpending.' '.$classpending2.'" data-msg="'.$message.'" id="v-pills-settings-tab" data-toggle="pill" href="#cont_tab_aprob '.$bloquearTab.'" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                            <i class="fal fa-chevron-right"> APROBACION</i>                        
                        </a>                       
                        <a class="nav-link '.$navActiveObraPreliq.' '.$classpending.' '.$classpending2.'" data-msg="'.$message.'" id="v-pills-settings-tab" data-toggle="pill" href="#cont_tab_pre_liqui'.$bloquearTab.'" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                            <i class="fal fa-chevron-right"> PRE LIQUIDADO</i>                        
                        </a>
                        <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#cont_tab_verificacion" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                            <i class="fal fa-chevron-right"> EN VERIFICACION</i>                        
                        </a>
                        <a class="nav-link '.$navActiveTerminado.' '.$classpending2.'" data-msg="'.$message.'" id="v-pills-settings-tab" data-toggle="pill" href="#cont_tab_terminado " role="tab" aria-controls="v-pills-settings" aria-selected="false">
                            <i class="fal fa-chevron-right"> TERMINADO</i>                        
                        </a>
                        <a class="nav-link '.$navActiveEnCertificacion.'" id="v-pills-settings-tab" data-toggle="pill" href="#cont_tab_en_certificacion" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                            <i class="fal fa-chevron-right"> EN CERTIFICACION</i>                        
                        </a>
                    </div>
                </div>            
                <div class="col">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade '.$tabActivePreReg.'" id="cont_tab_pre_reg" role="tabpanel" aria-labelledby="v-pills-home-tab">
                            <h3>
                                --
                            </h3>


                        </div>
                        <div class="tab-pane fade '.$tabActiveDiseno.'" id="cont_tab_diseno" role="tabpanel" aria-labelledby="v-pills-home-tab">
                            
                            <div class="panel-container show">
                                <div class="panel-content">
                                    '.$tabDiseno.'
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade '.$tabActiveLic.'" id="cont_tab_lic" role="tabpanel" aria-labelledby="v-pills-home-tab">
                            
                            <div class="panel-container show">
                                <div class="panel-content">                                   
                                        '.$tabEstacion.'                                  
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade '.$tabActiveAprob.'" id="cont_tab_aprob" role="tabpanel" aria-labelledby="v-pills-home-tab">
                            <div class="panel-container show">
                                <div class="panel-content">
                                    '.$tabAprobacion.' 
                                </div>
                            </div>
                        </div>                       
                        <div class="tab-pane fade '.$tabActiveObraPreliqui.'" id="cont_tab_pre_liqui" role="tabpanel" aria-labelledby="v-pills-home-tab">                          
                            <div class="panel-container show">
                                <div class="panel-content table-responsive">
                                    '.$tabObraPreliqui.'
                                </div>
                            </div>
                        </div>  
                        <div class="tab-pane fade" id="cont_tab_verificacion" role="tabpanel" aria-labelledby="v-pills-home-tab">
                           
                            <div class="panel-container show">
                                <div class="panel-content">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div id="panel-1" class="panel">
                                                <!--
                                                <div class="panel-hdr">
                                                    <h2>
                                                        Validando
                                                    </h2>
                                                    <div class="panel-toolbar">
                                                        <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                                        <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                                     </div>
                                                </div>
                                                -->
                                                <div class="panel-container show">
                                                    <div class="panel-content">
            
                                                        <ul class="nav nav-tabs nav-fill" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-toggle="tab" href="#tab_justified-1" role="tab">
                                                                    <i class="fal fa-layer-group mr-1"></i>
                                                                    RESUMEN
                                                                </a>
                                                            </li>
                                                            <!--  
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#tab_justified-2" role="tab">
                                                                    <i class="fal fa-house-leave mr-1"></i>
                                                                    SIROPE
                                                                </a>
                                                            </li>
                                                            -->
                                                        </ul>
                                                        <div class="tab-content p-3">
                                                            <div class="tab-pane fade show active" id="tab_justified-1" role="tabpanel">
                                                                <h5 class="frame-heading">CONDICIONES PARA VALIDACIÓN</h5>
                                                                <div class="frame-wrap">
                                                                    '.$contVerificacion[0].'
                                                                </div>
                                                            </div>
                                                            <!--  
                                                            <div class="tab-pane fade" id="tab_justified-2" role="tabpanel">
                                                                '.$contVerificacion[1].'
                                                            </div> 
                                                            -->
                                                        </div>
                                                        
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </div>
                        <div class="tab-pane fade '.$tabActiveTerminado.'" id="cont_tab_terminado" role="tabpanel" aria-labelledby="v-pills-home-tab">
                           <div class="panel-container show">
                                <div class="panel-content">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div id="panel-2" class="panel">
                                                <div class="panel-hdr">
                                                    <h2>
                                                        Propuesta de partidas
                                                    </h2>                                                  
                                                </div>
                                                <div class="panel-container show">
                                                    <div class="panel-content">
                                                        '.$htmlContTerminado.'
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade '.$tabActiveEnCertificacion.'" id="cont_tab_en_certificacion" role="tabpanel" aria-labelledby="v-pills-home-tab">
                            <h3>
                                En Certificacion
                            </h3>
                            <div class="panel-container show">
                                <div class="panel-content">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div id="panel-3" class="panel">
                                                <div class="panel-hdr">
                                                    <h2>
                                                        Solicitudes de Orden de Compra
                                                    </h2>
                                                    <div class="panel-toolbar">
                                                        <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                                        <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                                        <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                                    </div>
                                                </div>
                                                <div class="panel-container show">
                                                    <div class="panel-content">
                                                        '.$htmlContEnCertificacion.'
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
 //log_message('error',$html);
        return $html;
    }

    function getTabVerticalEstacion($itemplan,$infoItem) {
        //$dataSubEstacion = $this->m_utils->getSubProyectoEstaciosByItemplan($itemplan);
        $dataSubEstacion = $this->m_utils->getEstacionesToLicenciaByItemplan($itemplan);
        $tab = null;
        $tabContent = null;
        $cont = 0;

        if ($infoItem['orden_compra'] == null)
        {
            $tabContent =   'Se requiere de OC de Operaciones';
        }elseif($infoItem['idEstadoPlan']    ==  7){//DISENO EJECUTADO
            $tabContent =   'El Estado Plan no permite gestion de Licencias';
        }else if($infoItem['idEstadoPlan']    ==  2){//DISENO
            $tabContent =   'El Estado Plan no permite gestion de Licencias';
        }else{
            foreach($dataSubEstacion as $row) {
                
            
                $btnAddEntidad = null;
                if(in_array($infoItem['idEstadoPlan'],array(ID_ESTADO_PLAN_EN_LICENCIA,ID_ESTADO_PLAN_EN_APROBACION,ID_ESTADO_PLAN_EN_OBRA))){
                    $btnAddEntidad = '<a style="cursor:pointer; color:white" aria-expanded="true" data-toggle="tooltip"  data-original-title="Agregar Entidad" data-itemplan="'.$itemplan.'"
                                            data-id_estacion="'.$row['idEstacion'].'" class="btn btn-sm btn-primary waves-effect waves-themed" onclick="openMdlAgregarEntidad($(this));">NUEVA ENTIDAD
                                    </a>';
                }
                $tabContent .= '<div class="row"> 
                                    <div class="col-lg-6 col-xl-6 col-6 text-left py-1">         
                                        <h2 style="font-weight=900"><u>'.$row['estacionDesc'].'</u></h2>         
                                    </div>                                                  
                                    <div class="col-lg-6 col-xl-6 col-6 text-left py-1">
                                        '.$btnAddEntidad.'                                   
                                    </div>
                                    <div class="col-lg-12 col-xl-12 col-12 text-left py-1 table-responsive">
                                        <div id="contTablaLicencia_'.$itemplan.'_'.$row['idEstacion'].'">
                                            '.$this->tablaEntidadItemplanEstacion($itemplan, $row['idEstacion']).'
                                        </div>
                                    </div>
                                </div>';
                
            } 
        }
        return $tabContent;
    }
   
    function getContLicencia($itemplan, $idEstacion) {
        $btnLiquidar = '<a class="btn btn-sm btn-outline-primary" 
                                        aria-expanded="true" data-toggle="tooltip" data-offset="0,10" data-original-title="Liquidar Obra" data-itemplan="'.$itemplan.'"
                                        onclick="openMdlLiquidarObraPin($(this));"><i class="fal fa-search"></i></a>';
        $html = $btnLiquidar;
    }

    function getTablaEntidad() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan   = $this->input->post('itemplan');
            $idEstacion = $this->input->post('idEstacion');

            if($itemplan == null || $itemplan == '' ||  $idEstacion == null ||  $idEstacion == '') {
                throw new Exception('comunicarse con el programador a cargo');
            }
            
            $data['error']   = EXIT_SUCCESS;
            $tablaEntidad = $this->tablaEntidad($itemplan, $idEstacion);

            $data['tablaEntidad'] = $tablaEntidad;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    
    function tablaEntidad($itemplan, $idEstacion) {
        $arrayPlanobra = $this->m_utils->getEntidadAll($itemplan, $idEstacion);
        $html = '<table id="tbEntidad" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>Nro</th>  
                            <th>Entidad</th>                            
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $cant = 0;
                    foreach ($arrayPlanobra as $row) {
                        $cant++;

                        $check = '<div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="check_'.$cant.'" data-cant="'.$cant.'" data-id_entidad="'.$row['idEntidad'].'" onchange="agregarEntidad($(this));">
                                    <label class="custom-control-label" for="check_'.$cant.'">Agregar</label>
                                </div>';
                        $html .= ' <tr>
                                        <td>'.$cant.'</td>
                                        <td>'.$row['entidadDesc'].'</td>
                                        <td>'.$check.'</td>           
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function registrarEntidad() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $arrayDataEntidad   = $this->input->post('arrayDataEntidad');
            $itemplan           = $this->input->post('itemplan');
            $idEstacion         = $this->input->post('idEstacion');


            if(count($arrayDataEntidad) == 0) {
                throw new Exception('Debe ingresar por lo menos una entidad.');
            }

            if($itemplan == null || $itemplan == '') {
                throw new Exception('verificar, error interno.');
            }

            if($idEstacion == null || $idEstacion == '') {
                throw new Exception('verificar, error interno.');
            }
            
            $data = $this->m_utils->registrarEntidad($arrayDataEntidad);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
            $data['tablaEntidadItemplanEstacion'] = $this->tablaEntidadItemplanEstacion($itemplan, $idEstacion);

            
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function tablaEntidadItemplanEstacion($itemplan, $idEstacion) {
        $arrayPlanobra = $this->m_utils->getEntidadByItemplanEstacion($itemplan, $idEstacion);
        $html = '<table id="tbEntidad" class="table table-bordered table-sm table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th></th>  
                          
                            <th>Entidad</th>
                            <th>Expediente</th>
                            <th>Tipo</th>
                            <th>Distrito</th>
                            <th>Fec. Inicio</th>
                            <th>Fec. Fin</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>';
                 
                    foreach ($arrayPlanobra as $row) {
                     
                        $btnGuardarExp = null;
                        $btnComprobante = null;
                        $btnDelete = null;
                        $btnArchivoComp = null;
                        if($row['estado'] == null) {
                            $btnGuardarExp = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Guardar" 
                                                aria-expanded="true" data-itemplan="'.$itemplan.'" data-id_estacion="'.$idEstacion.'" 
                                                data-id_entidad="'.$row['idEntidad'].'" data-id="'.$row['id'].'"
                                                onclick="openModalRegExp1($(this));"><i class="fal fa-save"></i>
                                            </a>';
                        }

                        if($row['flg_comprobante'] == 1 && $row['estado'] != null) {
                            $btnComprobante = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Abrir Comprobante" 
                                                    aria-expanded="true" data-itemplan="'.$itemplan.'" data-id_estacion="'.$idEstacion.'" 
                                                    data-id_entidad="'.$row['idEntidad'].'" data-id="'.$row['id'].'" data-estado="'.$row['estado'].'"
                                                    onclick="openModalComprobante($(this));"><i class="fal fa-edit"></i>
                                                </a>';
                        }
                        if(in_array($row['idEstadoPlan'],array(ID_ESTADO_PLAN_EN_LICENCIA,ID_ESTADO_PLAN_EN_APROBACION,ID_ESTADO_PLAN_EN_OBRA))) {
                            $btnDelete = '<a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Eliminar Entidad" 
                                            aria-expanded="true" data-itemplan="'.$itemplan.'" data-id_estacion="'.$idEstacion.'" 
                                            data-id_entidad="'.$row['idEntidad'].'" data-id="'.$row['id'].'"
                                            onclick="eliminarEntidad(this);"><i class="fal fa-trash"></i>
                                        </a>';
                        }

                        if($row['estado'] != null) {
                            $btnArchivoComp = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Descargar Evidencia" target="_blank"
                                                    href="' . $row['ubicacion_evidencia'] . '">
                                                    <i class="fal fa-download"></i>
                                                </a>';
                        }
						
						$btnArchivoTermino = null;
                        if($row['idEstadoPlan'] ==  23	||	$row['idEstadoPlan'] ==  22	||	$row['idEstadoPlan'] ==  4){
                            if($row['has_termino_lic'] ==  null){
                                $btnArchivoTermino = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Abrir Termino Licencia" 
                                                            aria-expanded="true" data-itemplan="'.$itemplan.'" data-id_estacion="'.$idEstacion.'" 
                                                            data-id_entidad="'.$row['idEntidad'].'" data-id="'.$row['id'].'" data-estado="'.$row['estado'].'"
                                                            onclick="uploadFileTerminoLicencia($(this));"><i class="fal fa-upload"></i>
                                                        </a>';
                            }else if($row['has_termino_lic'] ==  1){
                                $btnArchivoTermino = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Descargar Termino Licencia" target="_blank"
                                                            href="' . $row['ubicacion_termino'] . '">
                                                            <i class="fal fa-download"></i>
                                                        </a>';
                            }
                        }

                        $html .= ' <tr>
                                        <td>'.$btnComprobante.'</td>
                                       
                                        <td>'.$row['entidadDesc'].'</td>
                                        <td><input id="exp_'.$idEstacion.'_'.$row['id'].'" class="form-control" type="text" value="'.$row['nroExpediente'].'" placeholder="Reg. Expediente" /></td>
                                        <td style="width: 150px;">
                                            <select id="cmbTipo_'.$idEstacion.'_'.$row['id'].'" class="select2 form-control w-100">
                                                '.__buildCmbTipoEntidad($row['idTipoEntidad']).'
                                            </select>
                                        </td>
                                        <td style="width: 150px;">
                                            <select id="cmbDistrito_'.$idEstacion.'_'.$row['id'].'" class="select2 form-control w-100">
                                                '.__buildCmbDistrito($row['idDistrito']).'
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group" style="width: 130px;">
                                                <input id="fechaIn_'.$idEstacion.'_'.$row['id'].'" type="text" class="form-control date_picker" 
                                                   value="'._getFormatoFechaDatePicker($row['fechaInicio'],'-',1).'" placeholder="Fecha Inicio" id="fechaInicio">
                                                <div class="input-group-append">
                                                    <span class="input-group-text fs-xl">
                                                        <i class="fal fa-calendar-plus"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group" style="width: 130px;">
                                                <input id="fechaFin_'.$idEstacion.'_'.$row['id'].'" type="text" class="form-control date_picker" 
                                                 value="'._getFormatoFechaDatePicker($row['fechaFin'],'-',1).'" placeholder="Fecha Fin" id="fechaFin">
                                                <div class="input-group-append">
                                                    <span class="input-group-text fs-xl">
                                                        <i class="fal fa-calendar-plus"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex demo">
                                                '.$btnArchivoComp.$btnGuardarExp. $btnDelete. $btnArchivoTermino.'
                                            </div>
                                        </td>
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function registrarExpLicencia() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $jsonDataLicencia = json_decode($this->input->post('jsonDataLicencia'), true);
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();

            $file     = $_FILES ["file"] ["name"];
            $filetype = $_FILES ["file"] ["type"];
            $filesize = $_FILES ["file"] ["size"];
            $archivo  = $_FILES["file"]["tmp_name"];

            if($idUsuario == null || $idUsuario == '') {
                throw new Exception('La sesión a expirado, recargue la página');
            }

            $ubicEvidencia = 'uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'];
            if (!is_dir ( 'uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'])){
                mkdir('uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'], 0777 );
            }
            // _log(print_r($jsonDataLicencia,true));
            // throw new Exception('GAAAAAAAAAAAAAAAAAAAAAA');

            //$file2 = utf8_decode($file);
            //$rutaFile = $ubicEvidencia . "/" . $file2;
			$rutaFile = $ubicEvidencia . "/" . $file;
            if(utf8_decode($file) && move_uploaded_file($archivo, $rutaFile)) {
                $jsonDataLicencia['idUsuarioExp'] = $idUsuario;
                $jsonDataLicencia['fechaExp']     = $fechaActual;
                $jsonDataLicencia['estado']       = 1;
                $jsonDataLicencia['ubicacion_evidencia'] = $rutaFile;
                if(in_array($jsonDataLicencia['idTipoEntidad'],array(1,3))){//COMUNICATIVA O CALA
                    $jsonDataLicencia['estado'] = 2;
                }
                $data = $this->m_utils->actualizarLicencia($jsonDataLicencia);
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
                //logica para pasar de estado
                $data = $this->cerrarFaseEnLicencia($jsonDataLicencia['itemplan']);
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }

            }
            $data['tablaEntidadItemplanEstacion'] = $this->tablaEntidadItemplanEstacion($jsonDataLicencia['itemplan'], $jsonDataLicencia['idEstacion']);
            $data['error'] = EXIT_SUCCESS;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
	
	function registrarExpTerminoLicencia() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $jsonDataLicencia = json_decode($this->input->post('jsonDataLicencia'), true);
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();

            $file     = $_FILES ["file"] ["name"];
            $filetype = $_FILES ["file"] ["type"];
            $filesize = $_FILES ["file"] ["size"];
            $archivo  = $_FILES["file"]["tmp_name"];

            if($idUsuario == null || $idUsuario == '') {
                throw new Exception('La sesión a expirado, recargue la página');
            }

            $ubicEvidencia = 'uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'];
            if (!is_dir ( 'uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'])){
                mkdir('uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'], 0777 );
            }
            // _log(print_r($jsonDataLicencia,true));
            // throw new Exception('GAAAAAAAAAAAAAAAAAAAAAA');

            //$file2 = utf8_decode($file);
            //$file2 = $file;
            $rutaFile = $ubicEvidencia . "/" . $file;
            if(utf8_decode($file) && move_uploaded_file($archivo, $rutaFile)) {
                $jsonDataLicencia['idUsuarioTermino']  = $idUsuario;
                $jsonDataLicencia['fechaTermino']      = $fechaActual;
                $jsonDataLicencia['has_termino_lic']   = 1;
                $jsonDataLicencia['ubicacion_termino'] = $rutaFile;
                           
                $data = $this->m_utils->actualizarLicencia($jsonDataLicencia);
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }                 
            }
            $data['tablaEntidadItemplanEstacion'] = $this->tablaEntidadItemplanEstacion($jsonDataLicencia['itemplan'], $jsonDataLicencia['idEstacion']);
            $data['error'] = EXIT_SUCCESS;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function eliminarEntidadLicencia() {
        $data['error'] = EXIT_ERROR;
        $data['msj']  = null;
        try {
            $jsonDataLicencia = json_decode($this->input->post('jsonDataLicencia'), true);
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();

            $this->db->trans_begin();

            if($idUsuario == null || $idUsuario == '') {
                throw new Exception('La sesión a expirado, recargue la página.');
            }
            $infoItem = $this->m_utils->getPlanObraByItemplan($jsonDataLicencia['itemplan']);
            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información del itemplan.');
            }
            if(!in_array($infoItem['idEstadoPlan'],array(ID_ESTADO_PLAN_EN_LICENCIA,ID_ESTADO_PLAN_EN_APROBACION,ID_ESTADO_PLAN_EN_OBRA))){
                throw new Exception('No puede eliminar la entidad, ya que la obra esta en un estado no válido.');
            }
            
            $infoEntidadLic = $this->m_utils->getEntidadByItemplanEstacion($jsonDataLicencia['itemplan'], $jsonDataLicencia['idEstacion'], $jsonDataLicencia['idEntidad'], $jsonDataLicencia['id']);
            if($infoEntidadLic == null){
                throw new Exception('Hubo un error al traer la información de la entidad.');
            }

            unset($infoEntidadLic[0]['entidadDesc']);
            unset($infoEntidadLic[0]['flg_comprobante']);
            unset($infoEntidadLic[0]['idEstadoPlan']);

            $infoEntidadLic[0]['idUsuarioReg'] = $idUsuario;
            $infoEntidadLic[0]['fechaRegistro'] = $fechaActual;

            $ubicEvidenciaExp = $infoEntidadLic[0]['ubicacion_evidencia'];
            if (file_exists($ubicEvidenciaExp)) {
                if(!unlink($ubicEvidenciaExp)){
                    throw new Exception('Hubo un error al eliminar el archivo: '.$ubicEvidenciaExp);
                }
            }
            $ubicEvidenciaComp = $infoEntidadLic[0]['ubicacion_comp'];
            if (file_exists($ubicEvidenciaComp)) {
                if(!unlink($ubicEvidenciaComp)){
                    throw new Exception('Hubo un error al eliminar el archivo: '.$ubicEvidenciaComp);
                }
            }
            $data = $this->m_utils->registrarLogLicencia($infoEntidadLic[0]);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $data = $this->m_utils->eliminarEntidadItemplanEstacionById($jsonDataLicencia['id']);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            
            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['tablaEntidadItemplanEstacion'] = $this->tablaEntidadItemplanEstacion($jsonDataLicencia['itemplan'], $jsonDataLicencia['idEstacion']);
            }

        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getTablaComprobanteLic() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan   = $this->input->post('itemplan');
            $idEstacion = $this->input->post('idEstacion');
            $idEntidad  = $this->input->post('idEntidad');
            $id = $this->input->post('id');

            if($itemplan == null || $itemplan == '') {
                throw new Exception('verificar, error interno.');
            }

            if($idEstacion == null || $idEstacion == '') {
                throw new Exception('verificar, error interno.');
            }
            
            if($idEntidad == null || $idEntidad == '') {
                throw new Exception('verificar, error interno.');
            }

            if($id == null || $id == '') {
                throw new Exception('verificar, error interno.');
            }

            $data['error'] = EXIT_SUCCESS;
            $data['tablaEntidadItemplanEstacion'] = $this->tablaComprobanteLic($itemplan, $idEstacion, $idEntidad, $id);
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function tablaComprobanteLic($itemplan, $idEstacion, $idEntidad, $id) {
        $arrayPlanobra = $this->m_utils->getEntidadByItemplanEstacion($itemplan, $idEstacion, $idEntidad, $id);
        $html = '<table id="tbEntidad" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th># Comprobante</th>  
                            <th>Fecha Emisión</th>
                            <th>Monto</th>
                            <th>Valida Comp.</th>
                            <th>Preliq. Admin.</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $cant = 0;
                    foreach ($arrayPlanobra as $row) {
                        $cant++;
                        $btnArchivoComp = ' <div>
                                                <input type="file" id="archivo_comp">
                                            </div>';
                        if($row['estado'] == 2) {
                            $btnArchivoComp = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Descargar Evidencia"
                                                    href="' . $row['ubicacion_comp'] . '">
                                                    <i class="fal fa-download"></i>
                                                </a>';
                        }
                        $styleValida = ($row['flgAccionComp'] == 1 ? 'style="display:block;"' : ($row['flgAccionComp'] == 2 ? 'style="display:none;"' : null));
                        $stylePreliqui = ($row['flgAccionComp'] == 2 ? 'style="display:block;"' : ($row['flgAccionComp'] == 1 ? 'style="display:none;"' : null));

                        $checkValidaComp = '<div class="custom-control custom-checkbox" id="cont_checkValida_'.$cant.'" '.$styleValida.'>
                                                <input type="checkbox" class="custom-control-input" id="checkValida_'.$cant.'" data-flg_tipo="1" data-cant="'.$cant.'" data-id_entidad="'.$row['idEntidad'].'" onchange="validarComprobanteCheck($(this));" '.($row['flgAccionComp'] == 1 ? 'checked' : null).'>
                                                <label class="custom-control-label" for="checkValida_'.$cant.'"></label>
                                            </div>';

                        $checkPreliqComp = '<div class="custom-control custom-checkbox" id="cont_checkPreliq_'.$cant.'" '.$stylePreliqui.'>
                                                <input type="checkbox" class="custom-control-input" id="checkPreliq_'.$cant.'" data-flg_tipo="2" data-cant="'.$cant.'" data-id_entidad="'.$row['idEntidad'].'" data-id="'.$row['id'].'" data-idestacion="'.$idEstacion.'" onchange="validarComprobanteCheck($(this));" '.($row['flgAccionComp'] == 2 ? 'checked' : null).'>
                                                <label class="custom-control-label" for="checkPreliq_'.$cant.'"></label>
                                            </div>';

                        $html .= ' <tr>
                                        <td>
                                            <input id="nro_comp_'.$idEstacion.'_'.$row['id'].'" class="form-control" type="text" value="'.$row['nroComprobante'].'" placeholder="Ingresar Comprobante" />
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input id="fechaEmision_'.$idEstacion.'_'.$row['id'].'" type="text" class="form-control date_picker" 
                                                   value="'._getFormatoFechaDatePicker($row['fechaEmisionComp'],'-',1).'" placeholder="Fecha Inicio">
                                                <div class="input-group-append">
                                                    <span class="input-group-text fs-xl">
                                                        <i class="fal fa-calendar-plus"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input id="monto_'.$idEstacion.'_'.$row['id'].'" class="form-control" type="text" value="'.$row['montoComp'].'" placeholder="Ingresar monto" />
                                        </td>
                                        <td style="text-align: center;">
                                            '.$checkValidaComp.'
                                        </td>
                                        <td style="text-align: center;">
                                            '.$checkPreliqComp.'
                                        </td>
                                        <td>
                                            '.$btnArchivoComp.'
                                        </td>
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function registrarCompLicencia() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $jsonDataLicencia = json_decode($this->input->post('objComprobante'), true);
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();

            $file     = $_FILES ["file"] ["name"];
            $filetype = $_FILES ["file"] ["type"];
            $filesize = $_FILES ["file"] ["size"];
            $archivo  = $_FILES["file"]["tmp_name"];

            if($idUsuario == null || $idUsuario == '') {
                throw new Exception('La sesión a expirado, recargue la página');
            }

            $ubicEvidencia = 'uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'];
            if (!is_dir ( 'uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'])){
                mkdir('uploads/evidencia_licencia/'.$jsonDataLicencia['itemplan'], 0777 );
            }

            $file2 = utf8_decode($file);
            $rutaFile = $ubicEvidencia . "/" . $file2;
            if(utf8_decode($file) && move_uploaded_file($archivo, $rutaFile)) {
                $jsonDataLicencia['idUsuarioComp'] = $idUsuario;
                $jsonDataLicencia['fechaComp']     = $fechaActual;
                $jsonDataLicencia['estado']        = 2;
                $jsonDataLicencia['ubicacion_comp'] = $rutaFile;
                $data = $this->m_utils->actualizarLicencia($jsonDataLicencia);

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
                $data = $this->cerrarFaseEnLicencia($jsonDataLicencia['itemplan']);
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
            }
            $data['error']  = EXIT_SUCCESS;
            $data['tablaEntidadItemplanEstacion'] = $this->tablaEntidadItemplanEstacion($jsonDataLicencia['itemplan'], $jsonDataLicencia['idEstacion']);
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function cerrarFaseEnLicencia($itemplan) {
        $data['error']  = EXIT_ERROR;
        $data['msj']  = null;
        try {
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();

            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            $dataEstadoFinalLicencias = $this->obtEstadoLicenciasEstacionPorItemplan($itemplan);

            if($idUsuario == null || $idUsuario == '') {
                throw new Exception('La sesión a expirado, recargue la página');
            }

            if($infoItem == null) {
                throw new Exception('Hubo un error al traer la información del itemplan.');
            }

            if($dataEstadoFinalLicencias["foCerrado"] == 1 || $dataEstadoFinalLicencias["coCerrado"] == 1){
                if($infoItem['idEstadoPlan'] ==  ID_ESTADO_PLAN_EN_LICENCIA){
                    $arrayUpdatePO = array(
                        'idEstadoPlan' => ID_ESTADO_PLAN_EN_APROBACION,
                        'idUsuarioLog' => $idUsuario, 
                        'fechaLog' => $fechaActual,
                        // "idEstadoReembolso" => 1,
                        // "fecha_pendiente_reembolso" => $fechaActual
                    );
                    $data = $this->m_utils->actualizarPlanObra($itemplan,$arrayUpdatePO);
                    if($data['error'] == EXIT_ERROR) {
                        throw new Exception($data['msj']);
                    }
                    $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                    if($infoItem['idEstadoPlan'] == ID_ESTADO_PLAN_EN_APROBACION){
                        $hasVR = $this->m_utils->countMatPoAprobConVR($itemplan);
                        if($hasVR > 0){
                            $arrayUpdatePO['idEstadoPlan'] = ID_ESTADO_PLAN_EN_OBRA;
                            $arrayUpdatePO['descripcion'] = 'GENERACIÓN DE VR';
                            $data = $this->m_utils->actualizarPlanObra($itemplan,$arrayUpdatePO);
                            if($data['error'] == EXIT_ERROR) {
                                throw new Exception($data['msj']);
                            }

                        }
                    }
                }

            }
            if($dataEstadoFinalLicencias["foCerrado"] == 1 && $dataEstadoFinalLicencias["foLiquidado"] == 0){
                $arrayUpdateDiseno = array(
                    "liquido_licencia" => 1,
                    "usua_liquido_licencia" => $idUsuario,
                    "fec_liquido_licencia" => $fechaActual
                );
                $data = $this->m_utils->updateDiseno($itemplan, ID_ESTACION_FO, $arrayUpdateDiseno);
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
            }

            if($dataEstadoFinalLicencias["coCerrado"] == 1 && $dataEstadoFinalLicencias["coLiquidado"] == 0){
                $arrayUpdateDiseno = array(
                    "liquido_licencia" => 1,
                    "usua_liquido_licencia" => $idUsuario,
                    "fec_liquido_licencia" => $fechaActual
                );
                $data = $this->m_utils->updateDiseno($itemplan, ID_ESTACION_COAX, $arrayUpdateDiseno);
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
            }

            $data['error'] = EXIT_SUCCESS;
            $data['msj'] = 'Se actualizó correctamente';
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    public function obtEstadoLicenciasEstacionPorItemplan($itemplan){
        $foCerrado = 0;
        $coCerrado = 0;
        $liquidoFo = 0;
        $liquidoCo = 0;
		$listaValidar = $this->m_utils->getEvaluacionLicenciasPorEstacionAncla($itemplan);
        foreach ($listaValidar as $row) {            
            if($row->idEstacion == ID_ESTACION_COAX){
                if($row->total_licencias == 0){
                    $coCerrado = 0;
                }else if($row->total_licencias != $row->total_lic_liqui){
                    $coCerrado = 0;
                }else if($row->total_licencias == $row->total_lic_liqui){
                    $coCerrado = 1;
                }                
                if($row->lic_liqui_MD_MP > 0){
                    $coCerrado = 1;
                }
                if($row->liquido_licencia == 1){
                    $liquidoCo = 1;
                }
            }else if($row->idEstacion == ID_ESTACION_FO){
                if($row->total_licencias == 0){
                    $foCerrado = 0;
                }else if($row->total_licencias > 0 && $row->total_lic_liqui > 0){                    
                    $foCerrado = 1;
                }else if($row->total_licencias != $row->total_lic_liqui){                    
                    $foCerrado = 0;
                } else if($row->total_licencias == $row->total_lic_liqui){                    
                    $foCerrado = 1;
                }
                if($row->lic_liqui_MD_MP > 0){
                    $foCerrado = 1;
                }
                if($row->liquido_licencia == 1){
                    $liquidoFo = 1;
                }
            }
        }
        $data["foCerrado"] = $foCerrado;
        $data["coCerrado"] = $coCerrado;
        $data["foLiquidado"] = $liquidoFo;
        $data["coLiquidado"] = $liquidoCo;
        return $data;
    }


    function getTabDiseno($itemplan) {
        // $dataSubEstacion = $this->m_utils->getSubProyectoEstaciosByItemplan($itemplan);
        $tab = null;
        $tabContent = null;
        $tab = '<div id="contTablaDiseno">
                    '.$this->makeTablaDiseno($itemplan).'
                </div>';
        
        return $tab;
    }

    function makeTablaDiseno($itemplan) {
        $html = '';
        $arrayDiseno = $this->m_utils->getDisenoByItemplan($itemplan);
        foreach ($arrayDiseno as $row) {
                $beEjecute = false;
                if($row['estado'] == 2) {
                    if($row['fecha_ejecucion'] == null && $row['flg_orden_compra'] == 1) {
                        $beEjecute  =    false;
                    }
                }

                if($row['estado'] == 3 || ($row['estado'] == 5 && $row->path_expediente_diseno != null)){
                    $beEjecute  =    true;
                }
                
                $html = '<div class="row no-gutters" style="font-weight: bold;margin-left: 10px;"> 
                            <div class="col-lg-12 col-xl-12 col-12 text-left py-1">         
                                <h2 style="font-weight=900"><u>'.$row['estacionDesc'].'</u></h2>         
                            </div>                                                  
                            <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                <a class="text-muted mb-0">SITUACION: </a>
                                <a style="color: var(--theme-primary);">'.(($beEjecute) ? 'EJECUTADO' : 'PENDIENTE DE EJECUCION').'</a>
                            </div>
                            <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                <a class="text-muted mb-0">USUARIO EJECUCION: </a>
                                <a style="color: var(--theme-primary);">'.$row['usu_ejecucion'].'</a>
                            </div>
                            <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                <a class="text-muted mb-0">FECHA EJECUCION: </a>
                                <a style="color: var(--theme-primary);">'.$row['fecha_ejecucion'].'</a>
                            </div>
                            <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                <a class="text-muted mb-0">EXPEDIENTE DISEÑO: </a>'
                                    .(($beEjecute) ? '<a class="btn btn-xs btn-outline-primary btn-inline-block mr-1" title="Descargar Evidencia"
                                    href="' . $row['path_expediente_diseno'] . '">
                                    DESCARGAR <i class="fal fa-download"></i> 
                                </a>': '<a style="color: var(--theme-primary);">SIN  EVIDENCIA</a>').'
                            </div>                
                            <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                <a class="text-muted mb-0">REQUIERE LICENCIA: </a>
                                <a style="color: var(--theme-primary);">'.$row['licencia'].'</a>
                            </div>
                            <div class="col-lg-12 col-xl-12 col-12 text-left py-1">'                                
                                     .(($beEjecute) ? '': '<a class="btn btn-sm btn-primary waves-effect waves-themed" style="color:white" title="Ejecutar Estación" 
                                                    aria-expanded="true" data-itemplan="'.$itemplan.'" data-id_estacion="'.$row['idEstacion'].'" 
                                                    data-id="'.$row['id'].'" data-estacion="'.$row['estacionDesc'].'" 
                                                    onclick="openModalEjecEstacion(this);">EJECUTAR DISEÑO
                                                </a>').'
                            </div>          
                        </div>';

                        $has_dise_rechazado = $this->m_detalle_consulta->getInfoDisenoRechazado($itemplan, $row['idEstacion']);
						
						if($has_dise_rechazado  ==  null){
                            if($row['idSubProyecto']    ==  734 ||  $row['idSubProyecto']    ==  741){//reforzamiento cto
                                $infoFormuRefo = $this->m_detalle_consulta->getInfoFormularioReforzamiento($itemplan);
                                if($infoFormuRefo != null){
                                    $html .= $this->getHtmlCardFormRefCto($infoFormuRefo);
                                }
                            }
                        }
						
                        if($has_dise_rechazado  !=  null){
                            $html .= '<br>
                                    <div class="row no-gutters" style="font-weight: bold;margin-left: 10px;"> 
                                        <div class="col-lg-12 col-xl-12 col-12 text-left py-1">         
                                            <h2 style="font-weight=900"><u>ULTIMA EJECUCION</u></h2>         
                                        </div>                                                  
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">SITUACION: </a>
                                            <a style="color: red;">EJECUCION RECHAZADA</a>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">USUARIO RECHAZO: </a>
                                            <a style="color: var(--theme-primary);">'.$has_dise_rechazado['usu_rechazo'].'</a>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">FECHA RECHAZO: </a>
                                            <a style="color: var(--theme-primary);">'.$has_dise_rechazado['fecha_rechazo'].'</a>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">EXPEDIENTE DISEÑO: </a>
                                            <a class="btn btn-xs btn-outline-primary btn-inline-block mr-1" title="Descargar Evidencia" href="' . $has_dise_rechazado['path_expediente_diseno'] . '">
                                                DESCARGAR <i class="fal fa-download"></i> 
                                            </a>
                                        </div>                
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">REQUIERE LICENCIA: </a>
                                            <a style="color: var(--theme-primary);">'.$has_dise_rechazado['licencia'].'</a>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">COMENTARIO: </a>
                                            <a style="color: var(--theme-primary);">'.$has_dise_rechazado['comentario_rechazo'].'</a>
                                        </div>                                             
                                    </div>';
		
							if($row['idSubProyecto']    ==  734 ||  $row['idSubProyecto']    ==  741){//reforzamiento cto
                                $infoFormuRefo = $this->m_detalle_consulta->getInfoFormularioReforzamiento($itemplan);
                                if($infoFormuRefo != null){
                                    $html .= $this->getHtmlCardFormRefCto($infoFormuRefo);                                    
                                }
                            }
                        }
            }
        return $html;
    }
	
	public function getHtmlCardFormRefCto($arrayFormulario){
        $html = '<br><div class="frame-wrap w-100">
                    <div class="accordion" id="accordionExample2">';
        $cont = 1;
        foreach($arrayFormulario as $formu){
         $html .= '      <div class="card">
                            <div class="card-header" id="headingOne_'.$cont.'">
                                <a href="javascript:void(0);" class="card-title" data-toggle="collapse" data-target="#collapseOne_'.$cont.'" aria-expanded="true" aria-controls="collapseOne_'.$cont.'">
                                    Formulario Reforzamiento CTO #'.$cont.'
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
                            <div id="collapseOne_'.$cont.'" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample2">
                                <div class="card-body">
                                    <div class="row no-gutters" style="font-weight: bold;margin-left: 10px;"> 
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">CTO ADJUDICADO: </a>
                                            <a style="color: var(--theme-primary);">'.$formu['cto_ajudi'].'</a>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">DIVICAU: </a>
                                            <a style="color: var(--theme-primary);">'.$formu['divcau'].'</a>
                                        </div>         
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">TIPO REFORZAMIENTO: </a>
                                            <a style="color: var(--theme-primary);">'.$formu['tipo_refo'].'</a>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">REFORZAMIENTO CTO FINAL: </a>
                                            <a style="color: var(--theme-primary);">'.$formu['do_splitter'].'</a>
                                        </div>           
                                        <!--<div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">CODIGO SPLITTER (NUEVO CTO)</a>
                                            <a style="color: var(--theme-primary);">'.$formu['nuevo_splitter'].'</a>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">NUEVO CODIGO CTO: </a>
                                            <a style="color: var(--theme-primary);">'.$formu['nuevo_cod_cto'].'</a>
                                        </div>   --> 
                                        <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                            <a class="text-muted mb-0">OBSERVACION: </a>
                                            <a style="color: var(--theme-primary);">'.$formu['observacion'].'</a>
                                        </div>                                               
                                    </div>
                                </div>
                            </div>
                        </div>';
                        $cont++;
        }
        $html .= '   </div>
                </div>';
                    
            return $html;
    }

    public function getEntidadesForEjecucion()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación!!');
            }
            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            $data['uip'] = $infoItem['cantFactorPlanificado'];
            $data['cmbEntidad'] = __buildComboEntidades($itemplan,$idEstacion);
            $data['error'] = EXIT_SUCCESS;

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function ejecutarDiseno(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{

            $id             = $this->input->post('id') ? $this->input->post('id') : null;
            $itemplan       = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idEstacion     = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
            $otActualizacion = $this->input->post('chxOTAC');
            $arrayEntidad   = $this->input->post('arrayEntidad') ? json_decode($this->input->post('arrayEntidad'),true) : array();
            $uips           = $this->input->post('uip') ? $this->input->post('uip') : null;
            $idUsuario      = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $tipoDisenoB2b  = $this->input->post('tipoDisenoB2b') ? $this->input->post('tipoDisenoB2b') : null;
			
			$formulariosCto      = $this->input->post('formulariosRefCto')       ? json_decode($this->input->post('formulariosRefCto'),true)       : null; 
            //log_message('error', 'tipo_diseno:'.$tipoDisenoB2b);
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($id == null){
                throw new Exception('Hubo un error a recibir la cita!!');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }

            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null){
                throw new Exception('No se pudo obtener información del itemplan, inténtelo de nuevo!!');
            }

            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación!!');
            }
            
            if(count($_FILES) == 0){
                throw new Exception('Hubo un error al recibir la evidencia!!');
            }
            $editUip = false;
            if(in_array($infoItem['idProyecto'], array(21,52)) ||  $infoItem['idSubProyecto'] ==  734 ||  $infoItem['idSubProyecto']    ==  741){//SI ES EDIFICIOS O FFTTH
                if($uips    ==  null    ||  $uips   ==  ''  ||  $uips   <=  0){
                    throw new Exception('UIP Invalido');
                }else{
                    if($infoItem['cantFactorPlanificado']  <>   $uips){
                        $editUip = true;
                    }
                }                
            }
        
            $fechaActual = $this->m_utils->fechaActual();
            $reject = false;            
            $requestSirope = true;//SE RETIRA LA VALIDACION DE SIROPE- FALSE 11.07.2022
            $infoPO = $this->m_utils->getCountPOsMatEstacionesByItemplan($itemplan);
            if ($infoPO != null) {
                if($infoItem['idSubProyecto'] == 737){//SOTERRADO
                    if($infoPO['conDiseno'] >  0){ 
                        $reject = false;
                        $requestSirope = false;//NO REQUIERE SIROPE
                    }else{
                        throw new Exception('No se puede ejecutar la estación, requiere de una po de Diseño');
                    }
                }else if ($idEstacion == ID_ESTACION_COAX) {
                    if ($infoPO['conCoax'] == 0) {
                        $reject = true;
                    }
                } else if ($idEstacion == ID_ESTACION_FO) {
                    if ($infoPO['conFo'] == 0) {
						if($infoItem['idProyecto']== 52){//SI ES FTTH
							if (($infoPO['conFoDist'] +$infoPO['conFoAlim']) == 0) {
								$reject = true;
							}
						}else if(/*$infoItem['is_habilitacion']   ==  1*/$tipoDisenoB2b ==    8){//Hbilitacion){//si es habilitacion de hilos pasa no requiere po mat.
                            $reject = false;    
                        }else{
							$reject = true;
						}
                    }
                }
            }else if(/*$infoItem['is_habilitacion']   ==  1*/$tipoDisenoB2b ==    8){//si es habilitacion de hilos pasa no requiere po mat.
                $reject = false;    
            }else if ($infoItem['idSubProyecto'] == 737) {//SOTERRADO
                throw new Exception('No se puede ejecutar la estación, requiere de una po de Diseño');              
            }else if ($infoItem['idSubProyecto'] == 741) {//% INCREMENTO PENTARCION
                $reject = false;           
            }else if ($infoPO == null) {
                $reject = true;
            }

            if ($reject) {
                throw new Exception('No se puede ejecutar la estación, ya que no cuenta con almenos 1 PO Material');
            }

            $otToCheekStatus = '';
            if($requestSirope){
				if($idEstacion  ==  ID_ESTACION_FO){
					$otToCheekStatus =  $itemplan.'FO';
				}else if($idEstacion  ==  ID_ESTACION_COAX){
					$otToCheekStatus =  $itemplan.'COAX';
				}
				
                //$this->m_integracion_sirope->execWsFilter($itemplan, $otToCheekStatus);//que siem
				$arrayIpException = array('');
				if(!in_array($itemplan,$arrayIpException)){
					if($idEstacion == ID_ESTACION_FO){
						if($infoItem['has_sirope_fo'] != '1'){
							if($infoItem['has_sirope_diseno'] != '1'){
								throw new Exception('Se requiere que la OT '.$otToCheekStatus.' se encuentre en estado 3-EN CONSTRUCCION.');
							}
						}
					}else if($idEstacion == ID_ESTACION_COAX){
						if($infoItem['has_sirope_coax']!='1'){
							if($infoItem['has_sirope_coax_diseno'] != '1'){
								throw new Exception('Se requiere que la OT '.$otToCheekStatus.' se encuentre en estado 3-EN CONSTRUCCION.');
							}
						}
					}
				}
			}

            $arrayInfoEstacion = $this->m_utils->getInfoEstacionById($idEstacion);
            if($arrayInfoEstacion == null){
                throw new Exception('Hubo un error al traer la información de la estación!!');
            }

            $dataInsertLic = array();
            $sinLicencia = false;
            if(count($arrayEntidad) > 0){
                foreach ($arrayEntidad as $row) {
                    if($row == null || $row == ''){
                        $sinLicencia = true;
                        break;
                    }else{
                        $arrayTemp = array(
                            "idEntidad" => $row,
                            "itemplan" => $itemplan,
                            "idEstacion" => $idEstacion
                        );
                        $dataInsertLic[]= $arrayTemp;
                    }
                }
                if (!$sinLicencia) {
                    $data = $this->m_utils->registrarEntidad($dataInsertLic);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }
                }
            }
            $dataUpdPo = array(
                'itemplan' => $itemplan, 
                ($idEstacion == 5 ? 'expediente_diseno_fo' : 'expediente_diseno_coax') => 1, 
                ($idEstacion == 5 ? 'plano_diseno_sirope_fo' : 'plano_diseno_sirope_coax') => 1, 
                'fec_ult_ejec_diseno' => $fechaActual
            );
            if($editUip){
                $dataUpdPo['cantFactorPlanificado']  =   $uips;
            }
            $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdPo);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }

            if ($infoItem['idProyecto']  ==  52)
            {
                if($editUip)
                {
                    $data = $this->m_detalle_consulta->actualizarDisenoFTTH($itemplan, $uips);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }
                }
            }

            $requiere_licencia = null;
            if ($sinLicencia) {
                $requiere_licencia = 2;
            }

            if ($infoItem['idEstadoPlan'] == ID_ESTADO_PLAN_DISENIO) {
                if($infoItem['idProyecto']  ==  52 ||	$infoItem['idSubProyecto'] == 750	||	$infoItem['idSubProyecto'] == 751){//FTTH
                    $costoMat = $this->m_detalle_consulta->getTotalMatByItemplan($itemplan);
                    $dataUpdPo = array(
                        'idEstadoPlan'  => 7, //DISENO EJECUTADO
                        'idUsuarioLog'  => $idUsuario, 
                        'fechaLog'      => $fechaActual,
                        'descripcion'   => 'DISEÑO - BANDEJA EJECUCIÓN',
                        'flg_valida_diseno'        =>  null,
                        'comentario_valida_diseno' =>  null,
                        'fecha_valida_diseno'      =>  null,
                        'id_usuario_valida_diseno' =>  null,
                        'costo_unitario_mat'       =>  (($costoMat == null) ? 0 : $costoMat)
                    );
                    // $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdPo);
                    $data   =   $this->m_detalle_consulta->updateToPoDeleteDise($itemplan, $idEstacion, $dataUpdPo);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }
                    $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                }else if($infoItem['idProyecto']  ==  3){//SISEGOS
                    
                    $dataUpdPo = array(
                        'idEstadoPlan'  => 7, //DISENO EJECUTADO
                        'idUsuarioLog'  => $idUsuario, 
                        'fechaLog'      => $fechaActual,
                        'descripcion'   => 'DISEÑO - BANDEJA EJECUCIÓN',
                        'flg_valida_diseno'        =>  null,
                        'comentario_valida_diseno' =>  null,
                        'fecha_valida_diseno'      =>  null,
                        'id_usuario_valida_diseno' =>  null
                    );

                    //validamos si cambio de estudio
                    if($tipoDisenoB2b ==    8){//Hbilitacion
                        $dataUpdPo['is_habilitacion']  =    1;   
                        $infoItem['is_habilitacion']    =   1;
                    }else{
                        $dataUpdPo['is_habilitacion']  =    null;
                        $infoItem['is_habilitacion']    =   null;
                    }

                    // $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdPo);
                    $data   =   $this->m_detalle_consulta->updateToPoDeleteDise($itemplan, $idEstacion, $dataUpdPo);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }

                    if($infoItem['is_habilitacion'] ==  1){
                        $codPartida = '12000-6'; //PROYECTO GENERICO
                        $cantidad  =   1;//OBRA
                        $this->generarPoDisenoByCodPartidaCantidad($itemplan, $codPartida, $cantidad);
                    }else{
                        $codPartida = '10001-3'; //MEDIA COMPLEJIDAD
                        $cantidad  =   1;//OBRA
                        $this->generarPoDisenoByCodPartidaCantidad($itemplan, $codPartida, $cantidad);
                        //$this->generarPoDisenoB2b($itemplan, $idUsuario, $fechaActual);
                    }

                    $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                }else if($infoItem['idSubProyecto']  ==  734 ||  $infoItem['idSubProyecto']    ==  741){//REFORZAMIENTO DE CTO
                    
                    $dataUpdPo = array(
                        'idEstadoPlan'  => 7, //DISENO EJECUTADO
                        'idUsuarioLog'  => $idUsuario, 
                        'fechaLog'      => $fechaActual,
                        'descripcion'   => 'DISEÑO - BANDEJA EJECUCIÓN',
                        'flg_valida_diseno'        =>  null,
                        'comentario_valida_diseno' =>  null,
                        'fecha_valida_diseno'      =>  null,
                        'id_usuario_valida_diseno' =>  null
                    );
                    // $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdPo);
                    $data   =   $this->m_detalle_consulta->updateToPoDeleteDise($itemplan, $idEstacion, $dataUpdPo);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }

                    $codPartida = '12000-6'; //PROYECTO GENERICO
                    $this->generarPoDisenoByCodPartidaCantidad($itemplan, $codPartida, $infoItem['cantFactorPlanificado']);
                    
					$formulariosRefCtoInsert  = array();
                    foreach($formulariosCto as $formulario){
                        $dataFormulario = array(
                            'itemplan'          =>  $itemplan,
                            'cto_ajudi'         =>  $formulario['cto_ajudi'],
                            'divcau'            =>  $formulario['divcau'],
                            'tipo_refo'         =>  $formulario['tipo_refo'],
                            'do_splitter'       =>  $formulario['do_splitter'],
                          //  'nuevo_splitter'    =>  $formulario['nuevo_splitter'],
                          //  'nuevo_cod_cto'     =>  $formulario['nuevo_cod_cto'],
                            'observacion'       =>  $formulario['observacion'],
                            'fecha_registro'    =>  $fechaActual,
                            'usuario_registro'  =>  $idUsuario
                        );
                        array_push($formulariosRefCtoInsert, $dataFormulario);
                    }                    
                    
                    if(count($formulariosRefCtoInsert)  >   0){
                        $this->m_detalle_consulta->insertFormularioReforzamiento($itemplan, $formulariosRefCtoInsert);
                    }
					
					$infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                  
                }else if($infoItem['idSubProyecto']  ==  737){//SOTERRADO                    
                    $dataUpdPo = array(
                        'idEstadoPlan'  => 7, //DISENO EJECUTADO
                        'idUsuarioLog'  => $idUsuario, 
                        'fechaLog'      => $fechaActual,
                        'descripcion'   => 'DISEÑO - BANDEJA EJECUCIÓN',
                        'flg_valida_diseno'        =>  null,
                        'comentario_valida_diseno' =>  null,
                        'fecha_valida_diseno'      =>  null,
                        'id_usuario_valida_diseno' =>  null
                    );
                    // $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdPo);
                    $data   =   $this->m_detalle_consulta->updateToPoDeleteDise($itemplan, $idEstacion, $dataUpdPo);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    } 
                    $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);                  
                }else if($infoItem['idSubProyecto']    ==  743){//INTERCONEXION OPERADORES                    
                        $dataUpdPo = array(
                            'idEstadoPlan'  => 7, //DISENO EJECUTADO
                            'idUsuarioLog'  => $idUsuario, 
                            'fechaLog'      => $fechaActual,
                            'descripcion'   => 'DISEÑO - BANDEJA EJECUCIÓN',
                            'flg_valida_diseno'        =>  null,
                            'comentario_valida_diseno' =>  null,
                            'fecha_valida_diseno'      =>  null,
                            'id_usuario_valida_diseno' =>  null
                        );
                        // $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdPo);
                        $data   =   $this->m_detalle_consulta->updateToPoDeleteDise($itemplan, $idEstacion, $dataUpdPo);
                        if($data['error'] == EXIT_ERROR){
                            throw new Exception($data['msj']);
                        } 

                        $codPartida = '10001-3'; //MEDIA COMPLEJIDAD
                        $cantidad  =   1;//OBRA
                        $this->generarPoDisenoByCodPartidaCantidad($itemplan, $codPartida, $cantidad);
                        
                        $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                }else{
                    if($sinLicencia) {
                        $data = $this->m_diseno->changeEstadoEnAprobacionFromEjecucionNoLicencia($itemplan,$idUsuario,$fechaActual);
                        if($data['error'] == EXIT_ERROR){
                            throw new Exception($data['msj']);
                        }
                        $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                        if($infoItem['idEstadoPlan'] == ID_ESTADO_PLAN_EN_APROBACION){
                            $hasVR = $this->m_utils->countMatPoAprobConVR($itemplan);
                            if($hasVR > 0){
                                $arrayUpdatePO = array(
                                    'idEstadoPlan' => ID_ESTADO_PLAN_EN_OBRA, 
                                    'idUsuarioLog' => $idUsuario, 
                                    'fechaLog' => $fechaActual,
                                    'descripcion' => 'GENERACIÓN DE VR'
                                );
                                $data = $this->m_utils->actualizarPlanObra($itemplan,$arrayUpdatePO);
                                if($data['error'] == EXIT_ERROR) {
                                    throw new Exception($data['msj']);
                                }
                            }
                        }

                    }else {
                        $dataUpdPo = array(
                            'idEstadoPlan' => ID_ESTADO_PLAN_EN_LICENCIA, 
                            'idUsuarioLog' => $idUsuario, 
                            'fechaLog' => $fechaActual,
                            'descripcion' => 'DISEÑO - BANDEJA EJECUCIÓN'
                        );
                        $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdPo);
                        if($data['error'] == EXIT_ERROR){
                            throw new Exception($data['msj']);
                        }
                        $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                    }                   
                }
            }

            //DE NO EXISTIR LA CARPETA EVIDENCIA_DISENO LA CREAMOS
            if (!file_exists("uploads/evidencia_diseno")) {
                if (!mkdir("uploads/evidencia_diseno")) {
                    throw new Exception('Hubo un error al crear la carpeta evidencia_diseno!!');
                }
            }
            // DE NO EXISTIR LA CARPETA ITEMPLAN LA CREAMOS
            $pathItemplan = 'uploads/evidencia_diseno/' . $itemplan;
            if (!file_exists($pathItemplan)) {
                if (!mkdir($pathItemplan)) {
                    throw new Exception('Hubo un error al crear la carpeta ' . $pathItemplan . '!!');
                }
            }
            
            $pathItemEstacion = $pathItemplan . '/' . $arrayInfoEstacion['estacionDesc'];
            if (!file_exists($pathItemEstacion)) {
                if (!mkdir($pathItemEstacion)) {
                    throw new Exception('Hubo un error al crear la carpeta ' . $pathItemEstacion . '!!');
                }
            }
            $rutaFinalArchivo = null;
            if(count($_FILES) > 0){
                $nombreArchivo = $_FILES['file']['name'];
                $tipoArchivo = $_FILES['file']['type'];
                $nombreArchivoTemp = $_FILES['file']['tmp_name'];
                $tamano_archivo = $_FILES['file']['size'];
                $rutaFinalArchivo = $pathItemEstacion . '/' . basename($nombreArchivo);
                if (!move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {
                    throw new Exception('No se pudo subir el archivo: ' . basename($nombreArchivo) . ' !!');
                }
            }

            $arrayDataUpdDiseno = array (
                'fecha_ejecucion'	     => $fechaActual,	    
                'usuario_ejecucion'      => $idUsuario,
                'estado'                 => 3,
                'path_expediente_diseno' => $rutaFinalArchivo,
                'requiere_licencia'      => $requiere_licencia
            );
            
            $data = $this->m_diseno->liquidarDiseno($id,$arrayDataUpdDiseno);
            if ($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se ejecutó exitosamente el diseño!!';
                $data['tbDiseno'] = $this->makeTablaDiseno($itemplan);
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function generarPoDisenoB2b($itemplan, $idUsuario, $fechaActual){
        $hasPoPqtACtive = $this->m_utils->hasPoActive($itemplan, 1);//1 diseno
        if($hasPoPqtACtive  ==  0){
            $codigoPO = $this->m_utils->getCodigoPO($itemplan);
            if($codigoPO == null || $codigoPO == '') {
                throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
            }
            $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MO', 1);
            if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                throw new Exception("No tiene configurado un area.");
            }

            $partidasDiseno = $this->m_utils->getDataPartidaDisenoMediaComplejidad($itemplan);
            if($partidasDiseno == null){
                throw new Exception('No se encontraro preiario para la partida de Media Complejidad');
            }

            $arrayDetalleInsert = array();
            $costo_total_po = $partidasDiseno['baremo']*$partidasDiseno['costo']*1;
            $costo_partida  = $partidasDiseno['baremo']*$partidasDiseno['costo']*1;

            $detallePo = array (
                'codigo_po'        => $codigoPO,
                'codigoPartida'    => $partidasDiseno['codigoPartida'], //ESTADO REGISTRADO
                'baremo'           => $partidasDiseno['baremo'],
                'preciario'        => $partidasDiseno['costo'],
                'cantidadInicial'  => 1,
                'montoInicial'     => $costo_partida,
                'cantidadFinal'    => 1,
                'montoFinal'       => $costo_partida,
                'costoMo'          => $costo_partida
            );        
            array_push($arrayDetalleInsert, $detallePo);        	
            
            if(count($arrayDetalleInsert) == 0){
                throw new Exception("No hay partidas válidas para el registro de la PO");
            }

            $dataPo = array (
                'codigo_po'      => $codigoPO,
                'itemplan'       => $itemplan,
                'estado_po'      => ID_ESTADO_PO_REGISTRADO,
                'idEstacion'     => 1,
                'costo_total'    => $costo_total_po,
                'idUsuario'      => $idUsuario,
                'fechaRegistro'  => $fechaActual,
                'flg_tipo_area'  => 2,
                'idEmpresaColab' => $partidasDiseno['idEmpresaColab'],
                'idArea'         => $dataSubEstacionArea['idArea'],
                'idSubProyecto'  => $partidasDiseno['idSubProyecto'],
                'isPoPqt'        => 1
            );

            
            $dataLogPO =    array(
                'codigo_po'        =>  $codigoPO,
                'itemplan'         =>  $itemplan,
                'idUsuario'        =>  $idUsuario,
                'fecha_registro'   =>  $fechaActual,
                'idPoestado'       =>  ID_ESTADO_PO_REGISTRADO
            );

            $data = $this->m_utils->registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
        }
    }

    function generarPoDisenoByCodPartidaCantidad($itemplan, $codPartida, $cantidad){
        $hasPoPqtACtive = $this->m_utils->hasPoActive($itemplan, 1);//1 diseno
        if($hasPoPqtACtive  ==  0){
            $idUsuario      = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual    = $this->m_utils->fechaActual();
            $codigoPO       = $this->m_utils->getCodigoPO($itemplan);
            if($codigoPO == null || $codigoPO == '') {
                throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
            }
            $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MO', 1);
            if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                throw new Exception("No tiene configurado un area.");
            }

            $partidasDiseno = $this->m_utils->getDataPartidaDisenoByCodPartidaItemplan($itemplan, $codPartida);
            if($partidasDiseno == null){
                throw new Exception('No se encontraro preiario para la partida de Media Complejidad');
            }

            $arrayDetalleInsert = array();
            $costo_total_po = $partidasDiseno['baremo']*$partidasDiseno['costo']*$cantidad;
            $costo_partida  = $partidasDiseno['baremo']*$partidasDiseno['costo']*$cantidad;

            $detallePo = array (
                'codigo_po'        => $codigoPO,
                'codigoPartida'    => $partidasDiseno['codigoPartida'], //ESTADO REGISTRADO
                'baremo'           => $partidasDiseno['baremo'],
                'preciario'        => $partidasDiseno['costo'],
                'cantidadInicial'  => $cantidad,
                'montoInicial'     => $costo_partida,
                'cantidadFinal'    => $cantidad,
                'montoFinal'       => $costo_partida,
                'costoMo'          => $costo_partida
            );        
            array_push($arrayDetalleInsert, $detallePo);        	
            
            if(count($arrayDetalleInsert) == 0){
                throw new Exception("No hay partidas válidas para el registro de la PO");
            }

            $dataPo = array (
                'codigo_po'      => $codigoPO,
                'itemplan'       => $itemplan,
                'estado_po'      => ID_ESTADO_PO_REGISTRADO,
                'idEstacion'     => 1,
                'costo_total'    => $costo_total_po,
                'idUsuario'      => $idUsuario,
                'fechaRegistro'  => $fechaActual,
                'flg_tipo_area'  => 2,
                'idEmpresaColab' => $partidasDiseno['idEmpresaColab'],
                'idArea'         => $dataSubEstacionArea['idArea'],
                'idSubProyecto'  => $partidasDiseno['idSubProyecto'],
                'isPoPqt'        => 1
            );

            
            $dataLogPO =    array(
                'codigo_po'        =>  $codigoPO,
                'itemplan'         =>  $itemplan,
                'idUsuario'        =>  $idUsuario,
                'fecha_registro'   =>  $fechaActual,
                'idPoestado'       =>  ID_ESTADO_PO_REGISTRADO
            );

            $data = $this->m_utils->registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
        }
    }

    function getTabObraPreliqui($itemplan) {
                
        $dataObra = $this->m_utils->getPlanObraByItemplan($itemplan);
        if($dataObra['idTipoPlanta'] == ID_TIPO_PLANTA_INTERNA) {
            $has_liqui_rechada = $this->m_utils->hasLiquiRechazadoActivaPIN($itemplan);
            if($has_liqui_rechada   !=  null){//HAY UN RECHAZO ACTIVO
                    $html = $this->getTHtmlEstacionesPIN($itemplan);                    
                    $html .= $this->getTHtmlRechazoPINLiquidado($has_liqui_rechada);
            }else{//NO HAY RECHAZO ACTIVO

                if($dataObra['idEstadoPlan']    ==  ID_ESTADO_PLAN_EN_OBRA){
                    $html = $this->getTHtmlEstacionesPIN($itemplan);
                }else if(in_array($dataObra['idEstadoPlan'], array(ID_ESTADO_PLAN_EN_OBRA, ID_ESTADO_PLAN_PRE_LIQUIDADO, ID_ESTADO_PLAN_TERMINADO, ID_ESTADO_PLAN_EN_CERTIFICACION, ID_ESTADO_PLAN_CERTIFICADO))){
                    $ip =   $this->m_utils->getAllByItemplanFull($itemplan);
                    $html = $this->getTHtmlEstacionesPINLiquidado($ip['ruta_evidencia']);
                }else{
                    $html   =   'El Estado Plan actual no permite Liquidacion de Obra';
                }           

            }
            
        }else{
            $html = $this->getHtmlEstaciones($itemplan);
        }       
    
        $tab = '<div id="contTablaObraPreliqui">
                    <div class="row">
                        '.$html.'
                    </div>
                </div>';
        return $tab;
    }

    function makeTablaEnObraPreliqui($itemplan) {
        $arrayData = $this->m_utils->getPlanobraAll($itemplan,null,array(3,9,4,21,22,23),null);
        $html = '<table id="tbObraPreliqui" class="table table-sm table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>  
                            <th>ITEMPLAN</th>  
                            <th>PROYECTO</th>
                            <th>SUBPROYECTO</th>
                            <th>EMPRESA COLABORADORA</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $cant = 0;
                    foreach ($arrayData as $row) {
                        $cant++;
                        $btnCheck = null;
                        $btnEvi = null;
                        $btnPorcentaje = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Porcentaje" 
                                            aria-expanded="true" data-itemplan="'.$itemplan.'"
                                            onclick="openModalPorcentaje(this);"><i class="fal fa-hourglass-half"></i>
                                        </a>';
                        // if($row['estado'] == 2) {
                        //     if($row['fecha_ejecucion'] == null && $row['flg_orden_compra'] == 1) {
                        //         $btnCheck = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" title="Ejecutar Estación" 
                        //                             aria-expanded="true" data-itemplan="'.$itemplan.'" data-id_estacion="'.$row['idEstacion'].'" 
                        //                             data-id="'.$row['id'].'" data-estacion="'.$row['estacionDesc'].'" 
                        //                             onclick="openModalEjecEstacion(this);"><i class="fal fa-check"></i>
                        //                         </a>';
                        //     }
                        // }
                        // if($row['estado'] == 3 || ($row['estado'] == 5 && $row->path_expediente_diseno != null)){
                        //     $btnCheck = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Descargar Evidencia"
                        //                     href="' . $row['path_expediente_diseno'] . '">
                        //                     <i class="fal fa-download"></i>
                        //                 </a>';
                        // }

                        $html .= ' <tr>
                                        <td>'.$btnPorcentaje.$btnEvi.'</td>
                                        <td>'.$row['itemplan'].'</td>
                                        <td>'.$row['proyectoDesc'].'</td>
                                        <td>'.$row['subproyectoDesc'].'</td>
                                        <td>'.$row['empresaColabDesc'].'</td>
                                        <td>'.$row['estadoPlanDesc'].'</td>
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    public function getEstacionesForLiquidacion()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;

            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }

            $dataObra = $this->m_utils->getPlanObraByItemplan($itemplan);
            log_message('error', print_r($dataObra,true));
            if($dataObra['idTipoPlanta'] == ID_TIPO_PLANTA_INTERNA) {
                $data['htmlEstaciones'] = $this->getTHtmlEstacionesPIN($itemplan);
            }else{
                $data['htmlEstaciones'] = $this->getHtmlEstaciones($itemplan);
            }            
            $data['error'] = EXIT_SUCCESS;

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function getHtmlEstaciones($itemplan)
    {   
        $html = '';
        $estadoPlanIn = $this->m_utils->getEstadoPlanByItemplan($itemplan);
        
            if(!in_array($estadoPlanIn, array(ID_ESTADO_PLAN_EN_OBRA, ID_ESTADO_PLAN_PRE_LIQUIDADO))){
                //$html = '<h2 style="text-align: center;">El Estado Plan actual no permite liquidacion!</h2>';
            }
            $arrayEstaciones = $this->m_utils->getEstacionesByItemplanLiquidacion($itemplan);       
            foreach($arrayEstaciones as $row){
                $has_evi = false;
                $has_per_complete = false;
                $classEvi = 'danger';
                $msjEvi = 'SIN EVIDENCIA';
                $show_down_evi   =   'none';
                $show_up_evi =   'none';
                if($row['path_pdf_pruebas'] != null && $row['path_pdf_pruebas'] != '' && $row['path_pdf_perfil'] != null && $row['path_pdf_perfil'] != ''){
                    $classEvi = 'success';
                    $msjEvi = 'CON EVIDENCIA';
                    $has_evi = true;                
                }
                $eventoCombo = 'onchange="ingresarPorcentajeLiqui(this);"';
                $eventoEvi = 'onclick="openModalEvidencia(this);"';

                if($row['porcentaje']   ==  '100'){
                    $has_per_complete = true;
                }

                if($has_evi && $has_per_complete){
                    $eventoCombo = 'disabled';
                    $eventoEvi = 'disabled';
                    $show_up_evi     =   'none';
                    $show_down_evi   =   'display';
                }else{
                    $show_up_evi     =   'true';
                }
				                
                if(!in_array($row['idEstadoPlan'], array(ID_ESTADO_PLAN_EN_OBRA, ID_ESTADO_PLAN_PRE_LIQUIDADO))){
					if(!in_array($itemplan, array('22-7812428544','22-8718751947'))){
						$eventoCombo = 'disabled';
						$eventoEvi = 'disabled'; 
					}                                 
                }
                
				$onclick = "liquidacionByEsta('".$itemplan."','".str_replace(' ','_',$row['estacionDesc'])."')";
                $html .= '
                    <div class="col-xl-6">
                        <div id="panel-'.$row['idEstacion'].'" class="panel">
                            <div class="panel-hdr">
                                <h2>'.$row['estacionDesc'].'</h2>
                                <div class="panel-toolbar">
                                    <h5 class="m-0">
                                        <span class="badge badge-pill badge-'.$classEvi.' fw-400 l-h-n">
                                            '.$msjEvi.'
                                        </span>
                                    </h5>
                                </div>
                                <div class="panel-toolbar ml-2">
                                    <h5 class="m-0">
                                        <span class="badge badge-primary fw-400 l-h-n">
                                            '.$row['porcentaje'].'%
                                        </span>
                                    </h5>
                                </div>
                            </div>
                            <div class="panel-container show">
                                <div class="panel-content">
                                    <!--
                                        <div class="panel-tag">
                                            Para liquidar la estación debe cargar el <code>porcentaje al 100%</code> y subir <code>las evidencias</code>.
                                        </div>
                                    -->
                                    <form id="formLiqui_'.$row['idEstacion'].'">
                                        <div class="form-row">
                                            <div class="col-md-6 mb-6">
                                                <label class="form-label" for="cmbPorcentaje_'.$row['idEstacion'].'">PORCENTAJE</label>
                                                <select id="cmbPorcentaje_'.$row['idEstacion'].'" name="cmbPorcentaje_'.$row['idEstacion'].'" class="select2 form-control w-100"  
                                                    data-itemplan="'.$itemplan.'" data-idEstacion="'.$row['idEstacion'].'" '.$eventoCombo.'>
                                                    <optgroup label="Porcentajes" data-select2-id="101">
                                                        <option value="0">0%</option>
                                                        '.__buildCmbPorcentaje($row['porcentaje']).'
                                                    </optgroup>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-6" style="display:'.$show_up_evi.'">
                                                <label class="form-label" style="color: white;">-</label>
                                                <button class="btn btn-outline-primary ml-auto waves-effect waves-themed form-control" type="button" data-desc_estacion="'.$row['estacionDesc'].'"
                                                    data-itemplan="'.$itemplan.'" data-idEstacion="'.$row['idEstacion'].'" '.$eventoEvi.' id="btnEvi_'.$row['idEstacion'].'">
                                                    <span class="fal fa-eject mr-1"></span> EVIDENCIA 
                                                </button>
                                            </div>

                                            <div class="col-md-6 mb-6" style="display:'.$show_down_evi.'">
                                                <label class="form-label" style="color: white;">-</label>
                                                <button class="btn btn-outline-primary ml-auto waves-effect waves-themed form-control" type="button" onclick='.$onclick.'>
                                                    <span class="fal fa-download mr-1"></span> EVIDENCIA 
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!--
                                <div class="panel-content py-2 rounded-bottom border-faded border-left-0 border-right-0 border-bottom-0 text-muted">
                                    <div class="progress progress-md w-100 shadow-inset-2">
                                        <div class="progress-bar bg-primary-300 bg-primary-gradient" role="progressbar" style="width: '.$row['porcentaje'].'%;" aria-valuenow="'.$row['porcentaje'].'" aria-valuemin="0" aria-valuemax="100">
                                            '.$row['porcentaje'].'%
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>';
                }
        
        
        return $html;
    }

    public function ingresarPorcentajeLiqui()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
            $porcentaje = $this->input->post('porcentaje');
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();

            $this->db->trans_begin();

            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación!!');
            }
            if($porcentaje == null){
                $porcentaje  = 0;
            }
            if($idUsuario == null){
                throw new Exception('Su sesión ha expirado, porfavor vuelva a logearse.');
            }

            $countExistItemplansEstacAvanc = $this->m_utils->countItemplanEstacionAvance($itemplan, $idEstacion);
            $dataArrayPorcentaje = array(
                'porcentaje'     => $porcentaje,
                'fecha'          => $fechaActual,
                'idUsuarioLog'   => $idUsuario
            );
            if($countExistItemplansEstacAvanc > 0) {
                $data = $this->m_utils->actualizarItemplanEstacionAvance($itemplan, $idEstacion, $dataArrayPorcentaje);
            } else {
                $dataArrayPorcentaje['itemplan'] = $itemplan;
                $dataArrayPorcentaje['idEstacion'] = $idEstacion;
                $data = $this->m_utils->insertItemplanEstacionAvance($dataArrayPorcentaje);
            }
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $dataItemplanEstacionAvance = $this->m_utils->getPorcentajeAvanceByItemplanEstacion($itemplan, $idEstacion);
            if($porcentaje == 100 && $dataItemplanEstacionAvance['path_pdf_pruebas'] != null && $dataItemplanEstacionAvance['path_pdf_perfil'] != null) {
                $listaPO = $this->m_utils->getAllPoByItemplanEstacion($itemplan, $idEstacion);
                $updateDataPo = array();
                $insertDataLog = array();
                foreach($listaPO as $row){
                    $dataUp = array(
                        'estado_po' =>  ID_ESTADO_PO_LIQUIDADO,
                        'codigo_po' =>  $row->codigo_po
                    );
                    $updateDataPo[] = $dataUp;
                    $dataIn = array(
                        'codigo_po' =>  $row->codigo_po,
                        'itemplan' =>  $row->itemplan,
                        'idUsuario' => $idUsuario,
                        'fecha_registro' => $fechaActual,
                        'idPoestado'    =>  ID_ESTADO_PO_LIQUIDADO,
                        'controlador'   => 'liquidacion PO auto'
                    );
                    $insertDataLog[] = $dataIn;
                }
                if(count($updateDataPo) > 0 && count($insertDataLog) > 0){
                    $data = $this->m_utils->updateBatchPo($updateDataPo, $insertDataLog);
                    if($data['error'] == EXIT_ERROR) {
                        throw new Exception($data['msj']);
                    }
                }
            }

            $this->evaluarPreliquidacion($itemplan);

            if($data['error'] == EXIT_SUCCESS) {
                $this->db->trans_commit();
                $data['htmlEstaciones'] = $this->getHtmlEstaciones($itemplan);
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function ingresarEvidenciaLiqui()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
            $descEstacion = $this->input->post('desc_estacion') ? $this->input->post('desc_estacion') : null;
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();

            $this->db->trans_begin();

            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación!!');
            }

            if($idUsuario == null){
                throw new Exception('Su sesión ha expirado, porfavor vuelva a logearse.');
            }
            if(count($_FILES) == 0){
                throw new Exception('Hubo un error al recibir la evidencia!!');
            }

            if(count($_FILES) < 2){
                throw new Exception('Debe cargar los 2 archivos para guardar!!');
            }

            // _log(print_r($_FILES,true));
            $pathItemplan = "uploads/evidencia_liquidacion/".$itemplan;

            if (!file_exists($pathItemplan)) {
                if (!mkdir($pathItemplan)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathItemplan.'!!');
                }
            }

            $pathItemEstacion = $pathItemplan.'/'.$descEstacion;

            if (!file_exists($pathItemEstacion)) {
                if (!mkdir($pathItemEstacion)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathItemEstacion.'!!');
                }
            }
            $pathReflectometricas = $pathItemEstacion.'/P_REFLECTOMETRICAS';
            if (!file_exists($pathReflectometricas)) {
                if (!mkdir($pathReflectometricas)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathReflectometricas.'!!');
                }
            }
            $pathPerfil = $pathItemEstacion.'/P_PERFIL';
            if (!file_exists($pathPerfil)) {
                if (!mkdir($pathPerfil)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathPerfil.'!!');
                }
            }           

            $nombreArchivoReflec = $_FILES['archivo_reflec']['name'];
            $tipoArchivoReflec = $_FILES['archivo_reflec']['type'];
            $nombreArchivoTempReflec = $_FILES['archivo_reflec']['tmp_name'];
            $tamano_archivoReflec = $_FILES['archivo_reflec']['size'];
            $rutaFinalArchivoReflec = $pathReflectometricas."/".$nombreArchivoReflec;
            if (!move_uploaded_file($nombreArchivoTempReflec, $rutaFinalArchivoReflec)) {
                throw new Exception('No se pudo subir el archivo: ' . $nombreArchivoReflec . ' !!');
            }

            $nombreArchivoPerfil = $_FILES['archivo_perfil']['name'];
            $tipoArchivoPerfil= $_FILES['archivo_perfil']['type'];
            $nombreArchivoTempPerfil = $_FILES['archivo_perfil']['tmp_name'];
            $tamano_archivoPerfil = $_FILES['archivo_perfil']['size'];
            $rutaFinalArchivoPerfil = $pathPerfil."/".$nombreArchivoPerfil;
            if (!move_uploaded_file($nombreArchivoTempPerfil, $rutaFinalArchivoPerfil)) {
                throw new Exception('No se pudo subir el archivo: ' . $nombreArchivoPerfil . ' !!');
            }

            $rutaFinalArchivoHGU = null;
            if(isset($_FILES["archivo_hgu"]) && !empty($_FILES["archivo_hgu"])){

                $pathHgu = $pathItemEstacion.'/P_HGU';
                if (!file_exists($pathHgu)) {
                    if (!mkdir($pathHgu)) {
                        throw new Exception('Hubo un error al crear la carpeta '.$pathHgu.'!!');
                    }
                }

                $nombreArchivoHGU   = $_FILES['archivo_hgu']['name'];
                $tipoArchivoHGU     = $_FILES['archivo_hgu']['type'];
                $nombreArchivotmpHGU = $_FILES['archivo_hgu']['tmp_name'];
                $tamano_archivoHGU = $_FILES['archivo_hgu']['size'];
                $rutaFinalArchivoHGU = $pathHgu."/".$nombreArchivoHGU;
                if (!move_uploaded_file($nombreArchivotmpHGU, $rutaFinalArchivoHGU)) {
                    throw new Exception('No se pudo subir el archivo: ' . $nombreArchivoHGU . ' !!');
                }

            }   
            /********************************************************* */
            $rutaFinalArchivoOtros = null;
            if(isset($_FILES["archivo_otros"]) && !empty($_FILES["archivo_otros"])){
                $fileOtros[] = $_FILES['archivo_otros']['tmp_name'];
                if(count($fileOtros)>0){
                    $pathOtros = $pathItemEstacion.'/P_OTROS';
                    if (!file_exists($pathOtros)) {
                        if (!mkdir($pathOtros)) {
                            throw new Exception('Hubo un error al crear la carpeta '.$pathOtros.'!!');
                        }
                    }
                    $nombreArchivoOtros     = $_FILES['archivo_otros']['name'];                
                    $rutaFinalArchivoOtros  = $pathOtros."/".$nombreArchivoOtros;
                    $nombreArchivoTempOtros = $_FILES['archivo_otros']['tmp_name'];
                    if (!move_uploaded_file($nombreArchivoTempOtros, $rutaFinalArchivoOtros)) {
                        throw new Exception('No se pudo subir el archivo: ' . $nombreArchivoOtros . ' !!');
                    }    
                }
            }
            /********************************************************** */

            $countExistItemplansEstacAvanc = $this->m_utils->countItemplanEstacionAvance($itemplan, $idEstacion);
            $dataArrayPorcentaje = array(
                    'fecha'             => $fechaActual,
                    'idUsuarioLog'      => $idUsuario,
                    'path_pdf_pruebas'  => $rutaFinalArchivoReflec,
                    'path_pdf_perfil'   => $rutaFinalArchivoPerfil,
                    'path_zip_otros'    => $rutaFinalArchivoOtros,
                    'path_zip_hgu'      => $rutaFinalArchivoHGU
            );
            if($countExistItemplansEstacAvanc > 0) {
                $data = $this->m_utils->actualizarItemplanEstacionAvance($itemplan, $idEstacion, $dataArrayPorcentaje);
            } else {
                $dataArrayPorcentaje['itemplan'] = $itemplan;
                $dataArrayPorcentaje['idEstacion'] = $idEstacion;
                $dataArrayPorcentaje['porcentaje'] = 0;
                $data = $this->m_utils->insertItemplanEstacionAvance($dataArrayPorcentaje);
            }
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $dataItemplanEstacionAvance = $this->m_utils->getPorcentajeAvanceByItemplanEstacion($itemplan, $idEstacion);
            if($dataItemplanEstacionAvance['porcentaje'] == 100) {
                $listaPO = $this->m_utils->getAllPoByItemplanEstacion($itemplan, $idEstacion);
                $updateDataPo = array();
                $insertDataLog = array();
                foreach($listaPO as $row){
                    $dataUp = array(
                        'estado_po' =>  ID_ESTADO_PO_LIQUIDADO,
                        'codigo_po' =>  $row->codigo_po
                    );
                    $updateDataPo[] = $dataUp;
                    $dataIn = array(
                        'codigo_po' =>  $row->codigo_po,
                        'itemplan' =>  $row->itemplan,
                        'idUsuario' => $idUsuario,
                        'fecha_registro' => $fechaActual,
                        'idPoestado'    =>  ID_ESTADO_PO_LIQUIDADO,
                        'controlador'   => 'liquidacion PO auto'
                    );
                    $insertDataLog[] = $dataIn;
                }
                if(count($updateDataPo) > 0 && count($insertDataLog) > 0){
                    $data = $this->m_utils->updateBatchPo($updateDataPo, $insertDataLog);
                    if($data['error'] == EXIT_ERROR) {
                        throw new Exception($data['msj']);
                    }
                }
            }
            $this->evaluarPreliquidacion($itemplan);

            if($data['error'] == EXIT_SUCCESS) {
                $this->db->trans_commit();
                $data['htmlEstaciones'] = $this->getHtmlEstaciones($itemplan);
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function evaluarPreliquidacion($itemplan)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();
            $arrayEstaciones = $this->m_utils->getEstacionesByItemplan($itemplan);
            $has_estacionAnclaFO = '';
            $has_estacionAnclaCOAX = '';
            $estacionAnclaFO_culminada = 0;
            $estacionAnclaCOAX_culminada = 0;
            $hasVRActivoCoaxial = 0;
            $hasVRActivoFo = 0;
			
			$has_estacionAnclaMante = '';
            $estacionAnclaMante_culminada = 0;

            $has_estacionAnclaOCFO = '';
            $estacionAnclaOCFO_culminada = 0;
						
            if($idUsuario == null){
                throw new Exception('Su sesión ha expirado, porfavor vuelva a logearse.');
            }

            foreach($arrayEstaciones as $row){
                if($row['idEstacion'] == ID_ESTACION_FO){
                    $has_estacionAnclaFO = '1';
                    if($row['porcentaje'] == 100 && $row['path_pdf_pruebas'] != null && $row['path_pdf_perfil'] != null){
                        $estacionAnclaFO_culminada = 1;
                    }
                    $hasVRActivoCoaxial = $this->m_utils->getCountItemplanAptopLiquida($itemplan, ID_ESTACION_FO);//SI ES 0 SI SE PUEDE LIQUIDAR, SI ES 1 NO SE PUEDE YA QUE TIENE VR
                    if($hasVRActivoCoaxial == 1) {
                        throw new Exception('COAX tiene VR verificar');
                    }
                }else if($row['idEstacion'] == ID_ESTACION_COAX){
                    $has_estacionAnclaCOAX = '1';
                    if($row['porcentaje'] == 100 && $row['path_pdf_pruebas'] != null && $row['path_pdf_perfil'] != null){
                        $estacionAnclaCOAX_culminada = 1;
                    }
                    $hasVRActivoFo = $this->m_utils->getCountItemplanAptopLiquida($itemplan, ID_ESTACION_COAX);//SI ES 0 SI SE PUEDE LIQUIDAR, SI ES 1 NO SE PUEDE YA QUE TIENE VR
                    if($hasVRActivoFo == 1) {
                        throw new Exception('FO tiene VR verificar');
                    }
                }else if($row['idEstacion'] == ID_ESTACION_MANTENIMIENTO){
                    $has_estacionAnclaMante = '1';
                    if($row['porcentaje'] == 100 && $row['path_pdf_pruebas'] != null && $row['path_pdf_perfil'] != null){
                        $estacionAnclaMante_culminada = 1;
                    }                    
                }else if($row['idEstacion'] == ID_ESTACION_OC_FO){
                    $has_estacionAnclaOCFO = '1';
                    if($row['porcentaje'] == 100 && $row['path_pdf_pruebas'] != null && $row['path_pdf_perfil'] != null){
                        $estacionAnclaOCFO_culminada = 1;
                    }                    
                }
            }
            $infoITem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoITem == null){
                throw new Exception('Hubo un error al traer la información del itemplan!!');
            }
            $liquidarObra = false;
            if($infoITem['idEstadoPlan'] == ID_ESTADO_PLAN_EN_OBRA){
                /*
                log_message('error', 'evaluando preliquidacion...');
                log_message('error', 'estacionAnclaFO_culminada:'.$estacionAnclaFO_culminada);
                log_message('error', 'has_estacionAnclaCOAX:'.$has_estacionAnclaCOAX);
                log_message('error', 'hasVRActivoCoaxial:'.$hasVRActivoCoaxial);
                log_message('error', 'estacionAnclaCOAX_culminada:'.$estacionAnclaCOAX_culminada);
                log_message('error', 'has_estacionAnclaFO:'.$has_estacionAnclaFO);
                log_message('error', 'hasVRActivoFo:'.$hasVRActivoFo);
                */                
                if($infoITem['idProyecto'] != 32){//TODO MENOS PIN FTTH
                 //   if(in_array($infoITem['idTipoSubProyecto'],array(1,3))){
                        if($estacionAnclaFO_culminada == 1 && ($has_estacionAnclaCOAX == '' || $hasVRActivoCoaxial == 0)){
                            $liquidarObra = true;
                        }else if($estacionAnclaCOAX_culminada == 1 && ($has_estacionAnclaFO == '' || $hasVRActivoFo == 0)){
                            $liquidarObra = true;
                        }else if($estacionAnclaFO_culminada == 1 && $estacionAnclaCOAX_culminada == 1){
                            $liquidarObra = true;
                        }else if($estacionAnclaMante_culminada == 1 && ($has_estacionAnclaCOAX == '' || $hasVRActivoCoaxial == 0) && ($has_estacionAnclaFO == '' || $hasVRActivoFo == 0)){
                            $liquidarObra = true;
                        }else if($infoITem['idSubProyecto'] ==  737){//soterrado para ftth solo con oc
                            if($estacionAnclaOCFO_culminada == 1 && ($has_estacionAnclaCOAX == '' || $hasVRActivoCoaxial == 0) && ($has_estacionAnclaFO == '' || $hasVRActivoFo == 0)){
                                $liquidarObra = true;
                            }
                        }
                }
            }

            if($liquidarObra){
                $dataUpdPo = array(
                    'idEstadoPlan' => ID_ESTADO_PLAN_PRE_LIQUIDADO, 
                    'idUsuarioLog' => $idUsuario, 
                    'fechaLog' => $fechaActual,
                    'descripcion' => 'PRE LIQUIDACIÓN',
                    "fechaPreLiquidacion" =>  $fechaActual
                );
                $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdPo);
                if($data['error'] == EXIT_ERROR){
                    throw new Exception($data['msj']);
                }

                if($infoITem['idProyecto'] == 21){//SOLO CABLEADO DE EDIFICIOS
                    $rsp = $this->m_utils->fnCreatePartidasIntegralesByItemplan($itemplan);
                    if($rsp != 1) {
                        $data['error'] = EXIT_ERROR;
                        throw new Exception('Hubo un error al ejecutar la función de partidas integrales');
                    }
                }

                if($infoITem['idProyecto'] == 3 || $infoITem['idProyecto'] == 52	||	$infoITem['idSubProyecto'] = 734){//SOLO B2B
                    $rsp = $this->m_detalle_consulta->deleteRechaLiquidacion($itemplan);
                    if($rsp != 1) {
                        $data['error'] = EXIT_ERROR;
                        throw new Exception('Hubo un error al borrar_detalle_consulta');
                    }
                }

            }

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getTabAprobacion($itemplan) {
        $tab = null;
        $tabContent = null;
        $tab = '<div id="contTablaAprobacion" class="table-responsive">
                    '.$this->getTablaAprob($itemplan, null).'
                </div>';
        
        return $tab;
    }

    function getTablaAprob($itemplan, $codigoPO) {
        if($itemplan == null && $codigoPO == null){
            $arrayData = array();
        }else{
            $arrayData = $this->m_bandeja_aprobacion_po_mat->getBandejaAprobPo($itemplan, $codigoPO);
        }
        
        $html = '<table id="tbBandejaAprob" class="table table-sm table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            
                            <th>CÓDIGO PO</th>                          
                            <th>ÁREA</th>
                            <th>COSTO TOTAL</th>
                            <th>ESTADO PO</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($arrayData as $row) {
           
            $html .= ' <tr>                          
                            <td>'.$row['codigo_po'].'</td>
                        
                             
                            <td>'.$row['areaDesc'].'</td>
                            <td>'.number_format($row['costo_total'],2).'</td>
                            <td>'.$row['desc_estado_po'].'</td>       
                        </tr>';
        }
        $html .= '</tbody>
            </table>';
        return $html;
    }

    function getContenidoChecks($itemplan) {
        $arrayEstaciones = $this->m_utils->getEstacionesConPoNoCanceladas($itemplan);
        $numEsta = 0;
        $numEstaLiquidadas = 0;
        $arrayIdEsta = array();
        foreach($arrayEstaciones as $row) {
            $style = '';
            if($row['porcentaje'] == 100) {
                $numEstaLiquidadas++;
            }else{
                $style = 'style="color:red"';
            }
            $numEsta++;
            $arrayIdEsta []= $row['idEstacion'];
        }
        $estacionesLiquidadas = false;
        if($numEsta > 0 && $numEstaLiquidadas == $numEsta){//validacion todas estaciones liquidadas.
            $estacionesLiquidadas = true;
        }
		
		if(count($arrayIdEsta)  ==  0){
            $style = '';
            $arrayIdEsta [] = 0;
        }

        $arrayInfoPo = $this->m_utils->getVericaPo($itemplan,$arrayIdEsta);
        if($arrayInfoPo != null){
            $numVrPdt = $arrayInfoPo['numVrPdt'];
            $numPoNoLiqui = $arrayInfoPo['numPoNoLiqui'];
            $numPoMatPdtAnular = $arrayInfoPo['numPoMatPdtAnular'];
        }else{
            $numVrPdt = 0;
            $numPoNoLiqui = 0;
            $numPoMatPdtAnular = 0;
        }

        $outPutContenidoSirope = $this->getContenidoSirope($itemplan);
        $contenidoSirope =  $outPutContenidoSirope['html'];

        $contCheckSirope = '';

        if($outPutContenidoSirope['has_ot_prin']){
            $contCheckSirope .= '<div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="chbx2" '.($outPutContenidoSirope['ot_prin_4'] ? 'checked=""' : null).' disabled="">
                                    <label class="custom-control-label" for="chbx2" '.($outPutContenidoSirope['ot_prin_4'] ? null : 'style="color:red"').'>OT SIROPE FO EN 04</label>
                                </div>';
        }
        if($outPutContenidoSirope['has_ot_coax']){
            $contCheckSirope .= '<div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="chbx2" '.($outPutContenidoSirope['ot_coax_4'] ? 'checked=""' : null).' disabled="">
                                    <label class="custom-control-label" for="chbx2" '.($outPutContenidoSirope['ot_coax_4'] ? null : 'style="color:red"').'>OT SIROPE COAXIAL EN 04</label>
                                </div>';
        }
        $infoITem = $this->m_utils->getPlanObraByItemplan($itemplan);
        $contValOpeB2b = '';
        $infoValOpeB2b =  $this->m_detalle_consulta->getInfoValOpeB2b($itemplan);
        if($infoValOpeB2b   !=  null){
            if($infoValOpeB2b['idProyecto']   ==  3 ||  $infoValOpeB2b['idProyecto']   ==  52   ||  $infoITem['idSubProyecto'] == 734){               
                $contValOpeB2b = '<div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="chbx2" '.($infoValOpeB2b['estado'] == 1 ? 'checked=""' : null).' disabled="">
                                    <label class="custom-control-label" for="chbx2" '.($infoValOpeB2b['estado'] == 1 ? null : 'style="color:red"').'>VALIDACION OPERACIONES?</label>
                                </div>';                
            }
        }
        
        $contOt4HijosMadreRefor = '';
        if($infoITem['idSubProyecto']  ==  747){//SI ES IP MADRE
            $infoMadreExpres = $this->m_detalle_consulta->getHijosOt4ByItemMadreReforzaExpress($itemplan);
            $contOt4HijosMadreRefor = '<div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="chbx2" '.($infoMadreExpres['total_hijos'] == $infoMadreExpres['total_hijos_4'] ? 'checked=""' : null).' disabled="">
                                            <label class="custom-control-label" for="chbx2" '.($infoMadreExpres['total_hijos'] == $infoMadreExpres['total_hijos_4'] ? null : 'style="color:red"').'>TODOS LOS IP HIJOS CON OT 04? ('.$infoMadreExpres['total_hijos_4'].'/'.$infoMadreExpres['total_hijos'].')</label>
                                        </div>';         
        }

        $html = '<div class="demo">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="chbx1" '.($estacionesLiquidadas ? 'checked=""' : null).'   disabled="">
                        <label class="custom-control-label" for="chbx1" '.$style.'>ESTACIONES LIQUIDADAS (100%)</label>
                    </div>
                    '. $contCheckSirope .'
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="chbx4" '.($numVrPdt > 0 ? null : 'checked=""').' disabled="">
                        <label class="custom-control-label" for="chbx4" '.($numVrPdt > 0 ? 'style="color:red"' : null).'>SIN VALE DE RESERVA PENDIENTE?</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="chbx5" '.($numPoNoLiqui == 0 ? 'checked=""' : null).' disabled="">
                        <label class="custom-control-label" for="chbx5" '.($numPoNoLiqui == 0 ? null : 'style="color:red"').'>TODAS PO MO LIQUIDADAS?</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="chbx6" '.($numPoMatPdtAnular > 0 ? null : 'checked=""').' disabled="">
                        <label class="custom-control-label" for="chbx6" '.($numPoMatPdtAnular > 0 ? 'style="color:red"' : null).'>PO MAT PENDIENTE DE ANULAR?</label>
                    </div>
                    '. $contValOpeB2b.$contOt4HijosMadreRefor.'
                </div>';

        return array($html,$contenidoSirope);
    }

    function getContenidoSirope($itemplan) {
        
        $has_ot_prin    = false;
        $ot_prin_4      = false;
        $has_ot_ac      = false;
        $ot_ac_4        = false;
        $has_ot_coax    = false;
        $ot_coax_4      = false;
        
        $inforSirope = $this->m_detalle_consulta->getInfoSiropeByItemplan($itemplan);
        $codigo_ot_principal = 'NO TIENE';
        $estado_ot_principal = '---';
        
        $diseFOLiqui = $this->m_detalle_consulta->getInfoDiseno($itemplan, ID_ESTACION_FO);
        if($diseFOLiqui != null && $diseFOLiqui['estado'] == 3){//SOLO SE CONSIDERA COMO RESTRICCION DE SIROPE SI TIENE LA ESTACION EJECUTADA DE DISENO 
            if($inforSirope['ult_codigo_sirope'] != null){
                $has_ot_prin = true;
                $ot_prin_4 = (($inforSirope['has_sirope_fo'] == 1) ? true : false);
                $codigo_ot_principal = $inforSirope['ult_codigo_sirope'];
                $estado_ot_principal = $inforSirope['ult_estado_sirope'];
            }else if($inforSirope['ot_prin'] != null){
                $has_ot_prin = true;            
                $codigo_ot_principal = $inforSirope['ot_prin'];
                $estado_ot_principal = 'PDT DE ACTUALIZACION';
            }else{
                $has_ot_prin = true;
                $estado_ot_principal = 'PDT GENERAR OT FO';
            } 
        }else if($inforSirope['idSubProyecto'] == 728 || $inforSirope['idSubProyecto'] == 722 || $inforSirope['idSubProyecto'] == 723|| $inforSirope['idSubProyecto'] == 724|| $inforSirope['idSubProyecto'] == 725 || $inforSirope['idSubProyecto'] == 738 || $inforSirope['idSubProyecto'] == 739 || $inforSirope['idSubProyecto'] == 744 || $inforSirope['idSubProyecto'] == 748){//SOLO PARA CTO EXPRESS CV SE LE PIDE SIROPE ASI NO TENGA EJEC DISENO
			if($inforSirope['ult_codigo_sirope']!=null){
                $has_ot_prin = true;
                $ot_prin_4 = (($inforSirope['has_sirope_fo'] == 1) ? true : false);
                $codigo_ot_principal = $inforSirope['ult_codigo_sirope'];
                $estado_ot_principal = $inforSirope['ult_estado_sirope'];
            }else if($inforSirope['ot_prin']!=null){
                $has_ot_prin = true;            
                $codigo_ot_principal = $inforSirope['ot_prin'];
                $estado_ot_principal = 'PDT DE ACTUALIZACION';
            }else{
                $has_ot_prin = true;
                $estado_ot_principal = 'PDT GENERAR OT FO';
            }
		}
        
        //LOGICA COAXIAL
        $codigo_ot_coaxial = 'NO TIENE';
        $estado_ot_coaxial = '---';
        $diseFOLiqui = $this->m_detalle_consulta->getInfoDiseno($itemplan, ID_ESTACION_COAX);
       // $requiereOTCoax = $this->m_utils->requiereOTCoaxial($inforSirope['idSubProyecto']);
       // if($requiereOTCoax > 0){
        if($diseFOLiqui!=null && $diseFOLiqui['estado'] == 3){//SOLO SE CONSIDERA COMO RESTRICCION DE SIROPE SI TIENE LA ESTACION EJECUTADA DE DISENO
            if($inforSirope['ult_codigo_sirope_coax']!=null){
                $has_ot_coax = true;
                $ot_coax_4 = (($inforSirope['has_sirope_coax'] == 1) ? true : false);
                $codigo_ot_coaxial = $inforSirope['ult_codigo_sirope_coax'];
                $estado_ot_coaxial = $inforSirope['ult_estado_sirope_coax'];
            }else if($inforSirope['ot_coax'] != null){           
                $has_ot_coax = true;
                $codigo_ot_coaxial = $inforSirope['ot_coax'];
                $estado_ot_coaxial = 'PDT DE ACTUALIZACION';
            }else{
                $has_ot_coax = true;
                $estado_ot_coaxial = 'PDT GENERAR OT FO';
            }            
        }
       // }
        //FIN LOGICA COAXIAL
        
        $codigo_ot_actualizacion = 'NO TIENE';
        $estado_ot_actualizacion = '---';
        if($inforSirope['ult_codigo_sirope_ac'] != null){
            $has_ot_ac = true;
            $ot_ac_4 = (($inforSirope['has_sirope_ac'] == 1) ? true : false);
            $codigo_ot_actualizacion = $inforSirope['ult_codigo_sirope_ac'];
            $estado_ot_actualizacion = $inforSirope['ult_codigo_sirope_ac'];
        }else if($inforSirope['ot_ac'] != null){
            $has_ot_ac = true;
            $codigo_ot_actualizacion = $inforSirope['ot_ac'];
            $estado_ot_actualizacion = 'PDT DE ACTUALIZACION';
        }
        $html = '<div class="row">
                     <div class="col-sm-6 col-md-6">
                            <p class="color_en_Verificacion_fuente">OT FO PRINCIPAL</p>
                            <table class="table table-bordered">
                                <thead class="bg-primary-600">
                                    <tr>
                                        <th>CODIGO OT</th>
                                        <th>ESTADO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>'.$codigo_ot_principal.'</th>
                                        <th>'.($estado_ot_principal).'</th>
                                    </tr>
                                </tbody>
                            </table>
                    </div>
                    <div class="col-sm-6 col-md-6">
                             <p class="color_en_Verificacion_fuente">OT ACTUALIZACION</p>
                            <table class="table table-bordered">
                                <thead class="bg-primary-600">
                                    <tr>
                                        <th>CODIGO OT</th>
                                        <th>ESTADO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>'.$codigo_ot_actualizacion.'</th>
                                        <th>'.($estado_ot_actualizacion).'</th>
                                    </tr>
                                </tbody>
                            </table>
                    </div>';
            $html .= '<div class="col-sm-6 col-md-6">
                             <p class="color_en_Verificacion_fuente">OT COAXIAL</p>
                            <table class="table table-bordered">
                                <thead class="bg-primary-600">
                                    <tr>
                                        <th>CODIGO OT</th>
                                        <th>ESTADO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>'.$codigo_ot_coaxial.'</th>
                                        <th>'.($estado_ot_coaxial).'</th>
                                    </tr>
                                </tbody>
                            </table>
                    </div>';
        
        $html .= '</div>';
        $outPut = array();
        $outPut['html']         = $html;
        $outPut['has_ot_prin']  = $has_ot_prin;
        $outPut['ot_prin_4']    = $ot_prin_4;#true;#pedido de zavala 28/04/2022
		$outPut['has_ot_coax']  = $has_ot_coax;
		$outPut['ot_coax_4']    = $ot_coax_4;#true;#pedido de zavala 28/04/2022
        $outPut['has_ot_ac']    = $has_ot_ac;
        $outPut['ot_ac_4']      = $ot_ac_4;
        return $outPut;
    }

    function getContenidoTerminado($itemplan) {    
        $htmlFinal = '';
        $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
        if($infoItem == null){//PRIMERO QUE EL IP TENGA INFORMACION
            $htmlFinal = '<h4 class="text-center" style="color:red">Excepcion detectada, comuniquese con soporte.</h4>';
        }else{
            $htmlFinal .= '<div class="py-3 w-100">';
            if($infoItem['idProyecto'] == 21){//CV
                $estacionesAnclas = $this->m_utils->getEstacionesAnclasByItemplan($itemplan);
                $htmlContUl = '<ul class="nav nav-tabs nav-fill" role="tablist">';
                $htmlContTab = '<div class="tab-content p-3">';
                $flg = 0;
                $contBotones = '';
                foreach ($estacionesAnclas as $row){
                
                
                    $liActive = '';
                    $tabActive = '';
                    if($flg == 0){
                        $liActive = 'active';
                        $tabActive = 'show active';
                        $flg = 1;
                    }
					
					$contPartidasPqt = $this->getPartidasPaquetizadas($infoItem,$row['idEstacion']);
                    $contPartidasAdic = $this->getPartidasAdicionales($infoItem,$row['idEstacion']);
                    // $contPartidasAdic = '';

                    $htmlContUl .= '<li class="nav-item">
                                        <a class="nav-link '.$liActive.'" data-toggle="tab" href="#tab_'.$row['idEstacion'].'" role="tab">
                                            '.$row['estacionDesc'].'
                                        </a>
                                    </li>';

                    $htmlContTab .= '<div class="table-responsive tab-pane fade '.$tabActive.'" id="tab_'.$row['idEstacion'].'" role="tabpanel">
                                        <p style="color: #007bff; text-align: left;font-weight: bold;">Partidas Paquetizadas</p>
                                            '.$contPartidasPqt['html'].'
                                        <p style="color: #007bff; text-align: left;font-weight: bold;">Partidas Adicionales</p>
                                            '. $contPartidasAdic['html'].'
                                    </div>';
                    $costoTotal = round($contPartidasPqt['costoTotalPqt'] + $contPartidasAdic['costoTotalAdic'],2);

                    $contBotones = '<div class="col-xl-6 col-6">
                                        <label style="color: red" class="control-label mb-10 text-left">Adjuntar Expediente.</label><br>
                                        <input id="fileEvidencia" name="fileEvidencia" type="file" class="file" data-show-preview="false">
                                    </div>
                                    <div class="col-xl-6 col-6" style="margin-top: 10px;">
                                        <button data-itemplan="'.$itemplan.'" data-idestacion="'.$row['idEstacion'].'" data-estaciondesc="'.$row['estacionDesc'].'" data-codigo_po="'.$contPartidasPqt['codigoPO'].'" data-tot_pqt="'.$contPartidasPqt['costoTotalPqt'].'" data-tot_padic="'.$contPartidasAdic['costoTotalAdic'].'" data-costo_total="'.$costoTotal.'" class="btn btn-primary ml-auto waves-effect waves-themed sendVali" type="button">APROBAR PROPUESTA</button>
                                    </div>';
                    
                    $solicitudPartAdic = $this->m_detalle_consulta->getSolicitudPartidasAdicionales($itemplan, $row['idEstacion']);
                    if($solicitudPartAdic != null){
                        if($solicitudPartAdic['estado'] == 3){
                            $contBotones = '<div class="col-xl-12">
                                                <h4 class="text-center" style="color:red">La Solicitud previa fue rechazada, puede ver el Motivo <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1 getRechazado2Bucles" data-esta="'.$row['idEstacion'].'" data-item="'.$itemplan.'" title="Movimientos"> <i class="fal fa-search"></i></a></h4>
                                            </div>
                                            <div class="col-xl-2">
                                            </div>
                                            <div class="col-xl-3">
                                                <label style="color: red" class="control-label mb-10 text-left">Adjuntar Expediente.</label><br>
                                                <input id="fileEvidencia" name="fileEvidencia" type="file" class="file" data-show-preview="false">
                                            </div>
                                            <div class="col-xl-3">
                                                <button data-itemplan="'.$itemplan.'" data-idestacion="'.$row['idEstacion'].'" data-estaciondesc="'.$row['estacionDesc'].'" data-codigo_po="'.$contPartidasPqt['codigoPO'].'" data-tot_pqt="'.$contPartidasPqt['costoTotalPqt'].'" data-tot_padic="'.$contPartidasAdic['costoTotalAdic'].'" data-costo_total="'.$costoTotal.'" class="btn btn-primary ml-auto waves-effect waves-themed sendVali" type="button">APROBAR PROPUESTA</button>
                                            </div>
                                            <div class="col-xl-2">
                                                <a class="btn btn-primary ml-auto waves-effect waves-themed" href="pqt_gestion_incidencias" target="_blank" type="button"">OBSERVAR</a>
                                            </div>
                                            <div class="col-xl-2">
                                            </div>';
                        }else if($solicitudPartAdic['estado'] == 4){
                            $contBotones = '<div class="col-xl-12">
                                                <h4 class="text-center" style="color:red">La Solicitud previa fue rechazada, puede ver el Motivo <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1 getRechazado2Bucles" data-esta="'.$row['idEstacion'].'" data-item="'.$itemplan.'" title="Movimientos"><i class="fal fa-search"></i></a></h4>
                                            </div>
                                            <div class="col-xl-2">
                                            </div>
                                            <div class="col-xl-3">
                                                <label style="color: red" class="control-label mb-10 text-left">Adjuntar Expediente.</label><br>
                                                <input id="fileEvidencia" name="fileEvidencia" type="file" class="file" data-show-preview="false">
                                            </div>
                                            <div class="col-xl-3">
                                                <button data-itemplan="'.$itemplan.'" data-idestacion="'.$row['idEstacion'].'" data-estaciondesc="'.$row['estacionDesc'].'" data-codigo_po="'.$contPartidasPqt['codigoPO'].'" data-tot_pqt="'.$contPartidasPqt['costoTotalPqt'].'" data-tot_padic="'.$contPartidasAdic['costoTotalAdic'].'" data-costo_total="'.$costoTotal.'" class="btn btn-primary ml-auto waves-effect waves-themed sendVali" type="button">APROBAR PROPUESTA</button>
                                            </div>
                                            <div class="col-xl-2">
                                                <a class="btn btn-primary ml-auto waves-effect waves-themed" href="pqt_gestion_incidencias" target="_blank" type="button"">OBSERVAR</a>
                                            </div>
                                            <div class="col-xl-2">
                                            </div> ';
                        }else if($solicitudPartAdic['estado'] == 0){
                            $contBotones = '<div class="col-xl-12">
                                                <h4 class="text-center" style="color:red">Tiene una solicitud pdt de validación TDP, Nivel 1.</h4>
                                            </div> ';
                        }else if($solicitudPartAdic['estado'] == 1){
                            
                        $infoExpediente = $this->m_bandeja_valida_obra->getInfoExpedienteLiquidacionNoPqtByItem($itemplan);                          
                        $contBotones = '
                                                <div class="col-xl-4 md-4 mb-4">
                                                </div>
                                                <div class="col-xl-4 md-4 mb-4" style="display:display">
                                                    <label class="form-label" style="color: white;">-</label>
                                                    <a class="btn btn-outline-primary ml-auto waves-effect waves-themed form-control" download type="button" href="'.utf8_decode($infoExpediente['path_expediente']).'">
                                                        <span class="fal fa-download mr-1"></span> EXPEDIENTE 
                                                    </a>
                                                </div>
                                                <div class="col-xl-4 md-4 mb-4">
                                                </div>
                                            ';
                            $contBotones .= '<div class="col-xl-12">
                                                <h4 class="text-center" style="color:red">Tiene una solicitud pdt de validación TDP, Nivel 2.</h4>
                                            </div> ';
                        }
                    }
                }
                $htmlContUl .= '</ul>';
                $htmlContTab .= '</div>';
                $htmlFinal .= $htmlContUl.$htmlContTab.'</div>';

                $htmlFinal .= '<div class="tab-content p-3" style="margin-top: -35px;">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <h4 class="text-center" style="color:var(--theme-primary);:">COSTO TOTAL PO <b>'.$contPartidasPqt['codigoPO'].' </b>:  S./ '.number_format($costoTotal,2).'</h4>
                                    </div>
                                '.$contBotones.'
                                </div>
                            </div> ';
            }else{
                //log_message('error','-->');
                $havePdt = $this->m_detalle_consulta->haveSolPdtValidacionByObra($itemplan);
                $havePdtAprob = $this->m_detalle_consulta->haveSolAprobadaByObra($itemplan);
                    
                $listaPoFull = $this->m_detalle_consulta->getAllPoMoBySoloItemplanFTTHPangea($itemplan);
                $has_pos_no_aceptados = 0;
                $costo_final_obra = 0;
                $has_po = 0;
                $htmlFinal .= '<div style="margin-bottom: 5%;margin-top: 5%;margin-left: 5%;margin-right: 5%;">
                                    <p style="font-size: larger;color: #007bff;text-align: left;font-weight: bold;">PO MO En la Obra</p> 
                                    <table class="table table-bordered">
                                                    <thead class="bg-primary-600">
                                                        <tr>
                                                            <th>AREA</th>
                                                            <th>TIPO</th>
                                                            <th>CODIGO PO</th>
                                                            <th>ESTADO PO</th>
                                                            <th>COSTO TOTAL</th>
                                                    <tr></thead>
                                        <tbody>';
                foreach ($listaPoFull as $row2){
                    $htmlFinal .= '<tr>
                                        <th>'.$row2->estacionDesc.'</th>
                                        <th>'.$row2->tipoPo.'</th>
                                        <th>'.$row2->codigo_po.'</th>
                                        <th>'.$row2->estado.'</th>
                                        <th>'.number_format($row2->costo_total, 2).'</th>
                                      </tr>';
                    $estados_no_acepted = array(ID_ESTADO_PO_REGISTRADO,ID_ESTADO_PO_PRE_APROBADO,ID_ESTADO_PO_APROBADO,ID_ESTADO_PO_PRE_CANCELADO,ID_ESTADO_PO_CERTIFICADO,ID_ESTADO_PO_CANCELADO);
                    if(in_array($row2->estado_po, $estados_no_acepted)){
                        $has_pos_no_aceptados++;
                    }else{
                        if($row2->estado_po	!=	ID_ESTADO_PO_CERTIFICADO){
                            $costo_final_obra = $costo_final_obra+$row2->costo_total;
                            $has_po ++;
                        }
                    }
                }

//log_message('error','-->1');
                $htmlFinal .= '<tr class="bg-primary-600">
                                    <th>TOTAL</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>'.number_format($costo_final_obra, 2).'</th>
                                </tr>
                            </tbody>
                        </table>';
                    $htmlFinal .= '</div></div>'; /*
                    if($havePdt > 0){
                        $htmlFinal .= '<h4 class="text-center" style="color:red">La estacion ya cuenta con una solicitud pdt de validacion.</h4>';
                    }else */
                    if($has_po==0){
                        $htmlFinal .= '<h4 class="text-center" style="color:red">La estacion no cuenta con PO Trabajadas.</h4>';
                    }else if($has_pos_no_aceptados > 0){
                        $htmlFinal .= '<h4 class="text-center" style="color:red">Las PO de MO deben estar Liquidadas.</h4>';
                    }else if($havePdtAprob > 0){
                        $htmlFinal .= '<h4 class="text-center" style="color:red">Solicitud Aprobada.</h4>';
                    }else{
                        $solicitudPartAdic = $this->m_detalle_consulta->getSolicitudPartidasAdicionalesNoEstacion($itemplan);//log_message('error','-->0');
                        if($solicitudPartAdic != null){
                            $contBotones = '<div class="row">';
                            if($solicitudPartAdic['estado'] == 3){          //log_message('error','-->22');                     
                                $contBotones .= '<div class="col-xl-12">
                                                    <h4 class="text-center" style="color:red">La Solicitud previa fue rechazada, puede ver el Motivo <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1 getRechazado2Bucles" data-item="'.$itemplan.'" title="Movimientos"> <i class="fal fa-search"></i></a></h4>
                                                </div>
                                                <div class="col-xl-2">
                                                </div>
                                                <div class="col-xl-6">
                                                    <label style="color: red" class="control-label mb-10 text-left">Adjuntar Expediente.</label><br>
                                                    <input id="fileEvidencia" name="fileEvidencia" type="file" class="file" data-show-preview="false">
                                                </div>
                                                <div class="col-xl-4">
                                                    <button data-itemplan="'.$itemplan.'" data-costo_total="'.$costo_final_obra.'"class="btn btn-primary ml-auto waves-effect waves-themed sendValiNoPqt" type="button">APROBAR PROPUESTA</button>
                                                </div>';
                            }else if($solicitudPartAdic['estado'] == 4){ //log_message('error','-->23');      
                                $contBotones .= '<div class="col-xl-12">
                                                    <h4 class="text-center" style="color:red">La Solicitud previa fue rechazada, puede ver el Motivo <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1 getRechazado2Bucles" data-item="'.$itemplan.'" title="Movimientos"><i class="fal fa-search"></i></a></h4>
                                                </div>
                                                <div class="col-xl-2">
                                                </div>
                                                <div class="col-xl-6">
                                                    <label style="color: red" class="control-label mb-10 text-left">Adjuntar Expediente.</label><br>
                                                    <input id="fileEvidencia" name="fileEvidencia" type="file" class="file" data-show-preview="false">
                                                </div>
                                                <div class="col-xl-4">
                                                    <button data-itemplan="'.$itemplan.'" data-costo_total="'.$costo_final_obra.'"class="btn btn-primary ml-auto waves-effect waves-themed sendValiNoPqt" type="button">APROBAR PROPUESTA</button>
                                                </div>';
                            }else if($solicitudPartAdic['estado'] == 0){ //log_message('error','-->24');      
                                $contBotones .= '<div class="col-xl-12">
                                                    <h4 class="text-center" style="color:red">Tiene una solicitud pdt de validación TDP, Nivel 1.</h4>
                                                </div> ';
                            }else if($solicitudPartAdic['estado'] == 1){ //log_message('error','-->25');      
                                
                                $infoExpediente = $this->m_bandeja_valida_obra->getInfoExpedienteLiquidacionNoPqtByItem($itemplan);                          
                                $contBotones .= '     <div class="col-xl-4 md-4 mb-4">
                                                        </div>
                                                        <div class="col-xl-4 md-4 mb-4" style="display:display">
                                                            <label class="form-label" style="color: white;">-</label>
                                                            <a class="btn btn-outline-primary ml-auto waves-effect waves-themed form-control" download type="button" href="'.$infoExpediente['path_expediente'].'">
                                                                <span class="fal fa-download mr-1"></span> EXPEDIENTE 
                                                            </a>
                                                        </div>
                                                        <div class="col-xl-4 md-4 mb-4">
                                                        </div>
                                                    ';
                                    $contBotones .= '<div class="col-xl-12">
                                                        <h4 class="text-center" style="color:red">Tiene una solicitud pdt de validación TDP, Nivel 2.</h4>
                                                    </div> ';
                            }
                            $contBotones .= '</div>';
                            $htmlFinal .= $contBotones;
//log_message('error','-->2');
                        }else{//log_message('error','-->3');

                            $htmlFinal .= '<div class="tab-content p-3" style="text-align: center;">
                                                <div class="row"> 
                                                    <div class="col-xl-6 col-6">
                                                        <label style="color: red" class="control-label mb-10 text-left">Adjuntar Expediente.</label><br>
                                                        <input id="fileEvidencia" name="fileEvidencia" type="file" class="file" data-show-preview="false">
                                                    </div>
                                                    <div class="col-xl-6 col-6" style="margin-top: 10px;">
                                                        <button data-itemplan="'.$itemplan.'" data-costo_total="'.$costo_final_obra.'"class="btn btn-primary ml-auto waves-effect waves-themed sendValiNoPqt" type="button">APROBAR PROPUESTA</button>
                                                    </div>
                                                </div>
                                            </div> ';      
                        }                 
                    }                        
            }            
        }
        
        return $htmlFinal;
    }

    function getPartidasPaquetizadas($infoItem, $idEstacion){
        $data['error'] = EXIT_ERROR;
        $data['html'] = '';
        try{
			
			//log_message('error','AQUIIIIIIIIIIIIIIII');
            $arrayCosto = $this->m_registro_itemplan_masivo->getCostoxDptoByIdEECCAndSubProy($infoItem['idSubProyecto'], $infoItem['idEmpresaColab'], $infoItem['cantFactorPlanificado']);
            if($arrayCosto == null){
                throw new Exception('LA OBRA NO CUENTA CON UN PRECIO CONFIGURADO PARA EL SUBPROYECTO, CONTRATA Y DPTO.');
            }
			/*if($infoItem['cantFactorPlanificado']<=16){
				$infoItem['cantFactorPlanificado'] = 0;
			}*/
			//log_message('error','AQUIIIIIIIIIIIIIIII');
            $dataPoPqt = $this->m_detalle_consulta->getInfoPoMoPqtLiquidadoByItemplan($infoItem['itemplan'], $idEstacion);
            if($dataPoPqt == null){
                throw new Exception('LA OBRA NO CUENTA CON UNA PO MO PAQUETIZADA.');
            }
            $infoPartida = $this->m_detalle_consulta->getInfoPartidaByCodPartida($arrayCosto['codigoPartida']);
            $costoTotal = round($arrayCosto['costo']*$infoItem['cantFactorPlanificado'],2);
            $html = '<table id="tbPartidasPqt" class="table table-sm table-bordered table-hover table-striped w-100">
                        <thead class="bg-primary-600">
                            <tr> 
                                <th>CÓDIGO PARTIDA</th>
                                <th>NOMBRE PARTIDA</th>                            
                                <th>BAREMO</th>
                                <th>CANTIDAD</th>
                                <th>PRECIO</th>
                                <th>TOTAL</th>                               
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>'.$arrayCosto['codigoPartida'].'</td>
                                <td>'.$infoPartida['descripcion'].'</td>
                                <td>'.(1).'</td>
                                <td>'.$infoItem['cantFactorPlanificado'].'</td>
                                <td>'.$arrayCosto['costo'].'</td>  
                                <td>'.number_format($costoTotal,2).'</td>
                                  
                            </tr>
                        </tbody>
                        <tfoot class="bg-primary-600">
                            <tr>
                                <th colspan="5" style="text-align: right;">TOTAL:</th>
                                <th>'.number_format($costoTotal,2).'</th>
                                
                            </tr>
                        </tfoot>
                    </table> ';

                $arrayDetalleInsert = array();
                $detallePo = array (
                    'codigo_po'        => $dataPoPqt['codigo_po'],
                    'codigoPartida'    => $arrayCosto['codigoPartida'], //ESTADO REGISTRADO
                    'baremo'           => 1,
                    'preciario'        => $arrayCosto['costo'],
                    'cantidadInicial'  => $infoItem['cantFactorPlanificado'],
                    'montoInicial'     => $costoTotal,
                    'cantidadFinal'    => $infoItem['cantFactorPlanificado'],
                    'montoFinal'       => $costoTotal,
                    'costoMo'          => $costoTotal
                );
                $arrayDetalleInsert[] = $detallePo;
                if(count($arrayDetalleInsert) == 0){
                    throw new Exception("No hay partidas válidas para la propuesta");
                }
            
            
            $data['html'] = $html;
            $data['codigoPO'] = $dataPoPqt['codigo_po'];
            $data['NewdetallePO'] = $arrayDetalleInsert;
            $data['costoTotalPqt'] = $costoTotal;
        
        }catch(Exception $e){
            $data['html'] = $e->getMessage();
        }

        return $data;
    }

    function getPartidasAdicionales($infoItem,$idEstacion){
        $data['error'] = EXIT_ERROR;
        $data['html'] = '';
        try{
            $dataPoPqt = $this->m_detalle_consulta->getInfoPoMoPqtLiquidadoByItemplan($infoItem['itemplan'], $idEstacion);
            if($dataPoPqt == null){
                throw new Exception('LA OBRA NO CUENTA CON UNA PO MO PAQUETIZADA.');
            }
            $arrayPartidasAdic = $this->m_detalle_consulta->getPartidasAdicionales($dataPoPqt['codigo_po']);
            $html = '<table id="tbPartidasAdic" class="table table-sm table-bordered table-hover table-striped w-100">
                        <thead class="bg-primary-600">
                            <tr> 
                                <th>CÓDIGO PARTIDA</th>
                                <th>NOMBRE PARTIDA</th>                            
                                <th>BAREMO</th>
                                <th>CANTIDAD</th>
                                <th>PRECIO</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>';
            $montoTotal = 0;
            foreach($arrayPartidasAdic as $row){
                $html .= '  <tr>
                                <td>'.$row['codigoPartida'].'</td>
                                <td>'.$row['descripcion'].'</td>
                                <td>'.$row['baremo'].'</td>
                                <td>'.$row['cantidadFinal'].'</td>
                                <td>'.$row['preciario'].'</td>  
                                <td>'.$row['montoFinalFormat'].'</td>
                            </tr>';
                $montoTotal = $row['montoFinal'] + $montoTotal;
            }

            $html .= '  </tbody>
                        <tfoot class="bg-primary-600">
                            <tr>
                                <th colspan="5" style="text-align: right;">TOTAL:</th>
                                <th>'.number_format($montoTotal,2).'</th>
                            </tr>
                        </tfoot>
                    </table> ';
            
            $data['html'] = $html;
            $data['costoTotalAdic'] = $montoTotal;
        
        }catch(Exception $e){
            $data['html'] = $e->getMessage();
        }

        return $data;
    }

    public function sendValidatePartidasAdicionales()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
            $codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
            $descEstacion = $this->input->post('estaciondesc') ? $this->input->post('estaciondesc') : null;
            $costoTotal = $this->input->post('costo_total') ? $this->input->post('costo_total') : null;
            $costo_inicial = $this->input->post('costo_inicial') ? $this->input->post('costo_inicial') : null;
            $costo_adicional = $this->input->post('costo_adicional') ? $this->input->post('costo_adicional') : null;

            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();

            $this->db->trans_begin();

            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación!!');
            }
            if($descEstacion == null){
                throw new Exception('Hubo un error al recibir la estación!!');
            }
            if($codigoPO == null){
                throw new Exception('Hubo un error al recibir el código po!!');
            }
            if($costoTotal == null){
                throw new Exception('Hubo un error al recibir el costo total de la propuesta !!');
            }

            if($idUsuario == null){
                throw new Exception('Su sesión ha expirado, porfavor vuelva a logearse.');
            }
            if(count($_FILES) == 0){
                throw new Exception('Hubo un error al recibir la evidencia!!');
            }

            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);

            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información del itemplan!!');
            }

            $pathExpediente = "uploads/evidencia_expediente";
            if (!file_exists($pathExpediente)) {
                if (!mkdir($pathExpediente)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathExpediente.'!!');
                }
            }

            $pathItemplan = $pathExpediente."/".$itemplan;

            if (!file_exists($pathItemplan)) {
                if (!mkdir($pathItemplan)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathItemplan.'!!');
                }
            }

            $pathItemEstacion = $pathItemplan.'/'.$descEstacion;

            if (!file_exists($pathItemEstacion)) {
                if (!mkdir($pathItemEstacion)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathItemEstacion.'!!');
                }
            }

            $nombreArchivo = $_FILES['file']['name'];
            $tipoArchivo = $_FILES['file']['type'];
            $nombreArchivoTemp = $_FILES['file']['tmp_name'];
            $tamano_archivo = $_FILES['file']['size'];
            $rutaFinalArchivo = $pathItemEstacion."/".$nombreArchivo;
            if (!move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {
                throw new Exception('No se pudo subir el archivo: ' . $nombreArchivoTemp . ' !!');
            }

            $dataSolValidacion = array(
                'fec_registro'       => $fechaActual,   
                'usua_registro'     =>  $idUsuario,
                'estado'            =>  0,
                'costo_total'       =>  $costoTotal,
                'itemplan'          =>  $itemplan,
                'idEstacion'        =>  $idEstacion,
                'costo_inicial'     =>  $costo_inicial,
                'costo_adicional'   =>  $costo_adicional,
                'activo'            =>  1                    
            );

            $listaEstacionesTrabajadas  = array();
            if($idEstacion == ID_ESTACION_FO){
                $listaEstacionesTrabajadas  = $this->m_detalle_consulta->getEstaTrabajadasFO($itemplan);
            }else if($idEstacion == ID_ESTACION_COAX){
                $listaEstacionesTrabajadas = $this->m_detalle_consulta->getEstaTrabajadasCOAX($itemplan);    
            }
            
            $array_expedientes = array();
            foreach($listaEstacionesTrabajadas as $row){
                $data_expediente = array(
                    'itemplan'     =>  $itemplan,
                    'fecha'        =>  $fechaActual,
                    'comentario'   =>  'VALIDACION PQT',
                    'usuario'      =>  $idUsuario,
                    'estado'       =>  'ACTIVO',
                    'estado_final' =>  'PENDIENTE',
                    'path_expediente'  => $rutaFinalArchivo,
                    'idEstacion'   =>  $row->idEstacion
                );
                array_push($array_expedientes, $data_expediente);
            }

            $contPartidasPqt = $this->getPartidasPaquetizadas($infoItem,$idEstacion);
            $arrayNewDetPo = $contPartidasPqt['NewdetallePO'];

            $data = $this->m_detalle_consulta->sendValidarPartidasAdicionales($itemplan, $idEstacion, $codigoPO, $dataSolValidacion, $array_expedientes, $arrayNewDetPo);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }

            if($infoItem['idProyecto'] == ID_PROYECTO_CABLEADO_DE_EDIFICIOS){
                $be_validate = false;
                if($infoItem['flg_flujo'] == 1){
                    $arrayValida = $this->m_detalle_consulta->validarMontoForCertificacion($itemplan,$infoItem['tipo_edificio']);
                    if($arrayValida == null){
                        throw new Exception('Excepcion detectada, no se encontro monto de material y mano de obra!!');
                    }
                    if($infoItem['flg_tipo'] == 1){//BUCLES
                        if($arrayInfo['tipo_edificio'] == 1){ //OVERLAY
                            $montoMaterial = 1577.66;
                            $montoMaxMoOC = 2392.32;
                            if($arrayValida['material'] <=	$montoMaterial && $arrayValida['mo_nopqt'] <= $montoMaxMoOC){
                                $be_validate = true;
                            }

                        }else{// NUEVO
                            $montoMaterial = 2521.07;
                            $montoMaxTotalMO = 4736.73;									
                            if($arrayValida['material'] <=	$montoMaterial && $arrayValida['manobra'] <= $montoMaxTotalMO){
                                $be_validate = true;
                            }
                        }
                    }else if($infoItem['flg_tipo'] == 2){//INTEGRALES
                        if($infoItem['tipo_edificio'] == 1){ //OVERLAY
                            $montoMaterial = 1577.66;
                            $montoMaxMOFO = 2422.91;
                            $montoMaxMoOC = 2392.32;
                            if($arrayValida['material'] <=	$montoMaterial && $arrayValida['mo_pqt'] <= $montoMaxMOFO &&  $arrayValida['mo_nopqt'] <= $montoMaxMoOC){
                                $be_validate = true;
                            }
                        }else{// NUEVO
                            $montoMaterial = 2521.07;
                            $montoMaxTotalMO = 4736.73;
                            if($arrayValida['material'] <=	$montoMaterial && $arrayValida['manobra'] <= $montoMaxTotalMO){
                                $be_validate = true;
                            }
                        }
                    }

                    if($be_validate){
                        $dataValidaNivel1 = $this->validarPartidasNivel1Forzado($itemplan,ID_USUARIO_VALIDADOR_EDIFICIOS_AUTOMATICO);
                        if($dataValidaNivel1['error'] == EXIT_ERROR){
                            throw new Exception($dataValidaNivel1['msj']);
                        }
                        $dataValidaNivel2 = $this->validarPropuestaNivel2_forzado($itemplan,$idEstacion,ID_USUARIO_VALIDADOR_EDIFICIOS_AUTOMATICO);
                        if($dataValidaNivel2['error'] == EXIT_ERROR){
                            throw new Exception($dataValidaNivel2['msj']);
                        }
                        /*
                        $dataValidaNivel2 = $this->validarPropuestaNivel2_forzado($itemplan,$idEstacion,ID_USUARIO_VALIDADOR_EDIFICIOS_AUTOMATICO);
                        if($dataValidaNivel2['error'] == EXIT_ERROR){
                            throw new Exception($dataValidaNivel2['msj']);
                        }                        
                        $data['error']  = $dataValidaNivel2['error'];
                        $data['msj'] = $dataValidaNivel2['msj'];
                        */
                        $data['error']  = $dataValidaNivel1['error'];
                        $data['msj'] = $dataValidaNivel1['msj'];
                    }
                }
            }

            if($data['error'] == EXIT_SUCCESS) {
                $this->db->trans_commit();
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    //validacion nivel 1
	function validarPartidasNivel1Forzado($itemplan, $id_usuario_tdp) {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
           
            $arraySoli =  $this->m_detalle_consulta->getIdSolicitudByItemplan($itemplan);
			if($arraySoli == null){
				throw new Exception('Hubo un error al traer la solicitud!!');
			}
            $fechaActual = $this->m_utils->fechaActual();

			$dataUpdate = array (
				'estado' => 1,
				'usua_val_nivel_1' => $id_usuario_tdp,
				'fec_val_nivel_1' => $fechaActual,
				'id_solicitud'  =>  $arraySoli['id_solicitud']
			);
			$reponseUpdate = $this->m_detalle_consulta->validateNivel1($dataUpdate, $arraySoli['id_solicitud']);
			if($reponseUpdate['error'] == EXIT_ERROR){
				throw new Exception($reponseUpdate['msj']);
			}
			$data['error'] = $reponseUpdate['error'];
			$data['msj'] = $reponseUpdate['msj'];

        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    public function validarPropuestaNivel2_forzado($itemplan,$idEstacion,$usuario_valida_tdp){
		$data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $idUsuario = $usuario_valida_tdp; //18 alonso cornelio, 32 : solange, 15 david lopez
            $fechaActual = $this->m_utils->fechaActual();
            if ($idUsuario != null) {
                // $idEstacion = 5; #idEstacion campo a enviar
                $arraySolicitud = array();
                $arrayItemXSolicitud = array();
                $infoItemplan = $this->m_utils->getPlanObraByItemplan($itemplan);
            
                $infoCreateSol = $this->m_detalle_consulta->getInfoSolCreacionByItem($itemplan); //getinfo solicitud de creacion
                
                if ($infoCreateSol == null) { //si no tiene sol creacion atendida realizamos lo siguiente.
                    $infoPoPqt = $this->m_detalle_consulta->getInfoPoMoPqtLiquidadoByItemplan($itemplan, $idEstacion); //po paquetizada
                    if ($infoPoPqt == null) {
                        throw new Exception('No se pudo obtener el codigo po PQT de la obra.');
                    }
                    $arrayPoUpdate = array();
                    $arrayPoInserLogPo = array();
                    $listaPosPdtValidar = array();
                    if ($idEstacion == ID_ESTACION_FO) {
                        $listaPosPdtValidar = $this->m_detalle_consulta->getPOToValidateToFOByItemplan($itemplan);
                    } else if ($idEstacion == ID_ESTACION_COAX) {
                        $listaPosPdtValidar = $this->m_detalle_consulta->getPOToValidateToCOAXByItemplan($itemplan);
                    }
                    if ($listaPosPdtValidar != null) {
                        foreach ($listaPosPdtValidar as $po_val) {
                            $dataLogPO = array(
                                'codigo_po' => $po_val->codigo_po,
                                'itemplan' => $itemplan,
                                'idUsuario' => $idUsuario,
                                'fecha_registro' => $fechaActual,
                                'idPoestado' => ID_ESTADO_PO_VALIDADO,
                                'controlador' => 'VALIDACION PQT 2D NIVEL',
                            );
                            array_push($arrayPoInserLogPo, $dataLogPO);
                            $dataUpdatePo = array(
                                'codigo_po' => $po_val->codigo_po,
                                'itemplan' => $po_val->itemplan,
                                'estado_po' => ID_ESTADO_PO_VALIDADO,
                            );
                            array_push($arrayPoUpdate, $dataUpdatePo);
                        }
                    }
                    $dataUpdateSolicitud = array(
                        'estado' => 2,
                        'usua_val_nivel_2' => $idUsuario,
                        'fec_val_nivel_2' => $fechaActual,
                        'itemplan' => $itemplan,
                        'idEstacion' => $idEstacion,
                    );

                    $dataExpediente = array(
                        'estado_final' => 'FINALIZADO',
                        'fecha_valida' => $fechaActual,
                        'usuario_valida' => $idUsuario
                    );

                    $data = $this->m_detalle_consulta->validarEstacionFOPqt2NivelsINoc($arrayPoInserLogPo, $itemplan, $arrayPoUpdate, $idEstacion, $dataUpdateSolicitud, $dataExpediente);

                } else { //SI YA CUENTA CON OC DE CREACION

                    $infoCertiEdicionOC = $this->m_detalle_consulta->getDataToSolicitudEdicionCertiOC($itemplan); //costos mo
                    if ($infoCertiEdicionOC == null) {
                        throw new Exception('No se pudo obtener los costos de MO para la obra.');
                    }
                    $infoPoPqt = $this->m_detalle_consulta->getInfoPoMoPqtLiquidadoByItemplan($itemplan, $idEstacion); //po paquetizada
                    if ($infoPoPqt == null) {
                        throw new Exception('No se pudo obtener el codigo po PQT de la obra.');
                    }
                    //sol edicion
                    $codigo_solicitud = $this->m_utils->getNextCodSolicitud(); //nuevo cod solicitud
                    if ($codigo_solicitud == null) {
                        throw new Exception('No se pudo obtener el codigo de Solicitud refresque la pantalla y vuelva a intentarlo.');
                    }

                    $solicitud_oc_edi_certi = array(
                        'codigo_solicitud' => $codigo_solicitud,
                        'idEmpresaColab' => $infoCreateSol['idEmpresaColab'],
                        'estado' => 1, //pendiente
                        'fecha_creacion' => $fechaActual,
                        'idSubProyecto' => $infoCreateSol['idSubProyecto'],
                        'plan' => $infoCreateSol['plan'],
                        'cesta' => $infoCreateSol['cesta'],
                        'orden_compra' => $infoCreateSol['orden_compra'],
                        'estatus_solicitud' => 'NUEVO',
                        'tipo_solicitud' => 2, //tipo edicion
						'usuario_creacion'  =>  $idUsuario
                    );
                    array_push($arraySolicitud, $solicitud_oc_edi_certi);

                    $item_x_sol = array(
                        'itemplan' => $itemplan,
                        'codigo_solicitud_oc' => $codigo_solicitud,
                        'costo_unitario_mo' => $infoCertiEdicionOC['total'],
                        'posicion' => $infoCreateSol['posicion'],
                    );

                    array_push($arrayItemXSolicitud, $item_x_sol);
                    //sol certificacion
                    $codigo_solicitud_2 = $this->m_utils->getNextCodSolicitud(); //nuevo cod solicitud
                    if ($codigo_solicitud_2 == null) {
                        throw new Exception('No se pudo obtener el codigo de Solicitud refresque la pantalla y vuelva a intentarlo.');
                    }

                    $solicitud_oc_edi_certi_2 = array(
                        'codigo_solicitud' => $codigo_solicitud_2,
                        'idEmpresaColab' => $infoCreateSol['idEmpresaColab'],
                        'estado' => 4, //pendiente
                        'fecha_creacion' => $fechaActual,
                        'idSubProyecto' => $infoCreateSol['idSubProyecto'],
                        'plan' => $infoCreateSol['plan'],
                        'cesta' => $infoCreateSol['cesta'],
                        'orden_compra' => $infoCreateSol['orden_compra'],
                        'estatus_solicitud' => 'NUEVO',
                        'tipo_solicitud' => 3, //tipo certificacion
						'usuario_creacion'  =>  $idUsuario
                    );
                    array_push($arraySolicitud, $solicitud_oc_edi_certi_2);

                    $item_x_sol_2 = array(
                        'itemplan' => $itemplan,
                        'codigo_solicitud_oc' => $codigo_solicitud_2,
                        'costo_unitario_mo' => $infoCertiEdicionOC['total'],
                        'posicion' => $infoCreateSol['posicion'],
                    );
                    array_push($arrayItemXSolicitud, $item_x_sol_2);


                    if ($infoCreateSol['idEstadoPlan'] == ID_ESTADO_PLAN_TERMINADO) { //pasar a en certificacion
                        $updatePlanObra = array(
                            'idEstadoPlan' => ID_ESTADO_PLAN_EN_CERTIFICACION,
                            'idUsuarioLog' => $idUsuario,
                            'fechaLog' => $fechaActual,
                            'descripcion' => 'VALIDACION PQT 2D NIVEL',
                            'solicitud_oc_certi' => $codigo_solicitud_2,
                            'costo_unitario_mo_certi' => $infoCertiEdicionOC['total'],
                            'estado_oc_certi' => 'PENDIENTE DE EDICION',
                            'solicitud_oc_dev' => $codigo_solicitud,
                            'costo_devolucion' => $infoCertiEdicionOC['total'],
                            'estado_oc_dev' => 'PENDIENTE'
                        );
                    } else {
                        $updatePlanObra = array(
                            'solicitud_oc_certi' => $codigo_solicitud_2,
                            'costo_unitario_mo_certi' => $infoCertiEdicionOC['total'],
                            'estado_oc_certi' => 'PENDIENTE DE EDICION',
                            'solicitud_oc_dev' => $codigo_solicitud,
                            'costo_devolucion' => $infoCertiEdicionOC['total'],
                            'estado_oc_dev' => 'PENDIENTE');
                    }

                    $arrayPoUpdate = array();
                    $arrayPoInserLogPo = array();
                    $listaPosPdtValidar = array();
                    if ($idEstacion == ID_ESTACION_FO) {
                        $listaPosPdtValidar = $this->m_detalle_consulta->getPOToValidateToFOByItemplan($itemplan);
                    } else if ($idEstacion == ID_ESTACION_COAX) {
                        $listaPosPdtValidar = $this->m_detalle_consulta->getPOToValidateToCOAXByItemplan($itemplan);
                    }
                    if ($listaPosPdtValidar != null) {
                        foreach ($listaPosPdtValidar as $po_val) {
                            $dataLogPO = array(
                                'codigo_po' => $po_val->codigo_po,
                                'itemplan' => $itemplan,
                                'idUsuario' => $idUsuario,
                                'fecha_registro' => $fechaActual,
                                'idPoestado' => ID_ESTADO_PO_VALIDADO,
                                'controlador' => 'VALIDACION PQT 2D NIVEL',
                            );
                            array_push($arrayPoInserLogPo, $dataLogPO);
                            $dataUpdatePo = array(
                                'codigo_po' => $po_val->codigo_po,
                                'itemplan' => $po_val->itemplan,
                                'estado_po' => ID_ESTADO_PO_VALIDADO,
                            );
                            array_push($arrayPoUpdate, $dataUpdatePo);
                        }
                    }
                    $dataUpdateSolicitud = array(
                        'estado' => 2,
                        'usua_val_nivel_2' => $idUsuario,
                        'fec_val_nivel_2' => $fechaActual,
                        'itemplan' => $itemplan,
                        'idEstacion' => $idEstacion,
                    );

                    $dataExpediente = array(
                        'estado_final' => 'FINALIZADO',
                        'fecha_valida' => $fechaActual,
                        'usuario_valida' => $idUsuario
                    );
                    $data = $this->m_detalle_consulta->validarEstacionFOPqt2Nivel($arraySolicitud, $arrayItemXSolicitud, $updatePlanObra, $itemplan, $arrayPoInserLogPo, $arrayPoUpdate, $idEstacion, $dataUpdateSolicitud, $dataExpediente);
                }
            } else {
                throw new Exception('Su sesion expiro, porfavor vuelva a logearse.');
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getContenidoEnCertificacion($itemplan) {
        $html = ' <div id="contTablaSolicitudOc" class="panel-content table-responsive">
                '.$this->getTablaSolicitudOc($itemplan, null).'
                </div>';
        return $html;
    }

    function getTablaSolicitudOc($itemplan, $codigoSolicitud) {
        $arrayPlanobra = $this->m_bandeja_solicitud_oc->getSolicitudOc($itemplan, $codigoSolicitud);
        $html = '<table id="tbSolicitudOc" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>Solicitud</th> 
							<th>Transaccion</th> 
							<th>Proyecto</th>	
							<th>Subproyecto</th>
                            <th>EECC</th>
							<th>#Itemplan</th>
							<th>Costo Total</th>
							<th>Costo Sap</th>							
                            <th>Plan</th>
							<th>Codigo Inversion</th>
							<th>Fecha creacion</th>			
                            <th>Usua Valida</th>
                            <th>Fecha Valida</th>
							<th>Cesta</th>	
							<th>Orden Compra</th>
							<th>Cod. Certificacion</th>							
                            <th>Estado</th>
							<th>Fecha Cancelado</th>
							<th>Situacion</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayPlanobra as $row) {

                        $html .= ' <tr>
                                        <td>'.$row['codigo_solicitud'].'</td>
                                        <td>'.$row['tipoSolicitud'].'</td>  
                                        <td>'.$row['proyectoDesc'].'</td>							
                                        <td>'.$row['subProyectoDesc'].'</td>
                                        <td>'.$row['empresaColabDesc'].'</td>
                                        <td>'.$row['numItemplan'].'</td>
                                        <td>'.$row['costo_total'].'</td>
                                        <td>'.$row['costo_sap'].'</td>
                                        <td>'.$row['plan'].'</td>
                                        <td>'.$row['codigoInversion'].'</td>
                                        <td>'.$row['fecha_creacion'].'</td>
                                        <td>'.$row['nombreCompleto'].'</td>
                                        <td>'.$row['fecha_valida'].'</td>
                                        <td>'.$row['cesta'].'</td>
                                        <td>'.$row['orden_compra'].'</td>
                                        <td>'.$row['codigo_certificacion'].'</td>
                                        <td>'.$row['estado_sol'].'</td>
                                        <td>'.$row['fecha_cancelacion'].'</td>
                                        <td>'.$row['estatus_solicitud'].'</td>
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    /** nuevo detalle Obra */
 

    function getHTMLTablaLog($itemplan) {
        $arrayLog = $this->m_utils->getLogPlanobra($itemplan);
        $html = '<table id="tb_log_itemplan" class="table table-bordered table-hover table-striped table-sm">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ESTADO</th>
							<th>FECHA</th>
                            <th>USUARIO</th>
							<th>MOTIVO</th>
                            <th>COMENTARIO</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                    
        foreach($arrayLog as $row){            
            $html .=' <tr>                          
                        <td>'.$row['estado_prev_ip'].'</td>			
                        <td>'.$row['fecha_upd'].'</td>				
                        <td>'.$row['usuario'].'</td>
                        <td>'.''.'</td>
                        <td>'.$row['comentario'].'</td>
                    </tr>';
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }
	
	function getHTMLTablaLogExpe($itemplan) {
        $arrayLog = $this->m_utils->getLogExpedientes($itemplan);
        $html = '<table id="tb_log_expediente" class="table table-bordered table-hover table-striped table-sm">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>FEC REG</th>
							<th>USUA REG.</th>
                            <th>ESTADO EXPE</th>
							<th>COMENTARIO</th>
                            <th>FEC VAL NIVEL 1</th>
                            <th>USUA VAL NIVEL 1</th>
                            <th>FEC VAL NIVEL 2</th>
                            <th>USUA VAL NIVEL 2</th>
                            <th>EXPEDIENTE</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                    
        foreach($arrayLog as $row){            
            $html .=' <tr>                          
                        <td>'.$row['fec_registro'].'</td>			
                        <td>'.$row['usua_reg_expediente'].'</td>				
                        <td>'.$row['estado_expe'].'</td>
                        <td>'.$row['comentario'].'</td>
                        <td>'.$row['fec_val_nivel_1'].'</td>
                        <td>'.$row['usua_val_niv1'].'</td>			
                        <td>'.$row['fec_val_nivel_2'].'</td>				
                        <td>'.$row['usua_val_niv2'].'</td>
                        <td>
                            <a class="btn btn-xs btn-outline-primary btn-inline-block mr-1" title="Descargar Evidencia" href="'.$row['path_expediente'].'">
                                DESCARGAR <i class="fal fa-download"></i> 
                            </a>
                        </td>
                    </tr>';
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }
	
    function getTabEstacionPo($itemplan) {
		$dataSubEstacion = $this->m_utils->getSubProyectoEstaciosByItemplan($itemplan);
		$dataInfoIP =  $this->m_utils->getPlanObraByItemplan($itemplan);
		$tab = null;
		$tabContent = null;
		$tab = '<div class="col-auto">
					<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
					//show active
		$cont = 0;
        $idEstadoPlanIn = $this->m_utils->getEstadoPlanByItemplan($itemplan);
        //log_message('error', print_r($dataSubEstacion, true));
		foreach($dataSubEstacion as $row) {
			$active = null;
			if($cont == 0) {
				$active = 'active';
			}
            $num_po = $this->m_utils->numPoByItemplanEstacion($itemplan, $row['idEstacion']);
			$tab .= '   <a class="nav-link '.$active.'" id="atab_'.$row['idEstacion'].'"  data-toggle="pill" href="#tab_'.$row['idEstacion'].'" role="tab" aria-controls="#tab_'.$row['idEstacion'].'" aria-selected="true">     
							<span class="hidden-sm-down ml-1"><i class="fal fa-chevron-right"> '.$row['estacionDesc'].' ('.$num_po.')</i></span>
						</a>';

			$tabContent .= ' <div class="tab-pane fade show '.$active.'" id="tab_'.$row['idEstacion'].'" role="tabpanel" aria-labelledby="atab_'.$row['idEstacion'].'">
								<h3>
									'.$row['estacionDesc'].'
								</h3>
								<div class="form-group">
									<div class="row col-md-12">';
											$arrayTipoArea = explode(',', $row['arrayTipoArea']);
											$arrayIdArea = explode(',', $row['arrayIdArea']);
											$arrayArea = explode(',', $row['arrayAreaDesc']);
                                            $areaDetN1 = explode(',', $row['arrayAreaDet']);
											$ctn = 0;

                                            foreach($areaDetN1 as $row3) { 
                                                $areaDetN2 = explode('|', $row3);//POS 0 ID AREA - POS 1 AREA DESC
										
												$urlPo = '';
                                         
                                                if(str_contains($areaDetN2[1],'MAT')){
                                                    $urlPo = 'href="regIndiPOMat?itemplan='.$itemplan.'&estacion='.$row['idEstacion'].'&estacionDesc='.$row['estacionDesc'].'" target="_blank" title="Registrar PO Mat Manual"';
                                                    if($dataInfoIP['idProyecto']    ==  21){
                                                        if(in_array($idEstadoPlanIn,array(ID_ESTADO_PLAN_TERMINADO, ID_ESTADO_PLAN_SUSPENDIDO, ID_ESTADO_PLAN_PRE_LIQUIDADO, ID_ESTADO_PLAN_EN_CERTIFICACION, ID_ESTADO_PLAN_CERTIFICADO))){//si ya esta en estos estados no pueden crear po mat
                                                            $urlPo  =   '';
                                                        }
                                                    }else{
                                                        if(in_array($idEstadoPlanIn,array(ID_ESTADO_PLAN_PRE_REGISTRO, ID_ESTADO_PLAN_TERMINADO, ID_ESTADO_PLAN_SUSPENDIDO, ID_ESTADO_PLAN_PRE_LIQUIDADO, ID_ESTADO_PLAN_EN_CERTIFICACION, ID_ESTADO_PLAN_CERTIFICADO))){//si ya esta en estos estados no pueden crear po mat
                                                            $urlPo  =   '';
                                                        }
                                                    }
                                                }else if(str_contains($areaDetN2[1],'MO')){
                                                    $urlPo = 'href="regPOMo?itemplan='.$itemplan.'&estacion='.$row['idEstacion'].'&estacionDesc='.$row['estacionDesc'].'" target="_blank" title="Registrar PO MO Manual"';
                                                    if(in_array($idEstadoPlanIn,array(ID_ESTADO_PLAN_PRE_REGISTRO, ID_ESTADO_PLAN_TERMINADO, ID_ESTADO_PLAN_SUSPENDIDO, ID_ESTADO_PLAN_PRE_LIQUIDADO, ID_ESTADO_PLAN_EN_CERTIFICACION, ID_ESTADO_PLAN_CERTIFICADO))){//si ya esta en estos estados no pueden crear po mat
                                                        $item_exception = array('P-23-5213260635','P-23-5448711292','P-23-5478781824','P-23-5480086437','P-23-5494014225');
														if(!in_array($itemplan, $item_exception)){
															$urlPo  =   '';
														}		
                                                    }
													if($dataInfoIP['idProyecto']    ==  52){
														if($row['idEstacion']==5){
															$urlPo  =   '';
														}
													}
                                                }else if(str_contains($areaDetN2[1],'DISEÑO_FO') && $dataInfoIP['idSubProyecto'] == 737 && $num_po == 0){
                                                    if($idEstadoPlanIn  ==  2){
                                                        $urlPo = 'href="regPOMo?itemplan='.$itemplan.'&estacion='.$row['idEstacion'].'&estacionDesc='.$row['estacionDesc'].'" target="_blank" title="Registrar PO MO Manual"';
                                                    }else{
                                                        $urlPo  =   '';
                                                    }                                                 
                                                }
												$htmlBody = $this->getHTMLPO($itemplan, $row['idEstacion'], $areaDetN2[0], $areaDetN2[1], $dataInfoIP, $row['porcentaje']);
                                                //finalmente
												$tabContent .= '<div class="card border col-md-5" style="margin: 10px;">
																	<div class="card-header">
																		<a '.$urlPo.' aria-expanded="true">
																			'.$areaDetN2[1].'
																		</a>
																	</div>
																	<div class="card-body">
																		'.$htmlBody.'           
																	</div>
																</div>';
												$ctn++;
											}
                                        
				$tabContent .=      '</div>
								</div>
							</div>';
			$cont++;
		}
		$tab .= '    </div>
				</div>
				<div class="col">
					<div class="tab-content" id="v-pills-tabContent">
						'.$tabContent.'
					</div>
				</div>';
		return $tab;
	}

	function getHTMLPO($itemplan, $idEstacion, $idArea, $areaDesc, $dataInfoIP, $porcentaje) {

		$dataPO = $this->m_utils->getArrayPOByFiltros($itemplan, $idEstacion, $idArea);
        $has_expe_rechazado = $this->m_utils->haveSolictudRechazadoExpediente($itemplan);
		$html = null;
		$tabContent = null;
		
		$html = '<div class="accordion" id="js_demo_accordion-'.$areaDesc.'">
					<div class="card">
						<div class="card-header">
							<a href="javascript:void(0);" class="card-title px-3 py-2 bg-success-600 text-white collapsed" data-toggle="collapse" data-target="#js_demo_accordion-'.$areaDesc.'1" aria-expanded="false">
								Bloodworks
								<span class="ml-auto">
									<span class="collapsed-reveal">
										<i class="fal fa-minus fs-xl"></i>
									</span>
									<span class="collapsed-hidden">
										<i class="fal fa-plus fs-xl"></i>
									</span>
								</span>
							</a>
						</div>
						<div id="js_demo_accordion-'.$areaDesc.'1" class="collapse" data-parent="#js_demo_accordion-'.$areaDesc.'" style="">
							<div class="card-body bg-success-50 p-3">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<a href="javascript:void(0);" class="card-title px-3 py-2 collapsed bg-warning-500 text-dark" data-toggle="collapse" data-target="#js_demo_accordion-'.$areaDesc.'b" aria-expanded="false">
								Xray reports
								<span class="ml-auto">
									<span class="collapsed-reveal">
										<i class="fal fa-minus fs-xl"></i>
									</span>
									<span class="collapsed-hidden">
										<i class="fal fa-plus fs-xl"></i>
									</span>
								</span>
							</a>
						</div>
						<div id="js_demo_accordion-'.$areaDesc.'b" class="collapse" data-parent="#js_demo_accordion-'.$areaDesc.'">
							<div class="card-body bg-warning-50 p-3">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<a href="javascript:void(0);" class="card-title px-3 py-2 collapsed bg-info-700 text-white" data-toggle="collapse" data-target="#js_demo_accordion-'.$areaDesc.'c" aria-expanded="false">
								ECG
								<span class="ml-auto">
									<span class="collapsed-reveal">
										<i class="fal fa-minus fs-xl"></i>
									</span>
									<span class="collapsed-hidden">
										<i class="fal fa-plus fs-xl"></i>
									</span>
								</span>
							</a>
						</div>
						<div id="js_demo_accordion-'.$areaDesc.'c" class="collapse" data-parent="#js_demo_accordion-'.$areaDesc.'">
							<div class="card-body bg-info-50 p-3">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
				</div>';

		$html = '<div class="accordion" id="accordion-'.$areaDesc.'">';
		$cont = 0;
		$contExtraButtons = '';
		foreach($dataPO as $row) {
			$contExtraButtons = '';
			if($row['flg_tipo_area'] == 1){//MAT
				if(in_array($row['estado_po'],array(3)) && $row['isPoPqt'] != 1){
                    $contExtraButtons .= '<button type="button" class="btn btn-danger ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" data-itemplan="'.$itemplan.'" data-estacion="'.$idEstacion.'"
                                                onclick="openModalMotivoPreCancelacion(this)" id="btnCance_'.$row['codigo_po'].'">
                                                <span class="fal fa-ban mr-1"></span>
                                                 
                                            </button>';
                }
			}else{//MO
				if($dataInfoIP['idTipoPlanta'] == ID_TIPO_PLANTA_INTERNA) {
					if($dataInfoIP['idEstadoPlan'] == ID_ESTADO_PLAN_EN_OBRA){
						$contExtraButtons .= '<button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" data-itemplan="'.$itemplan.'" data-estacion="'.$idEstacion.'"
												onclick="editarPO(this)" id="btnDet_'.$row['codigo_po'].'">
												<span class="fal fa-edit mr-1"></span>
												 
											</button>';
					}
				}else if($dataInfoIP['idTipoPlanta'] == ID_TIPO_PLANTA_EXTERNA) {
					if($row['isPoPqt'] == 1){
						if((in_array($row['estado_po'],array(ID_ESTADO_PO_REGISTRADO)) && $dataInfoIP['idProyecto']  ==  21) || (in_array($itemplan, array('P-23-2140012617','P-23-2130731183','P-23-2131366456')) && $row['estado_po'] == ID_ESTADO_PO_LIQUIDADO) || ($porcentaje	==	'100'   && $row['estado_po'] == ID_ESTADO_PO_LIQUIDADO && $has_expe_rechazado > 0)){//SOLO CV PUEDE EDITAR UNA PO PQT
							$contExtraButtons .= '<button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" data-itemplan="'.$itemplan.'" data-estacion="'.$idEstacion.'"
													onclick="editarPartidaAdicPqt(this)" id="btnDet_'.$row['codigo_po'].'">
													<span class="fal fa-edit mr-1"></span>												 
												</button>';
						}
					}else if((in_array($row['estado_po'],array(ID_ESTADO_PO_REGISTRADO)) && $porcentaje	==	'100')   ||  ($porcentaje	==	'100'   && $row['estado_po'] == ID_ESTADO_PO_LIQUIDADO && $has_expe_rechazado > 0)){
                        $contExtraButtons .= '<a href="liquiMo?item='.$itemplan.'&from=1&estaciondesc='.$areaDesc.'&estacion='.$idEstacion.'&poCod='.$row['codigo_po'].'" type="button" class="btn btn-primary ml-auto waves-effect waves-themed">Liquidar PO MO</a>';

                                            
                    }
				}
			}
			
			if(in_array($row['estado_po'],array(ID_ESTADO_PO_REGISTRADO,ID_ESTADO_PO_PRE_APROBADO)) && $row['isPoPqt'] != 1){
				$contExtraButtons .= '<button type="button" class="btn btn-danger ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" data-itemplan="'.$itemplan.'" data-estacion="'.$idEstacion.'"
											onclick="cancelarPO(this)" id="btnCance_'.$row['codigo_po'].'">
											<span class="fal fa-ban mr-1"></span>
											 
										</button>';
			}
			
			
			$html .= '	<div class="card">
							<div class="card-header">
								<a href="javascript:void(0);" class="card-title px-3 py-2 collapsed text-white" data-toggle="collapse" data-target="#accordion-'.$areaDesc.$cont.'" aria-expanded="false" style="background-color: '.$row['color_po'].';">
									'.$row['codigo_po'].' - '.$row['estadoDesc'].'
									<span class="ml-auto">
										<span class="collapsed-reveal">
											<i class="fal fa-minus fs-xl"></i>
										</span>
										<span class="collapsed-hidden">
											<i class="fal fa-plus fs-xl"></i>
										</span>
									</span>
								</a>
							</div>
							<div id="accordion-'.$areaDesc.$cont.'" class="collapse" data-parent="#accordion-'.$areaDesc.'">
								<div class="card-body p-3" style="background-color: '.$row['contraste_color'].';">
									<div class="form-row">
										<div class="col-md-6 mb-3">
											<a>EECC :</a>
											<br>
											<a>'.$row['empresaColabDesc'].'</a>
										</div>
										<div class="col-md-6 mb-3">
											<a>ESTADO :</a>
											<br>
											<a>'.$row['estadoDesc'].'</a>
										</div>
										<div class="col-md-6 mb-3">
											<a>VR</a>
											<br>
											<a>'.$row['vale_reserva'].'</a>
										</div>
										<div class="col-md-6 mb-3">
											<a>MONTO :</a>
											<br>
											<a>'.number_format($row['costo_total'],2).'</a>
										</div>
									</div>
									<div class="card-footer text-muted py-2" style="text-align: center;">
										<div class="btn-group">
											<button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" onclick="verDetallePO(this)" id="btnDet_'.$row['codigo_po'].'">
												<span class="fal fa-eye mr-1"></span>
												 
											</button>
											'.$contExtraButtons.'
										</div>
									</div>
								</div>
							</div>
						</div>';
			$cont++;
		}
		$html .= '</div>';
		return $html;
	}

    public function downloadLiquiEsta() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $filename = $this->input->post('itemPlan');
            $estacion = $this->input->post('estacion');
            $path = 'uploads/evidencia_liquidacion/' . $filename . '/'. str_replace('_',' ',$estacion).'/';
            log_message('error', $path);
            log_message('error', file_exists($path));
            if (file_exists($path)) {
                $data['path'] = 1;
            } else {
                $data['path'] = 2;
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
   
    public function liquidacion_download_estacion() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $estacion = (isset($_GET['estacion']) ? $_GET['estacion'] : '');
        $path = 'uploads/evidencia_liquidacion/' . $filename . '/' .str_replace('_',' ',$estacion). '/';
        $this->zip->read_dir($path, false);
        $this->zip->download($filename . '.zip');
    }

    public function liquidacionByEsta() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $filename = $this->input->post('itemPlan');
            $path = 'uploads/evidencia_liquidacion/' . $filename . '/';
            log_message('error', file_exists($path));
            if (file_exists($path)) {
                $data['path'] = 1;
            } else {
                $data['path'] = 2;
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function liquidacion_download_full_esta() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $path = 'uploads/evidencia_liquidacion/' . $filename . '/';
        $this->zip->read_dir($path, false);
        $this->zip->download($filename . '.zip');
    }

    function getTHtmlEstacionesPIN2($itemplan) {
        $dataSubEstacion = $this->m_utils->getSubProyectoEstaciosByItemplan($itemplan);        
        $tab = null;
        $tabContent = null;
        $tab = '<div class="col-auto">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
                    //show active
        $cont = 0;
        foreach($dataSubEstacion as $row) {
            $active = null;
            if($cont == 0) {
                $active = 'active';
            }
            $tab .= '   <a class="nav-link '.$active.'" id="atab_'.$row['idEstacion'].'"  data-toggle="pill" href="#tab_'.$row['idEstacion'].'" role="tab" aria-controls="#tab_'.$row['idEstacion'].'" aria-selected="true">     
                            <span class="hidden-sm-down ml-1">'.$row['estacionDesc'].'</span>
                        </a>';

            $tabContent .= ' <div class="tab-pane fade show '.$active.'" id="tab_'.$row['idEstacion'].'" role="tabpanel" aria-labelledby="atab_'.$row['idEstacion'].'">
                                <h3>
                                    '.$row['estacionDesc'].'
                                </h3>
                                <div class="form-group">
                                    <label class="form-label" for="inputGroupFile01">Subir Evidencia</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="evidencia" aria-describedby="evidencia4" onclick="getEstacion('.$row['idEstacion'].');">
                                            <label class="custom-file-label" for="evidencia"></label>
                                        </div>
                                    </div>
                                </div>
                                <button id="btnProcesar" type="button" class="btn btn-primary waves-effect waves-themed" onclick="liquidarObra();">Guardar</button>
                            </div>';
            $cont++;
        }
        $tab .= '    </div>
                </div>
                <div class="col">
                    <div class="tab-content" id="v-pills-tabContent">
                        '.$tabContent.'
                    </div>
                </div>';
        return $tab;
    }

    function getTHtmlEstacionesPIN($itemplan) {
        $dataSubEstacion = $this->m_utils->getSubProyectoEstaciosByItemplan($itemplan);
        $tab = null;
        
        
        foreach($dataSubEstacion as $row) {
           
            $tab .= '<div class="col-xl-6">
                        <div id="panel-'.$row['idEstacion'].'" class="panel">
                            <div class="panel-hdr">
                                <h2>'.$row['estacionDesc'].'</h2>                  
                            </div>
                            <div class="panel-container show">
                                <div class="panel-content">
                                    <div class="form-row">
                                        <div class="col-md-9 mb-9">
                                            <label class="form-label" for="inputGroupFile01">Subir Evidencia</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="evidenciaPin" onclick="getEstacion('.$row['idEstacion'].');">
                                                    <label class="custom-file-label" for="evidenciaPin"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <button id="btnProcesar" style="margin-top: 20px;" type="button" class="btn btn-primary waves-effect waves-themed" onclick="liquidarObraPin();">Guardar</button>
                                        </div>                   
                                    </div>
                                </div>                  
                            </div>
                        </div>
                    </div>';            
        }
        
        return $tab;
    }


    function getTHtmlEstacionesPINLiquidado($ruta_evidencia) {
           
            $tab = '<div class="col-xl-6">
                        <div class="panel">
                            <div class="panel-hdr">
                                <h2>PIN</h2>                  
                            </div>
                            <div class="panel-container show">
                                <div class="panel-content">
                                    <div class="form-row">
                                        <div class="col-md-12 mb-12" style="TEXT-ALIGN: center;">
                                            <a class="btn btn-sm btn-outline-primary waves-effect waves-themed" title="Descargar Evidencia" 
                                                    download href="'.$ruta_evidencia.'">DESCARGAR EVIDENCIA <i class="fal fa-download"></i>
                                                </a>
                                        </div>                   
                                    </div>
                                </div>                  
                            </div>
                        </div>
                    </div>';            
      
        
        return $tab;
    }

    function getTHtmlRechazoPINLiquidado($infoRechazo) {
           
        $tab = '<br><div class="col-xl-12">
                    <div class="panel">
                        <div class="panel-hdr">
                            <h2 style="color:red">ULTIMA LIQUIDACION RECHAZADA</h2>                  
                        </div>
                        <div class="panel-container show">
                            <div class="panel-content">
                                <div class="form-row">
                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                        <a class="text-muted mb-0">USUARIO RECHAZO: </a>
                                        <a style="color: var(--theme-primary);">'.$infoRechazo['usua_rechazo'].'</a>
                                    </div>
                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                        <a class="text-muted mb-0">FECHA RECHAZO: </a>
                                        <a style="color: var(--theme-primary);">'.$infoRechazo['fecha_rechazo'].'</a>
                                    </div>
                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                        <a class="text-muted mb-0">EVIDENCIA ENVIADA: </a>
                                        <a class="btn btn-xs btn-outline-primary btn-inline-block mr-1" title="Descargar Evidencia" download href="' . $infoRechazo['ruta_evidecia'] . '">
                                            DESCARGAR <i class="fal fa-download"></i> 
                                        </a>
                                    </div> 
                                    <div class="col-lg-6 col-xl-6 col-12 text-left py-1">
                                        <a class="text-muted mb-0">COMENTARIO: </a>
                                        <a style="color: var(--theme-primary);">'.$infoRechazo['comentario'].'</a>
                                    </div>                                                    
                                </div>
                            </div>                  
                        </div>
                    </div>
                </div>';

        return $tab;
    }

    public function getLogSeguimientoCV(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplan_hijo = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan_hijo == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }                
            $data['tbLog'] = $this->getHTMLTablaLogSeguimientoCVByHijo($itemplan_hijo);
            $data['error'] = EXIT_SUCCESS;
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function getLogSeguimientoB2b(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }                
            $data['tbLog'] = $this->getHTMLTablaLogSeguimientoB2bDet($itemplan);
            $data['error'] = EXIT_SUCCESS;
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function getLogSeguimientoReforzamiento(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }                
            $data['tbLog'] = $this->getHTMLTablaLogSeguimientoReforzamientobDet($itemplan);
            $data['error'] = EXIT_SUCCESS;
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getHTMLTablaLogSeguimientoCV($itemplan) {
        $arrayLog = $this->m_detalle_consulta->getHijosCvSeguimiento($itemplan);
        $html = '<table id="tb_log_segui_cv" class="table table-sm table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th></th>
                            <th>ITEMPLAN GEN.</th>
                            <th>ITEMPLAN HIJO</th>
                            <th>SIT. GENERAL</th>
                            <th>SIT. ESPECIFICA</th>
                            <th>NR DPTO</th>
                            <th>TIPO</th> 
                            <th>FECHA ENTREGA COMERCIAL</th> 
                            <th>COMENTARIO</th> 
                        </tr>
                    </thead>                    
                    <tbody>';
        $count = 1;                                                                   
        foreach($arrayLog as $row){
            $uploadFile = null;      
            if(in_array($row['ult_situa_especifica'], array(43,44))){
                if($row['has_quiebre_evidencia'] == null){
                    $uploadFile = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Subir Evidencia Quiebre" data-itemplan="'.$row['ip_hijo'].'" data-itemplanGen="'.$row['itemplan'].'" onclick="openModalQuiebreCV(this)">
                                        <i class="fal fa-upload"></i>
                                    </a>';
                }                
            }
            $html .='<tr>
                        <td>
                            <a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver log seguimiento" data-itemplan="'.$row['ip_hijo'].'" onclick="getLogSeguimientoCV(this)">
                                <i class="fal fa-search"></i>
                            </a>'.
                            $uploadFile.'
                        </td>
                        <td>'.$row['itemplan'].'</td>       
                        <td>'.$row['ip_hijo'].'</td>	                  
                        <td>'.$row['situacion_general'].'</td>			
                        <td>'.$row['situa_especifica'].'</td>				
                        <td>'.$row['nro_depa'].'</td>
                        <td>'.$row['tipo'].'</td>		
                        <td>'.$row['fecha_entrega_comercial'].'</td>
                        <td>'.$row['ultimo_comentario'].'</td>
                    </tr>';
            $count++;
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }

    function getHTMLTablaLogSeguimientoB2b($itemplan) {
        $arrayLog = $this->m_detalle_consulta->getHijosB2bSeguimiento($itemplan);
        $html = '<table id="tb_log_segui_cv" class="table table-sm table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th></th>
                            <th>INDICADOR</th>
                            <th>ITEMPLAN</th>
                            <th>EECC</th>
                            <th>DEPARTAMENTO</th> 
                            <th>DISTRITO</th>                             
                            <th>SIT. GENERAL</th>
                            <th>SIT. ESPECIFICA</th>
                            <th>COMENTARIO</th> 
                        </tr>
                    </thead>                    
                    <tbody>';
        $count = 1;                                                                   
        foreach($arrayLog as $row){            
            $html .='<tr>
                        <td>
                            <a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver log seguimiento" data-itemplan="'.$row['itemplan'].'" onclick="getLogSeguimientoB2b(this)">
                            <i class="fal fa-search"></i>
                            </a>
                        </td>
                        <td>'.$row['indicador'].'</td>       
                        <td>'.$row['itemplan'].'</td>	                  
                        <td>'.$row['empresaColabDesc'].'</td>	           
                        <td>'.$row['departamento'].'</td>
                        <td>'.$row['distrito'].'</td>
                        <td>'.$row['situacion_general'].'</td>			
                        <td>'.$row['situa_especifica'].'</td>	
                        <td>'.$row['ultimo_comentario'].'</td>
                    </tr>';
            $count++;
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }

    public function registrarLogSeguimientoCV(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idMotivo = $this->input->post('selectMotivoSegui') ? $this->input->post('selectMotivoSegui') : null;
            $comentario = $this->input->post('txtComentario') ? $this->input->post('txtComentario') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información el itemplan');
            }
			$estadosValidos = array(ID_ESTADO_PLAN_DISENIO,ID_ESTADO_PLAN_EN_LICENCIA, ID_ESTADO_PLAN_EN_APROBACION, ID_ESTADO_PLAN_EN_OBRA);
            if(!in_array($infoItem['idEstadoPlan'],$estadosValidos)){
                throw new Exception('El itemplan se encuentra en un estado no válido para registrar.');
            }
			
            $fechaActual = $this->m_utils->fechaActual();

            $dataInsert = array(  
                'itemplan'              =>  $itemplan,
                'idEstadoPlan'          =>  $infoItem['idEstadoPlan'],
                'usuario_registro'      =>  $idUsuario,
                'fecha_registro'        =>  $fechaActual,
                'id_motivo_seguimiento' =>  $idMotivo,
                'comentario_incidencia' =>  $comentario
            );
            $data = $this->m_agenda_cv->createSeguimientoCV($dataInsert);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();
            $data['tbLog'] = $this->getHTMLTablaLogSeguimientoCV($itemplan);
            $data['cmbMotivoSegui'] = '<option value="">Seleccione Motivo</option>'.__buildComboMotivoSeguiCV($infoItem['idEstadoPlan'],$itemplan);

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getHTMLTablaLogSeguimientoCVByHijo($itemplan_hijo) {
        $arrayLog = $this->m_utils->getLogSeguimientoCVHijo($itemplan_hijo);
        $html = '<table id="tb_log_segui_cv" class="table table-sm table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>#</th>
                            <th>IP HIJOL</th>
                            <th>SITUACION GENERAL</th>
							<th>SITUACION ESPECIFICA</th>
                            <th>USUARIO REGISTRO</th>
                            <th>FECHA REGISTRO</th>
							<th>COMENTARIO</th>        
                            <th>EVIDENCIA</th>                      
                        </tr>
                    </thead>                    
                    <tbody>';
        $count = 1;                                                                   
        foreach($arrayLog as $row){            
            $html .=' <tr>
                        <td>'.$count.'</td>        
                        <td>'.$row['itemplan_hijo'].'</td>	
                        <td>'.$row['situacion_general'].'</td>	                 
                        <td>'.$row['desc_motivo'].'</td>			
                        <td>'.$row['nombre_completo'].'</td>				
                        <td>'.$row['fecha_registro'].'</td>
                        <td>'.$row['comentario_incidencia'].'</td>';                     
                        if($row['path_file_quiebre']    ==  null){
                            $html .='<td>S/E</td>';
                        }else{
                            $html .='<td><a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Descargar Evidencia Quiebre" href="'.$row['path_file_quiebre'].'" download>
                                        <i class="fal fa-download"></i>
                                    </a></td>';
                        }                        
            $html .='</tr>';
            $count++;
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }

    function getHTMLTablaLogSeguimientoB2bDet($itemplan) {
        $arrayLog = $this->m_utils->getLogSeguimientoB2bHijo($itemplan);
        $html = '<table id="tb_log_segui_cv" class="table table-sm table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>#</th>
                            <th>IP HIJOL</th>
                            <th>SITUACION GENERAL</th>
							<th>SITUACION ESPECIFICA</th>
                            <th>USUARIO REGISTRO</th>
                            <th>FECHA REGISTRO</th>
							<th>COMENTARIO</th>                             
                        </tr>
                    </thead>                    
                    <tbody>';
        $count = 1;                                                                   
        foreach($arrayLog as $row){            
            $html .=' <tr>
                        <td>'.$count.'</td>        
                        <td>'.$row['itemplan'].'</td>	
                        <td>'.$row['situacion_general'].'</td>	                 
                        <td>'.$row['desc_motivo'].'</td>			
                        <td>'.$row['nombre_completo'].'</td>				
                        <td>'.$row['fecha_registro'].'</td>
                        <td>'.$row['comentario_incidencia'].'</td>
                    </tr>';
            $count++;
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }

    function getHTMLTablaLogSeguimientoReforzamientobDet($itemplan) {
        $arrayLog = $this->m_utils->getLogSeguimientoReforzamiento($itemplan);
        $html = '<table id="tb_log_segui_cv" class="table table-sm table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>#</th>
                            <th>ITEMPLAN</th>
                            <th>SITUACION GENERAL</th>							 
                            <th>USUARIO REGISTRO</th>
                            <th>FECHA REGISTRO</th>
							<th>COMENTARIO</th>                             
                        </tr>
                    </thead>                    
                    <tbody>';
        $count = 1;                                                                   
        foreach($arrayLog as $row){            
            $html .=' <tr>
                        <td>'.$count.'</td>        
                        <td>'.$row['itemplan'].'</td>	
                        <td>'.$row['situacion_general'].'</td>
                        <td>'.$row['nombre_completo'].'</td>				
                        <td>'.$row['fecha_registro'].'</td>
                        <td>'.$row['comentario_incidencia'].'</td>
                    </tr>';
            $count++;
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }
    
    public function sendValidatePartidasAdicionalesNoPqt()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;           
            $costoTotal = $this->input->post('costo_total') ? $this->input->post('costo_total') : null;            
            $idEstacion     =   null;
            $descEstacion   =   null;
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();

            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }
          
            if($costoTotal == null){
                throw new Exception('Hubo un error al recibir el costo total de la propuesta !!');
            }

            if($idUsuario == null){
                throw new Exception('Su sesión ha expirado, porfavor vuelva a logearse.');
            }
            if(count($_FILES) == 0){
                throw new Exception('Hubo un error al recibir la evidencia!!');
            }

            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);

            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información del itemplan!!');
            }

            $pathExpediente = "uploads/evidencia_expediente";
            if (!file_exists($pathExpediente)) {
                if (!mkdir($pathExpediente)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathExpediente.'!!');
                }
            }

            $pathItemplan = $pathExpediente."/".$itemplan;

            if (!file_exists($pathItemplan)) {
                if (!mkdir($pathItemplan)) {
                    throw new Exception('Hubo un error al crear la carpeta '.$pathItemplan.'!!');
                }
            }
           
            $nombreArchivo = $_FILES['file']['name'];
            $tipoArchivo = $_FILES['file']['type'];
            $nombreArchivoTemp = $_FILES['file']['tmp_name'];
            $tamano_archivo = $_FILES['file']['size'];
            $rutaFinalArchivo = $pathItemplan."/".$nombreArchivo;
            if (!move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {
                throw new Exception('No se pudo subir el archivo: ' . $nombreArchivoTemp . ' !!');
            }
			
			if($infoItem['idProyecto']	==	52){//TEMPORAL FTTH CZAVALA 18.07.2023
				$idEstacion	=	5;//FTTH SOLO FO
			}
            $dataSolValidacion = array(
                'fec_registro'       => $fechaActual,   
                'usua_registro'     =>  $idUsuario,
                'estado'            =>  0,
                'costo_total'       =>  $costoTotal,
                'itemplan'          =>  $itemplan,
                'idEstacion'        =>  $idEstacion,
                'costo_inicial'     =>  0,
                'costo_adicional'   =>  0,
                'activo'            =>  1                    
            );


            
            $array_expedientes = array();
         
            $data_expediente = array(
                'itemplan'     =>  $itemplan,
                'fecha'        =>  $fechaActual,
                'comentario'   =>  'VALIDACION PQT',
                'usuario'      =>  $idUsuario,
                'estado'       =>  'ACTIVO',
                'estado_final' =>  'PENDIENTE',
                'path_expediente'  => $rutaFinalArchivo,
                'idEstacion'   => $idEstacion
            );
            array_push($array_expedientes, $data_expediente);

            $data = $this->m_detalle_consulta->sendValidarPartidasAdicionalesNoPqt($itemplan, $idEstacion, $dataSolValidacion, $array_expedientes);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function getComboMotivoPreCancela()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            
            $codigoPO = $this->input->post('codigoPO') ? $this->input->post('codigoPO') : null;
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
             

            if ($idUsuario == null) {
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }        
            $estadoPO = $this->m_utils->getEstadoPO($codigoPO);
            if ($estadoPO == null) {
                throw new Exception('Hubo un error en traer el estado de la PO!!');
            }			 

            if ($estadoPO == 3) {
                $htmlCombo = $this->makeComboMotivo($this->m_utils->getMotivoAll(3));             
                $data['comboMotivo'] = $htmlCombo;
                $data['error'] = EXIT_SUCCESS;				
            }else{
                throw new Exception('El estado actual no permite una Pre cancelacion de PO!!');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
         echo json_encode(array_map('utf8_encode', $data));
    }

    public function makeComboMotivo($listaMotivos)
    {

        $html = '<option value="">Seleccionar Motivo</option>';

        foreach ($listaMotivos as $row) {

            $html .= '<option value="' . $row->idMotivo . '">' . $row->motivoDesc . '</option>';
        }       
        return utf8_decode($html);
    }

    public function preCancelarPO(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $codigoPO = $this->input->post('codigoPO') ? $this->input->post('codigoPO') : null;
            $motivo = $this->input->post('motivo') ? $this->input->post('motivo') : null;
            $observacion = $this->input->post('observacion');
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();
 
            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($itemplan == null || $codigoPO == null || $motivo == null || $observacion == null){
                throw new Exception('Hubo un error y no se cargaron los datos!!');
            }
                $poUpdate = array(
                    "estado_po" => 7,
                    'codigo_po' => $codigoPO
                );
			
                $arrayInsertLog = array(
                    "codigo_po" => $codigoPO,
                    "itemplan" => $itemplan,
                    "idUsuario" => $idUsuario,
                    "fecha_registro" => $fechaActual,
                    "idPoestado" => 7,
                    "controlador" => 'C_detalle_obra'
                );

                $arrayInsertPoCan= array(
                    "itemplan" => $itemplan,
                    "codigo_po" => $codigoPO,
                    "idMotivo" => $motivo,
                    "observacion" => $observacion,
                    "fecha_registro" => $fechaActual,
                    "id_usuario" => $idUsuario,
                    "idPoestado" => 7
                );

            $data = $this->m_detalle_consulta->preCancelarPoMat($poUpdate, $arrayInsertLog, $arrayInsertPoCan);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
	
	function getRechazadoByidSolicitud2Bucles() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
            if($this->session->userdata('idPersonaSessionPan') != null){
                $itemplan       = ($this->input->post('itemplan')=='')      ? null : $this->input->post('itemplan');
                $idEstacion     = ($this->input->post('idEstacion')=='')    ? null : $this->input->post('idEstacion');
                $data['tablaRechazado'] = $this->getTablaRechazado($this->m_detalle_consulta->getSolicitudPartidasAdicionales_2bucles($itemplan));
                $data['error'] = EXIT_SUCCESS;
            }else{
                throw new Exception('La session expiro, vuelva a iniciar Sesion.');
            }
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
	
	 function getTablaRechazado($solicitudPartAdic) {
        $html = '';
                   if($solicitudPartAdic!=null){
                       if($solicitudPartAdic['estado']==3){
                           $canCreateMarteriales = true;
                           $valEEcc = 'style="display:none"';
                           $valTDP = '';
                           $showButtons = true;
                           $html .= '<div>
                                      <table class="table table-bordered">
                                        <thead class="bg-primary-600">
                                            <tr>
                                                <th></th>
                                                <th>Usuario Rechazo</th>
                                                <th>Fecha Rechazo</th>
                                                <th>Comentario</th>
                                            <tr></thead>
                                            <tbody>
                                              <tr>
                                                <th></th>
                                                <th>'.$solicitudPartAdic['usuario_nivel_1'].'</th>
                                                <th>'.$solicitudPartAdic['fec_val_nivel_1'].'</th>
                                                <th>'.$solicitudPartAdic['comentario'].'</th>
            		                          </tr>
                                            </tbody>
                                        </table>
                                     </div>';
                       }else if($solicitudPartAdic['estado']==4){
                           $canCreateMarteriales = true;
                           $valEEcc = 'style="display:none"';
                           $valTDP = '';
                           $showButtons = true;
                           $html .= '<div>
                                      <table class="table table-bordered">
                                        <thead class="thead-default">
                                            <tr>
                                                <th></th>
                                                <th>Usuario Rechazo</th>
                                                <th>Fecha Rechazo</th>
                                                <th>Comentario</th>
                                            <tr></thead>
                                            <tbody>
                                              <tr>
                                                <th></th>
                                                <th>'.$solicitudPartAdic['usuario_nivel_2'].'</th>
                                                <th>'.$solicitudPartAdic['fec_val_nivel_2'].'</th>
                                                <th>'.$solicitudPartAdic['comentario'].'</th>
            		                          </tr>
                                            </tbody>
                                        </table>
                                     </div>';
                       }
                   }
                   return $html;
    }

    function getRechazadoByidSolicitud2BuclesOnlyrechazo() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
            if($this->session->userdata('idPersonaSessionPan') != null){
                $itemplan       = ($this->input->post('itemplan')=='')      ? null : $this->input->post('itemplan');
                $idEstacion     = ($this->input->post('idEstacion')=='')    ? null : $this->input->post('idEstacion');
                $data['tablaRechazado'] = $this->getTablaRechazadoList($this->m_detalle_consulta->getSolicitudPartidasAdicionales_2buclesOnlyRechazo($itemplan));
                $data['error'] = EXIT_SUCCESS;
            }else{
                throw new Exception('La session expiro, vuelva a iniciar Sesion.');
            }
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getTablaRechazadoList($solicitudPartAdicList) {
        $html = '';
        $canCreateMarteriales = true;
        $valEEcc = 'style="display:none"';
        $valTDP = '';
        $showButtons = true;
        $html .= '<div>
                    <table class="table table-bordered">
                    <thead class="bg-primary-600">
                        <tr>
                            <th></th>
                            <th>Usuario Rechazo</th>
                            <th>Fecha Rechazo</th>
                            <th>Comentario</th>
                        <tr></thead>
                        <tbody>';
        foreach($solicitudPartAdicList as $solicitudPartAdic){
            if($solicitudPartAdic!=null){
                if($solicitudPartAdic['estado']==3){
                $html .= '<tr>
                            <th></th>
                            <th>'.$solicitudPartAdic['usuario_nivel_1'].'</th>
                            <th>'.$solicitudPartAdic['fec_val_nivel_1'].'</th>
                            <th>'.$solicitudPartAdic['comentario'].'</th>
                            </tr>';
                }else if($solicitudPartAdic['estado']==4){
                    $html .= '<tr>
                                <th></th>
                                <th>'.$solicitudPartAdic['usuario_nivel_2'].'</th>
                                <th>'.$solicitudPartAdic['fec_val_nivel_2'].'</th>
                                <th>'.$solicitudPartAdic['comentario'].'</th>
                            </tr>';
                }
            }
        }
        $html .= '    </tbody>
                    </table>
                </div>';
        return $html;
    }

    function manualCreatePoDiseno(){
        $itemList = array('');

        foreach($itemList as $itemplan){
            //$codPartida = '10001-3'; //MEDIA COMPLEJIDAD
			$codPartida = '12000-6'; //PROYECTO GENERICO
			$infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            $cantidad  =   $infoItem['cantFactorPlanificado'];//OBRA
            $this->generarPoDisenoByCodPartidaCantidad($itemplan, $codPartida, $cantidad);
        }
    }

    function getHTMLTablaLogReforzamientoCTO($itemplan) {
        $arrayLog = $this->m_detalle_consulta->getHijosReforzamientoSeguimiento($itemplan);
        $html = '<table id="tb_log_segui_cv" class="table table-sm table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th></th>                           
                            <th>ITEMPLAN</th>
                            <th>EECC</th>     
                            <th>CTO AJUDICADO</th>
                            <th>TIPO REFORZAMIENTO</th>    
                            <th>CTO FINAL</th>                                                
                            <th>SIT. GENERAL</th>
                            <th>SIT. ESPECIFICA</th> 
                        </tr>
                    </thead>                    
                    <tbody>';
        $count = 1;                                                                   
        foreach($arrayLog as $row){            
            $html .='<tr>
                        <td>
                            <a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver log seguimiento" data-itemplan="'.$row['itemplan'].'" onclick="getLogSeguimientoReforzamiento(this)">
                                <i class="fal fa-search"></i>
                            </a>';
            if($row['id_situacion_especifica']  ==  1){
                $html .='   <a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Instalado" data-id="'.$row['id_formulario'].'" onclick="instaladoReforza(this)">
                                <i class="fal fa-check"></i>
                            </a>';
            }
            $html .='   </td>                         
                        <td>'.$row['itemplan'].'</td>	                  
                        <td>'.$row['empresaColabDesc'].'</td>	           
                        <td>'.$row['cto_ajudi'].'</td>
                        <td>'.$row['tipo_refo'].'</td>
                        <td>'.$row['do_splitter'].'</td>
                        <td>'.$row['situacion_general'].'</td>			
                        <td>'.$row['situacion_especifica'].'</td>	 
                    </tr>';
            $count++;
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }

    public function saveInstaladoReforzamiento(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $id_formulario = $this->input->post('idSeguimiento') ? $this->input->post('idSeguimiento') : null;            
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();
 
            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id_formulario == null || $id_formulario == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }
               
			
                $arrayUpdate = array(                    
                    "id_formulario" => $id_formulario,
                    "situacion_especifica" => 2,//INSTALADO
                    "fec_upd_sit_especifica" => $fechaActual,
                    "usu_upd_sit_especifica" => $idUsuario
                );              

            $data = $this->m_detalle_consulta->updSituacionSegReforzamiento($arrayUpdate);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
}