<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_bienvenido_planta extends CI_Controller {

    var $login;

    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_login/m_login');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
		// $this->load->database('db_planobra',true);
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, null, null, null, ID_MODULO_DESPLIEGUE_PLANTA);
            $data['opciones'] = $result['html'];
            $data['informacion_vista'] = "Despliegue Fija";
            $data['informacion_banner'] = "fondo.jpg";
           // $this->load->view('vf_bienvenida/v_bienvenido',$data);
			$arrayUsuario = $this->m_utils->getUsuario();
			$data['modulosTopFlotante'] = _getModulosFlotante();
           $this->load->view('vf_bienvenida/v_bienvenido',$data);        	  
    	 }else{
        	redirect(RUTA_OBRA2, 'refresh');
	    }     
    } 

}
