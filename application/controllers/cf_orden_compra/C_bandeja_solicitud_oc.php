<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_bandeja_solicitud_oc extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_orden_compra/m_bandeja_solicitud_oc');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, ID_BANDEJA_SOLICITUD_OC_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoBaOC($this->m_bandeja_solicitud_oc->getSolicitudOcNew(1, null,null));//1 = SOLO PENDIENTES
            //$data['tablaSolicitudOc'] = $this->getTablaSolicitudOc(NULL, NULL, array(ID_ESTADO_PLAN_DISENIO, ID_ESTADO_PLAN_PDT_OC));
            $this->load->view('vf_orden_compra/v_bandeja_solicitud_oc',$data);        	  
    	 }else{
        	redirect('login','refresh');
	    }     
    }

    public function ftth() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
        if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, ID_BANDEJA_SOLICITUD_OC_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoBaOC($this->m_bandeja_solicitud_oc->getSolicitudOcNew(1, null,null));//1 = SOLO PENDIENTES
            //$data['tablaSolicitudOc'] = $this->getTablaSolicitudOc(NULL, NULL, array(ID_ESTADO_PLAN_DISENIO, ID_ESTADO_PLAN_PDT_OC));
            $this->load->view('vf_orden_compra/v_bandeja_solicitud_oc_ftth',$data);            
         }else{
            redirect('login','refresh');
        }     
    }

    public function getArrayPoBaOC($listaSolOC){
        $listaFinal = array();       
        foreach($listaSolOC as $poMat){

            $btnValidar = null;
            $btnPdt = '';
            if($poMat['tipo_solicitud'] == TIPO_SOLICITUD_OC_CREA) {
                if($poMat['estado'] == 1) {
                    $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Solicitud" 
                                        aria-expanded="true" data-codigo_solicitud="'.$poMat['codigo_solicitud'].'"
                                        onclick="openModalAtenderSolicitud($(this));"><i class="fal fa-check"></i>
                                    </a>';
                }
            } else if($poMat['tipo_solicitud'] == TIPO_SOLICITUD_OC_CERTI) {
                if($poMat['estado'] == 1) {
                    $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Solicitud" 
                                        aria-expanded="true" data-codigo_solicitud="'.$poMat['codigo_solicitud'].'"
                                        onclick="openModalAtenderSolicitudCerti($(this));"><i class="fal fa-check"></i>
                                    </a>';
                }else if($poMat['estado'] == 5){
                    $btnPdt = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Pasar a Pdt" 
                                    aria-expanded="true" data-codigo_solicitud="'.$poMat['codigo_solicitud'].'"
                                    onclick="actualizarToPdt(this);">
                                    <i class="fal fa-sync-alt"></i>
                                </a>';
                }
            }else if($poMat['tipo_solicitud'] == TIPO_SOLICITUD_OC_EDIC) {
                if($poMat['estado'] == 1) {
                    $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Solicitud" 
                                        aria-expanded="true" data-codigo_solicitud="'.$poMat['codigo_solicitud'].'"
                                        onclick="validarEdicionOc(this);"><i class="fal fa-check"></i>
                                    </a>';
                }
            }else if($poMat['tipo_solicitud'] == TIPO_SOLICITUD_OC_ANULA) {
                if($poMat['estado'] == 1) {
                    $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Solicitud" 
                                        aria-expanded="true" data-codigo_solicitud="'.$poMat['codigo_solicitud'].'"
                                        onclick="validarAnulacionOc(this);"><i class="fal fa-check"></i>
                                    </a>';
                }
            }
            
            
            $btnDetalle =   '';
            /*
            $btnDetalle = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver Detalle" 
                            data-codigo_solicitud="'.$poMat['codigo_solicitud'].'" onclick="openModalDetalleSolicitudOc($(this));"><i class="fal fa-envelope-open-text"></i></a>';
            */

            array_push($listaFinal, array($btnDetalle.' '.$btnValidar.' '.$btnPdt,
                                            $poMat['codigo_solicitud'],$poMat['tipoSolicitud'],$poMat['proyectoDesc'],$poMat['subProyectoDesc'],$poMat['empresaColabDesc'], $poMat['itemplan'], $poMat['costo_total'], $poMat['costo_sap'], $poMat['codigoInversion']
                                            , $poMat['fecha_creacion'], $poMat['nombreCompleto'], $poMat['fecha_valida'], $poMat['cesta'], $poMat['orden_compra'], $poMat['codigo_certificacion'], $poMat['estado_sol'], $poMat['fecha_cancelacion'], $poMat['estatus_solicitud'], $poMat['P'], $poMat['Q'], $poMat['costo_total']));
        }                                                                 
        return $listaFinal;
    }

    function getTablaSolicitudOc($itemplan, $codigoSolicitud) {
        $arrayPlanobra = $this->m_bandeja_solicitud_oc->getSolicitudOc($itemplan, $codigoSolicitud);
        $html = '<table id="tbSolicitudOc" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>Acción</th>  
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
							<th>SolPed</th>	
							<th>Orden Compra</th>
							<th>Cod. Certificacion</th>							
                            <th>Estado</th>
							<th>Fecha Cancelado</th>
							<th>Situacion</th>
							<th>P</th>
                            <th>Q</th>
                            <th>P*Q</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayPlanobra as $row) {
                        $btnValidar = null;
						$btnPdt = '';
                        if($row['tipo_solicitud'] == TIPO_SOLICITUD_OC_CREA) {
                            if($row['estado'] == 1) {
                                $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Solicitud" 
                                                    aria-expanded="true" data-codigo_solicitud="'.$row['codigo_solicitud'].'"
                                                    onclick="openModalAtenderSolicitud($(this));"><i class="fal fa-check"></i>
                                                </a>';
                            }
                        } else if($row['tipo_solicitud'] == TIPO_SOLICITUD_OC_CERTI) {
                            if($row['estado'] == 1) {
                                $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Solicitud" 
                                                    aria-expanded="true" data-codigo_solicitud="'.$row['codigo_solicitud'].'"
                                                    onclick="openModalAtenderSolicitudCerti($(this));"><i class="fal fa-check"></i>
                                                </a>';
                            }else if($row['estado'] == 5){
								$btnPdt = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Pasar a Pdt" 
												aria-expanded="true" data-codigo_solicitud="'.$row['codigo_solicitud'].'"
												onclick="actualizarToPdt(this);">
												<i class="fal fa-sync-alt"></i>
											</a>';
							}
                        }else if($row['tipo_solicitud'] == TIPO_SOLICITUD_OC_EDIC) {
                            if($row['estado'] == 1) {
                                $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Solicitud" 
                                                    aria-expanded="true" data-codigo_solicitud="'.$row['codigo_solicitud'].'"
                                                    onclick="validarEdicionOc(this);"><i class="fal fa-check"></i>
                                                </a>';
                            }
                        }
                        
                        

                        $btnDetalle = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver Detalle" 
                                        data-codigo_solicitud="'.$row['codigo_solicitud'].'" onclick="openModalDetalleSolicitudOc($(this));"><i class="fal fa-envelope-open-text"></i></a>
                                        ';
                        $html .= ' <tr>
                                        <th>
                                            <div class="d-flex demo">
                                            '.$btnDetalle.' '.$btnValidar.' '.$btnPdt.'
                                            </div>           
                                        </th>
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
										<td>'.$row['P'].'</td>
                                        <td>'.$row['Q'].'</td>
                                        <td>'.$row['costo_total'].'</td>
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function getDataDetalleSolicitud() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $codigoSolicitud = $this->input->post('codigoSolicitud');

            if($codigoSolicitud == null || $codigoSolicitud == '') {
                throw new Exception('codigoSolicitud no existente, comunicarse con el programador a cargo');
            }
            $data['error']   = EXIT_SUCCESS;
            $tablaDetalleSolicitudOc = $this->getTablaDetalleSolicitudOc($codigoSolicitud);

            $data['tablaDetalleSolicitudOc'] = $tablaDetalleSolicitudOc;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getTablaDetalleSolicitudOc($codigoSolicitud) {
        $dataArrayDetalle = $this->m_bandeja_solicitud_oc->getDetalleSolicitudOc($codigoSolicitud);
        $html = '<table id="tbDetalleSolicitudOc" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ITEMPLAN</th>
                            <th>SUBPROYECTO</th> 
                            <th>NOMBRE PROYECTO</th>
                            <th>COSTO MO</th>
                            <th>COSTO MAT</th>
                            <th>SOLPED</th>
                            <th>OC</th>
                            <th>POSICION</th>
                        </tr>
                    </thead>

                    <tbody>';
            $indice = 0;
            foreach($dataArrayDetalle as $row){
            $html .='<tr>
                        <td>'.$row['itemplan'].'</td>
                        <td>'.$row['subProyectoDesc'].'</td>
                        <td>'.$row['nombrePlan'].'</td>
                        <td>'.$row['costo_mo_ix'].'</td>
                        <td>'.$row['limite_costo_mat'].'</td>	
                        <td>'.$row['cesta_sol'].'</td>
                        <td>'.$row['oc_sol'].'</td>
                        <td>'.$row['posicion_ix'].'</td>
                    </tr>';
            }


            $html .='   </tbody>
                    </table>';       

            return $html;
    }

    function getDataAtenderSolicitudOc() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $codigoSolicitud = $this->input->post('codigoSolicitud');

            if($codigoSolicitud == null || $codigoSolicitud == '') {
                throw new Exception('codigoSolicitud no existente, comunicarse con el programador a cargo');
            }
            $data['error']   = EXIT_SUCCESS;
            $tablaDetalleSolicitudOc = $this->getTablaAtenderSolicitudOc($codigoSolicitud);

            $data['tablaDetalleSolicitudOc'] = $tablaDetalleSolicitudOc;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getTablaAtenderSolicitudOc($codigoSolicitud) {
        $dataArrayDetalle = $this->m_bandeja_solicitud_oc->getDetalleSolicitudOc($codigoSolicitud);
        $html = '<table id="tbAtenderSolicitudOc" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>SOLICITUD</th>
                            <th>ITEMPLAN</th>
                            <th>COSTO MO</th>
                            <th>COSTO MAT</th>
                            <th>SOLPED</th>
                            <th>OC</th>
                            <th>POSICION</th>
                            <th>COSTO SAP</th>
                        </tr>
                    </thead>

                    <tbody>';
            $indice = 0;
            foreach($dataArrayDetalle as $row){
            $html .='<tr>
                        <td>'.$row['codigo_solicitud'].'</td>
                        <td>'.$row['itemplan'].'</td>
                        <td>'.$row['costo_mo_ix'].'</td>
                        <td>'.$row['limite_costo_mat'].'</td>
                        <td><input id="cesta_'.$row['itemplan'].'" value="'.$row['oc_sol'].'"></td>
                        <td><input id="orden_'.$row['itemplan'].'_'.$indice.'" value="'.$row['oc_sol'].'"></td>
                        <td><input id="posicion_'.$row['itemplan'].'_'.$indice.'" value="'.$row['oc_sol'].'"></td>
                        <td><input id="costo_sap_'.$row['itemplan'].'_'.$indice.'" ></td>
                    </tr>';
            }


            $html .='   </tbody>
                    </table>';       

            return $html;
    }

    function atenderSolicitudCreaOc() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $this->db->trans_begin();

            $arraySolicitud = $this->input->post('arraySolicitud');
            $idUsuario      = $this->session->userdata('idPersonaSessionPan');
            $fechaActual    = $this->m_utils->fechaActual();
            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }

            $arrayUpdateSolicitud      = array();
            $arrayUpdateSolicitudxItem = array();
            $arrayUpdatePlanObra       = array();

            $arrayInsertInci = array();

            if(count($arraySolicitud) == 0) {
                throw new Exception("Debe ingresar los parametros.");
            }
			
			$itemplanNuevoFlujoList = array();
			$itemplanListPoPqt = array();
			$disenoList = array();

            foreach($arraySolicitud as $row) {

                $be_adjudicacion = false;
                $dataIncidencia = array();

                $itemplan        = $row['itemplan'];
                $cesta           = $row['cesta'];
                $orden_compra    = $row['orden_compra'];
                $posicion        = $row['posicion'];
                $codigoSolicitud = $row['codigoSolicitud'];
                $costo_sap       = $row['costo_sap'];


                if($cesta == null || $cesta == '') {
                    throw new Exception("Debe ingresar la cesta.");
                }

                if($orden_compra == null || $orden_compra == '') {
                    throw new Exception("Debe ingresar la OC.");
                }

                if($posicion == null || $posicion == '') {
                    throw new Exception("Debe ingresar la posicion.");
                }

                if($costo_sap == null || $costo_sap == '') {
                    throw new Exception("Debe ingresar el costo sap.");
                }

                $objSolicitud['codigo_solicitud'] = $codigoSolicitud;
                $objSolicitud['cesta']         = $cesta;
                $objSolicitud['orden_compra']  = $orden_compra;
                $objSolicitud['estado']         = 2;
                $objSolicitud['usuario_valida'] = $idUsuario;
                $objSolicitud['fecha_valida']   = $fechaActual;
                $objSolicitud['costo_sap']      = $costo_sap;
                
                $objSolicitudxItem['itemplan']            = $itemplan;
                $objSolicitudxItem['codigo_solicitud_oc'] = $codigoSolicitud;
                $objSolicitudxItem['posicion']            = $posicion;

                $dataObra = $this->m_utils->getPlanObraByItemplan($itemplan);
                if($dataObra['idTipoPlanta'] == ID_TIPO_PLANTA_INTERNA) {
                    $objPlanObra['idUsuarioLog'] = $idUsuario;
                    $objPlanObra['fechaLog']  = $fechaActual;
                    $objPlanObra['idEstadoPlan'] = ID_ESTADO_PLAN_EN_OBRA;
                    $objPlanObra['descripcion'] = 'ORDEN DE COMPRA ATENDIDA';  
                } else {
                    if($dataObra['idProyecto'] == ID_PROYECTO_CABLEADO_DE_EDIFICIOS){
                        $arraySubProyNuevoFlujo = _getArrayIDSubProyNuevoFlujoCV();
                        if(!in_array($dataObra['idSubProyecto'],$arraySubProyNuevoFlujo)){
                            throw new Exception("No esta configurado el subproyecto de Planta externa");
                        }else{
                            if($dataObra['idEstadoPlan'] == ID_ESTADO_PLAN_PRE_REGISTRO){
                                $objPlanObra['idUsuarioLog'] = $idUsuario;
                                $objPlanObra['fechaLog']  = $fechaActual;
                                $objPlanObra['descripcion'] = 'ORDEN DE COMPRA ATENDIDA';                       
                                if($dataObra['idSubProyecto']   ==  753){//ACTIVACIONES
                                    $objPlanObra['idEstadoPlan']  = 20;//EN APROBACION
                                }else{
                                    $objPlanObra['idEstadoPlan']  = ID_ESTADO_PLAN_DISENIO;
                                }
                                $be_adjudicacion = true;
                                $dataIncidencia['itemplan'] = $itemplan;
                                $dataIncidencia['idEstadoPlan'] = ID_ESTADO_PLAN_DISENIO;
                                $dataIncidencia['usuario_registro'] = $idUsuario;
                                $dataIncidencia['fecha_registro'] = $fechaActual;
                                $dataIncidencia['id_motivo_seguimiento'] = 34;
                                $dataIncidencia['comentario_incidencia'] = 'AUTOMÁTICO, MODULO BANDEJA SOLICITUD OC';
                                $arrayInsertInci []= $dataIncidencia;
                      
                                $itemplanNuevoFlujoList[] = $itemplan;
                            }

                            if($be_adjudicacion){//solo si acaba de pasar de pre registro a diseno adjudico y registro ot
                                $has_ancla = false;
                                $has_fo  = false;
                                $has_coax = false;
                                $infoAnclasByItemplan = $this->m_bandeja_solicitud_oc->hasEstacionesAnclasByItemplan($itemplan);
                                if($infoAnclasByItemplan['coaxial'] > 0){
                                    $has_coax  = true;
                                    $has_ancla = true;
                                }
                                if($infoAnclasByItemplan['fo'] > 0){
                                    $has_fo    = true;
                                    $has_ancla = true;
                                }
                                if($has_ancla){//si tiene anclas obtenemos sus dias de adjudicacion
							            
                                    $dias = null;
                                    if($dias == null){//si no tiene por defecto 4
                                        $dias = 4;
                                    }
                                    $curHour = date('H');
                                    if ($curHour >= 13) {//13:00 PM
                                        $dias = ($dias + 1);
                                    }
                                    $nuevafecha = strtotime('+' . $dias . ' day', strtotime($fechaActual));
                                    $fechaPreAtencion = date('Y-m-d', $nuevafecha);

                                    if($has_fo){
                                        $infoAdjudicacion = array ( 
                                            'itemplan'                => $itemplan,
                                            'idEstacion'              => ID_ESTACION_FO,
                                            // 'estado'                  => (($idSubProyecto == 722) ? 6 : ID_ESTADO_PLAN_DISENIO),
                                            'estado'                  => ID_ESTADO_PLAN_DISENIO,
                                            'fecha_registro'          => $fechaActual,
                                            'usuario_registro'        => $idUsuario,     
                                            'fecha_adjudicacion'	  => $fechaActual,
                                            'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
                                            'fecha_prevista_atencion' => $fechaPreAtencion
                                        );				
                                        $disenoList[] = $infoAdjudicacion;
                               
                                    }

                                    if($has_coax){
                                        $infoAdjudicacion = array ( 
                                            'itemplan'                => $itemplan,
                                            'idEstacion'              => ID_ESTACION_COAX,
                                            // 'estado'                  => (($idSubProyecto == 722) ? 6 : ID_ESTADO_PLAN_DISENIO),
                                            'estado'                  => ID_ESTADO_PLAN_DISENIO,
                                            'fecha_registro'          => $fechaActual,
                                            'usuario_registro'        => $idUsuario,     
                                            'fecha_adjudicacion'	  => $fechaActual,
                                            'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
                                            'fecha_prevista_atencion' => $fechaPreAtencion
                                        );				
                                        $disenoList[] = $infoAdjudicacion;
                                    }
							            
                                    if($dataObra['paquetizado_fg'] ==  2){//es paquetizada
                                    	$itemplanListPoPqt []= $itemplan;//almacenamos el itemplan para postererormente generarle su po pqt
                                    }
                                }
                            }
                            
                        }
                    }else if($dataObra['idProyecto'] == 52){//FTTH
                        $objPlanObra['idUsuarioLog'] = $idUsuario;
                        $objPlanObra['fechaLog']    = $fechaActual;
                        $objPlanObra['descripcion'] = 'ORDEN DE COMPRA ATENDIDA';        
                        $infoDiseno = $this->m_bandeja_solicitud_oc->getDisenoInfoLiquiByItemEstacion($itemplan, 5);//FTTH ES FO      
                        if($infoDiseno['requiere_licencia'] ==  2){//si no requiere licencia
                            $objPlanObra['idEstadoPlan']  = ID_ESTADO_PLAN_EN_APROBACION;
                        }else{
                            $objPlanObra['idEstadoPlan']  = ID_ESTADO_PLAN_EN_LICENCIA;
                        }                        
                    }else if($dataObra['idProyecto'] == 3){//B2B

                        $nuevafecha = strtotime('+7 day', strtotime($fechaActual));
                        $fechaPreAtencion = date('Y-m-d', $nuevafecha);


                        $objPlanObra['idUsuarioLog']        = $idUsuario;
                        $objPlanObra['fechaLog']            = $fechaActual;
                        $objPlanObra['descripcion']         = 'ORDEN DE COMPRA ATENDIDA';                       
                        $objPlanObra['idEstadoPlan']        = ID_ESTADO_PLAN_DISENIO;
                        $objPlanObra['fechaPrevEjecucion']  = $fechaPreAtencion;
                        
                        
                        $infoAdjudicacion = array ( 
                            'itemplan'                => $itemplan,
                            'idEstacion'              => ID_ESTACION_FO,
                            'estado'                  => ID_ESTADO_PLAN_DISENIO,
                            'fecha_registro'          => $fechaActual,
                            'usuario_registro'        => $idUsuario,     
                            'fecha_adjudicacion'	  => $fechaActual,
                            'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
                            'fecha_prevista_atencion' => $fechaPreAtencion
                        );				
                        
                        $disenoList[] = $infoAdjudicacion;
                    }else if($dataObra['idProyecto'] == 54){//PROYECTOS VARIOS

                        $nuevafecha = strtotime('+7 day', strtotime($fechaActual));
                        $fechaPreAtencion = date('Y-m-d', $nuevafecha);


                        $objPlanObra['idUsuarioLog']        = $idUsuario;
                        $objPlanObra['fechaLog']            = $fechaActual;
                        $objPlanObra['descripcion']         = 'ORDEN DE COMPRA ATENDIDA';
                        $objPlanObra['fechaPrevEjecucion']  = $fechaPreAtencion;
                        
                        if($dataObra['idSubProyecto']  ==  747){
                            $objPlanObra['idEstadoPlan']        = 20;//IP MADRE VA EN APROBACION NO TIENE DISE NI LICE REFORZAMIENTO EXPRESS
                        }else if($dataObra['idSubProyecto']  ==  736 ||  $dataObra['idSubProyecto']  ==  740 ||  $dataObra['idSubProyecto']  ==  744){
                            $objPlanObra['idEstadoPlan']        = 3;//ESTUDIOS DE ESFUERZO EN OBRA
                        }else{
                            $objPlanObra['idEstadoPlan']        = ID_ESTADO_PLAN_DISENIO;

                            $infoAdjudicacion = array ( 
                                'itemplan'                => $itemplan,
                                'idEstacion'              => ID_ESTACION_FO,
                                'estado'                  => ID_ESTADO_PLAN_DISENIO,
                                'fecha_registro'          => $fechaActual,
                                'usuario_registro'        => $idUsuario,     
                                'fecha_adjudicacion'	  => $fechaActual,
                                'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
                                'fecha_prevista_atencion' => $fechaPreAtencion
                            );				
                            
                            $disenoList[] = $infoAdjudicacion;
                        }
                        
                    }else if($dataObra['idProyecto'] == 55){//PROYECTOS MANTENIMIENTO

                        $nuevafecha = strtotime('+7 day', strtotime($fechaActual));
                        $fechaPreAtencion = date('Y-m-d', $nuevafecha);
                        $objPlanObra['idUsuarioLog']        = $idUsuario;
                        $objPlanObra['fechaLog']            = $fechaActual;
                        $objPlanObra['descripcion']         = 'ORDEN DE COMPRA ATENDIDA';
                        $objPlanObra['fechaPrevEjecucion']  = $fechaPreAtencion;
                        
                        if(in_array($dataObra['idSubProyecto'], array(739,755,756,757,759))){
                            $objPlanObra['idEstadoPlan']        = 3;//MANTENIMIENTO CON OC
                        }else{
                            throw new Exception("No esta configurado el subproyecto de mantenimiento!");
                        }                        
                    }else{
                        throw new Exception("No esta configurado el proyeto de Planta externa");
                    }
                }
                $objPlanObra['orden_compra'] = $orden_compra;
                $objPlanObra['itemplan']     = $itemplan;
                $objPlanObra['costo_sap']    = $costo_sap;
				$objPlanObra['estado_sol_oc'] = 'ATENDIDO';
                array_push($arrayUpdateSolicitud, $objSolicitud);
                array_push($arrayUpdateSolicitudxItem, $objSolicitudxItem);
                array_push($arrayUpdatePlanObra, $objPlanObra);
            }
            
            $data = $this->m_bandeja_solicitud_oc->atencionSolicitudOcCrea($arrayUpdateSolicitud, $arrayUpdateSolicitudxItem, $arrayUpdatePlanObra);

            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

			if(count($disenoList) > 0){
                $reponseInsertBatch = $this->m_bandeja_solicitud_oc->insertMasiveDiseno($disenoList);
                if($reponseInsertBatch['error'] == EXIT_ERROR){
                    throw new Exception($reponseInsertBatch['msj']);
                }
            }

            /*
            if(count($arrayInsertInci) > 0){//para itemplan del nuevo flujo integral(que pasaran a diseño)
                $reponseInsertBatch =  $this->m_bandeja_solicitud_oc->registroMasivoIncidenciaCV($arrayInsertInci);
                if($reponseInsertBatch['error'] == EXIT_ERROR){
                    throw new Exception($reponseInsertBatch['msj']);
                }
            }*/

            $this->db->trans_commit();
        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function atenderSolicitudCertiOc() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
			$this->db->trans_begin();
			
            $codigo_solicitud = $this->input->post('codigoSolicitud');  
            $codigo_certifica = $this->input->post('codigoCertificacion');
            $idUsuario        = $this->session->userdata('idPersonaSessionPan');
			
            $fechaActual = $this->m_utils->fechaActual();
            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }

            
            $arrayData = array( 'estado'               => 2,
                                'usuario_valida'       => $idUsuario,
                                'fecha_valida'         => $fechaActual,
                                'codigo_certificacion' => $codigo_certifica);
                //falta actualizar el estado de litemplan y las po a certificado. 21.10.2020 czavala
            $arrayDataObra = $this->m_utils->getDataItemplanByCodOcCerti($codigo_solicitud);
            
            if($arrayDataObra['idEstadoPlan'] == 10) {
                $dataPlanObra = array(  'solicitud_oc_certi'	=> $codigo_solicitud,
                                        'estado_oc_certi'		=> 'ATENDIDO',
                                        'fecha_certifica'		=> $fechaActual,
                                        'trunco_situacion'    => ID_ESTADO_PLAN_CERTIFICADO,
                                        'idUsuarioLog'             => $idUsuario, 
                                        'fechaLog'           => $fechaActual,
                                        'descripcion'         => 'CERTIFICADO OC-TRUNCO');
            } else {//SI NO ES TRUNCO SE CERTIFICA LA OBRA
                $dataPlanObra = array(  'solicitud_oc_certi' => $codigo_solicitud,
                                        'estado_oc_certi'	 => 'ATENDIDO',
                                        'fecha_certifica'	 => $fechaActual,
                                        'idEstadoPlan'       => ID_ESTADO_PLAN_CERTIFICADO,
                                        'idUsuarioLog'       => $idUsuario, 
                                        'fechaLog'           => $fechaActual,
                                        'descripcion'        => 'CERTIFICADO OC');
            }
            
            $countValida = $this->m_utils->getCountPoByItemplanAndEstado($arrayDataObra['itemplan'], 5);
            
            $data = $this->m_bandeja_solicitud_oc->update_solicitud_oc_certi($codigo_solicitud, $arrayData, $dataPlanObra, $idUsuario, $countValida);

            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

			$this->db->trans_commit();
        } catch(Exception $e) {
			$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }


    function filtrarSolicitudOC() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $cod_obra  = $this->input->post('cod_obra') ? $this->input->post('cod_obra') : null;
        $cod_soli  = $this->input->post('cod_solicitud') ? $this->input->post('cod_solicitud') : null;
        $estado    = $this->input->post('estado') ? $this->input->post('estado') : null;

        if($idUsuario == null) {
            throw new Exception('La sesión a expirado, recargue la página');
        }

        //$data['tablaSolicitudOc'] = $this->getTablaSolicitudOc($itemplan, null);
        $data['json_bandeja']   = $this->getArrayPoBaOC($this->m_bandeja_solicitud_oc->getSolicitudOcNew($estado , $cod_obra, $cod_soli));
        $data['error']          = EXIT_SUCCESS;
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

	function actualizarSolicitudCertToPdt(){
		$data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
			
            $codigoSolicitud = $this->input->post('codigoSolicitud'); 
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $fechaActual = $this->m_utils->fechaActual();

			$this->db->trans_begin();

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }
			if($codigoSolicitud == null || $codigoSolicitud == ''){
                throw new Exception("Hubo un error al recibir la solicitud.");
            }
            $arrayData = array( 
				'estado' => 1,
				'usuario_to_pndte' => $idUsuario,
				'fecha_to_pndte' => $fechaActual
			);
            $data = $this->m_bandeja_solicitud_oc->actualizarSolicitud($codigoSolicitud, $arrayData);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
			$data['tablaSolicitudOc'] = $this->getTablaSolicitudOc(null, $codigoSolicitud);
			$this->db->trans_commit();
        } catch(Exception $e) {
			$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
		echo json_encode($data);
	}
	
	function validarSolicitudEdicionOC() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $codigoSolicitud = $this->input->post('codigo_solicitud') ? $this->input->post('codigo_solicitud') : null;
            $costoSap = $this->input->post('costoSap') ? $this->input->post('costoSap') : null;
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesión ha expirado, porfavor vuelva a logearse.');
            }
            if($codigoSolicitud == null || $codigoSolicitud == '') {
                throw new Exception('codigoSolicitud no existente, comunicarse con el programador a cargo.');
            }
            if($costoSap == null || $costoSap == '' || $costoSap == 0) {
                throw new Exception('Hubo un error al recibir el costo.');
            }
            if(!is_numeric($costoSap)) {
				throw new Exception('Debe ingresar el costo correctamente.');
			}
            $arrayDataObra = $this->m_utils->getDataItemplanByCodOcEdicion($codigoSolicitud);
            if($arrayDataObra == null) {
                throw new Exception('Hubo un error al traer la información del itemplan');
            }

            $fechaActual = $this->m_utils->fechaActual();

            $arrayData = array( 
				'estado' => 2,
				'usuario_valida' => $idUsuario,
				'fecha_valida' => $fechaActual,
                'costo_sap' => $costoSap
			);
            $data = $this->m_bandeja_solicitud_oc->actualizarSolicitud($codigoSolicitud, $arrayData);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $codigoSolicitudCerti = $this->m_bandeja_solicitud_oc->getSolCertPndEdicion($arrayDataObra['itemplan']);
            $dataSolCert = array(
                'codigo_solicitud' => $codigoSolicitudCerti,
                'estado'   =>  5//pnd de acta
            );
            $data = $this->m_bandeja_solicitud_oc->actualizarSolicitud($codigoSolicitudCerti, $dataSolCert);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
            $arrayUpdatePO = array(
                'costo_sap' => $costoSap,
                'estado_oc_dev' => 'ATENDIDO',
                'estado_oc_certi' => 'PENDIENTE DE ACTA'
            );
            $data = $this->m_utils->actualizarPlanObra($arrayDataObra['itemplan'],$arrayUpdatePO);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();
            //$data['tablaSolicitudOc'] = $this->getTablaSolicitudOc($arrayDataObra['itemplan'],null);
            $data['json_bandeja'] = $this->getArrayPoBaOC($this->m_bandeja_solicitud_oc->getSolicitudOcNew(1, null,null));//1 = SOLO PENDIENTES
        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
	
	function validarAnulacionDeOC() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $codigoSolicitud = $this->input->post('codigo_solicitud') ? $this->input->post('codigo_solicitud') : null;
            //$costoSap = $this->input->post('costoSap') ? $this->input->post('costoSap') : null;
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesión ha expirado, porfavor vuelva a logearse.');
            }
            if($codigoSolicitud == null || $codigoSolicitud == '') {
                throw new Exception('codigoSolicitud no existente, comunicarse con el programador a cargo.');
            }/*
            if($costoSap == null || $costoSap == '' || $costoSap == 0) {
                throw new Exception('Hubo un error al recibir el costo.');
            }
            if(!is_numeric($costoSap)) {
				throw new Exception('Debe ingresar el costo correctamente.');
			}*/
            $arrayDataObra = $this->m_utils->getDataItemplanByCodOcAnula($codigoSolicitud);
            if($arrayDataObra == null) {
                throw new Exception('Hubo un error al traer la información del itemplan');
            }

            $fechaActual = $this->m_utils->fechaActual();

            $arrayData = array( 
				'estado' => 2,
				'usuario_valida' => $idUsuario,
				'fecha_valida' => $fechaActual
			);
            $data = $this->m_bandeja_solicitud_oc->actualizarSolicitud($codigoSolicitud, $arrayData);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            
            $arrayUpdatePO = array(
                'solicitud_oc_anula_pos' => 'ATENDIDO',
                'orden_compra' => null
            );
            $data = $this->m_utils->actualizarPlanObra($arrayDataObra['itemplan'],$arrayUpdatePO);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();
            //$data['tablaSolicitudOc'] = $this->getTablaSolicitudOc($arrayDataObra['itemplan'],null);
            $data['json_bandeja'] = $this->getArrayPoBaOC($this->m_bandeja_solicitud_oc->getSolicitudOcNew(1, null,null));//1 = SOLO PENDIENTES
        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
}
