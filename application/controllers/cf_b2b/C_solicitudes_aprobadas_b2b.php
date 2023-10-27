<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_solicitudes_aprobadas_b2b extends CI_Controller {
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, 42, null, 46, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $iddEECC = $this->session->userdata('idEmpresaColabSesion');		 
            $data['json_bandeja'] = $this->getArrayPoBaCoti($this->m_cotizacion_b2b->getAllCotizaciones(2,null,null,$iddEECC));
            $this->load->view('vf_b2b/v_solicitudes_aprobadas_b2b',$data);        	  
    	 }else{
        	redirect('login', 'refresh');
	    }     
    }

    public function getArrayPoBaCoti($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $poMat){ 
                
  
                $detalleCoti    = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Detalle Cotizacion"
                                        aria-expanded="true" data-codigo_cotizacion="'.$poMat['codigo_cluster'].'"
                                        onclick="openModalDatosSisegos($(this));"><i class="fal fa-eye"></i>
                                    </a>';                  
                $btnDescarga    = ' <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" 
                                        download data-codigo_cotizacion="'.$poMat['codigo_cluster'].'" onclick="zipArchivosForm($(this));"><i class="fal fa-download"></i>
                                    </a>';

                $btnRegCot      = '';                 
                $btnValidar     = '';
                $btnRechazar    = '';               
                
               
                $actions    = $btnRegCot. $detalleCoti. $btnDescarga. $btnValidar. $btnRechazar; 
                array_push($listaFinal, array($actions,
                    $poMat['codigo_cluster'],$poMat['sisego'], $poMat['nombre_estudio'], $poMat['subproyectoDesc'], $poMat['empresaColabDesc'],$poMat['nodo_principal'],$poMat['distrito'],$poMat['tipo_enlace']
                   ,number_format($poMat['costo_materiales'],2), number_format($poMat['costo_mano_obra'],2), number_format(($poMat['costo_materiales']+$poMat['costo_mano_obra']),2),$poMat['clasificacion'],$poMat['tipo_enlace_2']
                   ,$poMat['duracion'],$poMat['fecha_aprobacion'],$poMat['total_coti'],$poMat['estadoDesc']
                   ,$poMat['cant_puntos_apoyo'],$poMat['operador_aereo'],$poMat['cant_postes_electricos'],$poMat['empresa_electrica'],$poMat['cant_ducto_2_pul'],$poMat['cant_ducto_3_pul'],$poMat['cant_ducto_4_pul'],$poMat['operador_subte']));
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
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
        $fec_inicio = $this->input->post('fec_ini') ? $this->input->post('fec_ini') : null;
        $fec_fin    = $this->input->post('fec_fin') ? $this->input->post('fec_fin') : null;
       // $estado    = $this->input->post('estado') ? $this->input->post('estado') : null;

        if($idUsuario == null) {
            throw new Exception('La sesión a expirado, recargue la página');
        }
		$iddEECC = $this->session->userdata('idEmpresaColabSesion');
        if($fec_inicio  != null &&  $fec_fin    !=null){
            $data['json_bandeja']   = $this->getArrayPoBaCoti($this->m_cotizacion_b2b->getAllCotizacionesByRangeDate(2,$fec_inicio, $fec_fin, $iddEECC));
        }else{
            $data['json_bandeja']   = $this->getArrayPoBaCoti($this->m_cotizacion_b2b->getAllCotizaciones(2,null,null, $iddEECC));
        }
        $data['error']          = EXIT_SUCCESS;
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
}
