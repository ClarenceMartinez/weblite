<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_liquidacion_po_mo extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_crecimiento_vertical/m_registro_manual_po_mat');
        $this->load->model('mf_consulta/m_registro_po_mo');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        $this->load->library('excel');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario      = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
        $itemplan       = $this->input->get('item');
        $idEstacion     = $this->input->get('estacion');
        $estacionDesc   = $this->input->get('estaciondesc');

	    if($idUsuario != null && $itemplan != null && $itemplan != '' && $idEstacion != null && $idEstacion != '' && $estacionDesc != null && $estacionDesc != ''){
            $data['estacionDesc'] = $estacionDesc;
            $data['itemplan'] = $itemplan;
            $data['idEstacion'] = $idEstacion;
         
            $ptr     = $this->input->get('poCod');
            $estadoPo    = $this->m_registro_po_mo->gestEstadoPoByItemplanPoCod($itemplan, $ptr);
            //log_message('error', 'poCod:'.$ptr);
            $canEdit = false;
            $btnLiqui = false;
            if($estadoPo    ==  ID_ESTADO_PO_LIQUIDADO){
                $has_expe_rechazado = $this->m_utils->haveSolictudRechazadoExpediente($itemplan);
                if($has_expe_rechazado > 0){
                    $canEdit = true;
                }
            }else if($estadoPo    ==  ID_ESTADO_PO_REGISTRADO){
                $canEdit = true;
                $btnLiqui   = true;
            }
            if($canEdit){
                list($html, $ctnValidos, $arrayFinal, $costoFinal) = $this->tablaRegistroPO($this->m_registro_po_mo->getPartidasBasicByPtr($ptr));
                $data['tablaPO']        = $html; 
                $data['costo_total']    = $costoFinal;
                $data['codPo']          = $ptr.''; 
                $data['showBtnLiqui']   = $btnLiqui;
                $permisos           = $this->session->userdata('permisosArbolPan');
                $result             = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_OBRA_PADRE, null, ID_CONSULTA_HIJO, null);
                $data['opciones']   = $result['html'];
                $data['header']     = $this->lib_utils->getHeader();
                $this->load->view('vf_consulta/v_liquidacion_po_mo',$data);        	  
            }else{
                redirect('getDetalleConsulta', 'refresh');
            }
    	 }else{
            redirect(RUTA_OBRA2, 'refresh');
	    }
        
    }

    function tablaRegistroPO($arrayTablaPO) {
    //    log_message('error', print_r($arrayTablaPO,true));
        $html = '<table id="tbRegistroPO" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>                            
                            <th>CÓDIGO</th>  
                            <th>PARTIDA</th>  
                            <th>TIPO</th>
                            <th>PRECIO</th>
                            <th>BAREMO</th>
                            <th>CANTIDAD</th>                     
                            <th>COSTO TOTAL</th>
                            <th>OBSERVACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>';
        $count = 0;
        $ctnVal = 0;
        $costoFinal = 0;
        $arrayFinal = array();
        $style = '';

        foreach ($arrayTablaPO as $row) {
            
          
        
            $html .= ' <tr>
                            <td></td>                           
                            <td>'.$row['codigoPartida'].'</td>
                            <td>'.$row['descPartida'].'</td>    
                            <td>'.$row['tipoPartida'].'</td>
                            <td>'.$row['precio'].'</td>
                            <td>'.$row['baremo'].'</td>
                            <td style="text-align:center; font-weight: bold;' . $style . '">' . $row['cantidad_ingresada'] . '</td>
                            <td style="text-align:center;">' . (is_numeric($row['costo_total']) ? number_format($row['costo_total'],2) : '-') . '</td>
                            <td> </td>
                        </tr>';
            $count++;
        }
        $html .= '</tbody>
            </table>';

        return array($html, $ctnVal, $arrayFinal, $costoFinal);
    }

    function tablaRegistroPOEdit($arrayTablaPO) {
        $html = '<table id="tbRegistroPO" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>
                            <th>OBSERVACIÓN</th>
                            <th>CÓDIGO</th>  
                            <th>PARTIDA</th>  
                            <th>TIPO</th>
                            <th>PRECIO</th>
                            <th>BAREMO</th>
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
            
            $style = '';
            if ($row['observacion'] != '') {
                $htmlColorFila = 'style="background:#FDBDBD"';
                $btnDelete = '<a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Eliminar" 
                                aria-expanded="false" data-codigo_material="'.$row['codigoPartida'].'"
                                onclick="deleteMatErroneo(this);"><i class="fal fa-delete"></i>
                            </a>';
            } else {
                $htmlColorFila = '';
                $btnDelete = '';
                $costoFinal = round($costoFinal + $row['costo_total'], 2);
                $arrayFinal[] = array(
                    "codigoPartida"     => $row['codigoPartida'],
                    "cantidadInicial"   => $row['cantidad_ingresada'],
                    "cantidadFinal"     => $row['cantidad_ingresada'],
                    "preciario"         => $row['precio'],
                    "baremo"            => $row['baremo'],
                    "montoFinal"        => $row['costo_total'],
                    "montoInicial"      => $row['costo_total']
                );
                $ctnVal++;
            }

        
            $html .= ' <tr>
                            <td>
                                <div class="d-flex demo">
                                    '.$btnDelete.'
                                </div>
                            </td>
                            <td>'.$row['observacion'].'</td>
                            <td>'.$row['codigoPartida'].'</td>
                            <td>'.$row['descPartida'].'</td>    
                            <td>'.$row['tipoPartida'].'</td>
                            <td>'.$row['precio'].'</td>
                            <td>'.$row['baremo'].'</td>
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
            $codigo_po  = $this->input->post('codigo_po');
          
            $spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Kit de Partidas')
				->setDescription('Kit de Partidas')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Kit de Partidas');

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
            $hoja->setCellValueByColumnAndRow($col, $row, 'PARTIDA');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIPO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'COSTO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'BAREMO');
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
         //   $listaMateriales = $this->m_registro_po_mo->getPartidasToCreatePoMoByItemplanEstacion($itemplan, $idEstacion);
            $listaMateriales = $this->m_registro_po_mo->getPartidasToLiquiPoMo($codigo_po, $itemplan, $idEstacion);

            foreach ($listaMateriales as $fila) {
                $col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigoPartida);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->partidaDesc);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->tipo);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->costo);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->baremo);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->cantidadInicial);
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
            $data['nombreArchivo'] = 'modelo_carga_liquidacion_po_mo_' . date("YmdHis") . '.xls';

            
        } catch (Exception $e) {
            $data['msj'] = 'Error interno, al crear archivo partidas MO';
        }

        echo json_encode($data);
    }

    public function procesarArchivoPoMo()
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
            $ctnValidos =   0;
			$objectExcel = PHPExcel_IOFactory::load($nombreFicheroTemp);
            foreach ($objectExcel->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();

				for ($row = 2; $row <= $highestRow; $row++) {
                    $col = 0;
                    $codigoPartida = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());
                    $col = 5;
                    $cantidadIngresada = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());

                    if ($cantidadIngresada != null && $cantidadIngresada != '' && is_numeric($cantidadIngresada)) {
                        $isPartidaOnKit = $this->m_registro_po_mo->getInfoPartidaExisteByItemplanEstacionPartida($itemplan, $idEstacion, $codigoPartida);
                        $dataArray['observacion'] = '';
                        if($isPartidaOnKit   !=  null) {
                            $descPartida    = $isPartidaOnKit['partidaDesc'];
                            $tipoPartida    = $isPartidaOnKit['tipo'];
                            $precio         = $isPartidaOnKit['costo'];
                            $baremo         = $isPartidaOnKit['baremo'];                            
                            $costo_total    = round(floatval($cantidadIngresada) * floatval($precio) * floatval($baremo),2);
                            $ctnValidos++;
                         }else{
                            $dataArray['observacion'] .= 'Partida no pertenece al kit.'.'<br>';
                            $descPartida = '-';
                            $tipoPartida = '-';
                            $precio = '-';
                            $baremo = '-';                         
                            $costo_total = '-';                            
                        }
                       
                        $dataArray['codigoPartida'] = $codigoPartida;
                        $dataArray['descPartida']   = $descPartida;
                        $dataArray['tipoPartida']   = $tipoPartida;
                        $dataArray['precio']        = $precio;
                        $dataArray['baremo']        = $baremo;
                        $dataArray['cantidad_ingresada'] = $cantidadIngresada;
                        $dataArray['costo_total']   = $costo_total; 

                        $arrayTablaPO []= $dataArray;
                    }
                    
                }
            }

			list($html, $ctnValidos, $arrayFinal, $costoFinal) = $this->tablaRegistroPOEdit($arrayTablaPO);
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


    public function registrarPoMo()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $itemplan   = $this->input->post('itemplan')  ? $this->input->post('itemplan') : null;
            $idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
            $costoTotalPo = $this->input->post('costoTotalPo') ? $this->input->post('costoTotalPo') : null;
            $arrayDetPo = $this->input->post('arrayDetPo') ? json_decode($this->input->post('arrayDetPo'),true) : array();
            $codigo_po = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
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

            $countPo = $this->m_utils->getCountPo($itemplan, $idEstacion, 2);
         
            $codigoPO = $codigo_po;
            if($codigoPO == null || $codigoPO == '') {
                throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
            }

            $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MO', $idEstacion);
            if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                throw new Exception("No tiene configurado un area.");
            }

            $fechaActual = $this->m_utils->fechaActual();

            $dataPo = array (
                'codigo_po'      => $codigoPO,               
                'costo_total'    => $costoTotalPo                
            );

            foreach($arrayDetPo as $key => $value) {
                $arrayDetPo[$key]['codigo_po'] = $codigoPO;
            }

            if(count($arrayDetPo) == 0){
                throw new Exception("No hay materiales válidos para el registro de la PO");
            }
            $dataLogPOEdit =    array(
                'codigo_po'        =>  $codigoPO,
                'itemplan'         =>  $itemplan,
                'idUsuario'        =>  $idUsuario,
                'fecha_registro'   =>  $fechaActual,
                'origen'           =>   'Editar / liquidar PO MO'
            );


            $data = $this->m_registro_po_mo->editPoMo($dataPo, $arrayDetPo, $dataLogPOEdit);
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

    function liquidarPOMO(){
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        $data['cabecera'] = null;
        try{
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();

            

            if($idUsuario   !=     null){
                $itemplan       = $this->input->post('itemplan');
                $codigo_po      = $this->input->post('codigo_po');     
                
                /*
                $countValid = $this->m_liquidar_mo->getCountControlPresupuestal($codigo_po);

                if ($countValid > 0) {
                    throw new Exception('Ya tiene una solicitud pendiente de aprobacion en la obra.');
                }*/

                $dataLogPO = array(
                    'codigo_po'         =>  $codigo_po,
                    'itemplan'          =>  $itemplan,
                    'idUsuario'         =>  $idUsuario,
                    'fecha_registro'    =>  $fechaActual,
                    'idPoestado'        =>  4,
                    'controlador'       =>  'editar/liquidar mo'
                );
                
                $dataUpdate = array(
                    'estado_po'     => 4
                );
                
                $data = $this->m_registro_po_mo->liquidarPO($dataLogPO, $dataUpdate, $codigo_po, $itemplan);
                if($data['error']   ==  EXIT_ERROR){
                    throw new Exception('Hubo un error interno, por favor volver a intentar.');
                }
                $data['codigoPO']    =   $itemplan;
                $data['error']       = EXIT_SUCCESS;
             
            }else{
                throw new Exception('Su sesion expiro, porfavor vuelva a logearse.');
            }
             
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
  
}
