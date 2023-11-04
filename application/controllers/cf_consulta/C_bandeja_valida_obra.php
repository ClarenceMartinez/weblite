<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_bandeja_valida_obra extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_bandeja_valida_obra');
        $this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
        $this->load->model('mf_consulta/m_detalle_consulta');
        $this->load->library('lib_utils');
        $this->load->library('zip');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_OBRA_PADRE, null, 25, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoBaVal($this->m_bandeja_valida_obra->getBandejaValidaObra(null, $idUsuario, null, 1));
           // $data['tablaValidacionObra'] = $this->getTablaConsulta(null, $idUsuario, null);
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_consulta/v_bandeja_valida_obra',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    public function getArrayPoBaVal($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $row){ 
                
                $actions = '';
                if($row['estado'] == 0){
                    $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                    onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                    <i class="fal fa-eye"></i>
                                </a>';
                }else if($row['estado'] == 1){
                    $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                    onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                    <i class="fal fa-eye"></i>
                                </a>';                
                }else {
                    $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                    onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                    <i class="fal fa-eye"></i>
                                </a>';
                }                
                
                $files = '<a style="color: #E51318" onclick=disenho("' . $row['itemplan'] . '")>Expe_Diseno&nbsp;&nbsp</a>
                            <a style="color: #954b97" onclick=licencias("' . $row['itemplan'] . '")>Licencia&nbsp;&nbsp</a>
                            <a style="color: #5bc500" onclick=liquidacion("' . $row['itemplan'] . '")>Liquida_obra&nbsp;&nbsp</a>
                            <a style="color: #121311" onclick=expedienteLiqui("' . $row['itemplan'] . '","")>Expe_Termino&nbsp</a>';
                 array_push($listaFinal, array($actions, $files,
                    $row['itemplan'],$row['proyectoDesc'], $row['subProyectoDesc'], $row['indicador'], $row['empresaColabDesc'],$row['jefaturaDesc'],$row['cantFactorPlanificado'], $row['costo_unitario_mo'], $row['total_mo_validado']
                   ,$row['fec_registro'],$row['nombre_completo'],$row['situacion']));
            }     
        }                                                            
        return $listaFinal;
    }

    function getTablaConsulta($itemplan, $idUsuario, $idEmpresaColab, $flgFiltro = 1) {

        $arrayData = $this->m_bandeja_valida_obra->getBandejaValidaObra($itemplan, $idUsuario, $idEmpresaColab, $flgFiltro);
        
        $html = '<table id="tbBandejaValObra" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>  
                            <th>FILES</th>
                            <th>ITEMPLAN</th>                            
                            <th>PROYECTO</th>
                            <th>SUBPROYECTO</th>
                            <th>INDICADOR</th>
                            <th>EECC</th>
                            <th>JEFATURA</th>
                            <th>UIP</th>
                            <th>COSTO TOTAL</th>
                            <th>FECHA REGISTRO</th>
                            <th>USUARIO REGISTRO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($arrayData as $row) {

            $actions = '';
            if($row['estado'] == 0){
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                <i class="fal fa-eye"></i>
                            </a>';
            }else if($row['estado'] == 1){
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                <i class="fal fa-eye"></i>
                            </a>';
                            /*
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Editar" data-itemplan="'.$row['itemplan'].'"
                            onclick="getPosByItemplan(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                            <i class="fal fa-clipboard"></i>
                        </a>';*/
            }else {
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                <i class="fal fa-eye"></i>
                            </a>';
            }

            $html .= ' <tr>
                            <td>
                                <div class="d-flex demo">
                                    '.$actions.'
                                </div>
                            </td>
                            <td>
                                <div class="d-flex demo">
                                <a class="" aria-expanded="true" title="Aprobar PO" data-itemplan="'.$row['itemplan'].'"
                                    onclick="descargarExpediseno(this)">
                                    Expe_diseno
                                </a>
                                </div>
                            </td>
                            <td>'.$row['itemplan'].'</td>
                            <td>'.$row['proyectoDesc'].'</td>
                            <td>'.$row['subProyectoDesc'].'</td>
                            <td>'.$row['indicador'].'</td> 
                            <td>'.$row['empresaColabDesc'].'</td> 
                            <td>'.$row['jefaturaDesc'].'</td>
                            <td>'.$row['cantFactorPlanificado'].'</td>
                            <td>'.$row['total_mo_validado'].'</td>
                            <td>'.$row['fec_registro'].'</td>
                            <td>'.$row['nombre_completo'].'</td>
                            <td>'.$row['situacion'].'</td> 
                        </tr>';
        }
        $html .= '</tbody>
            </table>';
        return $html;
    }

    public function filtrarTabla()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $codigoPO = $this->input->post('codigoPO') ? $this->input->post('codigoPO') : null;
			if($itemplan == null &&  $codigoPO == null){
				throw new Exception('Debe ingresar mínimo un filtro para buscar!!');
			}
            $data['tbBandejaAprob'] = $this->getTablaConsulta($itemplan,$codigoPO);
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getPartidasPdtValidacion() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
                $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
                $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
                $idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
				$idSolicitud = $this->input->post('idSol') ? $this->input->post('idSol') : null;
                _log('entro al metodo');
                if($idUsuario == null){
                    throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
                }
                if($itemplan == null){
                    throw new Exception('Hubo un error al recibir el itemplan.');
                }/*
                if($idEstacion == null){
                    throw new Exception('Hubo un error al recibir la estación.');
                }*/
                if($idSolicitud == null){
                    throw new Exception('Hubo un error al recibir la soliticud.');
                }
                $data['tablaPdt'] = $this->getContModal($itemplan, $idEstacion, $idSolicitud);
                $data['error'] = EXIT_SUCCESS;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    
    function getContModal($itemplan, $idEstacion, $idSolicitud){
        $htmlFinal = '';
        $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
        if($infoItem == null){//PRIMERO QUE EL IP TENGA INFORMACION
            $htmlFinal = '<h4 class="text-center" style="color:red">Excepcion detectada, comuniquese con soporte.</h4>';
        }else{
            $htmlFinal .= '<div class="py-3 w-100">';
            if($infoItem['paquetizado_fg'] == 1){
                $solicitudPartAdic = $this->m_bandeja_valida_obra->getSolicitudPartidasAdicionalesByItemplanSolo($idSolicitud);
                        $listaPoFull = $this->m_bandeja_valida_obra->getAllPoMoBySoloItemplan($itemplan);
                        $has_pos_no_aceptados = 0;
                        $costo_final_obra = 0;
                        $has_po = 0;
                        $html = '<div style="margin-bottom: 5%;margin-top: 5%;margin-left: 5%;margin-right: 5%;">
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
                            $html .= '<tr>
                                        <th>'.$row2->estacionDesc.'</th>
                                        <th>'.$row2->tipoPo.'</th>
                                        <th>'.$row2->codigo_po.'</th>
                                        <th>'.$row2->estado.'</th>
                                        <th>'.number_format($row2->costo_total, 2).'</th>
            		                  </tr>';
                            $estados_no_acepted = array(ID_ESTADO_PO_REGISTRADO,ID_ESTADO_PO_PRE_APROBADO,ID_ESTADO_PO_APROBADO,ID_ESTADO_PO_PRE_CANCELADO);
                            if(in_array($row2->estado_po, $estados_no_acepted)){
                                $has_pos_no_aceptados++;
                            }else{
                                $costo_final_obra = $costo_final_obra+$row2->costo_total;
                                $has_po ++;
                            }
                        }
                        $html .= '   </tbody>
                              </table>';
						if($has_po==0){
                            $html .= '<h4 class="text-center" style="color:red">La estacion no cuenta con PO Trabajadas.</h4>';
                        }else if($has_pos_no_aceptados > 0){
                            $html .= '<h4 class="text-center" style="color:red">Las PO de MO deben estar Liquidadas.</h4>';
                        }else{                         
                            if($solicitudPartAdic['estado']==0){//pdt validar nivel 1
                                $infoExpediente = $this->m_bandeja_valida_obra->getInfoExpedienteLiquidacionNoPqtByItem($itemplan);                             
                                $html .= '<div class="row">';
                                if($infoExpediente['path_expediente']!=null){
                                    $html .= '<div class="col-sm-4 col-md-4" style="TEXT-ALIGN: CENTER;">
                                                    <a class="btn btn-primary" href="'.utf8_decode($infoExpediente['path_expediente']).'" download>DESCARGAR EXPEDIENTE <i class="zmdi zmdi-hc-1x zmdi-download"></i></a>
                                              </div>';                                      
                                }
                                $html .= '  <div class="col-sm-4 col-md-4" style="TEXT-ALIGN: CENTER;">
                                                <button data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-item="'.$itemplan.'" class="btn btn-success valNi1" type="button">APROBAR PROPUESTA</button>
                                            </div>
                                            <div class="col-sm-4 col-md-4" style="TEXT-ALIGN: CENTER;">
                                                <button data-from="1" data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-item="'.$itemplan.'" class="btn btn-danger rejectSol" type="button">RECHAZAR</button>
                                            </div>
                                      </div>';
                            }else if($solicitudPartAdic['estado']==1){//pdt validar nivel 2
                                $infoExpediente = $this->m_bandeja_valida_obra->getInfoExpedienteLiquidacionNoPqtByItem($itemplan);
                              //  $tieneMatPdtContabilizar = $this->m_bandeja_valida_obra->getCountMatPDTContabilizarByItem($itemplan);
								
								$html .= '<div class="row">';
								/*if($tieneMatPdtContabilizar > 0){ TEMPORALMENTE 17.03.2021 PEDIDO OWEN SARAVIA.
									$html .= '<div class="col-sm-12 col-md-12" style="text-align: center; color: red; font-weight: bold;">
												La obra tiene materiales pendientes a contabilizar!!
											</div>
										</div>';
								}else{*/									
									if($infoExpediente['path_expediente']!=null){
										$html .= '<div class="col-sm-4 col-md-4" style="TEXT-ALIGN: CENTER;">
														<a class="btn btn-primary" href="'.$infoExpediente['path_expediente'].'" download>DESCARGAR EXPEDIENTE <i class="zmdi zmdi-hc-1x zmdi-download"></i></a>
													</div>';
									}
									$html .= '  <div class="col-sm-4 col-md-4" style="TEXT-ALIGN: CENTER;">
													<button data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-item="'.$itemplan.'" class="btn btn-success valNi2NoPqt" type="button">APROBAR PROPUESTA</button>
												</div>
												<div class="col-sm-4 col-md-4" style="TEXT-ALIGN: CENTER;">
													<button data-from="1" data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-item="'.$itemplan.'" class="btn btn-success rejectSol" type="button">RECHAZAR</button>
												</div>
										</div>';
							/*	}*/
							}
                        }
                        $html .= '</div></div>';
                           
                $html .='</div></div>';
                $htmlFinal = $htmlFinal.$html;
            }else{
				if($infoItem['idProyecto']==52){
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
						$contPartidasPqt = $this->getPartidasPaquetizadasFTTH($infoItem,$row['idEstacion']);
						//$contPartidasAdic = '';$this->getPartidasAdicionales($infoItem,$row['idEstacion']);
						$listaPoFull = $this->m_bandeja_valida_obra->getAllPoMoBySoloItemplan($itemplan);
                        $has_pos_no_aceptados = 0;
                        $costo_final_obra = 0;
                        $has_po = 0;
                        $html2 = '<div style="margin-left: 5%;margin-right: 5%;">
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
                            $html2 .= '<tr>
                                        <th>'.$row2->estacionDesc.'</th>
                                        <th>'.$row2->tipoPo.'</th>
                                        <th>'.$row2->codigo_po.'</th>
                                        <th>'.$row2->estado.'</th>
                                        <th>'.number_format($row2->costo_total, 2).'</th>
            		                  </tr>';
                            $estados_no_acepted = array(ID_ESTADO_PO_REGISTRADO,ID_ESTADO_PO_PRE_APROBADO,ID_ESTADO_PO_APROBADO,ID_ESTADO_PO_PRE_CANCELADO);
                            if(in_array($row2->estado_po, $estados_no_acepted)){
                                $has_pos_no_aceptados++;
                            }else{
                                $costo_final_obra = $costo_final_obra+$row2->costo_total;
                                $has_po ++;
                            }
                        }
                        $html2 .= '</tbody>
								<tfoot class="bg-primary-600">
									<tr>
										<th colspan="4" style="text-align: right;">TOTAL:</th>
										<th>'.number_format($costo_final_obra,2).'</th>									 
									</tr>
								</tfoot>
                              </table>';
						
						$htmlContUl .= '<li class="nav-item">
											<a class="nav-link '.$liActive.'" data-toggle="tab" href="#tab_'.$row['idEstacion'].'" role="tab">
												'.$row['estacionDesc'].'
											</a>
										</li>';

						$htmlContTab .= '<div class="tab-pane fade '.$tabActive.'" id="tab_'.$row['idEstacion'].'" role="tabpanel">
											 
												'.$contPartidasPqt['html'].'
											 
												'. $html2.'
										</div>';

						$contBotones = '<div class="col-xl-12">
										</div>';
						$solicitudPartAdic = $this->m_detalle_consulta->getSolicitudPartidasAdicionales($itemplan, $row['idEstacion']);
						if($solicitudPartAdic != null){
							$infoExpediente = $this->m_bandeja_valida_obra->getInfoExpedienteLiquidacion($itemplan, $idEstacion);
							$expediente = '';
							$hasSolRecha = $this->m_bandeja_valida_obra->hasSolicituExoeRechazada($itemplan);
							if($hasSolRecha > 0){
								$expediente .= '<div class="col-xl-12">
													<h4 class="text-center" style="color:red">La Solicitud previa fue rechazada, puede ver el Motivo <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1 getRechazado2Bucles" data-esta="'.$row['idEstacion'].'" data-item="'.$itemplan.'" title="Movimientos"> <i class="fal fa-search"></i></a></h4>
												</div>';
							}
							if($infoExpediente['path_expediente']!=null){                            
								$expediente .= '<div class="col-xl-4" style="text-align: center;">
													<a class="btn btn-primary" href="'.($infoExpediente['path_expediente']).'" download>DESCARGAR EXPEDIENTE <i class="fal fa-download"></i></a>
												</div>';
							}
							$hasSolActivo = $this->m_utils->getCountExcesoPdt($itemplan);
							if($solicitudPartAdic['estado'] == 0){
								if($hasSolActivo > 0){
									$contBotones = '<div class="col-xl-12">
														<h4 class="text-center" style="color:red">Obra con Solicitud de Exceso PDT de aprobacion.</h4>
													</div>';
								}else {
									$contBotones .= $expediente.'
										<div class="col-xl-4" style="text-align: center;">
											<button data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-idEs="'.$row['idEstacion'].'" data-item="'.$itemplan.'" class="btn btn-success valNi1" type="button">APROBAR PROPUESTA</button>
										</div>
										<div class="col-xl-4" style="text-align: center;">
											<button data-from="1" data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-idEs="'.$row['idEstacion'].'" data-item="'.$itemplan.'" class="btn btn-danger rejectSol" type="button">RECHAZAR</button>
										</div>';
								}
							}else if($solicitudPartAdic['estado'] == 1){
								//$contBotones .= '<div><p style="color:red">En mantenimiento, comunicarse con c. zavala</p></div>';
                                if($hasSolActivo > 0){
									$contBotones = '<div class="col-xl-12">
														<h4 class="text-center" style="color:red">Obra con Solicitud de Exceso PDT de aprobacion.</h4>
													</div>';
								}else {
									$contBotones .= $expediente.'
										<div class="col-xl-8" style="TEXT-ALIGN: CENTER;">
												<button data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-idEs="'.$row['idEstacion'].'" data-item="'.$itemplan.'" class="btn btn-success gpoMo" type="button">APROBAR PROPUESTA</button>
										</div>';
								}								
							}
						}
					}
					$htmlContUl .= '</ul>';
					$htmlContTab .= '</div>';
					$htmlFinal .= $htmlContUl.$htmlContTab.'</div>';

					$infoCertiEdicionOC = $this->m_bandeja_valida_obra->getDataToSolicitudEdicionCertiOC($itemplan, $idEstacion);

					$htmlFinal .= '<div class="tab-content p-3">
										<div class="row">
										'.$contBotones.'
										</div>
									</div>
									<div class="tab-content p-3">
										<div class="row">
											<div class="col-xl-6">
												<h3 class="text-center">Presupuesto Actual MO: S./'.number_format($infoCertiEdicionOC['costo_unitario_mo'],2).'</h3>
											</div>
											<div class="col-xl-6">
												<h3 class="text-center">Nuevo Costo MO: S./' .number_format($infoCertiEdicionOC['total'],2).'</h3>
											</div>
										</div>
									</div>';
									
				}else{
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
						$htmlContUl .= '<li class="nav-item">
											<a class="nav-link '.$liActive.'" data-toggle="tab" href="#tab_'.$row['idEstacion'].'" role="tab">
												'.$row['estacionDesc'].'
											</a>
										</li>';

						$htmlContTab .= '<div class="tab-pane fade '.$tabActive.'" id="tab_'.$row['idEstacion'].'" role="tabpanel">
											<p style="color: #007bff; text-align: left;font-weight: bold;">Partidas Paquetizadas</p>
												'.$contPartidasPqt['html'].'
											<p style="color: #007bff; text-align: left;font-weight: bold;">Partidas Adicionales</p>
												'. $contPartidasAdic['html'].'
										</div>';

						$contBotones = '<div class="col-xl-12">
										</div>';
						$solicitudPartAdic = $this->m_detalle_consulta->getSolicitudPartidasAdicionales($itemplan, $row['idEstacion']);
						if($solicitudPartAdic != null){
							$infoExpediente = $this->m_bandeja_valida_obra->getInfoExpedienteLiquidacion($itemplan, $idEstacion);
							$expediente = '';
							$hasSolRecha = $this->m_bandeja_valida_obra->hasSolicituExoeRechazada($itemplan);
							if($hasSolRecha > 0){
								$expediente .= '<div class="col-xl-12">
													<h4 class="text-center" style="color:red">La Solicitud previa fue rechazada, puede ver el Motivo <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1 getRechazado2Bucles" data-esta="'.$row['idEstacion'].'" data-item="'.$itemplan.'" title="Movimientos"> <i class="fal fa-search"></i></a></h4>
												</div>';
							}
							if($infoExpediente['path_expediente']!=null){                            
								$expediente .= '<div class="col-xl-4" style="text-align: center;">
													<a class="btn btn-primary" href="'.($infoExpediente['path_expediente']).'" download>DESCARGAR EXPEDIENTE <i class="fal fa-download"></i></a>
												</div>';
							}
							$hasSolActivo = $this->m_utils->getCountExcesoPdt($itemplan);
							if($solicitudPartAdic['estado'] == 0){
								if($hasSolActivo > 0){
									$contBotones = '<div class="col-xl-12">
														<h4 class="text-center" style="color:red">Obra con Solicitud de Exceso PDT de aprobacion.</h4>
													</div>';
								}else {
									$contBotones .= $expediente.'
										<div class="col-xl-4" style="text-align: center;">
											<button data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-idEs="'.$row['idEstacion'].'" data-item="'.$itemplan.'" class="btn btn-success valNi1" type="button">APROBAR PROPUESTA</button>
										</div>
										<div class="col-xl-4" style="text-align: center;">
											<button data-from="1" data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-idEs="'.$row['idEstacion'].'" data-item="'.$itemplan.'" class="btn btn-danger rejectSol" type="button">RECHAZAR</button>
										</div>';
								}
							}else if($solicitudPartAdic['estado'] == 1){
								if($hasSolActivo > 0){
									$contBotones = '<div class="col-xl-12">
														<h4 class="text-center" style="color:red">Obra con Solicitud de Exceso PDT de aprobacion.</h4>
													</div>';
								}else {
									$contBotones .= $expediente.'
										<div class="col-xl-8" style="TEXT-ALIGN: CENTER;">
												<button data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-idEs="'.$row['idEstacion'].'" data-item="'.$itemplan.'" class="btn btn-success gpoMo" type="button">APROBAR PROPUESTA</button>
										</div>
										<!--<div class="col-sm-6 col-md-6" style="TEXT-ALIGN: CENTER;">
												<button style="background-color: red;" data-from="2" data-idSol="'.$solicitudPartAdic['id_solicitud'].'" data-idEs="'.$row['idEstacion'].'" data-item="'.$itemplan.'" class="btn btn-success rejectSol" type="button">RECHAZAR</button>
										</div>-->';
								}
							}
						}
					}
					$htmlContUl .= '</ul>';
					$htmlContTab .= '</div>';
					$htmlFinal .= $htmlContUl.$htmlContTab.'</div>';

					$infoCertiEdicionOC = $this->m_bandeja_valida_obra->getDataToSolicitudEdicionCertiOC($itemplan, $idEstacion);

					$htmlFinal .= '<div class="tab-content p-3">
										<div class="row">
										'.$contBotones.'
										</div>
									</div>
									<div class="tab-content p-3">
										<div class="row">
											<div class="col-xl-6">
												<h3 class="text-center">Presupuesto Actual MO: S./'.number_format($infoCertiEdicionOC['costo_unitario_mo'],2).'</h3>
											</div>
											<div class="col-xl-6">
												<h3 class="text-center">Nuevo Costo MO: S./' .number_format($infoCertiEdicionOC['total'],2).'</h3>
											</div>
										</div>
									</div>';
				}
            }
        }
        return $htmlFinal;
    }
	
	function getPartidasPaquetizadasFTTH($infoItem,$idEstacion){
        $data['error'] = EXIT_ERROR;
        $data['html'] = '';
        try{
          /*  $arrayCosto = $this->m_registro_itemplan_masivo->getCostoxDptoByIdEECCAndSubProy($infoItem['idSubProyecto'], $infoItem['idEmpresaColab'], $infoItem['cantFactorPlanificado']);
            if($arrayCosto == null){
                throw new Exception('LA OBRA NO CUENTA CON UN PRECIO CONFIGURADO PARA EL SUBPROYECTO, CONTRATA Y DPTO.');
            }*/
            $dataPoPqt = $this->m_detalle_consulta->getInfoPoMoPqtLiquidadoByItemplan($infoItem['itemplan'], $idEstacion);
            if($dataPoPqt == null){
                throw new Exception('LA OBRA NO CUENTA CON UNA PO MO PAQUETIZADA.');
            }
            //$infoPartida = $this->m_detalle_consulta->getInfoPartidaByCodPartida($arrayCosto['codigoPartida']);
            //$costoTotal = round($arrayCosto['costo']*$infoItem['cantFactorPlanificado'],2);
			$costoTotal = 0;
            $html = '<div style="margin-left: 5%;margin-right: 5%;">
						<p style="font-size: larger;color: #007bff;text-align: left;font-weight: bold;">Partidas de la PO Paquetizada</p>
							<table id="tbPartidasPqt" class="table table-bordered table-hover table-striped w-100">
							<thead class="bg-primary-600">
								<tr> 
									<th>CÓDIGO PARTIDA</th>
									<th>NOMBRE PARTIDA</th>                            
									<th>BAREMO</th>                                
									<th>PRECIO</th>
									<th>CANTIDAD</th>
									<th>TOTAL</th> 
								</tr>
							</thead>
							<tbody>';
							$listaPartidasPqt = $this->m_utils->getDataPoDetalleMo(null, $dataPoPqt['codigo_po'], array(0));
							foreach($listaPartidasPqt as $row){
						  $html .=   '<tr>
										<td>'.$row['codigoPartida'].'</td>
										<td>'.$row['nomPartida'].'</td>
										<td>'.$row['baremo'].'</td>
										<td>'.$row['preciario'].'</td>
										<td>'.$row['cantidadFinal'].'</td>
										<td>'.number_format($row['montoFinal'],2).'</td>   
									</tr>';
									$costoTotal	= $costoTotal +$row['montoFinal'];
							}
				$html .=   '</tbody>
							<tfoot class="bg-primary-600">
								<tr>
									<th colspan="5" style="text-align: right;">TOTAL:</th>
									<th>'.number_format($costoTotal,2).'</th>									 
								</tr>
							</tfoot>
						</table>
					</div>';
	/*
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
            */
            
            $data['html'] = $html;
            $data['codigoPO'] = $dataPoPqt['codigo_po'];
            //$data['NewdetallePO'] = $arrayDetalleInsert;
            $data['costoTotalPqt'] = $costoTotal;
        
        }catch(Exception $e){
            $data['html'] = $e->getMessage();
        }

        return $data;
    }

    function getPartidasPaquetizadas($infoItem,$idEstacion){
        $data['error'] = EXIT_ERROR;
        $data['html'] = '';
        try{
            $arrayCosto = $this->m_registro_itemplan_masivo->getCostoxDptoByIdEECCAndSubProy($infoItem['idSubProyecto'], $infoItem['idEmpresaColab'], $infoItem['cantFactorPlanificado']);
            if($arrayCosto == null){
                throw new Exception('LA OBRA NO CUENTA CON UN PRECIO CONFIGURADO PARA EL SUBPROYECTO, CONTRATA Y DPTO.');
            }
            $dataPoPqt = $this->m_detalle_consulta->getInfoPoMoPqtLiquidadoByItemplan($infoItem['itemplan'], $idEstacion);
            if($dataPoPqt == null){
                throw new Exception('LA OBRA NO CUENTA CON UNA PO MO PAQUETIZADA.');
            }
            $infoPartida = $this->m_detalle_consulta->getInfoPartidaByCodPartida($arrayCosto['codigoPartida']);
            $costoTotal = round($arrayCosto['costo']*$infoItem['cantFactorPlanificado'],2);
            $html = '<table id="tbPartidasPqt" class="table table-bordered table-hover table-striped w-100">
                        <thead class="bg-primary-600">
                            <tr> 
                                <th>CÓDIGO PARTIDA</th>
                                <th>NOMBRE PARTIDA</th>                            
                                <th>BAREMO</th>
                                <th>CANTIDAD</th>
                                <th>PRECIO</th>
                                <th>TOTAL</th>
                                <th>STATUS</th>
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
                                <td>'.''.'</td>       
                            </tr>
                        </tbody>
                        <tfoot class="bg-primary-600">
                            <tr>
                                <th colspan="5" style="text-align: right;">TOTAL:</th>
                                <th>'.number_format($costoTotal,2).'</th>
                                <th></th>
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
            $html = '<table id="tbPartidasAdic" class="table table-bordered table-hover table-striped w-100">
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

    function validarPartidasNivel1() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
            $itemplan = ($this->input->post('itemplan')=='') ? null : $this->input->post('itemplan');
            $idEstacion = ($this->input->post('idEstacion')=='') ? null : $this->input->post('idEstacion');
            $idSolicitud  = ($this->input->post('idSolicitud')=='') ? null : $this->input->post('idSolicitud');
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan.');
            }
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación.');
            }
            if($idSolicitud == null){
                throw new Exception('Hubo un error al recibir la soliticud.');
            }
            $fechaActual = $this->m_utils->fechaActual();

            $dataUpdate = array (
                'estado' => 1,
                'usua_val_nivel_1' => $idUsuario,
                'fec_val_nivel_1' => $fechaActual,
                'id_solicitud'  =>  $idSolicitud
            );
            $data = $this->m_detalle_consulta->validateNivel1($dataUpdate, $idSolicitud);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();
            $data['tablaValidacionObra'] = $this->getTablaConsulta($itemplan, $idUsuario, null);

        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function rechazarSolicitud() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
            $itemplan = ($this->input->post('itemplan')=='') ? null : $this->input->post('itemplan');
            $idEstacion = ($this->input->post('idEstacion')=='') ? null : $this->input->post('idEstacion');
            $idSolicitud  = ($this->input->post('idSolicitud')=='') ? null : $this->input->post('idSolicitud');
            $comentario  = ($this->input->post('comentario')=='') ? null : $this->input->post('comentario');
            $from  = ($this->input->post('from')=='') ? null : $this->input->post('from');
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan.');
            }
            log_message('error', 'estacion:'.$idEstacion);
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación.');
            }
            if($idSolicitud == null){
                throw new Exception('Hubo un error al recibir la soliticud.');
            }
            if($from == null){
                throw new Exception('Hubo un error al recibir el origen de solicitud de rechazo.');
            }
            if($comentario == null){
                throw new Exception('Hubo un error el comentario para rechazar la soliticud.');
            }
            $fechaActual = $this->m_utils->fechaActual();

            $dataUpdate = array (
                'estado' => ($from == 1 ? 3 : 4),
                'usua_val_nivel_1' => $idUsuario,
                'fec_val_nivel_1' => $fechaActual,
                'id_solicitud'  =>  $idSolicitud,
                'comentario' => $comentario
            );
            $estaciones_con_expe = array();
            if($idEstacion == ID_ESTACION_FO){
                $estaciones_con_expe = $this->m_bandeja_valida_obra->getEstaTrabajadasConExpeFO($itemplan);
            }else if($idEstacion == ID_ESTACION_COAX){
                $estaciones_con_expe = $this->m_bandeja_valida_obra->getEstaTrabajadasConExpeCOAX($itemplan);
            }
            $data_expediente_update = array();
            foreach($estaciones_con_expe as $row){
                $expediente_in  =   array(
                    'id' => $row->id,
                    'estado' => 'DEVUELTO',
                    'estado_final' => 'DEVUELTO',
                    'usuario_valida' => $idUsuario,
                    'fecha_valida' => $fechaActual
                );
                array_push($data_expediente_update, $expediente_in);                                        
            }
            $data = $this->m_bandeja_valida_obra->rechazarSolicitud($dataUpdate, $idSolicitud, $data_expediente_update);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();

        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function validarPartidasNivel2() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
            $itemplan = ($this->input->post('itemplan')=='') ? null : $this->input->post('itemplan');
            $idEstacion = ($this->input->post('idEstacion')=='') ? null : $this->input->post('idEstacion');
            $idSolicitud  = ($this->input->post('idSolicitud')=='') ? null : $this->input->post('idSolicitud');
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan.');
            }
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación.');
            }
            if($idSolicitud == null){
                throw new Exception('Hubo un error al recibir la soliticud.');
            }
            $infoItemplan = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItemplan == null){
                throw new Exception('Hubo un error al traer la información del itemplan.');
            }

            $fechaActual = $this->m_utils->fechaActual();

            $arraySolicitud = array();
            $arrayItemXSolicitud = array();
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
                    if($infoItemplan['idProyecto']  ==  52){//FTTH
                        $listaPosPdtValidar = $this->m_detalle_consulta->getPOToValidateFTTHToFOByItemplan($itemplan);
                    }else{
                        $listaPosPdtValidar = $this->m_detalle_consulta->getPOToValidateToFOByItemplan($itemplan);
                    }                    
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
            }else{
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
                    if($infoItemplan['idProyecto']  ==  52){//FTTH
                        $listaPosPdtValidar = $this->m_detalle_consulta->getPOToValidateFTTHToFOByItemplan($itemplan);
                    }else{
                        $listaPosPdtValidar = $this->m_detalle_consulta->getPOToValidateToFOByItemplan($itemplan);
                    }
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

            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();
         //   $data['tablaValidacionObra'] = $this->getTablaConsulta($itemplan, $idUsuario, null);

        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function validarPropuestaNivel2NoPqt(){
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        $data['cabecera'] = null;
        try{
            $itemplan = ($this->input->post('itemplan')=='') ? null : $this->input->post('itemplan');
            $idEstacion = ($this->input->post('idEstacion')=='') ? null : $this->input->post('idEstacion');
            $idSolicitud  = ($this->input->post('idSolicitud')=='') ? null : $this->input->post('idSolicitud');
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan.');
            }
            /*
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación.');
            }*/
            if($idSolicitud == null){
                throw new Exception('Hubo un error al recibir la soliticud.');
            }
            $infoItemplan = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItemplan == null){
                throw new Exception('Hubo un error al traer la información del itemplan.');
            }

            $fechaActual = $this->m_utils->fechaActual();

            $arraySolicitud = array();
            $arrayItemXSolicitud = array();
            $infoCreateSol = $this->m_detalle_consulta->getInfoSolCreacionByItem($itemplan); //getinfo solicitud de creacion
            if ($infoCreateSol == null) { //si no tiene sol creacion atendida realizamos lo siguiente.              
                $arrayPoUpdate = array();
                $arrayPoInserLogPo = array();
                $listaPosPdtValidar = $this->m_bandeja_valida_obra->getPOToValidateToItemplanNoPqt($itemplan);
               
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
            }else{
                $infoCertiEdicionOC = $this->m_detalle_consulta->getDataToSolicitudEdicionCertiOC($itemplan); //costos mo
                if ($infoCertiEdicionOC == null) {
                    throw new Exception('No se pudo obtener los costos de MO para la obra.');
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
                $listaPosPdtValidar = $this->m_bandeja_valida_obra->getPOToValidateToItemplanNoPqt($itemplan);               
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

            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();
             
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function expediente_liquidacion() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $itemplan = $this->input->post('itemplan');
            $idEstacion = $this->input->post('idEstacion');
            $infoExpediente = $this->m_bandeja_valida_obra->getExpedienteLiquidacion($itemplan, $idEstacion);
            log_message('error', print_r($infoExpediente, true));
            if($infoExpediente==null){
                $data['path'] = 2;
                throw new Exception('No se encontro expediente.');
            }else{
                $path = $infoExpediente['path_expediente'];
                log_message('error', $infoExpediente['path_expediente']);
                if (file_exists($path)) {
                    $data['path'] = 1;
                    $data['ruta'] = $path;
                } else {
                    $data['path'] = 2;
                }
            }            
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function liquidacion() {
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

    public function liquidacion_download() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $path = 'uploads/evidencia_liquidacion/' . $filename . '/';
        $this->zip->read_dir($path, false);
        $this->zip->download($filename . '.zip');
    }

    public function licencias() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $filename = $this->input->post('itemPlan');
            $path = $this->m_bandeja_valida_obra->getLicencias($filename);
//            log_message('error', 'itemPlan: ' . $filename);
//            log_message('error', 'count: ' . count($path));
            log_message('error', print_r($path, true));
            if (count($path) == 0) {
                $data['path'] = 2;
            } else {
                $data['path'] = 1;
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function licencias_download() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $dataArray = array();
        $path = $this->m_bandeja_valida_obra->getLicencias($filename);
        foreach ($path as $row) {
//            $dataArray[] = $row['ruta_pdf'];
            $this->zip->read_file($row['ubicacion_evidencia']);
        }
        log_message('error', print_r($dataArray, true));
        $this->zip->download($filename . '.zip');
    }

    public function disenho() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $filename = $this->input->post('itemPlan');
            $path = 'uploads/evidencia_diseno/' . $filename . '/';
            log_message('error', $path);
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

    public function disenho_download() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $path = 'uploads/evidencia_diseno/' . $filename . '/';
        $this->zip->read_dir($path, false);
        $this->zip->download($filename . '.zip');
    }

}
