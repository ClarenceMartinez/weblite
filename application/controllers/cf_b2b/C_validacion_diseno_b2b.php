<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_validacion_diseno_b2b extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_b2b/m_validacion_diseno_b2b');
        $this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
        $this->load->model('mf_consulta/m_detalle_consulta');
        $this->load->model('mf_itemplan_madre/M_consulta_item_madre');
        $this->load->library('lib_utils');
        $this->load->library('zip');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 10, null, 48, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $iddEECC = $this->session->userdata('idEmpresaColabSesion');
            $data['json_bandeja'] = $this->getArrayPoBaCoti($this->m_validacion_diseno_b2b->getAllCotizaciones(null,null));
            $this->load->view('vf_b2b/v_validacion_diseno_b2b',$data);        	  
    	 }else{
        	redirect('login', 'refresh');
	    }     
    }

    public function getArrayPoBaCoti($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $poMat){ 
                if($poMat['idProyecto'] ==  3){
                    $detalleCoti = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Detalle Cotizacion"
                                        aria-expanded="true" data-codigo_cotizacion="'.$poMat['codigo_cluster'].'"
                                        onclick="openModalDatosSisegos($(this));"><i class="fal fa-eye"></i>
                                    </a>'; 
                }else{
                    $detalleCoti    =   '';
                }
                                 
                $dise = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" title="Validar" aria-expanded="true" onclick="modalValiOpe(' . "'" . $poMat['itemplan']. "'" . ',1)"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Expediente" download="" href="'.$poMat['path_expediente_diseno'].'"><i class="fal fa-download"></i></a>';
               
                  
               
                $actions    = $detalleCoti. $dise; 
                array_push($listaFinal, array($actions,
                    $poMat['itemplan'], $poMat['indicador'], $poMat['nombrePlan'], $poMat['subproyectoDesc'], $poMat['empresaColabDesc'], $poMat['codigo'], $poMat['codigo_cluster'],$poMat['nombre_estudio'],$poMat['distrito'],$poMat['tipo_enlace_2']
                   ,$poMat['costo_materiales'],$poMat['costo_mano_obra'],$poMat['costo_po_mat'], ($poMat['costo_po_mo'] == null ) ? 0 : $poMat['costo_po_mo']));
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

    function filtrarCotizacion() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $sisego  = $this->input->post('sisego') ? $this->input->post('sisego') : null;
        $itemplan  = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
 
        if($idUsuario == null) {
            throw new Exception('La sesión a expirado, recargue la página');
        }

        //$data['tablaSolicitudOc'] = $this->getTablaSolicitudOc($itemplan, null);
        $data['json_bandeja']   = $this->getArrayPoBaCoti($this->m_validacion_diseno_b2b->getAllCotizaciones($itemplan, $sisego));
        $data['error']          = EXIT_SUCCESS;
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

	function rmDir_rf($carpeta)
    {
      foreach(glob($carpeta . "/*") as $archivos_carpeta){             
        if (is_dir($archivos_carpeta)){
          $this->rmDir_rf($archivos_carpeta);
        } else {
        unlink($archivos_carpeta);
        }
      }
      //log_message('error', 'delete:'.$carpeta);
      rmdir($carpeta);
     }
	 
    function validarItemplanPanDi() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        $number_ip;
        try {
            log_message('error', 'ENTRO VALIDAR IP DISEÑO');
            $data['error'] = EXIT_SUCCESS;
            
            $this->db->trans_begin();
            $flg_estado = $this->input->post('selectEstado');
            $itemplan   = $this->input->post('itemPlan');
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $comentario = $this->input->post('textComentario');       
			$flg_tipo   = $this->input->post('flg_tipo');         
            $idEstacion =   5;//B2B SIEMPRE SERA FO
            if($flg_estado == 3) {//RECHAZADO
				if($flg_tipo  == 1) {//DISENO
					$dataPlanObra = array(
											"flg_valida_diseno" 	   => 2,
											"comentario_valida_diseno" => $comentario,
											"fecha_valida_diseno"      => $this->fechaActual(),
											"id_usuario_valida_diseno" => $idUsuario,
                                            "idEstadoPlan"             =>  2,
                                            "idUsuarioLog"             =>  $idUsuario,
                                            "fechaLog"                 =>  $this->fechaActual(),
                                            "descripcion"              =>  "DISEÑO RECHAZADO"
										);

				    $infoDisenoCopy =   $this->m_validacion_diseno_b2b->getInfoDisenoToCopy($itemplan, $idEstacion);
                    $dataDisenoRecha = array('itemplan'             =>      $infoDisenoCopy['itemplan'],
                                             'idEstacion'           =>      $infoDisenoCopy['idEstacion'],
                                             'usuario_ejecucion'    =>   	$infoDisenoCopy['usuario_ejecucion'],
                                             'fecha_ejecucion'      =>   	$infoDisenoCopy['fecha_ejecucion'],
                                             'path_expediente_diseno' =>   	$infoDisenoCopy['path_expediente_diseno'],
                                             'requiere_licencia'    =>   	$infoDisenoCopy['requiere_licencia'],
                                             'usuario_rechazo'	    =>      $idUsuario,
                                             'fecha_rechazo'	    =>      $this->fechaActual(),
                                             'comentario_rechazo'   =>      $comentario);

                    $dataDisenoUpd = array(
                                            'estado'                    =>  2,
                                            'usuario_ejecucion'         =>  null,
                                            'fecha_ejecucion'           =>  null,
                                            'path_expediente_diseno'    =>  null,
                                            'requiere_licencia'         =>  null
                                        );
                    $data   =   $this->m_validacion_diseno_b2b->rechazarItemplandISEvAL($itemplan, $idEstacion, $dataPlanObra, $dataDisenoRecha, $dataDisenoUpd);                      
                  
				} else if($flg_tipo  == 2){//OPERACIONES 
                     
					$validaOpera = array(   
                                            "itemplan"            => $itemplan,
											"estado" 	          => 2,
											"comentario_atencion" => $comentario,
											"fecha_atencion"      => $this->fechaActual(),
											"usuario_atencion"    => $idUsuario
										);

                    $dataPlanObra = array(     
                                        "itemplan"                 => $itemplan,                           
                                        "fechaPreliquidacion"      =>  null,                                        
                                        "idEstadoPlan"             =>  3,
                                        "idUsuarioLog"             =>  $idUsuario,
                                        "fechaLog"                 =>  $this->fechaActual(),
                                        "descripcion"              =>  $comentario
                    );

					$data = $this->m_validacion_diseno_b2b->rechazarOperaciones($validaOpera, $dataPlanObra, $itemplan);					
					if($data['error'] == EXIT_ERROR) {
						throw new Exception($data['msj']);
					} 
					
					$path = 'uploads/evidencia_liquidacion/' . $itemplan . '/';
                    if (file_exists($path)) {
                        $this->rmDir_rf($path);
                    }   
				}

            } else {//APPROBADO
				if($flg_tipo  == 1) {//DISENO
					$arrayUpdate = array(
											"flg_valida_diseno" 	   => 1,
											"comentario_valida_diseno" => $comentario,
											"fecha_valida_diseno"      => $this->fechaActual(),
											"id_usuario_valida_diseno" => $idUsuario,
                                            "idUsuarioLog"             =>  $idUsuario,
                                            "fechaLog"                 =>  $this->fechaActual(),
                                            "descripcion"              =>  "DISEÑO VALIDADO"
										);

                    $infoDisenoCopy =   $this->m_validacion_diseno_b2b->getInfoDisenoToCopy($itemplan, $idEstacion);
                     
                    $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                    if($infoItem['idProyecto']  ==  3){
                        if($infoItem['is_habilitacion'] ==  1){
                            $arrayUpdate['idEstadoPlan'] =  3;//EN OBRA
                            $arrayUpdate['descripcion']  =  "DISEÑO VALIDADO / HABILITACION";//EN OBRA
                        }else{
                            if($infoDisenoCopy['requiere_licencia'] ==  2){
                                $arrayUpdate['idEstadoPlan'] =  20;//EN APROBACION
                            }else{
                                $arrayUpdate['idEstadoPlan'] =  19;//EN LICENCIA
                            }
                        }
                    }else{//POR AHORA OBRAS VARIAS
                        if($infoDisenoCopy['requiere_licencia'] ==  2){
                            $arrayUpdate['idEstadoPlan'] =  20;//EN APROBACION
                        }else{
                            $arrayUpdate['idEstadoPlan'] =  19;//EN LICENCIA
                        }
                    }                   
                    
                    $data = $this->m_utils->actualizarPlanObra($itemplan, $arrayUpdate);
					if($data['error'] == EXIT_ERROR) {
						throw new Exception($data['msj']);
					}

                    $codigo_poDiseno = $this->m_validacion_diseno_b2b->getPoDisenoToValidate($itemplan);
                    // echo "ssss";
                    if($codigo_poDiseno == null) {
						throw new Exception('La obra no cuenta con una PO Diseno!');
					}
                    $arrayUpdatePo  =   array(
                                            'codigo_po'        =>  $codigo_poDiseno,
                                            'itemplan'         =>  $itemplan,
                                            'estado_po'        =>  ID_ESTADO_PO_VALIDADO//validado
                                            );
					
                    $arrayInsertLog =    array(
                                                'codigo_po'        =>  $codigo_poDiseno,
                                                'itemplan'         =>  $itemplan,
                                                'idUsuario'        =>  $idUsuario,
                                                'fecha_registro'   =>  $this->fechaActual(),
                                                'idPoestado'       =>  ID_ESTADO_PO_VALIDADO
                                            );

                    $data = $this->m_validacion_diseno_b2b->actualizarEstadoPo($arrayUpdatePo, $arrayInsertLog);
						
                    if ($infoItem['idProyecto']  ==  52)
                    {
                        $infoCreateSol = $this->m_detalle_consulta->getInfoSolCreacionByItemDiseno($itemplan); //getinfo solicitud de creacion

                        $infoCertiEdicionOC = $this->m_detalle_consulta->getDataToSolicitudEdicionCertiOCDiseno($itemplan); //costos mo
                        if ($infoCertiEdicionOC == null) {
                            throw new Exception('No se pudo obtener los costos de MO para la obra.');
                        }
                        $item_x_sol_2 = array();
                        $arraySolicitud = array();
                        $arrayItemXSolicitud = array();
                        $solicitud_oc_edi_certi = array();
                        //sol edicion
                        $codigo_solicitud = $this->m_utils->getNextCodSolicitud(); //nuevo cod solicitud
                        if ($codigo_solicitud == null) {
                            throw new Exception('No se pudo obtener el codigo de Solicitud refresque la pantalla y vuelva a intentarlo.');
                        }
                        $fechaActual = $this->m_utils->fechaActual();

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
                            'itemplan'              => $itemplan,
                            'codigo_solicitud_oc'   => $codigo_solicitud_2,
                            'costo_unitario_mo'     => $infoCertiEdicionOC['total'],
                            'posicion'              => $infoCreateSol['posicion'],

                        );
                        array_push($arrayItemXSolicitud, $item_x_sol_2);

                        $updatePlanObra = array(
                            // 'idEstadoPlan' => ID_ESTADO_PLAN_EN_CERTIFICACION,
                            // 'idUsuarioLog' => $idUsuario,
                            // 'fechaLog' => $fechaActual,
                            // 'descripcion' => 'VALIDACION PQT 2D NIVEL',
                            'solicitud_oc_certi_diseno' => $codigo_solicitud_2,
                            'costo_unitario_mo_certi_diseno' => $infoCertiEdicionOC['total'],
                            'estado_oc_certi_diseno' => 'PENDIENTE DE EDICION',
                            'solicitud_oc_dev_diseno' => $codigo_solicitud,
                            'costo_devolucion_diseno' => $infoCertiEdicionOC['total'],
                            'estado_oc_dev_diseno' => 'PENDIENTE'
                        );

                        //referencial
                        // $data = $this->m_detalle_consulta->validarEstacionFOPqt2Nivel($arraySolicitud, $arrayItemXSolicitud, $updatePlanObra, $itemplan, $arrayPoInserLogPo, $arrayPoUpdate, $idEstacion, $dataUpdateSolicitud, $dataExpediente);

                        $data = $this->m_detalle_consulta->validarEstacionFOPqt2NivelDiseno($arraySolicitud, $arrayItemXSolicitud, $updatePlanObra, $itemplan);

                        $idUsuario      = $this->session->userdata('idPersonaSessionPan');
                        //genera una solicitud de operaciones
                        $costoDiseno    = $this->createPoPqt(ID_ESTACION_FO, ID_ESTACION_FO, $itemplan, 2);
                        $costo_crea_oc  = @$costoDiseno['costo_total_po'];

                        if($costo_crea_oc > 0){//costo_po_pqt_fibra
                            $sol_crea = $this->M_consulta_item_madre->generarSolicitudCreacionOCItemplanPersonalizado($itemplan, $costo_crea_oc, $idUsuario);
                        }
                    }
					 
				} else if($flg_tipo  == 2){//OPERACIONES 
                     
					$arrayUpdate = array(   
                                            "itemplan"            => $itemplan,
											"estado" 	          => 1,
											"comentario_atencion" => $comentario,
											"fecha_atencion"      => $this->fechaActual(),
											"usuario_atencion"    => $idUsuario
										);
					$data = $this->m_validacion_diseno_b2b->validarOperaciones($arrayUpdate);					
					if($data['error'] == EXIT_ERROR) {
						throw new Exception($data['msj']);
					} 
				}
            }
            $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function createPoPqt($idEstacionPlace, $idEstacionPartidas, $itemplan, $tipo_partidas)
    {
        $data['error']          = EXIT_ERROR;
        $data['msj']            = null;
        $data['costo_total_po'] = 0;
        try { 
            $this->db->trans_begin();
         //   $idEstacionPartidas =  ID_ESTACION_FO;//todo es FO INTEGRAL
            $idUsuario      = $this->session->userdata('idPersonaSessionPan');
            $fechaActual        = $this->m_utils->fechaActual();              
            $hasPoPqtACtive = $this->m_utils->hasPoPqtActive($itemplan, $idEstacionPlace);
            
                $arrayInfo = $this->m_utils->getPlanObraByItemplan($itemplan);
                if($arrayInfo == null){
                    throw new Exception('Hubo un error al traer la información del itemplan.');
                }
                
                $codigoPO = $this->m_utils->getCodigoPO($itemplan); //Clarence
                //$codigoPO = date('Y').'-'.time();
                if($codigoPO == null || $codigoPO == '') {
                    throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
                }
                $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MO', $idEstacionPlace);
                if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                    throw new Exception("No tiene configurado un area.");
                }

                $partidasTOPoPqt = $this->m_utils->getPartidasToPoPqtByTipo($idEstacionPartidas, $itemplan, $tipo_partidas);
                if($partidasTOPoPqt == null){
                    throw new Exception('No se encontraron Partidas Configuradas para la obra.');
                }

                $arrayDetalleInsert = array();
                $costo_total_po = 0;
                foreach($partidasTOPoPqt as $dataPartidaPqt){

                    $costo_partida = $dataPartidaPqt['baremo']*$dataPartidaPqt['costo']*$arrayInfo['cantFactorPlanificado'];

                    $detallePo = array (
                        'codigo_po'        => $codigoPO,
                        'codigoPartida'    => $dataPartidaPqt['codigoPartida'], //ESTADO REGISTRADO
                        'baremo'           => $dataPartidaPqt['baremo'],
                        'preciario'        => $dataPartidaPqt['costo'],
                        'cantidadInicial'  => $dataPartidaPqt['cantFactorPlanificado'],
                        'montoInicial'     => $costo_partida,
                        'cantidadFinal'    => $arrayInfo['cantFactorPlanificado'],
                        'montoFinal'       => $costo_partida,
                        'costoMo'          => $costo_partida
                    );

                    $costo_total_po =   $costo_total_po +   $costo_partida;
                    array_push($arrayDetalleInsert, $detallePo);
                }

                if($arrayInfo['idProyecto'] ==  52){//FTTHA ADICIONAMOS PARTIDA FERRETERIA
                    if($idEstacionPlace ==  5){//SI ES LA FO
                        $baremoFerreteria = 1;
                        $costo_partida = $baremoFerreteria*COSTO_PARTIDA_FERRETERIA_FTTH*$arrayInfo['cantFactorPlanificado'];
                        $detallePo = array (
                            'codigo_po'        => $codigoPO,
                            'codigoPartida'    => '69901-2', //PARTIDA UNIDAD MATERIAL FERRETERIA
                            'baremo'           => $baremoFerreteria,
                            'preciario'        => COSTO_PARTIDA_FERRETERIA_FTTH,
                            'cantidadInicial'  => $arrayInfo['cantFactorPlanificado'],
                            'montoInicial'     => $costo_partida,
                            'cantidadFinal'    => $arrayInfo['cantFactorPlanificado'],
                            'montoFinal'       => $costo_partida,
                            'costoMo'          => $costo_partida
                        );

                        $costo_total_po =   $costo_total_po +   $costo_partida;
                        array_push($arrayDetalleInsert, $detallePo);
                    }
                }
                 
                if(count($arrayDetalleInsert) == 0){
                    throw new Exception("No hay partidas válidas para el registro de la PO");
                }

                $dataPo = array (
                    'codigo_po'      => $codigoPO,
                    'itemplan'       => $itemplan,
                    'estado_po'      => ID_ESTADO_PO_REGISTRADO,
                    'idEstacion'     => $idEstacionPlace,
                    'costo_total'    => $costo_total_po,
                    'idUsuario'      => $idUsuario,
                    'fechaRegistro'  => $fechaActual,
                    'flg_tipo_area'  => 2,
                    'idEmpresaColab' => $arrayInfo['idEmpresaColab'],
                    'idArea'         => $dataSubEstacionArea['idArea'],
                    'idSubProyecto'  => $arrayInfo['idSubProyecto'],
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
                $data['costo_total_po'] = $costo_total_po;
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
                $this->db->trans_commit();
        
               
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
       return $data;
    }

    function fechaActual() {
        $zonahoraria = date_default_timezone_get();
        ini_set('date.timezone', 'America/Lima');
        setlocale(LC_TIME, "es_ES", "esp");
        $hoy = strftime("%Y-%m-%d %H:%M:%S");
        return $hoy;
    }
	
	function manualRechazoDisenoMasivo() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {

            $itemplanList = array('');
            $idEstacion =   5;
            $comentario = 'DISEÑO RECHAZADO';
            $idUsuario  =   8;
            foreach($itemplanList as $itemplan){

                $dataPlanObra = array(
                    "flg_valida_diseno" 	   => 2,
                    "comentario_valida_diseno" => $comentario,
                    "fecha_valida_diseno"      => $this->fechaActual(),
                    "id_usuario_valida_diseno" => $idUsuario,
                    "idEstadoPlan"             =>  2,
                    "idUsuarioLog"             =>  $idUsuario,
                    "fechaLog"                 =>  $this->fechaActual(),
                    "descripcion"              =>  "DISEÑO RECHAZADO"
                );
            
                $infoDisenoCopy =   $this->m_validacion_diseno_b2b->getInfoDisenoToCopy($itemplan, $idEstacion);
                $dataDisenoRecha = array('itemplan'             =>      $infoDisenoCopy['itemplan'],
                        'idEstacion'           =>      $infoDisenoCopy['idEstacion'],
                        'usuario_ejecucion'    =>   	$infoDisenoCopy['usuario_ejecucion'],
                        'fecha_ejecucion'      =>   	$infoDisenoCopy['fecha_ejecucion'],
                        'path_expediente_diseno' =>   	$infoDisenoCopy['path_expediente_diseno'],
                        'requiere_licencia'    =>   	$infoDisenoCopy['requiere_licencia'],
                        'usuario_rechazo'	    =>      $idUsuario,
                        'fecha_rechazo'	    =>      $this->fechaActual(),
                        'comentario_rechazo'   =>      $comentario);
                
                $dataDisenoUpd = array(
                        'estado'                    =>  2,
                        'usuario_ejecucion'         =>  null,
                        'fecha_ejecucion'           =>  null,
                        'path_expediente_diseno'    =>  null,
                        'requiere_licencia'         =>  null
                    );
                $data   =   $this->m_validacion_diseno_b2b->rechazarItemplandISEvAL($itemplan, $idEstacion, $dataPlanObra, $dataDisenoRecha, $dataDisenoUpd);    
            }
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
	
	function manualAprobacionDisenoMasivo() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {

            $itemplanList = array('');

            $idEstacion =   5;
            $comentario = 'DISEÑO APROBADO';
            $idUsuario  =   8;
            foreach($itemplanList as $itemplan){

                $arrayUpdate = array(
                    "flg_valida_diseno" 	   => 1,
                    "comentario_valida_diseno" => $comentario,
                    "fecha_valida_diseno"      => $this->fechaActual(),
                    "id_usuario_valida_diseno" => $idUsuario,
                    "idUsuarioLog"             =>  $idUsuario,
                    "fechaLog"                 =>  $this->fechaActual(),
                    "descripcion"              =>  "DISEÑO VALIDADO"
                );

                $infoDisenoCopy =   $this->m_validacion_diseno_b2b->getInfoDisenoToCopy($itemplan, $idEstacion);

                $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                if($infoItem['idProyecto']  ==  3){
                    if($infoItem['is_habilitacion'] ==  1){
                        $arrayUpdate['idEstadoPlan'] =  3;//EN OBRA
                        $arrayUpdate['descripcion']  =  "DISEÑO VALIDADO / HABILITACION";//EN OBRA
                    }else{
                        if($infoDisenoCopy['requiere_licencia'] ==  2){
                            $arrayUpdate['idEstadoPlan'] =  20;//EN APROBACION
                        }else{
                            $arrayUpdate['idEstadoPlan'] =  19;//EN LICENCIA
                        }
                    }
                }else{//POR AHORA OBRAS VARIAS
                    if($infoDisenoCopy['requiere_licencia'] ==  2){
                        $arrayUpdate['idEstadoPlan'] =  20;//EN APROBACION
                    }else{
                        $arrayUpdate['idEstadoPlan'] =  19;//EN LICENCIA
                    }
                }                   

                $data = $this->m_utils->actualizarPlanObra($itemplan, $arrayUpdate);
                if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
                }

                $codigo_poDiseno = $this->m_validacion_diseno_b2b->getPoDisenoToValidate($itemplan);
                // echo 
                if($codigo_poDiseno == null) {
                throw new Exception('La obra no cuenta con una PO Diseno!');
                }
                $arrayUpdatePo  =   array(
                                    'codigo_po'        =>  $codigo_poDiseno,
                                    'itemplan'         =>  $itemplan,
                                    'estado_po'        =>  ID_ESTADO_PO_VALIDADO//validado
                                    );

                $arrayInsertLog =    array(
                                        'codigo_po'        =>  $codigo_poDiseno,
                                        'itemplan'         =>  $itemplan,
                                        'idUsuario'        =>  $idUsuario,
                                        'fecha_registro'   =>  $this->fechaActual(),
                                        'idPoestado'       =>  ID_ESTADO_PO_VALIDADO
                                    );

                $data = $this->m_validacion_diseno_b2b->actualizarEstadoPo($arrayUpdatePo, $arrayInsertLog);  
            }
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

}
