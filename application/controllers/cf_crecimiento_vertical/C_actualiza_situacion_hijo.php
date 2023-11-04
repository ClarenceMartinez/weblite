<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_actualiza_situacion_hijo extends CI_Controller {


    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_crecimiento_vertical/m_actualiza_situacion_hijo');        
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, 7, null, 40, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_crecimiento_vertical/v_actualiza_situacion_hijo',$data);        	  
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
			$hoja->setCellValueByColumnAndRow($col, $row, 'IP HIJO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SITUACION ESPECIFICA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COMENTARIO');
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

            $hoja->getStyle('A1:C1')->applyFromArray($estiloTituloColumnas);

			

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
                    
                    $ip_hijo        = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $situacion      = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $comentario     = $worksheet->getCellByColumnAndRow(2, $row)->getValue();

                    $exitRow = array();
                    $exitRow['ip_general']          =   '';
                    $exitRow['ip_hijo']             =   $ip_hijo;                   
                    $exitRow['subproyecto']         =   '';
                    $exitRow['eecc']                =   '';
                    $exitRow['situacion']           =   $situacion;
                    $exitRow['comentario']          =   trim(strtoupper($comentario));
                    $exitRow['observacion']         =   '';
                    $exitRow['status']              =   1;

                    $infoIpHijo = $this->m_actualiza_situacion_hijo->exist_ip_hijo_info($ip_hijo);
                    if($infoIpHijo    !=  null){//EXISTE ITEMPLAN
                        $exitRow['ip_general']          =   $infoIpHijo['itemplan'];          
                        $exitRow['subproyecto']         =   $infoIpHijo['subProyectoDesc'];
                        $exitRow['eecc']                =   $infoIpHijo['empresaColabDesc'];

                        $situaEspecifica = $this->m_actualiza_situacion_hijo->existSituacionEspecifica(trim(strtoupper($situacion)));
                        if($situaEspecifica!=null){

                            $dataInsertSeguimiento = array(  
                                'itemplan'              =>  $exitRow['ip_general'],
                                'itemplan_hijo'         =>  $ip_hijo,
                                'idEstadoPlan'          =>  $infoIpHijo['idEstadoPlan'],
                                'usuario_registro'      =>  $idUsuario,
                                'fecha_registro'        =>  $fechaActual,
                                'id_motivo_seguimiento' =>  $situaEspecifica['id'],
                                'comentario_incidencia' =>  trim(strtoupper($comentario))
                            );

                            $dataPoDetCvHijo = array(
                                'ip_hijo'                   =>  $ip_hijo,
                                'ult_situa_especifica'      =>  $situaEspecifica['id'],
                                'fec_ult_situa_especifia'   =>  $fechaActual,
                                'ultimo_comentario'         =>  trim(strtoupper($comentario))
                            );

                            $dataIP = $this->m_actualiza_situacion_hijo->registroSeguiHijosMasivo($dataPoDetCvHijo, $dataInsertSeguimiento);

                            if($dataIP['error']    ==  EXIT_SUCCESS){ 
                                $exitRow['observacion'] =   'REGISTRO CORRECTO';
                                $exitRow['status']  =    0;
                            }else{
                                $exitRow['observacion'] =   $dataIP['msj'];
                            }
                        }else{
                            $exitRow['observacion'] =   'SITUACION NO RECONOCIDA';
                        }
                    }else{
                        $exitRow['observacion'] =   'ITEMPLAN HIJO NO EXISTENTE';
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
							<th>ITEMPLAN GENERAL</th>
							<th>ITEMPLAN HIJO</th>
                            <th>SUBPROYECTO</th>
                            <th>EECC</th>
							<th>SITUACION ESPECIFICA</th>
                            <th>COMENTARIO</th>
                            <th>STATUS</th>
                            <th>OBSERVACIÓN</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                            
        foreach($dataTabla as $row){
            $html .=' <tr>
                        <td>'.$row['ip_general'].'</td>
                        <td>'.$row['ip_hijo'].'</td>
                        <td>'.$row['subproyecto'].'</td>
                        <td>'.$row['eecc'].'</td>
                        <td>'.$row['situacion'].'</td>
                        <td>'.$row['comentario'].'</td>
                        <td>'.(($row['status']  ==  0) ? '<span class="badge badge-success badge-pill">REGISTRADO</span>' : '<span class="badge badge-danger badge-pill">FALLIDO</span>').'</td>
                        <td>'.$row['observacion'].'</td>
                    </tr>';
        }
        $html .='
                </tbody>
                <tfoot class="thead-themed">
                    <tr>
                        <th>ITEMPLAN GENERAL</th>
                        <th>ITEMPLAN HIJO</th>
                        <th>SUBPROYECTO</th>
                        <th>EECC</th>
                        <th>SITUACION ESPECIFICA</th>
                        <th>COMENTARIO</th>
                        <th>STATUS</th>
                        <th>OBSERVACIÓN</th>
                    </tr>
                </tfoot>
            </table>';
                    
        return $html;
    }    

}