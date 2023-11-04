<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_evaluacion_eecc extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
		$this->load->model('mf_reportes/m_evaluacion_eecc');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    function index() {
        $idUsuario      = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
			$data['empresaColaboradora'] = $this->m_utils->getEmpresaColabById($idEmpresaColab);
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 35, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
   
            $this->load->view('vf_reportes/v_evaluacion_eecc',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2, 'refresh');
	    }     
    }

    public function filtrarReporteEvaEcc(){
        $data['error']    = EXIT_ERROR;
        $data['msj']      = 'Hubo un error al filtrar los datos';
        $data['cabecera'] = null;
        try{

            $contrata = $this->input->post('eecc');
            $anio = $this->input->post('anio'); 
            $mes = $this->input->post('mes');
            $check  =   1;//siempre considerar los tiempos muertos a favor.

            log_message('error', $anio);
            log_message('error', $mes);
            log_message('error', $contrata);
            $this->m_evaluacion_eecc->getNivelAltoEvaluacionContratas($contrata, $anio, $mes, $check);
            $html = '';
            //$html .= $this->makeHTLMTablaConsulta22($this->m_reporte_cotizaciones->getNivelAltoEvaluacionContratas($contrata, $anio, $mes, $check)); 
            $data['contenido'] = $html;
            $data['error']    = EXIT_SUCCESS;
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

}