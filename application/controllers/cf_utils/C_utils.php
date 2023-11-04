<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_utils extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
		$this->load->model('mf_servicios/m_integracion_sirope');
		$this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
		$this->load->model('mf_orden_compra/m_bandeja_solicitud_oc');
		$this->load->model('mf_itemplan_madre/M_consulta_item_madre');
		$this->load->model('mf_consulta/m_detalle_consulta');
		$this->load->model('mf_control_presupuestal/m_control_presupuestal');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	       
    }

    function getDataCentralByCoordenadas() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try{
            $latitud  = $this->input->post('latitud');
            $longitud = $this->input->post('longitud');
            //$codigoCentral = $this->input->post('codigoCentral');
        
            //$idCentral = $this->m_pqt_central->getPqtCentralByCodigo($codigoCentral)->idCentral;
            
            $arrayIdCentral = $this->m_utils->getMdfCercanoByCoord($longitud, $latitud);
            
            $idCentral    = $arrayIdCentral['idCentral'];
            $codigo       = $arrayIdCentral['codigo'];
            $departamento = $arrayIdCentral['departamento'];
            $distrito     = $arrayIdCentral['distrito'];
			$provincia    = $arrayIdCentral['provincia'];
            $idZonal      = $arrayIdCentral['idZonal'];
            $empresaColabDesc = $arrayIdCentral['empresaColabDesc'];
            $zonalDesc        = $arrayIdCentral['zonalDesc'];
            $idEmpresaColab   = $arrayIdCentral['idEmpresaColab'];
            $idZonal          = $arrayIdCentral['idZonal'];
            

            $data['idCentral']        = $idCentral;
            $data['idZonal']          = $idZonal;
            $data['departamento']     = $departamento;
            $data['distrito']         = $distrito;
            $data['empresaColabDesc'] = $empresaColabDesc;
            $data['zonalDesc']        = $zonalDesc;
            $data['idEmpresaColab']   = $idEmpresaColab;
            $data['idZonal']          = $idZonal;
            $data['codigoCentral']    = $codigo;
			$data['provincia']        = $provincia;
            $data['cmbInversion']     = __buildComboInversion($idEmpresaColab);
            $data['error']    = EXIT_SUCCESS;
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function registrarItemplan() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $objJson = json_decode($this->input->post('objJson'), true);
            $itemplan    = $this->m_utils->getCodigoItemplan($objJson['idZonal'], $objJson['idProyecto']);
            
            $fechaActual = $this->m_utils->fechaActual();
            $idUsuario   = $this->session->userdata('idPersonaSessionPan');

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }

			$isCableadoEdificios = false;
			$isFTTHH	=	false;
			$isOVarias = false;
			$isMantenimiento 	= false;
			$costo_mo = null;
			$isIpMadreRefoExpres = false;
			$arrayCosto = null;
			if($objJson['idProyecto']	==	21){//CABLEADO DE EDIFICIOS
				$arrayCosto = $this->m_registro_itemplan_masivo->getCostoxDptoByIdEECCAndSubProy($objJson['idSubProyecto'], $objJson['idEmpresaColab'], $objJson['cantFactorPlanificado']);
				if($arrayCosto == null){
					throw new Exception('LA OBRA NO CUENTA CON UN PRECIO CONFIGURADO PARA EL SUBPROYECTO, CONTRATA Y DPTO INGRESADO.');
				}else{
					$precioObra = $arrayCosto['costo'];
					$costo_mo = round($objJson['cantFactorPlanificado'] * $arrayCosto['costo'],2);
				}
				if($costo_mo == null || $costo_mo == 0){
					throw new Exception('NO HAY COSTO DE MANO DE OBRA.');
				}
				$isCableadoEdificios	=	true;
				$objJson['idEstadoPlan']  = ID_ESTADO_PLAN_PRE_REGISTRO;
			}else 
			if($objJson['idProyecto']	==	52)
			{//FTTH
				$hasAnclaConfig = $this->m_utils->hasEstacionesAnclasBySubProyecto($objJson['idSubProyecto']);
				if($hasAnclaConfig	==	0){
					throw new Exception('EL subproyecto no cuenta con Estaciones Configuradas.');
				}
				

				// if ($objJson['idProyecto'] != 52)
				// {
				// 	$infoItemMadre = $this->M_consulta_item_madre->getInfoItemplanMadre($objJson['itemplan_m']);
				// 	if($infoItemMadre['orden_compra']	==	null){
				// 		throw new Exception('EL IP Madre requiere de una Orden de Compra.');
				// 	}
				// 	if($infoItemMadre['idEstado']	<>	2){
				// 		throw new Exception('EL estado plan no permite registro de nuevos itemplan.');
				// 	}
				// 	if($infoItemMadre['idEmpresaColab']	<>	$objJson['idEmpresaColab']){
				// 		throw new Exception('La EECC de la Obra a registrar debe ser la misma del Itemplan Madre Seleccionado.');
				// 	}
				// }




				$precioObra	=	0;
				$isFTTHH	=	true;
				// $objJson['idEstadoPlan']  = ID_ESTADO_PLAN_DISENIO;
				$objJson['idEstadoPlan']  = 1; // PRE-REGISTRO


			}else if($objJson['idProyecto']	==	54){//PROYECTOS VARIOS
				$costo_mo = $objJson['costo_unitario_mo'];
				if($objJson['idSubProyecto']	==	747){///SI ES IP MADRE REFORZAMIENTO EXPRESS
					$costo_mo = $this->m_utils->getCostoTotalToOCMoPqtReforzamientoExpress($objJson['idCentral'], $objJson['idEmpresaColab'],$objJson['cantFactorPlanificado']);
					$isIpMadreRefoExpres = true;
					$objJson['costo_unitario_mat']	= $this->m_utils->getCostoTotalMatToCostounitarioMatReforzamientoMadre($objJson['cantFactorPlanificado']);
				}
				if($costo_mo <= 0 || $costo_mo == null 	||	$costo_mo	==	''){
					throw new Exception('No se detecto un costo MO no es Valido, ingrese un costo MO correcto.');
				}				
				$uips	=	$objJson['cantFactorPlanificado'];
				if($uips <= 0 || $uips == null 	||	$uips	==	''){
					throw new Exception('Debe ingresar una cantidad de UIP');
				}	
				
				$precioObra	=	0;
				$objJson['idEstadoPlan']  	= ID_ESTADO_PLAN_PRE_REGISTRO;
				$objJson['paquetizado_fg']  = 1;
				$isOVarias	=	true;
			}else if($objJson['idProyecto']	==	55){//PROYECTOS MANTENIMIENTO		
				$precioObra	=	0;
				if($objJson['idSubProyecto']	==	738	||	$objJson['idSubProyecto']	==	758){//MANTENMIENTO SIN OC
					/*
					$uips	=	$objJson['cantFactorPlanificado'];
					if($uips <= 0 || $uips == null 	||	$uips	==	''){
						throw new Exception('Debe ingresar una cantidad de UIP');
					}*/											
					$objJson['idEstadoPlan']  	= ID_ESTADO_PLAN_EN_OBRA;
					$objJson['paquetizado_fg']  = 1;
					$isOVarias	=	false;
				}else if(in_array($objJson['idSubProyecto'], array(739,755,756,757,759))){
					/*
					$uips	=	$objJson['cantFactorPlanificado'];
					if($uips <= 0 || $uips == null 	||	$uips	==	''){
						throw new Exception('Debe ingresar una cantidad de UIP');
					}*/
					$costo_mo = $objJson['costo_unitario_mo'];
					if($costo_mo <= 0 || $costo_mo == null 	||	$costo_mo	==	''){
						throw new Exception('El costo MO no es Valido, ingrese un costo MO correcto.');
					}
					$objJson['idEstadoPlan']  	= ID_ESTADO_PLAN_PRE_REGISTRO;
					$objJson['paquetizado_fg']  = 1;					 
					$isMantenimiento = true;//para registro OC
				}else{
					$objJson['idEstadoPlan']  	= ID_ESTADO_PLAN_PRE_REGISTRO;
					$objJson['paquetizado_fg']  = 1;
				}
			}else{
				$precioObra	=	0;
				$objJson['idEstadoPlan']  = ID_ESTADO_PLAN_PRE_REGISTRO;
			}
			
            $objJson['itemplan'] 	  = $itemplan;            
            $objJson['fechaRegistro'] = $fechaActual;
			$objJson['usua_crea_obra']= $idUsuario;
            $objJson['idUsuarioLog']  = $idUsuario;
            $objJson['fechaLog']      = $fechaActual; 
			$objJson['precio_obra']   = $precioObra;
			$objJson['descripcion']	  = 'REGISTRO INDIVIDUAL';
			if($objJson['idSubProyecto']	==	658){//PIN FTTH CON SIROPE
				$objJson['ult_codigo_sirope']   = $itemplan.'FO'; 
			}
            unset($objJson['idProyecto']);
            $data = $this->m_utils->registrarItemplan($objJson);

            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
			}
			if($isCableadoEdificios){//cableado de edificios genera solicitud oc



				
				$this->createPOMoAutomaticIndividual($itemplan, $idUsuario);
				
				$codigo_solicitud = $this->m_utils->getNextCodSolicitud();//obtengo codigo unico de solicitud
				$dataPlanobra = array(  
					"itemplan"                   => $itemplan,
					"costo_unitario_mo"          => $costo_mo,
					//"costo_unitario_mat"         => 0,
					"solicitud_oc"               => $codigo_solicitud,
					"estado_sol_oc"              => 'PENDIENTE',
					"costo_unitario_mo_crea_oc"  => $costo_mo
					//"costo_unitario_mat_crea_oc" => 0
				);
				$solicitud_oc_creacion = array(
					'codigo_solicitud'  =>  $codigo_solicitud,
					'idEmpresaColab'    =>  $objJson['idEmpresaColab'],
					'estado'            =>  1,//pendiente
					'fecha_creacion'    =>  date("Y-m-d H:i:s"),
					'idSubProyecto'     =>  $objJson['idSubProyecto'],
					'plan'              =>  null,//plan no va
					'codigoInversion'   => 	$objJson['codigoInversion'],
					'estatus_solicitud' => 'NUEVO',
					'tipo_solicitud'    =>  1,// 1= CREACION, 2 = EDICION, 3 = CERTIFICACION,
					'usuario_creacion'  =>  $idUsuario,
					'fecha_creacion'    =>  date("Y-m-d H:i:s")
				);
				$item_x_sol = array(
					'itemplan'            =>  $itemplan,
					'codigo_solicitud_oc' =>  $codigo_solicitud,
					'costo_unitario_mo'   =>  $costo_mo
				);
				$data = $this->m_registro_itemplan_masivo->crearSolCreacionForItemplan($dataPlanobra, $solicitud_oc_creacion, $item_x_sol);
			}
			
			if($isOVarias || $isMantenimiento){
				log_message('error', 'generate sol oc');
				$codigo_solicitud = $this->m_utils->getNextCodSolicitud();//obtengo codigo unico de solicitud
				$dataPlanobra = array(  
					"itemplan"                   => $itemplan,
					"costo_unitario_mo"          => $costo_mo,
					//"costo_unitario_mat"         => 0,
					"solicitud_oc"               => $codigo_solicitud,
					"estado_sol_oc"              => 'PENDIENTE',
					"costo_unitario_mo_crea_oc"  => $costo_mo
					//"costo_unitario_mat_crea_oc" => 0
				);
				$solicitud_oc_creacion = array(
					'codigo_solicitud'  =>  $codigo_solicitud,
					'idEmpresaColab'    =>  $objJson['idEmpresaColab'],
					'estado'            =>  1,//pendiente
					'fecha_creacion'    =>  date("Y-m-d H:i:s"),
					'idSubProyecto'     =>  $objJson['idSubProyecto'],
					'plan'              =>  null,//plan no va
					'codigoInversion'   => 	$objJson['codigoInversion'],
					'estatus_solicitud' => 'NUEVO',
					'tipo_solicitud'    =>  1,// 1= CREACION, 2 = EDICION, 3 = CERTIFICACION,
					'usuario_creacion'  =>  $idUsuario,
					'fecha_creacion'    =>  date("Y-m-d H:i:s")
				);
				$item_x_sol = array(
					'itemplan'            =>  $itemplan,
					'codigo_solicitud_oc' =>  $codigo_solicitud,
					'costo_unitario_mo'   =>  $costo_mo
				);
				$data = $this->m_registro_itemplan_masivo->crearSolCreacionForItemplan($dataPlanobra, $solicitud_oc_creacion, $item_x_sol);

				if($isIpMadreRefoExpres){
					$this->createPoRefoExpresPqt($itemplan, $objJson['cantFactorPlanificado'], 1);//MO
					$this->createPoRefoExpresPqt($itemplan, $objJson['cantFactorPlanificado'], 2);//MAT
				}
			}


			if($isFTTHH){
				$disenoList	=	$this->adjudicarITemplanIndividual($itemplan);
				if(count($disenoList) > 0){
					log_message('error', "creando log pqt");
					// log_message('error', $disenoList);
					$data = $this->m_bandeja_solicitud_oc->insertMasiveDiseno($disenoList);		
					$costoDiseno = $this->createPoPqt(ID_ESTACION_DISENO, ID_ESTACION_FO, $itemplan, 1);
					// $this->createPoPqt(ID_ESTACION_FO, ID_ESTACION_FO, $itemplan, 2);

					/* clarence */
					// pre($costoDiseno);
					$costo_mo = @$costoDiseno['costo_total_po'];
					$codigo_solicitud = $this->m_utils->getNextCodSolicitud();//obtengo codigo unico de solicitud
					$dataPlanobra = array(  
						"itemplan"                   => $itemplan,
						// "costo_unitario_mo"          => $costo_mo,//no va
						"costo_unitario_mo_diseno"   => $costo_mo,// crea columna
						//"costo_unitario_mat"         => 0,
						// "solicitud_oc"               => $codigo_solicitud, //nova
						"solicitud_oc_diseno"               => $codigo_solicitud, //crear columna
						// "estado_sol_oc"              => 'PENDIENTE', no va
						"estado_sol_oc_diseno"              => 'PENDIENTE',// crear coilumna
						// "costo_unitario_mo_crea_oc"  => $costo_mo //nova
						"costo_unitario_mo_crea_oc_diseno"  => $costo_mo // crear columna
						//"costo_unitario_mat_crea_oc" => 0
					);
					$solicitud_oc_creacion = array(
						'codigo_solicitud'  =>  $codigo_solicitud,
						'idEmpresaColab'    =>  $objJson['idEmpresaColab'],
						'estado'            =>  1,//pendiente
						'fecha_creacion'    =>  date("Y-m-d H:i:s"),
						'idSubProyecto'     =>  $objJson['idSubProyecto'],
						'plan'              =>  null,//plan no va
						'codigoInversion'   => 	$objJson['codigoInversion'],
						'estatus_solicitud' => 'NUEVO',
						'tipo_solicitud'    =>  1,// 1= CREACION, 2 = EDICION, 3 = CERTIFICACION,
						'usuario_creacion'  =>  $idUsuario,
						'fecha_creacion'    =>  date("Y-m-d H:i:s")
					);
					$item_x_sol = array(
						'itemplan'            =>  $itemplan,
						'codigo_solicitud_oc' =>  $codigo_solicitud,
						'costo_unitario_mo'   =>  $costo_mo
					);

					// $data = $this->m_registro_itemplan_masivo->crearSolCreacionForItemplan($dataPlanobra, $solicitud_oc_creacion, $item_x_sol);
					$data = $this->m_registro_itemplan_masivo->crearSolCreacionForItemplanDiseno($dataPlanobra, $solicitud_oc_creacion, $item_x_sol);
					/*fin clarence */
				}			

			}
            $data['itemplan'] = $itemplan;
        } catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

	public function adjudicarITemplanIndividual($itemplan){
			$disenoList	= array();
			$idUsuario      = $this->session->userdata('idPersonaSessionPan');
			$fechaActual    = $this->m_utils->fechaActual();
			$has_ancla = false;
			$has_fo  = false;
			$has_coax = false;
			$infoAnclasByItemplan = $this->m_bandeja_solicitud_oc->hasEstacionesAnclasByItemplan($itemplan);
			//log_message('error', 'IP POR ADJUDICAR:'.$itemplan);
			if($infoAnclasByItemplan['coaxial'] > 0){
				$has_coax  = true;
				$has_ancla = true;
			}
			if($infoAnclasByItemplan['fo'] > 0){
				$has_fo    = true;
				$has_ancla = true;
			}
			if($has_ancla){//si tiene anclas obtenemos sus dias de adjudicacion
					
				$dias = null;
				if($dias == null){//si no tiene por defecto 4
					$dias = 4;
				}
				$curHour = date('H');
				if ($curHour >= 13) {//13:00 PM
					$dias = ($dias + 1);
				}
				$nuevafecha = strtotime('+' . $dias . ' day', strtotime($fechaActual));
				$fechaPreAtencion = date('Y-m-d', $nuevafecha);

				if($has_fo){
					$infoAdjudicacion = array ( 
						'itemplan'                => $itemplan,
						'idEstacion'              => ID_ESTACION_FO,
						// 'estado'                  => (($idSubProyecto == 722) ? 6 : ID_ESTADO_PLAN_DISENIO),
						'estado'                  => ID_ESTADO_PLAN_DISENIO,
						'fecha_registro'          => $fechaActual,
						'usuario_registro'        => $idUsuario,     
						'fecha_adjudicacion'	  => $fechaActual,
						'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
						'fecha_prevista_atencion' => $fechaPreAtencion
					);				
					$disenoList[] = $infoAdjudicacion;
		   
				}

				if($has_coax){
					$infoAdjudicacion = array ( 
						'itemplan'                => $itemplan,
						'idEstacion'              => ID_ESTACION_COAX,
						// 'estado'                  => (($idSubProyecto == 722) ? 6 : ID_ESTADO_PLAN_DISENIO),
						'estado'                  => ID_ESTADO_PLAN_DISENIO,
						'fecha_registro'          => $fechaActual,
						'usuario_registro'        => $idUsuario,     
						'fecha_adjudicacion'	  => $fechaActual,
						'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
						'fecha_prevista_atencion' => $fechaPreAtencion
					);				
					$disenoList[] = $infoAdjudicacion;
				}

				/*	
				if($dataObra['paquetizado_fg'] ==  2){//es paquetizada
					$itemplanListPoPqt []= $itemplan;//almacenamos el itemplan para postererormente generarle su po pqt
				}
				*/
			}
		 
		return $disenoList;
	}	

	function createManualPoPqt(){
		/*
		$itemplan = '';
		$this->createPoPqt(ID_ESTACION_DISENO, ID_ESTACION_FO, $itemplan, 1);
		$this->createPoPqt(ID_ESTACION_FO, ID_ESTACION_FO, $itemplan, 2);		
		*/
		$itemplanList = array('');
		foreach($itemplanList as $itemplan){
			$this->createPoPqt(ID_ESTACION_DISENO, ID_ESTACION_FO, $itemplan, 1);
			//$this->createPoPqt(ID_ESTACION_FO, ID_ESTACION_FO, $itemplan, 2);
		}	
	}
	/**
 * 	idEstacionPlace	= ESTACION EN LA QUE SE CREARA LA PO PQT
 * idEstacionPartidas	=	PARTIDAS CONFIGURADAS A LA ESTACION 
 * tipo_partidas	=	1	SOLO PARITDA DE DISENO, 2 TODO MENOS DISENO, 3 TODAS LAS PARTIDAS DE ESTA ESTACION
	 */
	public function createPoPqt($idEstacionPlace, $idEstacionPartidas, $itemplan, $tipo_partidas)
    {
        $data['error'] 			= EXIT_ERROR;
        $data['msj'] 			= null;
        $data['costo_total_po'] = 0;
        try { 
            $this->db->trans_begin();
         //   $idEstacionPartidas =  ID_ESTACION_FO;//todo es FO INTEGRAL
		    $idUsuario      = $this->session->userdata('idPersonaSessionPan');
            $fechaActual 		= $this->m_utils->fechaActual();              
			$hasPoPqtACtive = $this->m_utils->hasPoPqtActive($itemplan, $idEstacionPlace);
			
				$arrayInfo = $this->m_utils->getPlanObraByItemplan($itemplan);
				if($arrayInfo == null){
					throw new Exception('Hubo un error al traer la información del itemplan.');
				}
			 	
				$codigoPO = $this->m_utils->getCodigoPO($itemplan); //Clarence
				//$codigoPO = date('Y').'-'.time();
				if($codigoPO == null || $codigoPO == '') {
					throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
				}
				$dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MO', $idEstacionPlace);
				if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
					throw new Exception("No tiene configurado un area.");
				}

				$partidasTOPoPqt = $this->m_utils->getPartidasToPoPqtByTipo($idEstacionPartidas, $itemplan, $tipo_partidas);
				if($partidasTOPoPqt == null){
					throw new Exception('No se encontraron Partidas Configuradas para la obra.');
				}

				$arrayDetalleInsert = array();
				$costo_total_po = 0;
				foreach($partidasTOPoPqt as $dataPartidaPqt){

					$costo_partida = $dataPartidaPqt['baremo']*$dataPartidaPqt['costo']*$arrayInfo['cantFactorPlanificado'];

					$detallePo = array (
						'codigo_po'        => $codigoPO,
						'codigoPartida'    => $dataPartidaPqt['codigoPartida'], //ESTADO REGISTRADO
						'baremo'           => $dataPartidaPqt['baremo'],
						'preciario'        => $dataPartidaPqt['costo'],
						'cantidadInicial'  => $dataPartidaPqt['cantFactorPlanificado'],
						'montoInicial'     => $costo_partida,
						'cantidadFinal'    => $arrayInfo['cantFactorPlanificado'],
						'montoFinal'       => $costo_partida,
						'costoMo'          => $costo_partida
					);

					$costo_total_po	=	$costo_total_po	+	$costo_partida;
					array_push($arrayDetalleInsert, $detallePo);
				}

				if($arrayInfo['idProyecto']	==	52){//FTTHA ADICIONAMOS PARTIDA FERRETERIA
					if($idEstacionPlace	==	5){//SI ES LA FO
						$baremoFerreteria = 1;
						$costo_partida = $baremoFerreteria*COSTO_PARTIDA_FERRETERIA_FTTH*$arrayInfo['cantFactorPlanificado'];
						$detallePo = array (
							'codigo_po'        => $codigoPO,
							'codigoPartida'    => '69901-2', //PARTIDA UNIDAD MATERIAL FERRETERIA
							'baremo'           => $baremoFerreteria,
							'preciario'        => COSTO_PARTIDA_FERRETERIA_FTTH,
							'cantidadInicial'  => $arrayInfo['cantFactorPlanificado'],
							'montoInicial'     => $costo_partida,
							'cantidadFinal'    => $arrayInfo['cantFactorPlanificado'],
							'montoFinal'       => $costo_partida,
							'costoMo'          => $costo_partida
						);

						$costo_total_po	=	$costo_total_po	+	$costo_partida;
						array_push($arrayDetalleInsert, $detallePo);
					}
				}
				 
				if(count($arrayDetalleInsert) == 0){
					throw new Exception("No hay partidas válidas para el registro de la PO");
				}

				$dataPo = array (
					'codigo_po'      => $codigoPO,
					'itemplan'       => $itemplan,
					'estado_po'      => ID_ESTADO_PO_REGISTRADO,
					'idEstacion'     => $idEstacionPlace,
					'costo_total'    => $costo_total_po,
					'idUsuario'      => $idUsuario,
					'fechaRegistro'  => $fechaActual,
					'flg_tipo_area'  => 2,
					'idEmpresaColab' => $arrayInfo['idEmpresaColab'],
					'idArea'         => $dataSubEstacionArea['idArea'],
					'idSubProyecto'  => $arrayInfo['idSubProyecto'],
					'isPoPqt'        => 1
				);

				
				$dataLogPO =    array(
					'codigo_po'        =>  $codigoPO,
					'itemplan'         =>  $itemplan,
					'idUsuario'        =>  $idUsuario,
					'fecha_registro'   =>  $fechaActual,
					'idPoestado'       =>  ID_ESTADO_PO_REGISTRADO
				);

				$data = $this->m_utils->registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO);
				$data['costo_total_po'] = $costo_total_po;
				if($data['error'] == EXIT_ERROR) {
					throw new Exception($data['msj']);
				}
				$this->db->trans_commit();
		
               
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
       return $data;
    }

	public function createPOMoAutomaticIndividual($itemplan_in, $idUsuario)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try { 
            $this->db->trans_begin();
            // $idEstacion =  5;//todo es FO INTEGRAL
            $fechaActual = $this->m_utils->fechaActual();
            $item	=	$itemplan_in;
                $estacionesAnclas = $this->m_utils->getEstacionesAnclasByItemplan($item);
                foreach ($estacionesAnclas as $row){
                    $hasPoPqtACtive = $this->m_utils->hasPoPqtActive($item, $row['idEstacion']);
                    if($hasPoPqtACtive == 0){
                        $idEstacion = $row['idEstacion'];
                        $arrayInfo = $this->m_utils->getPlanObraByItemplan($item);
                        if($arrayInfo == null){
                            throw new Exception('Hubo un error al traer la información del itemplan.');
                        }
                        // $arrayMat = $this->m_utils->getListaMaterialesToEdificios($arrayInfo['tipo_edificio'],$arrayInfo['cant_cto']);
                        $arrayCosto = $this->m_registro_itemplan_masivo->getCostoxDptoByIdEECCAndSubProy($arrayInfo['idSubProyecto'], $arrayInfo['idEmpresaColab'], $arrayInfo['cantFactorPlanificado']);
                        if($arrayCosto == null){
                            throw new Exception('LA OBRA NO CUENTA CON UN PRECIO CONFIGURADO PARA EL SUBPROYECTO, CONTRATA Y DPTO.');
                        }
                        $codigoPO = $this->m_utils->getCodigoPO($item);
                        if($codigoPO == null || $codigoPO == '') {
                            throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
                        }
                        $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($item, 'MO', $idEstacion);
                        if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                            throw new Exception("No tiene configurado un area.");
                        }

                        $costoTotal = round($arrayCosto['costo']*$arrayInfo['cantFactorPlanificado'],2);
                        $dataPo = array (
                            'codigo_po'      => $codigoPO,
                            'itemplan'       => $item,
                            'estado_po'      => ID_ESTADO_PO_REGISTRADO,
                            'idEstacion'     => $idEstacion,
                            'costo_total'    => &$costoTotal,
                            'idUsuario'      => $idUsuario,
                            'fechaRegistro'  => $fechaActual,
                            'flg_tipo_area'  => 2,
                            'idEmpresaColab' => $arrayInfo['idEmpresaColab'],
                            'idArea'         => $dataSubEstacionArea['idArea'],
                            'idSubProyecto'  => $arrayInfo['idSubProyecto'],
							'isPoPqt'        => 1
                        );

                        $arrayDetalleInsert = array();
                        $detallePo = array (
                            'codigo_po'        => $codigoPO,
                            'codigoPartida'    => $arrayCosto['codigoPartida'], //ESTADO REGISTRADO
                            'baremo'           => 1,
                            'preciario'        => $arrayCosto['costo'],
                            'cantidadInicial'  => $arrayInfo['cantFactorPlanificado'],
                            'montoInicial'     => $costoTotal,
                            'cantidadFinal'    => $arrayInfo['cantFactorPlanificado'],
                            'montoFinal'       => $costoTotal,
                            'costoMo'          => $costoTotal
                        );
                        $arrayDetalleInsert[] = $detallePo;
                        if(count($arrayDetalleInsert) == 0){
                            throw new Exception("No hay partidas válidas para el registro de la PO");
                        }

                        $dataLogPO =    array(
                            'codigo_po'        =>  $codigoPO,
                            'itemplan'         =>  $item,
                            'idUsuario'        =>  $idUsuario,
                            'fecha_registro'   =>  $fechaActual,
                            'idPoestado'       =>  ID_ESTADO_PO_REGISTRADO
                        );

                        $data = $this->m_utils->registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO);
                        if($data['error'] == EXIT_ERROR) {
                            throw new Exception($data['msj']);
                        }
                        $this->db->trans_commit();
                    }
                }          

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
       return $data;
    }

    function liquidarObra() {
        try {
            $this->db->trans_begin();

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();
            $itemplan    = $this->input->post('itemplan');
            $idEstacion  = $this->input->post('idEstacion');
            if($idUsuario == null) {
                throw new Exception('La sesión a expirado, recargue la página');
            }
			
			log_message('error', print_r($_FILES, true));
            
			if(count($_FILES) == 0){
                throw new Exception('Debe poner un archivo para registrar!! ');
            }

            if($itemplan == null || $itemplan == '') {
                throw new Exception('No cuenta con itemplan, verificar.');
            }

            if($idEstacion == null || $idEstacion == '') {
                throw new Exception('No cuenta con Estacion, verificar.');
            }

			$hasSolActivo = $this->m_utils->getCountExcesoPdt($itemplan);

			if($hasSolActivo > 0){
				throw new Exception('No se pueden aplicar los cambios, debido ah que cuenta con una Solicitud de Exceso Pendiente de Aprobacion.');
			}

            $file     = $_FILES ["file"] ["name"];
            $filetype = $_FILES ["file"] ["type"];
            $filesize = $_FILES ["file"] ["size"];
            $archivo  = $_FILES["file"]["tmp_name"];

            $ubicEvidencia = 'uploads/evidencia_liquidacion/'.$itemplan;
            if (!is_dir ( 'uploads/evidencia_liquidacion/'.$itemplan)){
                mkdir('uploads/evidencia_liquidacion/'.$itemplan, 0777 );
            }

            //$file2 = utf8_decode($file);
            $rutaFile = $ubicEvidencia . "/" . $file;
			log_message('error', $rutaFile);
			log_message('error', $file);
			log_message('error', $archivo);
            if (utf8_decode($file) && move_uploaded_file($archivo, $rutaFile)) {
                $dataObra = $this->m_utils->getPlanObraByItemplan($itemplan);
                if($dataObra['idTipoPlanta'] == ID_TIPO_PLANTA_INTERNA) {
					if($dataObra['idEstadoPlan'] ==	ID_ESTADO_PLAN_EN_OBRA){			
						$objDetalle = array(
												'itemplan'       => $itemplan,
												'idEstacion'     => $idEstacion,
												'idUsuarioLog'   => $idUsuario,
												'fecha'          => $fechaActual,
												'ruta_evidencia' => $rutaFile, 
												'porcentaje'     => 100,
												'comentario'     => 'Liquidando PIN (evidencia)'
											);
						
						$objPlanObra = array(
												'idEstadoPlan' => ID_ESTADO_PLAN_PRE_LIQUIDADO,
												'idUsuarioLog' => $idUsuario,
												'fechaLog'     => $fechaActual,
												'descripcion'  => 'LIQUIDACION DE OBRA',
												'fechaPreLiquidacion' => $fechaActual
											);
						
											
						$data = $this->m_utils->actualizarPlanObra($itemplan, $objPlanObra);
						
						if($data['error'] == EXIT_ERROR) {
							throw new Exception('No se liquido la obra, verificar.');
						}
					
						$objPo = array(
							'estado_po' => ID_ESTADO_PO_LIQUIDADO
						);

						$flg_area = 2;

						$data = $this->m_utils->actualizarPoByItemplan($itemplan, ID_ESTADO_PO_APROBADO, $objPo, ID_ESTACION_PIN, $flg_area, $idUsuario);
						
						if($data['error'] == EXIT_ERROR) {
							throw new Exception($data['msj']);
						}
					}else if($dataObra['idEstadoPlan'] ==	ID_ESTADO_PLAN_PRE_LIQUIDADO){							
							$this->m_utils->desactivarUltimaLiquiPin($itemplan);
							$objDetalle = array(
								'itemplan'       => $itemplan,
								'idEstacion'     => $idEstacion,
								'idUsuarioLog'   => $idUsuario,
								'fecha'          => $fechaActual,
								'ruta_evidencia' => $rutaFile, 
								'porcentaje'     => 100,
								'comentario'     => 'Liquidando PIN (evidencia)'
							);
					}
                } else {
                    throw new Exception('No se encuentra configurado la liquidacion para esta obra.');
                }

                $countExist = $this->m_utils->countItemplanEstacionAvance($itemplan, $idEstacion);

                if($countExist == 0) {
                    $data = $this->m_utils->insertItemplanEstacionAvance($objDetalle);
                } else {
                    $data = $this->m_utils->actualizarItemplanEstacionAvance($itemplan, $idEstacion, $objDetalle);
                }
                
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception('No se subio la evidencia, verificar.');
                }
            } else {
                throw new Exception('No se cargo la evidencia, verificar.');
            }

            $this->db->trans_commit();
        } catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

	function insertEvidenciaByItemplan() {
		$itemplan =  $this->session->userdata('itemplanEvi');
		$file = $_FILES ["file"] ["name"];
		$filetype = $_FILES ["file"] ["type"];
		$filesize = $_FILES ["file"] ["size"];
		$directorio = 'uploads/evidencias/'.$itemplan;
		if (! is_dir ( 'uploads/evidencias/'.$itemplan)){
			mkdir ( 'uploads/evidencias/'.$itemplan, 0777 );
		}
		
		$subCarpeta = 'uploads/evidencias/'.$itemplan.'/'.$itemplan.'_tmp/';
		
		$this->session->set_userdata('subCarpetaEvi',$subCarpeta);
		
		$file2 = utf8_decode($file);//le generamos un nombreAleatorio
	
		if (! is_dir ( $subCarpeta))
			mkdir ( $subCarpeta, 0777 );
			if (utf8_decode($file) && move_uploaded_file ( $_FILES["file"] ["tmp_name"], $subCarpeta. $file2 )) {
					
					
				// $this->zip->add_data("uploads/evidencias/imagenes/" . $file2, "uploads/evidencias/imagenes/" . $file2);
				// $this->zip->read_file("uploads/evidencias/imagenes/" . $file2);
					
				/*   $dataimg = array (
						"file_name" => utf8_decode($file),
						"file_type" => 'img',
						"ruta_mostrar" => 'uploads/evidencias/imagenes/' . $file2,
					);*/
					log_message('error', 'INSERTO IMG');
				}
			// $result = $this->m_consultarEscuela->insertvidencia( $dataimg );
					
		
	//  $this->zip->archive('uploads/evidencias/imagenes/'.rand(1, 100).date("dmhis").'my_info.zip');
	$data['error'] = EXIT_SUCCESS;
		echo json_encode(array_map('utf8_encode', $data));
	}

	function getTabEstacionPoUtils() {
		$data['error'] = EXIT_ERROR;
		$data['msj']      = null;
		try {
			$itemplan = $this->input->post('itemplan');

			if($itemplan == null || $itemplan == '') {
				throw new Exception('comunicarse con el programador a cargo');
			}
			
			$data['error']   = EXIT_SUCCESS;
			$TabVerticalEstacion = $this->getTabEstacionPo($itemplan);

			$data['TabVerticalEstacion'] = $TabVerticalEstacion;
		} catch(Exception $e) {
			$data['msj'] = $e->getMessage();
		}
		echo json_encode($data);
	}

	function getTabEstacionPo($itemplan) {
		$dataSubEstacion = $this->m_utils->getSubProyectoEstaciosByItemplan($itemplan);
		$dataInfoIP =  $this->m_utils->getPlanObraByItemplan($itemplan);
		$tab = null;
		$tabContent = null;
		$tab = '<div class="col-auto">
					<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
					//show active
		$cont = 0;
		foreach($dataSubEstacion as $row) {
			$active = null;
			if($cont == 0) {
				$active = 'active';
			}
			$tab .= '   <a class="nav-link '.$active.'" id="atab_'.$row['idEstacion'].'"  data-toggle="pill" href="#tab_'.$row['idEstacion'].'" role="tab" aria-controls="#tab_'.$row['idEstacion'].'" aria-selected="true">     
							<span class="hidden-sm-down ml-1">'.$row['estacionDesc'].'</span>
						</a>';

			$tabContent .= ' <div class="tab-pane fade show '.$active.'" id="tab_'.$row['idEstacion'].'" role="tabpanel" aria-labelledby="atab_'.$row['idEstacion'].'">
								<h3>
									'.$row['estacionDesc'].'
								</h3>
								<div class="form-group">
									<div class="row col-md-12">';
											$arrayTipoArea = explode(',', $row['arrayTipoArea']);
											$arrayIdArea = explode(',', $row['arrayIdArea']);
											$arrayArea = explode(',', $row['arrayAreaDesc']);
											$ctn = 0;
											foreach($arrayArea as $row2) {
												// $arrayPo = explode(',', $row['arrayCodigoPo']);
												$urlPo = '';
												if($arrayTipoArea[$ctn] == 'MAT'){
													$urlPo = 'href="regIndiPOMat?itemplan='.$itemplan.'&estacion='.$row['idEstacion'].'&estacionDesc='.$row['estacionDesc'].'" target="_blank" title="Registrar PO Manual"';
												}else if($arrayTipoArea[$ctn] == 'MO'){
													$urlPo = '';
												}
												$htmlBody = $this->getHTMLPO($itemplan, $row['idEstacion'], $arrayIdArea[$ctn], $row2, $dataInfoIP);
												$tabContent .= '<div class="card border col-md-5" style="margin: 10px;">
																	<div class="card-header">
																		<a '.$urlPo.' aria-expanded="true">
																			'.$row2.'
																		</a>
																	</div>
																	<div class="card-body">
																		'.$htmlBody.'           
																	</div>
																</div>';
												$ctn++;
											}
				$tabContent .=      '</div>
								</div>
							</div>';
			$cont++;
		}
		$tab .= '    </div>
				</div>
				<div class="col">
					<div class="tab-content" id="v-pills-tabContent">
						'.$tabContent.'
					</div>
				</div>';
		return $tab;
	}

	function getHTMLPO($itemplan, $idEstacion, $idArea, $areaDesc, $dataInfoIP) {

		$dataPO = $this->m_utils->getArrayPOByFiltros($itemplan, $idEstacion, $idArea);
		$html = null;
		$tabContent = null;
		
		$html = '<div class="accordion" id="js_demo_accordion-'.$areaDesc.'">
					<div class="card">
						<div class="card-header">
							<a href="javascript:void(0);" class="card-title px-3 py-2 bg-success-600 text-white collapsed" data-toggle="collapse" data-target="#js_demo_accordion-'.$areaDesc.'1" aria-expanded="false">
								Bloodworks
								<span class="ml-auto">
									<span class="collapsed-reveal">
										<i class="fal fa-minus fs-xl"></i>
									</span>
									<span class="collapsed-hidden">
										<i class="fal fa-plus fs-xl"></i>
									</span>
								</span>
							</a>
						</div>
						<div id="js_demo_accordion-'.$areaDesc.'1" class="collapse" data-parent="#js_demo_accordion-'.$areaDesc.'" style="">
							<div class="card-body bg-success-50 p-3">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<a href="javascript:void(0);" class="card-title px-3 py-2 collapsed bg-warning-500 text-dark" data-toggle="collapse" data-target="#js_demo_accordion-'.$areaDesc.'b" aria-expanded="false">
								Xray reports
								<span class="ml-auto">
									<span class="collapsed-reveal">
										<i class="fal fa-minus fs-xl"></i>
									</span>
									<span class="collapsed-hidden">
										<i class="fal fa-plus fs-xl"></i>
									</span>
								</span>
							</a>
						</div>
						<div id="js_demo_accordion-'.$areaDesc.'b" class="collapse" data-parent="#js_demo_accordion-'.$areaDesc.'">
							<div class="card-body bg-warning-50 p-3">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<a href="javascript:void(0);" class="card-title px-3 py-2 collapsed bg-info-700 text-white" data-toggle="collapse" data-target="#js_demo_accordion-'.$areaDesc.'c" aria-expanded="false">
								ECG
								<span class="ml-auto">
									<span class="collapsed-reveal">
										<i class="fal fa-minus fs-xl"></i>
									</span>
									<span class="collapsed-hidden">
										<i class="fal fa-plus fs-xl"></i>
									</span>
								</span>
							</a>
						</div>
						<div id="js_demo_accordion-'.$areaDesc.'c" class="collapse" data-parent="#js_demo_accordion-'.$areaDesc.'">
							<div class="card-body bg-info-50 p-3">
								Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod.
							</div>
						</div>
					</div>
				</div>';

		$html = '<div class="accordion" id="accordion-'.$areaDesc.'">';
		$cont = 0;
		$contExtraButtons = '';
		foreach($dataPO as $row) {
			$contExtraButtons = '';
			if($row['flg_tipo_area'] == 1){//MAT
				
			}else{//MO
				if($dataInfoIP['idTipoPlanta'] == ID_TIPO_PLANTA_INTERNA) {
					if($dataInfoIP['idEstadoPlan'] == ID_ESTADO_PLAN_EN_OBRA){
						$contExtraButtons .= '<button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" data-itemplan="'.$itemplan.'" data-estacion="'.$idEstacion.'"
												onclick="editarPO(this)" id="btnDet_'.$row['codigo_po'].'">
												<span class="fal fa-edit mr-1"></span>
												Editar
											</button>';
					}
				}else if($dataInfoIP['idTipoPlanta'] == ID_TIPO_PLANTA_EXTERNA) {
					if($row['isPoPqt'] == 1){
						if(in_array($row['estado_po'],array(ID_ESTADO_PO_REGISTRADO))){
							$contExtraButtons .= '<button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" data-itemplan="'.$itemplan.'" data-estacion="'.$idEstacion.'"
													onclick="editarPartidaAdicPqt(this)" id="btnDet_'.$row['codigo_po'].'">
													<span class="fal fa-edit mr-1"></span>
													Editar
												</button>';
						}
					}
				}
			}
			
			if(in_array($row['estado_po'],array(ID_ESTADO_PO_REGISTRADO,ID_ESTADO_PO_PRE_APROBADO))){
				$contExtraButtons .= '<button type="button" class="btn btn-danger ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" data-itemplan="'.$itemplan.'" data-estacion="'.$idEstacion.'"
											onclick="cancelarPO(this)" id="btnCance_'.$row['codigo_po'].'">
											<span class="fal fa-ban mr-1"></span>
											Cancelar
										</button>';
			}
			
			
			$html .= '	<div class="card">
							<div class="card-header">
								<a href="javascript:void(0);" class="card-title px-3 py-2 collapsed text-white" data-toggle="collapse" data-target="#accordion-'.$areaDesc.$cont.'" aria-expanded="false" style="background-color: '.$row['color_po'].';">
									'.$row['codigo_po'].' - '.$row['estadoDesc'].'
									<span class="ml-auto">
										<span class="collapsed-reveal">
											<i class="fal fa-minus fs-xl"></i>
										</span>
										<span class="collapsed-hidden">
											<i class="fal fa-plus fs-xl"></i>
										</span>
									</span>
								</a>
							</div>
							<div id="accordion-'.$areaDesc.$cont.'" class="collapse" data-parent="#accordion-'.$areaDesc.'">
								<div class="card-body p-3" style="background-color: '.$row['contraste_color'].';">
									<div class="form-row">
										<div class="col-md-6 mb-3">
											<a>EECC :</a>
											<br>
											<a>'.$row['empresaColabDesc'].'</a>
										</div>
										<div class="col-md-6 mb-3">
											<a>ESTADO :</a>
											<br>
											<a>'.$row['estadoDesc'].'</a>
										</div>
										<div class="col-md-6 mb-3">
											<a>VR</a>
											<br>
											<a>'.$row['vale_reserva'].'</a>
										</div>
										<div class="col-md-6 mb-3">
											<a>MONTO :</a>
											<br>
											<a>'.number_format($row['costo_total'],2).'</a>
										</div>
									</div>
									<div class="card-footer text-muted py-2" style="text-align: center;">
										<div class="btn-group">
											<button type="button" class="btn btn-primary ml-auto waves-effect waves-themed" data-codigo_po="'.$row['codigo_po'].'" onclick="verDetallePO(this)" id="btnDet_'.$row['codigo_po'].'">
												<span class="fal fa-eye mr-1"></span>
												Ver Detalle
											</button>
											'.$contExtraButtons.'
										</div>
									</div>
								</div>
							</div>
						</div>';
			$cont++;
		}
		$html .= '</div>';
		return $html;
	}

	function validarObraUtils() {
		try {
			$this->db->trans_begin();

			$idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();
			$itemplan    = $this->input->post('itemplan');

			if($idUsuario == null) {
				throw new Exception('La sesión a expirado, recargue la página');
			}

			if($itemplan == null || $itemplan == '') {
				throw new Exception('No cuenta con itemplan, verificar.');
			}

			$dataObra = $this->m_utils->getPlanObraByItemplan($itemplan, ID_ESTADO_PO_LIQUIDADO);
			if($dataObra['idTipoPlanta'] == ID_TIPO_PLANTA_INTERNA) {				
				
				$objPlanObra = array(
										'idEstadoPlan' => ID_ESTADO_PLAN_EN_CERTIFICACION,
										'idUsuarioLog' => $idUsuario,
										'fechaLog'     => $fechaActual,
										'descripcion'  => 'Validacion Obra'
									);

				$data = $this->m_utils->actualizarPlanObra($itemplan, $objPlanObra);

				if($data['error'] == EXIT_ERROR) {
					throw new Exception($data['msj']);
				}

                $objPo = array(
                    'estado_po' => ID_ESTADO_PO_VALIDADO
                );
                $flg_area = 2;
                $data = $this->m_utils->actualizarPoByItemplan($itemplan, ID_ESTADO_PO_LIQUIDADO, $objPo, ID_ESTACION_PIN, $flg_area, $idUsuario);
                    
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
				$data = $this->generarSolEdiCertiByItemplan($itemplan, $idUsuario);
				if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
			} else {
				throw new Exception('No se encuentra configurado la liquidacion para esta obra.');
			}		
		
			$this->db->trans_commit();
		} catch(Exception $e){
			$this->db->trans_rollback();
			$data['msj'] = $e->getMessage();
		}
		echo json_encode($data);
	}

	function rechazarLiquidacionPIN() {
		try {
			$this->db->trans_begin();

			$idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();
			$itemplan    = $this->input->post('itemplan');
			$comentario    = $this->input->post('comentario');

			if($idUsuario == null) {
				throw new Exception('La sesión a expirado, recargue la página');
			}

			if($itemplan == null || $itemplan == '') {
				throw new Exception('No cuenta con itemplan, verificar.');
			}
		        
			$info_ult_liquida = $this->m_utils->getAllByItemplanFull($itemplan);
			$path_file = $info_ult_liquida['ruta_evidencia'];

			$rechazo_liqui = array('itemplan'			=>	$itemplan,
									'fecha_rechazo'		=>	$fechaActual,
									'usuario_rechazo'	=>	$idUsuario,
									'comentario'		=>	$comentario,
									'ruta_evidecia'		=>	$path_file,
									'estado'			=>	1);
			
			$data	=	$this->m_utils->inserRechazoLiquidacion($rechazo_liqui);
			if($data['error'] == EXIT_ERROR) {
				throw new Exception($data['msj']);
			}
		
			$this->db->trans_commit();
		} catch(Exception $e){
			$this->db->trans_rollback();
			$data['msj'] = $e->getMessage();
		}
		echo json_encode($data);
	}

	function cargarArchivoCertiPdt() {
		try {

			$idUsuario = $this->session->userdata('idPersonaSessionPan');

			if($idUsuario == null) {
                throw new Exception('La sesión a expirado, recargue la página.');
            }

			$fechaActual = $this->m_utils->fechaActual();

            if(isset($_FILES['file']['name'])) {
                $path   = $_FILES['file']['tmp_name'];
                $object = PHPExcel_IOFactory::load($path);
                foreach($object->getWorksheetIterator() as $worksheet) {
                    $highestRow    = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();
                    for($row=2; $row<=$highestRow; $row++) {
						$codigo_solicitud = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $itemplan         = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        
						$arrayDataSolicitud = $this->m_utils->getSolicitudOcByCodigo($codigo_solicitud);

						if($arrayDataSolicitud != null) {
							if($arrayDataSolicitud['estado'] == ID_ESTADO_SOLICITUD_OC_PDT_ACTA) {
								$dataItemplan = array();
								$dataItemplan['itemplan'] 		 = $itemplan;
								$dataItemplan['estado_oc_certi'] =	'PENDIENTE';
								array_push($itemplanList, $dataItemplan);	
								
								$dataSolicitud = array();
								$dataSolicitud['codigo_solicitud']  = $codigo_solicitud;
								$dataSolicitud['usuario_to_pndte']  = $idUsuario;
								$dataSolicitud['fecha_to_pndte']    = $fechaActual;
								$dataSolicitud['estado']            = 1;
								array_push($solicitudesList, $dataSolicitud);
							}
						}
 
                    }
                }

				$data = $this->m_utils->actualizarSolicitudPlanObraMasivo($solicitudesList, $itemplanList); 
            } else {
				throw new Exception('Cargar el excel.');
			}

		} catch(Exception $e) {
			$this->db->trans_rollback();
			$data['msj'] = $e->getMessage();
		}
		echo json_encode($data);
	}
	
	function reenviarTramaSiropeMN() {
        $data['msj'] = '';
        $data['error'] = EXIT_ERROR;
        try {
            $itemplan  = $this->input->post('itemplan');
			
			if($itemplan == null || $itemplan == '') {
				throw new Exception('No cuenta con itemplan, verificar.');
			}
			
			$dt 	= date("Y-m-d");
			$dt_7 	= date( "Y-m-d", strtotime( "$dt +7 day" ) );
           $data = $this->m_integracion_sirope->execWs($itemplan, $itemplan.'MN', $dt, $dt_7,'UPDATE_DATABASE');
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

	function getDataCentralByIdCentral() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try{
            $idCentral  = $this->input->post('idCentral');       
            $arrayIdCentral = $this->m_utils->getCentralByIdCodigo($idCentral, null);
            
            $idCentral    = $arrayIdCentral['idCentral'];
            $codigo       = $arrayIdCentral['codigo'];
            $latitud 			= $arrayIdCentral['latitud'];
            $longitud        	= $arrayIdCentral['longitud'];
            $idEmpresaColab   	= $arrayIdCentral['idEmpresaColab'];
            $idZonal          	= $arrayIdCentral['idZonal'];

            $data['idCentral']        = $idCentral;
            $data['latitud'] 		  = $latitud;
            $data['longitud']         = $longitud;
            $data['idEmpresaColab']   = $idEmpresaColab;
            $data['idZonal']          = $idZonal;
            $data['codigoCentral']    = $codigo;
		
            $data['cmbInversion']     = __buildComboInversion($idEmpresaColab);
            $data['error']    = EXIT_SUCCESS;
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

	public function generarSolEdiCertiByItemplan($itemplan, $idUsuarioIn){
		$data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try{ 
						 
			$idUsuario = $idUsuarioIn;			
			$fechaActual = $this->m_utils->fechaActual();
			$infoCreateSol = $this->m_detalle_consulta->getInfoSolCreacionByItem($itemplan); //getinfo solicitud de creacion                
			if ($infoCreateSol != null) {
				$infoCertiEdicionOC = $this->m_detalle_consulta->getDataToSolicitudEdicionCertiOC($itemplan); //costos mo
                    if ($infoCertiEdicionOC != null) {
                         $arrayItemXSolicitud	=	array();
						 $arraySolicitud = array();
						if($infoCertiEdicionOC['costo_unitario_mo']	>	0	&&	$infoCertiEdicionOC['costo_unitario_mo']	==	$infoCertiEdicionOC['total']){//SOLO GENERA CERTIFICACION EN ESPERA DE ACTA
							$codigo_solicitud_2 = $this->m_utils->getNextCodSolicitud(); //nuevo cod solicitud
							if ($codigo_solicitud_2 == null) {
								throw new Exception('No se pudo obtener el codigo de Solicitud refresque la pantalla y vuelva a intentarlo.');
							}
		
							$solicitud_oc_edi_certi_2 = array(
								'codigo_solicitud' => $codigo_solicitud_2,
								'idEmpresaColab' => $infoCreateSol['idEmpresaColab'],
								'estado' => 5, //EN ESPERA DE ACTA
								'fecha_creacion' => $fechaActual,
								'idSubProyecto' => $infoCreateSol['idSubProyecto'],
								'plan' => $infoCreateSol['plan'],
								'cesta' => $infoCreateSol['cesta'],
								'orden_compra' => $infoCreateSol['orden_compra'],
								'estatus_solicitud' => 'NUEVO',
								'tipo_solicitud' => 3, //tipo certificacion
								'usuario_creacion'  =>  $idUsuario
							);
							array_push($arraySolicitud, $solicitud_oc_edi_certi_2);
		
							$item_x_sol_2 = array(
								'itemplan' => $itemplan,
								'codigo_solicitud_oc' => $codigo_solicitud_2,
								'costo_unitario_mo' => $infoCertiEdicionOC['total'],
								'posicion' => $infoCreateSol['posicion'],
							);
							array_push($arrayItemXSolicitud, $item_x_sol_2);

							$updatePlanObra = array(
													'itemplan'				=> $itemplan,
													'solicitud_oc_certi' 	=> $codigo_solicitud_2,
													'costo_unitario_mo_certi' => $infoCertiEdicionOC['total'],
													'estado_oc_certi' 		=> 'EN ESPERA DE ACTA');


							$data = $this->m_detalle_consulta->generateOcEdicionCerti($arraySolicitud, $arrayItemXSolicitud, $updatePlanObra);

						}else if($infoCertiEdicionOC['total']	>	0 && $infoCertiEdicionOC['costo_unitario_mo']	!=	$infoCertiEdicionOC['total']){//se genera ediicon y certi porque vario el costo
							//sol edicion
							$codigo_solicitud = $this->m_utils->getNextCodSolicitud(); //nuevo cod solicitud
							if ($codigo_solicitud == null) {
								throw new Exception('No se pudo obtener el codigo de Solicitud refresque la pantalla y vuelva a intentarlo.');
							}
		
							$solicitud_oc_edi_certi = array(
								'codigo_solicitud' => $codigo_solicitud,
								'idEmpresaColab' => $infoCreateSol['idEmpresaColab'],
								'estado' => 1, //pendiente
								'fecha_creacion' => $fechaActual,
								'idSubProyecto' => $infoCreateSol['idSubProyecto'],
								'plan' => $infoCreateSol['plan'],
								'cesta' => $infoCreateSol['cesta'],
								'orden_compra' => $infoCreateSol['orden_compra'],
								'estatus_solicitud' => 'NUEVO',
								'tipo_solicitud' => 2, //tipo edicion
								'usuario_creacion'  =>  $idUsuario
							);
							array_push($arraySolicitud, $solicitud_oc_edi_certi);
		
							$item_x_sol = array(
								'itemplan' => $itemplan,
								'codigo_solicitud_oc' => $codigo_solicitud,
								'costo_unitario_mo' => $infoCertiEdicionOC['total'],
								'posicion' => $infoCreateSol['posicion'],
							);
		
							array_push($arrayItemXSolicitud, $item_x_sol);
							//sol certificacion
							$codigo_solicitud_2 = $this->m_utils->getNextCodSolicitud(); //nuevo cod solicitud
							if ($codigo_solicitud_2 == null) {
								throw new Exception('No se pudo obtener el codigo de Solicitud refresque la pantalla y vuelva a intentarlo.');
							}
		
							$solicitud_oc_edi_certi_2 = array(
								'codigo_solicitud' => $codigo_solicitud_2,
								'idEmpresaColab' => $infoCreateSol['idEmpresaColab'],
								'estado' => 4, //EN ESPERA DE EDICION
								'fecha_creacion' => $fechaActual,
								'idSubProyecto' => $infoCreateSol['idSubProyecto'],
								'plan' => $infoCreateSol['plan'],
								'cesta' => $infoCreateSol['cesta'],
								'orden_compra' => $infoCreateSol['orden_compra'],
								'estatus_solicitud' => 'NUEVO',
								'tipo_solicitud' => 3, //tipo certificacion
								'usuario_creacion'  =>  $idUsuario
							);
							array_push($arraySolicitud, $solicitud_oc_edi_certi_2);
		
							$item_x_sol_2 = array(
								'itemplan' => $itemplan,
								'codigo_solicitud_oc' => $codigo_solicitud_2,
								'costo_unitario_mo' => $infoCertiEdicionOC['total'],
								'posicion' => $infoCreateSol['posicion'],
							);
							array_push($arrayItemXSolicitud, $item_x_sol_2);

							$updatePlanObra = array(
													'itemplan'				=> $itemplan,
													'solicitud_oc_certi' 	=> $codigo_solicitud_2,
													'costo_unitario_mo_certi' => $infoCertiEdicionOC['total'],
													'estado_oc_certi' 		=> 'PENDIENTE DE EDICION',
													'solicitud_oc_dev' 		=> $codigo_solicitud,
													'costo_devolucion' 		=> $infoCertiEdicionOC['total'],
													'estado_oc_dev' 		=> 'PENDIENTE');

							$data = $this->m_detalle_consulta->generateOcEdicionCerti($arraySolicitud, $arrayItemXSolicitud, $updatePlanObra);
						}
                    } 
			}  

		}catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
		return $data;
	}

	function createManualOcEdiCertiByItemplan() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
			$itemplanList = array('');
			$idUsuario = 1;
			foreach($itemplanList as $itemplan){				 
				 //log_message('error', $itemplan);
				 $data = $this->generarSolEdiCertiByItemplan($itemplan, $idUsuario);
			}/*
			$itemplan = 'P-22-3299967635';
			$data = $this->generarSolEdiCertiByItemplan($itemplan);
			log_message('error',print_r($data,true));*/
		}catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
		return $data;
	}

	
	/*
		$tipoPo	=	1: MAT, 2:MO
		$tipoRegistro	=	1: NUEVO, 2:EDICION
		$origen			=	comentario 
	*/
	public function validateRegPoByCostoUnitario(){
	    $data['error']         = EXIT_ERROR;
	    $data['canGenSoli']    = EXIT_ERROR;
	    $data['msj'] = null;
	    try {
	        
	        $idUsuario     = ($this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null);
	        if($idUsuario  !=  null){
								
				$origen        = ($this->input->post('origen')      ? $this->input->post('origen')      : null);//1= CREACION PO MAT, 2 = CREACION PO MO, 3 = GESTION VR MAT, 4 = LIQUIDACION MO
    	        $tipoPo        = ($this->input->post('tipo_po')      ? $this->input->post('tipo_po')      : null);//1 = material, 2 = mo	        
    	        $tipoAccion    = ($this->input->post('accion')       ? $this->input->post('accion')       : null);//1 = nuevo, 2 = editar
    	        $codigo_po     = ($this->input->post('codigo_po')    ? $this->input->post('codigo_po')    : null);	        
    	        $itemplan      = ($this->input->post('itemplan')     ? $this->input->post('itemplan')     : null);
        	    $costoTotalPo  = ($this->input->post('costoTotalPo') ? $this->input->post('costoTotalPo') : null);
        	    
				$infoCU = $this->m_utils->getVariablesCostoUnitario($itemplan, $tipoPo, (($tipoAccion == 1 ) ? null : $codigo_po));
				
				if(in_array($infoCU['idSubProyecto'],array(697,728,750,751))){
					if(in_array($infoCU['idEstadoPlan'],array(2))){//solo si esta diseño no le validamos control presupuestal
						if($tipoPo == TIPO_PO_MATERIAL){//solo si es material pasa..
							$data['error'] = EXIT_SUCCESS;
							throw new Exception('Pasa, no requiere validacion');
						}
					}
				}

				if(in_array($infoCU['idSubProyecto'],array(722,723,724,725))){//cv					
					if($tipoPo == TIPO_PO_MATERIAL){//solo si es material pasa..
						$data['error'] = EXIT_SUCCESS;
						log_message('error', 'pasa....');
						throw new Exception('Pasa, no requiere validacion');
					}
				}

				if($tipoPo == TIPO_PO_MATERIAL){
					$costoUnitarioObra = $infoCU['costo_unitario_mat'];//costo limite de la obra
					$desc_tipoPo = 'Material';
				}else if($tipoPo == TIPO_PO_MANO_OBRA){
					$costoUnitarioObra = $infoCU['costo_unitario_mo'];//costo limite de la obra
					$desc_tipoPo = 'Mano de Obra';
				}else{
					throw new Exception('No se pudo determinar el tipo de PO a procesar. refresque y vuelva a intentarlo, de continuar comuniquise con el Administrador.');
				}
							
				if($costoUnitarioObra==null || $costoUnitarioObra==0){
					throw new Exception('La Obra no cuenta con Costo Unitario Registrado.');
				}
				
				$hasSolActivo = $this->m_utils->hasSolExceActivo($itemplan, $tipoPo);
				if($hasSolActivo > 0){
					throw new Exception('No se pueden aplicar los cambios, debido ah que cuenta con una Solicitud de Exceso Pendiente de Aprobacion.');
				}
				
				$costoTotalAllPo    =  $infoCU['total'];//costo actual de todas las po    	   
				//_log("COSTO TOTAL: ".$costoTotalAllPo." COSTO2: ".$costoTotalPo);
				#costoTotalPo costo de la po actual a editar, crear etc
				#costo de las po menos la que se va editar
				$nuevoCostoTotalAllPo = $costoTotalAllPo + $costoTotalPo;					 
				//_log("nuevoCostoTotalAllPo: ".$nuevoCostoTotalAllPo);
				//_log("costoUnitarioObra: ".$costoUnitarioObra);
				$arrayItem = explode('-', $itemplan);
				if((round($nuevoCostoTotalAllPo,2) == round($costoUnitarioObra,2))	&&  $tipoAccion == 2){
					//_log("igual");
					throw new Exception('No se detecto variacion en el costo MO.');
				}else if($nuevoCostoTotalAllPo > $costoUnitarioObra){// si el nuevo costo es mayor al programado, SOLO 2020 SE TOMARA POR AHORA 3-12-2020
					//_log("mayor");
					$exceso = $nuevoCostoTotalAllPo - $costoUnitarioObra;
					$data['canGenSoli']    =  EXIT_SUCCESS;//SI PODRA GENERAR SOLICITUD DE EXCEDENTE
					$data['costo_actual']  = $costoUnitarioObra;
					$data['excedente']      = $exceso;
					$data['costo_final']   = ($costoUnitarioObra+$exceso);
					//throw new Exception('No se puede procesar la Solicitud debido ah que el Costo programado para '.$desc_tipoPo.' de la Obra es de: S/.'.number_format($costoUnitarioObra,2,'.', ',').' y el costo consumido a la fecha es de:  S/.'.number_format($costoTotalAllPo,2,'.', ',').', siendo el Costo de la PO a procesar de: S/.'.number_format($costoTotalPo,2,'.', ',').' esta genera un Exceso de: S/.'.number_format($exceso,2,'.', ',').' ¿Desea Generar una Solicitud de Ampliacion de Costo de Obra por: S/.'.number_format($exceso,2,'.', ',').' ?');
					throw new Exception('No se puede registrar la po de '.$desc_tipoPo.' debido ah que excede en  S/.'.number_format($exceso,2,'.', ',').'.<br> Monto Cotizado: S/.'.number_format($costoUnitarioObra,2,'.', ',').'<br> Monto Consumido:  S/.'.number_format($costoTotalAllPo,2,'.', ',').'<br> Nueva Solicitud: S/.'.number_format($costoTotalPo,2,'.', ',').'<br> Exceso: S/.'.number_format($exceso,2,'.', ',').'<br> Desea Generar una Solicitud de Ampliacion de Costo de Obra por: S/.'.number_format($exceso,2,'.', ',').' ?');
				}else{//Permitir creacion de PO        	        
					$data['error'] = EXIT_SUCCESS;
				}				 				
	        }else{
	            throw new Exception('su sesion ha expirado, vuelva a iniciar sesion.');
	        }
	    } catch (Exception $e) {
	        $data['msj'] = $e->getMessage();
	    }
        echo json_encode(array_map('utf8_encode', $data));
	}

	function generarSolicitud() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
			$origen	 		= $this->input->post('origen');
            $itemplan 		= $this->input->post('itemplan');
            $tipo_po 		= $this->input->post('tipo_po');
            $costo_inicial 	= $this->input->post('costo_inicial');
            $exceso 		= $this->input->post('exceso_solicitado');
            $costo_final 	= $this->input->post('costo_final');
			$codigo_po	   	= $this->input->post('codigo_po');
			$data_json     	= $this->input->post('data_json');
			$comentario    	= $this->input->post('comentario');
			$idEstacion    	= $this->input->post('idEstacion');

			$data_json = json_decode($data_json);

			$fechaActual = $this->m_utils->fechaActual();

			$this->db->trans_begin();

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            if ($idUsuario == null) {
                throw new Exception('Su sesion expiro, porfavor vuelva a logearse.');
            }
			
			
			if(count($data_json) == 0) {
				throw new Exception("No cuenta con detalle");
			}
            $dataInsert = array('itemplan' 			=> $itemplan,
								'codigo_po'     	=> $codigo_po,
								'tipo_po'  			=> $tipo_po,
								'costo_inicial' 	=> $costo_inicial,
								'exceso_solicitado' => $exceso,
								'costo_final' 		=> $costo_final,
								'usuario_solicita'  => $idUsuario,
								'fecha_solicita' 	=> $fechaActual,
								'comentario_reg' 	=> $comentario,
								'idEstacion'    	=> $idEstacion,
								'origen'	     	=>	$origen,
								'url_archivo'    	=> null
								);

			if (count($_FILES) > 0) {
				$uploaddir =  'uploads/solicitud_paquetizado/'.$itemplan.'_'.$origen.'/';//ruta final del file
				$uploadfile = $uploaddir . basename($_FILES['file']['name']);
				if (! is_dir ( $uploaddir))
					mkdir ( $uploaddir, 0777 );
				
				if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
					$dataInsert['url_archivo'] = $uploadfile;
				}else {
					throw new Exception('Hubo un problema con la carga del archivo al servidor, comuniquese con el administrador.');
				}
			} else {
				throw new Exception('Subir el archivo de evidencia de exceso.');
			}
			
			if($codigo_po != null) {
				$countPendiente = $this->m_control_presupuestal->getCountValida($itemplan, $codigo_po);
				if($countPendiente > 0) {
					throw new Exception('Esta PO ya cuenta con una solicitud de exceso pendiente.');
				}
			}
			
			$data = $this->m_control_presupuestal->registrarSolicitudCP($dataInsert);
			
			$dataDetalleSolicitud = array();
			if($data['error'] == EXIT_SUCCESS) {
				if($origen == 6) {//EDICION PIN
					foreach($data_json as $row) {
						$arrayDetallePin = array(	"id_solicitud"                 => $data['id_solicitud'],
													"codigo_po" 				   => $row->ptr,
													"id_ptr_x_actividades_x_zonal" => $row->id_ptr_x_actividades_x_zonal,
													"idActividad" 				   => $row->id_actividad,
													"baremo"      				   => $row->baremo,
													"costo"       				   => $row->precio,
													"cantidad_inicial"	 		   => $row->cantidadInicial,
													"cantidad_final"   			   => $row->cantidad_final,
													"costo_kit"   			       => $row->costo_kit,
												    "costo_mat"                    => $row->costo_mat );
						array_push($dataDetalleSolicitud, $arrayDetallePin);							
					}
					
					if($codigo_po == null || $codigo_po == '') {
						throw new Exception('No ingreso la PO, comunicarse con el programador a cargo.');
					}
					
					$data = $this->m_control_presupuestal->regDetalleEditPin($dataDetalleSolicitud);
				} else if($origen == 4) {//LIQUIDACION MO
					if($codigo_po == null || $codigo_po == '') {
						throw new Exception('No ingreso la PO, comunicarse con el programador a cargo.');
					}
					$arrayActividades = array();
					foreach($data_json as $datos){
                        if($datos!=null){                        
							if(!in_array($datos->codigoPartida, $arrayActividades)){
								$dataCMO = array();
								$dataCMO['id_solicitud']     = $data['id_solicitud'];
								$dataCMO['codigo_po']        = $codigo_po;
								$dataCMO['codigoPartida']    = $datos->codigoPartida;
								$dataCMO['baremo']           = $datos->baremo;
								$dataCMO['preciario']        = $datos->preciario;
								$dataCMO['cantidadInicial']  = $datos->cantidadInicial;
								$dataCMO['montoInicial']     = $datos->montoInicial;
								$dataCMO['cantidadFinal']    = $datos->cantidadFinal;
								$dataCMO['montoFinal']       = $datos->montoFinal;
								array_push($dataDetalleSolicitud, $dataCMO);
								array_push($arrayActividades, $datos->codigoPartida);
							}                          
                        }
                    }
					$data = $this->m_control_presupuestal->regDetalleLiquiMo($dataDetalleSolicitud);
				} else if($origen == 1) {//REGISTRO PO MAT			
					
					foreach($data_json as $row) {
						if($row != null){
							if($row->codigo_material!=null){
								$arrayDetallePOTemp = array(
									"id_solicitud" 	   	=> $data['id_solicitud'],
									"codigo_material"  	=> $row->codigo_material,
									"cantidadInicial" 	=> $row->cantidadInicial,
									"cantidadFinal"   	=> $row->cantidadFinal,
									"costoMat"   		=> $row->costoMat,
									'montoFinal'		=> $row->montoFinal
								);
			
								array_push($dataDetalleSolicitud, $arrayDetallePOTemp);
							}
						}
					}
					if(count($dataDetalleSolicitud) == 0) {
						throw new Exception('No hay detalle de po para registrar la solicitud!!');
					}
					$data = $this->m_control_presupuestal->regDetalleRegPo($dataDetalleSolicitud);
				} else if($origen == 3) {//gestion vr				
					$infoPo = $this->m_utils->getInfoPoByCodigoPo($codigo_po);		
					$infoJefatura = $this->m_utils->getInfoJefaturaByItemplan($itemplan);
					$dataAlmCen = explode('|', $infoJefatura['dataJefaturaEmp']);
					foreach($data_json as $row) {				
						$row->id_solicitud 	= 	$data['id_solicitud'];
						$row->ptr			=	$codigo_po;
						$row->itemplan 		=	$itemplan;
						$row->vr 			=	$infoPo['vale_reserva'];		
						$row->idJefaturaSap	=	$dataAlmCen[2];									 
						array_push($dataDetalleSolicitud, $row);
					}

					$data = $this->m_control_presupuestal->regDetalleVr($dataDetalleSolicitud);
				} else if($origen == 2) {//REGISTRO PO MO				
					$arrayActividades = array();
                    foreach($data_json as $datos){
                        if($datos!=null){//log_message('error', print_r($datos,true));                        
							if(!in_array($datos->codigoPartida, $arrayActividades)){
								$dataCMO = array();
								$dataCMO['id_solicitud']    = $data['id_solicitud'];
								$dataCMO['codigoPartida']   = $datos->codigoPartida;
								$dataCMO['baremo']          = $datos->baremo;
								$dataCMO['preciario']       = $datos->preciario;
								$dataCMO['cantidadInicial'] = $datos->cantidadInicial;
								$dataCMO['montoInicial']    = $datos->montoInicial;
								$dataCMO['cantidadFinal']   = $datos->cantidadFinal;
								$dataCMO['montoFinal']      = $datos->montoFinal;
								array_push($dataDetalleSolicitud, $dataCMO);
								array_push($arrayActividades, $datos->codigoPartida);//metemos idActividad
							}
                        }
                    }
					$data = $this->m_control_presupuestal->regDetallePoMo($dataDetalleSolicitud);
				}
			}
			
			if($data['error'] == EXIT_ERROR) {
				throw new Exception($data['msj']);
			}
			
			$this->db->trans_commit();
            //$data['tablaBandejaSiom'] = $this->getTablaSiom(null,null,null,null,null,null);
        } catch (Exception $e) {
			$data['error'] = EXIT_ERROR;
			$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
 
	/**tipo_po = 1 mo, 2 mat */
	public function createPoRefoExpresPqt($itemplan, $nro_cto, $tipo_po)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try { 
            $this->db->trans_begin();
         //   $idEstacionPartidas =  ID_ESTACION_FO;//todo es FO INTEGRAL
		    $idUsuario      = $this->session->userdata('idPersonaSessionPan');
            $fechaActual 	= $this->m_utils->fechaActual();              
			$hasPoPqtACtive = $this->m_utils->hasPoPqtActive($itemplan, 5);
			
				$arrayInfo = $this->m_utils->getPlanObraByItemplan($itemplan);
				if($arrayInfo == null){
					throw new Exception('Hubo un error al traer la información del itemplan.');
				}
			 			 
				$codigoPO = $this->m_utils->getCodigoPO($itemplan);
				if($codigoPO == null || $codigoPO == '') {
					throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
				}
				if($tipo_po == 1){//MO
					$dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MO', 5);
				}else if($tipo_po == 2){
					$dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MAT', 5);
				}

				if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
					throw new Exception("No tiene configurado un area.");
				}

				if($tipo_po == 1){//MO
					$partidasTOPoPqt = $this->m_utils->getPartidasToPoPqtReforzamientoExpress($itemplan, $nro_cto);
					if($partidasTOPoPqt == null){
						throw new Exception('No se encontraron Partidas Configuradas para la obra.');
					}

					$arrayDetalleInsert = array();
					$costo_total_po = 0;
					foreach($partidasTOPoPqt as $dataPartidaPqt){

						$costo_partida = $dataPartidaPqt['baremo']*$dataPartidaPqt['costo']*$dataPartidaPqt['cantidad'];

						$detallePo = array (
							'codigo_po'        => $codigoPO,
							'codigoPartida'    => $dataPartidaPqt['codigoPartida'], //ESTADO REGISTRADO
							'baremo'           => $dataPartidaPqt['baremo'],
							'preciario'        => $dataPartidaPqt['costo'],
							'cantidadInicial'  => $dataPartidaPqt['cantidad'],
							'montoInicial'     => $costo_partida,
							'cantidadFinal'    => $dataPartidaPqt['cantidad'],
							'montoFinal'       => $costo_partida,
							'costoMo'          => $costo_partida
						);

						$costo_total_po	=	$costo_total_po	+	$costo_partida;
						array_push($arrayDetalleInsert, $detallePo);
					}
				 
					if(count($arrayDetalleInsert) == 0){
						throw new Exception("No hay partidas válidas para el registro de la PO");
					}

					$dataPo = array (
						'codigo_po'      => $codigoPO,
						'itemplan'       => $itemplan,
						'estado_po'      => ID_ESTADO_PO_REGISTRADO,
						'idEstacion'     => 5,
						'costo_total'    => $costo_total_po,
						'idUsuario'      => $idUsuario,
						'fechaRegistro'  => $fechaActual,
						'flg_tipo_area'  => 2,
						'idEmpresaColab' => $arrayInfo['idEmpresaColab'],
						'idArea'         => $dataSubEstacionArea['idArea'],
						'idSubProyecto'  => $arrayInfo['idSubProyecto'],
						'isPoPqt'        => 1
					);

					
					$dataLogPO =    array(
						'codigo_po'        =>  $codigoPO,
						'itemplan'         =>  $itemplan,
						'idUsuario'        =>  $idUsuario,
						'fecha_registro'   =>  $fechaActual,
						'idPoestado'       =>  ID_ESTADO_PO_REGISTRADO
					);

					$data = $this->m_utils->registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO);
					if($data['error'] == EXIT_ERROR) {
						throw new Exception($data['msj']);
					}
					$this->db->trans_commit();
				}else if($tipo_po	==	2){//MAT			 
					$materialesToPoPqt = $this->m_utils->getMaterialesToReforzamientoExpress($nro_cto);
					if($materialesToPoPqt == null){
						throw new Exception('No se encontraron Materiales Configuradas para la obra.');
					}					 

					$arrayDetalleInsert =	array();
					$costo_total_po	=	0;
					foreach($materialesToPoPqt as $material){
						$costo_material = $material['costo_material']*$material['cantidad'];

						$detallePo = array (
							'codigo_po'        => $codigoPO,
							'codigo_material'  => $material['codigo_material'], //ESTADO REGISTRADO
  							'cantidadInicial'  => $material['cantidad'],
 							'cantidadFinal'    => $material['cantidad'],
							'costoMat'         => $material['costo_material'],
							'montoFinal'       => $costo_material
						);

						$costo_total_po	=	$costo_total_po	+	$costo_material;
						array_push($arrayDetalleInsert, $detallePo);
					}


					$dataPo = array (
						'codigo_po'      => $codigoPO,
						'itemplan'       => $itemplan,
						'estado_po'      => ID_ESTADO_PO_PRE_APROBADO,
						'idEstacion'     => 5,
						'costo_total'    => $costo_total_po,
						'idUsuario'      => $idUsuario,
						'fechaRegistro'  => $fechaActual,
						'flg_tipo_area'  => 1,
						'idEmpresaColab' => $arrayInfo['idEmpresaColab'],
						'idArea'         => $dataSubEstacionArea['idArea'],
						'idSubProyecto'  => $arrayInfo['idSubProyecto']
					); 
					
					if(count($arrayDetalleInsert) == 0){
						throw new Exception("No hay materiales válidos para el registro de la PO");
					}
					$dataLogPO =    array(
						'codigo_po'        =>  $codigoPO,
						'itemplan'         =>  $itemplan,
						'idUsuario'        =>  $idUsuario,
						'fecha_registro'   =>  $fechaActual,
						'idPoestado'       =>  ID_ESTADO_PO_PRE_APROBADO
					); 

					$data = $this->m_utils->registrarPoMat($dataPo, $arrayDetalleInsert, $dataLogPO);
					if($data['error'] == EXIT_ERROR) {
						throw new Exception($data['msj']);
					}

					if($data['error'] == EXIT_SUCCESS){
						$this->db->trans_commit();
						$data['msj'] = 'Se creó la po mat exitosamente!!';
					}
				}
				
		
               
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
       return $data;
    }

}
