<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_validacion_operaciones_b2b extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_b2b/m_validacion_operaciones_b2b');
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, 10, null, 49, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $iddEECC = $this->session->userdata('idEmpresaColabSesion');
            $data['json_bandeja'] = $this->getArrayPoBaCoti($this->m_validacion_operaciones_b2b->getAllCotizaciones(null));
            $this->load->view('vf_b2b/v_validacion_operaciones_b2b',$data);        	  
    	 }else{
        	redirect('login', 'refresh');
	    }     
    }

    public function getArrayPoBaCoti($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $poMat){ 
                     
                $eviden = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" onclick=liquidacion("' . $poMat['itemplan'] . '")><i class="fal fa-download"></i></a>';
                $validar = '';
                if($poMat['estado'] ==  null){
                    $validar = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" title="Validar" aria-expanded="true" onclick="modalValiOpe(' . "'" . $poMat['itemplan']. "'" . ',2)"><i class="fal fa-edit"></i></a>';
                }                  
              
                $actions    = $validar.$eviden;//$detalleCoti. $dise; 
                array_push($listaFinal, array($actions,
                    $poMat['itemplan'], (($poMat['idProyecto']==3) ? $poMat['indicador'] : $poMat['nombrePlan']), $poMat['subProyectoDesc'], $poMat['empresaColabDesc'], $poMat['fechaPreliquidacion'], $poMat['jefaturaDesc'],$poMat['centralDesc'],$poMat['costo_po_mat'], ($poMat['costo_po_mo'] == null ) ? 0 : $poMat['costo_po_mo'], $poMat['estado_desc']));
            }     
        }                                                            
        return $listaFinal;
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
        $data['json_bandeja']   = $this->getArrayPoBaCoti($this->m_validacion_operaciones_b2b->getAllCotizaciones($itemplan, $sisego));
        $data['error']          = EXIT_SUCCESS;
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function fechaActual() {
        $zonahoraria = date_default_timezone_get();
        ini_set('date.timezone', 'America/Lima');
        setlocale(LC_TIME, "es_ES", "esp");
        $hoy = strftime("%Y-%m-%d %H:%M:%S");
        return $hoy;
    }

}
