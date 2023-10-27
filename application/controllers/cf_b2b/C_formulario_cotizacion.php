<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_formulario_cotizacion extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_b2b/m_cotizacion_b2b');
        $this->load->model('mf_orden_compra/m_asociacion_oc_cotizacion_b2b');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){      
            $codigo        = (isset($_GET['cod'])    ? $_GET['cod'] : '');     
            $flg_principal = (isset($_GET['flg_principal']) ? $_GET['flg_principal'] : '');//COMO DETECTAR PRINCIPAL  O NO
            $estado        = (isset($_GET['estado'])    ? $_GET['estado'] : '');     
            if($codigo!=null){
                $infoSol = $this->m_asociacion_oc_cotizacion_b2b->getInfoClusterToPutOC($codigo);
                if($infoSol['orden_compra'] != null){
                    if($infoSol['estado'] ==  0){ //pendiente de cotizacion
                        $permisos =  $this->session->userdata('permisosArbolPan');         
                        $result = $this->lib_utils->getHTMLPermisos($permisos, 42, null, 44, null);
                        $data['opciones'] = $result['html'];
                        $data['header'] = $this->lib_utils->getHeader();
    
    
                        $data['costo_pqt_mo'] = 0;//tmp
                        $data['flg_catv'] = 0;//tmp
    
                        $arrayDataPqt = $this->m_utils->getDataPqtCostoByCodCoti($codigo);
                        $data['flg_distancia_lineal'] = (($arrayDataPqt['distancia_lineal']+($arrayDataPqt['distancia_lineal']) * 0.30) > 4000 ? 1 : 0);
    
                        $data['codigo']          =   $codigo;
                        $data['flg_principal']   = $flg_principal;
                        $data['arrayTipoDiseno'] = $this->m_utils->getTipoDiseno(NULL);
                    //  log_message('error', $data['arrayTipoDiseno']);
                        $idEECC = $this->session->userdata('idEmpresaColabSesion');		 
                        $this->load->view('vf_b2b/v_formulario_cotizacion',$data);   
                    }else{
                        redirect('cotib2b','refresh');
                    }
                }else{
                    redirect('cotib2b','refresh');
                }
                
            }else{
                redirect('login','refresh');
            }     	  
    	 }else{
        	redirect('login', 'refresh');
	    }     
    }

    function getCentralByTipoRed() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {

            //$idEECC  = $this->session->userdata('idEmpresaColabSesion');		 
            $idEECC = null;
            $data['cmbCentral'] = '<option>&nbsp;</option>'.__buildComboCentralToCotiB2b($idEECC);
        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getDataSeiaMtc() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $dia  = null;
            $seia = null;
            $mtc  = null;
            $inc  = null;

            $totalMetros = $this->input->post('totalMetros');
            $idCentral   = $this->input->post('idCentral');
                
            if($totalMetros != null && $idCentral != null) {
                $arrayDataCentral = $this->m_utils->getDataCentralById($idCentral);

                $arrayData = $this->m_utils->getDiasMatriz($totalMetros, $seia, $mtc, $inc, $arrayDataCentral['flg_tipo_zona'], $arrayDataCentral['jefatura']);

                $seia = $arrayData['seia'];
                $mtc  = $arrayData['mtc'];
            }

            $data['seia'] = $seia;
            $data['mtc']  = $mtc;
        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
 
    function getEbcByDistritoByDistrito() {
        $idCentral = $this->input->post('idCentral');

        $dataArray = $this->m_utils->getDataCentralPqtById($idCentral);

        $cmbEbc = __buildComboEBCs($dataArray['departamento']);
		
        $data['cmbEbc'] = $cmbEbc;
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getDiasMatriz() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $dia  = null;
            $seia = null;
            $mtc  = null;
            $inc  = null;

            $totalMetros = $this->input->post('totalMetros');
            $idCentral   = $this->input->post('idCentral');
            $seia        = $this->input->post('seia');
            $mtc         = $this->input->post('mtc');
            $inc         = $this->input->post('inc');
                
            if($totalMetros != null && $idCentral != null) {
                $arrayDataCentral = $this->m_utils->getDataCentralById($idCentral);

                $arrayData = $this->m_utils->getDiasMatriz($totalMetros, $seia, $mtc, $inc, $arrayDataCentral['flg_tipo_zona'], $arrayDataCentral['jefatura']);
                
                $dia  = $arrayData['dias'];
            }

            $data['dia']  = $dia;
        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function sendCotizacionIndividual(){
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try{
         //   $this->db->trans_begin();
			
			$tipoRed        = $this->input->post('selectTipoRed');
            $nodoPrincipal  = $this->input->post('selectCentral');    
            $nodoRespaldo   = $this->input->post('selectCentral2');
            //$facilidadRed   = $this->input->post('inputFacRed');
            $facilidadRed   = $this->input->post('facilidad_n');
            $metroTendidoAe = $this->input->post('inputMetroTenAereo');
            $metroTendidoSub = $this->input->post('inputMetroTenSubt');
            $metroCanali    = $this->input->post('inputMetroCana');
            $cantCamaraNue  = $this->input->post('cantCamaNue');
            $cantPostesNue  = $this->input->post('inputPostNue');
            $cantPostesApo  = $this->input->post('inputCantPostApo');
            $requiereSeia   = $this->input->post('reqSia');
            $requeAproMtc   = $this->input->post('reqMtc');
            $requeAproInc   = $this->input->post('reqInc');

            $nodoPrincipalDesc = $this->input->post('nodoPrincipal');
            $nodoRespaldoDesc  = $this->input->post('nodoRespaldo');
            
            $costoMat       = $this->input->post('inputCostoMat');
            $costmoMo       = floatval($this->input->post('costoMoPqt'));
            $costoDiseno    = 0;           
            $costoExpe      = floatval($this->input->post('costoEIA'));
            $costoAdic      = floatval($this->input->post('costoAdicZon'));
            $costoTotalMo   = $costmoMo+$costoExpe+$costoAdic;
            $costoMOSisego  = $costmoMo+$costoExpe+$costoAdic;
            $idTipoDiseno   = $this->input->post('cmbTipoDiseno');
            $duracion       = $this->input->post('duracion');
            $codigo         = $this->input->post('codigo');
            $filePerfil     = $this->input->post('filePerfil');
            $fileSisego     = $this->input->post('fileSisego');
            $fileRutas      = $this->input->post('fileRutas');
            $cantAperCamara = $this->input->post('inputCantAperCamara');
            $flgPrincipal   = $this->input->post('flgPrincipal');
            $tiempoEjec     = 30;//30 DIAS SISEGO ESTANDAR
            $comentario     = $this->input->post('textareaComentario');
            $tipo_enlace_2  = $this->input->post('cmbTipoEnlace');

            $uploaddir      = 'uploads/sisego/cotizacion_individual/'.$codigo.'/';//ruta final del file Tss
            $pathPerfil     = $uploaddir .'PERFIL/';
            $pathSisego     = $uploaddir .'SISEGO/';
            $pathRuta       = $uploaddir .'RUTAS/';
            $uploadfilePerfil =  $pathPerfil.basename($_FILES['filePerfil']['name']);
            $uploadfileSisego =  $pathSisego.basename($_FILES['fileSisego']['name']);
            $uploadfileRutas  =  $pathRuta.basename($_FILES['fileRutas']['name']);

            $nodoP = explode('-', $nodoPrincipalDesc);
            $nodoR = explode('-', $nodoRespaldoDesc);
            
            $count = $this->m_cotizacion_b2b->getCountConfirmaSisego($codigo);
			
            $flg_ebc = $this->input->post('flg_ebc');
            $costoOc = floatval($this->input->post('costoOc'));
			
			$flg_catv  = $this->input->post('flg_catv');
			
			$flg_nodo_otro_op = $this->input->post('flg_nodo_otro_op');
			$nodo_otro_op 	  = $this->input->post('nodo_otro_op');
			
			$fechaActual = $this->m_utils->fechaActual();

            //nuevos campos
            $cantCto            = $this->input->post('inputCantCTO');
            $txtDivcau          = $this->input->post('txtDivcau');
            $txtEmpal1632       = $this->input->post('txtEmpal1632');
            $txtEmpal64         = $this->input->post('txtEmpal64');
            $txtEmpal128        = $this->input->post('txtEmpal128');
            $txtEmpal256        = $this->input->post('txtEmpal256');
            $txtCruceta         = $this->input->post('txtCruceta');
            $txtcapotel         = $this->input->post('txtcapotel');
            $selectOperador     = $this->input->post('selectOperador');
            $txtcaPosElec       = $this->input->post('txtcaPosElec');
            $selectEmpresaElec  = $this->input->post('selectEmpresaElec');
            $txtDuctoN2         = $this->input->post('txtDuctoN2');
            $txtDuctoN3         = $this->input->post('txtDuctoN3');
            $txtDuctoN4         = $this->input->post('txtDuctoN4');
            $selectOperaSubte   = $this->input->post('selectOperaSubte');
            
            /*
            log_message('error', '----------------------------------');
            log_message('error', $cantCto);
            log_message('error', $txtDivcau);
            log_message('error', $txtEmpal1632);
            log_message('error', $txtEmpal64);
            log_message('error', $txtEmpal128);
            log_message('error', $txtEmpal256);
            log_message('error', $txtCruceta);
            log_message('error', $txtcapotel);
            log_message('error', $selectOperador);
            log_message('error', $txtcaPosElec);
            log_message('error', $selectEmpresaElec);
            log_message('error', $txtDuctoN2);
            log_message('error', $txtDuctoN3);
            log_message('error', $txtDuctoN4);
            log_message('error', $selectOperaSubte);
            log_message('error', '----------------------------------');
            */
            $puntos_apoyo  = 0;

            if($cantCto > 0){
                $puntos_apoyo = $puntos_apoyo + ($cantCto*2.7);
            }
            if($txtDivcau > 0){
                $puntos_apoyo   = $puntos_apoyo + ($txtDivcau * 5.5);
            }
            if($txtEmpal1632 > 0){
                $puntos_apoyo   = $puntos_apoyo + ($txtEmpal1632 * 3.2);
            }
            if($txtEmpal64 > 0){
                $puntos_apoyo   = $puntos_apoyo + ($txtEmpal64 * 4.7);
            }
            if($txtEmpal128 > 0){
                $puntos_apoyo   = $puntos_apoyo + ($txtEmpal128 * 5.5);
            }
            if($txtEmpal256 > 0){
                $puntos_apoyo   = $puntos_apoyo + ($txtEmpal256 * 5.5);
            }
            if($txtCruceta > 0){
                $puntos_apoyo   = $puntos_apoyo + ($txtCruceta * 1);
            }
            if($txtcapotel > 0){
                $puntos_apoyo   = $puntos_apoyo + ($txtcapotel * 1);
            }
            log_message('error', $puntos_apoyo);
			if($flg_nodo_otro_op == 1) {
				if($nodo_otro_op == null || $nodo_otro_op == '') {
					throw new Exception('Seleccionar NODO OTRO OPERADOR');
				}
			} else {/*
				if($flg_ebc == null || $flg_ebc == '') {
					throw new Exception('Seleccionar si tiene EBC');
				}*/
			}
			
            

			if($costoTotalMo == 0 || $costoTotalMo == '' || $costoTotalMo == null) {
				throw new Exception('Debe ingresar el costo MO.');    
			}

			if($costoMat == 0 || $costoMat == '' || $costoMat == null) {
				throw new Exception('Debe ingresar el costo MAT.');    
			}
			
            if($count >= 1) {
                throw new Exception('Ya tiene formulario registrado.');    
            }
            
            $nodoPrincipalDesc = $nodoP[0];
            $nodoRespaldoDesc  = $nodoR[0];
            if($nodoPrincipal == null || $nodoPrincipalDesc == null) {
                throw new Exception('Debe ingresar el Nodo');    
            }
            
            if($idTipoDiseno == null || $idTipoDiseno == null) {
                throw new Exception('Debe seleccionar tipo de dise&ntilde;o.');  
            }
            
            if($duracion == null) {
                throw new Exception('Debe ingresar la duraci&oacute;n');   
            }
            
            if($requeAproMtc    ==  'SI' || $requiereSeia == 'SI'){
                $tiempoEjec = 60;
            }
            if($requeAproInc    ==  'SI'){
                $tiempoEjec = 90;
            }
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            if($idUsuario == null || $idUsuario == '') {
                 throw new Exception('La sesi&oacute;n espir&oacute;, recargue la p&aacute;gina.');   
            }
			
            if($flgPrincipal == null || $flgPrincipal == '') {
                throw new Exception('error Principal, comunicarse con el administrador.');   
            }
			
			if($tipoRed == null || $tipoRed == '') {
				throw new Exception('Debe seleccionar un tipo de red.');   
			}
			$troba     = null;
			$cantTroba = null;
			/* HFC DESCOMENTAR CUANDO SE PIDA SUBIR */
			if($flg_catv == 1) {
				$troba     = $this->input->post('troba');
				$cantTroba = $this->input->post('cantTroba');
				
				if($troba == null || $troba == '') {
					throw new Exception('No ingreso la troba, verificar.');   
				}
				
				if($cantTroba == null || $cantTroba == '') {
					throw new Exception('No ingreso la cant. troba, verificar.');   
				}
			}
			
			$codEbc = 'xxxx';
            $tipo   = null;
         
			$dataCotizacion = $this->m_utils->getDataCotizacionByCodigo($codigo, $costoTotalMo, $costoMat);
			
			if($dataCotizacion['flg_mayor_mo'] == 1 ){
				throw new Exception('La cotizacion es una habilitacion de hilo, el costo total de MO es muy elevado ');
			}
			
			if($dataCotizacion['flg_mayor_mat'] == 1 ){
				throw new Exception('La cotizacion es una habilitacion de hilo, el costo total de MAT es muy elevado');
			}
			
			if($dataCotizacion['tipo_enlace'] == 'FIBRA OSCURA' && $idTipoDiseno != 5) {
				throw new Exception('La obra es de tipo FIBRA OSCURA, DEBE SELECCIONAR EL TIPO DE DISENO QUE CORRESPONDE (F. OSCURA)');
			}
			
			if (is_dir ( $uploaddir)){
                $this->rmDir_rf($uploaddir);
            }
			
            if (!is_dir ( $uploaddir))
            mkdir ( $uploaddir, 0777 );

            if (!is_dir ( $pathPerfil))
            mkdir ( $pathPerfil, 0777 );

            if (!is_dir ( $pathSisego))
            mkdir ( $pathSisego, 0777 );

            if (!is_dir ( $pathRuta))
            mkdir ( $pathRuta, 0777 );
        
            $total = $costoTotalMo+$costoMat;
            if (move_uploaded_file($_FILES['filePerfil']['tmp_name'], $uploadfilePerfil) && move_uploaded_file($_FILES['fileSisego']['tmp_name'], $uploadfileSisego)
                && move_uploaded_file($_FILES['fileRutas']['tmp_name'], $uploadfileRutas)) {

                    //if($total < 15000) {
                        $estado_co = 1;//no necesita aprob
                    /*}else{
                        $estado_co = 4;//necesita aprob
                    }*/

                    $dataUpdate = array(
                                        'nodo_principal'              => $nodoPrincipal,
                                        'nodo_respaldo'               => $nodoRespaldo,
                                        'facilidades_de_red'          => $facilidadRed,
                                        'cant_cto'                    => $cantCto,
                                        'metro_tendido_aereo'         => $metroTendidoAe,
                                        'metro_tendido_subterraneo'   => $metroTendidoSub,
                                        'metors_canalizacion'         => $metroCanali,
                                        'cant_camaras_nuevas'         => $cantCamaraNue,
                                        'cant_postes_nuevos'          => $cantPostesNue,
                                        'cant_postes_apoyo'           => $cantPostesApo,
                                        'requiere_seia'               => $requiereSeia,
                                        'requiere_aprob_mml_mtc'      => $requeAproMtc,
                                        'requiere_aprob_inc'          => $requeAproInc,
                                        'costo_materiales'            => $costoMat,
                                        'costo_mano_obra'             => $costmoMo,
                                        'costo_diseno'                => $costoDiseno,
                                        'costo_expe_seia_cira_pam'    => $costoExpe,
                                        'costo_adicional_rural'       => $costoAdic,
                                        'costo_total'                 => ($costoTotalMo+$costoMat+$costoOc),
                                        'tiempo_ejecu_planta_externa' => $tiempoEjec,
                                        'estado'                      => $estado_co,
                                        'fecha_envio_bandeja_val'     => $fechaActual,
                                        'usuario_envio_bandeja_val'   => $idUsuario,
                                        'duracion'                    => $duracion,
                                        'id_tipo_diseno'              => $idTipoDiseno,
                                        'ubic_perfil'                 => $uploadfilePerfil,
                                        'ubic_sisego'                 => $uploadfileSisego,
                                        'ubic_rutas'                  => $uploadfileRutas,
                                        'cant_apertura_camara'        => $cantAperCamara,
                                        //'idCentral'                   => $idCentral,
                                        'flg_rech_conf_ban_conf'      => 0,
                                        'comentario'                  => $comentario,
                                        'tipo'                        => $tipo,
                                        'costo_oc'                    => $costoOc,
                                        'id_usua_reg_cotizacion'      => $idUsuario,
                                        'fecha_reg_cotizacion'        => $fechaActual,
                                        'troba'						  => $troba,
                                        'cant_troba'			      => $cantTroba,
                                        'tipo_red'                    => $tipoRed,
                                        'flg_nodo_otro_operador'      => $flg_nodo_otro_op,
                                        'nodo_otro_operador'          => $nodo_otro_op,
                                        'tipo_enlace_2'               => $tipo_enlace_2,
                                        'cant_divicau'                => $txtDivcau,
                                        'cant_empame_1632'            => $txtEmpal1632,
                                        'cant_empalme_64'             => $txtEmpal64,
                                        'cant_empalme_128'            => $txtEmpal128,
                                        'cant_empalme_256'            => $txtEmpal256,
                                        'cant_cruceta'                => $txtCruceta,
                                        'cant_postes_telefonico'      => $txtcapotel,
                                        'operador_aereo'              => $selectOperador,
                                        'cant_postes_electricos'      => $txtcaPosElec,
                                        'empresa_electrica'           => $selectEmpresaElec,
                                        'cant_ducto_2_pul'            => $txtDuctoN2,
                                        'cant_ducto_3_pul'            => $txtDuctoN3,
                                        'cant_ducto_4_pul'            => $txtDuctoN4,
                                        'operador_subte'              => $selectOperaSubte,
                                        'cant_puntos_apoyo'           => $puntos_apoyo
                                    );
             
                   $dataLog = array('codigo_cl'        =>  $codigo,
                                    'estado'           =>  'COTIZADO',
                                    'usuario_registro' =>  $idUsuario,
                                    'fecha_registro'   =>  $fechaActual);

                  $data = $this->m_cotizacion_b2b->updateClusterPadre($codigo, $dataUpdate, $dataLog);
            }
            $data['codigo'] = $codigo;
        }catch(Exception $e){
			$data['error'] = EXIT_ERROR;
		//	$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
		//log_message('error',print_r($data, true));
        echo json_encode(array_map('utf8_encode', $data));
    }
 
    function getFacilidadesRedByIdCentral() {
        $idCentral      = $this->input->post('idCentral');
        $cmbFacilidades = __buildComboFaciByCentral($idCentral);		
        $data['cmbFacilidades'] = $cmbFacilidades;
        echo json_encode(array_map('utf8_encode', $data));
    }
	
	function rmDir_rf($carpeta)
    {		
      foreach(glob($carpeta . "/*") as $archivos_carpeta){             
        if (is_dir($archivos_carpeta)){
          $this->rmDir_rf($archivos_carpeta);
        } else {
        unlink($archivos_carpeta);
        }
      }
      rmdir($carpeta);
     }

}
