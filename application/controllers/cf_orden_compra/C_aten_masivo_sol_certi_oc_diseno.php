<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_aten_masivo_sol_certi_oc_diseno extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_orden_compra/m_aten_masivo_sol_certi_oc_diseno');
        $this->load->model('mf_orden_compra/m_bandeja_solicitud_oc');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        $this->load->library('excel');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario      = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');

	    if($idUsuario != null){

            list($html, $ctnValidos, $arrayFinal) = $this->makeHTMLTablaObservacion('');
            $data['tbObservacion'] = $html;

            $permisos = $this->session->userdata('permisosArbolPan');
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, 58, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_orden_compra/v_aten_masivo_sol_certi_oc_diseno',$data);        	  
    	 }else{
            redirect(RUTA_OBRA2, 'refresh');
	    }
    }

    public function makeHTMLTablaObservacion($objectExcel)
    {
        $html = '
                <table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
                            <th style="text-align: center; vertical-align: middle;">CÓDIGO SOLICITUD</th>
                            <th style="text-align: center; vertical-align: middle;">CODIGO CERTIFICACION</th>
                            <th style="text-align: center; vertical-align: middle;">ORDEN COMPRA</th>
                            <th style="text-align: center; vertical-align: middle;">COSTO SAP</th>                            
                            <th style="text-align: center; vertical-align: middle;">OBSERVACIÓN</th>
                        </tr>
                    </thead>

                    <tbody>';

        $count = 1;
        $arrayFinal = array();
        $ctnVal = 0;
        $arrayOC = array();
        if ($objectExcel != '') {
            $col = 1;
            foreach ($objectExcel->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();

                // $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                // $row_dimension = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $col = 0;
                    $codigoSolicitud = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $codigoSolicitud = _removeEnterYTabs(trim(utf8_encode(utf8_decode($codigoSolicitud)),'?'));
                    $col++;                  
                    $codigoCerti = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $codigoCerti = _removeEnterYTabs(trim(utf8_encode(utf8_decode($codigoCerti)),'?'));
                    $col++;

                    $ordenCompra = '';
                    $costoSap = '';
                    if(strlen($codigoSolicitud) == 0 || $codigoSolicitud == null || $codigoSolicitud == ''){
                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                    <th style="text-align:center;">
                                        ' . $count . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoSolicitud . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoCerti . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $ordenCompra . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $costoSap . '
                                    </th>
                                    <th>
                                        <span class="badge badge-danger badge-pill">CODIGO DE SOLICITUD INVÁLIDO</span>
                                    </th>
                                </tr>';
                    }else if($codigoCerti == null || $codigoCerti == ''){
                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                    <th style="text-align:center;">
                                        ' . $count . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoSolicitud . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoCerti . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $ordenCompra . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $costoSap . '
                                    </th>
                                    <th>
                                        <span class="badge badge-danger badge-pill">CODIGO CERTIFICACION INVÁLIDO</span>
                                    </th>
                                </tr>';
                    }else{
                        $infoSol = $this->m_aten_masivo_sol_certi_oc_diseno->getInfoSolicitudOCCreaByCodigo($codigoSolicitud);    
                        // pre($infoSol);               
                        if($infoSol != null && $infoSol['itemplan'] != null){
                            $ordenCompra = $infoSol['orden_compra'];
                            $costoSap    = $infoSol['costo_sap'];
                            if(!in_array($codigoSolicitud,$arrayOC)){
                                if($infoSol['estado'] == 1 && $infoSol['cant'] == 1){                                  
                                        $html .= '<tr id="tr' . $count . '" >
                                                    <th style="text-align:center;">
                                                    ' . $count . '
                                                    </th>
                                                    <th style="text-align:center; font-weight: bold;">
                                                        ' . $codigoSolicitud . '
                                                    </th>     
                                                    <th style="text-align:center; font-weight: bold;">
                                                        ' . $codigoCerti . '
                                                    </th>                                             
                                                    <th style="text-align:center; font-weight: bold;">
                                                        ' . $ordenCompra . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $costoSap . '
                                                    </th>                                                 
                                                    <th>
                                                        <span class="badge badge-success badge-pill">OK</span>
                                                    </th>
                                                </tr>';
                                        $dataTemp = array(
                                            $codigoSolicitud, 
                                            $codigoCerti,                                      
                                            $ordenCompra,
                                            $costoSap,                                         
                                            $infoSol['itemplan'], $infoSol['idEstadoPlan'], $infoSol['idSubProyecto'], $infoSol['idTipoPlanta'], $infoSol['paquetizado_fg']
                                        );
                                        $arrayFinal []= $dataTemp;
                                        $ctnVal++;                                   
                                }else{//IVALIDO ATENDIDA O CANCELADA
                                    if($infoSol['cant'] == 0 || $infoSol['cant'] == null){
                                        $msj = 'SOLICITUD SIN ITEMPLAN ASOCIADO';
                                    }else if($infoSol['cant'] > 1){
                                        $msj = 'SOLICITUD CON MAS DE 1 ITEMPLAN ASOCIADO';
                                    }else if($infoSol['estado'] == 2){
                                        $msj = 'SOLICITUD ATENDIDA';
                                    }else if($infoSol['estado'] == 3){
                                        $msj = 'SOLICITUD CANCELADA';
                                    }

                                    $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                                <th style="text-align:center;">
                                                    ' . $count . '
                                                </th>
                                                <th style="text-align:center; font-weight: bold;">
                                                    ' . $codigoSolicitud . '
                                                </th>                     
                                                <th style="text-align:center; font-weight: bold;">
                                                    ' . $codigoCerti . '
                                                </th>                         
                                                <th style="text-align:center; font-weight: bold;">
                                                    ' . $ordenCompra . '
                                                </th>
                                                <th style="text-align:center;">
                                                    ' . $costoSap . '
                                                </th>                                               
                                                <th>
                                                    <span class="badge badge-danger badge-pill">'.$msj.'</span>
                                                </th>
                                            </tr>';
                                }
                                $arrayOC[]= $codigoSolicitud;
                            }else{
                                $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                            <th style="text-align:center;">
                                                ' . $count . '
                                            </th>
                                            <th style="text-align:center; font-weight: bold;">
                                                ' . $codigoSolicitud . '
                                            </th>                        
                                            <th style="text-align:center; font-weight: bold;">
                                                ' . $codigoCerti . '
                                            </th>                    
                                            <th style="text-align:center; font-weight: bold;">
                                                ' . $ordenCompra . '
                                            </th>
                                            <th style="text-align:center;">
                                                ' . $costoSap . '
                                            </th>                                          
                                            <th>
                                                <span class="badge badge-danger badge-pill">SOLICITUD REPETIDA</span>
                                            </th>
                                        </tr>';
                            }

                        }else{//INVALIDO SOLICITUD NO EXISTE
                            $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                        <th style="text-align:center;">
                                            ' . $count . '
                                        </th>
                                        <th style="text-align:center; font-weight: bold;">
                                            ' . $codigoSolicitud . '
                                        </th>                   
                                        <th style="text-align:center; font-weight: bold;">
                                            ' . $codigoCerti . '
                                        </th>                   
                                        <th style="text-align:center; font-weight: bold;">
                                            ' . $ordenCompra . '
                                        </th>
                                        <th style="text-align:center;">
                                            ' . $costoSap . '
                                        </th>                                        
                                        <th>
                                            <span class="badge badge-danger badge-pill">SOLICITUD NO RECONOCIDA</span>
                                        </th>
                                    </tr>';
                        }
                        
                    }

                    $count++;
                }
            }

            $html .= '</tbody>
                </table>';

        } else {
            $html .= '</tbody>
                </table>';
        }

        return array($html, $ctnVal, $arrayFinal);
    }
    public function procesarFileMasivoAtenSolCreaOc()
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

            if (!file_exists("./uploads/aten_masivo_sol_oc_edicion_pan")) {
                if (!mkdir("./uploads/aten_masivo_sol_oc_edicion_pan")) {
                    throw new Exception('Hubo un error al crear la carpeta aten_masivo_sol_oc_edicion_pan!!');
                }
            }

            $objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);

            list($html, $contador, $arrayFinal) = $this->makeHTMLTablaObservacion($objectExcel);
            $data['titulo'] = 'Se muestran la cantidad registros a cargar ('.$contador.') ';
            $data['tbObservacion'] = $html;
            $data['jsonDataFile'] = json_encode($arrayFinal);
            $data['msj']  = 'Se procesó correctamente el archivo!!';
            $data['error']  = EXIT_SUCCESS;

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


    public function updateMasivoSolCreacionOC()
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
                $arrayUpdateSolicitud      = array();               
                $arrayUpdatePlanObra       = array();
                $arraySolicitudes          = array();

                foreach ($arrayDataFile as $datos) {
                    $codigoSolicitud    = $datos[0];    
                    $codigo_certi       = $datos[1];          
                    $orden_compra       = $datos[2];
                    $costo_sap          = $datos[3];
                    $itemplan           = $datos[4];  
                    $idEstadoPlan       = $datos[5];                                            
             
                    //SOL CERTIFICACION
                    $objSolicitud = array();
                    $objSolicitud['codigo_solicitud']       = $codigoSolicitud;
                    $objSolicitud['estado']                 = 2;
                    $objSolicitud['usuario_valida']         = $idUsuario;
                    $objSolicitud['fecha_valida']           = $fechaActual;
                    $objSolicitud['codigo_certificacion']   = $codigo_certi;
                    $arrayUpdateSolicitud[] = $objSolicitud;

                    //PLANOBRA
                    $objPlanObra = array();
                    $objPlanObra['itemplan']                    = $itemplan;
                    $objPlanObra['solicitud_oc_certi_diseno']   = $codigoSolicitud;
                    $objPlanObra['estado_oc_certi_diseno']      = 'ATENDIDO';
                    
                    $arrayUpdatePlanObra[] = $objPlanObra;           
                    
                    $arraySolicitudes[] = $codigoSolicitud;
                }

                // pre($arrayUpdatePlanObra);
                $data = $this->m_aten_masivo_sol_certi_oc_diseno->atencionSolicitudOcEdi($arrayUpdateSolicitud, $arrayUpdatePlanObra, $arraySolicitudes, $idUsuario);
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