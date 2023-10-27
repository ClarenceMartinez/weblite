<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_matriz_seguimiento extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_matriz_seguimiento/m_matriz_seguimiento');
        $this->load->library('lib_utils');
        $this->load->library('excel');
        $this->load->helper('url');
    }

    public function index()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');	   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);


            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_matriz_seguimiento/index',$data);        	  
    	 }else{
        	redirect('login','refresh');
	    }  
    }

    public function index2()
    {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
        $username   = $this->session->userdata('usernameSession');
        if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');     
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);


            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['nodos']  = $this->m_matriz_seguimiento->listNodos();
            $this->load->view('vf_matriz_seguimiento/index2',$data);           
         }else{
            redirect('login','refresh');
        }  
    }

    public function showUploadMatSeg()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');	   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);


            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_matriz_seguimiento/upload',$data);        	  
    	 }else{
        	redirect('login','refresh');
	    }  
    }

    public function showUploadMatSegm()
    {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
        $username   = $this->session->userdata('usernameSession');
        if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');     
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);


            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_matriz_seguimiento/uploads',$data);
         }else{
            redirect('login','refresh');
        }  
    }


    public function getCableByNodo()
    {
        $data               = [];
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $nodo   = $this->input->post('nodo');
            $lista  = $this->m_matriz_seguimiento->getCableByNodo(trim($nodo));
            $data['lista'] = $lista;
            $data['total'] = count($lista);
        } catch (Exception $e) {
         $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


    public function getInfoCableNodo()
    {
        $data               = [];
        $data['error']    = EXIT_SUCCESS;
        $data['msj']      = null;
        try {
            $nodo   = $this->input->post('nodo');
            $cable   = $this->input->post('cable');
            $lista  = $this->m_matriz_seguimiento->getInfoCableNodo(trim($nodo), trim($cable));
            $data['lista'] = $lista;
            $data['total'] = count($lista);
        } catch (Exception $e) {
         $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function saveMatSegInfoPIN()
    {
        $data               = [];
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            
        $cable                      = $this->input->post('cable');
        $nodo                       = $this->input->post('nodo');
        $FechaJumpeoCentral         = $this->input->post('FechaJumpeoCentral');
        $numHilosPuertoOLT         = $this->input->post('numHilos');
        $statusPin                  = $this->input->post('statusPin');

        $info  = $this->m_matriz_seguimiento->getInfoCableNodo(trim($nodo), trim($cable));
        

        $arrayUpdate = array(                    
                "FechaJumpeoCentral"        => $FechaJumpeoCentral,
                "estadoPin"                 => $statusPin,
            ); 

        $data_old = array(
            "numHilosPuertoOLT"     => @$info[0]['numHilosPuertoOLT'],
            "FechaJumpeoCentral"    => @$info[0]['FechaJumpeoCentral'],
            "estadoPin"             => @$info[0]['estadoPin'],
            );        

            $invoque1 = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoPIN1($arrayUpdate, $cable, $nodo);
    
            if (count($info)> 0)
            {
                $item = $info[0];
                $this->m_matriz_seguimiento->actualizaMatrizSeguimientoPIN2(["numHilosPuertoOLT" => $numHilosPuertoOLT], $item['id']);
            }

            $data['error']    = EXIT_SUCCESS;
        } catch (Exception $e)
        {
         $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);

    }


    public function getByItemPlan()
    {

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan');

            if($itemplan == null || $itemplan == '') {
                throw new Exception('itemplan no existente, comunicarse con el programador a cargo');
            }
            $data['error']   = EXIT_SUCCESS;
            $dataArrayDetalle = $this->m_matriz_seguimiento->getByItemPlan($itemplan);

            $data['lista'] = $dataArrayDetalle;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function getInfoMatrizSegByItemPlan()
    {

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan');
            $cbodivicau = $this->input->post('cbodivicau');

            if($itemplan == null || $itemplan == '') {
                throw new Exception('itemplan no existente, comunicarse con el programador a cargo');
            }

            if($cbodivicau == null || $cbodivicau == '') {
                throw new Exception('cbodivicau no existente, comunicarse con el programador a cargo');
            }
            $data['error']   = EXIT_SUCCESS;
            $dataArrayDetalle = $this->m_matriz_seguimiento->getInfoMatrizSegByItemPlan(trim($itemplan), trim($cbodivicau));



            $data['lista'] 	= $dataArrayDetalle;
            $data['log']    = [];

            if (count($dataArrayDetalle) > 0)
            {
                $data['log'] 	= $this->getLogDataTable($this->m_matriz_seguimiento->getInfoMatrizSegByItemPlanLogByID(trim($dataArrayDetalle[0]['id'])));
            }


        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


    public function getInfoMatrizSegByItemPlan2()
    {

        $data               = [];
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan');

            if($itemplan == null || $itemplan == '') {
                throw new Exception('itemplan no existente, comunicarse con el programador a cargo');
            }
            $data['error']   = EXIT_SUCCESS;
            $dataArrayDetalle = $this->m_matriz_seguimiento->getInfoMatrizSegByItemPlan2(trim($itemplan));



            $data['lista']  = $dataArrayDetalle;
            $data['log']    = $this->getLogDataTable($this->m_matriz_seguimiento->getInfoMatrizSegLogByItemPlan(trim($itemplan)));
            


        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


    public function getInfoMatrizSeguimientoByItempanAndDivicau()
    {

        $data               = [];
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan');
            $cbodivicau = $this->input->post('cbodivicau');

            if($itemplan == null || $itemplan == '') {
                throw new Exception('itemplan no existente, comunicarse con el programador a cargo');
            }

            if($cbodivicau == null || $cbodivicau == '') {
                throw new Exception('cbodivicau no existente, comunicarse con el programador a cargo');
            }
            $data['error']   = EXIT_SUCCESS;
            $dataArrayDetalle = $this->m_matriz_seguimiento->getInfoMatrizSegByItemPlan(trim($itemplan), trim($cbodivicau));



            $data['lista']  = $dataArrayDetalle;
            $data['log']    = [];

            if (count($dataArrayDetalle) > 0)
            {
                $data['log']    = $this->getLogDataTable($this->m_matriz_seguimiento->getInfoMatrizSegByItemPlanLogByID(trim($dataArrayDetalle[0]['id'])));
            }


        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }



    public function getLogDataTable($data){
        $listaFinal = array();      
        if($data!=null){
        	$key = 1;
            foreach($data as $row){ 
                $btnValidar = null;
						$btnPdt = '';
                        
                                $btnValidar = $key;
                           
                        		$btnDetalle = '';
                        		$btnPdt = '';

                        // $btnDetalle = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver Detalle" 
                        //                 data-codigo_solicitud="'.$row['itemplan'].'" onclick="openModalDetalleSolicitudOc($(this));"><i class="fal fa-envelope-open-text"></i></a>
                        //                 ';   

                
                // array_push($listaFinal, array($btnDetalle.' '.$btnValidar.' '.$btnPdt,
                //     $row['modulo'], $row['usuario_id'], $row['fecha_registro'],$row['data_new'],$row['data_old']));



                $datosImpactados = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver Detalle" 
                                        data-new="'.base64_encode(($row['data_new'])).'" data-old="'.base64_encode(($row['data_old'])).'"  onclick="openModalDetalleLogMatrizSeg($(this));"><i class="fal fa-envelope-open-text"></i></a> ';



                
                array_push($listaFinal, array($key, $row['modulo'], $row['nombre_completo'], $row['fecha_registro'],$datosImpactados));




                $key++;
            }      
        }  
        return $listaFinal;
    }

    public function postUpdateMatrizSeguimientoDiseno()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  				= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $itemplan 					= $this->input->post('itemplan');
            $divicau 					= $this->input->post('divicau');
            $anio 						= $this->input->post('anio');
            $plan 						= $this->input->post('plan');
            $nodo 						= $this->input->post('nodo');
            $modelo 					= $this->input->post('modelo');
            $cable 						= $this->input->post('cable');
            $tipo  						= $this->input->post('tipo');
            $troba  					= $this->input->post('troba');
            // $uipHorizonal  				= $this->input->post('uipHorizonal');
            $fechaAdjudicaDiseno  		= $this->input->post('fechaAdjudicaDiseno');
            $fechaCierreDisenoExpediente= $this->input->post('fechaCierreDisenoExpediente');
            $fechaEntregaDiseno  		= $this->input->post('fechaEntregaDiseno');
            $estadoDiseno  				= $this->input->post('estadoDiseno');
            $id  						= $this->input->post('_id');


            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $data_old = $this->db->select('id, anio, plan, modelo, cable, tipo, modelo, troba, fechaAdjudicaDiseno, fechaCierreDisenoExpediente, fechaEntregaDiseno, estadoDiseno')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];
               		
            $arrayUpdate = array(                    
                "id" 							=> $id,
                "anio" 							=> $anio,
                "plan" 							=> $plan,
                "modelo" 						=> $modelo,
                "cable" 						=> $cable,
                "tipo" 							=> $tipo,
                "modelo" 						=> $modelo,
                "troba" 						=> $troba,
                "fechaAdjudicaDiseno" 			=> $fechaAdjudicaDiseno,
                "fechaCierreDisenoExpediente" 	=> $fechaCierreDisenoExpediente,
                "fechaEntregaDiseno" 			=> $fechaEntregaDiseno,
                "estadoDiseno" 					=> $estadoDiseno
            );              

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old, $idUsuario, $id, 'Diseño');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);

    }

    public function postUpdateMatrizSeguimientoEconomico()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  				= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $pptoAprobado 				= $this->input->post('pptoAprobado');
            $pep 						= $this->input->post('pep');
            $ocConstruccionH 			= $this->input->post('ocConstruccionH');
            $generacionVR 				= $this->input->post('generacionVR');
            $estadoOC 					= $this->input->post('estadoOC');
            $estadoCertificaOC 			= $this->input->post('estadoCertificaOC');
            $id  						= $this->input->post('_id');


            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $data_old = $this->db->select('id, pptoAprobado, pep, ocConstruccionH, generacionVR, estadoOC, estadoCertificaOC')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];
               		
               		
            $arrayUpdate = array(                    
                "id" 							=> $id,
                "pptoAprobado" 					=> $pptoAprobado,
                "pep" 							=> $pep,
                "ocConstruccionH" 				=> $ocConstruccionH,
                "generacionVR" 					=> $generacionVR,
                "estadoOC" 						=> $estadoOC,
                "estadoCertificaOC" 			=> $estadoCertificaOC,
            );              

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old, $idUsuario, $id, 'Economico');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);
    }
    public function postUpdateMatrizSeguimientoLicencia()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  				= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id  						= $this->input->post('_id');
            $fechaPresentaLicencia 		= $this->input->post('fechaPresentaLicencia');
            $fechaInicioLicencia 		= $this->input->post('fechaInicioLicencia');
            $estadoLicencia 			= $this->input->post('estadoLicencia');

            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $data_old = $this->db->select('id, fechaPresentaLicencia, fechaInicioLicencia, estadoLicencia')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];
               		
            $arrayUpdate = array(                    
                "id" 							=> $id,
                "fechaPresentaLicencia" 		=> $fechaPresentaLicencia,
                "fechaInicioLicencia" 			=> $fechaInicioLicencia,
                "estadoLicencia" 				=> $estadoLicencia,
            );              

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old, $idUsuario, $id, 'Licencia');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);	
    }
    public function postUpdateMatrizSeguimientoLogistica()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  				= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id  						= $this->input->post('_id');
            $entregaMateriales 		= $this->input->post('entregaMateriales');
            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }


            $data_old = $this->db->select('id, entregaMateriales')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];
               		
            $arrayUpdate = array(                    
                "id" 					=> $id,
                "entregaMateriales" 	=> $entregaMateriales,
            );              

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old, $idUsuario, $id, 'Logistica');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);		
    }
    public function postUpdateMatrizSeguimientoPIN()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  				= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id  						= $this->input->post('_id');
            $numHilosPuertoOLT 		= $this->input->post('numHilosPuertoOLT');
            $FechaJumpeoCentral 	= $this->input->post('FechaJumpeoCentral');
            $estadoPin 				= $this->input->post('estadoPin');
            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $data_old = $this->db->select('id, numHilosPuertoOLT, FechaJumpeoCentral, estadoPin')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];
               		
            $arrayUpdate = array(                    
                "id" 					=> $id,
                "numHilosPuertoOLT" 	=> $numHilosPuertoOLT,
                "FechaJumpeoCentral" 	=> $FechaJumpeoCentral,
                "estadoPin" 			=> $estadoPin
            );    

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old, $idUsuario, $id, 'PIN');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);	
    }
    public function postUpdateMatrizSeguimientoCensado()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  				= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id  						= $this->input->post('_id');
            $fechaCensado 				= $this->input->post('fechaCensado');
            $UIPHorizontalCenso 		= $this->input->post('UIPHorizontalCenso');
            $estadoCenso 				= $this->input->post('estadoCenso');
            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $data_old = $this->db->select('id, fechaCensado, UIPHorizontalCenso, estadoCenso')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];
               		
            $arrayUpdate = array(                    
                "id" 					=> $id,
                "fechaCensado" 			=> $fechaCensado,
                "UIPHorizontalCenso" 	=> $UIPHorizontalCenso,
                "estadoCenso" 			=> $estadoCenso
            );              

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old, $idUsuario, $id, 'Censado');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);	
    }
    public function postUpdateMatrizSeguimientoDespliegue()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  				= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id  						= $this->input->post('_id');
            $fechaInstalacionODF 		= $this->input->post('bandejaODF');
            $fechaInicioConstruccion 	= $this->input->post('fechaInicioConstruccion');
            $fechaProyectadaEntrega 	= $this->input->post('fechaProyectadaEntrega');
            $fechaFinalEntregaDivicau 	= $this->input->post('fechaFinalEntregaDivicau');
            $estadoDespliegue 			= $this->input->post('estadoDespliegue');
            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $data_old = $this->db->select('id, fechaInstalacionODF, fechaInicioConstruccion, fechaProyectadaEntrega, fechaFinalEntregaDivicau, estadoDespliegue')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];
               		
            $arrayUpdate = array(                    
                "id" 							=> $id,
                "fechaInstalacionODF" 			=> $fechaInstalacionODF,
                "fechaInicioConstruccion" 		=> $fechaInicioConstruccion,
                "fechaProyectadaEntrega" 		=> $fechaProyectadaEntrega,
                "fechaFinalEntregaDivicau" 		=> $fechaFinalEntregaDivicau,
                "estadoDespliegue" 				=> $estadoDespliegue
            );              

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old, $idUsuario, $id, 'Despliegue');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);
    }
    public function postUpdateMatrizSeguimientoHGU()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  		= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id  				= $this->input->post('_id');
            $fechaPruebaHGU 	= $this->input->post('fechaPruebaHGU');
            $comodinAvanceHGU 	= $this->input->post('comodinAvanceHGU');
            $estadoHGU 			= $this->input->post('estadoHGU');
            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $data_old = $this->db->select('id, fechaPruebaHGU, comodinAvanceHGU, estadoHGU')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];
               		
            $arrayUpdate = array(                    
                "id" 					=> $id,
                "fechaPruebaHGU" 		=> $fechaPruebaHGU,
                "comodinAvanceHGU" 		=> $comodinAvanceHGU,
                "estadoHGU" 			=> $estadoHGU,
            );              

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old,  $idUsuario, $username, 'HGU');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);
    }
    public function postUpdateMatrizSeguimientoStatus()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
        	$idUsuario  		= $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id  				= $this->input->post('_id');
            $estadoFinal 	= $this->input->post('estadoFinal');
            $estadoGlobal 	= $this->input->post('estadoGlobal');
            

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $data_old = $this->db->select('id, estadoFinal, estadoGlobal')
            				->where('id', $id)
            				->get('matrizseguimiento')
            				->result()[0];

            $arrayUpdate = array(                    
                "id" 					=> $id,
                "estadoFinal" 		=> $estadoFinal,
                "estadoGlobal" 		=> $estadoGlobal,
            );              

            $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimiento($arrayUpdate, $data_old, $idUsuario, $id, 'Status');

        } catch(Exception $e) {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);
    }

    public function getFormatoExcelCarga() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Clarence Martinez')
				->setLastModifiedBy('Clarence Martinez')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Excel de prueba')
				->setDescription('Excel generado como prueba')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de prueba');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->setTitle('FT CARGA MATRIZ');

            $col = 0;
            $row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'N°');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'AÑO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NODO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'MODELO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CABLE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TIPO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TROBA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'UIP HORIZONTAL DISEÑO');
			$col++;			 		
			$hoja->setCellValueByColumnAndRow($col, $row, '% PENETRACION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PROVINCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DEPARTAMENTO');
			$col++;			 		
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ADJUDICACION DISEÑO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA CIERRE DISEÑO EXPEDIENTE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA CIERRE OSP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ENTREGA');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTATUS DISEÑO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PPTO APROBADO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PEP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'OC CONSTRUCCION HORIZONTAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'GENERACION DE VR');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS OC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CERTIFICACION');
			$col++;


			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE PRESENTACION DE LICENCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE INICIO DE LICENCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTATUS LICENCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ENTREGA DE MATERIALES');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD DE HILOS O PUERTOS OLT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA JUMPLEO CENTRAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTATUS PIN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE CENSADO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'UIP HORIZONTAL CENSO');
			$col++;

			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS CENSO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ODF/BANDEJA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA INSTALACION ODF');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA INICIO DE CONSTRUCCION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE ENTREGA PROYECTADA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE ENTRAGA FINAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS DESPLIEGUE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE PRUEBA HGU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, '% DE AVANCE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS HGU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS FINAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS GLOBAL');
			$col++;

            $estiloTituloColumnas = array(
                'font' => array(
                    'name' => 'Calibri',
                    'bold' => true,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
            );

            $hoja->getStyle('A1:F1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
			ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

			$data['error'] = EXIT_SUCCESS;
			$data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }


    public function getFormatoExcelCargaMin() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $spreadsheet = $this->excel;

            $spreadsheet
                ->getProperties()
                ->setCreator('Clarence Martinez')
                ->setLastModifiedBy('Clarence Martinez')
                ->setTitle('Excel creado con PhpSpreadSheet')
                ->setSubject('Excel de prueba')
                ->setDescription('Excel generado como prueba')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Categoría de prueba');

            $hoja = $spreadsheet->getActiveSheet();
            $hoja->setTitle('FT CARGA MATRIZ');

            $col = 0;
            $row = 1;
            // $hoja->setCellValueByColumnAndRow($col, $row, 'N°');
            // $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'AÑO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'PLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'MODELO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CABLE');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TROBA');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '% PENETRACION');
            $col++;
            

            $estiloTituloColumnas = array(
                'font' => array(
                    'name' => 'Calibri',
                    'bold' => true,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );

            $hoja->getStyle('A1:H1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
            ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $data['error'] = EXIT_SUCCESS;
            $data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento__' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }

    public function procesarFileMatrizSeguimiento()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$idUsuarioSession = $this->session->userdata('idPersonaSessionPan');

			if (!isset($idUsuarioSession)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
			if(count($_FILES) == 0){
				throw new Exception('Debe seleccionar un archivo para procesar data!!');
			}

            $nombreArchivo = $_FILES['file']['name'];
            $tipoArchivo = $_FILES['file']['type'];
            $nombreFicheroTemp = $_FILES['file']['tmp_name'];
            $tamano_archivo = $_FILES['file']['size'];

            $arryNombreArchivo = explode(".", $nombreArchivo);

            $arrayTipos = array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.ms-excel'
			);

            if (!in_array($tipoArchivo, $arrayTipos)) {
                throw new Exception('Sólo puede subir archivos de tipo excel (.xls , .xlsx)!!');
            }

            if (!file_exists("./uploads/aten_masivo_sol_coti_b2b_pan")) {
                if (!mkdir("./uploads/aten_masivo_sol_coti_b2b_pan")) {
                    throw new Exception('Hubo un error al crear la carpeta aten_masivo_sol_oc_creacion_pan!!');
                }
            }

			








			$html = '
                <table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
							<th style="text-align: center; vertical-align: middle;">OBSERVACION</th>                           
							<th style="text-align: center; vertical-align: middle;">AÑO</th>                           
                            <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                            <th style="text-align: center; vertical-align: middle;">PLAN</th>
							<th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                            <th style="text-align: center; vertical-align: middle;">NODO</th>
  							<th style="text-align: center; vertical-align: middle;">EMPRESA COLABORADORA</th>
                            <th style="text-align: center; vertical-align: middle;">MODELO</th>
                            <th style="text-align: center; vertical-align: middle;">CABLE</th>
                            <th style="text-align: center; vertical-align: middle;">TIPO</th>
                            <th style="text-align: center; vertical-align: middle;">TROBA</th>
                            <th style="text-align: center; vertical-align: middle;">UIP HORIZONTAL DISEÑO</th>
                            <th style="text-align: center; vertical-align: middle;">% PENETRACION</th>
                            <th style="text-align: center; vertical-align: middle;">DISTRITO</th>
                            <th style="text-align: center; vertical-align: middle;">PROVINCIA</th>
                            <th style="text-align: center; vertical-align: middle;">DEPARTAMENTO</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA ADJUDICACION DISEÑO</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA CIERRE DISEÑO EXPEDIENTE</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA CIERRE OSP</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA ENTREGA</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS DISEÑO</th>
                            <th style="text-align: center; vertical-align: middle;">PPTO APROBADO</th>
                            <th style="text-align: center; vertical-align: middle;">PEP</th>
                            <th style="text-align: center; vertical-align: middle;">OC CONSTRUCCION HORIZONTAL</th>
                            <th style="text-align: center; vertical-align: middle;">GENERACION VR</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS OC</th>
                            <th style="text-align: center; vertical-align: middle;">CERTIFICACION</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA PRESENTACION DE LICENCIA</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA INICIO LICENCIA</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS LICENCIA</th>
                            <th style="text-align: center; vertical-align: middle;">ENTREGA MATERIALES</th>
                            <th style="text-align: center; vertical-align: middle;">CANTIDAD DE HILOS PUERTOS OLT</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA JUMPLEO CENTRAL</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS PIN</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA DE CENSADO</th>
                            <th style="text-align: center; vertical-align: middle;">UIP HORIZONTAL CENSO</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS CENSO</th>
                            <th style="text-align: center; vertical-align: middle;">ODF BANDEJA</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA INSTALACION ODF</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA INICIO CONSTRUCCION</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA ENTREGA PROYECTADA</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA ENTREGA FINAL</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS DESPLIEGUE</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA DE PRUEBA HGU</th>
                            <th style="text-align: center; vertical-align: middle;">PORCENTAJE DE AVANCE</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS HGU</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS FINAL</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS GLOBAL</th>
                        </tr>
                    </thead>

                    <tbody>';

        $count = 1;
		$arrayFinal 			= array();
		$arrayTabla             = array();
		$dataArray              = array();
        $arrayOC 				= array();
		$ctnVal = 0;
        $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
        if ($objectExcel != '')
        {
			$col = 1;
			foreach ($objectExcel->getWorksheetIterator() as $worksheet)
			{
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();

				for ($row = 2; $row <= $highestRow; $row++)
				{
					$col = 0;
                    $n_orden = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$n_orden = _removeEnterYTabs(trim(utf8_encode(utf8_decode($n_orden)),'?'));
					$col++;

                    $anio = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$anio = _removeEnterYTabs(trim(utf8_encode(utf8_decode($anio)),'?'));
					$col++;
					$divicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$divicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($divicau)),'?'));
					$col++;
					$plan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$plan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($plan)),'?'));
					$col++;
					$itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
					$col++; 
					$nodo = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$nodo = _removeEnterYTabs(trim(utf8_encode(utf8_decode($nodo)),'?'));
					$col++; 
					$empresaColabDesc = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$empresaColabDesc = _removeEnterYTabs(trim(utf8_encode(utf8_decode($empresaColabDesc)),'?'));
                    $col++;                 
                    $modelo = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$modelo = _removeEnterYTabs(trim(utf8_encode(utf8_decode($modelo)),'?'));
                    $col++;
                    $cable = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$cable = _removeEnterYTabs(trim(utf8_encode(utf8_decode($cable)),'?'));
                    $col++;

                    $tipo = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$tipo = _removeEnterYTabs(trim(utf8_encode(utf8_decode($tipo)),'?'));
                    $col++;

                    $troba = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$troba = _removeEnterYTabs(trim(utf8_encode(utf8_decode($troba)),'?'));
                    $col++;

                    $UIPHorizontalDiseno = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$UIPHorizontalDiseno = _removeEnterYTabs(trim(utf8_encode(utf8_decode($UIPHorizontalDiseno)),'?'));
                    $col++;

                    $comodinPenetracion = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$comodinPenetracion = _removeEnterYTabs(trim(utf8_encode(utf8_decode($comodinPenetracion)),'?'));
                    $col++;

                    $distrito = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$distrito = _removeEnterYTabs(trim(utf8_encode(utf8_decode($distrito)),'?'));
                    $col++;

                    $provincia = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$provincia = _removeEnterYTabs(trim(utf8_encode(utf8_decode($provincia)),'?'));
                    $col++;

                    $departamento = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$departamento = _removeEnterYTabs(trim(utf8_encode(utf8_decode($departamento)),'?'));
                    $col++;

                    $fechaAdjudicaDiseno = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaAdjudicaDiseno = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaAdjudicaDiseno)),'?'));
                    $col++;

                    $fechaCierreDisenoExpediente = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaCierreDisenoExpediente = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaCierreDisenoExpediente)),'?'));
                    $col++;

                    $fechaCierreOSP = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaCierreOSP = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaCierreOSP)),'?'));
                    $col++;

                    $fechaEntregaDiseno = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaEntregaDiseno = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaEntregaDiseno)),'?'));
                    $col++;


                    $estadoDiseno = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoDiseno = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoDiseno)),'?'));
                    $col++;

                    $pptoAprobado = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$pptoAprobado = _removeEnterYTabs(trim(utf8_encode(utf8_decode($pptoAprobado)),'?'));
                    $col++;

                    $pep = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$pep = _removeEnterYTabs(trim(utf8_encode(utf8_decode($pep)),'?'));
                    $col++;

                    $ocConstruccionH = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$ocConstruccionH = _removeEnterYTabs(trim(utf8_encode(utf8_decode($ocConstruccionH)),'?'));
                    $col++;

                    $generacionVR = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$generacionVR = _removeEnterYTabs(trim(utf8_encode(utf8_decode($generacionVR)),'?'));
                    $col++;

                    $estadoOC = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoOC = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoOC)),'?'));
                    $col++;

                    $estadoCertificaOC = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoCertificaOC = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoCertificaOC)),'?'));
                    $col++;

                    $fechaPresentaLicencia = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaPresentaLicencia = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaPresentaLicencia)),'?'));
                    $col++;

                    $fechaInicioLicencia = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaInicioLicencia = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaInicioLicencia)),'?'));
                    $col++;

                    $estadoLicencia = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoLicencia = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoLicencia)),'?'));
                    $col++;

                    $entregaMateriales = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$entregaMateriales = _removeEnterYTabs(trim(utf8_encode(utf8_decode($entregaMateriales)),'?'));
                    $col++;

                    $numHilosPuertoOLT = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$numHilosPuertoOLT = _removeEnterYTabs(trim(utf8_encode(utf8_decode($numHilosPuertoOLT)),'?'));
                    $col++;

                    $FechaJumpeoCentral = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$FechaJumpeoCentral = _removeEnterYTabs(trim(utf8_encode(utf8_decode($FechaJumpeoCentral)),'?'));
                    $col++;

                    $estadoPin = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoPin = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoPin)),'?'));
                    $col++;

                    $fechaCensado = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaCensado = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaCensado)),'?'));
                    $col++;

                    $UIPHorizontalCenso = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$UIPHorizontalCenso = _removeEnterYTabs(trim(utf8_encode(utf8_decode($UIPHorizontalCenso)),'?'));
                    $col++;

                    $estadoCenso = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoCenso = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoCenso)),'?'));
                    $col++;

                    $bandejaODF = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$bandejaODF = _removeEnterYTabs(trim(utf8_encode(utf8_decode($bandejaODF)),'?'));
                    $col++;

                    $fechaInstalacionODF = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaInstalacionODF = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaInstalacionODF)),'?'));
                    $col++;

                    $fechaInicioConstruccion = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaInicioConstruccion = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaInicioConstruccion)),'?'));
                    $col++;

                    $fechaProyectadaEntrega = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaProyectadaEntrega = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaProyectadaEntrega)),'?'));
                    $col++;

                    $fechaFinalEntregaDivicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaFinalEntregaDivicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaFinalEntregaDivicau)),'?'));
                    $col++;

                    $estadoDespliegue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoDespliegue = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoDespliegue)),'?'));
                    $col++;

                    $fechaPruebaHGU = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$fechaPruebaHGU = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaPruebaHGU)),'?'));
                    $col++;

                    $comodinAvanceHGU = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$comodinAvanceHGU = _removeEnterYTabs(trim(utf8_encode(utf8_decode($comodinAvanceHGU)),'?'));
                    $col++;

                    $estadoHGU = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoHGU = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoHGU)),'?'));
                    $col++;

                    $estadoFinal = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoFinal = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoFinal)),'?'));
                    $col++;

                    $estadoGlobal = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$estadoGlobal = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoGlobal)),'?'));
                    $col++;
                    $col++;
                    $eecc 	= '';


                    $dataArray['observacion'] = '';
                    if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                    {
                    	$dataArray['observacion'] .= 'El campo itemplan es requerido<br>';
                    }

                    if (strlen($divicau) == 0 || $divicau == null || $divicau == '')
                    {
                    	$dataArray['observacion'] .= 'El campo divicau es requerido<br>';
                    }

                    $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanDivicau(trim($itemplan), trim($divicau));
                    if (count($dataArrayDetalle)> 0)
                    {
                    	$dataArray['observacion'] .= 'Los Valores Item y divicau ya existen<br>';
                    }

                    $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanInPlanObra(trim($itemplan));
                    if (count($dataArrayDetalle) == 0)
                    {
                    	$dataArray['observacion'] .= 'El Item plan ingresado no existe en plan de obras<br>';
                    }

                    $arrayParticleCode[]                = $itemplan.''.$divicau;
                    $dataArray['itemplanDivicau']          		= $itemplan.''.$divicau;
					$dataArray['anio'] 							= $anio;
					$dataArray['divicau'] 						= $divicau;
					$dataArray['plan'] 							= $plan;
					$dataArray['itemplan'] 						= $itemplan;
					$dataArray['nodo'] 							= $nodo;
					$dataArray['empresaColabDesc'] 				= $empresaColabDesc;
					$dataArray['modelo'] 						= $modelo;
					$dataArray['cable'] 						= $cable;
					$dataArray['tipo'] 							= $tipo;
					$dataArray['troba']	 						= $troba;
					$dataArray['UIPHorizontalDiseno'] 			= $UIPHorizontalDiseno;
					$dataArray['comodinPenetracion'] 			= $comodinPenetracion;
					$dataArray['distrito'] 						= $distrito;
					$dataArray['provincia'] 					= $provincia;
					$dataArray['departamento'] 					= $departamento;
					$dataArray['fechaAdjudicaDiseno'] 			= $fechaAdjudicaDiseno;
					$dataArray['fechaCierreDisenoExpediente'] 	= $fechaCierreDisenoExpediente;
					$dataArray['fechaCierreOSP'] 				= $fechaCierreOSP;
					$dataArray['fechaEntregaDiseno'] 			= $fechaEntregaDiseno;
					$dataArray['estadoDiseno'] 					= $estadoDiseno;
					$dataArray['pptoAprobado'] 					= $pptoAprobado;
					$dataArray['pep'] 							= $pep;
					$dataArray['ocConstruccionH'] 				= $ocConstruccionH;
					$dataArray['generacionVR'] 					= $generacionVR;
					$dataArray['estadoOC'] 						= $estadoOC;
					$dataArray['estadoCertificaOC'] 			= $estadoCertificaOC;
					$dataArray['fechaPresentaLicencia'] 		= $fechaPresentaLicencia;
					$dataArray['fechaInicioLicencia'] 			= $fechaInicioLicencia;
					$dataArray['estadoLicencia'] 				= $estadoLicencia;
					$dataArray['entregaMateriales'] 			= $entregaMateriales;
					$dataArray['numHilosPuertoOLT'] 			= $numHilosPuertoOLT;
					$dataArray['FechaJumpeoCentral'] 			= $FechaJumpeoCentral;
					$dataArray['estadoPin'] 					= $estadoPin;
					$dataArray['fechaCensado'] 					= $fechaCensado;
					$dataArray['UIPHorizontalCenso'] 			= $UIPHorizontalCenso;
					$dataArray['estadoCenso'] 					= $estadoCenso;
					$dataArray['bandejaODF'] 					= $bandejaODF;
					$dataArray['fechaInstalacionODF'] 			= $fechaInstalacionODF;
					$dataArray['fechaInicioConstruccion'] 		= $fechaInicioConstruccion;
					$dataArray['fechaProyectadaEntrega'] 		= $fechaProyectadaEntrega;
					$dataArray['fechaFinalEntregaDivicau'] 		= $fechaFinalEntregaDivicau;
					$dataArray['estadoDespliegue'] 				= $estadoDespliegue;
					$dataArray['fechaPruebaHGU'] 				= $fechaPruebaHGU;
					$dataArray['comodinAvanceHGU'] 				= $comodinAvanceHGU;
					$dataArray['estadoHGU'] 					= $estadoHGU;
					$dataArray['estadoFinal'] 					= $estadoFinal;
					$dataArray['estadoGlobal'] 					= $estadoGlobal;
                    $arrayTabla []= $dataArray;

                }
            }
        }

			list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistro($arrayTabla);
            $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
			$data['tbObservacion'] = $html;
			$data['jsonDataFile'] = json_encode($arrayFinal);

			$data['msj']  = 'Se procesó correctamente el archivo!!';
			$data['error']  = EXIT_SUCCESS;

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function procesarFileMatrizSeguimientoMin()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuarioSession = $this->session->userdata('idPersonaSessionPan');

            if (!isset($idUsuarioSession)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if(count($_FILES) == 0){
                throw new Exception('Debe seleccionar un archivo para procesar data!!');
            }

            $nombreArchivo = $_FILES['file']['name'];
            $tipoArchivo = $_FILES['file']['type'];
            $nombreFicheroTemp = $_FILES['file']['tmp_name'];
            $tamano_archivo = $_FILES['file']['size'];

            $arryNombreArchivo = explode(".", $nombreArchivo);

            $arrayTipos = array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            );

            if (!in_array($tipoArchivo, $arrayTipos)) {
                throw new Exception('Sólo puede subir archivos de tipo excel (.xls , .xlsx)!!');
            }

            if (!file_exists("./uploads/aten_masivo_sol_coti_b2b_pan")) {
                if (!mkdir("./uploads/aten_masivo_sol_coti_b2b_pan")) {
                    throw new Exception('Hubo un error al crear la carpeta aten_masivo_sol_oc_creacion_pan!!');
                }
            }

            








            $html = '
                <table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
                            <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>                           
                            <th style="text-align: center; vertical-align: middle;">AÑO</th>                           
                            <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                            <th style="text-align: center; vertical-align: middle;">PLAN</th>
                            <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                            <th style="text-align: center; vertical-align: middle;">MODELO</th>
                            <th style="text-align: center; vertical-align: middle;">CABLE</th>
                            <th style="text-align: center; vertical-align: middle;">TROBA</th>
                            <th style="text-align: center; vertical-align: middle;">% PENETRACION</th>
                        </tr>
                    </thead>

                    <tbody>';

        $count = 1;
        $arrayFinal             = array();
        $arrayTabla             = array();
        $dataArray              = array();
        $arrayOC                = array();
        $ctnVal = 0;
        $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
        if ($objectExcel != '')
        {
            $col = 1;
            foreach ($objectExcel->getWorksheetIterator() as $worksheet)
            {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();

                for ($row = 2; $row <= $highestRow; $row++)
                {
                    $col = 0;
                    // $n_orden = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    // $n_orden = _removeEnterYTabs(trim(utf8_encode(utf8_decode($n_orden)),'?'));
                    // $col++;

                    $anio = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $anio = _removeEnterYTabs(trim(utf8_encode(utf8_decode($anio)),'?'));
                    $col++;
                    $divicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $divicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($divicau)),'?'));
                    $col++;
                    $plan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $plan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($plan)),'?'));
                    $col++;
                    $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                    $col++; 
                                
                    $modelo = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $modelo = _removeEnterYTabs(trim(utf8_encode(utf8_decode($modelo)),'?'));
                    $col++;
                    $cable = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $cable = _removeEnterYTabs(trim(utf8_encode(utf8_decode($cable)),'?'));
                    $col++;

                    $troba = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $troba = _removeEnterYTabs(trim(utf8_encode(utf8_decode($troba)),'?'));
                    $col++;

                    $comodinPenetracion = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $comodinPenetracion = _removeEnterYTabs(trim(utf8_encode(utf8_decode($comodinPenetracion)),'?'));
                    $col++;
                    $col++;
                    $eecc   = '';


                    $dataArray['observacion'] = '';
                    if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                    {
                        $dataArray['observacion'] .= 'El campo itemplan es requerido<br>';
                    }

                    if (strlen($divicau) == 0 || $divicau == null || $divicau == '')
                    {
                        $dataArray['observacion'] .= 'El campo divicau es requerido<br>';
                    }

                    $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanDivicau(trim($itemplan), trim($divicau));
                    if (count($dataArrayDetalle)> 0)
                    {
                        $dataArray['observacion'] .= 'Los Valores Item y divicau ya existen<br>';
                    }

                    $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanInPlanObra(trim($itemplan));
                    if (count($dataArrayDetalle) == 0)
                    {
                        $dataArray['observacion'] .= 'El Item plan ingresado no existe en plan de obras<br>';
                    }

                    $arrayParticleCode[]                = $itemplan.''.$divicau;
                    $dataArray['itemplanDivicau']               = $itemplan.''.$divicau;
                    $dataArray['anio']                          = $anio;
                    $dataArray['divicau']                       = $divicau;
                    $dataArray['plan']                          = $plan;
                    $dataArray['itemplan']                      = $itemplan;
                    $dataArray['modelo']                        = $modelo;
                    $dataArray['cable']                         = $cable;
                    $dataArray['troba']                         = $troba;
                    $dataArray['comodinPenetracion']            = $comodinPenetracion;
                    
                    $arrayTabla []= $dataArray;

                }
            }
        }

            list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistro2($arrayTabla);
            $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
            $data['tbObservacion'] = $html;
            $data['jsonDataFile'] = json_encode($arrayFinal);

            $data['msj']  = 'Se procesó correctamente el archivo!!';
            $data['error']  = EXIT_SUCCESS;

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }



    function tablaRegistro($arrayTabla) {
        $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
							<th style="text-align: center; vertical-align: middle;">OBSERVACION</th>                           
							<th style="text-align: center; vertical-align: middle;">AÑO</th>                           
                            <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                            <th style="text-align: center; vertical-align: middle;">PLAN</th>
							<th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                            <th style="text-align: center; vertical-align: middle;">NODO</th>
  							<th style="text-align: center; vertical-align: middle;">EMPRESA COLABORADORA</th>
                            <th style="text-align: center; vertical-align: middle;">MODELO</th>
                            <th style="text-align: center; vertical-align: middle;">CABLE</th>
                            <th style="text-align: center; vertical-align: middle;">TIPO</th>
                            <th style="text-align: center; vertical-align: middle;">TROBA</th>
                            <th style="text-align: center; vertical-align: middle;">UIP HORIZONTAL DISEÑO</th>
                            <th style="text-align: center; vertical-align: middle;">% PENETRACION</th>
                            <th style="text-align: center; vertical-align: middle;">DISTRITO</th>
                            <th style="text-align: center; vertical-align: middle;">PROVINCIA</th>
                            <th style="text-align: center; vertical-align: middle;">DEPARTAMENTO</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA ADJUDICACION DISEÑO</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA CIERRE DISEÑO EXPEDIENTE</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA CIERRE OSP</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA ENTREGA</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS DISEÑO</th>
                            <th style="text-align: center; vertical-align: middle;">PPTO APROBADO</th>
                            <th style="text-align: center; vertical-align: middle;">PEP</th>
                            <th style="text-align: center; vertical-align: middle;">OC CONSTRUCCION HORIZONTAL</th>
                            <th style="text-align: center; vertical-align: middle;">GENERACION VR</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS OC</th>
                            <th style="text-align: center; vertical-align: middle;">CERTIFICACION</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA PRESENTACION DE LICENCIA</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA INICIO LICENCIA</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS LICENCIA</th>
                            <th style="text-align: center; vertical-align: middle;">ENTREGA MATERIALES</th>
                            <th style="text-align: center; vertical-align: middle;">CANTIDAD DE HILOS PUERTOS OLT</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA JUMPLEO CENTRAL</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS PIN</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA DE CENSADO</th>
                            <th style="text-align: center; vertical-align: middle;">UIP HORIZONTAL CENSO</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS CENSO</th>
                            <th style="text-align: center; vertical-align: middle;">ODF BANDEJA</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA INSTALACION ODF</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA INICIO CONSTRUCCION</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA ENTREGA PROYECTADA</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA ENTREGA FINAL</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS DESPLIEGUE</th>
                            <th style="text-align: center; vertical-align: middle;">FECHA DE PRUEBA HGU</th>
                            <th style="text-align: center; vertical-align: middle;">PORCENTAJE DE AVANCE</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS HGU</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS FINAL</th>
                            <th style="text-align: center; vertical-align: middle;">STATUS GLOBAL</th>
                        </tr>
                    </thead>
                    <tbody>';
        $count = 0;
        $ctnVal = 0;
        $arrayFinal = array();
        $style = '';
        $htmlColorFila = '';
        $arrayParticleCode      = array();
        $arrayParticleCodeSumi  = array();

        foreach ($arrayTabla as $row) {
            
            $style = '';
            if ($row['observacion'] != '') {
                $htmlColorFila = 'style="background:#FDBDBD"';
                $btnDelete = '<a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Eliminar" 
                                aria-expanded="false" data-itemplanDivicau="'.$row['itemplanDivicau'].'"
                                onclick="deleteItemErroneo(this);"><i class="fal fa-trash"></i>
                            </a>';
            } else {
                    if(!in_array($row['itemplanDivicau'],$arrayParticleCode)){

                        $arrayParticleCode[] = $row['itemplan'].''.$row['divicau'];
                        $htmlColorFila = '';
                        $btnDelete = '';
                        $arrayFinal[] = array(
                            'itemplanDivicau' 				=> $row['itemplan'].''.$row['divicau'],
                            'observacion' 					=> $row['observacion'],
							'anio' 							=> $row['anio'],
							'divicau' 						=> $row['divicau'],
							'plan' 							=> $row['plan'],
							'itemplan' 						=> $row['itemplan'],
							'nodo' 							=> $row['nodo'],
							'empresaColabDesc' 				=> $row['empresaColabDesc'],
							'modelo' 						=> $row['modelo'],
							'cable' 						=> $row['cable'],
							'tipo' 							=> $row['tipo'],
							'troba' 						=> $row['troba'],
							'UIPHorizontalDiseno' 			=> $row['UIPHorizontalDiseno'],
							'comodinPenetracion' 			=> $row['comodinPenetracion'],
							'distrito' 						=> $row['distrito'],
							'provincia' 					=> $row['provincia'],
							'departamento' 					=> $row['departamento'],
							'fechaAdjudicaDiseno' 			=> $row['fechaAdjudicaDiseno'],
							'fechaCierreDisenoExpediente' 	=> $row['fechaCierreDisenoExpediente'],
							'fechaCierreOSP' 				=> $row['fechaCierreOSP'],
							'fechaEntregaDiseno' 			=> $row['fechaEntregaDiseno'],
							'estadoDiseno' 					=> $row['estadoDiseno'],
							'pptoAprobado' 					=> $row['pptoAprobado'],
							'pep' 							=> $row['pep'],
							'ocConstruccionH' 				=> $row['ocConstruccionH'],
							'generacionVR' 					=> $row['generacionVR'],
							'estadoOC' 						=> $row['estadoOC'],
							'estadoCertificaOC' 			=> $row['estadoCertificaOC'],
							'fechaPresentaLicencia' 		=> $row['fechaPresentaLicencia'],
							'fechaInicioLicencia' 			=> $row['fechaInicioLicencia'],
							'estadoLicencia' 				=> $row['estadoLicencia'],
							'entregaMateriales' 			=> $row['entregaMateriales'],
							'numHilosPuertoOLT' 			=> $row['numHilosPuertoOLT'],
							'FechaJumpeoCentral' 			=> $row['FechaJumpeoCentral'],
							'estadoPin' 					=> $row['estadoPin'],
							'fechaCensado' 					=> $row['fechaCensado'],
							'UIPHorizontalCenso' 			=> $row['UIPHorizontalCenso'],
							'estadoCenso' 					=> $row['estadoCenso'],
							'bandejaODF' 					=> $row['bandejaODF'],
							'fechaInstalacionODF' 			=> $row['fechaInstalacionODF'],
							'fechaInicioConstruccion' 		=> $row['fechaInicioConstruccion'],
							'fechaProyectadaEntrega' 		=> $row['fechaProyectadaEntrega'],
							'fechaFinalEntregaDivicau' 		=> $row['fechaFinalEntregaDivicau'],
							'estadoDespliegue' 				=> $row['estadoDespliegue'],
							'fechaPruebaHGU' 				=> $row['fechaPruebaHGU'],
							'comodinAvanceHGU' 				=> $row['comodinAvanceHGU'],
							'estadoHGU' 					=> $row['estadoHGU'],
							'estadoFinal' 					=> $row['estadoFinal'],
							'estadoGlobal' 					=> $row['estadoGlobal']
                        );
                        $ctnVal++;
                    }
                    
               
                

                
            }


            $html .= ' <tr ' . $htmlColorFila . '>
                            <td>
                                <div class="d-flex demo">
                                    '.$btnDelete.'
                                </div>
                            </td>
                            <td>'.$row['observacion'].'</td>
							<td>'.$row['anio'].'</td>
							<td>'.$row['divicau'].'</td>
							<td>'.$row['plan'].'</td>
							<td>'.$row['itemplan'].'</td>
							<td>'.$row['nodo'].'</td>
							<td>'.$row['empresaColabDesc'].'</td>
							<td>'.$row['modelo'].'</td>
							<td>'.$row['cable'].'</td>
							<td>'.$row['tipo'].'</td>
							<td>'.$row['troba'].'</td>
							<td>'.$row['UIPHorizontalDiseno'].'</td>
							<td>'.$row['comodinPenetracion'].'</td>
							<td>'.$row['distrito'].'</td>
							<td>'.$row['provincia'].'</td>
							<td>'.$row['departamento'].'</td>
							<td>'.$row['fechaAdjudicaDiseno'].'</td>
							<td>'.$row['fechaCierreDisenoExpediente'].'</td>
							<td>'.$row['fechaCierreOSP'].'</td>
							<td>'.$row['fechaEntregaDiseno'].'</td>
							<td>'.$row['estadoDiseno'].'</td>
							<td>'.$row['pptoAprobado'].'</td>
							<td>'.$row['pep'].'</td>
							<td>'.$row['ocConstruccionH'].'</td>
							<td>'.$row['generacionVR'].'</td>
							<td>'.$row['estadoOC'].'</td>
							<td>'.$row['estadoCertificaOC'].'</td>
							<td>'.$row['fechaPresentaLicencia'].'</td>
							<td>'.$row['fechaInicioLicencia'].'</td>
							<td>'.$row['estadoLicencia'].'</td>
							<td>'.$row['entregaMateriales'].'</td>
							<td>'.$row['numHilosPuertoOLT'].'</td>
							<td>'.$row['FechaJumpeoCentral'].'</td>
							<td>'.$row['estadoPin'].'</td>
							<td>'.$row['fechaCensado'].'</td>
							<td>'.$row['UIPHorizontalCenso'].'</td>
							<td>'.$row['estadoCenso'].'</td>
							<td>'.$row['bandejaODF'].'</td>
							<td>'.$row['fechaInstalacionODF'].'</td>
							<td>'.$row['fechaInicioConstruccion'].'</td>
							<td>'.$row['fechaProyectadaEntrega'].'</td>
							<td>'.$row['fechaFinalEntregaDivicau'].'</td>
							<td>'.$row['estadoDespliegue'].'</td>
							<td>'.$row['fechaPruebaHGU'].'</td>
							<td>'.$row['comodinAvanceHGU'].'</td>
							<td>'.$row['estadoHGU'].'</td>
							<td>'.$row['estadoFinal'].'</td>
							<td>'.$row['estadoGlobal'].'</td>
                        </tr>';
            $count++;
        }
        $html .= '</tbody>
            </table>';

        return array($html, $ctnVal, $arrayFinal, $count);
    }



    function tablaRegistro2($arrayTabla) {
        $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
                            <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>                           
                            <th style="text-align: center; vertical-align: middle;">AÑO</th>                           
                            <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                            <th style="text-align: center; vertical-align: middle;">PLAN</th>
                            <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                            <th style="text-align: center; vertical-align: middle;">MODELO</th>
                            <th style="text-align: center; vertical-align: middle;">CABLE</th>
                            <th style="text-align: center; vertical-align: middle;">TROBA</th>
                            <th style="text-align: center; vertical-align: middle;">% PENETRACION</th>
                        </tr>
                    </thead>
                    <tbody>';
        $count = 0;
        $ctnVal = 0;
        $arrayFinal = array();
        $style = '';
        $htmlColorFila = '';
        $arrayParticleCode      = array();
        $arrayParticleCodeSumi  = array();

        foreach ($arrayTabla as $row) {
            
            $style = '';
            if ($row['observacion'] != '') {
                $htmlColorFila = 'style="background:#FDBDBD"';
                $btnDelete = '<a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Eliminar" 
                                aria-expanded="false" data-itemplanDivicau="'.$row['itemplanDivicau'].'"
                                onclick="deleteItemErroneo(this);"><i class="fal fa-trash"></i>
                            </a>';
            } else {
                    if(!in_array($row['itemplanDivicau'],$arrayParticleCode)){

                        $arrayParticleCode[] = $row['itemplan'].''.$row['divicau'];
                        $htmlColorFila = '';
                        $btnDelete = '';
                        $arrayFinal[] = array(
                            'itemplanDivicau'               => $row['itemplan'].''.$row['divicau'],
                            'observacion'                   => $row['observacion'],
                            'anio'                          => $row['anio'],
                            'divicau'                       => $row['divicau'],
                            'plan'                          => $row['plan'],
                            'itemplan'                      => $row['itemplan'],
                            'modelo'                        => $row['modelo'],
                            'cable'                         => $row['cable'],
                            'troba'                         => $row['troba'],
                            'comodinPenetracion'            => $row['comodinPenetracion']
                        );
                        $ctnVal++;
                    }
                    
            }

            $html .= ' <tr ' . $htmlColorFila . '>
                            <td>
                                <div class="d-flex demo">
                                    '.$btnDelete.'
                                </div>
                            </td>
                            <td>'.$row['observacion'].'</td>
                            <td>'.$row['anio'].'</td>
                            <td>'.$row['divicau'].'</td>
                            <td>'.$row['plan'].'</td>
                            <td>'.$row['itemplan'].'</td>
                            <td>'.$row['modelo'].'</td>
                            <td>'.$row['cable'].'</td>
                            <td>'.$row['troba'].'</td>
                            <td>'.$row['comodinPenetracion'].'</td>
                        </tr>';
            $count++;
        }
        $html .= '</tbody>
            </table>';

        return array($html, $ctnVal, $arrayFinal, $count);
    }




    public function cargarMasivoMatrizSeguimiento()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$idUsuario = $this->session->userdata('idPersonaSessionPan');
			$arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

			if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
			if($arrayDataFile == null || count($arrayDataFile) == 0){
				throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
			} 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud      	= array();               
                $arrayTabla       			= array();

				foreach ($arrayDataFile as $datos)
				{
					// $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
					$anio    			= @$datos['anio'];              
                    $divicau       		= @$datos['divicau'];
                    $plan          		= @$datos['plan'];
                    $itemplan           = @$datos['itemplan'];    						
             
                    
                    $dataArray = array();
                    $dataArray['anio'] 							= @$anio;
					$dataArray['divicau'] 						= @$divicau;
					$dataArray['plan'] 							= @$plan;
					$dataArray['itemplan'] 						= @$itemplan;
					// $dataArray['nodo'] 							= @$nodo;
					// $dataArray['empresaColabDesc'] 				= @$empresaColabDesc;
					// $dataArray['modelo'] 						= @$modelo;
					// $dataArray['cable'] 						= @$cable;
					// $dataArray['tipo'] 							= @$tipo;
					// $dataArray['troba']	 						= @$troba;
					// $dataArray['UIPHorizontalDiseno'] 			= @$UIPHorizontalDiseno;
					// $dataArray['comodinPenetracion'] 			= @$comodinPenetracion;
					// $dataArray['distrito'] 						= @$distrito;
					// $dataArray['provincia'] 					= @$provincia;
					// $dataArray['departamento'] 					= @$departamento;
					// $dataArray['fechaAdjudicaDiseno'] 			= @$fechaAdjudicaDiseno;
					// $dataArray['fechaCierreDisenoExpediente'] 	= @$fechaCierreDisenoExpediente;
					// $dataArray['fechaCierreOSP'] 				= @$fechaCierreOSP;
					// $dataArray['fechaEntregaDiseno'] 			= @$fechaEntregaDiseno;
					// $dataArray['estadoDiseno'] 					= @$estadoDiseno;
					// $dataArray['pptoAprobado'] 					= @$pptoAprobado;
					// $dataArray['pep'] 							= @$pep;
					// $dataArray['ocConstruccionH'] 				= @$ocConstruccionH;
					// $dataArray['generacionVR'] 					= @$generacionVR;
					// $dataArray['estadoOC'] 						= @$estadoOC;
					// $dataArray['estadoCertificaOC'] 			= @$estadoCertificaOC;
					// $dataArray['fechaPresentaLicencia'] 		= @$fechaPresentaLicencia;
					// $dataArray['fechaInicioLicencia'] 			= @$fechaInicioLicencia;
					// $dataArray['estadoLicencia'] 				= @$estadoLicencia;
					// $dataArray['entregaMateriales'] 			= @$entregaMateriales;
					// $dataArray['numHilosPuertoOLT'] 			= @$numHilosPuertoOLT;
					// $dataArray['FechaJumpeoCentral'] 			= @$FechaJumpeoCentral;
					// $dataArray['estadoPin'] 					= @$estadoPin;
					// $dataArray['fechaCensado'] 					= @$fechaCensado;
					// $dataArray['UIPHorizontalCenso'] 			= @$UIPHorizontalCenso;
					// $dataArray['estadoCenso'] 					= @$estadoCenso;
					// $dataArray['bandejaODF'] 					= @$bandejaODF;
					// $dataArray['fechaInstalacionODF'] 			= @$fechaInstalacionODF;
					// $dataArray['fechaInicioConstruccion'] 		= @$fechaInicioConstruccion;
					// $dataArray['fechaProyectadaEntrega'] 		= @$fechaProyectadaEntrega;
					// $dataArray['fechaFinalEntregaDivicau'] 		= @$fechaFinalEntregaDivicau;
					// $dataArray['estadoDespliegue'] 				= @$estadoDespliegue;
					// $dataArray['fechaPruebaHGU'] 				= @$fechaPruebaHGU;
					// $dataArray['comodinAvanceHGU'] 				= @$comodinAvanceHGU;
					// $dataArray['estadoHGU'] 					= @$estadoHGU;
					// $dataArray['estadoFinal'] 					= @$estadoFinal;
					// $dataArray['estadoGlobal'] 					= @$estadoGlobal;
                	
                	$data = $this->m_matriz_seguimiento->insertTabla($dataArray);
                    // $arrayTabla []= $dataArray;


				}

                // $data['error'] = 0;

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    public function cargarMasivoMatrizSeguimientoMin()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    $anio                           = @$datos['anio'];              
                    $divicau                        = @$datos['divicau'];
                    $plan                           = @$datos['plan'];
                    $itemplan                       = @$datos['itemplan'];                        
                    $modelo                         = @$datos['modelo'];                        
                    $cable                          = @$datos['cable'];                        
                    $troba                          = @$datos['troba'];                        
                    $comodinPenetracion             = @$datos['comodinPenetracion'];                        
             
                    
                    $dataArray = array();
                    $dataArray['anio']                              = trim($anio);
                    $dataArray['divicau']                           = trim($divicau);
                    $dataArray['plan']                              = trim($plan);
                    $dataArray['itemplan']                          = trim($itemplan);
                    $dataArray['modelo']                            = trim($modelo);
                    $dataArray['cable']                             = trim($cable);
                    $dataArray['troba']                             = trim($troba);
                    $dataArray['comodinPenetracion']                = trim($comodinPenetracion);
                    
                    $data = $this->m_matriz_seguimiento->insertTabla($dataArray);
                    // $arrayTabla []= $dataArray;


                }

                // $data['error'] = 0;

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


    public function saveUploadFileMatriz()
    {
        $data['error']  = EXIT_ERROR;
        $data['msj']    = null;

        try
        {
            $idUsuarioSession = $this->session->userdata('idPersonaSessionPan');

            if (!isset($idUsuarioSession)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if(count($_FILES) == 0){
                throw new Exception('Debe seleccionar un archivo para procesar data!!');
            }

            $nombreArchivo      = $_FILES['file']['name'];
            $tipoArchivo        = $_FILES['file']['type'];
            $nombreFicheroTemp  = $_FILES['file']['tmp_name'];
            $tamano_archivo     = $_FILES['file']['size'];

            $arryNombreArchivo = explode(".", $nombreArchivo);

            $arrayTipos = array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            );

            if (!in_array($tipoArchivo, $arrayTipos)) {
                throw new Exception('Sólo puede subir archivos de tipo excel (.xls , .xlsx)!!');
            }

            if (!file_exists("./uploads/aten_masivo_sol_coti_b2b_pan")) {
                if (!mkdir("./uploads/aten_masivo_sol_coti_b2b_pan")) {
                    throw new Exception('Hubo un error al crear la carpeta aten_masivo_sol_oc_creacion_pan!!');
                }
            }

            $data['error']              = EXIT_SUCCESS;
            $data['nombreFicheroTemp']  = $nombreFicheroTemp;
            
        } catch (Exception $e)
        {
            $data['msj'] = $e->getMessage();
        }

        return $data;

    }

    public function tablaRegistroMatriz($arrayTabla, $modulo)
    {
        $html = '';
        switch ($modulo)
        {
            case 'DISENO':
                $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">#</th>
                                    <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>                           
                                    <th style="text-align: center; vertical-align: middle;">AÑO</th>                           
                                    <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                                    <th style="text-align: center; vertical-align: middle;">PLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">MODELO</th>
                                    <th style="text-align: center; vertical-align: middle;">CABLE</th>
                                    <th style="text-align: center; vertical-align: middle;">NODO</th>
                                    <th style="text-align: center; vertical-align: middle;">TIPO DIVICAU</th>
                                    <th style="text-align: center; vertical-align: middle;">NOMBRE TROBA</th>
                                    <th style="text-align: center; vertical-align: middle;">ESTADO DISEÑO</th>
                                    <th style="text-align: center; vertical-align: middle;">UIP DISENO</th>
                                    <th style="text-align: center; vertical-align: middle;">DEPARTAMENTO</th>
                                    <th style="text-align: center; vertical-align: middle;">PROVINCIA</th>
                                    <th style="text-align: center; vertical-align: middle;">DISTRITO</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA TERMINO</th>
                                </tr>
                            </thead>
                            <tbody>';
            break;

            case 'ECONOMICO':
                $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">#</th>
                                    <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>
                                    <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">CONFIRMACION PPTO</th>
                                    <th style="text-align: center; vertical-align: middle;">N° PEP</th>
                                    <th style="text-align: center; vertical-align: middle;">N° OC</th>
                                    <th style="text-align: center; vertical-align: middle;">GENERACION VR</th>
                                    <th style="text-align: center; vertical-align: middle;">ESTADO OC</th>
                                    <th style="text-align: center; vertical-align: middle;">CERTIFICACION OC</th>
                                </tr>
                            </thead>
                            <tbody>';
            break;
            case 'LICENCIA':

                $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">#</th>
                                    <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>
                                    <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA PRESENTACION</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA DE INICIO</th>
                                    <th style="text-align: center; vertical-align: middle;">STATUS LICENCIA</th>
                                </tr>
                            </thead>
                            <tbody>';
            break;
            
            case 'LOGISTICA':

                $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">#</th>
                                    <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>
                                    <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">CONFIRMACION DE ENTREGA</th>
                                </tr>
                            </thead>
                            <tbody>';
            break;

            case 'CENSADO':
                $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">#</th>
                                    <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>
                                    <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA CENSADO</th>
                                    <th style="text-align: center; vertical-align: middle;">CANTIDAD UIP</th>
                                    <th style="text-align: center; vertical-align: middle;">ESTADO DE CENSO</th>
                                </tr>
                            </thead>
                            <tbody>';
            break;

            case 'DESPLIEGUE':
                $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">#</th>
                                    <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>
                                    <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                                    <th style="text-align: center; vertical-align: middle;">CONFIRMACION PART DESPLIEGUE</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA INSTALACION ODF</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA DE INICIO CONSTRUCCION</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA PROYECTADA ENTREGA</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA ENTREGA DIVICAU</th>
                                    <th style="text-align: center; vertical-align: middle;">STATUS DESPLIEGUE</th>
                                </tr>
                            </thead>
                            <tbody>';
            break;

            case 'HGU':
                $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">#</th>
                                    <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>
                                    <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                                    <th style="text-align: center; vertical-align: middle;">FECHA DE PRUEBA DE HGU</th>
                                    <th style="text-align: center; vertical-align: middle;">AVANCE DE PRUEBAS HGU</th>
                                    <th style="text-align: center; vertical-align: middle;">CONFIRMACION DE EJECUCION DE PRUEBAS HGU</th>
                                </tr>
                            </thead>
                            <tbody>';
            break;
            
            case 'STATUS':
                $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">#</th>
                                    <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>
                                    <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                                    <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                                    <th style="text-align: center; vertical-align: middle;">STATUS DETALLADO</th>
                                    <th style="text-align: center; vertical-align: middle;">STATUS GLOBAL</th>
                                </tr>
                            </thead>
                            <tbody>';
            break;
            
            default:
                $html = '';
            break;
        }
        $count = 0;
        $ctnVal = 0;
        $arrayFinal = array();
        $style = '';
        $htmlColorFila = '';
        $arrayParticleCode      = array();
        $arrayParticleCodeSumi  = array();

        foreach ($arrayTabla as $row) {
            
            $style = '';
            if ($row['observacion'] != '') {
                $htmlColorFila = 'style="background:#FDBDBD"';
                $btnDelete = '<a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Eliminar" 
                                aria-expanded="false" data-itemplanDivicau="'.$row['itemplanDivicau'].'"
                                onclick="deleteItemErroneo(this);"><i class="fal fa-trash"></i>
                            </a>';
            } else {
                    if(!in_array($row['itemplanDivicau'],$arrayParticleCode)){

                        $arrayParticleCode[] = $row['itemplan'].''.@$row['divicau'];
                        $htmlColorFila = '';
                        $btnDelete = '';

                        switch ($modulo)
                        {
                            case 'DISENO':
                                $arrayFinal[] = array(
                                    'itemplanDivicau'               => $row['itemplan'].''.@$row['divicau'],
                                    'observacion'                   => $row['observacion'],
                                    'anio'                          => $row['anio'],
                                    'divicau'                       => $row['divicau'],
                                    'plan'                          => $row['plan'],
                                    'itemplan'                      => $row['itemplan'],
                                    'modelo'                        => $row['modelo'],
                                    'cable'                         => $row['cable'],
                                    'nodo'                         => $row['nodo'],
                                    'tipoDivicau'                   => $row['tipoDivicau'],
                                    'troba'                         => $row['troba'],
                                    'estadoDiseno'                  => $row['estadoDiseno'],
                                    'uipDiseno'                     => $row['uipDiseno'],
                                    'departamento'                  => $row['departamento'],
                                    'provincia'                     => $row['provincia'],
                                    'distrito'                      => $row['distrito'],
                                    'fechaTermino'                  => $row['fechaTermino']
                                );
                            break;

                            case 'ECONOMICO':
                                $arrayFinal[] = array(
                                    'itemplanDivicau'               => $row['itemplan'],
                                    'itemplan'                      => $row['itemplan'],
                                    'observacion'                   => $row['observacion'],
                                    'confirmacionPPTO'              => $row['confirmacionPPTO'],
                                    'estadoOC'                      => $row['estadoOC'],
                                    'n_pep'                         => $row['n_pep'],
                                    'n_oc'                          => $row['n_oc'],
                                    'generacion_vr'                 => $row['generacion_vr'],
                                    'certificacion_oc'              => $row['certificacion_oc'],
                                );
                            break;

                            case 'LICENCIA':

                                $arrayFinal[] = array(
                                    'itemplanDivicau'               => $row['itemplan'],
                                    'itemplan'                      => $row['itemplan'],
                                    'observacion'                   => $row['observacion'],
                                    'fechaPresentaLicencia'         => $row['fechaPresentaLicencia'],
                                    'fechaInicioLicencia'           => $row['fechaInicioLicencia'],
                                    'estadoLicencia'                => $row['estadoLicencia'],
                                );
                            break;

                            case 'LOGISTICA':

                                $arrayFinal[] = array(
                                    'itemplanDivicau'               => $row['itemplan'],
                                    'itemplan'                      => $row['itemplan'],
                                    'observacion'                   => $row['observacion'],
                                    'confirmacionEntrega'         => $row['confirmacionEntrega'],
                                );
                            break;


                            case 'CENSADO':

                                $arrayFinal[] = array(
                                    'itemplanDivicau'               => $row['itemplan'],
                                    'itemplan'                      => $row['itemplan'],
                                    'observacion'                   => $row['observacion'],
                                    'divicau'                       => $row['divicau'],
                                    'fechaCensado'                  => $row['fechaCensado'],
                                    'cantidadUIP'                   => $row['cantidadUIP'],
                                    'estadoCenso'                   => $row['estadoCenso'],
                                );
                            break;

                            case 'DESPLIEGUE':

                                $arrayFinal[] = array(
                                    'itemplanDivicau'               => $row['itemplan'],
                                    'itemplan'                      => $row['itemplan'],
                                    'observacion'                   => $row['observacion'],
                                    'divicau'                       => $row['divicau'],
                                    'confirmacionDespliegue'        => $row['confirmacionDespliegue'],
                                    'fechaInstalacionODF'           => $row['fechaInstalacionODF'],
                                    'fechaInicioConstruccion'       => $row['fechaInicioConstruccion'],
                                    'fechaProyectadaEntrega'        => $row['fechaProyectadaEntrega'],
                                    'fechaFinalEntregaDivicau'      => $row['fechaFinalEntregaDivicau'],
                                    'estadoDespliegue'              => $row['estadoDespliegue'],
                                );
                            break;

                            case 'HGU':
                                $arrayFinal[] = array(
                                    'itemplanDivicau'               => $row['itemplan'],
                                    'itemplan'                      => $row['itemplan'],
                                    'observacion'                   => $row['observacion'],
                                    'divicau'                       => $row['divicau'],
                                    'fechaPruebaHGU'                => $row['fechaPruebaHGU'],
                                    'comodinAvanceHGU'              => $row['comodinAvanceHGU'],
                                    'estadoHGU'                     => $row['estadoHGU'],
                                );
                            break;

                            case 'STATUS':
                                $arrayFinal[] = array(
                                    'itemplanDivicau'               => $row['itemplan'],
                                    'itemplan'                      => $row['itemplan'],
                                    'observacion'                   => $row['observacion'],
                                    'divicau'                       => $row['divicau'],
                                    'estadoFinal'                   => $row['estadoFinal'],
                                    'estadoGlobal'                  => $row['estadoGlobal'],
                                );
                            break;

                            
                        }
                        $ctnVal++;
                    }
                    
            }


            switch ($modulo)
            {
                case 'DISENO':
                    $html .= ' <tr ' . $htmlColorFila . '>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnDelete.'
                                        </div>
                                    </td>
                                    <td>'.$row['observacion'].'</td>
                                    <td>'.$row['anio'].'</td>
                                    <td>'.$row['divicau'].'</td>
                                    <td>'.$row['plan'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['modelo'].'</td>
                                    <td>'.$row['cable'].'</td>
                                    <td>'.$row['nodo'].'</td>
                                    <td>'.$row['tipoDivicau'].'</td>
                                    <td>'.$row['troba'].'</td>
                                    <td>'.$row['estadoDiseno'].'</td>
                                    <td>'.$row['uipDiseno'].'</td>
                                    <td>'.$row['departamento'].'</td>
                                    <td>'.$row['provincia'].'</td>
                                    <td>'.$row['distrito'].'</td>
                                    <td>'.$row['fechaTermino'].'</td>
                                </tr>';
                break;

                case 'ECONOMICO':
                    $html .= ' <tr ' . $htmlColorFila . '>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnDelete.'
                                        </div>
                                    </td>
                                    <td>'.$row['observacion'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['confirmacionPPTO'].'</td>
                                    <td>'.$row['n_pep'].'</td>
                                    <td>'.$row['n_oc'].'</td>
                                    <td>'.$row['generacion_vr'].'</td>
                                    <td>'.$row['estadoOC'].'</td>
                                    <td>'.$row['certificacion_oc'].'</td>
                                </tr>';
                break;

                case 'LICENCIA':
                    $html .= ' <tr ' . $htmlColorFila . '>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnDelete.'
                                        </div>
                                    </td>
                                    <td>'.$row['observacion'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['fechaPresentaLicencia'].'</td>
                                    <td>'.$row['fechaInicioLicencia'].'</td>
                                    <td>'.$row['estadoLicencia'].'</td>
                                </tr>';
                break;

                case 'LOGISTICA':
                    $html .= ' <tr ' . $htmlColorFila . '>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnDelete.'
                                        </div>
                                    </td>
                                    <td>'.$row['observacion'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['confirmacionEntrega'].'</td>
                                </tr>';
                break;

                case 'CENSADO':

                    $html .= ' <tr ' . $htmlColorFila . '>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnDelete.'
                                        </div>
                                    </td>
                                    <td>'.$row['observacion'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['divicau'].'</td>
                                    <td>'.$row['fechaCensado'].'</td>
                                    <td>'.$row['cantidadUIP'].'</td>
                                    <td>'.$row['estadoCenso'].'</td>
                                </tr>';
                break;

                case 'DESPLIEGUE':

                    $html .= ' <tr ' . $htmlColorFila . '>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnDelete.'
                                        </div>
                                    </td>
                                    <td>'.$row['observacion'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['divicau'].'</td>
                                    <td>'.$row['confirmacionDespliegue'].'</td>
                                    <td>'.$row['fechaInstalacionODF'].'</td>
                                    <td>'.$row['fechaInicioConstruccion'].'</td>
                                    <td>'.$row['fechaProyectadaEntrega'].'</td>
                                    <td>'.$row['fechaFinalEntregaDivicau'].'</td>
                                    <td>'.$row['estadoDespliegue'].'</td>
                                </tr>';
                break;

                case 'HGU':

                    $html .= ' <tr ' . $htmlColorFila . '>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnDelete.'
                                        </div>
                                    </td>
                                    <td>'.$row['observacion'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['divicau'].'</td>
                                    <td>'.$row['fechaPruebaHGU'].'</td>
                                    <td>'.$row['comodinAvanceHGU'].'</td>
                                    <td>'.$row['estadoHGU'].'</td>
                                </tr>';
                break;

                case 'STATUS':

                    $html .= ' <tr ' . $htmlColorFila . '>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnDelete.'
                                        </div>
                                    </td>
                                    <td>'.$row['observacion'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['divicau'].'</td>
                                    <td>'.$row['estadoFinal'].'</td>
                                    <td>'.$row['estadoGlobal'].'</td>
                                </tr>';
                break;
            }

            $count++;
        }
        $html .= '</tbody>
            </table>';

        return array($html, $ctnVal, $arrayFinal, $count);
    }

    public function procesarFileMatrizSeguimientoDiseno()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $retorno = $this->saveUploadFileMatriz();
            if (isset($retorno['nombreFicheroTemp']))
            {
                $nombreFicheroTemp = $retorno['nombreFicheroTemp'];
                $count = 1;
                $arrayFinal             = array();
                $arrayTabla             = array();
                $dataArray              = array();
                $arrayOC                = array();
                $ctnVal = 0;
                $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
                if ($objectExcel != '')
                {
                    $col = 1;
                    foreach ($objectExcel->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for ($row = 2; $row <= $highestRow; $row++)
                        {
                            $col = 0;
                            

                            $anio = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $anio = _removeEnterYTabs(trim(utf8_encode(utf8_decode($anio)),'?'));
                            $col++;
                            $divicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $divicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($divicau)),'?'));
                            $col++;
                            $plan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $plan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($plan)),'?'));
                            $col++;
                            $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                            $col++; 
                                        
                            $modelo = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $modelo = _removeEnterYTabs(trim(utf8_encode(utf8_decode($modelo)),'?'));
                            $col++;
                            $cable = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $cable = _removeEnterYTabs(trim(utf8_encode(utf8_decode($cable)),'?'));
                            $col++;
                            $nodo = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $nodo = _removeEnterYTabs(trim(utf8_encode(utf8_decode($nodo)),'?'));
                            $col++;

                            $tipoDivicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $tipoDivicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($tipoDivicau)),'?'));
                            $col++;

                            $troba = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $troba = _removeEnterYTabs(trim(utf8_encode(utf8_decode($troba)),'?'));
                            $col++;

                            $estadoDiseno = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $estadoDiseno = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoDiseno)),'?'));
                            $col++;
                            $uipDiseno = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $uipDiseno = _removeEnterYTabs(trim(utf8_encode(utf8_decode($uipDiseno)),'?'));
                            $col++;
                            $departamento = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $departamento = _removeEnterYTabs(trim(utf8_encode(utf8_decode($departamento)),'?'));
                            $departamento = strtoupper($departamento);
                            $col++;
                            $provincia = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $provincia = _removeEnterYTabs(trim(utf8_encode(utf8_decode($provincia)),'?'));
                            $provincia = strtoupper($provincia);
                            $col++;
                            $distrito = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $distrito = _removeEnterYTabs(trim(utf8_encode(utf8_decode($distrito)),'?'));
                            $distrito = strtoupper($distrito);
                            $col++;

                            $fechaTermino       = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaTerminoI       = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaTermino)),'?'));
                            // $fechaTerminoa       = PHPExcel_Shared_Date::ExcelToPHP($fechaTerminoI);
                            // $fechaTermino       = date('Y-m-d',$fechaTerminoa);


                            $fechaTerminoa = ($fechaTerminoI - 25569) * 86400;
                            $fechaTermino = gmdate("Y-m-d", $fechaTerminoa);

                            // pre($fechaTerminoI, $fechaTerminoa);


                            $col++;
                            $col++;
                            $eecc   = '';


                            $dataArray['observacion'] = '';
                            if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                            {
                                $dataArray['observacion'] .= 'El campo itemplan es requerido.<br>';
                            }

                            if (strlen($divicau) == 0 || $divicau == null || $divicau == '')
                            {
                                $dataArray['observacion'] .= 'El campo divicau es requerido.<br>';
                            }

                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanDivicau(trim($itemplan), trim($divicau));
                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'La combinación de Itemplan + Divicau no forma parte del seguimiento<br>';
                            }

                            $modelo_Arr = array('FUS', 'PREC');
                            if (!in_array(mayusculas($modelo), $modelo_Arr))
                            {
                                 $dataArray['observacion'] .= 'El modelo ingresado no forma parte de los valores permitidos.<br>';
                            }

                            $tipoDivicau_Arr = array('DIVICAU', 'HUB-BOX');
                            if (!in_array(mayusculas($tipoDivicau), $tipoDivicau_Arr))
                            {
                                 $dataArray['observacion'] .= 'El tipo de DIVICAU no forma parte de los valores permitidos.<br>';
                            }

                            $estadoDiseno_Arr = array('Diseño/Expediente', 'Carga en OSP', 'Liquidacion en Weblight', 'Pendiente', 'TERMINADO', 'TRUNCO');
                            if (!in_array(mayusculas($estadoDiseno), $estadoDiseno_Arr))
                            {
                                 $dataArray['observacion'] .= 'El estado de diseño no forma parte de los valores permitidos.<br>';
                            }

                            if (!empty($fechaTerminoI))
                            {
                                if (!validateDate($fechaTerminoI))
                                {
                                    $dataArray['observacion'] .= 'La fecha de termino ingresado no cumple el formato de término.<br>';
                                    $fechaTermino = $fechaTerminoI;
                                }
                            }else
                            {
                                $fechaTermino = '';
                            }

                            
                            // $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanInPlanObra(trim($itemplan));
                            // if (count($dataArrayDetalle) == 0)
                            // {
                            //     $dataArray['observacion'] .= 'El Item plan ingresado no existe en plan de obras<br>';
                            // }

                            $arrayParticleCode[]                        = $itemplan.''.$divicau;
                            $dataArray['itemplanDivicau']               = $itemplan.''.$divicau;
                            $dataArray['anio']                          = $anio;
                            $dataArray['divicau']                       = $divicau;
                            $dataArray['plan']                          = $plan;
                            $dataArray['itemplan']                      = $itemplan;
                            $dataArray['modelo']                        = $modelo;
                            $dataArray['cable']                         = $cable;
                            $dataArray['nodo']                         = $nodo;
                            $dataArray['tipoDivicau']                   = $tipoDivicau;
                            $dataArray['troba']                         = $troba;
                            $dataArray['estadoDiseno']                  = $estadoDiseno;
                            $dataArray['uipDiseno']                     = $uipDiseno;
                            $dataArray['departamento']                  = mayusculas($departamento);
                            $dataArray['provincia']                     = mayusculas($provincia);
                            $dataArray['distrito']                      = mayusculas($distrito);
                            $dataArray['fechaTermino']                  = ($fechaTermino);
                            
                            $arrayTabla []= $dataArray;

                        }
                    }
                }

                    list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistroMatriz($arrayTabla, 'DISENO');
                    $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
                    $data['tbObservacion'] = $html;
                    $data['jsonDataFile'] = json_encode($arrayFinal);

                    $data['msj']  = 'Se procesó correctamente el archivo!!';
                    $data['error']  = EXIT_SUCCESS;
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    public function procesarFileMatrizSeguimientoEconomico()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $retorno = $this->saveUploadFileMatriz();
            if (isset($retorno['nombreFicheroTemp']))
            {
                $nombreFicheroTemp = $retorno['nombreFicheroTemp'];
                $count = 1;
                $arrayFinal             = array();
                $arrayTabla             = array();
                $dataArray              = array();
                $arrayOC                = array();
                $ctnVal = 0;
                $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
                if ($objectExcel != '')
                {
                    $col = 1;
                    foreach ($objectExcel->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for ($row = 2; $row <= $highestRow; $row++)
                        {
                            $col = 0;
                            $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                            $col++; 
                                        
                            $confirmacionPPTO = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $confirmacionPPTO = _removeEnterYTabs(trim(utf8_encode(utf8_decode($confirmacionPPTO)),'?'));
                            $confirmacionPPTO = strtoupper($confirmacionPPTO);
                            $col++;
                            $estadoOC = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $estadoOC = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoOC)),'?'));
                            $estadoOC = strtoupper($estadoOC);
                            $col++;

                            $n_pep = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $n_pep = _removeEnterYTabs(trim(utf8_encode(utf8_decode($n_pep)),'?'));
                            $n_pep = strtoupper($n_pep);
                            $col++;

                            $n_oc = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $n_oc = _removeEnterYTabs(trim(utf8_encode(utf8_decode($n_oc)),'?'));
                            $n_oc = strtoupper($n_oc);
                            $col++;

                            $generacion_vr = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $generacion_vr = _removeEnterYTabs(trim(utf8_encode(utf8_decode($generacion_vr)),'?'));
                            $generacion_vr = strtoupper($generacion_vr);
                            $col++;
                            $certificacion_oc = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $certificacion_oc = _removeEnterYTabs(trim(utf8_encode(utf8_decode($certificacion_oc)),'?'));
                            $certificacion_oc = strtoupper($certificacion_oc);
                            $col++;
                            $col++;
                            $eecc   = '';


                            $dataArray['observacion'] = '';
                            if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                            {
                                $dataArray['observacion'] .= 'El campo itemplan es requerido.<br>';
                            }

                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyByItemplan(trim($itemplan));

                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'El itemplan ingresado no existe.<br>';
                            }

                            if (!empty($estadoOC))
                            {
                                $estadoOC_Arr = array('PENDIENTE', 'EN GETEC', 'ATENDIDO');
                                if (!in_array(mayusculas($estadoOC), $estadoOC_Arr))
                                {
                                     $dataArray['observacion'] .= 'El estado IC no forma parte de los valores permitidos.<br>';
                                }
                            }

                            if (!empty($confirmacionPPTO))
                            {
                                $confirmacionPPTO_Arr = array('SI', 'NO');
                                if (!in_array(mayusculas($confirmacionPPTO), $confirmacionPPTO_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor de confirmación de ppto, no forma parte de los valores permitidos.<br>';
                                }
                            }

                            if (!empty($generacion_vr))
                            {
                                $generacion_vr_Arr = array('COMPLETO', 'PENDIENTE DE STOCK', 'PARCIAL');
                                if (!in_array(mayusculas($generacion_vr), $generacion_vr_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor Generacion VR, no forma parte de los valores permitidos.<br>';
                                }
                            }

                            if (!empty($certificacion_oc))
                            {
                                $certificacion_oc_Arr = array('PENDIENTE', 'EN EJECUCION', 'CERTIFICADO');
                                if (!in_array(mayusculas($certificacion_oc), $certificacion_oc_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor certificacion OC, no forma parte de los valores permitidos.<br>';
                                }
                            }



                            $arrayParticleCode[]                        = $itemplan;
                            $dataArray['itemplanDivicau']               = $itemplan;
                            $dataArray['itemplan']                      = $itemplan;
                            $dataArray['confirmacionPPTO']              = $confirmacionPPTO;
                            $dataArray['estadoOC']                      = $estadoOC;
                            $dataArray['n_pep']                         = $n_pep;
                            $dataArray['n_oc']                          = $n_oc;
                            $dataArray['generacion_vr']                 = $generacion_vr;
                            $dataArray['certificacion_oc']              = $certificacion_oc;
                            
                            $arrayTabla []= $dataArray;

                        }
                    }
                }

                    list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistroMatriz($arrayTabla, 'ECONOMICO');
                    $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
                    $data['tbObservacion'] = $html;
                    $data['jsonDataFile'] = json_encode($arrayFinal);

                    $data['msj']  = 'Se procesó correctamente el archivo!!';
                    $data['error']  = EXIT_SUCCESS;
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    public function procesarFileMatrizSeguimientoLicencia()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $retorno = $this->saveUploadFileMatriz();
            if (isset($retorno['nombreFicheroTemp']))
            {
                $nombreFicheroTemp = $retorno['nombreFicheroTemp'];
                $count = 1;
                $arrayFinal             = array();
                $arrayTabla             = array();
                $dataArray              = array();
                $arrayOC                = array();
                $ctnVal = 0;
                $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
                if ($objectExcel != '')
                {
                    $col = 1;
                    foreach ($objectExcel->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for ($row = 2; $row <= $highestRow; $row++)
                        {
                            $col = 0;
                            $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                            $col++; 
                                        
                            $fechaPresentaLicencia      = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaPresentaLicenciaI     = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaPresentaLicencia)),'?'));
                            // $fechaPresentaLicenciaa     = PHPExcel_Shared_Date::ExcelToPHP($fechaPresentaLicenciaI);
                            // $fechaPresentaLicenciax      = date('Y-m-d',$fechaPresentaLicenciaa);

                            $fechaPresentaLicenciaa = ($fechaPresentaLicenciaI - 25569) * 86400;
                            $fechaPresentaLicenciax = gmdate("Y-m-d", $fechaPresentaLicenciaa);




                            $col++;
                            $fechaInicioLicencia    = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaInicioLicenciaI     = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaInicioLicencia)),'?'));
                            
                            $fechaInicioLicenciaIx = ($fechaInicioLicenciaI - 25569) * 86400;
                            $fechaInicioLicenciaax = gmdate("Y-m-d", $fechaInicioLicenciaIx);






                            $col++;
                            $estadoLicencia         = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $estadoLicencia         = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoLicencia)),'?'));
                            $estadoLicencia = strtoupper($estadoLicencia);
                            $col++;
                            $col++;
                            $eecc   = '';


                            $dataArray['observacion'] = '';
                            if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                            {
                                $dataArray['observacion'] .= 'El campo itemplan es requerido.<br>';
                            }

                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyByItemplan(trim($itemplan));

                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'El itemplan ingresado no existe.<br>';
                            }

                            if (!empty($fechaPresentaLicencia))
                            {
                                if (!validateDate($fechaPresentaLicenciaI))
                                {
                                    $dataArray['observacion'] .= 'La fecha de Presentacion ingresado no cumple el formato de término.<br>';
                                    $fechaPresentaLicencia = $fechaPresentaLicenciaI;
                                }
                                else
                                {
                                    $fechaPresentaLicencia = $fechaPresentaLicenciax;
                                }
                            } else
                            {
                                $fechaPresentaLicencia = '';
                            }


                            if (!empty($fechaInicioLicencia))
                            {
                                if (!validateDate($fechaInicioLicenciaI))
                                {
                                    $dataArray['observacion'] .= 'La fecha de Inicio ingresado no cumple el formato de término.<br>';
                                    $fechaInicioLicencia = $fechaInicioLicenciaI;
                                }
                                else
                                {
                                    $fechaInicioLicencia = $fechaInicioLicenciaax;
                                }
                            } else
                            {
                                $fechaInicioLicencia = '';
                            }

                            if (!empty($estadoLicencia))
                            {
                                // $estadoLicencia_Arr = array('PENDIENTE', 'EN EJECUCION', 'CERTIFICADO');
                                $estadoLicencia_Arr = array('PENDIENTE', 'EN GESTION', 'CON LICENCIA');
                                if (!in_array(mayusculas($estadoLicencia), $estadoLicencia_Arr))
                                {
                                     $dataArray['observacion'] .= 'El estado de Licencia no forma parte de los valores permitidos.<br>';
                                }
                            }

                            $arrayParticleCode[]                        = $itemplan;
                            $dataArray['itemplanDivicau']               = $itemplan;
                            $dataArray['itemplan']                      = $itemplan;
                            $dataArray['fechaPresentaLicencia']         = $fechaPresentaLicencia;
                            $dataArray['fechaInicioLicencia']           = $fechaInicioLicencia;
                            $dataArray['estadoLicencia']                = $estadoLicencia;
                            
                            $arrayTabla []= $dataArray;

                        }
                    }
                }

                    list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistroMatriz($arrayTabla, 'LICENCIA');
                    $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
                    $data['tbObservacion'] = $html;
                    $data['jsonDataFile'] = json_encode($arrayFinal);

                    $data['msj']  = 'Se procesó correctamente el archivo!!';
                    $data['error']  = EXIT_SUCCESS;
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    public function procesarFileMatrizSeguimientoLogistica()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $retorno = $this->saveUploadFileMatriz();
            if (isset($retorno['nombreFicheroTemp']))
            {
                $nombreFicheroTemp = $retorno['nombreFicheroTemp'];
                $count = 1;
                $arrayFinal             = array();
                $arrayTabla             = array();
                $dataArray              = array();
                $arrayOC                = array();
                $ctnVal = 0;
                $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
                if ($objectExcel != '')
                {
                    $col = 1;
                    foreach ($objectExcel->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for ($row = 2; $row <= $highestRow; $row++)
                        {
                            $col = 0;
                            $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                            $col++; 
                                        
                            $confirmacionEntrega = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $confirmacionEntrega = _removeEnterYTabs(trim(utf8_encode(utf8_decode($confirmacionEntrega)),'?'));
                            $confirmacionEntrega = strtoupper($confirmacionEntrega);
                            $col++;
                            $col++;
                            $eecc   = '';


                            $dataArray['observacion'] = '';
                            if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                            {
                                $dataArray['observacion'] .= 'El campo itemplan es requerido.<br>';
                            }

                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyByItemplan(trim($itemplan));

                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'El itemplan ingresado no existe.<br>';
                            }

                            if (!empty($confirmacionEntrega))
                            {
                                $confirmacionEntrega_Arr = array('DESPACHADO', 'PENDIENTE', 'FALTA DE STOCK');
                                if (!in_array(mayusculas($confirmacionEntrega), $confirmacionEntrega_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor de confirmacion de entrega, no forma parte de los valores permitidos.<br>';
                                }
                            }

                            $arrayParticleCode[]                        = $itemplan;
                            $dataArray['itemplanDivicau']               = $itemplan;
                            $dataArray['itemplan']                      = $itemplan;
                            $dataArray['confirmacionEntrega']              = $confirmacionEntrega;
                            
                            $arrayTabla []= $dataArray;

                        }
                    }
                }

                    list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistroMatriz($arrayTabla, 'LOGISTICA');
                    $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
                    $data['tbObservacion'] = $html;
                    $data['jsonDataFile'] = json_encode($arrayFinal);

                    $data['msj']  = 'Se procesó correctamente el archivo!!';
                    $data['error']  = EXIT_SUCCESS;
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    public function procesarFileMatrizSeguimientoPin()
    {

    }
    public function procesarFileMatrizSeguimientoCensado()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $retorno = $this->saveUploadFileMatriz();
            if (isset($retorno['nombreFicheroTemp']))
            {
                $nombreFicheroTemp = $retorno['nombreFicheroTemp'];
                $count = 1;
                $arrayFinal             = array();
                $arrayTabla             = array();
                $dataArray              = array();
                $arrayOC                = array();
                $ctnVal = 0;
                $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
                if ($objectExcel != '')
                {
                    $col = 1;
                    foreach ($objectExcel->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for ($row = 2; $row <= $highestRow; $row++)
                        {
                            $col = 0;
                            $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                            $col++; 
                                        
                            $divicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $divicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($divicau)),'?'));
                            $col++;

                            $fechaCensado    = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaCensadoI     = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaCensado)),'?'));
                            // $fechaCensadoa     = PHPExcel_Shared_Date::ExcelToPHP($fechaCensadoI);
                            // $fechaCensadox      = date('Y-m-d',$fechaCensadoa);


                            $fechaCensadoa = ($fechaCensadoI - 25569) * 86400;
                            $fechaCensadox = gmdate("Y-m-d", $fechaCensadoa);

                            $col++;
                            $cantidadUIP = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $cantidadUIP = _removeEnterYTabs(trim(utf8_encode(utf8_decode($cantidadUIP)),'?'));
                            $col++;
                            $estadoCenso = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $estadoCenso = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoCenso)),'?'));
                            $estadoCenso = strtoupper($estadoCenso);
                            $col++;
                            $col++;
                            $eecc   = '';


                            $dataArray['observacion'] = '';
                            if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                            {
                                $dataArray['observacion'] .= 'El campo itemplan es requerido.<br>';
                            }

                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyByItemplan(trim($itemplan));

                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'El itemplan ingresado no existe.<br>';
                            }


                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanDivicau(trim($itemplan), trim($divicau));
                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'La combinación de Itemplan + Divicau no forma parte del seguimiento<br>';
                            }


                            if (!empty($fechaCensado))
                            {
                                if (!validateDate($fechaCensadoI))
                                {
                                    $dataArray['observacion'] .= 'La fecha de Inicio ingresado no cumple el formato de término.<br>';
                                    $fechaCensado = $fechaCensadoI;
                                }
                                else
                                {
                                    $fechaCensado = $fechaCensadox;
                                }
                            } else
                            {
                                $fechaCensado = '';
                            }



                            if (!empty($estadoCenso))
                            {
                                $estadoCenso_Arr = array('TERMINADO', 'PENDIENTE');
                                if (!in_array(mayusculas($estadoCenso), $estadoCenso_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor de Estado de Censo, no forma parte de los valores permitidos.<br>';
                                }
                            }

                            $arrayParticleCode[]                        = $itemplan;
                            $dataArray['itemplanDivicau']               = $itemplan;
                            $dataArray['itemplan']                      = $itemplan;
                            $dataArray['divicau']                       = $divicau;
                            $dataArray['fechaCensado']                  = $fechaCensado;
                            $dataArray['cantidadUIP']                   = $cantidadUIP;
                            $dataArray['estadoCenso']                   = $estadoCenso;
                            
                            $arrayTabla []= $dataArray;

                        }
                    }
                }

                    list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistroMatriz($arrayTabla, 'CENSADO');
                    $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
                    $data['tbObservacion'] = $html;
                    $data['jsonDataFile'] = json_encode($arrayFinal);

                    $data['msj']  = 'Se procesó correctamente el archivo!!';
                    $data['error']  = EXIT_SUCCESS;
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    public function procesarFileMatrizSeguimientoDespliegue()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $retorno = $this->saveUploadFileMatriz();
            if (isset($retorno['nombreFicheroTemp']))
            {
                $nombreFicheroTemp = $retorno['nombreFicheroTemp'];
                $count = 1;
                $arrayFinal             = array();
                $arrayTabla             = array();
                $dataArray              = array();
                $arrayOC                = array();
                $ctnVal = 0;
                $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
                if ($objectExcel != '')
                {
                    $col = 1;
                    foreach ($objectExcel->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for ($row = 2; $row <= $highestRow; $row++)
                        {
                            $col = 0;
                            $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                            $col++; 
                                        
                            $divicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $divicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($divicau)),'?'));
                            $col++;
                            $confirmacionDespliegue         = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $confirmacionDespliegueI        = _removeEnterYTabs(trim(utf8_encode(utf8_decode($confirmacionDespliegue)),'?'));
                            // $confirmacionDesplieguea        = PHPExcel_Shared_Date::ExcelToPHP($confirmacionDespliegueI);
                            // $confirmacionDesplieguex        = date('Y-m-d',$confirmacionDesplieguea);

                            $confirmacionDesplieguea = ($confirmacionDespliegueI - 25569) * 86400;
                            $confirmacionDesplieguex = gmdate("Y-m-d", $confirmacionDesplieguea);




                            $col++;
                            $fechaInstalacionODF = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaInstalacionODFI        = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaInstalacionODF)),'?'));
                            // $fechaInstalacionODFa        = PHPExcel_Shared_Date::ExcelToPHP($fechaInstalacionODFI);
                            // $fechaInstalacionODFx        = date('Y-m-d',$fechaInstalacionODFa);

                            $fechaInstalacionODFa = ($fechaInstalacionODFI - 25569) * 86400;
                            $fechaInstalacionODFx = gmdate("Y-m-d", $fechaInstalacionODFa);





                            // $fechaInstalacionODF = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaInstalacionODF)),'?'));
                            $col++;
                            $fechaInicioConstruccion        = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaInicioConstruccionI       = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaInicioConstruccion)),'?'));
                            // $fechaInicioConstrucciona       = PHPExcel_Shared_Date::ExcelToPHP($fechaInicioConstruccionI);
                            // $fechaInicioConstruccionx       = date('Y-m-d',$fechaInicioConstrucciona);

                            $fechaInicioConstrucciona = ($fechaInicioConstruccionI - 25569) * 86400;
                            $fechaInicioConstruccionx = gmdate("Y-m-d", $fechaInicioConstrucciona);



                            // $fechaInicioConstruccion = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaInicioConstruccion)),'?'));
                            $col++;
                            $fechaProyectadaEntrega         = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaProyectadaEntregaI        = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaProyectadaEntrega)),'?'));
                            // $fechaProyectadaEntregaa        = PHPExcel_Shared_Date::ExcelToPHP($fechaProyectadaEntregaI);
                            // $fechaProyectadaEntregax        = date('Y-m-d',$fechaProyectadaEntregaa);

                            $fechaProyectadaEntregaa = ($fechaProyectadaEntregaI - 25569) * 86400;
                            $fechaProyectadaEntregax = gmdate("Y-m-d", $fechaProyectadaEntregaa);



                            // $fechaProyectadaEntrega = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaProyectadaEntrega)),'?'));
                            $col++;
                            $fechaFinalEntregaDivicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaFinalEntregaDivicauI        = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaFinalEntregaDivicau)),'?'));
                            // $fechaFinalEntregaDivicaua        = PHPExcel_Shared_Date::ExcelToPHP($fechaFinalEntregaDivicauI);
                            // $fechaFinalEntregaDivicaux        = date('Y-m-d',$fechaFinalEntregaDivicaua);

                            $fechaFinalEntregaDivicaua = ($fechaFinalEntregaDivicauI - 25569) * 86400;
                            $fechaFinalEntregaDivicaux = gmdate("Y-m-d", $fechaFinalEntregaDivicaua);




                            // $fechaFinalEntregaDivicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaFinalEntregaDivicau)),'?'));
                            $col++;
                            $estadoDespliegue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $estadoDespliegue = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoDespliegue)),'?'));
                            $estadoDespliegue = strtoupper($estadoDespliegue);
                            $col++;
                            $col++;
                            $eecc   = '';


                            $dataArray['observacion'] = '';
                            if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                            {
                                $dataArray['observacion'] .= 'El campo itemplan es requerido.<br>';
                            }

                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyByItemplan(trim($itemplan));

                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'El itemplan ingresado no existe.<br>';
                            }


                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanDivicau(trim($itemplan), trim($divicau));
                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'La combinación de Itemplan + Divicau no forma parte del seguimiento<br>';
                            }


                            if (!empty($confirmacionDespliegue))
                            {
                                if (!validateDate($confirmacionDespliegueI))
                                {
                                    $dataArray['observacion'] .= 'La fecha Confirmación Bandeja ODF, no cumple el formato de fecha.<br>';
                                    $confirmacionDespliegue = $confirmacionDespliegueI;
                                }
                                else
                                {
                                    $confirmacionDespliegue = $confirmacionDesplieguex;
                                }
                            } else
                            {
                                $confirmacionDespliegue = '';
                            }


                            if (!empty($fechaInstalacionODF))
                            {
                                if (!validateDate($fechaInstalacionODFI))
                                {
                                    $dataArray['observacion'] .= 'La fecha de Instalación ODF, no cumple el formato de fecha.<br>';
                                    $fechaInstalacionODF = $fechaInstalacionODFI;
                                }
                                else
                                {
                                    $fechaInstalacionODF = $fechaInstalacionODFx;
                                }
                            } else
                            {
                                $fechaInstalacionODF = '';
                            }




                            if (!empty($fechaInicioConstruccion))
                            {
                                if (!validateDate($fechaInicioConstruccionI))
                                {
                                    $dataArray['observacion'] .= 'La fecha de Inicio de construcción, no cumple el formato de fecha.<br>';
                                    $fechaInicioConstruccion = $fechaInicioConstruccionI;
                                }
                                else
                                {
                                    $fechaInicioConstruccion = $fechaInicioConstruccionx;
                                }
                            } else
                            {
                                $fechaInicioConstruccion = '';
                            }


                            if (!empty($fechaProyectadaEntrega))
                            {
                                if (!validateDate($fechaProyectadaEntregaI))
                                {
                                    $dataArray['observacion'] .= 'La fecha Proyectada de entrega, no cumple el formato de fecha.<br>';
                                    $fechaProyectadaEntrega = $fechaProyectadaEntregaI;
                                }
                                else
                                {
                                    $fechaProyectadaEntrega = $fechaProyectadaEntregax;
                                }
                            } else
                            {
                                $fechaProyectadaEntrega = '';
                            }


                            if (!empty($fechaFinalEntregaDivicau))
                            {
                                if (!validateDate($fechaFinalEntregaDivicauI))
                                {
                                    $dataArray['observacion'] .= 'La fecha Final de entrega DIVICAU, no cumple el formato de fecha.<br>';
                                    $fechaFinalEntregaDivicau = $fechaFinalEntregaDivicauI;
                                }
                                else
                                {
                                    $fechaFinalEntregaDivicau = $fechaFinalEntregaDivicaux;
                                }
                            } else
                            {
                                $fechaFinalEntregaDivicau = '';
                            }



                            if (!empty($estadoDespliegue))
                            {
                                $estadoDespliegue_Arr = array('SIN INICIO', 'EN CONSTRUCCION', 'EJECUCION', 'ETAPA DE PASIVOS', 'MEDICIONES', 'PENDIENTE DE MATERIALES', 'TERMINADO');
                                if (!in_array(mayusculas($estadoDespliegue), $estadoDespliegue_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor de Estado de Despliegue, no forma parte de los valores permitidos.<br>';
                                }
                            }

                            $arrayParticleCode[]                        = $itemplan;
                            $dataArray['itemplanDivicau']               = $itemplan;
                            $dataArray['itemplan']                      = $itemplan;
                            $dataArray['divicau']                       = $divicau;
                            $dataArray['confirmacionDespliegue']        = $confirmacionDespliegue;
                            $dataArray['fechaInstalacionODF']           = $fechaInstalacionODF;
                            $dataArray['fechaInicioConstruccion']       = $fechaInicioConstruccion;
                            $dataArray['fechaProyectadaEntrega']        = $fechaProyectadaEntrega;
                            $dataArray['fechaFinalEntregaDivicau']      = $fechaFinalEntregaDivicau;
                            $dataArray['estadoDespliegue']              = $estadoDespliegue;
                            
                            $arrayTabla []= $dataArray;

                        }
                    }
                }

                    list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistroMatriz($arrayTabla, 'DESPLIEGUE');
                    $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
                    $data['tbObservacion'] = $html;
                    $data['jsonDataFile'] = json_encode($arrayFinal);

                    $data['msj']  = 'Se procesó correctamente el archivo!!';
                    $data['error']  = EXIT_SUCCESS;
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    public function procesarFileMatrizSeguimientoHGU()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $retorno = $this->saveUploadFileMatriz();
            if (isset($retorno['nombreFicheroTemp']))
            {
                $nombreFicheroTemp = $retorno['nombreFicheroTemp'];
                $count = 1;
                $arrayFinal             = array();
                $arrayTabla             = array();
                $dataArray              = array();
                $arrayOC                = array();
                $ctnVal = 0;
                $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
                if ($objectExcel != '')
                {
                    $col = 1;
                    foreach ($objectExcel->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for ($row = 2; $row <= $highestRow; $row++)
                        {
                            $col = 0;
                            $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                            $col++; 
                                        
                            $divicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $divicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($divicau)),'?'));
                            $col++;
                            $fechaPruebaHGU = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $fechaPruebaHGUI        = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fechaPruebaHGU)),'?'));
                            // $fechaPruebaHGUa        = PHPExcel_Shared_Date::ExcelToPHP($fechaPruebaHGUI);
                            // $fechaPruebaHGUx        = date('Y-m-d',$fechaPruebaHGUa);

                            $fechaPruebaHGUa = ($fechaPruebaHGUI - 25569) * 86400;
                            $fechaPruebaHGUx = gmdate("Y-m-d", $fechaPruebaHGUa);


                            $col++;
                            $comodinAvanceHGU = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $comodinAvanceHGU = _removeEnterYTabs(trim(utf8_encode(utf8_decode($comodinAvanceHGU)),'?'));
                            $col++;
                            $estadoHGU = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $estadoHGU = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoHGU)),'?'));
                            $estadoHGU = strtoupper($estadoHGU);
                            $col++;
                            $col++;
                            $eecc   = '';

                            $dataArray['observacion'] = '';
                            if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                            {
                                $dataArray['observacion'] .= 'El campo itemplan es requerido.<br>';
                            }

                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyByItemplan(trim($itemplan));

                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'El itemplan ingresado no existe.<br>';
                            }


                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanDivicau(trim($itemplan), trim($divicau));
                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'La combinación de Itemplan + Divicau no forma parte del seguimiento<br>';
                            }


                            


                            if (!empty($fechaPruebaHGU))
                            {
                                if (!validateDate($fechaPruebaHGUI))
                                {
                                    $dataArray['observacion'] .= 'La fecha de Prueba, no cumple el formato de fecha.<br>';
                                    $fechaPruebaHGU = $fechaPruebaHGUI;
                                }
                                else
                                {
                                    $fechaPruebaHGU = $fechaPruebaHGUx;
                                }
                            } else
                            {
                                $fechaPruebaHGU = '';
                            }





                            if (!empty($estadoHGU))
                            {
                                $estadoHGU_Arr = array('PENDIENTE', 'COMPLETO');
                                if (!in_array(mayusculas($estadoHGU), $estadoHGU_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor de Estado HGU, no forma parte de los valores permitidos.<br>';
                                }
                            }

                            $arrayParticleCode[]                        = $itemplan;
                            $dataArray['itemplanDivicau']               = $itemplan;
                            $dataArray['itemplan']                      = $itemplan;
                            $dataArray['divicau']                       = $divicau;
                            $dataArray['fechaPruebaHGU']                = $fechaPruebaHGU;
                            $dataArray['comodinAvanceHGU']              = $comodinAvanceHGU;
                            $dataArray['estadoHGU']                     = $estadoHGU;
                            
                            $arrayTabla []= $dataArray;

                        }
                    }
                }

                    list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistroMatriz($arrayTabla, 'HGU');
                    $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
                    $data['tbObservacion'] = $html;
                    $data['jsonDataFile'] = json_encode($arrayFinal);

                    $data['msj']  = 'Se procesó correctamente el archivo!!';
                    $data['error']  = EXIT_SUCCESS;
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    public function procesarFileMatrizSeguimientoStatus()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $retorno = $this->saveUploadFileMatriz();
            if (isset($retorno['nombreFicheroTemp']))
            {
                $nombreFicheroTemp = $retorno['nombreFicheroTemp'];
                $count = 1;
                $arrayFinal             = array();
                $arrayTabla             = array();
                $dataArray              = array();
                $arrayOC                = array();
                $ctnVal = 0;
                $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
                if ($objectExcel != '')
                {
                    $col = 1;
                    foreach ($objectExcel->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for ($row = 2; $row <= $highestRow; $row++)
                        {
                            $col = 0;
                            $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                            $col++; 
                                        
                            $divicau = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $divicau = _removeEnterYTabs(trim(utf8_encode(utf8_decode($divicau)),'?'));
                            $col++;
                            $estadoFinal = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $estadoFinal = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoFinal)),'?'));
                            $estadoFinal = strtoupper($estadoFinal);
                            $col++;
                            $estadoGlobal = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                            $estadoGlobal = _removeEnterYTabs(trim(utf8_encode(utf8_decode($estadoGlobal)),'?'));
                            $estadoGlobal = strtoupper($estadoGlobal);
                            $col++;
                            $col++;
                            $eecc   = '';


                            $dataArray['observacion'] = '';
                            if(strlen($itemplan) == 0 || $itemplan == null || $itemplan == '')
                            {
                                $dataArray['observacion'] .= 'El campo itemplan es requerido.<br>';
                            }

                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyByItemplan(trim($itemplan));

                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'El itemplan ingresado no existe.<br>';
                            }


                            $dataArrayDetalle = $this->m_matriz_seguimiento->verifyItemPanDivicau(trim($itemplan), trim($divicau));
                            if (count($dataArrayDetalle)== 0)
                            {
                                $dataArray['observacion'] .= 'La combinación de Itemplan + Divicau no forma parte del seguimiento<br>';
                            }

                            if (!empty($estadoFinal))
                            {
                                $estadoFinal_Arr = array('EN EJECUCION', 'PENDIENTE', 'PASIVOS', 'POTENCIA', 'SIN INICIO', 'TERMINADO');
                                if (!in_array(mayusculas($estadoFinal), $estadoFinal_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor de Estado Final, no forma parte de los valores permitidos.<br>';
                                }
                            }

                            if (!empty($estadoGlobal))
                            {
                                $estadoGlobal_Arr = array('EN DESPLIEGUE', 'SIN INICIO', 'TERMINADO');
                                if (!in_array(mayusculas($estadoGlobal), $estadoGlobal_Arr))
                                {
                                     $dataArray['observacion'] .= 'El valor de Estado Global, no forma parte de los valores permitidos.<br>';
                                }
                            }

                            $arrayParticleCode[]                        = $itemplan;
                            $dataArray['itemplanDivicau']               = $itemplan;
                            $dataArray['itemplan']                      = $itemplan;
                            $dataArray['divicau']                       = $divicau;
                            $dataArray['estadoFinal']                   = $estadoFinal;
                            $dataArray['estadoGlobal']                  = $estadoGlobal;
                            
                            $arrayTabla []= $dataArray;

                        }
                    }
                }

                    list($html, $ctnValidos, $arrayFinal, $ctnTotal) = $this->tablaRegistroMatriz($arrayTabla, 'STATUS');
                    $data['titulo'] = 'Cantidad de registros válidos a cargar: ('.$ctnValidos.' de '.$ctnTotal.')';
                    $data['tbObservacion'] = $html;
                    $data['jsonDataFile'] = json_encode($arrayFinal);

                    $data['msj']  = 'Se procesó correctamente el archivo!!';
                    $data['error']  = EXIT_SUCCESS;
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }




    public function cargarMasivoMatrizSeguimientoDiseno()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    // pre($datos);
                    $anio                           = @$datos['anio'];              
                    $divicau                        = @$datos['divicau'];
                    $plan                           = @$datos['plan'];
                    $itemplan                       = @$datos['itemplan'];                        
                    $modelo                         = @$datos['modelo'];                        
                    $cable                          = @$datos['cable'];                        
                    $nodo                           = @$datos['nodo'];                     
                    $tipo                           = @$datos['tipoDivicau'];                        
                    $troba                          = @$datos['troba'];

                    $estadoDiseno                   = @$datos['estadoDiseno'];
                    $uipDiseno                      = @$datos['uipDiseno'];
                    $departamento                   = @$datos['departamento'];
                    $provincia                      = @$datos['provincia'];
                    $distrito                       = @$datos['distrito'];
                    $fechaTermino                   = @$datos['fechaTermino'];
                    
                    $dataArray = array();
                    $dataArray['anio']                              = trim($anio);
                    $dataArray['divicau']                           = trim($divicau);
                    $dataArray['plan']                              = trim($plan);
                    $dataArray['itemplan']                          = trim($itemplan);
                    $dataArray['modelo']                            = trim($modelo);
                    $dataArray['cable']                             = trim($cable);
                    $dataArray['nodo']                              = trim($nodo);
                    $dataArray['tipo']                              = trim($tipo);
                    $dataArray['troba']                             = trim($troba);
                    $dataArray['estadoDiseno']                      = trim($estadoDiseno);
                    $dataArray['uipHorizontalDiseno']               = trim($uipDiseno);
                    $dataArray['departamento']                      = trim($departamento);
                    $dataArray['provincia']                         = trim($provincia);
                    $dataArray['distrito']                          = trim($distrito);
                    $dataArray['fechaEntregaDiseno']                          = trim($fechaTermino);
                    
                    $id = 0;
                    $data_old = $this->db->select('id, anio, plan, modelo, cable, nodo, tipo, troba, fechaAdjudicaDiseno, fechaCierreDisenoExpediente, fechaEntregaDiseno, estadoDiseno, uipHorizontalDiseno')
                            ->where('itemplan', $itemplan)
                            ->get('matrizseguimiento')
                            ->result()[0];


                    $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoMasiva($dataArray, $data_old, $idUsuario, $id, 'DISEÑO');
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function cargarMasivoMatrizSeguimientoEconomico()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)($datos);
                    // pre($datos);
                    $itemplan                       = @$datos['itemplan'];                     
                    $confirmacionPPTO               = @$datos['confirmacionPPTO'];
                    $estadoOC                       = @$datos['estadoOC'];   
                    $n_pep                          = @$datos['n_pep'];                    
                    $n_oc                           = @$datos['n_oc'];                    
                    $generacion_vr                  = @$datos['generacion_vr'];                    
                    $certificacion_oc                  = @$datos['certificacion_oc'];                    
                    
                    $confirmacionPPTO               = mayusculas(trim($confirmacionPPTO));
                    $confirmacionPPTO               = ($confirmacionPPTO == 'SI') ? '1' : 0;
                    
                    $dataArray = array();
                    $dataArray['itemplan']           = trim($itemplan);
                    $dataArray['pptoAprobado']       = $confirmacionPPTO;
                    // $dataArray['estadoCertificaOC']  = ucfirst(strtolower(trim($estadoOC)));
                    $dataArray['estadoOC']           = ucfirst(strtolower(trim($estadoOC)));
                    $dataArray['pptoAprobado']       = $confirmacionPPTO;

                    $dataArray['pep']                = $n_pep;
                    $dataArray['ocConstruccionH']    = $n_oc;
                    $dataArray['generacionVR']       = $generacion_vr;
                    $dataArray['estadoCertificaOC']  = $certificacion_oc;

                    // pre($dataArray);
                    
                    $id = 0;
                    $data_old = $this->db->select('id, pptoAprobado, pep, ocConstruccionH, generacionVR, estadoOC, estadoCertificaOC')
                            ->where('itemplan', $itemplan)
                            ->get('matrizseguimiento')
                            ->result()[0];
                    
                    $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoMasiva($dataArray, $data_old, $idUsuario, $id, 'ECONOMICO');
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function cargarMasivoMatrizSeguimientoLicenciada()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    $itemplan                       = @$datos['itemplan'];                        
                    $fechaPresentaLicencia          = @$datos['fechaPresentaLicencia'];              
                    $fechaInicioLicencia            = @$datos['fechaInicioLicencia'];
                    $estadoLicencia                 = @$datos['estadoLicencia'];                 
             
                    
                    $dataArray = array();
                    $dataArray['itemplan']                          = trim($itemplan);
                    $dataArray['fechaPresentaLicencia']             = trim($fechaPresentaLicencia);
                    $dataArray['fechaInicioLicencia']               = trim($fechaInicioLicencia);
                    $dataArray['estadoLicencia']                    = ucfirst(strtolower(trim($estadoLicencia)));
                    
                    $id = 0;
                    $data_old = $this->db->select('id, fechaPresentaLicencia, fechaInicioLicencia, estadoLicencia')
                            ->where('itemplan', $itemplan)
                            ->get('matrizseguimiento')
                            ->result()[0];
                    
                    $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoMasiva($dataArray, $data_old, $idUsuario, $id, 'LICENCIADA');
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function cargarMasivoMatrizSeguimientoLogistica()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    $itemplan                       = @$datos['itemplan'];                        
                    $confirmacionEntrega            = @$datos['confirmacionEntrega']; 
                    
                    $dataArray = array();
                    $dataArray['itemplan']             = trim($itemplan);
                    $dataArray['entregaMateriales']  = trim($confirmacionEntrega);
                    
                    $id = 0;
                    $data_old = $this->db->select('id, entregaMateriales')
                            ->where('itemplan', $itemplan)
                            ->get('matrizseguimiento')
                            ->result()[0];
                    // pre($dataArray);
                    $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoMasiva($dataArray, $data_old, $idUsuario, $id, 'LOGISTICA');
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    
    public function cargarMasivoMatrizSeguimientoPIN()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    $anio                           = @$datos['anio'];              
                    $divicau                        = @$datos['divicau'];
                    $plan                           = @$datos['plan'];
                    $itemplan                       = @$datos['itemplan'];                        
                    $modelo                         = @$datos['modelo'];                        
                    $cable                          = @$datos['cable'];                        
                    $troba                          = @$datos['troba'];                        
                    $comodinPenetracion             = @$datos['comodinPenetracion'];                        
             
                    
                    $dataArray = array();
                    $dataArray['anio']                              = trim($anio);
                    $dataArray['divicau']                           = trim($divicau);
                    $dataArray['plan']                              = trim($plan);
                    $dataArray['itemplan']                          = trim($itemplan);
                    $dataArray['modelo']                            = trim($modelo);
                    $dataArray['cable']                             = trim($cable);
                    $dataArray['troba']                             = trim($troba);
                    $dataArray['comodinPenetracion']                = trim($comodinPenetracion);
                    
                    $data = $this->m_matriz_seguimiento->insertTabla($dataArray);
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    
    public function cargarMasivoMatrizSeguimientoCensado()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    // pre($datos);
                    $itemplan                       = @$datos['itemplan'];                        
                    $divicau                        = @$datos['divicau'];
                    $fechaCensado                   = @$datos['fechaCensado'];
                    $UIPHorizontalCenso             = @$datos['cantidadUIP'];                        
                    $estadoCenso                    = @$datos['estadoCenso'];                       
             
                    
                    $dataArray = array();
                    $dataArray['itemplan']                          = trim($itemplan);
                    $dataArray['divicau']                           = trim($divicau);
                    $dataArray['fechaCensado']                      = trim($fechaCensado);
                    $dataArray['UIPHorizontalCenso']                = trim($UIPHorizontalCenso);
                    $dataArray['estadoCenso']                       = trim($estadoCenso);
                    $id = 0;
                    $data_old = $this->db->select('id, fechaCensado, UIPHorizontalCenso, estadoCenso')
                            ->where('itemplan', $itemplan)
                            ->where('divicau', $divicau)
                            ->get('matrizseguimiento')
                            ->result()[0];
                    
                    $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoMasiva($dataArray, $data_old, $idUsuario, $id, 'CENSADO');
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function cargarMasivoMatrizSeguimientoDespliegue()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    $itemplan                       = @$datos['itemplan'];                        
                    $divicau                        = @$datos['divicau'];

                    $fechaInstalacionODF            = @$datos['fechaInstalacionODF'];
                    $fechaInicioConstruccion        = @$datos['fechaInicioConstruccion'];
                    $fechaProyectadaEntrega         = @$datos['fechaProyectadaEntrega'];                    
                    $fechaFinalEntregaDivicau       = @$datos['fechaFinalEntregaDivicau'];
                    $estadoDespliegue               = @$datos['estadoDespliegue'];
             
                    
                    $dataArray = array();
                    $dataArray['itemplan']                      = trim($itemplan);
                    $dataArray['divicau']                       = trim($divicau);
                    $dataArray['fechaInstalacionODF']           = trim($fechaInstalacionODF);
                    $dataArray['fechaInicioConstruccion']       = trim($fechaInicioConstruccion);
                    $dataArray['fechaProyectadaEntrega']        = trim($fechaProyectadaEntrega);
                    $dataArray['fechaFinalEntregaDivicau']      = trim($fechaFinalEntregaDivicau);
                    $dataArray['estadoDespliegue']              = trim($estadoDespliegue);
                    
                    $id = 0;

                    $data_old = $this->db->select('id, fechaInstalacionODF, fechaInicioConstruccion, fechaProyectadaEntrega, fechaFinalEntregaDivicau, estadoDespliegue')
                            ->where('itemplan', $itemplan)
                            ->where('divicau', $divicau)
                            ->get('matrizseguimiento')
                            ->result()[0];
                    $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoMasiva($dataArray, $data_old, $idUsuario, $id, 'DESPLIEGUE');
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function cargarMasivoMatrizSeguimientoHGU()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    $itemplan                       = @$datos['itemplan'];                  
                    $divicau                        = @$datos['divicau'];
                    $fechaPruebaHGU                 = @$datos['fechaPruebaHGU'];              
                    $comodinAvanceHGU               = @$datos['comodinAvanceHGU'];
                    $estadoHGU                      = @$datos['estadoHGU'];                         
             
                    
                    $dataArray = array();
                    $dataArray['itemplan']                          = trim($itemplan);
                    $dataArray['divicau']                           = trim($divicau);

                    $dataArray['fechaPruebaHGU']                    = trim($fechaPruebaHGU);
                    $dataArray['comodinAvanceHGU']                  = trim($comodinAvanceHGU);
                    $dataArray['estadoHGU']                         = trim($estadoHGU);
                    
                    $id = 0;



                    $data_old = $this->db->select('id, fechaPruebaHGU, comodinAvanceHGU, estadoHGU')
                            ->where('itemplan', $itemplan)
                            ->where('divicau', $divicau)
                            ->get('matrizseguimiento')
                            ->result()[0];
                    $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoMasiva($dataArray, $data_old, $idUsuario, $id, 'HGU');
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function cargarMasivoMatrizSeguimientoStatus()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            } 

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud       = array();               
                $arrayTabla                 = array();

                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    $itemplan                 = @$datos['itemplan'];          
                    $divicau                  = @$datos['divicau'];                    
                    $estadoFinal              = @$datos['estadoFinal'];                        
                    $estadoGlobal             = @$datos['estadoGlobal'];                        
             
                    
                    $dataArray = array();
                    $dataArray['divicau']                           = trim($divicau);
                    $dataArray['itemplan']                          = trim($itemplan);
                    $dataArray['estadoFinal']                       = ucfirst(strtolower(trim($estadoFinal)));
                    $dataArray['estadoGlobal']                      = ucfirst(strtolower(trim($estadoGlobal)));
                    $data_old = [];
                    $id = 0;



                    $data_old = $this->db->select('id, estadoFinal, estadoGlobal')
                            ->where('itemplan', $itemplan)
                            ->where('divicau', $divicau)
                            ->get('matrizseguimiento')
                            ->result()[0];
                    $data = $this->m_matriz_seguimiento->actualizaMatrizSeguimientoMasiva($dataArray, $data_old, $idUsuario, $id, 'STATUS');
                }

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }




}
?>