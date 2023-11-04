<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_carga_seguimiento_refor_cto extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_transferencias/m_carga_seguimiento_refor_cto'); 
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

            //list($html, $ctnValidos, $arrayFinal) = $this->makeHTMLTablaObservacion('');
            $data['tbObservacion'] = $this->tablaRegistroRegistroRefor(null);

            $permisos = $this->session->userdata('permisosArbolPan');
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, ID_ATEN_SOL_OC_CREA_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_transferencias/v_carga_seguimiento_refor_cto',$data);        	  
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CTO ADJUDICADO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIPO REFORZAMIENTO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'REFORZAMIENTO CTO DISEÑO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FASE');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'REFORZAMIENTO CTO FINAL');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO DIVISOR');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CABLE');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'HILO');
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
            $data['nombreArchivo'] = 'Formato_carga_reforzamiento_cto' . date("YmdHis") . '.xls';
            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo de formato de carga';
        }

        echo json_encode($data);
    }

    public function procesarFileEstadoSirope()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();
            if($idUsuario == null) {
                throw new Exception('La sesión a expirado, recargue la página');
            }
            if(count($_FILES) == 0){
                throw new Exception('Debe poner un archivo para registrar!! ');
            }
            
            $tipoArchivo = $_FILES['file']['type'];
            $nombreFicheroTemp = $_FILES['file']['tmp_name'];

            $arrayTipos = array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.ms-excel'
			);

            if (!in_array($tipoArchivo, $arrayTipos)) {
                throw new Exception('Sólo puede subir archivos de tipo excel (.xls , .xlsx)!!');
            }
 
            $arrayTablaRegRef = array();
            $arrayToUpdate = array();
            $itemplanUpdLog = array();
            $ctnValidos   = 0;
            $ctnInValidos = 0;
 			$objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
            foreach ($objectExcel->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();

				for ($row = 2; $row <= $highestRow; $row++) {
                   
                    $itemplan       = trim($worksheet->getCellByColumnAndRow(0, $row)->getValue());
                    $ctoAdjudicado  = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
                    $tipoReforza    = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());
                    $ctoDiseno      = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());
                    $fase           = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());
                    $ctoFinal       = trim($worksheet->getCellByColumnAndRow(5, $row)->getValue());
                    $codigoDivisor  = trim($worksheet->getCellByColumnAndRow(6, $row)->getValue());
                    $cable          = trim($worksheet->getCellByColumnAndRow(7, $row)->getValue());
                    $hilo           = trim($worksheet->getCellByColumnAndRow(8, $row)->getValue());
                    $dataArray = array();
                    if ($itemplan != '' && $ctoAdjudicado != '' && $tipoReforza != '' && $ctoDiseno != '') {                        
                        $existeCombinatoria = $this->m_carga_seguimiento_refor_cto->validateExistCombinatoria($itemplan, $ctoAdjudicado, $tipoReforza, $ctoDiseno);
                        $dataArray['observacion'] = '';
                        if($existeCombinatoria   !=  null) {
                            if($existeCombinatoria['has_seguimiento'] == 1){
                                $ctnInValidos++;
                                $dataArray['estatus']   = 'ERROR';
                                $dataArray['observacion'] .= 'COMBINATORIA YA SE ENCUENTRA REGISTRADA (ITEMPLAN - CTO ADJUDICADO - TIPO REFORZAMIENTO - REFORZAMIENTO CTO DISEÑO).'.'<br>';  
                            }else{
                                $ctnValidos++;
                                $dataArray['estatus']   = 'OK';
                                $dataArray['observacion'] = '';
                                /**cargamos a bd*/                              
                                $arraytoUpd = array();
                                $arraytoUpd['id_formulario']            =  $existeCombinatoria['id_formulario'];
                                $arraytoUpd['has_seguimiento']          = 1;
                                $arraytoUpd['situacion_especifica']     = 1;
                                $arraytoUpd['fec_upd_sit_especifica']   = $fechaActual;
                                $arraytoUpd['usu_upd_sit_especifica']   = $idUsuario;
                                $arraytoUpd['fase']                     = $fase;
                                $arraytoUpd['codigo_divisor']           = $codigoDivisor;
                                $arraytoUpd['cable']                    = $cable;
                                $arraytoUpd['hilo']                     = $hilo;
                                $arraytoUpd['cto_final']                = $ctoFinal;
                                array_push($arrayToUpdate, $arraytoUpd);

                                if($existeCombinatoria['situacion_general_reforzamiento']   ==  null){
                                    array_push($itemplanUpdLog, $itemplan);
                                }
                            }
                         }else{
                            $ctnInValidos++;
                            $dataArray['estatus']   = 'ERROR';
                            $dataArray['observacion'] .= 'COMBINATORIA NO ENCONTRARA (ITEMPLAN - CTO ADJUDICADO - TIPO REFORZAMIENTO - REFORZAMIENTO CTO DISEÑO).'.'<br>';                                          
                        }          
                       
                    }else{
                        $ctnInValidos++;
                        $dataArray['observacion']    = 'DATOS INCOMPLETOS Y/O INVALIDOS EN COMBINATORIA (ITEMPLAN - CTO ADJUDICADO - TIPO REFORZAMIENTO - REFORZAMIENTO CTO DISEÑO)';
                        $dataArray['estatus']        = 'ERROR';                                            
                    }
                    $dataArray['itemplan']       = $itemplan;
                    $dataArray['ctoAdjudicado']  = $ctoAdjudicado;
                    $dataArray['tipoReforza']    = $tipoReforza;
                    $dataArray['ctoDiseno']      = $ctoDiseno;
                    $dataArray['fase']           = $fase;
                    $dataArray['codigoDivisor']  = $codigoDivisor;
                    $dataArray['cable']          = $cable; 
                    $dataArray['hilo']           = $hilo;     
                    $dataArray['cto_final']      = $ctoFinal;
                    $arrayTablaRegRef []= $dataArray;
                    
                }
            }

			$html = $this->tablaRegistroRegistroRefor($arrayTablaRegRef);
            $data['titulo1'] = 'Cantidad de registros válidos: '.$ctnValidos;
            $data['titulo2'] = 'Cantidad de registros inválidos: '.$ctnInValidos;
			$data['tbReporte'] = $html;
			$data['jsonDataFileUpd'] = json_encode($arrayToUpdate);
            $data['jsonItemList']    = json_encode(array_unique($itemplanUpdLog));
			$data['msj']  = 'Se procesó correctamente el archivo!!';
			$data['error']  = EXIT_SUCCESS;

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function tablaRegistroRegistroRefor($arrayTablaPO) {
        $html = '<table id="tbRegistroRefo" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ITEMPLAN</th>
                            <th>CTO ADJUDICADO</th>
                            <th>TIPO REFORZAMIENTO</th>  
                            <th>REFORZAMIENTO CTO DISEÑO</th>  
                            <th>FASE</th>
                            <th>CTO FINAL</th> 
                            <th>CODIGO DIVISOR</th>
                            <th>CABLE</th>
                            <th>HILO</th>
                            <th>ESTATUS</th>
                            <th>OBSERVACION</th>
                        </tr>
                    </thead>
                    <tbody>';
        if($arrayTablaPO != null){
            foreach ($arrayTablaPO as $row) {            
                $style = '';
                if ($row['observacion'] != '') {
                    $htmlColorFila = 'style="background:#FDBDBD"';                 
                } else {
                    $htmlColorFila = '';
                }        
                $html .= ' <tr '.$htmlColorFila.'>                         
                                <td>'.$row['itemplan'].'</td>
                                <td>'.$row['ctoAdjudicado'].'</td>
                                <td>'.$row['tipoReforza'].'</td>    
                                <td>'.$row['ctoDiseno'].'</td>
                                <td>'.$row['fase'].'</td>
                                <td>'.$row['codigoDivisor'].'</td>
                                <td>'.$row['cable'].'</td>
                                <td>'.$row['hilo'].'</td>
                                <td>'.$row['cto_final'].'</td>
                                <td>'.$row['estatus'].'</td>
                                <td>'.$row['observacion'].'</td>
                            </tr>';
             }
        }
        $html .= '</tbody>
            </table>';

        return $html;
    }

    public function procesarFileToSegRefor()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $arrayDetToFormu    = $this->input->post('arrayDetToFormu') ? json_decode($this->input->post('arrayDetToFormu'),true) : array();
            $arrayItemList    = $this->input->post('arrayItemList') ? json_decode($this->input->post('arrayItemList'),true) : array();

            $idUsuario          = $this->session->userdata('idPersonaSessionPan');

            if (count($arrayDetToFormu) == 0 ) {
                throw new Exception('No hay un detalle de materiales para registrar la po!!');
            }

			if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
			$itemUpdReg = array();
            $logFirstReg = array();
            $fechaActual = $this->m_utils->fechaActual();
            foreach($arrayItemList as $item){
                $itemArray = array('itemplan' =>  $item,
                              'situacion_general_reforzamiento'   =>   1);
                array_push($itemUpdReg, $itemArray);

                $logitem = array('itemplan' =>  $item,
                                'fecha_registro'    =>  $fechaActual,
                                'usuario_registro'  =>  $idUsuario, 
                                'id_motivo_seguimiento' =>  1,
                                'comentario_incidencia' =>  'REGISTRADO');
                             
                array_push($logFirstReg, $logitem);
            }
            
            $data = $this->m_utils->updateBatchSeguiFormulario($arrayDetToFormu, $itemUpdReg, $logFirstReg);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }        

        } catch (Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    
}
