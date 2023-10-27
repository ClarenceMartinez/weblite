<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registrar_itemplan_madre extends CI_Controller {

    var $login;

    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 34, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['cmbFase'] = __buildComboFase();
            $data['cmbProyecto'] = __buildProyectoAll(52, 1);
            $this->load->view('vf_itemplan_madre/v_registrar_itemplan_madre',$data);        	  
    	 }else{
        	redirect(RUTA_OBRA2, 'refresh');
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
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function registrarItemplanMadre() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $this->db->trans_begin();

            $objJson = json_decode($this->input->post('objJson'), true);

            $itemplanM = $this->m_utils->generarItemMadre();
            $fechaActual = $this->m_utils->fechaActual();
            $idUsuario   = $this->session->userdata('idPersonaSessionPan');

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }

            $dataCentral = $this->m_utils->getCentralByIdCodigo($objJson['idCentral'], null);
            $arrayDataPreciario = $this->m_utils->getPreciarioPqt($objJson['idEmpresaColab'], $dataCentral['idJefatura'], 1);
			
			if($arrayDataPreciario['costo'] == null || $arrayDataPreciario['costo'] == '') {
				throw new Exception('No se encontro el costo preciario, verificar la configuracion');
			}
			
            $costoEstimado = $objJson['cantidad_uip']*0.19*$arrayDataPreciario['costo'];
            
            $objJson['itemplan_m']    = $itemplanM;
            $objJson['idEstado']      = 1;//PRE REGISTRO - CON SOL OC CREACION
            $objJson['fechaRegistro'] = $fechaActual;
            $objJson['idUsuarioLog']  = $idUsuario;
            $objJson['fechaLog']      = $fechaActual;
            $objJson['costoEstimado'] = $costoEstimado;

            unset($objJson['idProyecto']);
            $data = $this->m_utils->registrarItemplanMadre($objJson);

            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
			
            $rsp = $this->m_utils->generarSolicitudItemPlanMadre($itemplanM, $costoEstimado, $idUsuario, $objJson['codigoInversion']);

            if($rsp == 2) {
                $data['error'] = EXIT_ERROR;
                throw new Exception('No tiene monto disponible, verificar la pep.');
            }
            
            if($rsp == 5) {
                $data['error'] = EXIT_ERROR;
                throw new Exception('Ya cuenta con una solicitud, verificar.');
            }

            if($rsp == 6) {
                $data['error'] = EXIT_ERROR;
                throw new Exception('Complicaciones en la creacion de solicitud OC, verificar.');
            }

            $data['itemplan_m'] = $itemplanM;
            $this->db->trans_commit();
        } catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function regItemPlanMadreExterna() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
            $this->db->trans_begin();

            $nomMadre = $this->input->post('nomMadre');
            $idProyecto = $this->input->post('idProyecto');
            $idSubProyecto = $this->input->post('idSubProyecto');
            $inputCoordX = $this->input->post('inputCoordX');
            $inputCoordY = $this->input->post('inputCoordY');
            $selectEmpresaColab = $this->input->post('selectEmpresaColab');
            $cantUip = $this->input->post('textMonto');
            $selectPrioridad = $this->input->post('selectPrioridad');
			$idZonal         = $this->input->post('idZonal');
			
			$codigo_inversion = $this->input->post('codigo_inversion');
            // -- Grilla --//
            // IdOpex

            $itemplanM = $this->m_utils->generarItemMadre();
			$arrayDataPreciario = $this->m_utils->getPreciarioPqt($selectEmpresaColab, $idZonal);
			
			if($arrayDataPreciario['costo'] == null || $arrayDataPreciario['costo'] == '') {
				throw new Exception('No se encontro el costo preciario, verificar la configuracion');
			}
			
			$costoPreciario = $arrayDataPreciario['costo'];
			
			//BAREMO DISEÑO PQT 0.19
			$textMonto = $cantUip*0.19*$costoPreciario;
            $objReg = array(
                'itemplan_m' => $itemplanM,
                'idProyecto' => $idProyecto,
                'idSubProyecto' => $idSubProyecto,
                'fecha_registro' => $this->fechaActual(),
                'id_usuario' => $this->session->userdata('idPersonaSession'),
                'nombre' => $nomMadre,
                'coordenadaX' => $inputCoordX,
                'coordenadaY' => $inputCoordY,
                'idEmpresaColab' => $selectEmpresaColab,
                'costoEstimado' => $textMonto,
                'idEstado' => 2,
				'codigo_inversion' => $codigo_inversion,
				'cantidad_uip'     => $cantUip,
				'preciario'        => $costoPreciario,
				'baremo'           => 0.19,
				'idZonal'          => $idZonal
            );

            $data = $this->m_reg_itemplan_madre_externa->regItemMadre($objReg);

            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $rsp = $this->m_utils->generarSolicitudItemPlanMadrePan($itemplanM, $textMonto);

            if($rsp == 2) {
                $data['error'] = EXIT_ERROR;
                throw new Exception('No tiene monto disponible, verificar la pep.');
            }
            
            if($rsp == 5) {
                $data['error'] = EXIT_ERROR;
                throw new Exception('Ya cuenta con una solicitud, verificar.');
            }

            if($rsp == 6) {
                $data['error'] = EXIT_ERROR;
                throw new Exception('Complicaciones en la creacion de solicitud OC, verificar.');
            }

            $departamento = $this->input->post('txt_departamento');
            $provincia = $this->input->post('txt_provincia');
            $distrito = $this->input->post('txt_distrito');
            $fec_recepcion = $this->input->post('fecRecepcion');
            $nomCliente = $this->input->post('inputNomCli');
            $numCarta = NULL;
            $ano = $this->input->post('selectAno');
            $numCartaFin = NULL;
            $kickOff = NULL;

            $arrayDataOP = array(
                'itemplan' => $itemplanM,
                'departamento' => $departamento,
                'provincia' => $provincia,
                'distrito' => $distrito,
                'fecha_recepcion' => $fec_recepcion,
                'nombre_cliente' => $nomCliente,
                'numero_carta' => $numCarta,
                'ano' => $ano,
                'usuario_envio_carta' => $this->session->userdata('idPersonaSession'),
                'has_kickoff' => $kickOff,
                'estado_kickoff' => (($kickOff == 1) ? 'PENDIENTE' : null)
            );
            $data = $this->m_reg_itemplan_madre_externa->guardarDetalleItemMadre($arrayDataOP);

            if ($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $data['itemplanM'] = $itemplanM;
            $data['tbItemMadre'] = $this->getTbItemsMadre();

            $this->db->trans_commit();
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
}
