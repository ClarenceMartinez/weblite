<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registro_manual_po_mat extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_crecimiento_vertical/m_registro_manual_po_mat');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        $this->load->library('excel');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario      = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
        $itemplan       = $this->input->get('itemplan');
        $idEstacion     = $this->input->get('estacion');
        $estacionDesc   = $this->input->get('estacionDesc');

	    if($idUsuario != null && $itemplan != null && $itemplan != '' && $idEstacion != null && $idEstacion != '' && $estacionDesc != null && $estacionDesc != ''){
            $data['estacionDesc'] = $estacionDesc;
            $data['itemplan'] = $itemplan;
            $data['idEstacion'] = $idEstacion;
            list($html, $ctnValidos, $arrayFinal, $costoFinal) = $this->tablaRegistroPO(array());
            $data['tablaPO'] = $html;

            $permisos           = $this->session->userdata('permisosArbolPan');
            $result             = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_OBRA_PADRE, null, ID_CONSULTA_HIJO, null);
            $data['opciones']   = $result['html'];
            $data['header']     = $this->lib_utils->getHeader();
	//		$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_consulta/v_registro_manual_po_mat',$data);        	  
    	 }else{
            redirect(RUTA_OBRA2, 'refresh');
	    }
    }


    function tablaRegistroPO($arrayTablaPO) {
        $html = '<table id="tbRegistroPO" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>
                            <th>OBSERVACIÓN</th>
                            <th>CÓDIGO</th>  
                            <th>MATERIAL</th>  
                            <th>UDM</th>
                            <th>CANT. KIT</th>
                            <th>PRECIO</th>
                            <th>CANTIDAD</th>
                            <th>COSTO TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>';
        $count = 0;
        $ctnVal = 0;
        $costoFinal = 0;
        $arrayFinal = array();
        $style = '';

        foreach ($arrayTablaPO as $row) {
            log_message('error', print_r($row,true));
            $style = '';
            if ($row['observacion'] != '') {
                $htmlColorFila = 'style="background:#FDBDBD"';
                $btnDelete = '<a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Eliminar" 
                                aria-expanded="false" data-codigo_material="'.$row['codigo_material'].'"
                                onclick="deleteMatErroneo(this);"><i class="fal fa-delete"></i>
                            </a>';
            } else {
                $htmlColorFila = '';
                $btnDelete = '';
                $costoFinal = round($costoFinal + floatval($row['costo_total']), 2);
                $arrayFinal[] = array(
                    "codigo_material" => $row['codigo_material'],
                    "cantidadInicial" => $row['cantidad_ingresada'],
                    "cantidadFinal"   => $row['cantidad_ingresada'],
                    "costoMat" => $row['costo_material'],
                    "montoFinal" => $row['costo_total']
                );
                $ctnVal++;
            }

            if (is_numeric($row['cantidad_kit']) && is_numeric($row['factor_porcentual'])) {
                $cant_evaluar = ($row['cantidad_kit'] * $row['factor_porcentual']) / 100;

                if ($row['cantidad_ingresada'] > $row['cantidad_kit']) {
                    $style = ' color: #fd3995';
                } else if ($row['cantidad_ingresada'] < $cant_evaluar) {
                    $style = 'color: #1dc9b7';
                } else {
                    $style = '';
                }
            }


            $html .= ' <tr ' . $htmlColorFila . '>
                            <td>
                                <div class="d-flex demo">
                                    '.$btnDelete.'
                                </div>
                            </td>
                            <td>'.$row['observacion'].'</td>
                            <td>'.$row['codigo_material'].'</td>
                            <td>'.$row['descrip_material'].'</td>
                            <td>'.$row['unidad_medida'].'</td>
                            <td>'.$row['cantidad_kit'].'</td>
                            <td>'.(is_numeric($row['costo_material']) ? number_format($row['costo_material'], 2) : '-').'</td>
                            <td style="text-align:center; font-weight: bold;' . $style . '">' . $row['cantidad_ingresada'] . '</td>
                            <td style="text-align:center;">' . (is_numeric($row['costo_total']) ? number_format($row['costo_total'],2) : '-') . '</td>
                        </tr>';
            $count++;
        }
        $html .= '</tbody>
            </table>';

        return array($html, $ctnVal, $arrayFinal, $costoFinal);
    }

    public function getFormatoExcelCarga() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $itemplan   = $this->input->post('itemplan');
            $idEstacion = $this->input->post('idEstacion');
          
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
			$hoja->setTitle('FT CARGA PO MAT');
             
            
            $infoITem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoITem == null) {
                throw new Exception('Hubo un error al traer la información del itemplan.');
            }

            $col = 0;
			$row = 1;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'MATERIAL');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'UM');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'COSTO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'KIT MAT');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD INGRESADA');
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

            $col = 0;
			$row = 2;
            $listaMateriales = $this->m_registro_manual_po_mat->getMaterialesBySubProyectoEstacion($infoITem['idSubProyecto'], $idEstacion);
            foreach ($listaMateriales as $fila) {
                $col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_material);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->descrip_material);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->unidad_medida);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->costo_material);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->cant_kit_material);
            	$col++;

                $row++;
            }

            $estilo = [
                'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => [
							'rgb' => '000000',
						],
					)
				)
            ];
            $hoja->getStyle('A1' . ':' . ($hoja->getHighestColumn()) . ($hoja->getHighestRow()))->applyFromArray($estilo);

            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');
			ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

			$data['error'] = EXIT_SUCCESS;
			$data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
            $data['nombreArchivo'] = 'modelo_carga_registro_po_mat_' . date("YmdHis") . '.xls';

            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo partidas MO';
        }

        echo json_encode($data);
    }

    public function procesarArchivoPoMat()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $itemplan   = $this->input->post('itemplan');
            $idEstacion = $this->input->post('idEstacion');
			$idUsuario = $this->session->userdata('idPersonaSessionPan');

            if (!isset($itemplan)) {
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }
            if (!isset($idEstacion)) {
                throw new Exception('Hubo un error al recibir la estación!!');
            }

			if (!isset($idUsuario)) {
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

            // if (!file_exists("./uploads/archivos_carga_masiva_alumno")) {
            //     if (!mkdir("./uploads/archivos_carga_masiva_alumno")) {
            //         throw new Exception('Hubo un error al crear la carpeta archivos_carga_masiva_alumno!!');
            //     }
            // }
            $infoITem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoITem == null) {
                throw new Exception('Hubo un error al traer la información del itemplan.');
            }
            $arrayTablaPO = array();

			$objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
            foreach ($objectExcel->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();

				for ($row = 2; $row <= $highestRow; $row++) {
                    $col = 0;
                    $codigoMaterial = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());
                    $col = 5;
                    $cantidadIngresada = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());
					log_message('error', $codigoMaterial);
                    $existeMaterial = $this->m_registro_manual_po_mat->countMaterialByCod($codigoMaterial);
                    $exitMatKit = $this->m_utils->countMatxKit($codigoMaterial,$infoITem['idSubProyecto'],$idEstacion);
                    $detalleMat = $this->m_registro_manual_po_mat->getDetalleMaterial($codigoMaterial,$infoITem['idSubProyecto'], $idEstacion);
                    $flgMatPhaseOut = $this->m_registro_manual_po_mat->countMatPhaseOut($codigoMaterial);


                    if ($cantidadIngresada != null && $cantidadIngresada != '' && is_numeric($cantidadIngresada)) {
                    
                        if(isset($detalleMat)) {
                            $desc_material = $detalleMat['descrip_material'];
                            $unidad_medida = $detalleMat['unidad_medida'];
                            $cantidad_kit = $detalleMat['cant_kit_material'];
                            $factorMaxPorcen = $detalleMat['factor_porcentual'];
                            $costo_material = $detalleMat['costo_material'];
                            $costo_total = round(floatval($cantidadIngresada) * floatval($costo_material),2);
                            $flgTipoMat = $detalleMat['flg_tipo'];
                        }else{
                            $desc_material = '-';
                            $unidad_medida = '-';
                            $cantidad_kit = '-';
                            $factorMaxPorcen = '-';
                            $costo_material = '-';
                            $costo_total = '-';
                            $flgTipoMat = '-';
                        }

                        $dataArray['observacion'] = '';

                        if ($codigoMaterial == null || $codigoMaterial == '') {
                            $dataArray['observacion'] .= 'No tiene código de material.'.'<br>';
                        }

                        if ($existeMaterial == 0) {
                            $dataArray['observacion'] .= 'No existe material.'.'<br>';
                        }
                        if ($exitMatKit == 0 && $flgTipoMat == 0) {
                            $dataArray['observacion'] .= 'Material no pertenece al kit.'.'<br>';
                        }else if ($flgTipoMat == 1) {// SI ES BUCLE
                            if ($flgMatPhaseOut > 0) {
                                $dataArray['observacion'] .= 'Material Phase Out.'.'<br>';
                            }
                        }
                       
                        $dataArray['codigo_material'] = $codigoMaterial;
                        $dataArray['descrip_material'] = $desc_material;
                        $dataArray['unidad_medida'] = $unidad_medida;
                        $dataArray['cantidad_kit'] = $cantidad_kit;
                        $dataArray['factor_porcentual'] = $factorMaxPorcen;
                        $dataArray['costo_material'] = $costo_material;
                        $dataArray['cantidad_ingresada'] = $cantidadIngresada;
                        $dataArray['costo_total'] = $costo_total;
                        $dataArray['flg_tipo'] = $flgTipoMat;

                        $arrayTablaPO []= $dataArray;
                    }
                    
                }
            }

			list($html, $ctnValidos, $arrayFinal, $costoFinal) = $this->tablaRegistroPO($arrayTablaPO);
            $data['titulo'] = 'Cantidad de registros válidos a cargar: '.$ctnValidos;
            $data['costoTotalFormat'] = 'S/.'.number_format($costoFinal,2);
            $data['costoTotal'] = $costoFinal;
			$data['tbReporte'] = $html;
			$data['jsonDataFile'] = json_encode($arrayFinal);

			$data['msj']  = 'Se procesó correctamente el archivo!!';
			$data['error']  = EXIT_SUCCESS;

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


    public function registrarPoMat()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $itemplan   = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
            $costoTotalPo = $this->input->post('costoTotalPo') ? $this->input->post('costoTotalPo') : null;
            $arrayDetPo = $this->input->post('arrayDetPo') ? json_decode($this->input->post('arrayDetPo'),true) : array();
			$idUsuario = $this->session->userdata('idPersonaSessionPan');

            $this->db->trans_begin();

            if (!isset($itemplan)) {
                throw new Exception('Hubo un error al recibir el itemplan!!');
            }
            if (!isset($idEstacion)) {
                throw new Exception('Hubo un error al recibir la estación!!');
            }
            if ($costoTotalPo == 0 || $costoTotalPo == null || $costoTotalPo == '') {
                throw new Exception('No hay un costo válido para registrar la po!!');
            }

            if (count($arrayDetPo) == 0 ) {
                throw new Exception('No hay un detalle de materiales para registrar la po!!');
            }

			if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
			
            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null) {
                throw new Exception('Hubo un error al traer la información del itemplan.');
            }

            $countPo = $this->m_utils->getCountPo($itemplan, $idEstacion, 1);
            $countCienPor = $this->m_utils->getPorcentajeByItemplanAndEstacion($itemplan, $idEstacion);

            if($infoItem['idProyecto'] == ID_PROYECTO_CABLEADO_DE_EDIFICIOS){
                if($infoItem['idTipoSubProyecto'] == 1) {
                    if($countPo >= 3){
                        throw new Exception('No se puede ingresar mas de tres POs.');
                    }
                }else{
                    if($countPo >= 3){
                        throw new Exception('No se puede ingresar mas de tres POs.');
                    }
                }
            }

            $codigoPO = $this->m_utils->getCodigoPO($itemplan);
            if($codigoPO == null || $codigoPO == '') {
                throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
            }

            $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MAT', $idEstacion);
            if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                throw new Exception("No tiene configurado un area.");
            }

            $fechaActual = $this->m_utils->fechaActual();

            $dataPo = array (
                'codigo_po'      => $codigoPO,
                'itemplan'       => $itemplan,
                'estado_po'      => ID_ESTADO_PO_PRE_APROBADO,
                'idEstacion'     => $idEstacion,
                'costo_total'    => $costoTotalPo,
                'idUsuario'      => $idUsuario,
                'fechaRegistro'  => $fechaActual,
                'flg_tipo_area'  => 1,
                'idEmpresaColab' => $infoItem['idEmpresaColab'],
                'idArea'         => $dataSubEstacionArea['idArea'],
                'idSubProyecto'  => $infoItem['idSubProyecto']
            );
	
			if($infoItem['idProyecto']  ==  55){//mantenimiento
                $dataPo['estado_po'] =   ID_ESTADO_PO_REGISTRADO; 
            }
			
            foreach($arrayDetPo as $key => $value) {
                $arrayDetPo[$key]['codigo_po'] = $codigoPO;
            }

            if(count($arrayDetPo) == 0){
                throw new Exception("No hay materiales válidos para el registro de la PO");
            }
            $dataLogPO =    array(
                'codigo_po'        =>  $codigoPO,
                'itemplan'         =>  $itemplan,
                'idUsuario'        =>  $idUsuario,
                'fecha_registro'   =>  $fechaActual,
                'idPoestado'       =>  ID_ESTADO_PO_PRE_APROBADO
            );
			
			if($infoItem['idProyecto']  ==  55){//mantenimiento
                $dataLogPO['idPoestado'] =   ID_ESTADO_PO_REGISTRADO; 
            }

            $data = $this->m_utils->registrarPoMat($dataPo, $arrayDetPo, $dataLogPO);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
                $data['msj'] = 'Se creó la po mat exitosamente!!';
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
  
}
