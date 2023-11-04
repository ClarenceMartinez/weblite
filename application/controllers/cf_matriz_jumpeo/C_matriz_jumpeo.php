<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class C_matriz_jumpeo extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_matriz_jumpeo/m_matriz_jumpeo');
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
	            $this->load->view('vf_matriz_jumpeo/index',$data);        	  
	    	 }else{
	        	redirect('login','refresh');
		    }  
    }

    public function getSolJumEECCPEXT()
    {
    	$idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');
        $idUsuario              = $this->session->userdata('idPersonaSessionPan');
	    $username 	            = $this->session->userdata('usernameSession');

		    if($idUsuario != null){           
	            $permisos =  $this->session->userdata('permisosArbolPan');	   
	            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);


	            $data['opciones'] = $result['html'];
	            $data['header'] = $this->lib_utils->getHeader();
	            $data['matriz'] = $this->getSolJumEECCPEXTJson(null);
	            $this->load->view('vf_matriz_jumpeo/getSolJumEECCPEXT',$data);        	  
	    	 }else{
	        	redirect('login','refresh');
		    }  
    }


    public function getSolJumEECCPEXTJson($estado)
    {
        $idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');
        $data = $this->getDataTable($this->m_matriz_jumpeo->getMatrizJumpeoByEECCPext($idEmpresaColabSesion, $estado), 1, 8);
        return $data;
    }


    public function searchMatrizJumpeoByFiltersgetSolJumEECCPEXT()
    {
        $data = [];
        $estado                 = $this->input->post('estado');
        $idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');
        $data['matriz']         = $this->getSolJumEECCPEXTJson($estado);

        $data['error']  = EXIT_SUCCESS;
        $data['msj']    = 'Se atendió exitosamente las solicitudes!!';

        echo json_encode($data);
    }

    public function getSolJumEECCPINT()
    {
    	$idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    	$username 	= $this->session->userdata('usernameSession');
		    if($idUsuario != null){           
	            $permisos =  $this->session->userdata('permisosArbolPan');	   
	            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);


	            $data['opciones']  = $result['html'];
	            $data['header']    = $this->lib_utils->getHeader();
                $data['matriz']    = [];
                
                $data['matriz'] = $this->getSolJumEECCPINTJson(null);                    
                $this->load->view('vf_matriz_jumpeo/getSolJumEECCPINT',$data);        	  
	    	 }else{
	        	redirect('login','refresh');
		    }  
    }

    public function getSolJumEECCPINTJson($estado)
    {
        $idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');
        $empresaColabDesc        = $this->session->userdata('empresaColabDesc');
        
        return $this->getDataTable($this->m_matriz_jumpeo->getMatrizProgramadaByEmpresaColab($idEmpresaColabSesion, $estado), 2);
    }

    public function searchMatrizJumpeoByFiltersgetSolJumEECCPIN()
    {
        $data = [];
        $estado                 = $this->input->post('estado');
        $data['matriz']         = $this->getSolJumEECCPINTJson($estado);

        $data['error']  = EXIT_SUCCESS;
        $data['msj']    = 'Se atendió exitosamente las solicitudes!!';

        echo json_encode($data);
    }

    public function getInfoMatrizJumpeoById()
    {
        $data = [];
        $id                 = $this->input->post('id');
        $response          = $this->m_matriz_jumpeo->getInfoMatrizJumpeoById($id);
        $data['TOTAL']  = count($response);
        $data['LISTA']  = $response;
        $data['error']  = EXIT_SUCCESS;
        $data['msj']    = 'Se atendió exitosamente las solicitudes!!';

        echo json_encode($data);
    }
    public function banProgJum()
    {
        $idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');	   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);


            $data['opciones']   = $result['html'];
            $data['header']     = $this->lib_utils->getHeader();
            $data['matriz']     = $this->searchMatrizJumpeoByFiltersbanPlIntJumJson(null);            


            $this->load->view('vf_matriz_jumpeo/banPlIntJum',$data);        	  
    	 }else{
        	redirect('login','refresh');
	    }  
    }


    public function searchMatrizJumpeoByFiltersbanPlIntJumJson($estado)
    {
        $idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');
        $data = [];
        if ($idEmpresaColabSesion == 6)
        {
            $data['matriz'] = $this->getDataTable($this->m_matriz_jumpeo->lista($estado), 1, $idEmpresaColabSesion);
        }
        else
        {
            $data['matriz'] = $this->getDataTable($this->m_matriz_jumpeo->listaByEmpresaColab($idEmpresaColabSesion, $estado), 1, $idEmpresaColabSesion);
        }

        return $data;
    }


    public function searchMatrizJumpeoByFiltersbanPlIntJum()
    {
        $data = [];
        $estado         = $this->input->post('estado');
        $data           = $this->searchMatrizJumpeoByFiltersbanPlIntJumJson($estado);
        $data['error']  = EXIT_SUCCESS;
        $data['msj']    = 'Se atendió exitosamente las solicitudes!!';

        echo json_encode($data);
    }

    



    public function jumpeoRechazado()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try
        {
        	$idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id 		= $this->input->post('id');


            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }


            $arrayUpdate = array
            (
                "id" 		=> $id,
                "estado" 	=> 'RECHAZADO'
            );

            insertar_logMatrizJumpeo([
                    'matrizjumpeo_id' 	=> $id,
                    'usuario_id' 		=> $idUsuario,
                    'proceso' 			=> 'RECHAZADO',
                    'fecha_registro' 	=> date('Y-m-d H:i:s'),
                    'evidencia' 		=> '',
                    'comentario' 		=> '',
                ]);            

            $data = $this->m_matriz_jumpeo->actualizaMatriz($arrayUpdate);
        }
        catch (Exception $e)
        {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();	
        }
        echo json_encode($data);
    }


    public function jumpeoObservado()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try
        {
        	$idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id 		= $this->input->post('id');
            $comentario = $this->input->post('comentario');


            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }

            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            if($comentario == null || $comentario == ''){
                throw new Exception('Debe ingresar el comentario');
            }


            $arrayUpdate = array
            (
                "id" 					=> $id,
                "comentarioObservado" 	=> $comentario,
                "estado" 				=> 'OBSERVADO'
            );              

            //insertar_logMatrizJumpeo

            insertar_logMatrizJumpeo([
                    'matrizjumpeo_id' 	=> $id,
                    'usuario_id' 		=> $idUsuario,
                    'proceso' 			=> 'OBSERVADO',
                    'fecha_registro' 	=> date('Y-m-d H:i:s'),
                    'evidencia' 		=> '',
                    'comentario' 		=> $comentario,
                ]);
            $data = $this->m_matriz_jumpeo->actualizaMatriz($arrayUpdate);
        }
        catch (Exception $e)
        {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();	
        }
        echo json_encode($data);
    }



    public function saveMatrizJumpeo()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try
        {
        	$idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id 		= $this->input->post('id');
            $itemplanPinOt 		= trim($this->input->post('itemplanPinOt'));
            $eeccpint 			= trim($this->input->post('eeccpint'));
            $workOrderISP 		= trim($this->input->post('workOrderISP'));
            $codigoCMS 			= trim($this->input->post('codigoCMS'));
            $nombre_olt         = trim($this->input->post('nombre_olt'));
            $slot               = trim($this->input->post('slot'));


            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            // pre($codigoCMS);

            if ($codigoCMS == "")
            {
            	$data['msj']      = 'Seleccione CODIGO CMS';
            	 echo json_encode($data);
            	 exit;
            }

            $filtro1 = $this->m_matriz_jumpeo->verificarItemPlan($itemplanPinOt);		
			
			$infoItemplanPINT = $this->m_utils->getPlanObraByItemplan($itemplanPinOt);

            if (count($filtro1) > 0)
            {
            	$data['msj']      = 'El Itemplan Ingresado ya existe';
            	 echo json_encode($data);
            	 exit;
            }

            $filtro2 = $this->m_matriz_jumpeo->verificarItemPlanObra($itemplanPinOt);

            if (count($filtro2) == 0)
            {
            	$data['msj']      = 'El Itemplan Ingresado NO Existe en Plan de Obras';
            	 echo json_encode($data);
            	 exit;
            }



            $arrayUpdate = array
            (
                "id" 				=> $id,
                // "itemplan" 		=> $itemplanPinOt,
                "codigoCMS" 		=> $codigoCMS,
                "itemplanPinOt" 	=> $itemplanPinOt,
                "eeccpint" 			=> $eeccpint,
				"idcontrataPint"    => $infoItemplanPINT['idEmpresaColab'],
                "workOrderISP" 		=> $workOrderISP,
                "estado" 			=> 'PROGRAMADO',
                "nombre_olt"        => mayusculas($nombre_olt),
                "slot"              => mayusculas($slot),
            );
            
            insertar_logMatrizJumpeo([
                    'matrizjumpeo_id' 	=> $id,
                    'usuario_id' 		=> $idUsuario,
                    'proceso' 			=> 'PROGRAMADO',
                    'fecha_registro' 	=> date('Y-m-d H:i:s'),
                    'evidencia' 		=> '',
                    'comentario' 		=> '',
                ]);    

            $data = $this->m_matriz_jumpeo->actualizaMatriz($arrayUpdate);
            $data['error']    = EXIT_SUCCESS;
        }
        catch (Exception $e)
        {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();	
        }
        echo json_encode($data);
    }


    public function saveMatrizJumpeoJum()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try
        {
        	$idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id 		= $this->input->post('id');


            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }


           
            $arrayUpdate = array
            (
                "id" 				=> $id,
                "estado" 			=> 'JUMPEADO',
                "fecha_cierre"      => date('Y-m-d')
            );

            insertar_logMatrizJumpeo([
                    'matrizjumpeo_id' 	=> $id,
                    'usuario_id' 		=> $idUsuario,
                    'proceso' 			=> 'JUMPEADO',
                    'fecha_registro' 	=> date('Y-m-d H:i:s'),
                    'evidencia' 		=> '',
                    'comentario' 		=> '',
                ]);          

            $data = $this->m_matriz_jumpeo->actualizaMatriz($arrayUpdate);
            $data['error']    = EXIT_SUCCESS;
        }
        catch (Exception $e)
        {
        	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();	
        }
        echo json_encode($data);
    }

    public function getDataTable($data, $tipo, $empresaColabId = null){
        $listaFinal = array();
        $idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');  
        if($data!=null){
        	$key = 1;
            foreach($data as $row){ 
                $btnValidar = null;
				$btnPdt = '';
				$btnValidar = $key;
                
                $botones 	= '';
                $evidencia 	= '';
                $log 		= '';

                if ($tipo == 1)
                {
                	if ($row['estado'] == 'NUEVO' && $idEmpresaColabSesion == 6)
	                {


	                	$botones .= ' <a href="javascript:;" data-idx="'.$row["id"].'"  data-slot="'.$row["slot"].'" data-shelf="'.$row["shelf"].'"  data-port="'.$row["port"].'" data-olt="'.$row["nombre_olt"].'"    data-estado="'.$row["estado"].'" class="btn-atender-sol btn btn-primary btn-sm ml-auto waves-effect waves-themed text-white">Programar</a>';

                        if ($empresaColabId != null)
                        {
                            // $botones .= ' <a href="javascript:;" data-idx="'.$row["id"].'" class="btn btn-danger btn-cancelar-sol">Cancelar</a>';
                        }
	                }

	                if ($row['estado'] == 'EJECUTADO' && $idEmpresaColabSesion == 6)
	                {
	                	$botones .= ' <a href="javascript:;" data-idx="'.$row["id"].'" data-estado="'.$row["estado"].'" class="btn-atender-sol3 btn btn-dark btn-sm ml-auto waves-effect waves-themed text-white">Ejecutar</a>';
	                }

                    if ($row['estado'] == 'NUEVO' && $row['idcontrataPext'] == $idEmpresaColabSesion)
                    {
                        $botones .= ' <a href="javascript:;" data-idx="'.$row["id"].'" class="btn btn-danger btn-cancelar-sol">Cancelar</a>';
                    }
                }

                if ($tipo == 2)
                {
                	if ($row['estado'] == 'PROGRAMADO')
	                {
	                	$botones = '<a href="javascript:;" data-idx="'.$row["id"].'" data-estado="'.$row["estado"].'" class="btn-atender-sol2 btn btn-primary btn-sm ml-auto waves-effect waves-themed text-white">Cargar Evidencia</a>';
	                }

	                if ($row['estado'] != 'NUEVO' && $row['evidencia'] != "")
	                {

	                	$evidencia = '<a href="'.base_url().'uploads/matriz_jumpeo/'.$row['evidencia'].'"  target="_new" class="" style="color: #1a73e8;"><i class="fa fa-download"></i></a>';
	                }



	                if ($row['estado'] == 'OBSERVADO')
	                {
	                	$botones .= ' <a href="javascript:;" data-idx="'.$row["id"].'" data-estado="'.$row["estado"].'" class="btn-atender-sol3 btn btn-dark btn-sm ml-auto waves-effect waves-themed text-white">Ejecutar</a>';
	                }


                }

                $log  = ' <a href="javascript:;" data-idx="'.$row["id"].'" data-base="'.base_url().'" class="btn-log btn btn-success btn-sm ml-auto waves-effect waves-themed text-white"><i class="fa fa-list"></i></a>';

                $verTable = ' <a href="javascript:;" data-idx="'.$row["id"].'" data-base="'.base_url().'" class="btn-verAll btn btn-info btn-sm ml-auto waves-effect waves-themed text-white"><i class="fa fa-eye"></i></a>';

                
                if ($row['evidencia'] != "")
                {
                    
                    $evidencia = '<a href="'.base_url().'uploads/matriz_jumpeo/'.$row['evidencia'].'"  target="_new" class="" style="color: #1a73e8;"><i class="fa fa-download"></i></a>';
                }

                $botonesx = $botones.' '.$log.' '.$verTable;
                array_push($listaFinal, array($key, $row['codigoSolicitud'], $row['itemplan'], $row['subProyectoDesc'], $row['nodo'], $row['contrataPext'], $row['itemplanPinOt'], $row['subProyectoDesc2'], $row['eeccpint'], $row['estado'], $evidencia,  $botonesx));




                $key++;
            }      
        }  
        return $listaFinal;
    }

    public function cancelarSolicitudJumpeo()
    {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
        $username   = $this->session->userdata('usernameSession');

        $data               = [];
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;

        try
        {
            $id         = $this->input->post('id');

            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

            $arrayUpdate = array
            (
                "id"            => $id,
                "estado"        => 'CANCELADO',
                'evidencia'     => '',
                'comentarioObservado'    => ''
            );              

            insertar_logMatrizJumpeo([
                    'matrizjumpeo_id'   => $id,
                    'usuario_id'        => $idUsuario,
                    'proceso'           => 'CANCELADO',
                    'fecha_registro'    => date('Y-m-d H:i:s')
                ]); 

            $data = $this->m_matriz_jumpeo->actualizaMatriz($arrayUpdate);
            $data['error']      = EXIT_SUCCESS;


        } catch (Exception $e)
        {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();    
        }
        echo json_encode($data);
    }
    public function getLogByMatrizJumpeo()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;

    	try
    	{
    		$idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id 		= $this->input->post('id');


            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

	    	
            $resultado = $this->m_matriz_jumpeo->obtenerLogMatrizJumpeoById($id);
            $data['TOTAL'] 		= count($resultado);
            $data['lista'] 		= $resultado;
            $data['error']    	= EXIT_SUCCESS;


    	} catch (Exception $e)
    	{
    		$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();	
    	}
    	echo json_encode($data);
    }


    public function saveEvidenciaMatrizJumpeo()
    {

    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;

    	try
    	{
    		$idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $id 		= $this->input->post('id');
            $itemplanPinOt 		= $this->input->post('itemplanPinOt');


            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
            if($id == null || $id == ''){
                throw new Exception('Hubo un error y no se cargaron los datos, refresque la pagina!!');
            }

	    	if (!isset($_FILES['evidencia']))
	    	{
	    		$data['msj'] = 'Debe cargar archivo de evidencia';
	    		echo json_encode($data);
	    		exit;
	    	}



			$nombreDelArchivo 	= $_FILES['evidencia']['name'];
			$extension 			= pathinfo($nombreDelArchivo, PATHINFO_EXTENSION);

			if (strtolower($extension) != 'zip')
			{
				$data['msj'] = 'El archivo cargado debe ser un archivo zip';
	    		echo json_encode($data);
	    		exit;
			}

			$nombreFinalArchivo = date("Y_m_d_His_").$nombreDelArchivo;
			$rutaFinalArchivo 	= "uploads/matriz_jumpeo/".$nombreFinalArchivo;
			$nombreArchivoTemp 	= $_FILES['evidencia']['tmp_name'];
			if (!move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {
				$data['msj'] = 'No se pudo subir el archivo: ' . $nombreFinalArchivo . ' !!';
			}


			$arrayUpdate = array
            (
                "id" 			=> $id,
                "estado" 		=> 'EJECUTADO',
                "evidencia" 	=> $nombreFinalArchivo
            );              

            insertar_logMatrizJumpeo([
                    'matrizjumpeo_id' 	=> $id,
                    'usuario_id' 		=> $idUsuario,
                    'proceso' 			=> 'EJECUTADO',
                    'fecha_registro' 	=> date('Y-m-d H:i:s'),
                    'evidencia' 		=> $nombreFinalArchivo,
                    'comentario' 		=> '',
                ]);    
            $data = $this->m_matriz_jumpeo->actualizaMatriz($arrayUpdate);



    	} catch (Exception $e)
    	{
    		$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();	
    	}
    	echo json_encode($data);

    }



    public function bangestPINT()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');
	    if($idUsuario != null){           
            $permisos   = $this->session->userdata('permisosArbolPan');	   
            $result     = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);


            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['matriz'] = $this->getDataTable($this->m_matriz_jumpeo->lista(), 1);
            $this->load->view('vf_matriz_jumpeo/banPlIntJum',$data);        	  
    	 }else{
        	redirect('login','refresh');
	    } 
    }

    public function getEECCByItemPlan()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
    	$username 	= $this->session->userdata('usernameSession');

    	$data 				= [];
    	$data['error']    = EXIT_ERROR;
        $data['msj']      = null;

    	try
    	{
    		$idUsuario  = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $itemplan 	= trim($this->input->post('itemplan'));


            if($idUsuario == null){
                throw new Exception('La sesion de usuario a expirado, ingrese nuevamente porfavor!!');
            }
          

	    	$resultado = $this->m_matriz_jumpeo->obtenerEECCByItemPlan($itemplan);
	    	$data['TOTAL'] = count($resultado);
	    	$data['LISTA'] = ($resultado);
            $data['CMS'] = $this->m_matriz_jumpeo->obtenercmsEECCByItemPlan($itemplan);

			



    	} catch (Exception $e)
    	{
    		$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();	
    	}
    	echo json_encode($data);
    }


    public function getLoadMatJum()
    {
    	$idUsuario  = $this->session->userdata('idPersonaSessionPan');
        $username   = $this->session->userdata('usernameSession');
        if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');     
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);
            $dataFecha = $this->m_utils->obtenerUltimaFechaArchivoMatrizJumpeo(1);
            $fechaRegistro = '';
            if (count($dataFecha) > 0)
            {
                $fechaRegistro = $dataFecha[0]['fecha_registro'];
            }


            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['fechaRegistro'] = $fechaRegistro;
            $this->load->view('vf_matriz_jumpeo/uploads',$data);
         }else{
            redirect('login','refresh');
        }  
    }
  
    public function getLoadPinPex()
    {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
        $username   = $this->session->userdata('usernameSession');
        if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');     
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);
            $dataFecha = $this->m_utils->obtenerUltimaFechaArchivoMatrizJumpeo(2);
            $fechaRegistro = '';
            if (count($dataFecha) > 0)
            {
                $fechaRegistro = $dataFecha[0]['fecha_registro'];
            }

            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['fechaRegistro'] = $fechaRegistro;
            $this->load->view('vf_matriz_jumpeo/uploads-pin-pex',$data);
         }else{
            redirect('login','refresh');
        }  
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
            // $hoja->setCellValueByColumnAndRow($col, $row, 'N°');
            // $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'GESTOR');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FASE');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ETAPAS');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NODO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CABLE');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'HILO');
            $col++;
            // $hoja->setCellValueByColumnAndRow($col, $row, 'ARMARIO');
            // $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
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
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_PIXPEX__' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }


    public function procesarFileMatrizJumpeo()
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
                    $gestor = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $gestor = _removeEnterYTabs(trim(utf8_encode(utf8_decode($gestor)),'?'));
                    $col++;
                    $fase = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $fase = _removeEnterYTabs(trim(utf8_encode(utf8_decode($fase)),'?'));
                    $col++;
                    $etapas = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $etapas = _removeEnterYTabs(trim(utf8_encode(utf8_decode($etapas)),'?'));
                    $col++;
                    $itemplan = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $itemplan = _removeEnterYTabs(trim(utf8_encode(utf8_decode($itemplan)),'?'));
                    $col++; 
                                
                    $nodo = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $nodo = _removeEnterYTabs(trim(utf8_encode(utf8_decode($nodo)),'?'));
                    $col++;

                    $cable = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $cable = _removeEnterYTabs(trim(utf8_encode(utf8_decode($cable)),'?'));
                    $col++;

                    $hilo = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $hilo = _removeEnterYTabs(trim(utf8_encode(utf8_decode($hilo)),'?'));
                    $col++;

                    // $armario = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    // $armario = _removeEnterYTabs(trim(utf8_encode(utf8_decode($armario)),'?'));
                    // $col++;

                    $distrito = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $distrito = _removeEnterYTabs(trim(utf8_encode(utf8_decode($distrito)),'?'));
                    $col++;
                    $col++;
                    $eecc   = '';


                    $dataArray['observacion'] = '';


                    $verifyItemPLan 	= $this->m_matriz_jumpeo->verifyItemPanInPlanObra($itemplan);
             		
                    if (count($verifyItemPLan) == 0)
             		{
             			$dataArray['observacion'] .= 'El itemplan no existe<br>';
             		}

                    $empresaColaboradora 	= $this->m_matriz_jumpeo->obtenerEECCByItemPlan($itemplan);
             		// $codNodoArr 			= $this->m_matriz_jumpeo->obtenerCodNodo($itemplan);
                    $codNodoArr             = $this->m_matriz_jumpeo->obtenerInfoNodo($itemplan, $nodo);



					$dataArray['nodo'] 			= '';
					$dataArray['proyecto'] 		= '';
					$dataArray['distrito']  	= '';
					$dataArray['contrataPext']  = '';
					$dataArray['odf']			= '';
					$dataArray['bandeja']		= '';
					$dataArray['modulo']		= '';
					$dataArray['divicau']		= '';
					$dataArray['secundario']	= '';

					$dataArray['nombre_olt']	= '';
					$dataArray['slot']	= '';
					$dataArray['port']	= '';




             		if (count($codNodoArr) == 0)
             		{
             			$dataArray['observacion'] .= 'El Nodo Ingresado con el Itemplan, no existe<br>';
             		}

             		if (count($codNodoArr) > 0)
             		{
             			$codNodo 					= $codNodoArr[0]['codNodo'];
             			$dataArray['nodo'] 			= $codNodoArr[0]['codNodo'];
             			$cuentaPares 				= $cable.', '.$hilo;
             			$dataArray['proyecto'] 		= $codNodoArr[0]['subProyectoDesc'];
                    	$dataArray['distrito']  	= $codNodoArr[0]['distrito'];
                    	$dataArray['contrataPext']  = $codNodoArr[0]['empresaColabDesc'];
                        $dataArray['idcontrataPext']= $codNodoArr[0]['idEmpresaColab'];


             			$matrizPinPex 	= $this->m_matriz_jumpeo->obtenerMatrizPinPext($codNodo, $cuentaPares);

             			if (count($matrizPinPex)> 0)
             			{
             				$dataArray['odf']		= $matrizPinPex[0]['odf'];
             				$dataArray['bandeja']		= $matrizPinPex[0]['bandeja'];
             				$dataArray['modulo']		= $matrizPinPex[0]['modulo'];
             				$dataArray['divicau']		= $matrizPinPex[0]['divicau'];
             				$dataArray['secundario']	= $matrizPinPex[0]['cable_secundario'];
             				$dataArray['nombre_olt']	= $matrizPinPex[0]['olt'];
							$dataArray['slot']			= $matrizPinPex[0]['slot'];
							$dataArray['port']			= $matrizPinPex[0]['puerto'];
                            $puerto_olt                 = trim($matrizPinPex[0]['puerto_olt']);

                            if (!empty(trim($matrizPinPex[0]['puerto_olt'])))
                            {
                                $dataArray['observacion'] .= 'El Nodo-cable-hilo ya esta utilizado<br>';
                            }

                            $llave   = $this->m_matriz_jumpeo->obtenerMatrizJumpeoLLave($codNodo, $cable, $hilo);
                            if (count($llave) > 0 && $llave[0]['estado']!= 'CANCELADO')
                            {
                                $dataArray['observacion'] .= 'La solicitud ya fue ingresada y esta en estado: '.$llave[0]['estado'].'<br>';
                            }

             			}
             			else
             			{
             				$dataArray['observacion'] .= 'El número de cable/Puerto no existe<br>';
             			}


                        $idEmpresaColabSesion   = $this->session->userdata('idEmpresaColabSesion');

                        if ($idEmpresaColabSesion != 6)
                        {
                            if ($codNodoArr[0]['idEmpresaColab'] != $idEmpresaColabSesion)
                            {
                                $dataArray['observacion'] .= 'El itemplan debe estar asociado a su Empresa Colaboradora<br>';
                            }
                        }
             		}

                    //
                    
                    

                    $arrayParticleCode[]			= $itemplan.''.@$puerto;
                    $dataArray['itemplanPuerto']    = $itemplan.''.@$puerto;
                    $dataArray['gestor']            = $gestor;
                    $dataArray['fase']            	= $fase;
                    $dataArray['etapas']            = $etapas;
                    $dataArray['itemplan']          = $itemplan;
                    $dataArray['puerto']            = @$puerto;
                    $dataArray['cable']             = $cable;
                    $dataArray['hilo']              = $hilo;
                    $dataArray['armario'] 			= @$armario;
                    
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


    function   procesarFileMatrizPinPex()
    {
        // pre($_FILES);
        

        $data ['error']= EXIT_ERROR;
        $data['msj'] = null;
        try{

            $uploaddir = 'uploads/matriz_pin_pex/'; //ruta final del file
            $pathFinal = $uploaddir.basename($_FILES["file"]["tmp_name"]);
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $this->db->query("TRUNCATE TABLE matriz_pin_pex_base");
            $this->db->trans_commit();

            if (move_uploaded_file($_FILES['file']['tmp_name'], $pathFinal))
            { 
                $this->db->trans_begin();
                if ($this->db->trans_status() === TRUE) {

                    $string_1 = '\n';
                    $string_2 = '"';

                    $this->db->query("LOAD DATA LOCAL INFILE '".$pathFinal."' INTO TABLE matriz_pin_pex_base
                                        CHARACTER SET latin1
                                        FIELDS TERMINATED BY ',' 
                                        OPTIONALLY ENCLOSED BY '".$string_2."'
                                        LINES TERMINATED BY '".$string_1."'
                                        IGNORE 1 LINES
                                        (`departamento`, 
                                        `sitio`, `nodo`, `olt`, `fabricante`, `modelo`, `olt_esn`, `olt_slot`, `puerto_olt`, `nombre_sistema`, `olt_descripcion`, `olt_comentario`, `dgo`, `piso`, `sala`, `modulo`, `bandeja`, `llave_isp_osp`, `puerto`, `ot_n1`, `ot_n2`, `proyecto`, `descripcionProyecto`, `estadoOT`, `tipoSitio`, `cuentaPares`, `idDivisorN1`, `divisorN1`, `idTerminalN1`, `terminalN1`, `funcion`, `tipo`, `puertoN1`, `paTerminalN1`, `coordenadaTerminalN1`, `CableSecundario`, `hiloSecundario`, `cableFinal`, `hiloFinal`, `idDivisorN2`, `divisorN2`, `idTerminalN2`, `terminalN2`, `distanciaCentral`, `distanciaEstimada`, `paTerminalN2`, `coordenadaTerminalN2`, `codPaTerminalN2`, `CoordernadasSiteHolder`, `observacion1TN2`, `observacion2TN2`);");
                     if ($this->db->trans_status() === TRUE)
                     {
                        $this->db->trans_commit();

                        insertar_logMatrizJumpeoFile([
                            'matrizjumpeo_id'   => 0,
                            'usuario_id'        => $idUsuario,
                            'fecha_registro'    => date('Y-m-d H:i:s'),
                            'tipo'              => 2,
                        ]);
                        
                     }

                } 
                   
            }

            $data ['error']= EXIT_SUCCESS;
            $data['msj'] = 'La Carga Masiva de Matriz PIN PEX ha sido cargado correctamente';

        }catch(Exception $e)
        {

            $data['msj'] = $e->getMessage();
        }

        echo json_encode($data);
    }

    public function depDupliMatPinPex()
    {
        $data  = $this->m_matriz_jumpeo->getMatrizPinPexBase();
        if (count($data) > 0)
        {
            $data  = $this->m_matriz_jumpeo->getMatrizPinPexBaseInsert();
        }

    }


    function tablaRegistro($arrayTabla) {
        $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
                            <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>                           
                            <th style="text-align: center; vertical-align: middle;">GESTOR</th>                           
                            <th style="text-align: center; vertical-align: middle;">FASE</th>
                            <th style="text-align: center; vertical-align: middle;">ETAPAS</th>
                            <th style="text-align: center; vertical-align: middle;">ITEMPLAN</th>
                            <th style="text-align: center; vertical-align: middle;">CABLE</th>
                            <th style="text-align: center; vertical-align: middle;">HILO</th>
                            <th style="text-align: center; vertical-align: middle;">NODO</th>
                            <th style="text-align: center; vertical-align: middle;">PROYECTO</th>
                            <th style="text-align: center; vertical-align: middle;">DISTRITO</th>
                            <th style="text-align: center; vertical-align: middle;">CONTRATA PEXT</th>
                            <th style="text-align: center; vertical-align: middle;">ODF</th>
                            <th style="text-align: center; vertical-align: middle;">BANDEJA</th>
                            <th style="text-align: center; vertical-align: middle;">MODULO</th>
                            <th style="text-align: center; vertical-align: middle;">DIVICAU</th>
                            <th style="text-align: center; vertical-align: middle;">SECUNDARIO</th>
                            <th style="text-align: center; vertical-align: middle;">NOMBRE OLT</th>
                            <th style="text-align: center; vertical-align: middle;">SLOT</th>
                            <th style="text-align: center; vertical-align: middle;">PUERTO</th>
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
                                aria-expanded="false" data-itemplanPuerto="'.$row['itemplanPuerto'].'"
                                onclick="deleteItemErroneo(this);"><i class="fal fa-trash"></i>
                            </a>';
            } else {
                    if(!in_array($row['itemplanPuerto'],$arrayParticleCode)){

                        $arrayParticleCode[] = $row['itemplan'].''.$row['puerto'];
                        $htmlColorFila = '';
                        $btnDelete = '';
                        $arrayFinal[] = array(
                            'itemplanPuerto'        => $row['itemplan'].''.$row['puerto'],
                            'observacion'           => $row['observacion'],
                            'gestor'                => $row['gestor'],
                            'fase'                  => $row['fase'],
                            'etapas'                => $row['etapas'],
                            'itemplan'              => $row['itemplan'],
                            'puerto'                => $row['puerto'],
                            'cable'                 => $row['cable'],
                            'hilo'                  => $row['hilo'],
                            'armario'            	=> $row['armario'],

                            'nodo'            		=> $row['nodo'],
                            'proyecto'            	=> $row['proyecto'],
                            'distrito'            	=> $row['distrito'],
                            'contrataPext'          => $row['contrataPext'],
                            'odf'            		=> $row['odf'],
                            'bandeja'            	=> $row['bandeja'],
                            'modulo'            	=> $row['modulo'],
                            'divicau'            	=> $row['divicau'],
                            'secundario'            => $row['secundario'],
                            'nombre_olt'            => $row['nombre_olt'],
                            'slot'            		=> $row['slot'],
                            'port'            		=> $row['port'],
                            'idcontrataPext'        => $row['idcontrataPext'],
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
                            <td>'.$row['gestor'].'</td>
                            <td>'.$row['fase'].'</td>
                            <td>'.$row['etapas'].'</td>
                            <td>'.$row['itemplan'].'</td>
                            <td>'.$row['cable'].'</td>
                            <td>'.$row['hilo'].'</td>
                            <td>'.$row['nodo'].'</td>
                            <td>'.$row['proyecto'].'</td>
                            <td>'.$row['distrito'].'</td>
                            <td>'.$row['contrataPext'].'</td>
                            <td>'.$row['odf'].'</td>
                            <td>'.$row['bandeja'].'</td>
                            <td>'.$row['modulo'].'</td>
                            <td>'.$row['divicau'].'</td>
                            <td>'.$row['secundario'].'</td>

                            <td>'.$row['nombre_olt'].'</td>
                            <td>'.$row['slot'].'</td>
                            <td>'.$row['port'].'</td>
                        </tr>';
            $count++;
        }
        $html .= '</tbody>
            </table>';

        return array($html, $ctnVal, $arrayFinal, $count);
    }




    function tablaRegistroPinPex($arrayTabla) {
        $html = '<table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
                            <th style="text-align: center; vertical-align: middle;">OBSERVACION</th>                           
                            <th style="text-align: center; vertical-align: middle;">GESTOR</th>                           
                            <th style="text-align: center; vertical-align: middle;">DEPARTAMENTO</th>
                            <th style="text-align: center; vertical-align: middle;">SITIO</th>
                            <th style="text-align: center; vertical-align: middle;">NODO</th>
                            <th style="text-align: center; vertical-align: middle;">OLT</th>
                            <th style="text-align: center; vertical-align: middle;">FABRICANTE</th>
                            <th style="text-align: center; vertical-align: middle;">MODELO</th>
                            <th style="text-align: center; vertical-align: middle;">OLT ESN</th>
                            <th style="text-align: center; vertical-align: middle;">OLT  SLOT</th>
                            <th style="text-align: center; vertical-align: middle;">PUERTO OLT</th>
                            <th style="text-align: center; vertical-align: middle;">NOMBRE SISTEMA</th>
                            <th style="text-align: center; vertical-align: middle;">OLT DESCRIPCION</th>
                            <th style="text-align: center; vertical-align: middle;">OLT COMENTARIO</th>
                            <th style="text-align: center; vertical-align: middle;">DGO</th>
                            <th style="text-align: center; vertical-align: middle;">PISO</th>
                            <th style="text-align: center; vertical-align: middle;">SALA</th>
                            <th style="text-align: center; vertical-align: middle;">MODULO</th>
                            <th style="text-align: center; vertical-align: middle;">BANDEJA</th>
                            <th style="text-align: center; vertical-align: middle;">LLAVE ISP OSP</th>
                            <th style="text-align: center; vertical-align: middle;">PUERTO</th>
                            <th style="text-align: center; vertical-align: middle;">OT N1</th>
                            <th style="text-align: center; vertical-align: middle;">OT N2</th>
                            <th style="text-align: center; vertical-align: middle;">PROYECTO</th>
                            <th style="text-align: center; vertical-align: middle;">DESCRIPCION PROYECTO</th>
                            <th style="text-align: center; vertical-align: middle;">ESTADO OT</th>
                            <th style="text-align: center; vertical-align: middle;">TIPO SITIO</th>
                            <th style="text-align: center; vertical-align: middle;">CUENTA PARES</th>
                            <th style="text-align: center; vertical-align: middle;">ID DIVISOR N1</th>
                            <th style="text-align: center; vertical-align: middle;">DIVISOR N1</th>
                            <th style="text-align: center; vertical-align: middle;">ID TERMINAL N1</th>
                            <th style="text-align: center; vertical-align: middle;">TERMINAL N1</th>
                            <th style="text-align: center; vertical-align: middle;">FUNCION</th>
                            <th style="text-align: center; vertical-align: middle;">TIPO</th>
                            <th style="text-align: center; vertical-align: middle;">PUERTO N1</th>
                            <th style="text-align: center; vertical-align: middle;">PA TERMINAL N1</th>
                            <th style="text-align: center; vertical-align: middle;">COORDENADA TERMINAL N1</th>
                            <th style="text-align: center; vertical-align: middle;">CABLE SECUNDARIO</th>
                            <th style="text-align: center; vertical-align: middle;">HILO SECUNDARIO</th>
                            <th style="text-align: center; vertical-align: middle;">CABLE FINAL</th>
                            <th style="text-align: center; vertical-align: middle;">HILO FINAL</th>
                            <th style="text-align: center; vertical-align: middle;">ID DIVISOR N2</th>
                            <th style="text-align: center; vertical-align: middle;">DIVISOR N2</th>
                            <th style="text-align: center; vertical-align: middle;">ID TERMINAL N2</th>
                            <th style="text-align: center; vertical-align: middle;">TERMINAL N2</th>
                            <th style="text-align: center; vertical-align: middle;">DISTANCIA A LA CENTRAL</th>
                            <th style="text-align: center; vertical-align: middle;">PA TERMINAL N2</th>
                            <th style="text-align: center; vertical-align: middle;">COORDENADA TERMINAL N2</th>
                            <th style="text-align: center; vertical-align: middle;">COD PA TERMINAL N2</th>
                            <th style="text-align: center; vertical-align: middle;">COORDENADAS SITEHOLDER</th>
                            <th style="text-align: center; vertical-align: middle;">OBSERVACION 1 TN2</th>
                            <th style="text-align: center; vertical-align: middle;">OBSERVACION 2 TN2</th>
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
                                aria-expanded="false" data-itemplanPuerto="'.$row['itemplanPuerto'].'"
                                onclick="deleteItemErroneo(this);"><i class="fal fa-trash"></i>
                            </a>';
            } else {
                    if(!in_array($row['itemplanPuerto'],$arrayParticleCode)){

                        $arrayParticleCode[] = $row['itemplan'].''.$row['puerto'];
                        $htmlColorFila = '';
                        $btnDelete = '';
                        $arrayFinal[] = array(
                            'itemplanPuerto'            => $row['itemplan'].''.$row['puerto'],
                            'departamento'              => $row['departamento'],
                            'sitio'                     => $row['sitio'],
                            'nodo'                      => $row['nodo'],
                            'olt'                       => $row['olt'],
                            'fabricante'                => $row['fabricante'],
                            'modelo'                    => $row['modelo'],
                            'olt_esn'                   => $row['olt_esn'],
                            'olt_slot'                  => $row['olt_slot'],
                            'puerto_olt'                => $row['puerto_olt'],
                            'nombre_sistema'            => $row['nombre_sistema'],
                            'olt_descripcion'           => $row['olt_descripcion'],
                            'olt_comentario'            => $row['olt_comentario'],
                            'dgo'                       => $row['dgo'],
                            'piso'                      => $row['piso'],
                            'sala'                      => $row['sala'],
                            'modulo'                    => $row['modulo'],
                            'bandeja'                   => $row['bandeja'],
                            'llave_isp_osp'             => $row['llave_isp_osp'],
                            'puerto'                    => $row['puerto'],
                            'ot_n1'                     => $row['ot_n1'],
                            'ot_n2'                     => $row['ot_n2'],
                            'proyecto'                  => $row['proyecto'],
                            'descripcionProyecto'       => $row['descripcionProyecto'],
                            'estadoOT'                  => $row['estadoOT'],
                            'tipoSitio'                 => $row['tipoSitio'],
                            'cuentaPares'               => $row['cuentaPares'],
                            'idDivisorN1'               => $row['idDivisorN1'],
                            'divisorN1'                 => $row['divisorN1'],
                            'idterminalN1'              => $row['idterminalN1'],
                            'terminalN1'                => $row['terminalN1'],
                            'funcion'                   => $row['funcion'],
                            'tipo'                      => $row['tipo'],
                            'puertoN1'                  => $row['puertoN1'],
                            'paTerminalN1'              => $row['paTerminalN1'],
                            'coordenadaTerminalN1'      => $row['coordenadaTerminalN1'],
                            'CableSecundario'           => $row['CableSecundario'],
                            'hiloSecundario'            => $row['hiloSecundario'],
                            'cableFinal'                => $row['cableFinal'],
                            'hiloFinal'                 => $row['hiloFinal'],
                            'idDivisorN2'               => $row['idDivisorN2'],
                            'divisorN2'                 => $row['divisorN2'],
                            'idTerminalN2'              => $row['idTerminalN2'],
                            'terminalN2'                => $row['terminalN2'],
                            'distanciaCentral'          => $row['distanciaCentral'],
                            'distanciaEstimada'         => $row['distanciaEstimada'],
                            'paTerminalN2'              => $row['paTerminalN2'],
                            'coordenadaTerminalN2'      => $row['coordenadaTerminalN2'],
                            'codPaTerminalN2'           => $row['codPaTerminalN2'],
                            'CoordernadasSiteHolder'    => $row['CoordernadasSiteHolder'],
                            'observacion1TN2'           => $row['observacion1TN2'],
                            'observacion2TN2'           => $row['observacion2TN2'],
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
                            <td>'.$row['departamento'].'</td>
                            <td>'.$row['sitio'].'</td>
                            <td>'.$row['nodo'].'</td>
                            <td>'.$row['olt'].'</td>
                            <td>'.$row['fabricante'].'</td>
                            <td>'.$row['modelo'].'</td>
                            <td>'.$row['olt_esn'].'</td>
                            <td>'.$row['olt_slot'].'</td>
                            <td>'.$row['puerto_olt'].'</td>
                            <td>'.$row['nombre_sistema'] .'</td>
                            <td>'.$row['olt_descripcion'].'</td>
                            <td>'.$row['olt_comentario'] .'</td>
                            <td>'.$row['dgo'].'</td>
                            <td>'.$row['piso'].'</td>
                            <td>'.$row['sala'].'</td>
                            <td>'.$row['modulo'].'</td>
                            <td>'.$row['bandeja'].'</td>
                            <td>'.$row['llave_isp_osp'].'</td>
                            <td>'.$row['puerto'].'</td>
                            <td>'.$row['ot_n1'].'</td>
                            <td>'.$row['ot_n2'].'</td>
                            <td>'.$row['proyecto'].'</td>
                            <td>'.$row['descripcionProyecto'].'</td>
                            <td>'.$row['estadoOT'].'</td>
                            <td>'.$row['tipoSitio'].'</td>
                            <td>'.$row['cuentaPares'].'</td>
                            <td>'.$row['idDivisorN1'].'</td>
                            <td>'.$row['divisorN1'].'</td>
                            <td>'.$row['idterminalN1'].'</td>
                            <td>'.$row['terminalN1']  .'</td>
                            <td>'.$row['funcion'].'</td>
                            <td>'.$row['tipo'].'</td>
                            <td>'.$row['puertoN1'].'</td>
                            <td>'.$row['paTerminalN1'].'</td>
                            <td>'.$row['coordenadaTerminalN1'].'</td>
                            <td>'.$row['CableSecundario'].'</td>
                            <td>'.$row['hiloSecundario'].'</td>
                            <td>'.$row['cableFinal'].'</td>
                            <td>'.$row['hiloFinal'].'</td>
                            <td>'.$row['idDivisorN2'].'</td>
                            <td>'.$row['divisorN2'].'</td>
                            <td>'.$row['idTerminalN2'].'</td>
                            <td>'.$row['terminalN2'].'</td>
                            <td>'.$row['distanciaCentral'].'</td>
                            <td>'.$row['distanciaEstimada'].'</td>
                            <td>'.$row['paTerminalN2'].'</td>
                            <td>'.$row['coordenadaTerminalN2'].'</td>
                            <td>'.$row['codPaTerminalN2'].'</td>
                            <td>'.$row['CoordernadasSiteHolder'].'</td>
                            <td>'.$row['observacion1TN2'].'</td>
                            <td>'.$row['observacion2TN2'].'</td>
                        </tr>';
            $count++;
        }
        $html .= '</tbody>
            </table>';

        return array($html, $ctnVal, $arrayFinal, $count);
    }


    public function cargarMasivoMatrizJumpeo()
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
                $indice = 1;
                foreach ($arrayDataFile as $datos)
                {
                    // $datos = convert_object_to_array($datos);
                    $datos = (array)$datos;
                    $gestor  	= @$datos['gestor'];              
                    $fase       = @$datos['fase'];
                    $etapas		= @$datos['etapas'];
                    $itemplan   = @$datos['itemplan'];                        
                    $puerto     = @$datos['puerto'];                        
                    $cable      = @$datos['cable'];                        
                    $hilo      	= @$datos['hilo'];                        
                    $armario    = @$datos['armario'];                        
                    $nodo    	= @$datos['nodo'];                        
                    $distrito   = @$datos['distrito'];

                    $proyecto   	= @$datos['proyecto'];                        
                    $contrataPext   = @$datos['contrataPext'];                        
                    $idcontrataPext = @$datos['idcontrataPext'];                        
                    $odf   			= @$datos['odf'];                        
                    $bandeja   		= @$datos['bandeja'];                        
                    $modulo   		= @$datos['modulo'];                        
                    $divicau   		= @$datos['divicau'];                        
                    $secundario   	= @$datos['secundario'];   

                    $nombre_olt   	= @$datos['nombre_olt'];                      
                    $slot   		= @$datos['slot'];                      
                    $port   		= @$datos['port'];                      
             	
             		$codigoSolicitud 		= date('Y-m-d').'-'.$indice.'-'.time();
             		


                    $dataArray = array();
             		
                    
                    $dataArray['codigoSolicitud']	= $codigoSolicitud;
                    //$dataArray['proyecto']			= trim($proyecto);
                    $dataArray['gestor']			= trim($gestor);
                    $dataArray['fase']      		= trim($fase);
                    $dataArray['etapa']         	= trim($etapas);
                    $dataArray['itemplan']     		= trim($itemplan);
                    $dataArray['puertoCto']       	= trim($puerto);
                    $dataArray['cable']        		= trim($cable);
                    $dataArray['hilo']        		= trim($hilo);
                    $dataArray['armario']           = trim($armario);
                    $dataArray['nodo']           	= trim($nodo);
                    $dataArray['fechaSolicitud']    = date('Y-m-d H:i:s');
                    $dataArray['empresaColab'] 		= 'PANGEACO';



					$dataArray['proyecto'] 		= trim($proyecto);
					$dataArray['distrito']  	= trim($distrito);
					$dataArray['contrataPext']  = trim($contrataPext);
                    $dataArray['idcontrataPext']= trim($idcontrataPext);


                    $dataArray['odf']			= trim($odf);
     				$dataArray['bandeja']		= trim($bandeja);
     				$dataArray['modulo']		= trim($modulo);
     				$dataArray['divicau']		= trim($divicau);
     				$dataArray['secundario']	= trim($secundario);

     				$dataArray['nombre_olt']	= trim($nombre_olt);
     				$dataArray['slot']	= trim($slot);
     				$dataArray['port']	= trim($port);
     				$dataArray['shelf']	= trim("1");

                    
                    $data = $this->m_matriz_jumpeo->insertTabla($dataArray);
                    // pre($data);
                    // $idx = $this->db->insert_id();;
     				insertar_logMatrizJumpeo([
	                    'matrizjumpeo_id' 	=> $data['insert_id'],
	                    'usuario_id' 		=> $idUsuario,
	                    'proceso' 			=> 'NUEVO',
	                    'fecha_registro' 	=> date('Y-m-d H:i:s'),
	                    'evidencia' 		=> '',
	                    'comentario' 		=> '',
	                ]);

                    insertar_logMatrizJumpeoFile([
                        'matrizjumpeo_id'   => $data['insert_id'],
                        'usuario_id'        => $idUsuario,
                        'fecha_registro'    => date('Y-m-d H:i:s'),
                        'tipo'              => 1,
                    ]);
                    // $arrayTabla []= $dataArray;



                    $indice++;

                }

                // $data['error'] = 0;

                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
             	$data['insert_id'] = null;
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();
 
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function searchMatrizJumpeoByFilters()
    {
    	$data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');

            $this->db->trans_begin();

            if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }

            $codigo_solicitud 	     = $this->input->post('codigo_solicitud');
            $estado 			     = $this->input->post('estado');
            $idEmpresaColabSesion    = $this->session->userdata('idEmpresaColabSesion');
            $empresaColabDesc        = $this->session->userdata('empresaColabDesc');

            if ($estado == "ALL")
            {
            	
            	$data['matriz'] = $this->getDataTable($this->m_matriz_jumpeo->getMatrizByFilterEstado($estado, $idEmpresaColabSesion, $empresaColabDesc), 1);
            }

            if ($codigo_solicitud != "" && $estado != "ALL")
            {
            	
            	$data['matriz'] = $this->getDataTable($this->m_matriz_jumpeo->getMatrizByFilter($codigo_solicitud, $estado), 2);
            }

            if ($codigo_solicitud == "" && $estado != "ALL")
            {
            	
            	$data['matriz'] = $this->getDataTable($this->m_matriz_jumpeo->getMatrizByFilterEstado($estado, $idEmpresaColabSesion, $empresaColabDesc), 2);
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


    public function getExcelFmtAtenMatrizJumpeoDiseno()
    {
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'AÑO PROYECTO');
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'NODO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIPO DIVICAU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NOMBRE TROBA');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO DISEÑO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'UIP DISEÑO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DEPARTAMENTO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'PROVINCIA');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE TERMINO');
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

            $hoja->getStyle('A1:O1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
            ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $data['error'] = EXIT_SUCCESS;
            $data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento_Diseño_' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }

    public function getExcelFmtAtenMatrizJumpeoEconomico()
    {
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CONFIRMACION PPTO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'STATUS DE OC');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'N° PEP');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'N° OC');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'GENERACION VR');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CERTIFICACION OC');
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

            $hoja->getStyle('A1:G1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
            ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $data['error'] = EXIT_SUCCESS;
            $data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento_Economico_' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }


    public function getExcelFmtAtenMatrizJumpeoLicencia()
    {
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA PRESENTACION');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE INICIO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'STATUS LICENCIA');
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

            $hoja->getStyle('A1:C1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
            ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $data['error'] = EXIT_SUCCESS;
            $data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento_Licencia_' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }

    public function getExcelFmtAtenMatrizJumpeoLogistica()
    {
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CONFIRMACION ENTREGA');
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

            $hoja->getStyle('A1:C1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
            ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $data['error'] = EXIT_SUCCESS;
            $data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento_Logistica_' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }



    public function getExcelFmtAtenMatrizJumpeoCensado()
    {
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA CENSADO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD UIP');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO DE CENSO');
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

            $hoja->getStyle('A1:E1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
            ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $data['error'] = EXIT_SUCCESS;
            $data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento_Censado_' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }

    public function getExcelFmtAtenMatrizJumpeoDespliegue()
    {
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CONFIRMACION PART DESPLIEGUE');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA INSTALACION ODF');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE INICIO CONSTRUCCION');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA PROYECTADA ENTREGA');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ENTREGA DIVICAU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'STATUS DESPLIEGUE');
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
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento_Despliegue_' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }


    public function getExcelFmtAtenMatrizJumpeoHGU()
    {
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE PRUEBA DE HGU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'AVANCE DE PRUEBAS HGU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CONFIRMACION DE EJECUCION DE PRUEBAS HGU');
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

            $hoja->getStyle('A1:E1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
            ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $data['error'] = EXIT_SUCCESS;
            $data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento_HGU_' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }


    public function getExcelFmtAtenMatrizJumpeoStatus()
    {
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'STATUS DETALLADO');
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

            $hoja->getStyle('A1:E1')->applyFromArray($estiloTituloColumnas);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
            ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $data['error'] = EXIT_SUCCESS;
            $data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'Formato_Carga_Matriz_Seguimiento_Status_' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }
}

?>