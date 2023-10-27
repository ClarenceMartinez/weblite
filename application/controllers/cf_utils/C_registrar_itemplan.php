<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registrar_itemplan extends CI_Controller {

    var $login;

    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_solicitud_Vr');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_OBRA_PADRE, null, ID_REGISTRO_ITEMPLAN_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header']   = $this->lib_utils->getHeader();
            $data['cmbFase']  = __buildComboFase();
            $data['cmbEecc']  = __buildComboEECC();
            $data['cmbCentral']  = __buildCmbMdfOnlyCentral();
            $data['cmbProyecto'] = __buildProyectoAllCreateIP(NULL, NULL);
            $this->load->view('vf_utils/v_registrar_itemplan',$data);        	  
    	 }else{
        	redirect('login','refresh');
	    }
    }
    
    function getSubProyectoByProyecto() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $idProyecto = $this->input->post('idProyecto');

            if($idProyecto == null || $idProyecto == '') {
                throw new Exception("Ingresar el proyecto");
            }

            $data['error'] = EXIT_SUCCESS;
            $data['cmbSubProyecto'] = __buildComboSubProyectoAll($idProyecto, NULL);
			 if($idProyecto  ==  55){
                $data['cmbDivCayOym'] = __buildComboDivCauOyM(); 
            }
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getItemplanMadreFactorMed() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $idSubProyecto = $this->input->post('idSubProyecto');

            if ($idSubProyecto == null) {
                throw new Exception("Ingresar el subproyecto");
            }

            $infoFactorMedicion = $this->m_utils->getFactorDeMedicionXIdSubProyecto($idSubProyecto);
            $data['descFactorMedicion'] = $infoFactorMedicion['descPqtTipoFactorMedicion'];
            $data['idFactorMedicion']   = $infoFactorMedicion['idPqtTipoFactorMedicion'];
            $data['cmbEeccElec']  = __buildComboEECCElec();
            $data['cmbItemMadre'] = __buildCmbItemplanMadre(null, $idSubProyecto, 2);

            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getCodInverByEECCToChoice() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            
            $idEmpresaColab = $this->input->post('idEecc');
            if ($idEmpresaColab == null) {
                throw new Exception("Seleccione EECC");
            }

            $data['cmbInversion']     = __buildComboInversion($idEmpresaColab);

            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    
}
