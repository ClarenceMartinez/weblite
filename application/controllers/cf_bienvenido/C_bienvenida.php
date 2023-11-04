<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_bienvenida extends CI_Controller {

    var $login;

    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_login/m_login');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, null, null, null,null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
           // $this->load->view('vf_bienvenida/v_bienvenido',$data);        	  
           $this->load->view('vf_bienvenida/v_bienvenida',$data);        	  
    	 }else{
        	 redirect('login','refresh');
	    }     
    } 

}
