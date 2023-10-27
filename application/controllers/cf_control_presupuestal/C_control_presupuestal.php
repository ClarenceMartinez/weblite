<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_control_presupuestal extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_control_presupuestal/m_control_presupuestal');
		$this->load->model('mf_consulta/m_solicitud_Vr');
        $this->load->library('lib_utils');
        $this->load->library('zip');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 66, null, 67, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoBaVal($this->m_control_presupuestal->getBandejaControlPresupuestal('0',null,null));
           // $data['tablaValidacionObra'] = $this->getTablaConsulta(null, $idUsuario, null);
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_control_presupuestal/v_control_presupuestal',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    public function getArrayPoBaVal($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $row){ 
                
                $actions = '';
                $btnArchivo = '';
                if($row['url_archivo'] != null) {
					$btnArchivo = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" download="" href="'.$row['url_archivo'].'"><i class="fal fa-download"></i></a>';
				}
                $btnVerDetalle = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver Detalle Exceso" 
                                        onclick="openMdlDetalleExceso(this)" data-id_solicitud="'.$row['id_solicitud'].'" data-origen ="'.$row['origen'].'">
                                        <i class="fal fa-eye"></i>
                                    </a>';

                $btnAtender = '';
                if($row['situacion'] == 'PENDIENTE'){ 
                            
                    $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Aprobar Exceso" data-origen="'.$row['origen'].'" data-id_estacion="'.$row['idEstacion'].'" data-cos="' . $row['costo_final_nf'] . '" data-costo_po="'.$row['costoPo'].'" data-sol="' . $row['id_solicitud'] . '" data-acc="1" onclick="openModalAtender($(this))">
                                        <i class="fal fa-check"></i>
                                    </a>';
                    $btnRechazar = ' <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Rechazar Exceso" data-origen="'.$row['origen'].'" data-id_estacion="'.$row['idEstacion'].'" data-cos="' . $row['costo_final_nf'] . '" data-costo_po="'.$row['costoPo'] .'" data-sol="' . $row['id_solicitud'] . '" data-acc="2" onclick="openModalAtender($(this));"">
                                        <i class="fal fa-times"></i>
                                    </a>';

                    $btnAtender = $btnValidar.$btnRechazar;
                }
                $actions = $btnArchivo.$btnVerDetalle.$btnAtender;
                 array_push($listaFinal, array($actions,  
                    $row['itemplan'],$row['estadoPlanDesc'], $row['tipo_origen'], (($row['codigo_po']=='null') ? '' : $row['codigo_po']), $row['proyectoDesc'],$row['subProyectoDesc'],$row['estacionDesc'], $row['tipo_po'], $row['eecc']
                   ,$row['zonalDesc'],$row['costo_unitario_mo'],$row['costoActualPo'],$row['excesoPo'],$row['costo_final_f'],$row['usua_solicita'],$row['fecha_solicita'],$row['usua_valida'],$row['fecha_valida'],$row['situacion']));
            }     
        }                                                            
        return $listaFinal;
    }

    function openMdlDetalleExceso() {
		$data['msj'] = null;
        $data['error'] = EXIT_ERROR;
		try {
			$id_solicitud = $this->input->post('id_solicitud');
			$origen       = $this->input->post('origen');
			
			if($id_solicitud == null || $id_solicitud == '') {
				throw new Exception('sin codigo po, comunicarse con el programador a cargo.');
			}
			
			if($origen == null || $origen == '') {
				throw new Exception('sin origen, comunicarse con el programador a cargo.');
			}

			if($origen == 4) {//LIQUIDACION PO MO
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleLiqui($id_solicitud);
			} else if($origen == 1) {//REGISTRO PO MAT
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleRegMat($id_solicitud);
			} else if($origen == 2) {//REGISTRO PO MO
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleRegMo($id_solicitud);
			} else if($origen == 3) {//SOLICITUD DE VR
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleVr($id_solicitud);
			} else if($origen == 5) {
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleAdicPqt($id_solicitud);
			} else if($origen == 6) {
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetallePin($id_solicitud);
			}

			$data['error'] = EXIT_SUCCESS;
			
			// $data['tablaDetallePo'] 	   = $tablaDetallePo;
			$data['tablaDetalleSolicitud'] = $tablaDetalleSolicitud;
			$data['htmlComentario']		   = $htmlComentario;
		} catch(Exception $e) {
			$data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));		
	}
    
    function getTablaDetalleRegMo($id_solicitud) {
		$ListaDetallePO = $this->m_control_presupuestal->getDataSolicitudRegMo($id_solicitud);
		$htmlDetallePO  = null;
		$comentario     = null;
		
		$htmlDetallePO .= '<table id="tbDetalleSolicitud" class="table table-bordered table-hover table-striped w-100">
							<thead class="bg-primary-600">
								<tr>
									<th>CODIGO</th>
									<th>DESCRIPCION</th>
									<th>BAREMO</th>
									<th>COSTO</th>
									<th>CANTIDAD NUEVA</th>
									<th>TOTAL NUEVO</th>
								</tr>
							</thead>
							<tbody>';

		foreach ($ListaDetallePO as $row) {
			$comentario = $row['comentario_reg'];
			$htmlDetallePO .= ' <tr>
									<th>' . $row['codigoPartida']. '</th>
									<td>' . utf8_decode($row['descripcion']) . '</td>
									<td>' . $row['baremo'] . '</td>
									<td>' . $row['preciario'] . '</td>
									<td style="background:#FAB8AA">' . $row['cantidadFinal'] . '</td>
									<td style="background:#FAB8AA">' . $row['total_partida'] . '</td>
								</tr>';
		}
		$htmlDetallePO .= '</tbody>
                    </table>';
		$areaComentario = '<textarea class="form-control input-mask" rows="4" disabled>'.utf8_decode($comentario).'</textarea>';			
		return array($htmlDetallePO, $areaComentario) ;
		
	}

    function validarControlPresupuestal() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {

            $accion = $this->input->post('accion');
            $idSolicitud = $this->input->post('solicitud');
            $comentario  = $this->input->post('comentario');
            $costoFinal  = $this->input->post('costoFinal');
			$origen	     = $this->input->post('origen');
			$idEstacion  = $this->input->post('idEstacion');
			$costoPo     = $this->input->post('costoPo');
			$pep1        = $this->input->post('pep1');
			$pep2        = $this->input->post('pep2');
			
            $idUsuario   = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
			$this->db->trans_begin();
			
			if ($idUsuario == null) {
                throw new Exception('Su sesion expiro, porfavor vuelva a logearse.');
            }
            if ($accion == null || $idSolicitud == null) {
                throw new Exception('Datos Invalidos, refresque la pagina y vuelva a intentarlo.');
            }
            if ($comentario == null || $comentario == '') {
                throw new Exception('Ingresar comentario');
            }
						
			$infoObra = $this->m_control_presupuestal->getInfoObraByIdSolicitud($idSolicitud);
			// if ($origen == null || $origen == '') {
				// throw new Exception('Ingresar origen');
			// }
			$idProyecto = $infoObra['idProyecto'];
			$sisego     = $infoObra['indicador'];
            $dataUpdateSolicitud = array(	'usuario_valida' => $this->session->userdata('idPersonaSessionPan'),
											'fecha_valida' => $this->fechaActual(),
											'estado_valida' => $accion, //1=APROBADO  2=RECHAZADO
											'comentario_valida' => utf8_decode(strtoupper($comentario)));
			
            if ($accion == 2) {//rechazar
                $data = $this->m_control_presupuestal->rejectSolicitud($dataUpdateSolicitud, $idSolicitud);
				 if($origen == 5) {//ADICION PQT
                    if($infoObra['isFerreteria'] == 1){
                        $data = $this->m_control_presupuestal->deletTmpFerreteria($infoObra['itemplan'], $idEstacion);
                    }               
                }
            } else if ($accion == 1) {//aprobar
				if(($costoPo == null || $costoPo == 0) && $origen != 5 && $origen != 3  && $origen != 6) {
					throw new Exception('Costo PO Incorrecto, verificar.');
				}
				
				if($origen == null || $origen == '' || $idEstacion == null) {
					$itemplan = $infoObra['itemplan'];
					if ($infoObra['tipo_po'] == 1) {//material{
						if ($infoObra['costo_unitario_mat'] > $costoFinal) {
							throw new Exception('El costo ingresado es menor al costo actual ' . number_format($infoObra['costo_unitario_mat'], 2) . ', favor de ingresar un costo mayor');
						}
						$dataItemplan = array('costo_unitario_mat' => $costoFinal);
					} else if ($infoObra['tipo_po'] == 2) {//mano_obra
						if ($infoObra['costo_unitario_mo'] > $costoFinal) {
							throw new Exception('El costo ingresado es menor al costo actual ' . number_format($infoObra['costo_unitario_mo'], 2) . ', favor de ingresar un costo mayor');
						}
						$dataItemplan = array('costo_unitario_mo' => $costoFinal);
					} else {
						throw new Exception('Ocurrio un error al obetener la informacion del tipo de la solicitud, refresque la pagina y vuelva a intentarlo.');
					}
					$data = $this->m_control_presupuestal->aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud);
				} else {
					$itemplan = $infoObra['itemplan'];
					$flgCostoUnMayor = null;
					if ($infoObra == null) {
						throw new Exception('Ocurrio un error al obetener la informacion de la solicitud, refresque la pagina y vuelva a intentarlo.');
					}
					if($itemplan == null) {
						throw new Exception('Error itemplan no encontrado');
					}
					
					if ($infoObra['tipo_po'] == 1) {//material{
						if ($infoObra['costo_unitario_mat'] > $costoFinal) {
							$flgCostoUnMayor = 1;
						}
						
						$dataItemplan = array('costo_unitario_mat' => $costoFinal);
					} else if ($infoObra['tipo_po'] == 2) {//mano_obra
						if ($infoObra['costo_unitario_mo'] > $costoFinal) {
							$flgCostoUnMayor = 1;
						} 
						
						$dataItemplan = array('costo_unitario_mo' => $costoFinal);
					} else {
						throw new Exception('Ocurrio un error al obetener la informacion del tipo de la solicitud, refresque la pagina y vuelva a intentarlo.');
					}
					if ($infoObra['itemplan'] == null) {
						throw new Exception('Ocurrio un error al obetener el itemplan de la solicitud, refresque la pagina y vuelva a intentarlo.');
					}
					
					$genSolEdic = 1;//POR DEFECTOR 1
					if($origen == 5) {//ADICION PQT
						$ferreteria = false;
						if($infoObra['isFerreteria'] == 1){
							 $ferreteria = true;
						}else if($infoObra['isFerreteria']    ==  0){
							 $ferreteria = false;
						}else{
							 throw new Exception('Tipo de ferreteria no reconocido, genere un ticket CAP.');					         
						}
					    
						if($infoObra['genSolEdic'] == 1) {
							$genSolEdic == 1;
						} else {
							$genSolEdic == 0;//CAMBIA RECIEN SI genSolEdic NO ES IGUAL A 1 Y NO SE GENERA SU SOLICITUD OC
						}
						
						$codigo_po = $this->m_control_presupuestal->getCodPoSolicitudLiqui($idSolicitud);
						$data = $this->m_control_presupuestal->updateEstadoSolicitudLiquiAdocPqt($dataItemplan, $infoObra['itemplan'], $dataUpdateSolicitud, $idSolicitud, $codigo_po, $ferreteria, $flgCostoUnMayor);
					 }if($origen == 4) {//LIQUI
						$codigo_po = $this->m_control_presupuestal->getCodPoSolicitudLiqui($idSolicitud);
						$arrayPo = $this->m_utils->getInfoPoByCodigoPo($codigo_po);
						
						if($arrayPo['estado_po'] == 5 || $arrayPo['estado_po'] == 6) {
							throw new Exception('La PO ya se encuentra validada, por favor rechazar la solicitud.');
						}
						
						$data = $this->m_control_presupuestal->updateEstadoSolicitudLiqui($dataItemplan, $infoObra['itemplan'], $dataUpdateSolicitud, $idSolicitud, $codigo_po, $costoPo, $flgCostoUnMayor);
						
					} else if($origen == 2) {// REG MO
						$idEecc = $infoObra['idEmpresaColab'];						
						$codigo_po = $this->m_utils->getCodigoPO($itemplan);
						
						if($codigo_po == null || $codigo_po == '') {
							throw new Exception('codigo PO no existe');
						}
						
						if($idEstacion == null || $idEstacion == '') {
							throw new Exception('estacion no existe');
						}
						
                        $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MO', $idEstacion);
                        if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                            throw new Exception("No tiene configurado un area.");
                        }
                        
						$dataPO = array(
											'itemplan'      => $itemplan,
											'codigo_po'     => $codigo_po,
											'estado_po'     => ID_ESTADO_PO_REGISTRADO, //ESTADO REGISTRADO
											'idEstacion'    => $idEstacion,
											'from'          => 4,
											'costo_total'   => $costoPo,
											'idUsuario'     => $idUsuario,
                                            'idArea'        => $dataSubEstacionArea['idArea'],
                                            'idSubProyecto' => $infoObra['idSubProyecto'],
                                            'idEmpresaColab'=> $infoObra['idEmpresaColab'],
											'fechaRegistro' => $this->fechaActual(),
											'estado_asig_grafo' => 0,
											'flg_tipo_area' => 2,//MANO DE OBRA
											'id_eecc_reg'   => $idEecc
										);
					
						$dataLogPO = array	(
												'codigo_po'         =>  $codigo_po,
												'itemplan'          =>  $itemplan,
												'idUsuario'         =>  $idUsuario,
												'fecha_registro'    =>  $this->fechaActual(),
												'idPoestado'        =>  ID_ESTADO_PO_REGISTRADO,
												'controlador'       =>  'VALIDADO EN LA BANDEJA DE EXCESOS'
											); 
                                           // log_message('error', 'codigo_po:'.$codigo_po);
						//log_message('error', print_r($dataDetalleplan, true));
						$dataUpdateSolicitud['codigo_po'] = $codigo_po;
						$data = $this->m_control_presupuestal->updateEstadoSolicitudRegMo(  $dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud,
																							$dataPO, $dataLogPO, $codigo_po, $flgCostoUnMayor);
						if($data['error']   ==  EXIT_ERROR){
							throw new Exception('Hubo un error interno, por favor volver a intentar.');
						}
						$data['codigoPO'] = $codigo_po;
					} else if($origen == 1) {//REG MAT
						//$infoObra = $this->m_control_presupuestal->getInfoObraByIdSolicitud($idSolicitud);
					 
					    $idEecc 		= $infoObra['idEmpresaColab'];
						$idSubProyecto 	= $infoObra['idSubProyecto'];
						$codigo_po 		= $this->m_utils->getCodigoPO($itemplan);
						
						if ($codigo_po == null) {
							throw new Exception('Hubo un error al generar el codigo PO ');
						}
						
						if($idSubProyecto == null) {
							throw new Exception('Hubo un error interno, subproyecto vacio.');
						}

						$dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MAT', $idEstacion);
                        if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                            throw new Exception("No tiene configurado un area.");
                        }						 
 
						$dataPO	 = array(
											"itemplan"    		=> $itemplan,
											"codigo_po"   		=> $codigo_po,
											"estado_po"   		=> ID_ESTADO_PO_PRE_APROBADO,
											"idEstacion"  		=> $idEstacion,
											"from"        		=> 4,
											"costo_total" 		=> $costoPo,
											"idUsuario"   		=> $idUsuario,
											'idArea'        	=> $dataSubEstacionArea['idArea'],
											'idSubProyecto' 	=> $infoObra['idSubProyecto'],
											'idEmpresaColab'	=> $infoObra['idEmpresaColab'],
											"fechaRegistro" 	=> $this->fechaActual(),
											'estado_asig_grafo' => 0,
											"flg_tipo_area" 	=> 1,
											"id_eecc_reg"   	=> $idEecc
										);
			
						$dataLogPO = array(
											"codigo_po" => $codigo_po,
											"itemplan" => $itemplan,
											"idUsuario" => $idUsuario,
											"fecha_registro" => $this->fechaActual(),
											"idPoestado" => ID_ESTADO_PO_PRE_APROBADO,
											"controlador" => 'VALIDADO EN LA BANDEJA DE EXCESOS'
										  );
										  
						$dataUpdateSolicitud['codigo_po'] = $codigo_po;
						
						$data = $this->m_control_presupuestal->updateEstadoSolicitudRegMat($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud,
																							$dataPO, $dataLogPO, $codigo_po, $flgCostoUnMayor);
						if($data['error']   ==  EXIT_ERROR){
							throw new Exception('Hubo un error interno REG MAT, por favor volver a intentar.');
						}
						$data['codigoPO'] = $codigo_po;					
					} else if($origen == 3) {//REG VR
						$codigoSolVr = $this->m_solicitud_Vr->getCodigoSolicitudVr();
						$idEecc 	= $infoObra['idEmpresaColab'];	
						if($codigoSolVr == null || $codigoSolVr == '') {
							throw new Exception('Error codigo vr comunicarse con el programador');
						}
						
						$infoSolPoVR = $this->m_control_presupuestal->getCodPoSolicitudVR($idSolicitud);
						if($infoSolPoVR == null || $infoSolPoVR == '') {
							throw new Exception('Error obtener informacion del VR');
						}
						
						$dataSolVr = array(
										'codigoSolVr'		=> $codigoSolVr,
										'itemplan' 	   		=> $itemplan,
										'codigo_po'     	=> $infoSolPoVR['ptr'],
										'idJefaturaSap'  	=> $infoSolPoVR['idJefaturaSap'],//ver de donde obtener el dato
										'idEmpresaColab' 	=> $idEecc,									
										'vale_reserva'  	=> $infoSolPoVR['vr'],//ver de donde obtener el dato
										'estado'            => 0,//ver de donde obtener el dato
										'idUsuario'  	   	=> $idUsuario,
										'fecha_registro' 	=> $this->m_utils->fechaActual(),
										'costoTotalAnt' 	=> 0,//ver de donde obtener el dato
										'costoTotalNew'     => 0/*ver de donde obtener el dato*/);

						$arrayDetalleInsert = array();
						$arrayDetalleSol = $this->m_control_presupuestal->getDataDetalleVr($idSolicitud);
						foreach($arrayDetalleSol as $row) {
							$arrayTemp = array (
								'codigoSolVr'       => $codigoSolVr,
								'codigo_material'   => $row['codigoMaterial'],
								'cantidadInicio'    => $row['cantidadInicio'],
								'cantidadFin'       => $row['cantidadFin'],
								'flg_estado'        => 0,//pdt
								'idTipoSolicitudVr' => $row['idTipoSolicitudVr'],
								'flg_adicion'       => isset($row['flg_adicion']) ? $row['flg_adicion'] : null,
								'send_rpa'          => isset($row['send_rpa']) ? $row['send_rpa'] : null,
								'orden_prioridad'   => 1,
								'costoMat'          => $row['costoMaterial']
							);
							$arrayDetalleInsert[] = $arrayTemp;
						}

						if(count($arrayDetalleInsert) == 0){
							throw new Exception('No hay un detalle de solicitud de vr para registrar!!');
						}

						$data = $this->m_control_presupuestal->updateEstadoSolicitudVr(	$dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud,$dataSolVr, $arrayDetalleInsert, $flgCostoUnMayor);
					
						if($data['error']   ==  EXIT_ERROR){
							throw new Exception('realizo una accion incorrecta en VR, por favor volver a intentar.');
						}
					} else if($origen == 6) {//EDICION PIN
						
						$data = $this->m_control_presupuestal->updateEstadoSolicitudPin($dataItemplan, $infoObra['itemplan'], $dataUpdateSolicitud, $idSolicitud, $flgCostoUnMayor);
					}
				
                    /* no genera edicion oc por el momento
					$arrayData = $this->m_control_presupuestal->getDataSolicitudOc($itemplan); //OC EDICION
					if($infoObra['tipo_po'] == 2) {//MO
						if($arrayData['pep1'] != null && $arrayData['pep1'] != '') {
							if($genSolEdic == 1) {
								if($infoObra['costo_unitario_mo'] < $costoFinal) {									 
									$countPendienteEdicOc = $this->m_control_presupuestal->getValidOcEdic($itemplan);
									
									if($countPendienteEdicOc > 0) {
										$data['error'] = EXIT_ERROR;
										throw new Exception("No se puede generar una solicitud Edic. OC, ya que una solicitud del mismo tipo se encuentra pendiente de validacion.");
									}
									
									if($pep1 != null) {//SI SE INGRESA LA PEP MANUAL
										if($pep2 == null){
											throw new Exception("Ingresar la pep2.");
										}
										$arrayData['pep1'] = $pep1;
										$arrayData['pep2'] = $pep2;
									}
									
									$arrayExcedete = $this->m_control_presupuestal->getExcedente($idSolicitud, $arrayData['pep1']);
									if($arrayExcedete['flg_presupuesto'] == 1) {
										$fechaActual = $this->m_utils->fechaActual();
										$cod_solicitud = $this->m_utils->getCodSolicitudOC();

										$data = $this->m_control_presupuestal->insertSolicitudOcEdi($arrayData, $fechaActual, $cod_solicitud, $costoFinal, $itemplan);	
									
										if($data['error'] == EXIT_ERROR) {
											throw new Exception($data['msj']);
										}
										
										//$data = $this->m_utils->actualizarMontoDisponibleAll($arrayData['pep1'], $arrayExcedete['excedente']);
										
										if($data['error'] == EXIT_ERROR) {
											throw new Exception($data['msj']);
										}
									} else {
										$data['error'] = EXIT_ERROR;
										throw new Exception("La pep ".$arrayData['pep1']." se encuentra sin presupuesto para este exceso.");
									}
								}
							}
						} else {
							 throw new Exception('No cuenta con una PEP, verificar.');
						}
					}*/	
				}					
            } else {
                throw new Exception('Ocurrio un error al obetener la informacion del tipo de la accion a realizar, refresque la pagina y vuelva a intentarlo.');
            }
			
			$this->db->trans_commit();

            /*
              $data['tablaBandejaSiom'] = $this->getTablaSiom(null,null,null,null,null,null); */
            //
        } catch (Exception $e) {
			$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function fechaActual() {
        $zonahoraria = date_default_timezone_get();
        ini_set('date.timezone', 'America/Lima');
        setlocale(LC_TIME, "es_ES", "esp");
        $hoy = strftime("%Y-%m-%d %H:%M:%S");
        return $hoy;
    }

    function filtrarBandejaExceso() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');        
        $itemplan    = ($this->input->post('itemplan') != '' ? $this->input->post('itemplan') : null);       
		$estado      = ($this->input->post('estado') != '' ? $this->input->post('estado') : null);       
        if($idUsuario == null) {
            throw new Exception('La sesión a expirado, recargue la página');
        }
 
        $data['json_bandeja']   = $this->getArrayPoBaVal($this->m_control_presupuestal->getBandejaControlPresupuestal($estado,null,$itemplan));
        $data['error']          = EXIT_SUCCESS;
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

	function getTablaDetalleLiqui($id_solicitud) {
		$ListaDetallePO = $this->m_control_presupuestal->getDataSolicitudLiqui($id_solicitud);
		$htmlDetallePO  = null;
		$comentario     = null;
		
		$htmlDetallePO .= '<table id="tbDetalleSolicitud" class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th>CODIGO</th>
									<th>DESCRIPCION</th>
									<th>BAREMO</th>
									<th>COSTO</th>
									<th>CANTIDAD ACTUAL</th>
									<th>TOTAL ACTUAL</th>
									<th>CANTIDAD NUEVA</th>
									<th>TOTAL NUEVO</th>
								</tr>
							</thead>
							<tbody>';

		foreach ($ListaDetallePO as $row) {
			$comentario = $row['comentario_reg'];
			$htmlDetallePO .= ' <tr>
									<th>' . $row['codigoPartida']. '</th>
									<td>' . utf8_decode($row['descripcion']) . '</td>
									<td>' . $row['baremo'] . '</td>
									<td>' . $row['preciario'] . '</td>
									<td>' . $row['cantidad_actual'] . '</td>
									<td>' . $row['total_actual'] . '</td>
									<td style="background:#FAB8AA">' . $row['cantidadFinal'] . '</td>
									<td style="background:#FAB8AA">' . $row['total_partida'] . '</td>
								</tr>';
		}
		$htmlDetallePO .= '</tbody>
                    </table>';
		$areaComentario = '<textarea class="form-control input-mask" rows="4" disabled>'.utf8_decode($comentario).'</textarea>';			
		return array($htmlDetallePO, $areaComentario) ;
	}

	function getTablaDetalleRegMat($id_solicitud) {
		$ListaDetallePO = $this->m_control_presupuestal->getDataSolicitudRegMat($id_solicitud);
		$htmlDetallePO  = null;
		$comentario     = null;
		
		$htmlDetallePO .= '<table id="tbDetalleSolicitud" class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th>CODIGO MAT</th>
									<th>DESCRIPCION</th>
									<th>COSTO</th>
									<th>CANTIDAD</th>
									<th>TOTAL</th>
								</tr>
							</thead>
							<tbody>';

		foreach ($ListaDetallePO as $row) {
			$comentario = $row['comentario_reg'];
			$htmlDetallePO .= ' <tr>
									<th>' . $row['codigo_material']. '</th>
									<td>' . utf8_decode($row['descrip_material']) . '</td>
									<td>' . $row['costoMat'] . '</td>
									<td style="background:#FAB8AA">' . $row['cantidadFinal'] . '</td>
									<td style="background:#FAB8AA">' . $row['total_mat'] . '</td>
								</tr>';
		}
		$htmlDetallePO .= '</tbody>
                    </table>';
		$areaComentario = '<textarea class="form-control input-mask" rows="4" disabled>'.utf8_decode($comentario).'</textarea>';			
		return array($htmlDetallePO, $areaComentario) ;
		
	}

	function getTablaDetalleVr($id_solicitud) {
		$ListaDetallePO = $this->m_control_presupuestal->getDataSolicitudVr($id_solicitud);
		$htmlDetallePO  = null;
		$comentario     = null;
		
		$htmlDetallePO .= '<table id="tbDetalleSolicitud" class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th>CODIGO MAT</th>
									<th>DESCRIPCION</th>
									<th>COSTO</th>
									<th>CANTIDAD</th>
									<th>TIPO</th>
									<th>CANTIDAD INGRESADO SOL VR</th>
									<th>CANTIDAD FINAL</th>
									<th>TOTAL</th>
								</tr>
							</thead>
							<tbody>';

		foreach ($ListaDetallePO as $row) {
			$style = null;
			if($row['cantidadFin'] != null && $row['cantidadFin'] != '') {
				$style = 'style="background:#FAB8AA"';
			}
			$comentario = $row['comentario_reg'];
			$htmlDetallePO .= ' <tr>
									<th>' . $row['codigo_material']. '</th>
									<td>' . utf8_decode($row['descrip_material']) . '</td>
									<td>' . $row['costoMat'] . '</td>
									<td>' . $row['cant_actual'] . '</td>
									<td '.$style.'>' . $row['desc_tipo_solicitud'] . '</td>
									<td '.$style.'>' . $row['cantidadIngresado'] . '</td>
									<td '.$style.'>' . $row['cantidadFin'] . '</td>
									<td '.$style.'>' . $row['totalSolVr'] . '</td>
								</tr>';
		}
		$htmlDetallePO .= '</tbody>
                    </table>';
		$areaComentario = '<textarea class="form-control input-mask" rows="4" disabled>'.utf8_decode($comentario).'</textarea>';			
		return array($htmlDetallePO, $areaComentario) ;
		
	}
}
