<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registro_solicitud_cotizacion extends CI_Controller {


    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_b2b/m_registro_solicitud_cotizacion');        
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->library('excel');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 42, null, 43, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_b2b/v_registro_solicitud_cotizacion',$data);        	  
    	 }else{
        	 redirect('login','refresh');
	    }     
    }


    function getExcelCargaMasiva()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			/*AQUI CREAMOS EL EXCEL*/
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
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('FORMATO CARGA');

			// $col = 0;
			// $row = 1;
			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SISEGO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CLIENTE');
			$col++;	
            $hoja->setCellValueByColumnAndRow($col, $row, 'SEGMENTO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CASIFICACION');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIPO REQUERIMIENTO');
			$col++;           
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIPO CLIENTE');
			$col++;	 
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIPO ENLACE');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DEPARTAMENTO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'PROVINCIA');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'LATITUD');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'LONGITUD');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ACCESO CLIENTE');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TENDIDO EXTERNO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ESTUDIO');
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

            $hoja->getStyle('A1:O1')->applyFromArray($estiloTituloColumnas);

			

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
			
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		echo json_encode($data);

	}

    public function regItemplanCvMasivo() 
    {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null; 

        try {
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			$fechaActual = $this->m_utils->fechaActual();
            if($idUsuario == null) {
                throw new Exception('La sesión a expirado, recargue la página');
            }
            if(count($_FILES) == 0){
                throw new Exception('Debe poner un archivo para registrar!! ');
            }
            $arrayHtmlExit = array();
            
            $path   = $_FILES['file']['tmp_name'];
            $object = PHPExcel_IOFactory::load($path);
            foreach($object->getWorksheetIterator() as $worksheet) {
                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                for($row=2; $row<=$highestRow; $row++) {
                                    
                    $sisego             = strtoupper(trim($worksheet->getCellByColumnAndRow(0, $row)->getValue()));
                    $cliente            = strtoupper(trim($worksheet->getCellByColumnAndRow(1, $row)->getValue()));
                    $segmento           = strtoupper(trim($worksheet->getCellByColumnAndRow(2, $row)->getValue()));
                    $clasificacion      = strtoupper(trim($worksheet->getCellByColumnAndRow(3, $row)->getValue()));
                    $tipo_requerimiento = strtoupper(trim($worksheet->getCellByColumnAndRow(4, $row)->getValue()));
                    $tipo_cliente       = strtoupper(trim($worksheet->getCellByColumnAndRow(5, $row)->getValue()));
                    $tipo_enlace        = strtoupper(trim($worksheet->getCellByColumnAndRow(6, $row)->getValue()));
                    $departamento       = strtoupper(trim($worksheet->getCellByColumnAndRow(7, $row)->getValue()));
                    $provincia          = strtoupper(trim($worksheet->getCellByColumnAndRow(8, $row)->getValue()));
                    $distrito           = strtoupper(trim($worksheet->getCellByColumnAndRow(9, $row)->getValue()));
                    $latitud            = strtoupper(trim($worksheet->getCellByColumnAndRow(10, $row)->getValue()));
                    $longitud           = strtoupper(trim($worksheet->getCellByColumnAndRow(11, $row)->getValue()));
                    $acceso_cliente     = strtoupper(trim($worksheet->getCellByColumnAndRow(12, $row)->getValue()));
                    $tendido_externo    = strtoupper(trim($worksheet->getCellByColumnAndRow(13, $row)->getValue()));
                    $estudio            = strtoupper(trim($worksheet->getCellByColumnAndRow(14, $row)->getValue()));

                    $exitRow = array();
                    $exitRow['codigo_cl']      =   '';               
                    $exitRow['eecc']           =   '';
                    $exitRow['observacion']    =   '';
                    $exitRow['mdf']            =   '';
                    $exitRow['jefatura']       =   '';
                    $exitRow['status']         =   1;
                    $exitRow['sisego']         =   $sisego;
                    $exitRow['cliente']        =   $cliente;
                    $exitRow['segmento']       =   $segmento;
                    $exitRow['acceso']         =   $acceso_cliente;
                    $exitRow['clasificacion']  =   $clasificacion;
                    $exitRow['estudio']        =   $estudio;
                    if($sisego!='' && $cliente!='' && $segmento!='' && $clasificacion!='' && $tipo_requerimiento!='' && $tipo_cliente!='' && $tipo_enlace!='' && $departamento!=''
                        && $provincia!='' && $distrito!='' && $latitud!='' && $longitud!='' && $acceso_cliente!='' && $tendido_externo!='' && $estudio!=''  ){

                        $exist_sisego_estudio = $this->m_registro_solicitud_cotizacion->existSisegoEstudioActivo($sisego, $estudio);        
                        if($exist_sisego_estudio == 0){ 

                            $idSubproyecto   = $this->m_utils->getIdSubProyectoBySubProyectoDesc($segmento);
                            if($idSubproyecto != null){
                               // log_message('error','$longitud - $latitud:'.$longitud.'|'.$latitud);
                                if($tipo_cliente == 'CATV' || $clasificacion == 'CATV') {
                                     $arrayIdCentral = _getDataKmzNodoHfc($longitud, $latitud);
                                } else {                                   
                                    $arrayIdCentral = _getDataKmz($latitud, $longitud);
                                 }
                            //    log_message('error', print_r($arrayIdCentral,true));
                                $idCentral = $arrayIdCentral[0]['idCentral'];
                                $codigo    = $arrayIdCentral[0]['codigo'];
                                if($idCentral != null && $idCentral != '') {                                   
                                    $exitRow['mdf']      = $codigo;
                                    $arrayDataCentral    = $this->m_utils->getDataCentralPqtById($idCentral);
                                    $exitRow['eecc']     = $arrayDataCentral['empresaColabDesc'];
                                    $exitRow['jefatura'] = $arrayDataCentral['jefaturaDesc'];
                                    $codigo_cl           = $this->m_utils->getCodCluster();
                                    $exitRow['codigo_cl'] =   $codigo_cl;

                                    $dataInsert = array(
                                                        'codigo_cluster'        =>  $codigo_cl,
                                                        'sisego'                =>  $sisego, 
                                                        'cliente'               =>  $cliente,  
                                                        'segmento'              =>  $segmento,
                                                        'clasificacion'         =>  $clasificacion,
                                                        'tipo_requerimiento'    =>  $tipo_requerimiento,
                                                        'tipo_cliente'          =>  $tipo_cliente,
                                                        'tipo_enlace'           =>  $tipo_enlace,
                                                        'departamento'          =>  $departamento,
                                                        'provincia'             =>  $provincia,
                                                        'distrito'              =>  $distrito,
                                                        'latitud'               =>  $latitud,
                                                        'longitud'              =>  $longitud,
                                                        'acceso_cliente'        =>  $acceso_cliente,
                                                        'tendido_externo'       =>  $tendido_externo,
                                                        'nombre_estudio'        =>  $estudio,
                                                        'estado'                =>  0,
                                                        'flg_tipo'        	    =>  2,
                                                        'flg_paquetizado'       =>  1,
                                                        'idCentral'             =>  $idCentral,
                                                        'nodo_respaldo'         =>  $idCentral,
                                                        'nodo_principal'        =>  $idCentral,
                                                        'idSubProyecto'         =>  $idSubproyecto,
                                                        'idEmpresaColab'        =>  $arrayDataCentral['idEmpresaColab'],
                                                        'fecha_registro'        =>  $fechaActual,
                                                        'flg_robot'             =>  2//eecc
                                                        );
                                                    
                                    $dataInsert = $this->m_registro_solicitud_cotizacion->insertSolicitudCotizacion($dataInsert);
                                    if($dataInsert['error']    ==  EXIT_SUCCESS){ 
                                        $exitRow['observacion'] =   'REGISTRO CORRECTO';
                                        $exitRow['status']      =    0;
                                    }else{
                                        $exitRow['observacion'] =   $dataInsert['msj'];
                                    }
                                }else{
                                    $exitRow['observacion'] =   'NO SE ENCONTRO UNA CENTRAL PARA LAS COORDENADAS INGRESADAS';
                                }   
                            }else{
                                $exitRow['observacion'] =   'SEGMENTO NO RECONOCIDO (EMPRESAS, NEGOCIO, MAYORISTA)';
                            }                            
                        }else{
                            $exitRow['observacion'] =   'SISEGO - TIPO ESTUDIO EXISTENTE';
                        }
                    }else{
                        $exitRow['observacion'] =   'NO SE PERMITEN CAMPOS EN BLANCO';
                    } 

                    array_push($arrayHtmlExit, $exitRow);
                }
            }
           
            $data['tablaItem'] = $this->getTablaItems($arrayHtmlExit);          
            $data['error']   =   EXIT_SUCCESS;
        }catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }    

    function getTablaItems($dataTabla) {
        $html = '<table id="tb_carga" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
							<th>CODIGO SOLICITUD</th>
							<th>SISEGO</th>
                            <th>CLIENTE</th>
                            <th>SEGMENTO</th>
                            <th>ACCESO CLIENTE</th>
                            <th>CLASIFICACION</th>
							<th>ESTUDIO</th>   
                            <th>JEFATURA</th>  
                            <th>MDF</th>                           
                            <th>EECC</th>
                            <th>STATUS</th>
                            <th>OBSERVACIÓN</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                            
        foreach($dataTabla as $row){
            $html .=' <tr>
                        <td>'.$row['codigo_cl'].'</td>
                        <td>'.$row['sisego'].'</td>
                        <td>'.$row['cliente'].'</td>
                        <td>'.$row['segmento'].'</td>
                        <td>'.$row['acceso'].'</td>                        
                        <td>'.$row['clasificacion'].'</td>
                        <td>'.$row['estudio'].'</td>    
                        <td>'.$row['jefatura'].'</td>   
                        <td>'.$row['mdf'].'</td>                     
                        <td>'.$row['eecc'].'</td>
                        <td>'.(($row['status']  ==  0) ? '<span class="badge badge-success badge-pill">REGISTRADO</span>' : '<span class="badge badge-danger badge-pill">FALLIDO</span>').'</td>
                        <td>'.$row['observacion'].'</td>
                    </tr>';
        }  
        $html .='
                </tbody>
                <tfoot class="thead-themed">
                    <tr>
                        <th>CODIGO SOLICITUD</th>
                        <th>SISEGO</th>
                        <th>CLIENTE</th>
                        <th>SEGMENTO</th>
                        <th>ACCESO CLIENTE</th>
                        <th>CLASIFICACION</th>
                        <th>ESTUDIO</th>    
                        <th>JEFATURA</th>   
                        <th>MDF</th>                       
                        <th>EECC</th>
                        <th>STATUS</th>
                        <th>OBSERVACIÓN</th>
                    </tr>
                </tfoot>
            </table>';
                    
        return $html;
    }    

}