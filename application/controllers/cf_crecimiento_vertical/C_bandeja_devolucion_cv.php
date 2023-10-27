<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_bandeja_devolucion_cv extends CI_Controller {

    var $login;

    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_crecimiento_vertical/m_bandeja_devolucion_cv');
        $this->load->model('mf_crecimiento_vertical/m_agenda_cv');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){
            $data['cmbBandaHoraria'] = __cmbHTML2(__buildComboBandaHorariaCV(), 'selectBandaHoraria', null, 'select2 form-control w-100', 'Banda Horaria', null, null);           
            $data['cmbMotivoCierre'] = __cmbHTML2(__buildComboMotivoCVSC(), 'selectMotivoCierre', null, 'select2 form-control w-100', 'Motivo Cierre', null, null);
            $data['tablaData'] = $this->getHTMLTablaDevolucion($this->m_bandeja_devolucion_cv->getObrasToBandejaDevolucion());

            $permisos = $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 7, null, 9, ID_MODULO_DESPLIEGUE_PLANTA);
            $data['opciones'] = $result['html'];
            $this->load->view('vf_crecimiento_vertical/v_bandeja_devolucion_cv',$data);        	  
    	 }else{
        	 redirect('login','refresh');
	    }     
    }

    function getHTMLTablaDevolucion($listaDevolucion) {
        
        $html = '<table id="tb_devolucion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-500">
                        <tr>
                            <th>ACCIÓN</th>
							<th>ITEMPLAN</th>
                            <th>ORDEN COMPRA</th>
							<th>SUBPROYECTO</th>
                            <th>NOMBRE PLAN</th>
                            <th>EECC</th>
                            <th>FASE</th>  
                            <th>ESTADO PLAN</th>                                     
							<th>ESTADO</th>
							<th>SITUACIÓN</th>
                        </tr>
                    </thead>                    
                    <tbody>';
             if($listaDevolucion != null){                                                                        
                foreach($listaDevolucion as $row){
                    $actions = '';
					if($row->cantSinContacto > 0){
						$actions .= '<a onclick="openModalLog(this)"  data-itemplan="' . $row->itemplan . '" class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Ver Registros">
                                        <i class="fal fa-eye"></i>
                                    </a>';
					}

                    if($row->estado ==  1) {
                        $actions .= '<a onclick="openModalAgenda(this)" data-id="'.$row->id.'" data-ip="'.$row->itemplan.'" data-eecc="'.$row->empresaColabDesc.'" data-subproy="'.$row->subProyectoDesc.'"
                                        class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Agendar">
                                        <i class="fal fa-calendar-week"></i>
                                    </a>
                                    <a onclick="openModalCerrarObra(this)" data-id="'.$row->id.'" data-ip="'.$row->itemplan.'" class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Cerrar Obra">
                                        <i class="fal fa-times-circle"></i>
                                    </a>
                                    ';
                        // $actions .= '<div class="dropdown d-inline-block">
                        //                 <a href="#" class="btn btn-sm btn-outline-primary btn-icon" data-toggle="dropdown" aria-expanded="true" title="Más opciones"><i class="fal fa-plus"></i></a>
                        //                 <div class="dropdown-menu">
                        //                     <a class="dropdown-item" href="javascript:void(0);">Agendar</a>
                        //                     <a class="dropdown-item" href="javascript:void(0);">Cerrar Obra</a>
                        //                 </div>
                        //             </div>';
                    }

					// if($row->estado ==  1) {
					// 	$actions = '<div class="actions listview__actions">
					// 					<div class="dropdown actions__item">
					// 						<i class="zmdi zmdi-settings" data-toggle="dropdown"></i>
					// 						<div class="dropdown-menu dropdown-menu-left">
					// 							<a class="dropdown-item reage_ci" data-idBa='.$row->id.' data-ip="'.$row->itemplan.'" >Agendar</a>
					// 							<a class="dropdown-item can_ci" data-idBa='.$row->id.' data-ip="'.$row->itemplan.'" >Cerrar Obra</a>
					// 						</div>
					// 					</div>
					// 					'.$btnLog.'
					// 				</div>	';
					// }else{
					// 	$actions = '<div class="actions listview__actions">
					// 					'.$btnLog.'
					// 				</div>	';
					// }
                    
                    $html .=' <tr>         
                                    <td>
                                        <div class="d-flex demo">
										    '.$actions.'
                                        </div>                                       
                                    </td>                  
        							<td>'.$row->itemplan.'</td>			
    							    <td>'.$row->orden_compra.'</td>				
        							<td>'.$row->subProyectoDesc.'</td>
									<td>'.$row->nombrePlan.'</td>
								    <td>'.$row->empresaColabDesc.'</td>
								    <td>'.$row->faseDesc.'</td>
							        <td>'.$row->estadoPlanDesc.'</td>
                                    <td>'.$row->estadoDesc.'</td>
        							<td>'.$row->situacion.'</td>
                            </tr>';
                }
             }
            $html .='</tbody>
                </table>';
                    
            return $html;
    }

    public function cerrarObra(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{

            $idBanDev = $this->input->post('idBandeja') ? $this->input->post('idBandeja') : null;
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idMotivo = $this->input->post('selectMotivoCierre') ? $this->input->post('selectMotivoCierre') : null;
            $comentario = $this->input->post('txtComentario') ? $this->input->post('txtComentario') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($idBanDev == null){
                throw new Exception('Hubo un error al recibir la devolución');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            if($idMotivo == null){
                throw new Exception('Hubo un error al recibir el motivo');
            }
            if($comentario == null){
                throw new Exception('Hubo un error al recibir el comentario');
            }

            $infoITem = $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('No se pudo obtener información del itemplan, inténtelo de nuevo !!');
            }

            $infoCreateSol = $this->m_bandeja_devolucion_cv->getInfoSolCreacionByItem($itemplan);
            $fechaActual = date("Y-m-d H:i:s");
            if($infoCreateSol != null){
                if($infoCreateSol['estado'] == 2){//ATENDIDO
                    $codigo_solicitud = $this->m_utils->getNextCodSolicitud();//obtengo codigo unico de solicitud
                    $solicitud_oc_anulacion = array(
                        'codigo_solicitud'  =>  $codigo_solicitud,
                        'idEmpresaColab'    =>  $infoCreateSol['idEmpresaColab'],
                        'estado'            =>  1,//pendiente
                        'fecha_creacion'    =>  $fechaActual,
                        'idSubProyecto'     =>  $infoCreateSol['idSubProyecto'],
                        'plan'              =>  $infoCreateSol['plan'],
                        'codigoInversion'   => 	$infoCreateSol['codigoInversion'],
                        'estatus_solicitud' => 'NUEVO',
                        'tipo_solicitud'    =>  4,// 1= CREACION, 2 = EDICION, 3 = CERTIFICACION, 4 = ANULACION
                        'usuario_creacion'  =>  $idUsuario,
                        'fecha_creacion'    =>  $fechaActual
                    );
                    $item_x_sol = array(
                        'itemplan'            =>  $itemplan,
                        'codigo_solicitud_oc' =>  $codigo_solicitud,
                        'costo_unitario_mo'   =>  $infoCreateSol['costo_unitario_mo']
                    );
                    
                    $dataPlanobra = array(	
                        'itemplan' => $itemplan,
                        'solicitud_oc_anula_pos' => $codigo_solicitud,
                        'costo_unitario_mo_anula_pos' => $infoCreateSol['costo_unitario_mo'],
                        'estado_oc_anula_pos' => 'PENDIENTE'
                    );

                    $data = $this->m_bandeja_devolucion_cv->registrarSolAnulacion($dataPlanobra, $solicitud_oc_anulacion, $item_x_sol);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }
                }else{//PENDIENTE
                    $dataSolicitud = array(
                        'codigo_solicitud'  =>  $infoCreateSol['codigo_solicitud'],
                        'usuario_valida'    =>  $idUsuario,
                        'fecha_valida'      =>  $fechaActual,
                        'estado'            =>  3,//cancelado
                        'usuario_cancela'   =>  $idUsuario,
                        'fecha_cancelacion' =>  $fechaActual,
                        'motivo_cancela'    =>  'CIERRE OBRA - BANDEJA DEVOLUCIÓN CV'
                    );

                    $dataPlanobra = array(	
                        'itemplan' => $itemplan,
                        'estado_sol_oc' => 'CANCELADO'
                    );

                    $data = $this->m_bandeja_devolucion_cv->cancelarSolCreacion($dataPlanobra, $dataSolicitud);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }
                }
            }

            $dataDevo = array(  
                'id'            =>  $idBanDev,
                'accion'        =>  2,//reagendado
                'estado'        =>  2,
                'fecha_valida'  =>  $fechaActual,
                'usuario_valida'=>  $idUsuario
            );
            $dataPlanobra = array(	
                'itemplan'      => $itemplan,
                'idEstadoPlan'  => ID_ESTADO_PLAN_SUSPENDIDO,
                'idUsuarioLog'  => $idUsuario,
                'fechaLog'      => $fechaActual,
                'descripcion'   => 'OBRA CERRADA - BANDEJA DE DEVOLUCIÓN',
                'estado_ant_suspendido' => $infoITem['idEstadoPlan']
            );

            $data = $this->m_bandeja_devolucion_cv->cerrarObraBanDev($dataPlanobra, $dataDevo);
			if($data['error'] == EXIT_ERROR){
				throw new Exception($data['msj']);
			}

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se cerró la obra exitosamente!!';
                $data['tablaData'] = $this->getHTMLTablaDevolucion($this->m_bandeja_devolucion_cv->getObrasToBandejaDevolucion());
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function agendarCita(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{

            $idBanDev = $this->input->post('idBandeja') ? $this->input->post('idBandeja') : null;
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $fechaCita = $this->input->post('txtFechaCita') ? $this->input->post('txtFechaCita') : null;
            $bandaHoraria = $this->input->post('selectBandaHoraria') ? $this->input->post('selectBandaHoraria') : null;             
            $contacto = $this->input->post('contacto') ? $this->input->post('contacto') : null;            
            $telefono_1 = $this->input->post('txtTelefono1') ? $this->input->post('txtTelefono1') : null;
            $telefono_2 = $this->input->post('txtTelefono2') ? $this->input->post('txtTelefono2') : null;
            $correo = $this->input->post('txtCorreo') ? $this->input->post('txtCorreo') : null;
            $comentario = $this->input->post('txtComentario') ? $this->input->post('txtComentario') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

            if($idBanDev == null){
                throw new Exception('Hubo un error al recibir la devolución');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            
            $havePndt = $this->m_agenda_cv->haveCitaPendiente($itemplan);	
            if($havePndt > 0){
                throw new Exception('El itemplan ya cuenta con una cita pendiente de atencion.');
            }

            $fechaCita = _convertirFechaDatePicker($fechaCita);

            $infoITem = $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('No se pudo obtener información del itemplan, inténtelo de nuevo !!');
            }
            $fechaActual = date("Y-m-d H:i:s");
            $infoBH = $this->m_agenda_cv->getInfoBandaHorariaByID($bandaHoraria);           
            
            $arrayCitaItem = array(
                'itemplan'              =>  $itemplan,
                'fecha_registro'        =>  $fechaActual,
                'usuario_registro'      =>  $idUsuario,
                'fecha_cita'            =>  $fechaCita,
                'banda_horaria_inicio'  =>  $infoBH['horaInicio'],
                'banda_horaria_fin'     =>  $infoBH['horaFin'],
                'contacto'              =>  $contacto,
                'telefono_1'            =>  $telefono_1,
                'telefono_2'            =>  $telefono_2,
                'correo'                =>  $correo,
                'estado'                =>  1
            );
            
            $dataDevo = array(  
                'id' =>  $idBanDev,
                'accion'        =>  1,//reagendado
                'estado'        =>  2,
                'fecha_valida'  =>  $fechaActual,
                'usuario_valida'=>  $idUsuario
            );
            $responseInsertCita = $this->m_agenda_cv->createCVCita($arrayCitaItem);
            if($responseInsertCita['error'] == EXIT_ERROR){
                throw new Exception('No se pudo generar la cita, refresque la pantalla y vuelva a intentarlo.');
            }

            $responseUpdate = $this->m_bandeja_devolucion_cv->updateBandejaDev($dataDevo);
            if($responseUpdate['error'] == EXIT_ERROR){
                throw new Exception($responseUpdate['msj']);
            }

            $data['error'] = $responseUpdate['error'];

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se agendó la obra exitosamente!!';
                $data['tablaData'] = $this->getHTMLTablaDevolucion($this->m_bandeja_devolucion_cv->getObrasToBandejaDevolucion());
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

}
