<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_aten_masivo_sol_crea_oc_diseno extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_orden_compra/m_aten_masivo_sol_crea_oc_diseno');
        $this->load->model('mf_orden_compra/m_bandeja_solicitud_oc_diseno');
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, ID_ATEN_SOL_OC_CREA_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_orden_compra/v_aten_masivo_sol_crea_oc_diseno',$data);        	  
    	 }else{
            redirect(RUTA_OBRA2, 'refresh');
	    }
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

            if (!file_exists("./uploads/aten_masivo_sol_oc_creacion_pan")) {
                if (!mkdir("./uploads/aten_masivo_sol_oc_creacion_pan")) {
                    throw new Exception('Hubo un error al crear la carpeta aten_masivo_sol_oc_creacion_pan!!');
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

    public function makeHTMLTablaObservacion($objectExcel)
    {
        $html = '
                <table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
                            <th style="text-align: center; vertical-align: middle;">CÓDIGO SOLICITUD</th>
                            <th style="text-align: center; vertical-align: middle;">SOLPAD</th>
                            <th style="text-align: center; vertical-align: middle;">ORDEN COMPRA</th>
                            <th style="text-align: center; vertical-align: middle;">COSTO SAP</th>
                            <th style="text-align: center; vertical-align: middle;">POSICIÓN</th>
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
                    $solPad = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $solPad = _removeEnterYTabs(trim(utf8_encode(utf8_decode($solPad)),'?'));
                    $col++;
                    $ordenCompra = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $ordenCompra = _removeEnterYTabs(trim(utf8_encode(utf8_decode($ordenCompra)),'?'));
                    $col++;
                    $costoSap = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $costoSap = _removeEnterYTabs(trim(utf8_encode(utf8_decode($costoSap)),'?'));
                    $col++;
                    $posicion = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $posicion = _removeEnterYTabs(trim(utf8_encode(utf8_decode($posicion)),'?'));
                    $col++;

                    if(strlen($codigoSolicitud) == 0 || $codigoSolicitud == null || $codigoSolicitud == ''){
                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                    <th style="text-align:center;">
                                        ' . $count . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoSolicitud . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $solPad . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $ordenCompra . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $costoSap . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $posicion . '
                                    </th>
                                    <th>
                                        <span class="badge badge-danger badge-pill">CODIGO DE SOLICITUD INVÁLIDO</span>
                                    </th>
                                </tr>';
                    }else if(strlen($solPad) == 0 || $solPad == null || $solPad == ''){
                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                        <th style="text-align:center;">
                                            ' . $count . '
                                        </th>
                                        <th style="text-align:center; font-weight: bold;">
                                            ' . $codigoSolicitud . '
                                        </th>
                                        <th style="text-align:center;">
                                            ' . $solPad . '
                                        </th>
                                        <th style="text-align:center; font-weight: bold;">
                                            ' . $ordenCompra . '
                                        </th>
                                        <th style="text-align:center;">
                                            ' . $costoSap . '
                                        </th>
                                        <th style="text-align:center;">
                                            ' . $posicion . '
                                        </th>
                                        <th>
                                            <span class="badge badge-danger badge-pill">SOLPAD INVÁLIDO</span>
                                        </th>
                                </tr>';
                    }else if(strlen($ordenCompra) == 0 || $ordenCompra == null || $ordenCompra == '' || !is_numeric($ordenCompra)){
                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                    <th style="text-align:center;">
                                        ' . $count . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoSolicitud . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $solPad . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $ordenCompra . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $costoSap . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $posicion . '
                                    </th>
                                    <th>
                                        <span class="badge badge-danger badge-pill">ORDEN DE COMPRA INVÁLIDO</span>
                                    </th>
                                </tr>';
                    }else if($costoSap == null || $costoSap == '' || !is_numeric($costoSap)){
                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                    <th style="text-align:center;">
                                        ' . $count . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoSolicitud . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $solPad . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $ordenCompra . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $costoSap . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $posicion . '
                                    </th>
                                    <th>
                                        <span class="badge badge-danger badge-pill">COSTO SAP INVÁLIDO</span>
                                    </th>
                                </tr>';
                    }else if($posicion == null || $posicion == '' || !is_numeric($posicion)){
                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                    <th style="text-align:center;">
                                        ' . $count . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoSolicitud . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $solPad . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $ordenCompra . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $costoSap . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $posicion . '
                                    </th>
                                    <th>
                                        <span class="badge badge-danger badge-pill">POSICIÓN INVÁLIDO</span>
                                    </th>
                                </tr>';
                    }else{
                        // $arrayInfo = $this->m_registro_masivo_sol_crea_oc_pan->getInfoItemplan($itemplan);
                        $infoSol = $this->m_aten_masivo_sol_crea_oc_diseno->getInfoSolicitudOCCreaByCodigo($codigoSolicitud);
                        // pre($infoSol);
                        if($infoSol != null && $infoSol['itemplan'] != null){
                            if(!in_array($ordenCompra,$arrayOC)){
                                if($infoSol['estado'] == 1 && $infoSol['cant'] == 1){
                                    $hasOcActiva = $this->m_aten_masivo_sol_crea_oc_diseno->existeItemplanConOC($ordenCompra);
                                    if($hasOcActiva == 0){
                                        $html .= '<tr id="tr' . $count . '" >
                                                    <th style="text-align:center;">
                                                    ' . $count . '
                                                    </th>
                                                    <th style="text-align:center; font-weight: bold;">
                                                        ' . $codigoSolicitud . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $solPad . '
                                                    </th>
                                                    <th style="text-align:center; font-weight: bold;">
                                                        ' . $ordenCompra . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $costoSap . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $posicion . '
                                                    </th>
                                                    <th>
                                                        <span class="badge badge-success badge-pill">OK</span>
                                                    </th>
                                                </tr>';
                                        $dataTemp = array(
                                            $codigoSolicitud,
                                            $solPad,
                                            $ordenCompra,
                                            $costoSap,
                                            $posicion,
                                            $infoSol['itemplan'], $infoSol['idEstadoPlan'], $infoSol['idSubProyecto'], $infoSol['idTipoPlanta'], $infoSol['paquetizado_fg']
                                        );
                                        $arrayFinal []= $dataTemp;
                                        $ctnVal++;
                                    }else{
                                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                                    <th style="text-align:center;">
                                                        ' . $count . '
                                                    </th>
                                                    <th style="text-align:center; font-weight: bold;">
                                                        ' . $codigoSolicitud . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $solPad . '
                                                    </th>
                                                    <th style="text-align:center; font-weight: bold;">
                                                        ' . $ordenCompra . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $costoSap . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $posicion . '
                                                    </th>
                                                    <th>
                                                        <span class="badge badge-danger badge-pill">LA OC YA SE ENCUENTRA EN UN ITEMPLAN</span>
                                                    </th>
                                                </tr>';
                                    }
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
                                                <th style="text-align:center;">
                                                    ' . $solPad . '
                                                </th>
                                                <th style="text-align:center; font-weight: bold;">
                                                    ' . $ordenCompra . '
                                                </th>
                                                <th style="text-align:center;">
                                                    ' . $costoSap . '
                                                </th>
                                                <th style="text-align:center;">
                                                    ' . $posicion . '
                                                </th>
                                                <th>
                                                    <span class="badge badge-danger badge-pill">'.$msj.'</span>
                                                </th>
                                            </tr>';
                                }
                                $arrayOC[]= $ordenCompra;
                            }else{
                                $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                            <th style="text-align:center;">
                                                ' . $count . '
                                            </th>
                                            <th style="text-align:center; font-weight: bold;">
                                                ' . $codigoSolicitud . '
                                            </th>
                                            <th style="text-align:center;">
                                                ' . $solPad . '
                                            </th>
                                            <th style="text-align:center; font-weight: bold;">
                                                ' . $ordenCompra . '
                                            </th>
                                            <th style="text-align:center;">
                                                ' . $costoSap . '
                                            </th>
                                            <th style="text-align:center;">
                                                ' . $posicion . '
                                            </th>
                                            <th>
                                                <span class="badge badge-danger badge-pill">ORDEN DE COMPRA REPETIDA</span>
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
                                        <th style="text-align:center;">
                                            ' . $solPad . '
                                        </th>
                                        <th style="text-align:center; font-weight: bold;">
                                            ' . $ordenCompra . '
                                        </th>
                                        <th style="text-align:center;">
                                            ' . $costoSap . '
                                        </th>
                                        <th style="text-align:center;">
                                            ' . $posicion . '
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
            if(count($_FILES) == 0){
                throw new Exception('Debe seleccionar un archivo para procesar data!!');
            }
            if($arrayDataFile == null || count($arrayDataFile) == 0){
                throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
            }

            $nombreArchivo = $_FILES['file']['name'];
            $tipoArchivo = $_FILES['file']['type'];
            $nombreArchivoTemp = $_FILES['file']['tmp_name'];
            $tamano_archivo = $_FILES['file']['size'];

            $arryNombreArchivo = explode(".", $nombreArchivo);

            $arrayTipos = array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            );

            if (!in_array($tipoArchivo, $arrayTipos)) {
                throw new Exception('Sólo puede subir archivos de tipo excel (.xls , .xlsx)!!');
            }

            if (!file_exists("./uploads/aten_masivo_sol_oc_creacion_pan_diseno")) {
                if (!mkdir("./uploads/aten_masivo_sol_oc_creacion_pan_diseno")) {
                    throw new Exception('Hubo un error al crear la carpeta aten_masivo_sol_oc_creacion_pan_diseno!!');
                }
            }

            $rutaFinalArchivo = './uploads/aten_masivo_sol_oc_creacion_pan_diseno/' . date("Y_m_d_His_").$nombreArchivo;

            if (move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {

                $fechaActual = $this->m_utils->fechaActual();
                $arrayUpdateSolicitud      = array();
                $arrayUpdateSolicitudxItem = array();
                $arrayUpdatePlanObra       = array();
                $arrayInsertInci = array();
                $disenoList = array();

                foreach ($arrayDataFile as $datos) {
                    $codigoSolicitud   = $datos[0];
                    $solPad             = $datos[1];
                    $orden_compra       = $datos[2];
                    $costo_sap          = $datos[3];
                    $posicion           = $datos[4];
                    $itemplan           = $datos[5];
                    $idEstadoPlan       = $datos[6];                           
                    $idSubProyecto      = $datos[7];                            
                    $idTipoPlanta       = $datos[8];
                    $paquetizado_fg     = $datos[9];

                    $be_adjudicacion = false;


                    $objSolicitud['codigo_solicitud']  = $codigoSolicitud;
                    $objSolicitud['cesta']             = $solPad;
                    $objSolicitud['orden_compra']      = $orden_compra;
                    $objSolicitud['estado']            = 2;
                    $objSolicitud['usuario_valida']    = $idUsuario;
                    $objSolicitud['fecha_valida']      = $fechaActual;
                    $objSolicitud['costo_sap']         = $costo_sap;

                    $objSolicitudxItem['itemplan']            = $itemplan;
                    $objSolicitudxItem['codigo_solicitud_oc'] = $codigoSolicitud;
                    $objSolicitudxItem['posicion']            = $posicion;

                    $dataObra = $this->m_utils->getPlanObraByItemplan($itemplan);
                    if($dataObra['idTipoPlanta'] == ID_TIPO_PLANTA_INTERNA) {
                        $objPlanObra['idUsuarioLog'] = $idUsuario;
                        $objPlanObra['fechaLog']  = $fechaActual;
                        $objPlanObra['idEstadoPlan'] = ID_ESTADO_PLAN_EN_OBRA;
                        $objPlanObra['descripcion'] = 'ATENCION MAS. ORDEN DE COMPRA';  
                    } else {
                        if($dataObra['idProyecto'] == ID_PROYECTO_CABLEADO_DE_EDIFICIOS){
                            $arraySubProyNuevoFlujo = _getArrayIDSubProyNuevoFlujoCV();
                            if(!in_array($dataObra['idSubProyecto'],$arraySubProyNuevoFlujo)){
                                throw new Exception("No esta configurado el subproyecto de Planta externa");
                            }else{
                                if($dataObra['idEstadoPlan'] == ID_ESTADO_PLAN_PRE_REGISTRO){
                                    $objPlanObra['idUsuarioLog'] = $idUsuario;
                                    $objPlanObra['fechaLog']  = $fechaActual;
                                    $objPlanObra['descripcion'] = 'ATENCION MAS. ORDEN DE COMPRA';                         
                                    $objPlanObra['idEstadoPlan']  = ID_ESTADO_PLAN_DISENIO;
    
                                    $be_adjudicacion = true;
                                    $dataIncidencia['itemplan'] = $itemplan;
                                    $dataIncidencia['idEstadoPlan'] = ID_ESTADO_PLAN_DISENIO;
                                    $dataIncidencia['usuario_registro'] = $idUsuario;
                                    $dataIncidencia['fecha_registro'] = $fechaActual;
                                    $dataIncidencia['id_motivo_seguimiento'] = 34;
                                    $dataIncidencia['comentario_incidencia'] = 'AUTOMÁTICO, MODULO BANDEJA SOLICITUD OC';
                                    $arrayInsertInci []= $dataIncidencia;
                          
                                    $itemplanNuevoFlujoList[] = $itemplan;
                                }
    
                                if($be_adjudicacion){//solo si acaba de pasar de pre registro a diseno adjudico y registro ot
                                    $has_ancla = false;
                                    $has_fo  = false;
                                    $has_coax = false;
                                    $infoAnclasByItemplan = $this->m_bandeja_solicitud_oc_diseno->hasEstacionesAnclasByItemplan($itemplan);
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
                                                'fecha_adjudicacion'      => $fechaActual,
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
                                                'fecha_adjudicacion'      => $fechaActual,
                                                'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
                                                'fecha_prevista_atencion' => $fechaPreAtencion
                                            );              
                                            $disenoList[] = $infoAdjudicacion;
                                        }
                                            
                                        if($dataObra['paquetizado_fg'] ==  2){//es paquetizada
                                            $itemplanListPoPqt []= $itemplan;//almacenamos el itemplan para postererormente generarle su po pqt
                                        }
                                    }
                                }
                                
                            }
                        }else if($dataObra['idProyecto'] == 52){//FTTH
                            $objPlanObra['idUsuarioLog'] = $idUsuario;
                            $objPlanObra['fechaLog']    = $fechaActual;
                            $objPlanObra['descripcion'] = 'ORDEN DE COMPRA ATENDIDA';        
                            // $infoDiseno = $this->m_bandeja_solicitud_oc_diseno->getDisenoInfoLiquiByItemEstacion($itemplan, 5);//FTTH ES FO      
                            $objPlanObra['idEstadoPlan']  = ID_ESTADO_PLAN_DISENIO;

                            $objPlanObraPO['oc_diseno']             = $orden_compra;
                            $objPlanObraPO['itemplan']              = $itemplan;
                            $objPlanObraPO['codigo_solicitud_oc']   = $codigoSolicitud;                      
                        }else if($dataObra['idProyecto'] == 3){//B2B

                            $nuevafecha = strtotime('+7 day', strtotime($fechaActual));
                            $fechaPreAtencion = date('Y-m-d', $nuevafecha);

                            $objPlanObra['idUsuarioLog']        = $idUsuario;
                            $objPlanObra['fechaLog']            = $fechaActual;
                            $objPlanObra['descripcion']         = 'ORDEN DE COMPRA ATENDIDA';                       
                            $objPlanObra['idEstadoPlan']        = ID_ESTADO_PLAN_DISENIO;
                            $objPlanObra['fechaPrevEjecucion']  = $fechaPreAtencion;   
                        
                            $infoAdjudicacion = array ( 
                                'itemplan'                => $itemplan,
                                'idEstacion'              => ID_ESTACION_FO,
                                'estado'                  => ID_ESTADO_PLAN_DISENIO,
                                'fecha_registro'          => $fechaActual,
                                'usuario_registro'        => $idUsuario,     
                                'fecha_adjudicacion'      => $fechaActual,
                                'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
                                'fecha_prevista_atencion' => $fechaPreAtencion
                            );              
                            $disenoList[] = $infoAdjudicacion;
                        }else if($dataObra['idProyecto'] == 54){//PROYECTOS VARIOS

                            $nuevafecha = strtotime('+7 day', strtotime($fechaActual));
                            $fechaPreAtencion = date('Y-m-d', $nuevafecha);
    
    
                            $objPlanObra['idUsuarioLog']        = $idUsuario;
                            $objPlanObra['fechaLog']            = $fechaActual;
                            $objPlanObra['descripcion']         = 'ORDEN DE COMPRA ATENDIDA';
                            $objPlanObra['fechaPrevEjecucion']  = $fechaPreAtencion;
                            
                            if($dataObra['idSubProyecto']  ==  747){
                                $objPlanObra['idEstadoPlan']        = 20;//IP MADRE VA EN APROBACION NO TIENE DISE NI LICE REFORZAMIENTO EXPRESS
                            }else if($dataObra['idSubProyecto']  ==  736 ||  $dataObra['idSubProyecto']  ==  740 ||  $dataObra['idSubProyecto']  ==  744){
                                $objPlanObra['idEstadoPlan']        = 3;//ESTUDIOS DE ESFUERZO EN OBRA
                            }else{
                                $objPlanObra['idEstadoPlan']        = ID_ESTADO_PLAN_DISENIO;
                                
                                $infoAdjudicacion = array ( 
                                    'itemplan'                => $itemplan,
                                    'idEstacion'              => ID_ESTACION_FO,
                                    'estado'                  => ID_ESTADO_PLAN_DISENIO,
                                    'fecha_registro'          => $fechaActual,
                                    'usuario_registro'        => $idUsuario,     
                                    'fecha_adjudicacion'      => $fechaActual,
                                    'usuario_adjudicacion'    => 'ORDEN COMPRA ATENDIDA',
                                    'fecha_prevista_atencion' => $fechaPreAtencion
                                );
                                
                                $disenoList[] = $infoAdjudicacion;
                            }
                            
                        }else if($dataObra['idProyecto'] == 55){//PROYECTOS MANTENIMIENTO

                            $nuevafecha = strtotime('+7 day', strtotime($fechaActual));
                            $fechaPreAtencion = date('Y-m-d', $nuevafecha);
                            $objPlanObra['idUsuarioLog']        = $idUsuario;
                            $objPlanObra['fechaLog']            = $fechaActual;
                            $objPlanObra['descripcion']         = 'ORDEN DE COMPRA ATENDIDA';
                            $objPlanObra['fechaPrevEjecucion']  = $fechaPreAtencion;
                            
                            if(in_array($dataObra['idSubProyecto'], array(739,755,756,757,759))){
                                $objPlanObra['idEstadoPlan']        = 3;//MANTENIMIENTO CON OC
                            }else{
                                throw new Exception("No esta configurado el subproyecto de mantenimiento!");
                            }                        
                        }else{
                            throw new Exception("No esta configurado el proyeto de Planta externa");
                        }
                    }

                    $objPlanObra['orden_compra_diseno']  = $orden_compra;
                    $objPlanObra['itemplan']      = $itemplan;
                    $objPlanObra['costo_sap_diseno']     = $costo_sap;
                    $objPlanObra['estado_sol_oc_diseno'] = 'ATENDIDO';
                    $objPlanObra['solicitud_oc_anula_pos'] = null;
                    $objPlanObra['costo_unitario_mo_anula_pos'] = null;
                    $objPlanObra['estado_oc_anula_pos'] = null;

                    $arrayUpdateSolicitud[] = $objSolicitud;
                    $arrayUpdateSolicitudxItem[] = $objSolicitudxItem;
                    $arrayUpdatePlanObra[] = $objPlanObra;

                }

                $data = $this->m_bandeja_solicitud_oc_diseno->atencionSolicitudOcCrea($arrayUpdateSolicitud, $arrayUpdateSolicitudxItem, $arrayUpdatePlanObra);
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
                if(count($disenoList) > 0){
                    $data = $this->m_bandeja_solicitud_oc_diseno->insertMasiveDiseno($disenoList);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }
                }
                /*
                if(count($arrayInsertInci) > 0){//para itemplan del nuevo flujo integral(que pasaran a diseño)
                    $data = $this->m_bandeja_solicitud_oc_diseno->registroMasivoIncidenciaCV($arrayInsertInci);
                    if($data['error'] == EXIT_ERROR){
                        throw new Exception($data['msj']);
                    }
                }*/
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se atendió exitosamente las solicitudes!!';
                $this->db->trans_commit();

            }else{
                throw new Exception('No se pudo subir el archivo: ' . date("Y_m_d_His_") . $nombreArchivo . ' !!');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

}
