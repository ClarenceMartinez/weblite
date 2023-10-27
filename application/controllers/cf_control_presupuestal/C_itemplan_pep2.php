<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_itemplan_pep2 extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
		$this->load->model('mf_control_presupuestal/m_itemplan_pep2');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    function index() {
        $idUsuario      = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
			$data['json_bandeja'] = $this->getArrayPoBaVal($this->m_itemplan_pep2->getBolsaPep(1, null, null, null, null, null));//1 = solo activos
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 66, null, 72, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['cmbProyecto'] = __buildProyectoAll(NULL, NULL);
            $data['cmbFase']  = __buildComboFase();
            $this->load->view('vf_control_presupuestal/v_itemplan_pep2',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2, 'refresh');
	    }     
    }

    public function getArrayPoBaVal($listaBolsaPep){
        $listaFinal = array();      
        if($listaBolsaPep!=null){
            foreach($listaBolsaPep as $row){ 
                $btnOffConf = '';
                if($row['estado_id']==1){
                    $btnOffConf = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Inactivar" 
                                            onclick="inactivarConfig(this)" data-id="'.$row['id'].'">
                                            <i class="fal fa-power-off"></i>
                                        </a>';
                }
                $actions = $btnOffConf;
                 array_push($listaFinal, array($actions, $row['pep1'],$row['pep2'],$row['itemplan'], $row['subProyectoDesc'], $row['faseDesc'],$row['tipo_pep'], $row['estado'], $row['fecha_registro'],$row['nombre_completo']));
            }     
        }                                                            
        return $listaFinal;
    }

    public function filtrarBolsaPep(){
        $data['error']    = EXIT_ERROR;
        $data['msj']      = 'Hubo un error al filtrar los datos';
        $data['cabecera'] = null;
        try{    
            $pep1       = $this->input->post('pep1')        ? $this->input->post('pep1')    : null;
            $pep2       = $this->input->post('pep2')        ? $this->input->post('pep2')    : null;
            $proyecto   = $this->input->post('proyecto')    ? $this->input->post('proyecto') : null;
            $subpro     = $this->input->post('subpro')      ? $this->input->post('subpro')  : null;
            $fase       = $this->input->post('fase')        ? $this->input->post('fase')    : null;
            $estado     = $this->input->post('estado')      ? $this->input->post('estado')  : null;

         
            $data['json_bandeja']   = $this->getArrayPoBaVal($this->m_itemplan_pep2->getBolsaPep($estado, $pep1, $pep2, $proyecto, $subpro, $fase));
            $data['error']    = EXIT_SUCCESS;
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function registrarNuevaConfig(){
        $data['error']    = EXIT_ERROR;
        $data['msj']      = 'Hubo un error al filtrar los datos';
        $data['cabecera'] = null;
        try{    
            $itemplan           = $this->input->post('txtItemplan')     ? $this->input->post('txtItemplan')     : null;
            $txtpep1            = $this->input->post('txtpep1')         ? $this->input->post('txtpep1')         : null;
            $txtpep2            = $this->input->post('txtpep2')         ? $this->input->post('txtpep2')         : null;
            $selectTipoPepM     = $this->input->post('selectTipoPepM')  ? $this->input->post('selectTipoPepM')  : null;
            $idUsuario          = $this->session->userdata('idPersonaSessionPan');
            $fechaActual        = $this->m_utils->fechaActual();
            $listConfig = array();

          
            $config_ = array(   'itemplan'          =>  $itemplan,
                                'pep1'              =>  $txtpep1,
                                'pep2'              =>  $txtpep2,                                 
                                'fecha_registro'    =>  $fechaActual,
                                'usuario_registro'  =>  $idUsuario,
                                'estado'            =>  1,
                                'tipo_pep'          =>  $selectTipoPepM);

            array_push($listConfig, $config_);
            log_message('error', 'subpro:'.print_r($listConfig,true));
            $data = $this->m_itemplan_pep2->regBolsaPepConfig($listConfig);
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function saveInactivarConfigBoPep(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $id = $this->input->post('id') ? $this->input->post('id') : null;            
            $idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();
 
            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }
               		
            $arrayUpdate = array(                    
                "id" => $id,
                "estado" => 2,//INSTALADO
                "fecha_cancela" => $fechaActual,
                "usuario_cancela" => $idUsuario
            );              

            $data = $this->m_itemplan_pep2->actualizaConfigBoPep($arrayUpdate);
          
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

}