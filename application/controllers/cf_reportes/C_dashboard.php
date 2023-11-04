<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_dashboard extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_reportes/m_dashboard'); 
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        $this->load->library('excel');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
  /*      $idUsuario      = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');

	    if($idUsuario != null){
*/ 
   //         $permisos = $this->session->userdata('permisosArbolPan');
           // $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, ID_ATEN_SOL_OC_CREA_HIJO, null);
           $data['cmbProyecto'] = __buildProyectoAll(NULL, NULL);
            $data['opciones'] = '';//$result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_reportes/v_dashboard',$data);        	  
    	/* }else{
            redirect(RUTA_OBRA2, 'refresh');
	    }*/
    }

    public function makeDataColum(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $idProyecto = $this->input->post('idProyecto');
          
            $drillDrown = array();  
            $listDrillDrown = $this->m_dashboard->getDataToReportColumEECC($idProyecto);
            $series = array();        
            $lista_series = $this->m_dashboard->getDataToReportColumEstadoPlan($idProyecto);
            foreach($lista_series as $row){
                $serie_1 = array('name'=> $row['estadoPlanDesc'], 'y' => intval($row['total']), 'drilldown'=> $row['estadoPlanDesc']);
                array_push($series, $serie_1);      
                
                
                $dataDrow = array();
                foreach($listDrillDrown as $row2){
                    if($row2['idEstadoPlan']    ==  $row['idEstadoPlan']){
                        $dataDronPlan = array($row2['empresaColabDesc'], intval($row2['total']));
                        array_push($dataDrow, $dataDronPlan);          
                    }                       
                }

                $drow_1 = array('name'=> $row['estadoPlanDesc'], 'id'=> $row['estadoPlanDesc'],'data'=> $dataDrow);
                array_push($drillDrown, $drow_1);          
            }


            $drillDrown2 = array();  
            $listDrillDrown2 = $this->m_dashboard->getDataToReportColumEECCUIP($idProyecto);
            $series2 = array();        
            $lista_series2 = $this->m_dashboard->getDataToReportColumEstadoPlanUIP($idProyecto);
            foreach($lista_series2 as $row){
                $serie_1 = array('name'=> $row['estadoPlanDesc'], 'y' => intval($row['total']), 'drilldown'=> $row['estadoPlanDesc']);
                array_push($series2, $serie_1);      
                
                
                $dataDrow2 = array();
                foreach($listDrillDrown2 as $row2){
                    if($row2['idEstadoPlan']    ==  $row['idEstadoPlan']){
                        $dataDronPlan = array($row2['empresaColabDesc'], intval($row2['total']));
                        array_push($dataDrow2, $dataDronPlan);          
                    }                       
                }

                $drow_1 = array('name'=> $row['estadoPlanDesc'], 'id'=> $row['estadoPlanDesc'],'data'=> $dataDrow2);
                array_push($drillDrown2, $drow_1);          
            }

        
            $salida = array('serie' =>$series, 'drillDrwon' => $drillDrown, 'serieuip' =>$series2, 'drillDrwonuip' => $drillDrown2);
            //log_message('error', print_r($salida,true));
        } catch (Exception $e) {         
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($salida);    
    }

}
