<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_agenda_edificios extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_crecimiento_vertical/m_agenda_cv');
		$this->load->model('mf_orden_compra/m_bandeja_solicitud_oc');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){
            $data['cmbBandaHoraria'] = __cmbHTML2(__buildComboBandaHorariaCV(), 'selectBandaHoraria', null, 'select2 form-control w-100', 'Banda Horaria', null, null);
            $data['cmbBandaHoraria2'] = __cmbHTML2(__buildComboBandaHorariaCV(), 'selectBandaHoraria2', null, 'select2 form-control w-100', 'Banda Horaria', null, null);
            $data['cmbMotivoCancela'] = __cmbHTML2(__buildComboMotivoCancelaCita(), 'selectMotivoCance', null, 'select2 form-control w-100', 'Motivo Cancelación', null, null);
            $data['cmbMotivoReagenda'] = __cmbHTML2(__buildComboMotivoReagendaCita(), 'selectMotivoReagenda', null, 'select2 form-control w-100', 'Motivo Reagenda', null, null);
            $data['cmbMotivoSC'] = __cmbHTML2(__buildComboMotivoCVSC(), 'selectMotivoSC', null, 'select2 form-control w-100', 'Motivo Sin Contacto', null, null);

            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 7, null, 8, ID_MODULO_DESPLIEGUE_PLANTA);
            $data['opciones'] = $result['html'];
            $this->load->view('vf_crecimiento_vertical/v_agenda_edificios',$data);        	  
    	 }else{
        	 redirect('login','refresh');
	    }     
    }

    public function getBasicInfoItemplan(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            $itemplan = $this->input->post('itemplan');
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            $infoITem = $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('Itemplan invalido o no existe valide bien la informacion.');
            }
            $data['subproyecto'] = $infoITem['subProyectoDesc'];
            $data['eecc'] = $infoITem['empresaColabDesc'];
            $data['error'] = EXIT_SUCCESS;

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function createCitaCV(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{

            $inputJSON = file_get_contents('php://input');
            $input = json_decode( $inputJSON, TRUE );
            $itemplan = $input['txtItemplan'];
            $eecc = $input['txtEECC'];
            $subproyecto = $input['txtSubProyecto'];
            $fechaCita = $input['txtFechaCita'];
            $bandaHoraria = $input['selectBandaHoraria'];             
            $contacto = $input['contacto1'];             
            $telefono_1 = $input['txtTelefono1'];
            $telefono_2 = isset($input['txtTelefono2']) ? $input['txtTelefono2'] : null;
            $correo = isset($input['txtCorreo']) ? $input['txtCorreo'] : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            
            $havePndt = $this->m_agenda_cv->haveCitaPendiente($itemplan);	
            if($havePndt > 0){
                throw new Exception('El itemplan ya cuenta con una cita pendiente de atencion.');
            }
            
            $existBDev = $this->m_agenda_cv->existOnBandejaDevolucion($itemplan);
            if($existBDev > 0){
                throw new Exception('El itemplan se encuentra en la Bandeja de Devolucion');
            }
            $fechaCita = _convertirFechaDatePicker($fechaCita);
            _log($fechaCita);

            $infoITem = $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('No se pudo obtener información del itemplan, inténtelo de nuevo !!');
            }
            
            $infoBH = $this->m_agenda_cv->getInfoBandaHorariaByID($bandaHoraria);           
            $fechaActual = date("Y-m-d h:i:s");

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
            
            // $fecha_inicio_cita = $fechaCita+' '+$infoBH['horaInicio'];
            $dataInsert = array(  
                'itemplan'              =>  $itemplan,
                'idEstadoPlan'          =>  $infoITem['idEstadoPlan'],
                'usuario_registro'      =>  $idUsuario,
                'fecha_registro'        =>  $fechaActual,
                'id_motivo_seguimiento' =>  32,
                'comentario_incidencia' =>  'AUTOMÁTICO, MODULO AGENDAMIENTO'
            ); 
            $responseInsertCita = $this->m_agenda_cv->createCVCita($arrayCitaItem);
            if($responseInsertCita['error'] == EXIT_ERROR){
                throw new Exception('No se pudo generar la cita, refresque la pantalla y vuelva a intentarlo.');
            }

            $responseInsertSegui = $this->m_agenda_cv->createSeguimientoCV($dataInsert);
            if($responseInsertSegui['error'] == EXIT_ERROR){
                throw new Exception($responseInsertSegui['msj']);
            }

            $data['error'] = $responseInsertSegui['error'];

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se registró exitosamente la cita!!';
                $data['itemplan'] = $itemplan;
                $data['titulo'] =  $infoITem['itemplan'].' | '.$infoITem['subProyectoDesc'].' | '.$infoITem['empresaColabDesc'];
                $data['htmlCitas'] = $this->makeHtmlCitasList($this->m_agenda_cv->getDetalleCitasByItemplan($itemplan));
            }

            // $data['itemListSearch'] = $this->makeHtmlItemList($this->m_agenda_cv->getItemListPendiente());                
            // $data['citasList']  = $this->makeHtmlCitasListByListCitas($this->m_agenda_cv->getDetalleCitasByItemplan($itemplan));

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function geDetalleCitas(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $itemplan = $this->input->post('itemplan');
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }                   
            
            $infoITem =  $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('No se pudo obtener informacion del itemplan,refresque la pagina y vuelva a intentarlo..');
            }
            $data['titulo'] =  $infoITem['itemplan'].' | '.$infoITem['subProyectoDesc'].' | '.$infoITem['empresaColabDesc'];
            $data['htmlCitas'] = $this->makeHtmlCitasList($this->m_agenda_cv->getDetalleCitasByItemplan($itemplan));
            $data['error'] =  EXIT_SUCCESS;

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function liquidarCitaCV(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{

            $idCita = $this->input->post('idCita') ? $this->input->post('idCita') : null;
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $contacto = $this->input->post('contacto3') ? $this->input->post('contacto3') : null;
            $comentario = $this->input->post('txtComentario2') ? $this->input->post('txtComentario2') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($idCita == null){
                throw new Exception('Hubo un error a recibir la cita');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            if($contacto == null){
                throw new Exception('Hubo un error al recibir el contacto');
            }
            if($comentario == null){
                throw new Exception('Hubo un error al recibir el comentario');
            }

            $infoITem = $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('No se pudo obtener información del itemplan, inténtelo de nuevo !!');
            }

            $fechaActual = $this->m_utils->fechaActual();

            $dataUpdate = array(  
				'id_agenda_cv_item'      =>  $idCita,								
				'estado'                 =>  4,
				'usuario_ultimo_estado'  =>  $idUsuario,
				'fecha_ultimo_estado'    =>  $fechaActual,
				'comentario_liquida'     =>  $comentario,
				'contacto_liquida'       =>  $contacto
			);

            $data =  $this->m_agenda_cv->updateCita($dataUpdate);
			if($data['error'] == EXIT_ERROR){
				throw new Exception($reponseUpdate['msj']);
			}

            $dataInsert = array(  
                'itemplan'              =>  $itemplan,
                'idEstadoPlan'          =>  $infoITem['idEstadoPlan'],
                'usuario_registro'      =>  $idUsuario,
                'fecha_registro'        =>  $fechaActual,
                'id_motivo_seguimiento' =>  33,
                'comentario_incidencia' =>  'AUTOMÁTICO, MODULO AGENDAMIENTO'
            );
            $data = $this->m_agenda_cv->createSeguimientoCV($dataInsert);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $arraySubProyNuevoFlujo = _getArrayIDSubProyNuevoFlujoCV();
            if(in_array($infoITem['idSubProyecto'],$arraySubProyNuevoFlujo) && $infoITem['idEstadoPlan'] == ID_ESTADO_PLAN_PRE_REGISTRO && $infoITem['flg_tiene_oc'] == 1){
                $arrayUpdateIP = array(
					"idEstadoPlan" => ID_ESTADO_PLAN_DISENIO,
					"idUsuarioLog" => $idUsuario,
                    "fechaLog"     => $fechaActual,
                    "descripcion"  => 'LIQUIDACIÓN CITA'
				);
				$data = $this->m_utils->actualizarPlanObra($itemplan, $arrayUpdateIP);
				if($data['error'] == EXIT_ERROR){
					throw new Exception($data['msj']);
				}
                $dataInsert['id_motivo_seguimiento'] = 28;
				$dataInsert['idEstadoPlan'] = ID_ESTADO_PLAN_DISENIO;
				$data = $this->m_agenda_cv->createSeguimientoCV($dataInsert);
				if($data['error'] == EXIT_ERROR){
					throw new Exception($data['msj']);
				} 
                $disenoList = array();
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
                            // 'estado'                  => (($infoITem['idSubProyecto'] == 722) ? 6 : ID_ESTADO_PLAN_DISENIO),
                            'estado'                  => ID_ESTADO_PLAN_DISENIO,
                            'fecha_registro'          => $fechaActual,
                            'usuario_registro'        => $idUsuario,     
                            'fecha_adjudicacion'	  => $fechaActual,
                            'usuario_adjudicacion'    => 'AGENDAMIENTO EXITOSO',
                            'fecha_prevista_atencion' => $fechaPreAtencion
                        );				
                        $disenoList[] = $infoAdjudicacion;
                    }

                    if($has_coax){
                        $infoAdjudicacion = array ( 
                            'itemplan'                => $itemplan,
                            'idEstacion'              => ID_ESTACION_COAX,
                            // 'estado'                  => (($infoITem['idSubProyecto'] == 722) ? 6 : ID_ESTADO_PLAN_DISENIO),
                            'estado'                  => ID_ESTADO_PLAN_DISENIO,
                            'fecha_registro'          => $fechaActual,
                            'usuario_registro'        => $idUsuario,     
                            'fecha_adjudicacion'	  => $fechaActual,
                            'usuario_adjudicacion'    => 'AGENDAMIENTO EXITOSO',
                            'fecha_prevista_atencion' => $fechaPreAtencion
                        );				
                        $disenoList[] = $infoAdjudicacion;
                    }

                    if(count($disenoList) > 0){
                        $data = $this->m_bandeja_solicitud_oc->insertMasiveDiseno($disenoList);
                        if($data['error'] == EXIT_ERROR){
                            throw new Exception($data['msj']);
                        }
                    }
                }

            }

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se liquidó exitosamente la cita!!';
                $data['itemplan'] = $itemplan;
                $data['titulo'] =  $infoITem['itemplan'].' | '.$infoITem['subProyectoDesc'].' | '.$infoITem['empresaColabDesc'];
                $data['htmlCitas'] = $this->makeHtmlCitasList($this->m_agenda_cv->getDetalleCitasByItemplan($itemplan));
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function cancelarCitaCV(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{

            $idCita = $this->input->post('idCita') ? $this->input->post('idCita') : null;
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idMotivo = $this->input->post('selectMotivoCance') ? $this->input->post('selectMotivoCance') : null;
            $comentario = $this->input->post('txtComentario3') ? $this->input->post('txtComentario3') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($idCita == null){
                throw new Exception('Hubo un error a recibir la cita!!');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }
            if($idMotivo == null){
                throw new Exception('Hubo un error al recibir el motivo de cancelación!!');
            }
            if($comentario == null){
                throw new Exception('Hubo un error al recibir el comentario!!');
            }
            if(count($_FILES) == 0){
                throw new Exception('Hubo un error al recibir la evidencia!!');
            }
            
            $infoITem = $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('No se pudo obtener información del itemplan, inténtelo de nuevo!!');
            }

            if (!file_exists("uploads/evidencia_agenda_cv/".$itemplan)) {
                if (!mkdir("uploads/evidencia_agenda_cv/".$itemplan)) {
                    throw new Exception('Hubo un error al crear la carpeta evidencia_agenda_cv!!');
                }
            }
            $rutaFinalArchivo = null;

            if(count($_FILES) > 0){
                $nombreArchivo = $_FILES['file']['name'];
                $tipoArchivo = $_FILES['file']['type'];
                $nombreArchivoTemp = $_FILES['file']['tmp_name'];
                $tamano_archivo = $_FILES['file']['size'];
                $nombreFinalArchivo = date("Y_m_d_His_").$nombreArchivo;
                $rutaFinalArchivo = "uploads/evidencia_agenda_cv/".$itemplan."/".$nombreFinalArchivo;
                if (!move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {
                    throw new Exception('No se pudo subir el archivo: ' . $nombreFinalArchivo . ' !!');
                }
            }
            $fechaActual = date("Y-m-d h:i:s");

            $dataUpdate = array(  
				'id_agenda_cv_item'         =>  $idCita,								
				'estado'                    =>  3,
				'usuario_ultimo_estado'     =>  $idUsuario,
				'fecha_ultimo_estado'       =>  $fechaActual,
				'motivo_cancela'            =>  $idMotivo,
				'comentario_cancela'        =>  $comentario,
                'ruta_evidencia_cancelada'  =>  $rutaFinalArchivo
			);

            $reponseUpdate =  $this->m_agenda_cv->updateCita($dataUpdate);
			if($reponseUpdate['error'] == EXIT_ERROR){
				throw new Exception($reponseUpdate['msj']);
			}

            $dataInsertBanDev = array(        
                'itemplan' => $infoITem['itemplan'],
                'estado' => 1,
                'usuario_registro' => $idUsuario,
                'fecha_registro' => $fechaActual
            );
            $responseInsertBanDev = $this->m_agenda_cv->insertBandejaDev($dataInsertBanDev);
            if($responseInsertBanDev['error'] == EXIT_ERROR){
                throw new Exception($responseInsertBanDev['msj']);
            }
            $data['error'] = $responseInsertBanDev['error'];

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se canceló exitosamente la cita!!';
                $data['itemplan'] = $itemplan;
                $data['titulo'] =  $infoITem['itemplan'].' | '.$infoITem['subProyectoDesc'].' | '.$infoITem['empresaColabDesc'];
                $data['htmlCitas'] = $this->makeHtmlCitasList($this->m_agenda_cv->getDetalleCitasByItemplan($itemplan));
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function reagendarCitaCV(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{

            $idCita = $this->input->post('idCita') ? $this->input->post('idCita') : null;
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $fechaCita = $this->input->post('txtFechaCita3') ? $this->input->post('txtFechaCita3') : null;
            $bandaHoraria = $this->input->post('selectBandaHoraria2') ? $this->input->post('selectBandaHoraria2') : null;             
            $contacto = $this->input->post('contacto4') ? $this->input->post('contacto4') : null;            
            $telefono_1 = $this->input->post('txtTelefono1_3') ? $this->input->post('txtTelefono1_3') : null;
            $telefono_2 = $this->input->post('txtTelefono2_3') ? $this->input->post('txtTelefono2_3') : null;
            $correo = $this->input->post('txtCorreo3') ? $this->input->post('txtCorreo3') : null;
            $idMotivoReagenda = $this->input->post('selectMotivoReagenda') ? $this->input->post('selectMotivoReagenda') : null;
            $comentario = $this->input->post('txtComentario4') ? $this->input->post('txtComentario4') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }

            if(count($_FILES) == 0){
                throw new Exception('Hubo un error al recibir la evidencia!!');
            }
			if (!file_exists("uploads/evidencia_agenda_cv/".$itemplan)) {
                if (!mkdir("uploads/evidencia_agenda_cv/".$itemplan)) {
                    throw new Exception('Hubo un error al crear la carpeta evidencia_agenda_cv/'.$itemplan);
                }
            }
            
            $rutaFinalArchivo = null;

            if(count($_FILES) > 0){
                $nombreArchivo = $_FILES['file2']['name'];
                $tipoArchivo = $_FILES['file2']['type'];
                $nombreArchivoTemp = $_FILES['file2']['tmp_name'];
                $tamano_archivo = $_FILES['file2']['size'];
                $nombreFinalArchivo = date("Y_m_d_His_").$nombreArchivo;
                $rutaFinalArchivo = "uploads/evidencia_agenda_cv/".$itemplan."/".$nombreFinalArchivo;
                if (!move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {
                    throw new Exception('No se pudo subir el archivo: ' . $nombreFinalArchivo . ' !!');
                }
            }

            $fechaCita = _convertirFechaDatePicker($fechaCita);
            $infoBH = $this->m_agenda_cv->getInfoBandaHorariaByID($bandaHoraria); 
            $fechaActual = date("Y-m-d h:i:s");
            
            $infoITem = $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('No se pudo obtener información del itemplan, inténtelo de nuevo !!');
            }

            $dataUpdate = array(  
				'id_agenda_cv_item'         =>  $idCita,								
				'estado'                    =>  2,
				'usuario_ultimo_estado'     =>  $idUsuario,
				'fecha_ultimo_estado'       =>  $fechaActual,
				'motivo_reagenda'           =>  $idMotivoReagenda,
				'comentario_reagenda'       =>  $comentario,
                'ruta_evidencia_reagenda'   =>  $rutaFinalArchivo
			);

            $reponseUpdate =  $this->m_agenda_cv->updateCita($dataUpdate);
			if($reponseUpdate['error'] == EXIT_ERROR){
				throw new Exception($reponseUpdate['msj']);
			}
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

            $responseInsertCita = $this->m_agenda_cv->createCVCita($arrayCitaItem);
            if($responseInsertCita['error'] == EXIT_ERROR){
                throw new Exception('No se pudo generar la cita, refresque la pantalla y vuelva a intentarlo.');
            }

            $data['error'] = $responseInsertCita['error'];

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se reagendó exitosamente la cita!!';
                $data['itemplan'] = $itemplan;
                $data['titulo'] =  $infoITem['itemplan'].' | '.$infoITem['subProyectoDesc'].' | '.$infoITem['empresaColabDesc'];
                $data['htmlCitas'] = $this->makeHtmlCitasList($this->m_agenda_cv->getDetalleCitasByItemplan($itemplan));
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function regSinContacto(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{

            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $fechaCita = $this->input->post('txtFechaCita2') ? $this->input->post('txtFechaCita2') : null;
            $idMotivoSC = $this->input->post('selectMotivoSC') ? $this->input->post('selectMotivoSC') : null;             
            $contacto = $this->input->post('contacto2') ? $this->input->post('contacto2') : null;            
            $telefono = $this->input->post('txtTelefono3') ? $this->input->post('txtTelefono3') : null;
            $comentario = $this->input->post('txtComentario') ? $this->input->post('txtComentario') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            
            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }

            $fechaCita = _convertirFechaDatePicker($fechaCita);
            
            $infoITem = $this->m_agenda_cv->getInfoItemplanByItem($itemplan);
            if($infoITem == null){
                throw new Exception('No se pudo obtener información del itemplan, inténtelo de nuevo !!');
            }
            if($infoITem['flg_tiene_oc'] == 2){
				throw new Exception('La obra no cuenta con una Orden de Compra!.');
			}

            $existBDev = $this->m_agenda_cv->existOnBandejaDevolucion($itemplan);
			if($existBDev > 0){
				throw new Exception('El itemplan se encuentra en la Bandeja de Devolucion');
			}
            $fechaActual = date("Y-m-d h:i:s");

            $arrayCitaItem = array(
				'itemplan'               =>  $itemplan,
				'fecha_registro'         =>  $fechaActual,
				'usuario_registro'       =>  $idUsuario,
				'fecha_cita'             =>  $fechaCita,
				'contacto'               =>  $contacto,
				'telefono_1'             =>  $telefono,
				'id_motivo_sincontacto'  =>  $idMotivoSC,
				'comentario_sincontacto' =>  $comentario,
				'estado'                 =>  5, #sin contacto,
				'usuario_ultimo_estado'  =>  $idUsuario,
                'fecha_ultimo_estado'    =>  $fechaActual
			);

            $responseInsertCita = $this->m_agenda_cv->createCVCita($arrayCitaItem);
            if($responseInsertCita['error'] == EXIT_ERROR){
                throw new Exception('No se pudo generar la cita, refresque la pantalla y vuelva a intentarlo.');
            }
            $data['error'] = $responseInsertCita['error'];
            $arrayValida = $this->m_agenda_cv->getCountSinContactoAndCantConfig($itemplan);
            if($arrayValida['count'] == $arrayValida['cant_config']){

                $dataInsertBanDev =   array(        
                    'itemplan'          =>  $itemplan,
                    'estado'            =>  1,
                    'usuario_registro'  =>  $idUsuario,
                    'fecha_registro'    =>  $fechaActual
                );
                $reponseInsertDevolucion = $this->m_agenda_cv->insertarBandejaDevolucionCV($dataInsertBanDev);
                if($reponseInsertDevolucion['error'] == EXIT_ERROR){
                    throw new Exception($reponseInsertDevolucion['msj']);
                }
                $data['error'] = $reponseInsertDevolucion['error'];
            }

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se registró exitosamente el sin contacto!!';
                $data['itemplan'] = $itemplan;
                $data['titulo'] =  $infoITem['itemplan'].' | '.$infoITem['subProyectoDesc'].' | '.$infoITem['empresaColabDesc'];
                $data['htmlCitas'] = $this->makeHtmlCitasList($this->m_agenda_cv->getDetalleCitasByItemplan($itemplan));
            }

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }



    public function makeHtmlCitasList($listaDatos){
        $color_cabe = '';
        $contraste_cabe = '';
        $body_panel = '';
        $html = '';
        $count = 1;
        $contenido = '';

        if($listaDatos != null){

            foreach($listaDatos as $row){
                $color_cicle = 'bg-white';//default
                $estado_cita = 'ND';
				$contenido = '';
                $contDropDown = '';
                if($row->estado == 1){//PENDIENTE
                    $color_cabe = '#8BC34A';
                    $contraste_cabe = 'linear-gradient(250deg,rgb(9 165 71 / 70%),transparent)';
                    $body_panel = '#8bc34a21';
                    $estado_cita = 'PENDIENTE';
                    $contenido = '';
                    $contDropDown = '   <a onclick="openModalLiquiCita(this)" data-ip="'.$row->itemplan.'" data-ic="'.$row->id_agenda_cv_item.'" class="dropdown-item">
                                            <span data-i18n="drpdwn.refreshpanel">Liquidar</span>
                                        </a>
                                        <div class="dropdown-divider m-0"></div>
                                        <a onclick="openModalReagendarCita(this)" data-ip="'.$row->itemplan.'" data-ic="'.$row->id_agenda_cv_item.'" 
                                            data-eecc="'.$row->empresaColabDesc.'" data-subproy="'.$row->subProyectoDesc.'" class="dropdown-item reage_ci">
                                            <span data-i18n="drpdwn.lockpanel">Re Agendar</span>
                                        </a>   
                                        <div class="dropdown-divider m-0"></div>
                                        <a onclick="openModalCancelarCita(this)" data-ip="'.$row->itemplan.'" data-ic="'.$row->id_agenda_cv_item.'" class="dropdown-item">
                                            <span data-i18n="drpdwn.resetpanel">Devuelto</span>
                                        </a>
                                        <div class="dropdown-divider m-0"></div>';
                }else if($row->estado == 2){//RE AGENDADO
                    $color_cabe = '#f0e916';
                    $contraste_cabe = 'linear-gradient(250deg,rgb(226 220 44 / 70%),transparent)';
                    $body_panel = '#ffc10714';
                    $estado_cita = 'RE AGENDADO';
                    $contenido = '  <h4 class="mb-g">
										DATOS ATENCION
										<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" title="Descargar Evidencia"
                                            href="' . $row->ruta_evidencia_reagenda . '">
                                            <i class="fal fa-download"></i>
                                        </a>
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-user"></i></span>
                                                    '.$row->usuario_update.'
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-calendar"></i></span>
                                                    '.$row->fecha_ultimo_estado.'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="mt-4 mb-g">
                                    <h4 class="mb-g">
                                        DATOS REAGENDADO
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-exclamation-triangle"></i></span>
                                                    '.$row->motivo_reagenda_desc.'
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-comment-alt-lines"></i></span>
                                                    '.$row->comentario_reagenda.'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="mt-4 mb-g">';
                }else if($row->estado == 3){//CANCELADO
                    $color_cabe = '#ee7e2e';
                    $contraste_cabe = 'linear-gradient(250deg,rgb(228 3 3 / 70%),transparent)';
                    $body_panel = '#f3392317';
                    $estado_cita = 'CANCELADO';
                    $contenido = '  <h4 class="mb-g">
                                        DATOS ATENCION
										<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" title="Descargar Evidencia"
                                            href="' . $row->ruta_evidencia_cancelada . '">
                                            <i class="fal fa-download"></i>
                                        </a>
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-user"></i></span>
                                                    '.$row->usuario_update.'
                                                </li>
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-exclamation-triangle"></i></span>
                                                    '.$row->motivo_cancela.'
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-calendar"></i></span>
                                                    '.$row->fecha_ultimo_estado.'
                                                </li>
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-comment-alt-lines"></i></span>
                                                    '.$row->comentario_cancela.'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="mt-4 mb-g">';
                }else if($row->estado == 4){//EJECUTADO / LIQUIDADO
                    $color_cabe = '#39bbb0';
                    $contraste_cabe = 'linear-gradient(250deg,rgb(17 110 105 / 70%),transparent)';
                    $body_panel = '#39bbb01a';
                    $estado_cita = 'EJECUTADO';
                    $contenido = '  <h4 class="mb-g">
                                        DATOS ATENCION
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-user"></i></span>
                                                    '.$row->usuario_update.'
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-calendar"></i></span>
                                                    '.$row->fecha_ultimo_estado.'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="mt-4 mb-g">
                                    <h4 class="mb-g">
                                        DATOS CONTACTO
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-user"></i></span>
                                                    '.$row->contacto_liquida.'
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-comment-alt-lines"></i></span>
                                                    '.$row->comentario_liquida.'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="mt-4 mb-g">';
                }else if($row->estado == 5){//SIN CONTACTO
                    $color_cabe = '#9545b6';
                    $contraste_cabe = 'linear-gradient(250deg,rgb(83 17 110 / 70%),transparent)';
                    $body_panel = '#ad39bb1a';
                    $estado_cita = 'SIN CONTACTO';
					$contenido = '  <h4 class="mb-g">
                                    DATOS ATENCION
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-user"></i></span>
                                                    '.$row->usuario_update.'
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-calendar"></i></span>
                                                    '.$row->fecha_ultimo_estado.'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="mt-4 mb-g">
                                    <h4 class="mb-g">
                                        DATOS SIN CONTACTO
                                    </h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-exclamation-triangle"></i></span>
                                                    '.$row->desc_motivo.'
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="nav d-block fs-md pl-3">
                                                <li>
                                                    <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-comment-alt-lines"></i></span>
                                                    '.$row->comentario_sincontacto.'
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr class="mt-4 mb-g">';
                }
                
                $html .= '  <div id="panel-'.$count.'" class="panel" data-panel-fullscreen="false" style="margin-bottom: -1px">
                                <div class="panel-hdr" style="background-color:'.$color_cabe.'; background-image: '.$contraste_cabe.'">                                    
                                    <h2 style="color:white;">'.$estado_cita.'</h2>
                                    <div class="panel-toolbar" role="menu">
                                        <a href="#" class="btn btn-toolbar-master waves-effect waves-themed" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fal fa-ellipsis-v" style="color:white;"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-animated dropdown-menu-right p-0" x-placement="top-end">
                                            '.$contDropDown.'
                                            <a onclick="mostrarCard(this)" data-id_body="id_body_'.$count.'" class="dropdown-item">
                                                <span>Mostrar Detalle</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content p-0">
                                        <div id="cp-'.$count.'" class="card" style="background-color: '.$body_panel.'; margin-bottom: -1px">
                                            <div id="id_body_'.$count.'" class="card-body collapse" data-flg="0">
                                                <h4 class="mb-g">
                                                    DATOS DE CITA
                                                </h4>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <ul class="nav d-block fs-md pl-3">
                                                            <li>
                                                                <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-calendar"></i></span>
                                                                '.$row->fecha_cita.'
                                                            </li>
                                                            <li>
                                                                <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-clock"></i></span>
                                                                '.$row->banda_horaria_inicio.' - '.$row->banda_horaria_fin.'
                                                            </li>
                                                            <li>
                                                                <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-user"></i></span>
                                                                '.$row->contacto.'
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <ul class="nav d-block fs-md pl-3">
                                                            <li>
                                                                <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-phone"></i></span>
                                                                '.$row->telefono_1.'
                                                            </li>
                                                            <li>
                                                                <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-phone"></i></span>
                                                                '.$row->telefono_2.'
                                                            </li>
                                                            <li>
                                                                <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-envelope-square"></i></span>
                                                                '.$row->correo.'
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <hr class="mt-4 mb-g">
                                                '.$contenido.'
                                                <h4 class="mb-g">
                                                    DATOS DE REGISTRO
                                                </h4>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <ul class="nav d-block fs-md pl-3">
                                                            <li>
                                                                <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-user"></i></span>
                                                                '.$row->usuario_registro.'
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <ul class="nav d-block fs-md pl-3">
                                                            <li>
                                                                <span class="badge badge-success fw-n mr-1 width-3"><i class="fal fa-calendar"></i></span>
                                                                '.$row->fecha_registro.'
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                $count++;
            }
        }else{
            $html = '';
        }
        return $html;
    }

}
