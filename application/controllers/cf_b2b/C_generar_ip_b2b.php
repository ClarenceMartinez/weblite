<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_generar_ip_b2b extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_b2b/m_cotizacion_b2b');
        $this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
        $this->load->library('lib_utils');
        $this->load->library('zip');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 42, null, 47, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $iddEECC = $this->session->userdata('idEmpresaColabSesion');
            $data['json_bandeja'] = $this->getArrayPoBaCoti($this->m_cotizacion_b2b->getAllCotizaciones(2,null,null,$iddEECC));
            $this->load->view('vf_b2b/v_generar_ip_b2b',$data);        	  
    	 }else{
        	redirect('login', 'refresh');
	    }     
    }

    public function getArrayPoBaCoti($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $poMat){ 
             
                $detalleCoti = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Detalle Cotizacion"
                                    aria-expanded="true" data-codigo_cotizacion="'.$poMat['codigo_cluster'].'"
                                    onclick="openModalDatosSisegos($(this));"><i class="fal fa-eye"></i>
                                </a>';                  
                $btnDescarga = ' <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" 
                                    download data-codigo_cotizacion="'.$poMat['codigo_cluster'].'" onclick="zipArchivosForm($(this));"><i class="fal fa-download"></i>
                                </a>';              
                
                if($poMat['estado'] ==  2){
                    if($poMat['itemplan']   ==  null){
                        $btnGenIp =     '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Generar IP" 
                                            aria-expanded="true" data-cod="'.$poMat['codigo_cluster'].'" data-eecc="'.$poMat['idEmpresaColab'].'"
                                            onclick="generarIp($(this));"><i class="fal fa-check"></i>
                                        </a>'; 
                        $btnRechazar = ' <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Rechazar Cotizacion" 
                                            aria-expanded="true" data-cod="'.$poMat['codigo_cluster'].'" onclick="rechazarCoti($(this));">
                                            <i class="fal fa-times"></i>
                                        </a>';
                    }else{
                        $btnGenIp       = '';
                        $btnRechazar    = '';

                    }          

                }else{
                    $btnGenIp = '';
                    $btnRechazar    = '';
                }
                
               
                $actions    = $detalleCoti. $btnDescarga. $btnGenIp. $btnRechazar; 
                array_push($listaFinal, array($actions,
                    $poMat['codigo_cluster'], $poMat['itemplan'], $poMat['sisego'], $poMat['nombre_estudio'], $poMat['subproyectoDesc'], $poMat['empresaColabDesc'],$poMat['codigo'],$poMat['distrito'],$poMat['tipo_enlace']
                   ,$poMat['costo_materiales'],$poMat['costo_mano_obra'],$poMat['clasificacion'],$poMat['fecha_aprobacion'],$poMat['estadoDesc']));
            }     
        }                                                            
        return $listaFinal;
    }
   
    function getDataDetalleCotizacionSisego() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            
            $codigo_cot = $this->input->post('codigo_cot');

            if($codigo_cot == null) {
                throw new Exception('itemplan null, comunicarse con el programador');
            }

            $arrayPlanObra = $this->m_utils->getDataCotizacionIndividual(NULL, $codigo_cot, 1);

            $html = '  <div class="card">
                            <div class="card-header; container form-group" style="background:var(--celeste_telefonica);color:white;">
                                CENTRAL OTRO OPERADOR
                            </div>
                            <div class="card-body container">
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label>NODO OTRO OPERADOR?: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['flg_nodo_otro_operador'])).'</label>
                                    </div>
                                    <div class="col-md-8">
                                        <label>NODO OTRO OPERADOR:</label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['nom_nodo'])).'</label>
                                    </div>
                                </div>
                            </div>
                        </div>
			
						<div class="card">
                            <div class="card-header; container form-group" style="background:var(--celeste_telefonica);color:white;">
                                DATOS SISEGO
                            </div>
                            <div class="card-body container">
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label>OPERADOR: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['operador'])).'</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>TIPO DISE&Ntilde;O:</label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['tipo_diseno_desc'])).'</label>
                                    </div>
                                     <div class="col-md-4">
                                        <label>TIPO ENLACE:</label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['tipo_enlace'])).'</label>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label>ACCESO CLIENTE: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['acceso_cliente'])).'</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>TENDIDO EXTERNO: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['tendido_externo'])).'</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>DURACI&Oacute;N: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['duracion'])).'</label>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label>NOMBRE ESTUDIO: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['nombre_estudio'])).'</label>
                                    </div>
                                     <div class="col-md-4">
                                        <label>TIPO CLIENTE: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['tipo_cliente'])).'</label>
                                    </div>
									 <div class="col-md-4">
                                        <label>CLASIFICACI&Oacute;N: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['clasificacion'])).'</label>
                                    </div>
                                </div>
								<div class="row form-group">
                                    <div class="col-md-6">
                                        <label>NOMBRE EBC: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['nom_ebc'])).'</label>
                                    </div>
                                </div>
                            </div>
                        </div>    
                        <div class="card">
                            <div class="card-header; container form-group" style="background:var(--celeste_telefonica);color:white;">
                                DATOS FORMULARIO COTIZACI&Oacute;N
                            </div>
                            <div class="card-body container">
                            
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label>NODO PRINCIPAL: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['nodo_principal'])).'</label>
                                        </div>
                                         <div class="col-md-6">
                                            <label>NODO RESPALDO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['nodo_respaldo'])).'</label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label>FACILIDADES DE RED: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['facilidades_de_red'])).'</label>
                                        </div>
                                         <div class="col-md-6">
                                            <label>CANT. CTO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_cto'])).'</label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label>METRO TENDIDO A&Eacute;REO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['metro_tendido_aereo'])).'</label>
                                        </div>
                                         <div class="col-md-6">
                                            <label>METRO TENDIDO SUBTERRANEO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['metro_tendido_subterraneo'])).'</label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label>METRO CANALiZACI&Oacute;N: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['metors_canalizacion'])).'</label>
                                        </div>
                                         <div class="col-md-6">
                                            <label>CANT. C&Aacute;MARAS NUEVAS: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_camaras_nuevas'])).'</label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label>CANT. POSTES NUEVOS: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_postes_nuevos'])).'</label>
                                        </div>
                                         <div class="col-md-6">
                                            <label>CANT. POSTES APOYO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_postes_apoyo'])).'</label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label>CANT. APERTURA C&Aacute;MARA: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_apertura_camara'])).'</label>
                                        </div>
                                         <div class="col-md-6">
                                            <label>REQUIERE SEIA: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['requiere_seia'])).'</label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6">
                                            <label>REQUIERE APROb. MML MTC: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['requiere_aprob_mml_mtc'])).'</label>
                                        </div>
                                         <div class="col-md-6">
                                            <label>REQUIERE APROB. INC.: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['requiere_aprob_inc'])).'</label>
                                        </div>
                                    </div>
                           
                            </div>
                            <div class="col-md-12">
                                <label>COMENTARIO.: </label>
                                <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['comentario'])).'</label>
                            </div>
                        </div>';
            $data['dataInfoSisego'] = $html;
            $data['error']    = EXIT_SUCCESS;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function zipArchivosForm() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        $data['cabecera'] = null;
        try{
            $codigo_cot = $this->input->post('codigo_cot');
            
            if($codigo_cot == null || $codigo_cot == '') {
                throw new Exception('accion no permitida');
            }
            
            $ubicacion = 'uploads/sisego/cotizacion_individual/'.$codigo_cot;
            $fileName = $codigo_cot.'_archivos_cotizacion.zip';
            $ubicZip = $ubicacion.'/'.$fileName;

            if (file_exists($ubicZip)) {
                unlink($ubicZip);
             }
             

            $this->zip->read_dir($ubicacion,false);            
            $this->zip->archive($ubicZip);
            //$this->rrmdir($ubicacion);           

            $data['directorioZip'] =  $ubicacion.'/'.$fileName;
            $data['error'] = EXIT_SUCCESS;
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function aprobarCancelarCotizacion() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $codigo = $this->input->post('codigo');
            $estado = $this->input->post('estado'); //1 aprobado  y 2 rechazado

            /*
            $countCancelado = $this->m_utils->getCountCotizacionByCod($codigo, 8);
			
			if ($countCancelado > 0) {
                throw new Exception('LA COTIZACION HA EXPIRADO, PARA CONTINUAR SE REQUIERE SOLICITAR UNA NUEVA.');
            }*/
					
			if($estado == null || $estado == '') {
                throw new Exception('no se envio el tipo (aprobado, cancelacion)');
            }

            if($codigo == null || $codigo == '') {
                throw new Exception('no se envio el codigo CL');
            }
            $fechaActual = $this->m_utils->fechaActual();
			if ($estado == 1) {//APROBAR
			 
                $dataUpdate = array(
                                    "estado"            => 2,
                                    "fecha_aprobacion"  => $fechaActual
                                );

                                
            } else if ($estado == 2) {//RECHAZAR
				 
                $comentario = $this->input->post('codigo');
                $dataUpdate = array(
                                    "estado"            => 3, //cluster cancelado
                                    "fecha_aprobacion"  => $fechaActual,
                                    'comentario_rechazo'=>$comentario
                                );
              
            }  
            
            $data = $this->m_cotizacion_b2b->updateClusterPadre($codigo, $dataUpdate);
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
         }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function filtrarCotizacion() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $sisego  = $this->input->post('sisego') ? $this->input->post('sisego') : null;
        $cod_soli  = $this->input->post('cod_solicitud') ? $this->input->post('cod_solicitud') : null;
        $estado    = $this->input->post('estado') ? $this->input->post('estado') : null;

        if($idUsuario == null) {
            throw new Exception('La sesión a expirado, recargue la página');
        }

        $iddEECC = $this->session->userdata('idEmpresaColabSesion');
        $data['json_bandeja']   = $this->getArrayPoBaCoti($this->m_cotizacion_b2b->getAllCotizaciones($estado, $sisego, $cod_soli, $iddEECC));
        $data['error']          = EXIT_SUCCESS;
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function createPlanObraFromSisego() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            //VALIDAMOS LOS 60 DIAS TRANSCURRIDOS LUEGO DE LA APROBACION		
			$idUsuario      = $this->session->userdata('idPersonaSessionPan');
            $cod_cotiz      = $this->input->post('codigo');
            $cod_inversion  = $this->input->post('cod_inversion');
            if ($idUsuario ==  null) {
                throw new Exception('Su session ha terminado, vuelva logearse.');
            }

			$countCancelado = $this->m_utils->getCountCotizacionByCod($cod_cotiz, 8);
			
			if ($countCancelado > 0) {
                throw new Exception('LA COTIZACION HA EXPIRADO, PARA CONTINUAR SE REQUIERE SOLICITAR UNA NUEVA.');
            }
         
            $dataCoti = $this->m_utils->getDataCotizacionByCod($cod_cotiz);  
            if ($dataCoti ==    null) {
                throw new Exception('LA COTIZACION NO ESTA APROBADA.');
            }                 
            
            $fechaActual = $this->m_utils->fechaActual();    
            $idProyecto = 3;//SOLO B2B
            $itemplan    = $this->m_utils->getCodigoItemplan($dataCoti['idZonal'], $idProyecto);
            /*
            $nuevafecha     = strtotime('+7 day', strtotime($fechaActual));
            $fechaPrevista  = date('Y-m-j', $nuevafecha);
            */
            $objJson = array();            
            $objJson['itemplan'] 	        = $itemplan;      
            $objJson['idEstadoPlan']        = ID_ESTADO_PLAN_PRE_REGISTRO;      
            $objJson['fechaRegistro']       = $fechaActual;
			$objJson['usua_crea_obra']      = $idUsuario;
            $objJson['idUsuarioLog']        = $idUsuario;
            $objJson['fechaLog']            = $fechaActual; 
			$objJson['descripcion']	        = 'REGISTRO INDIVIDUAL';
            $objJson['ult_codigo_sirope']   = $itemplan.'FO'; 
            $objJson['idEmpresaColab']      = $dataCoti['idEmpresaColab'];  
            $objJson['idCentral']           = $dataCoti['idCentral'];
            $objJson['idZonal']             = $dataCoti['idZonal'];  
            $objJson['longitud']            = $dataCoti['longitud'];  
            $objJson['latitud']             = $dataCoti['latitud'];      
            $objJson['nombrePlan']          = $dataCoti['sisego']. " - " . $dataCoti['cliente'];
            $objJson['idSubProyecto']       = $dataCoti['idSubProyecto'];
            $objJson['idFase']              = ID_FASE_ANIO_CREATE_ITEMPLAN;
            $objJson['fechaInicio']         = $fechaActual;  
            $objJson['paquetizado_fg']  	= 1;
            $objJson['codigoInversion']     = $cod_inversion;
            $objJson['idEmpresaElec']       =   6;
            $objJson['indicador']               =   $dataCoti['sisego'];  
            $objJson['cantFactorPlanificado']   =   1;
            $objJson['idPqtTipoFactorMedicion'] =   1;

            if($dataCoti['id_tipo_diseno']  ==  8){//HABILITACION DE HILO
                $objJson['is_habilitacion'] =   1;
            }

            $data = $this->m_utils->registrarItemplan($objJson);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
			}
            $codigo_solicitud = $this->m_utils->getNextCodSolicitud();//obtengo codigo unico de solicitud
				$dataPlanobra = array(  
					"itemplan"                   => $itemplan,
					"costo_unitario_mo"          => $dataCoti['costo_sol_oc'],
					"costo_unitario_mat"         => $dataCoti['costo_materiales'],
					"solicitud_oc"               => $codigo_solicitud,
					"estado_sol_oc"              => 'PENDIENTE',
					"costo_unitario_mo_crea_oc"  => $dataCoti['costo_sol_oc'],
					"costo_unitario_mat_crea_oc" => $dataCoti['costo_materiales']
				);
				$solicitud_oc_creacion = array(
					'codigo_solicitud'  =>  $codigo_solicitud,
					'idEmpresaColab'    =>  $objJson['idEmpresaColab'],
					'estado'            =>  1,//pendiente
					'fecha_creacion'    =>  $fechaActual,
					'idSubProyecto'     =>  $objJson['idSubProyecto'],
					'plan'              =>  null,//plan no va
					'codigoInversion'   => 	$objJson['codigoInversion'],
					'estatus_solicitud' => 'NUEVO',
					'tipo_solicitud'    =>  1,// 1= CREACION, 2 = EDICION, 3 = CERTIFICACION,
					'usuario_creacion'  =>  $idUsuario
				);
				$item_x_sol = array(
					'itemplan'            =>  $itemplan,
					'codigo_solicitud_oc' =>  $codigo_solicitud,
					'costo_unitario_mo'   =>  $dataCoti['costo_sol_oc']
				);

                $upd_cluster = array(   'codigo_cluster' => $cod_cotiz,
                                        'itemplan'       => $itemplan);


                $logSeguimientoB2b = array(  
                    'itemplan'              =>  $itemplan,
                    'idEstadoPlan'          =>  ID_ESTADO_PLAN_PRE_REGISTRO,
                    'usuario_registro'      =>  $idUsuario,
                    'fecha_registro'        =>  $fechaActual,
                    'id_motivo_seguimiento' =>  1,
                    'comentario_incidencia' =>  'AUTOMÁTICO'
                );

                $registroDetalleb2b = array(
                    'itemplan'                  =>  $itemplan,
                    'ult_situa_especifica'      =>  1,
                    'fec_ult_situa_especifia'   =>  $fechaActual,
                    'usua_reg'                  =>  $idUsuario,
                    'sisego'                    =>  $dataCoti['sisego'],
                    'fec_reg'                   =>  $fechaActual,
                    'ultimo_comentario'         =>  'AUTOMÁTICO'
                );
         
				$data = $this->m_registro_itemplan_masivo->crearSolCreacionForItemplanb2b($dataPlanobra, $solicitud_oc_creacion, $item_x_sol, $upd_cluster, $registroDetalleb2b, $logSeguimientoB2b);

			$data['itemplan'] = $itemplan;
			$data['msj'] = 'Registro Exitoso.';
        } catch (Exception $e) {
            $data['error'] = EXIT_ERROR;
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getCodInverByEECCToChoice() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            
            $idEmpresaColab = $this->input->post('eecc');
            if ($idEmpresaColab == null) {
                throw new Exception("Seleccione EECC");
            }

            $cod_cluster = $this->input->post('cod');
            $dataCoti = $this->m_utils->getDataCotizacionByCod($cod_cluster);  
            if ($dataCoti ==    null) {
                throw new Exception('LA COTIZACION NO ESTA APROBADA.');
            }   
            $fechaActual = $this->m_utils->fechaActual();
            $fechaAprobacion    = $dataCoti['fecha_aprobacion'];
            log_message('error', $dataCoti['fecha_aprobacion']);
            $fechaAproPlus60    =   strtotime('+60 day', strtotime($fechaAprobacion));
            log_message('error', $fechaAproPlus60);
            $newFecAProbPlus60  = date('Y-m-d h:m:s', $fechaAproPlus60);
            $exede_60_dias = 0;
            log_message('error', $newFecAProbPlus60);
            log_message('error', $fechaActual);
            if($newFecAProbPlus60   <   $fechaActual){
                $exede_60_dias  =   1;
            }


            $data['cmbInversion']     = __buildComboInversion($idEmpresaColab);
            $data['exede_sla']  =   $exede_60_dias;
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

}
