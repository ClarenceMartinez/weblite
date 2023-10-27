<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registro_itemplan_hijo_masivo extends CI_Controller {


    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_reforzamiento_cto/M_registro_itemplan_hijo_masivo');
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, 7, null, 13, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_reforzamiento_cto/v_registro_itemplan_hijo_masivo',$data);        	  
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
			$hoja->setCellValueByColumnAndRow($col, $row, 'IP MADRE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'LATITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'LONGITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DIVCAU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NOM. PROYECTO');
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

            $hoja->getStyle('A1:E1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
            $hoja->setCellValueByColumnAndRow($col, $row, 'P-XX-XXXXXXXXX');            
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '-12.04318');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '-77.02824');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'SMG354');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ABCDEFG');
            $col++;            

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

    public function regItemplanRefMasivo() 
    {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null; 

        try {
            $idUsuario = $this->session->userdata('idPersonaSessionPan');

            if($idUsuario == null) {
                throw new Exception('La sesión a expirado, recargue la página');
            }
            if(count($_FILES) == 0){
                throw new Exception('Debe poner un archivo para registrar!! ');
            }
            $arrayRegistro = array();
            $cont = 0;
            $path   = $_FILES['file']['tmp_name'];
            $object = PHPExcel_IOFactory::load($path);
            
            $dataTabla = array();
            foreach($object->getWorksheetIterator() as $worksheet) {
                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $cont = 1;
                for($row=2; $row<=$highestRow; $row++) {
                    $itemplan_m = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $dataArray = array();                  
                    $dataArray['nro']            = $cont;              
                    $dataArray['itemplan_m']     = $itemplan_m;
                    $dataArray['itemplan']       = '';
                    $dataArray['observacion']    = '';
                    $dataArray['eecc']           = '';
                    $dataArray['subproyecto']    = '';
                    $dataArray['nom_plan']       = '';
                    $dataArray['fecha_registro'] = '';

                    $be_insert = true;
                    
                    $infoMadre  = $this->m_utils->getInfoItemplanMadreRefo($itemplan_m);
                    if($infoMadre == null){
                        $dataArray['observacion'] .= 'ITEMPLAN MADRE NO RECONOCIDO.'.'<br>';
                        $be_insert = false;
                    }

                    $coord_y            = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $coord_x            = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $dataCentral        = _getCentralCercana($coord_x, $coord_y);

                    if($dataCentral == null){
                        $dataArray['observacion'] .= 'NO SE ENCONTRO CENTRAL PARA LA COORDENADAS INGRESADAS.'.'<br>';
                        $be_insert = false;
                    }

                    $costo_mo  = 0;
                    /*/$costo_mo           = $worksheet->getCellByColumnAndRow(6, $row)->getValue();        
                    if($costo_mo == null ||  $costo_mo <= 0){
                        $dataArray['observacion'] .= 'DEBE INGRESAR UN COSTO DE MO VALIDO.'.'<br>';
                        $be_insert = false;
                    }*/

                    //$nro_cto           = $worksheet->getCellByColumnAndRow(5, $row)->getValue();      
                    $nro_cto           = 1;      
                    if($nro_cto == null ||  $nro_cto <= 0){
                        $dataArray['observacion'] .= 'DEBE INGRESAR EL NRO DE CTO VALIDO.'.'<br>';
                        $be_insert = false;
                    }

                    if($coord_x == null || $coord_x == '' || !is_numeric($coord_x)) {
                        $dataArray['observacion'] .= 'DEBE INGRESAR LA LONGITUD VALIDO.'.'<br>';
                        $be_insert = false;
                    }
                    
                    if($coord_y == null || $coord_y == '' || !is_numeric($coord_y)) {
                        $dataArray['observacion'] .= 'DEBE INGRESAR LA LATITUD VALIDO.'.'<br>';
                        $be_insert = false;
                    }
                    log_message('error', $coord_y);
                    if($coord_y <= -68 && $coord_y >= -81) {// SI LA COORDENADA Y SE ENCUENTRA EN EL REANGO DE LA LONG.
                        $dataArray['observacion'] .= 'La coordenada "X" (longitud), que se esta mandando pertenece a "Y" (latitud), enviar de manera correcta.'.'<br>';
                        $be_insert = false;
                    }
    

                    if($be_insert){
                        $divcau             = $worksheet->getCellByColumnAndRow(3, $row)->getValue();                    
                        $nombreProyecto     = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $fechaActual        = $this->m_utils->fechaActual();
                        $idFase             = ($this->m_utils->getFaseAll())[0]['idFase'];

                        $idSubProyecto      = 748;//TODO VA A REFORZAMIENTO HIJOS
                        $dataSubProyecto    = $this->m_utils->getDataSubProyectoById($idSubProyecto);
                        
                        $new_itemplan       = $this->m_utils->getCodigoItemplan($dataCentral['idZonal'],$dataSubProyecto['idProyecto']); //BTENEMIS CODIGO ITEMPLAN

                        $dataInsert = array(
                            "itemplan"         =>  $new_itemplan,
                            "nombrePlan"       =>  strtoupper($nombreProyecto),
                            "longitud"         =>  $coord_x,
                            "latitud"          =>  $coord_y,
                            "indicador"        =>  strtoupper($nombreProyecto), 
                            "uip"              =>  intval($nro_cto),
                            "fechaRegistro"    =>  $fechaActual,
                            "usua_crea_obra"   =>  $idUsuario, 
                            "fechaInicio"      =>  $fechaActual,
                            "idEstadoPlan"     =>  intval(ID_ESTADO_PLAN_EN_OBRA),
                            "idFase"           =>  intval($idFase),
                            "idCentral"        =>  intval($dataCentral['idCentral']),
                            "idSubProyecto"    =>  $idSubProyecto,
                            "idZonal"          =>  intval($infoMadre['idZonal']),
                            "idEmpresaColab"   =>  intval($infoMadre['idEmpresaColab']),
                            "has_cotizacion"   =>  '0',
                            "hasAdelanto"      =>  '0',
                            "paquetizado_fg"   => 1,
                            "costo_unitario_mat" => 0,
                            "costo_unitario_mo"  => $costo_mo,
                            "cantFactorPlanificado"     => $nro_cto,
                            "idPqtTipoFactorMedicion"   => 3,
                            "idUsuarioLog"      => $idUsuario,
                            "fechaLog"          => $fechaActual,
                            "usu_upd"           => $idUsuario,
                            "fecha_upd"         => $fechaActual,      
                            "descripcion"       =>  'REGISTRO MASIVO IP HIJO REFORZAMIENTO',
                            'itemplan_m'        => $itemplan_m
                        );
                        $data = $this->M_registro_itemplan_hijo_masivo->registrarItemplanMasivoCV($dataInsert);
                        if($data['error'] == EXIT_ERROR){
                            $dataArray['observacion'] .= $data['msj'].'<br>';
                        }else{
                            $eeccInfo = $this->m_utils->getInfoEECCByIdEECC($infoMadre['idEmpresaColab']);
                            $dataArray['itemplan']       = $new_itemplan;
                            $dataArray['observacion']    = 'REGISTRO CORRECTO.';
                            $dataArray['subproyecto']    = $dataSubProyecto['subProyectoDesc'];
                            $dataArray['eecc']           =  $eeccInfo['empresaColabDesc'];
                            $dataArray['nom_plan']       = strtoupper($nombreProyecto);
                            $dataArray['fecha_registro'] = $fechaActual;
                        }
                     }
                    array_push($dataTabla, $dataArray);
                    $cont ++;
                }
               
            }
       
            $data['tablaItem'] = $this->getTablaItems($dataTabla);
            $data['error']     = EXIT_SUCCESS;
        }catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getTablaItems($dataTabla) {
        $html = '<table id="tb_carga" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
							<th>NRO</th>
                            <th>ITEMPLAN MADRE</th>
							<th>ITEMPLAN HIJO</th>
                            <th>SUBPROYECTO</th>
                            <th>EECC</th>
                            <th>NOMBRE PLAN</th>
                            <th>FECHA REGISTRO</th>
                            <th>STATUS</th>
                            <th>OBSERVACIÓN</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                            
        foreach($dataTabla as $row){
            $html .=' <tr>
                        <td>'.$row['nro'].'</td>
                        <td>'.$row['itemplan_m'].'</td>
                        <td>'.$row['itemplan'].'</td>
                        <td>'.$row['subproyecto'].'</td>
                        <td>'.$row['eecc'].'</td>
                        <td>'.$row['nom_plan'].'</td>
                         <td>'.$row['fecha_registro'].'</td>
                        <td>'.(($row['itemplan'] != '') ? '<span class="badge badge-success badge-pill">REGISTRADO</span>' : '<span class="badge badge-danger badge-pill">FALLIDO</span>').'</td>
                        <td>'.$row['observacion'].'</td>
                    </tr>';
        }
        $html .='
                </tbody>
                <tfoot class="thead-themed">
                    <tr>
                        <th>NRO</th>
                        <th>ITEMPLAN MADRE</th>
                        <th>ITEMPLAN HIJO</th>
                        <th>SUBPROYECTO</th>
                        <th>EECC</th>
                        <th>NOMBRE PLAN</th>
                        <th>FECHA REGISTRO</th>
                        <th>STATUS</th>
                        <th>OBSERVACIÓN</th>
                    </tr>
                </tfoot>
            </table>';
                    
        return $html;
    }
 

}