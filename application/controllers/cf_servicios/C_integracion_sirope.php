<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 *
 */
class C_integracion_sirope extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=ISO-8859-1');
        $this->load->model('mf_servicios/M_integracion_sirope');
        $this->load->library('lib_utils');
        $this->load->helper('url');
    }

    public function index()
    {	
		$items = array('22-2111100268',
'22-2111100253',
'22-2111100292',
'22-2111100295',
'22-2111100261',
'22-2110900068',
'22-2110900069',
'22-2111100262',
'22-2111100294',
'22-2111100290');
       
        foreach ($items as $var){
			$this->M_integracion_sirope->execWs($var, $var.'FO','2022-05-24','2022-06-01','PROJECT');
			//$this->M_integracion_sirope->execWs($var, $var.'COAX','2022-01-19','2022-01-26','PROJECT');
			// $this->M_integracion_sirope->execWs($var, $var.'AC','2021-08-02','2021-08-09','UPDATE_DATABASE');
        }
		//PROJECT
        //$this->M_integracion_sirope->execWs('20-0210900007','20-0210900007FO','2020-02-18','2020-02-22');
		//$this->M_integracion_sirope->getXMLexecWs('19-0111100333', '19-0111100333FO', '2019-12-18', '2019-12-22');
		//$this->M_integracion_sirope->execWsFilter('20-0320300072','20-0320300072FO');
       // $this->load->view('vf_sirope/v_test_ws');
    
    }
	
}