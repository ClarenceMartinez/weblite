<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_solicitud_oc_item_madre extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_itemplan_madre/m_solicitud_oc_item_madre');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');
            $result = $this->lib_utils->getHTMLPermisos($permisos, 20, null, 36, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoConIpMadre($this->m_solicitud_oc_item_madre->getSolicitudOc(null, null));
            //$data['tablaSolicitudOc'] = $this->getTablaSolicitudOc(NULL, NULL, array(ID_ESTADO_PLAN_DISENIO, ID_ESTADO_PLAN_PDT_OC));
            $this->load->view('vf_itemplan_madre/v_solicitud_oc_item_madre',$data);        	  
    	 }else{
        	redirect(RUTA_OBRA2, 'refresh');
	    }     
    }

    public function getArrayPoConIpMadre($listaSolVr){
        $listaFinal = array();      
        if($listaSolVr!=null){
            foreach($listaSolVr as $row){ 
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
                
                array_push($listaFinal, array($btnDetalle.' '.$btnValidar.' '.$btnPdt,
                    $row['codigo_solicitud'],$row['tipoSolicitud'], $row['proyectoDesc'], $row['subProyectoDesc'],$row['empresaColabDesc'],$row['itemplan_m'],
                    $row['costo_total'], $row['costo_sap'], $row['codigoInversion'], $row['fecha_creacion'] ,$row['nombreCompleto'], $row['fecha_valida'],
                    $row['cesta'], $row['orden_compra'] ,$row['codigo_certificacion'], $row['estado_sol'],$row['fecha_cancelacion'], $row['estatus_solicitud'],
                    $row['P'],$row['Q'], $row['costo_total']));
            }      
        }  
        return $listaFinal;
    }

    function filtrarSolicitudOCItemMadre() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');

        $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;

        if($idUsuario == null) {
            throw new Exception('La sesión a expirado, recargue la página');
        }

        $data['tablaSolicitudOc'] = $this->getTablaSolicitudOc($itemplan, null);
        echo json_encode($data);
    }

    function getTablaSolicitudOc($itemplan, $codigoSolicitud) {
        $arrayPlanobra = $this->m_solicitud_oc_item_madre->getSolicitudOc($itemplan, $codigoSolicitud);
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

    function getDetalleSolicitudOcItemMadre() {
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
        $dataArrayDetalle = $this->m_solicitud_oc_item_madre->getDetalleSolicitudOc($codigoSolicitud);
        $html = '<table id="tbDetalleSolicitudOc" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ITEMPLAN</th>
                            <th>SUBPROYECTO</th> 
                            <th>NOMBRE PROYECTO</th>
                            <th>COSTO MO</th>
                            <th>SOLPED</th>
                            <th>OC</th>
                            <th>POSICION</th>
                        </tr>
                    </thead>

                    <tbody>';
            $indice = 0;
            foreach($dataArrayDetalle as $row){
            $html .='<tr>
                        <td>'.$row['itemplan_m'].'</td>
                        <td>'.$row['subProyectoDesc'].'</td>
                        <td>'.$row['nombrePlan'].'</td>
                        <td>'.$row['costo_mo_ix'].'</td>	
                        <td>'.$row['cesta_sol'].'</td>
                        <td>'.$row['oc_sol'].'</td>
                        <td>'.$row['posicion_ix'].'</td>
                    </tr>';
            }


            $html .='   </tbody>
                    </table>';       

            return $html;
    }

    function getDataAtenderSolicitudOcItemMadre() {
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
        $dataArrayDetalle = $this->m_solicitud_oc_item_madre->getDetalleSolicitudOc($codigoSolicitud);
        $html = '<table id="tbAtenderSolicitudOc" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>SOLICITUD</th>
                            <th>ITEMPLAN</th>
                            <th>COSTO MO</th>
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
                        <td>'.$row['itemplan_m'].'</td>
                        <td>'.$row['costo_mo_ix'].'</td>
                        <td><input id="cesta_'.$row['itemplan_m'].'" value="'.$row['oc_sol'].'"></td>
                        <td><input id="orden_'.$row['itemplan_m'].'_'.$indice.'" value="'.$row['oc_sol'].'"></td>
                        <td><input id="posicion_'.$row['itemplan_m'].'_'.$indice.'" value="'.$row['oc_sol'].'"></td>
                        <td><input id="costo_sap_'.$row['itemplan_m'].'_'.$indice.'" ></td>
                    </tr>';
            }


            $html .='   </tbody>
                    </table>';       

            return $html;
    }

    function atenderSolicitudCreaOcItemMadre() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $this->db->trans_begin();
            log_message('error', 'ENTRO ATENDER...');
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
            log_message('error', 'ENTRO ATENDER2...');
            foreach($arraySolicitud as $row) {

                $be_adjudicacion = false;
                $dataIncidencia = array();

                $itemplan        = $row['itemplan_m'];
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
                
                $objSolicitudxItem['itemplan_m']          = $itemplan;
                $objSolicitudxItem['codigo_solicitud_oc'] = $codigoSolicitud;
                $objSolicitudxItem['posicion']            = $posicion;

                $objPlanObra['idEstado']        = 2;//DISENO
                $objPlanObra['orden_compra']    = $orden_compra;
                $objPlanObra['itemplan_m']      = $itemplan;
                $objPlanObra['costo_sap']       = $costo_sap;
				$objPlanObra['estado_sol_oc']   = 'ATENDIDO';
                array_push($arrayUpdateSolicitud, $objSolicitud);
                array_push($arrayUpdateSolicitudxItem, $objSolicitudxItem);
                array_push($arrayUpdatePlanObra, $objPlanObra);
            }
            log_message('error', 'ENTRO ATENDER3...');
            $data = $this->m_solicitud_oc_item_madre->atencionSolicitudOcCrea($arrayUpdateSolicitud, $arrayUpdateSolicitudxItem, $arrayUpdatePlanObra);
           
            log_message('error', '>'.print_r($data, true));
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
            log_message('error', 'ENTRO ATENDER4...');
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

            $codigoSolicitudCerti = $this->m_creacion_oc->getSolCertPndEdicion($arrayDataObra['itemplan']);
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
            $data['tablaSolicitudOc'] = $this->getTablaSolicitudOc($arrayDataObra['itemplan'],null);
        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
}
