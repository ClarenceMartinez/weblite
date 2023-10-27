<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registro_hijo_masivo extends CI_Controller {


    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_crecimiento_vertical/m_registro_hijo_masivo');        
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, 7, null, 39, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_crecimiento_vertical/v_registro_hijo_masivo',$data);        	  
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
			$hoja->setCellValueByColumnAndRow($col, $row, 'IP GENERAL');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'IP HIJO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NRO DEPA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TIPO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE ENTREGA DEL EDIFICIO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DIRECCION');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NUMERO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'MANZANA');
			$col++;
       		$hoja->setCellValueByColumnAndRow($col, $row, 'LOTE');
			$col++;			
            $hoja->setCellValueByColumnAndRow($col, $row, 'BLOQUE');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'URBANIZACION');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NRO PISOS');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIPO CABLEADO ACTUAL');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'AREA COMUN');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'COMPETENCIA');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NOMBRE DEL ADMINISTRADOR / RESPONSABLE DEL EDIFICIO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'E-MAIL DEL CONTACTO - TRABAJO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NÚMERO DE CONTACTO - MÓVIL');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NÚMERO DE CONTACTO - OTRO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'REQ ADICIONALES DADOS POR EL EDIFICIO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIENE DUCTERIAS A LA CALLE');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'MONTANTE');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ABRE PUERTA');
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

            $hoja->getStyle('A1:Z1')->applyFromArray($estiloTituloColumnas);

			

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
                    $ip_general     = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $ip_hijo        = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    
                    $exitRow = array();                    
                    $exitRow['ip_general']          =   $ip_general;
                    $exitRow['ip_hijo']             =   $ip_hijo;
                    $exitRow['fecha_registro']      =   $fechaActual;
                    $exitRow['subproyecto']         =   '';
                    $exitRow['eecc']                =   '';
                    $exitRow['codigo_inversion']    =   '';
                    $exitRow['observacion']         =   '';
                    $exitRow['status']              =   1;
                    $exist_ip = $this->m_registro_hijo_masivo->getBasicInfoItemplan($ip_general);
                    if($exist_ip    !=  null){//EXISTE ITEMPLAN
                        $exitRow['subproyecto']         =   $exist_ip['subProyectoDesc'];
                        $exitRow['eecc']                =   $exist_ip['empresaColabDesc'];
                        $exitRow['codigo_inversion']    =   $exist_ip['codigoInversion'];                        
                        $motivo_ini = 47;//PDT DE OC
                        if($exist_ip['orden_compra']!=null){//PDT LLAMAR CLIENTE                          
                            $motivo_ini = 31;
                        }
                        $exist_ip_hijo = $this->m_registro_hijo_masivo->existItemplanHijo($ip_hijo);
                        if($exist_ip_hijo    ==  0){//NO EXISTE ITEMPLAN HIJO
                                                                             
                            $codigo         = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                            $nro_depa       = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                            $tipo           = $worksheet->getCellByColumnAndRow(4, $row)->getValue();                    
                            $fec_entraga    = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                            $distrito       = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                            $direccion      = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                            $numero         = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                            $manzana        = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                            $lote           = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                            $bloque         = $worksheet->getCellByColumnAndRow(11, $row)->getValue();                    
                            $urbanizacion   = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                            $nro_piso       = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                            $cableado_actual= $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                            $area_comun     = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                            $competencias   = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                            $contacto       = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                            $email_contac   = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                            $numero_contac  = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                            $numero_otro    = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                            $req_adiciona   = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                            $tiene_ducteria = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
                            $montante       = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                            $divicau        = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
                            $fec_abre_pu    = $worksheet->getCellByColumnAndRow(25, $row)->getValue();

                            $dataRegistro = array(
                                'itemplan'          => $ip_general,
                                'ip_hijo'           => $ip_hijo,
                                'nro_depa'          => $nro_depa,
                                'tipo'              => $tipo,           	 
                                'fec_entrega'       => $fec_entraga,
                                'distrito'          => $distrito,
                                'direccion'         => $direccion,
                                'numero'            => $numero,
                                'manzana'           => $manzana,
                                'lote'              => $lote,
                                'bloque'            => $bloque,
                                'urbanizacion'      => $urbanizacion,
                                'nro_pisos'         => $nro_piso,
                                'cableado_actual'   => $cableado_actual,
                                'area_comun'        => $area_comun,
                                'competencias'      => $competencias,
                                'contacto'          => $contacto,
                                'email_contacto'    => $email_contac,
                                'telefono_contacto' => $numero_contac,
                                'telefono_otro'     => $numero_otro,
                                'reque_adicional'   => $req_adiciona,
                                'has_ducteria'      => $tiene_ducteria,
                                'montante'          => $montante,
                                'divicau'           => $divicau,
                                'usua_reg'          => $idUsuario,
                                'fec_reg'           => $fechaActual,
                                'ult_situa_especifica'      =>  $motivo_ini,
                                'fec_ult_situa_especifia'   =>  $fechaActual,
                                'codigo'                    =>  $codigo,
                                'fecha_abre_puerta'         =>  $fec_abre_pu
                            );
                                                      
                            $dataInsertSeguimiento = array(  
                                'itemplan'              =>  $ip_general,
                                'itemplan_hijo'         =>  $ip_hijo,
                                'idEstadoPlan'          =>  ID_ESTADO_PLAN_PRE_REGISTRO,
                                'usuario_registro'      =>  $idUsuario,
                                'fecha_registro'        =>  $fechaActual,
                                'id_motivo_seguimiento' =>  $motivo_ini,
                                'comentario_incidencia' =>  'AUTOMÁTICO'
                            );

                            $dataIP = $this->m_registro_hijo_masivo->registroHijosMasivo($dataRegistro, $dataInsertSeguimiento);

                            if($dataIP['error']    ==  EXIT_SUCCESS){ 
                                $exitRow['observacion'] =   'REGISTRO CORRECTO';
                                $exitRow['status']  =    0;
                            }else{
                                $exitRow['observacion'] =   $dataIP['msj'];
                            }
                        }else if($exist_ip_hijo == 1){//YA EXISTE EL IP HIJO
                            $exitRow['observacion'] =   'ITEMPLAN HIJO EXISTENTE';
                        }
                    }else{//NO EXISTE ITEMPLAN
                        $exitRow['observacion'] =   'ITEMPLAN NO EXISTE';
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
							<th>CÓDIGO INVERSIÓN</th>
                            <th>FECHA REGISTRO</th>
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
                        <td>'.$row['codigo_inversion'].'</td>
                        <td>'.$row['fecha_registro'].'</td>
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
                        <th>CÓDIGO INVERSIÓN</th>
                        <th>FECHA REGISTRO</th>
                        <th>STATUS</th>
                        <th>OBSERVACIÓN</th>
                    </tr>
                </tfoot>
            </table>';
                    
        return $html;
    }    

}