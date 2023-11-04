<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_cotizacion_b2b extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_b2b/m_cotizacion_b2b');
        $this->load->library('lib_utils');
        $this->load->library('zip');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 42, null, 44, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $iddEECC = $this->session->userdata('idEmpresaColabSesion');		 
            $data['json_bandeja'] = $this->getArrayPoBaCoti($this->m_cotizacion_b2b->getAllCotizaciones(0,null,null,$iddEECC));
            $this->load->view('vf_b2b/v_cotizacion_b2b',$data);        	  
    	 }else{
        	redirect('login', 'refresh');
	    }     
    }

    public function getArrayPoBaCoti($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $poMat){ 

                 $btnRechazar = '';              
                if($poMat['estado'] ==  0){
                    $detalleCoti =   '';
                    $btnDescarga = '';
                    $btnRegCot = 'SIN OC';
                    if($poMat['orden_compra'] != null){
                        $btnRegCot   = '<a href="formCotiB2b?cod='.$poMat['codigo_cluster'].'&&estado=0&&flg_principal=0"" class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ingresar"  ><i class="fal fa-edit"></i></a>';
                    }

                    $idEEcc  = $this->session->userdata('idEmpresaColabSesion');
                    if($idEEcc  ==  6){//SOLO PANGEACO
                        $btnRechazar = ' <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Cancelar Cotizacion" 
                                        aria-expanded="true" data-cod="'.$poMat['codigo_cluster'].'" onclick="cancelarCoti($(this));">
                                        <i class="fal fa-times"></i>
                                    </a>';
                    }                  
                }else if($poMat['estado'] ==  8){
                    $detalleCoti = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Detalle Cotizacion"
                                    aria-expanded="true" data-codigo_cotizacion="'.$poMat['codigo_cluster'].'"
                                    onclick="openModalDatosSisegos($(this));"><i class="fal fa-eye"></i>
                                </a>';      
                    $btnDescarga = '';   
                    $btnRegCot   =   '';           
                }else{
                    $detalleCoti = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Detalle Cotizacion"
                                        aria-expanded="true" data-codigo_cotizacion="'.$poMat['codigo_cluster'].'"
                                        onclick="openModalDatosSisegos($(this));"><i class="fal fa-eye"></i>
                                    </a>';                  
                    $btnDescarga = ' <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" 
                                        download data-codigo_cotizacion="'.$poMat['codigo_cluster'].'" onclick="zipArchivosForm($(this));"><i class="fal fa-download"></i>
                                    </a>';
                    $btnRegCot   =   '';                    
                }               
            
                    $btnValidar = '';
                     
               
                $actions    = $btnRegCot. $detalleCoti. $btnDescarga. $btnValidar. $btnRechazar; 
                array_push($listaFinal, array($actions,
                    $poMat['codigo_cluster'],$poMat['sisego'], $poMat['nombre_estudio'], $poMat['subproyectoDesc'], $poMat['empresaColabDesc'],$poMat['nodo_principal'],$poMat['distrito'],$poMat['tipo_enlace']
                   ,$poMat['costo_materiales'],$poMat['costo_mano_obra'],$poMat['clasificacion'],$poMat['fecha_registro'],$poMat['estadoDesc'],$poMat['fecha_reg_cotizacion'],$poMat['total_coti'],$poMat['orden_compra'],$poMat['comentario_final']));
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

            $html = '	    <div class="card">
                                <div class="card-header; container form-group">
                                    DATOS GENERALES
                                </div>
                                <div class="card-body container">
                                    <div class="row form-group">
                                    
                                        <div class="col-md-4">
                                            <label>TIPO DISE&Ntilde;O:</label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['tipo_diseno_desc'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>TIPO ENLACE:</label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['tipo_enlace'])).'</label>
                                        </div>                       
                                        <div class="col-md-4">
                                        <label>TIPO ENLACE 2:</label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['tipo_enlace_2'])).'</label>
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
                                        <div class="col-md-4">
                                            <label>NODO PRINCIPAL: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['nodo_principal'])).'</label>
                                        </div> 
                                        <div class="col-md-4">
                                            <label>FACILIDADES DE RED: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['facilidades_de_red'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>REQUIERE SEIA: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['requiere_seia'])).'</label>
                                        </div>   
                                    </div>    
                                    <div class="row form-group">         
                                        <div class="col-md-4">
                                            <label>REQUIERE APROb. MML MTC: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['requiere_aprob_mml_mtc'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>REQUIERE APROB. INC.: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['requiere_aprob_inc'])).'</label>
                                        </div>
                                    </div>
                                
                                </div>
                            </div>                             
                            <div class="card">
                                <div class="card-header; container form-group">
                                    AEREO
                                </div>
                                <div class="card-body container">
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label>METRO TENDIDO A&Eacute;REO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['metro_tendido_aereo'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CANT. POSTES NUEVOS: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_postes_nuevos'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CANT. POSTES APOYO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_postes_apoyo'])).'</label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label>CANT. CTO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_cto'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CANT. DIVICAU: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_divicau'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CANT. EMPALME 16/32F: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_empame_1632'])).'</label>
                                        </div>                                       
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label>CANT. EMPALME 64F: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_empalme_64'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CANT. EMPALME 128F: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_empalme_128'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CANT. EMPALME 256F: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_empalme_256'])).'</label>
                                        </div>                                       
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label>CANT. CRUCETA: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_cruceta'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CANT. CABLES APOYADOS POSTES TELEFONICOS: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_postes_telefonico'])).'</label>
                                        </div>
                                        <div class="col-md-4" style="font-weight: bold;">
                                            <label>CANT. PUNTOS DE APOYO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_puntos_apoyo'])).'</label>
                                        </div>                                       
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label>OPERADOR AEREO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['operador_aereo'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>CANT. CABLES APOYADOS POSTES ELECTRICOS: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_postes_electricos'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>EMPRESA ELECTRICA: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['empresa_electrica'])).'</label>
                                        </div> 
                                    </div>                                    
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header; container form-group">
                                    SUBTERRANEO
                                </div>
                                <div class="card-body container">
                                    <div class="row form-group">                                         
                                        <div class="col-md-4">
                                            <label>METRO TENDIDO SUBTERRANEO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['metro_tendido_subterraneo'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>METRO CANALiZACI&Oacute;N: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['metors_canalizacion'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                        <label>CANT. C&Aacute;MARAS NUEVAS: </label>
                                        <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_camaras_nuevas'])).'</label>
                                    </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label>CANT. APERTURA C&Aacute;MARA: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_apertura_camara'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>USO DUCTO 2": </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_ducto_2_pul'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>USO DUCTO 3": </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_ducto_3_pul'])).'</label>
                                        </div>
                                    </div>  
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label>USO DUCTO 4": </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['cant_ducto_4_pul'])).'</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>OPERADOR SUBTERRANEO: </label>
                                            <label style="color:blue">'.utf8_decode(strtoupper($arrayPlanObra['operador_subte'])).'</label>
                                        </div>                                         
                                    </div>
                                </div>
                            </div>
                            <div class="card"><br>
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
            $idUsuario  = $this->session->userdata('idPersonaSessionPan');
            if($idUsuario == null){ 
                throw new Exception('Su sesion ha expirado, vuelva a logearse.');
            }
					
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

                $dataLog = array('codigo_cl'        =>  $codigo,
                                 'estado'           =>  'APROBADO',
                                 'usuario_registro'    =>  $idUsuario,
                                 'fecha_registro'   =>  $fechaActual);
                                
            } else if ($estado == 2) {//RECHAZAR
				 
                $comentario = $this->input->post('comentario');
                $dataUpdate = array(
                                    "estado"            => 3, //cluster cancelado
                                    "fecha_aprobacion"  => $fechaActual,
                                    'comentario_rechazo'=>$comentario
                                );

                $dataLog = array('codigo_cl'        =>  $codigo,
                                 'estado'           =>  'CANCELADO',
                                 'usuario_registro'    =>  $idUsuario,
                                 'fecha_registro'   =>  $fechaActual,
                                 'comentario'       =>  $comentario);
            }  else if ($estado == 3) {//DEVOLVER
                $comentario = $this->input->post('comentario');
                $dataUpdate = array(
                                    "estado"                => 0, //cluster cancelado
                                   # "fecha_registro"        => $fechaActual,
                                    'comentario_devolucion' =>  $comentario,
                                    'fecha_devolucion'  => $fechaActual,
                                    'usua_devolucion'   =>  $idUsuario
                                );

                $dataLog = array('codigo_cl'        =>  $codigo,
                                 'estado'           =>  'DEVUELTO',
                                 'usuario_registro' =>  $idUsuario,
                                 'fecha_registro'   =>  $fechaActual,
                                 'comentario'       =>  $comentario);
              
            }   else if ($estado == 8) {//CANCELAR
                $comentario = $this->input->post('comentario');
                $dataUpdate = array(
                                    "estado"                => 8, //cluster cancelado
                                    "fecha_cancela"        => $fechaActual,
                                    'comentario_cancela'    => $comentario,
                                    'usua_cancela'  =>  $idUsuario
                                );

                $dataLog = array('codigo_cl'        =>  $codigo,
                                'estado'           =>  'DEVUELTO',
                                'usuario_registro' =>  $idUsuario,
                                'fecha_registro'   =>  $fechaActual,
                                'comentario'       =>  $comentario);
            }  
            
            
            $data = $this->m_cotizacion_b2b->updateClusterPadre($codigo, $dataUpdate, $dataLog);  
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
        $estado    = ($this->input->post('estado') != '' ? $this->input->post('estado') : null);       

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
}
