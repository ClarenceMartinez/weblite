<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registrar_cap extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_bandeja_aprobacion_po_mat');
        $this->load->library('lib_utils');
		$this->load->library('excel');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_CAP_REQUERIMIENTO_PADRE, null, ID_REGISTRAR_CAP_HIJO, ID_MODULO_DESPLIEGUE_PLANTA);
            $data['opciones'] = $result['html'];
            // $data['tablaAprobacionPo'] = $this->getTablaConsulta(null, null);
			$data['modulosTopFlotante'] = _getModulosFlotante();
            $data['cmbTipoReq']     = __buildComboTipoReq(null);
            $data['cmbTipoProy']    = __buildComboTipoProyecto(null);
            $data['cmbTipoAreaReq'] = __buildComboAreaReq(null);
            $this->load->view('vf_cap_requerimiento/v_registrar_cap',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    function getMotivoResponsableCap() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $idAreaReq = $this->input->post('idAreaReq');

            $data['cmbMotivo'] = __buildComboMotivoByAreaReq($idAreaReq);

            $data['error'] = EXIT_SUCCESS;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getResponsableCap() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $idMotivo = $this->input->post('idMotivo');

            $data['objUsuario'] = $this->m_utils->getResponsableCapByMotivo($idMotivo);

            $data['error'] = EXIT_SUCCESS;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function registrarRequerimiento() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $objJson = json_decode($this->input->post('objJson'), true);

            $codigoReq    = $this->m_utils->getCodigoRequerimiento();
            $fechaActual = _fecha_actual();
            $idUsuario   = $this->session->userdata('idPersonaSessionPan');

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }

            $dataTipoReq = $this->m_utils->getTipoRequerimientoById($objJson['id_tipo_requerimiento']);

            $objJson['codigo_requerimiento']     = $codigoReq;
            $objJson['id_estado_requerimiento']  = CAP_ESTADO_PENDIENTE;
            $objJson['fecha_registro']           = $fechaActual;
            $objJson['id_usuario_registro']      = $idUsuario;
            $objJson['sla']                      = $dataTipoReq['sla'];

            $data = $this->m_utils->registrarRequerimientoCap($objJson);

            $data['codigoReq'] = $codigoReq;
        } catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
}