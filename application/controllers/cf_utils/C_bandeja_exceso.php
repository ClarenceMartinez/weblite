<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_bandeja_exceso extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_utils/m_bandeja_exceso');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_EXCESO_OBRA_PADRE, null, ID_BANDEJA_EXCESO_HIJO, ID_MODULO_ADMINISTRATIVO);
            $data['opciones'] = $result['html'];
            $data['tablaSolExceso'] = $this->getTablaSolExceso(null, null, null);
            $this->load->view('vf_utils/v_bandeja_exceso',$data);        	  
    	 }else{
        	redirect(RUTA_OBRA2, 'refresh');
	    }     
    }

    function getTablaSolExceso($situacion, $tipoPO, $itemplan) {
        $arrayData = $this->m_bandeja_exceso->getBandejaExceso($situacion, $tipoPO, $itemplan);
        $html = '<table id="tb_bandeja_exceso" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-500">
                        <tr>
                            <th>ACCION</th>
                            <th>ITEMPLAN</th>
                            <th>ESTADO PLAN</th>
                            <th>TIPO SOLICITUD</th>
                            <th>PO</th>
                            <th>PROYECTO</th>
                            <th>SUBPROYECTO</th>    
                            <th>ESTACION</th>                           
                            <th>TIPO AREA</th>                            
                            <th>EECC</th>
                            <th>ZONAL</th>
                            <th>COSTO OC</th>
                            <th>COSTO ACTUAL</th>                            
                            <th>EXCEDENTE SOL.</th>
                            <th>COSTO FINAL</th>
                            <th>USUA. SOLICITA</th>
                            <th>FEC. SOLICITA</th>							
                            <th>USUA. VALIDA</th>
                            <th>FEC. VALIDA</th>
                            <th>SITUACION</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayData as $row) {

						$actions = '';
						  
						if($row['situacion'] == 'PENDIENTE') {
							$actions .= '	<a onclick="openModalAtender(this);" data-origen="'.$row['origen'].'" data-id_estacion="'.$row['idEstacion'].'" data-cos="' . $row['costo_final_nf'] . '" data-costo_po="'.$row['costoPo'].'" data-sol="' . $row['id_solicitud'] . '" data-acc="1" 
											data-tipo_po="'.$row['tipo_po'].'" class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1"  title="Aprobar">
												<i class="fal fa-check"></i>
											</a>  
											<a onclick="openModalAtender(this);" data-origen="'.$row['origen'].'" data-id_estacion="'.$row['idEstacion'].'" data-cos="' . $row['costo_final_nf'] . '" data-costo_po="'.$row['costoPo'].'" data-sol="' . $row['id_solicitud'] . '" data-acc="2"
											data-tipo_po="'.$row['tipo_po'].'" class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1"  title="Rechazar">
												<i class="fal fa-times-circle"></i>
											</a>';
						}

						$actions .= '	<a onclick="openModalDetalleExceso(this);" data-origen="'.$row['origen'].'" data-id_estacion="'.$row['idEstacion'].'" data-cos="' . $row['costo_final_nf'] . '" data-costo_po="'.$row['costoPo'].'" data-sol="' . $row['id_solicitud'] . '" 
										data-comentario="'.$row['comentario_reg'].'" class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1"  title="Ver Detalle">
											<i class="fal fa-eye"></i>
										</a> ';

                        $html .= ' <tr>
                                        <th>
                                            <div class="d-flex demo">
                                            '.$actions.'
                                            </div>           
                                        </th>
                                        <td>'.$row['itemplan'].'</td>
                                        <td>'.$row['estadoPlanDesc'].'</td>  
                                        <td>'.$row['tipo_origen'].'</td>							
                                        <td>'.$row['codigo_po'].'</td>
                                        <td>'.$row['proyectoDesc'].'</td>
                                        <td>'.$row['subProyectoDesc'].'</td>
                                        <td>'.$row['estacionDesc'].'</td>
                                        <td>'.$row['tipo_po_desc'].'</td>
                                        <td>'.$row['eecc'].'</td>
                                        <td>'.$row['zonalDesc'].'</td>
                                        <td>'.$row['costo_unitario_mo'].'</td>
                                        <td>'.$row['costoActualPo'].'</td>
                                        <td>'.$row['exceso_solicitado'].'</td>
                                        <td>'.$row['costo_final'].'</td>
                                        <td>'.$row['usua_solicita'].'</td>
                                        <td>'.$row['fecha_solicita'].'</td>
                                        <td>'.$row['usua_valida'].'</td>
                                        <td>'.$row['fecha_valida'].'</td>
                                        <td>'.$row['situacion'].'</td>
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function atenderSolicitudExceso() {
        $data['error']  = EXIT_ERROR;
        $data['msj']  = null;
        try {

            $idSolicitud = $this->input->post('solicitud');
            $comentario  = $this->input->post('txtComentario');
            $costoFinal  = $this->input->post('costoFinal');
			$origen	     = $this->input->post('origen');
			$idEstacion  = $this->input->post('idEstacion');
			$costoPo     = $this->input->post('costoPo');
			$tipoPO      = $this->input->post('tipoPO');
			$accion      = $this->input->post('accion');
			

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			if ($accion == null || $idSolicitud == null) {
                throw new Exception('Datos Invalidos, refresque la pagina y vuelva a intentarlo.');
            }

			$fechaActual = $this->m_utils->fechaActual();

			$infoObra = $this->m_bandeja_exceso->getInfoObraByIdSolicitud($idSolicitud);
			if ($infoObra == null) {
				throw new Exception('Ocurrio un error al obetener la información de la solicitud, refresque la página y vuelva a intentarlo.');
			}
			$itemplan = $infoObra['itemplan'];
			$dataUpdateSolicitud = array(	
				'usuario_valida' =>  $idUsuario,
				'fecha_valida' => $fechaActual,
				'estado_valida' => $accion, //1=APROBADO  2=RECHAZADO
				'comentario_valida' => $comentario
			);
			if ($accion == 2) {//rechazar
                $data = $this->m_bandeja_exceso->updateSolicitud($dataUpdateSolicitud, $idSolicitud);
				if ($data['error'] == EXIT_ERROR) {
					throw new Exception($data['msj']);
				}
				// if($origen == 5) {//ADICION PQT
                //     if($infoObra['isFerreteria'] == 1){
                //         $data = $this->m_control_presupuestal->deletTmpFerreteria($infoObra['itemplan'], $idEstacion);
                //     }               
                // }
            } else if ($accion == 1){ //aprobar
				if($tipoPO == 1){//MAT
					$dataItemplan = array('costo_unitario_mat' => $costoFinal);
				}else{//MO
					$dataItemplan = array('costo_unitario_mo' => $costoFinal);
					if($origen == 2){
						$itemplan = $infoObra['itemplan'];
						$data = $this->m_bandeja_exceso->updateSolicitud($dataUpdateSolicitud, $idSolicitud);
						if ($data['error'] == EXIT_ERROR) {
							throw new Exception($data['msj']);
						}
						if ($infoObra['costo_unitario_mo'] > $costoFinal) {
							$data = $this->m_utils->actualizarPlanObra($itemplan, $dataItemplan);
							if ($data['error'] == EXIT_ERROR) {
								throw new Exception($data['msj']);
							}
						}
						$data = $this->m_utils->deleteDetallePOMO($infoObra['codigo_po']);
						if($data['error'] == EXIT_ERROR){
							throw new Exception($data['msj']);
						}
						$arrayDetallePo = $this->m_utils->getDataDetalleRegMo($idSolicitud);
						$arrayDetalleInsert = array();
						foreach($arrayDetallePo as $row) {
							$detallePo = array (
								'codigo_po'        => $infoObra['codigo_po'],
								'codigoPartida'    => $row['codigoPartida'],
								'baremo'           => $row['baremo'],
								'costoMo'          => $row['costoMo'],
								'costoMat'         => $row['costoMat'],
								'preciario'        => $row['preciario'],
								'cantidadInicial'  => $row['cantidadInicial'],
								'montoInicial'     => $row['montoInicial'],
								'cantidadFinal'    => $row['cantidadFinal'],
								'montoFinal'       => $row['montoFinal']
							);
							$arrayDetalleInsert[] = $detallePo;
						}
						$data = $this->m_utils->registrarDetallePoMo($arrayDetalleInsert);
						if($data['error'] == EXIT_ERROR){
							throw new Exception($data['msj']);
						}
						
						$arrayUpdatePO = array(
							"costo_total" => $infoObra['exceso_solicitado']
						);
						$data = $this->m_utils->actualizarPoByCodigo($infoObra['codigo_po'],$arrayUpdatePO);
						if($data['error'] == EXIT_ERROR){
							throw new Exception($data['msj']);
						}

						$arrayData = $this->m_bandeja_exceso->getDataSolicitudOc($itemplan);
						if($arrayData == null){
							throw new Exception('Hubo un error entrar la información de la oc');
						}
						if($infoObra['costo_unitario_mo'] < $costoFinal) {
							$countPendienteEdicOc = $this->m_utils->getValidOcEdic($itemplan, TIPO_SOLICITUD_OC_EDIC);	
							if($countPendienteEdicOc > 0) {
								$data['error'] = EXIT_ERROR;
								throw new Exception("No se puede generar una solicitud Edic. OC, ya que una solicitud del mismo tipo se encuentra pendiente de validacion.");
							}
							$rsp = $this->m_utils->fnRegistrarSolicitudOcEdicCerti($itemplan, $costoFinal, $idUsuario, 'PLAN PIN', 2);
							
							if($rsp != 1) {
								$data['error'] = EXIT_ERROR;
								throw new Exception('No se creo la solicitud de edición, verificar.');
							}
						}
					}
				}
			}
			$this->db->trans_commit();
        } catch(Exception $e) {
			$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


	function getDetalleSolExceso() {
        $data['error']  = EXIT_ERROR;
        $data['msj']  = null;
        try {

            $idSolicitud = $this->input->post('solicitud') ? $this->input->post('solicitud') : null;
			$origen = $this->input->post('origen') ? $this->input->post('origen') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			if ($idSolicitud == null) {
                throw new Exception('Hubo un error al recibir la solicitud.');
            }
			if ($origen == null) {
                throw new Exception('Datos Inválidos, refresque la página y vuelva a intentarlo.');
            }

			$fechaActual = $this->m_utils->fechaActual();
			if($origen == 1){//MAT

			}else if($origen == 2){//MO
				$data['tbDetalleSol'] = $this->getTablaDetalleRegMo($idSolicitud);
			}
			$data['error']  = EXIT_SUCCESS;

        } catch(Exception $e) {
			$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

	function getTablaDetalleRegMo($idSolicitud) {
        $arrayData = $this->m_bandeja_exceso->getDataSolicitudRegMo($idSolicitud);
        $html = '<table id="tb_detalle_sol" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-500">
                        <tr>
                            <th>CÓDIGO</th>
							<th>DESCRIPCIÓN</th>
                            <th>BAREMO</th>
							<th>PRECIARIO</th>
                            <th>CANTIDAD NUEVA</th>
							<th>TOTAL NUEVO</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                    
        foreach($arrayData as $row){            
            $html .=' <tr>                          
                        <td>'.$row['codigoPartida'].'</td>			
                        <td>'.$row['desc_partida'].'</td>				
                        <td>'.$row['baremo'].'</td>
						<td>'.$row['preciario'].'</td>
                        <td>'.$row['cantidadFinal'].'</td>
						<td>'.number_format($row['montoFinal'],2).'</td>
                    </tr>';
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }

    
}
