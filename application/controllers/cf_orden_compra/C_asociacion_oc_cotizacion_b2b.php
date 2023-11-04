<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_asociacion_oc_cotizacion_b2b extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_orden_compra/m_asociacion_oc_cotizacion_b2b');
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, 55, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_orden_compra/v_asociacion_oc_cotizacion_b2b',$data);        	  
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ORDEN COMPRA');
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
            $data['nombreArchivo'] = 'Formato_asociacion_oc_cotizacion_b2b' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }

    public function procesarFileMasivoAtenSolCotiOc()
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
                            <th style="text-align: center; vertical-align: middle;">SISEGO</th>
                            <th style="text-align: center; vertical-align: middle;">EECC</th>
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
                    $ordenCompra = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
					$ordenCompra = _removeEnterYTabs(trim(utf8_encode(utf8_decode($ordenCompra)),'?'));
                    $col++;
                    $sisego = '';
                    $eecc = '';
					if(strlen($codigoSolicitud) == 0 || $codigoSolicitud == null || $codigoSolicitud == ''){
						$html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
									<th style="text-align:center;">
										' . $count . '
									</th>
									<th style="text-align:center; font-weight: bold;">
										' . $codigoSolicitud . '
									</th>
                                    <th style="text-align:center;">
                                        ' . $sisego . '
									</th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $eecc . '
									</th>
                                    <th style="text-align:center;">
                                        ' . $ordenCompra . '
									</th>                   
                                    <th style="text-align:center;">
                                    ' . $costoSap . '
                                    </th>                
									<th>
                                        <span class="badge badge-danger badge-pill">CODIGO DE SOLICITUD INVÁLIDO</span>
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
                                        ' . $sisego . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $eecc . '
                                    </th>                                   
                                    <th style="text-align:center;">
                                        ' . $ordenCompra . '
                                    </th>    
                                    <th style="text-align:center;">
                                    ' . $costoSap . '
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
                                        ' . $sisego . '
                                    </th>
                                    <th style="text-align:center; font-weight: bold;">
                                        ' . $eecc . '
                                    </th>
                                    <th style="text-align:center;">
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
						// $arrayInfo = $this->m_registro_masivo_sol_crea_oc_pan->getInfoItemplan($itemplan);
                        $infoSol = $this->m_asociacion_oc_cotizacion_b2b->getInfoClusterToPutOC($codigoSolicitud);
                        if($infoSol != null && $infoSol['codigo_cluster'] != null){
                            $sisego = $infoSol['sisego'];
                            $eecc  =  $infoSol['empresaColabDesc'];
                            if(!in_array($ordenCompra,$arrayOC)){
                                if($infoSol['orden_compra'] == null){
                                    if($infoSol['estado'] != 4){//NO PDT DE COTIZACION
                                        $hasOcActiva = $this->m_asociacion_oc_cotizacion_b2b->existeCLConOC($ordenCompra);
                                        if($hasOcActiva == 0){
                                            $html .= '<tr id="tr' . $count . '" >
                                                        <th style="text-align:center;">
                                                        ' . $count . '
                                                        </th>
                                                        <th style="text-align:center; font-weight: bold;">
                                                        ' . $codigoSolicitud . '
                                                        </th>
                                                        <th style="text-align:center;">
                                                            ' . $sisego . '
                                                        </th>
                                                        <th style="text-align:center; font-weight: bold;">
                                                            ' . $eecc . '
                                                        </th>
                                                        <th style="text-align:center;">
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
                                                $costoSap                                           
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
                                                            ' . $sisego . '
                                                        </th>
                                                        <th style="text-align:center; font-weight: bold;">
                                                            ' . $eecc . '
                                                        </th>
                                                        <th style="text-align:center;">
                                                            ' . $ordenCompra . '
                                                        </th>                   
                                                        <th style="text-align:center;">
                                                        ' . $costoSap . '
                                                        </th>      
                                                        <th>
                                                            <span class="badge badge-danger badge-pill">LA OC YA SE ENCUENTRA EN UN CODIGO CL</span>
                                                        </th>
                                                    </tr>';
                                        }
                                    }else{//IVALIDO ATENDIDA O CANCELADA
                                      
                                        $msj =  $infoSol['estadoDesc'];
                                         
                                        $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                                    <th style="text-align:center;">
                                                        ' . $count . '
                                                    </th>
                                                    <th style="text-align:center; font-weight: bold;">
                                                    ' . $codigoSolicitud . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $sisego . '
                                                    </th>
                                                    <th style="text-align:center; font-weight: bold;">
                                                        ' . $eecc . '
                                                    </th>
                                                    <th style="text-align:center;">
                                                        ' . $ordenCompra . '
                                                    </th>                   
                                                    <th style="text-align:center;">
                                                    ' . $costoSap . '
                                                    </th>      
                                                    <th>
                                                        <span class="badge badge-danger badge-pill">ESTADO SOLICITUD "'.$msj.'" NO VALIDO PARA PAGO</span>
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
                                                ' . $sisego . '
                                            </th>
                                            <th style="text-align:center; font-weight: bold;">
                                                ' . $eecc . '
                                            </th>
                                            <th style="text-align:center;">
                                                ' . $ordenCompra . '
                                            </th>                   
                                            <th style="text-align:center;">
                                            ' . $costoSap . '
                                            </th>      
                                            <th>
                                                <span class="badge badge-danger badge-pill">CODIGO CL YA CUENTA CON UNA OC: '.$infoSol['orden_compra'].'</span>
                                            </th>
                                        </tr>';
                                }
                                
                            }else{
                                $html .= '<tr style="background-color: #FDBDBD;" id="tr' . $count . '" >
                                            <th style="text-align:center;">
                                                ' . $count . '
                                            </th>
                                            <th style="text-align:center; font-weight: bold;">
                                            ' . $codigoSolicitud . '
                                            </th>
                                            <th style="text-align:center;">
                                                ' . $sisego . '
                                            </th>
                                            <th style="text-align:center; font-weight: bold;">
                                                ' . $eecc . '
                                            </th>
                                            <th style="text-align:center;">
                                                ' . $ordenCompra . '
                                            </th>                   
                                            <th style="text-align:center;">
                                            ' . $costoSap . '
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
                                            ' . $sisego . '
                                        </th>
                                        <th style="text-align:center; font-weight: bold;">
                                            ' . $eecc . '
                                        </th>
                                        <th style="text-align:center;">
                                            ' . $ordenCompra . '
                                        </th>                   
                                        <th style="text-align:center;">
                                        ' . $costoSap . '
                                        </th>                                                  
                                        <th>
                                            <span class="badge badge-danger badge-pill">CODIGO CL NO RECONOCIDO</span>
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

    public function updateMasivoSolCotiB2bOC()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$idUsuario = $this->session->userdata('idPersonaSessionPan');
			$arrayDataFile = $this->input->post('arrayDataFile') ? json_decode($this->input->post('arrayDataFile')) : null;

          
			if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            } 
			if($arrayDataFile == null || count($arrayDataFile) == 0){
				throw new Exception('No se pudo cargar la iformación a actualizar, refresque la página y vuelva a intentarlo.');
			} 
                $fechaActual = $this->m_utils->fechaActual();
                $arrayListClusterUpdate      = array();
          

				foreach ($arrayDataFile as $datos) {
                    $codigoSolicitud    = $datos[0];                   
                    $orden_compra       = $datos[1];
                    $costo_sap          = $datos[2]; 
                    
                    $objCoti = array();
                    $objCoti['codigo_cluster']      = $codigoSolicitud;
                    $objCoti['orden_compra']        = $orden_compra;                   
                    $objCoti['costo_sap_oc']        = $costo_sap;
                    $objCoti['estado_orden_compra'] = 'CREADO';
                    $objCoti['fec_reg_oc']          = $fechaActual;
                    $objCoti['usua_reg_oc']         = $idUsuario;

                    array_push($arrayListClusterUpdate, $objCoti);
                     
				}

                $data = $this->m_asociacion_oc_cotizacion_b2b->updateClusterB2b($arrayListClusterUpdate);
                       
        } catch (Exception $e) {         
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


  
}
