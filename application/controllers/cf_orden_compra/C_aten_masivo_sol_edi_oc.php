<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_aten_masivo_sol_edi_oc extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_orden_compra/m_aten_masivo_sol_edi_oc');
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, 57, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_orden_compra/v_aten_masivo_sol_edi_oc',$data);        	  
    	 }else{
            redirect(RUTA_OBRA2, 'refresh');
	    }
    }


    public function getFormatoExcelCarga() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Fernando Paolo Luna Villalba')
				->setLastModifiedBy('Fernando Paolo Luna Villalba')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Excel de prueba')
				->setDescription('Excel generado como prueba')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de prueba');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->setTitle('FT CARGA');

            $col = 0;
			$row = 1;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO SOLICITUD');
            $col++;           
            $hoja->setCellValueByColumnAndRow($col, $row, 'COSTO SAP');
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
            $data['nombreArchivo'] = 'Formato_Atencion_Masiva_Sol_OC_Edicion' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
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

    public function makeHTMLTablaObservacion($objectExcel)
    {
        $html = '
                <table id="tbObservacion" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">#</th>
							<th style="text-align: center; vertical-align: middle;">CÓDIGO SOLICITUD</th>
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
                    $costoSap = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$costoSap = _removeEnterYTabs(trim(utf8_encode(utf8_decode($costoSap)),'?'));
                    $col++;

                    $ordenCompra = '';
					if(strlen($codigoSolicitud) == 0 || $codigoSolicitud == null || $codigoSolicitud == ''){
						$html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
									<th style="text-align:center;">
										' . $count . '
									</th>
									<th style="text-align:center; font-weight: bold;">
										' . $codigoSolicitud . '
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
					}else if($costoSap == null || $costoSap == '' || !is_numeric($costoSap)){
						$html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                    <th style="text-align:center;">
                                        ' . $count . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $codigoSolicitud . '
                                    </th>
                                    
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $ordenCompra . '
                                    </th>
                                    <th style="text-align:center;">
                                        ' . $costoSap . '
                                    </th>
                                    <th>
                                        <span class="badge badge-danger badge-pill">COSTO SAP INVÁLIDO</span>
                                    </th>
								</tr>';
					}else{
                        $infoSol = $this->m_aten_masivo_sol_edi_oc->getInfoSolicitudOCCreaByCodigo($codigoSolicitud);                      
                        if($infoSol != null && $infoSol['itemplan'] != null){
                            $ordenCompra = $infoSol['orden_compra'];
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

				foreach ($arrayDataFile as $datos) {
                    $codigoSolicitud    = $datos[0];              
                    $orden_compra       = $datos[1];
                    $costo_sap          = $datos[2];
                    $itemplan           = $datos[3];       						
             
                    //SOL EDICION
                    $objSolicitud = array();
                    $objSolicitud['codigo_solicitud']  = $codigoSolicitud;
                    $objSolicitud['estado']            = 2;
                    $objSolicitud['usuario_valida']    = $idUsuario;
                    $objSolicitud['fecha_valida']      = $fechaActual;
                    $objSolicitud['costo_sap']         = $costo_sap;
                    $arrayUpdateSolicitud[] = $objSolicitud;

                    //SOL CERTIFICACION
                    $codigoSolicitudCerti = $this->m_bandeja_solicitud_oc->getSolCertPndEdicion($itemplan);
                    if($codigoSolicitudCerti != null){//enconetro pdt de edicion
                        $objSolicitudCert = array();
                        $objSolicitudCert['codigo_solicitud']  = $codigoSolicitudCerti;
                        $objSolicitudCert['estado']            = 5;//pnd de acta
                        $arrayUpdateSolicitud[] = $objSolicitudCert;
                    }
                    //PLANOBRA
              
                    $objPlanObra = array();
                    $objPlanObra['itemplan']            = $itemplan;
                    $objPlanObra['costo_sap']           = $costo_sap;
                    $objPlanObra['estado_oc_dev']       = 'ATENDIDO';
                    $objPlanObra['costo_unitario_mo']   = $costo_sap;
                    if($codigoSolicitudCerti != null){
                        $objPlanObra['estado_oc_certi']   = 'EN ESPERA DE ACTA';
                    }                 
                    $arrayUpdatePlanObra[] = $objPlanObra;
				}

                $data = $this->m_aten_masivo_sol_edi_oc->atencionSolicitudOcEdi($arrayUpdateSolicitud, $arrayUpdatePlanObra);
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
