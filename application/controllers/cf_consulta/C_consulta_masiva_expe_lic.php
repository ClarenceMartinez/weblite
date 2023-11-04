<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_consulta_masiva_expe_lic extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_consulta_masiva_expe_lic'); 
        $this->load->library('lib_utils');
        $this->load->library('zip');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_OBRA_PADRE, null, 63, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoBaVal(/*$this->m_consulta_licencias->getConsultaEvidencias(null, null)*/null);
            $data['cmbProyecto'] = __buildProyectoAll(NULL, NULL);            
            $data['cmbEstadoPlan']  = __buildComboEstadoPlan();         
            $this->load->view('vf_consulta/v_consulta_masiva_expe_lic',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    public function getArrayPoBaVal($listaCotiB2b){
        $output = array();
        $num_files_down = 0;
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $row){
                $existFile = 'NO';
                $name_file = '';
                $filePath   = $row['ubicacion_evidencia'];
                if(file_exists($filePath)){
                    $existFile  = 'SI';
                    $name_file  = $row['file_name'];
                    $num_files_down++;
                }                 									 
                 array_push($listaFinal, array($row['itemplan'],$row['proyectoDesc'], $row['subProyectoDesc'], $row['empresaColabDesc'], $row['entidadDesc'],$existFile,$name_file));
            }     
        }             
        $output['array'] =   $listaFinal;
        $output['num_val_down'] =   $num_files_down;                                                   
        return $output;
    } 

    public function filtrarTabla()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$itemplanListArea       = $this->input->post('txt_itemplan_list')      ? $this->input->post('txt_itemplan_list') : null;      
            $itemplanList = explode(",", $itemplanListArea);       
			if($itemplanList == null){
				throw new Exception('Debe ingresar mínimo un filtro para buscar!!');
			}
			$itemplanList = array_slice($itemplanList,0,500);
            $output = $this->getArrayPoBaVal($this->m_consulta_masiva_expe_lic->getEvidenciasLicenciaByItemLiteToTable($itemplanList));
            
            $data['json_bandeja'] = $output['array'];
            $data['num_val_down'] = $output['num_val_down'];
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
 
	 
    public function downloadExpedientesLicenciaByItemplanList() {
        $itemplanListArea = (isset($_GET['dat']) ? $_GET['dat'] : ''); 
     //   log_message('error', $itemplanListArea);
        $itemplanList = explode(",", $itemplanListArea);
    //   log_message('error', print_r( $itemplanList,true)); 
		$itemplanList = array_slice($itemplanList,0,500);
        $detList = $this->m_consulta_masiva_expe_lic->getEvidenciasLicenciaByItemLite($itemplanList);
        if($detList != null){     
            $filename = 'evidencias_licencia';
            foreach($detList as $detItem){
                $filePath   = $detItem->ubicacion_evidencia;   
                if(file_exists($filePath)){
                    $itemplan   = $detItem->itemplan;                                 
                    $destiny = $itemplan.'/'.$detItem->file_name;
                   // log_message('error',$filePath.'|'.$destiny);
                    $this->zip->read_file($filePath, $destiny);
                }
            }
            $this->zip->download($filename . '.zip');
            $this->zip->clear_data();
        } 

    }
}
