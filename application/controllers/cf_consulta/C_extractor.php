<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_extractor extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_extractor');
        $this->load->library('lib_utils');
        $this->load->library('excel');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 38, null, 24, null);
            $data['header'] = $this->lib_utils->getHeader();
			$data['opciones'] = $result['html'];
			$data['idEmpresaColab'] = $idEmpresaColab;
            $this->load->view('vf_consulta/v_extractor',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    
    function reportePlanobraCV()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Reporte de Obras')
				->setDescription('Reporte de Obras')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de Pangeaco');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('obras_pangeaco');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO DE OBRA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SUBPROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SISEGO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NOMBRE PLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FASE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'UIP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'LONGITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'LATITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO PLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA CREACION IP');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA INICIO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA EJECUCIÓN DISEÑO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CENTRAL');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'JEFATURA');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ZONAL');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'EMPRESA COLABORADORA');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO MDF');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FECHA PRELIQUIDACION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ORDEN COMPRA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO DE INVERSION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'OT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'OT ESTADO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'USUARIO REGISTRO IP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SITUACION EXPEDIENTE');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'IP MADRE');
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

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReportePlanobraCV($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->proyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->subproyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->sisego);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nombrePlan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->faseDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cant_uip);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->longitud);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->latitud);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoPlanDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaRegistro);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaInicio);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_ult_ejec_diseno);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->centralDesc);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->jefaturaDesc);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->zonalDesc);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_mdf);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaPreLiquidacion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->orden_compra);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigoInversion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ult_codigo_sirope);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ult_estado_sirope);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->usuario_cre);
            	$col++;			
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->situacion_expediente);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan_m);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}

    function reporteDetallePoMoCV()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

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
			$hoja->setTitle('planobra_mo_pan');

			// $col = 0;
			// $row = 1;

			// $hoja->setCellValueByColumnAndRow($col, $row, 'SIROPE');
			// $hoja->mergeCells('A'.$row.':G'.$row);
			// $col = 7;
			// $hoja->setCellValueByColumnAndRow($col, $row, 'WEB PO');
			// $hoja->mergeCells('H'.$row.':L'.$row);
			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO PLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ÁREA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO PARTIDA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NOMBRE PARTIDA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'BAREMO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COSTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TOTAL');
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

            $hoja->getStyle('A1:K1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReportePoMo($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoPlanDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estado_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->areaDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigoPartida);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nomPartida);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->baremo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cantidadFinal);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->preciario);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->montoFinal);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}

	function reporteDetallePoMatCV()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

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
			$hoja->setTitle('planobra_mat_pan');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO MATERIAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DESCRIPCIÓN MATERIAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD INICIAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD FINAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COSTO MATERIAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TOTAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ÁREA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'VALE DE RESERVA');
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

            $hoja->getStyle('A1:K1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReportePoMat($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estado_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_material);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->descrip_material);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cantidadInicial);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cantidadFinal);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->costoMat);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->montoFinal);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->areaDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->vale_reserva);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}

	function reporteDetallePlanCV()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
			
			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Pangeaco')
				->setDescription('Pangeaco')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Pangeaco');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('Detalle PO de Obras');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO DE OBRA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO DE OBRA');
			$col++;			
			$hoja->setCellValueByColumnAndRow($col, $row, 'PROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SUBPROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FASE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NOMBRE PLAN');
			$col++;			
			$hoja->setCellValueByColumnAndRow($col, $row, 'JEFATURA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ZONAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'MDF');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EE.CC.');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ÁREA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COSTO MO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COSTO MAT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'VALE DE RESERVA');
			$col++;			
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA REGISTRO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'USUARIO REGISTRO PO');
			$col++;			
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO DE INVERSION');
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

            $hoja->getStyle('A1:K1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReporteDetallePlan($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoPlanDesc);
            	$col++;				
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->proyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->subproyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->faseDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nombrePlan);
            	$col++;				
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->jefaturaDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->zonalDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_mdf);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estado_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->areaDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, ($fila->flg_tipo_area == 2 ? $fila->costo_total : null));
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, ($fila->flg_tipo_area == 1 ? $fila->costo_total : null));
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->vale_reserva);
            	$col++;				
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaRegPo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->usu_reg_po);
            	$col++;				
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigoInversion);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}
	
	function getReporteSolicitudOc() {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Carlos Cuya Alvarado')
				->setLastModifiedBy('Carlos Cuya Alvarado')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Excel de prueba')
				->setDescription('Excel generado como prueba')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de prueba');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('detalleplan oc');

			$col = 0;
			$row = 1;

			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO SOLICITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TIPO OC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA CREACION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA VALIDACION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SOLPED');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ORDEN COMPRA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO CERTIFICACION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO SOLICITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'POSICION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COSTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COSTO SAP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SUBPROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO DE INVERSIÓN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'P');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'Q');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'P*Q');
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

            $hoja->getStyle('A1:K1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			$listaReporte = $this->m_extractor->getReporteExtractorOCOnline($iddEECC);

			foreach ($listaReporte as $fila) {
				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_solicitud);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->tipo_pc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_creacion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_validacion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cesta);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->orden_compra);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_certificacion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estado_solicitud);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->posicion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->costo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->costo_sap);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->proyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->subProyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigoInversion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->P);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->Q);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->costo);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);
	}

	function reporteDetHijosCV()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Reporte de Obras')
				->setDescription('Reporte de Obras')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de Pangeaco');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('obras_pangeaco');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'IP GENERAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'IP HIJO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NRO DEPARTAMENTOS');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TIPO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ENTREGA');
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'CABLEADO ACTUAL');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'AREA COMUN');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'COMPETENCIAS');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CONTACTO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'EMAIL CONTACTO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TELEFONO CONTACTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TELEFONO OTRO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'REQUE. ADICIONAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DUCTERIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'MONTANTE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ULTIMA SITUACION GENERAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ULTIMA SITUACION ESPCIFICA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FEC. ULT. SITUACION ESPCIFICA');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'COMENTARIO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FEC. ENTREGA COMERCIAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FEC. ABRE PUERTA');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'OT');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO OT');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'EVIDENCIA QUIEBRE');
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

            $hoja->getStyle('A1:AA1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReporteDetHijoCV($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ip_hijo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nro_depa);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->tipo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_entrega);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->distrito);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->direccion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->numero);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->manzana);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->lote);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->bloque);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->urbanizacion);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->nro_pisos);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->cableado_actual);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->area_comun);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->competencias);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->contacto);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->email_contacto);
            	$col++;
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->telefono_contacto);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->telefono_otro);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->reque_adicional);
            	$col++;	
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->has_ducteria);
            	$col++;	
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->montante);
            	$col++;	
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->divicau);
            	$col++;		
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->situacion_general);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->situacion_especifica);
            	$col++;	
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_ult_situa_especifia);
            	$col++;	
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ultimo_comentario);
				$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_entrega_comercial);
				$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_abre_puerta);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ult_codigo_sirope);
				$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ult_estado_sirope);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->has_evi_quiebre);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}

	function reporteDetHijosB2b()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Reporte de Obras')
				->setDescription('Reporte de Obras')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de Pangeaco');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('obras_pangeaco');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SISEGO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO PLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DEPARTAMENTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SITUACION GENERAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SITUACION ESPECIFICA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE CREACIÓN IP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA GENERACION OC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA PREV EJEC DISEÑO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA EJEC DISEÑO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA PREV CIERRE DESPLIEGUE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA CIERRE DESPLIEGUE');
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

            $hoja->getStyle('A1:O1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReporteDetHijoB2b($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->indicador);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoPlanDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->departamento);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->distrito);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->situacion_general);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->situacion_especifica);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaRegistro);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_atencion_oc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_prev_ejec_dise);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_ult_ejec_diseno);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_prev_liquidacion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaPreLiquidacion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ultimo_comentario);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);
	}

	function reporteDetallePoMat()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Reporte de Obras')
				->setDescription('Reporte de Obras')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de Pangeaco');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('obras_pangeaco');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO MATERIAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DESCRIPCION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD FINAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'VALE RESERVA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE CREACION PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA GENERACION VR');
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

            $hoja->getStyle('A1:J1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReporteDetallePoMat($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estado);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_material);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->descrip_material);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cantidadFinal);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->vale_reserva);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaRegistro);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_reg_vr);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}
	
	function reporteCotizacionesB2b()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Reporte de Obras')
				->setDescription('Reporte de Obras')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de Pangeaco');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('obras_pangeaco');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO CL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SISEGO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTUDIO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NODO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ENLACE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COSTO MO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COSTO MAT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CLASIFICACION');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'FEC. REGISTRO');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'FEC. COTIZADO');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'FEC. REGISTRO OC');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'ORDEN DE COMPRA');
			$col++;			
			$hoja->setCellValueByColumnAndRow($col, $row, 'FEC. APROBACION');
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

            $hoja->getStyle('A1:J1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReporteCotizaciones($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_cluster);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->sisego);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nombre_estudio);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nodo_principal);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->distrito);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->tipo_enlace);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->costo_mano_obra);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->costo_materiales);
            	$col++;			
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->clasificacion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_registro);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoDesc);
            	$col++;		
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_reg_cotizacion);
            	$col++;			
                $hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_reg_oc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->orden_compra);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_aprobacion);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}

	function reporteDetallePoMoAll()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

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
			$hoja->setTitle('planobra_mo_pan');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO PARTIDA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DESCRIPCIÓN PARTIDA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TIPO PRECIARIO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'BAREMO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PRECIO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD FINAL');	 
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TOTAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ÁREA');
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

            $hoja->getStyle('A1:K1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReportePoMo2($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estado_po);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigoPartida);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->descripcion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->tipoPreciario);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->baremo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->preciario);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cantidadFinal);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->montoFinal);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->areaDesc);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}
	
	function getReporteDetalleLicencias()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

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
			$hoja->setTitle('detalle licencias');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SUBPROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADOPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTACION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ENTIDAD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TIPO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NRO EXPEDIENTE');	 
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA INICIO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA FIN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NRO COMPROBANTE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA EMISION COMPROBANTE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'MONTO COMPROBANTE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA TERMINO OBRA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'LICENCIA TERMINADA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DENTRO SLA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA TERMINO LICENCIA');
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

            $hoja->getStyle('A1:R1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReporteDetalleLicencias($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->proyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->subProyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoPlanDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estacionDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->entidadDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->tipoEntidadDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nroExpediente);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaInicio);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaFin);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nroComprobante);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaEmisionComp);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->montoComp);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_termino);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->has_termino_lic);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->dentro_sla_termino);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaTermino);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}
	
	function getReporteDetalleFormReforzamientoCto()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Excel de prueba')
				->setDescription('Excel generado como prueba')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de prueba');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('Formulario Reforzamiento Cto');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SUBPROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CENTRAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO CENTRAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADOPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CTO ADJUDICADO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DIVCAU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TIPO DE REFORZAMIENTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'REFORZAMIENTO CTO DISEÑO');
			$col++;			 		
			$hoja->setCellValueByColumnAndRow($col, $row, 'OBSERVACION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA REGISTRO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'HAS SEGUIMIENTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SITUACION GENERAL');
			$col++;			 		
			$hoja->setCellValueByColumnAndRow($col, $row, 'SITUACION ESPECIFICA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ULT. SITUACION ESPECIFICA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FASE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CTO FINAL');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'COD DIVISOR');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CABLE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'HILO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO OSP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO OT OSP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ESTADO OT');
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

            $hoja->getStyle('A1:W1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = $this->m_extractor->getReporteDetalleFormReforzamientoCto($iddEECC);

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->proyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->subProyectoDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->centralDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoPlanDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cto_ajudi);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->divcau);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->tipo_refo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->do_splitter);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->observacion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_registro);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->has_seguimiento);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->situacion_general);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->situacion_especifica);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_upd_sit_especifica);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fase);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cto_final);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigo_divisor);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cable);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->hilo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ult_codigo_sirope);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ult_estado_sirope);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fec_estado_ot);
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}



	function getReporteDetalleMatrizSeguimiento()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Excel de prueba')
				->setDescription('Excel generado como prueba')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de prueba');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('Formulario Reforzamiento Cto');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'N°');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'AÑO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NODO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'MODELO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CABLE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TIPO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'TROBA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'UIP HORIZONTAL DISEÑO');
			$col++;			 		
			$hoja->setCellValueByColumnAndRow($col, $row, '% PENETRACION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PROVINCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DEPARTAMENTO');
			$col++;			 		
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ADJUDICACION DISEÑO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA CIERRE DISEÑO EXPEDIENTE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA CIERRE OSP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA ENTREGA');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTATUS DISEÑO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PPTO APROBADO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PEP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'OC CONSTRUCCION HORIZONTAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'GENERACION DE VR');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS OC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CERTIFICACION');
			$col++;


			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE PRESENTACION DE LICENCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE INICIO DE LICENCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTATUS LICENCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ENTREGA DE MATERIALES');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD DE HILOS O PUERTOS OLT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA JUMPLEO CENTRAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTATUS PIN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE CENSADO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'UIP HORIZONTAL CENSO');
			$col++;

			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS CENSO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ODF/BANDEJA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA INSTALACION ODF');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA INICIO DE CONSTRUCCION');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE ENTREGA PROYECTADA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE ENTRAGA FINAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS DESPLIEGUE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DE PRUEBA HGU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, '% DE AVANCE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS HGU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS FINAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'STATUS GLOBAL');
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

            $hoja->getStyle('A1:W1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = [];
			$contador = 1;

			$listaReporte = $this->m_extractor->getReporteDetalleMatrizSeguimiento();

			foreach ($listaReporte as $fila) {

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $contador);
            	$col++;
            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->anio);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->divicau);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->plan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->centralDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColabDesc);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->modelo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cable);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->tipo);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->troba);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->cantfactorplanificado);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->comodinPenetracion);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->distrito);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->provincia);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->departamento);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaAdjudicaDiseno);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaCierreDisenoExpediente);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaCierreOSP);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaEntregaDiseno);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoDiseno);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->pptoAprobado);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->pep);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->ocConstruccionH);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->generacionVR);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoOC);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoCertificaOC);
            	$col++;
            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaPresentaLicencia);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaInicioLicencia);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoLicencia);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->entregaMateriales);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->numHilosPuertoOLT);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->FechaJumpeoCentral);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoPin);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaCensado);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->UIPHorizontalCenso);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoCenso);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->bandejaODF);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaInstalacionODF);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaInicioConstruccion);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaProyectadaEntrega);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaFinalEntregaDivicau);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoDespliegue);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaPruebaHGU);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->comodinAvanceHGU);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoHGU);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoFinal);
            	$col++;

            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estadoGlobal);
            	$col++;


				$row++;
				$contador++;
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}


	function getReporteDetalleMatrizJumpeo()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Excel de prueba')
				->setDescription('Excel generado como prueba')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de prueba');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('Formulario Reforzamiento Cto');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'N°');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO SOLICITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'GESTOR');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FASE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA SOLICITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ETAPA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PUERTO CTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ODF');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'BANDEJA');
			$col++;			 		
			$hoja->setCellValueByColumnAndRow($col, $row, 'MODULO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CABLE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'HILO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ARMARIO');
			$col++;			 		
			$hoja->setCellValueByColumnAndRow($col, $row, 'DIVICAU');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NODO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC PEXT');
			$col++;	
			$hoja->setCellValueByColumnAndRow($col, $row, 'CODIGO CMS');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NOMBRE OLT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SHELF');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SLOT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'PORT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EECC PINT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA DESPACHO EECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA TERMINO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ITEMPLAN PIN OT');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'WORK ORDER ISP');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EVIDENCIA');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'COMENTARIO OBSERVADO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SUB PROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'ZONAL');
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

            $hoja->getStyle('A1:AI1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = [];
			$contador = 1;

			$listaReporte = [];

			if ($iddEECC == 6)
			{
				$listaReporte = $this->m_extractor->getReporteDetalleMatrizJumpeo();
			}
			else
			{
				$iddEECC 			= $this->session->userdata('idEmpresaColabSesion');
				$listaReporte 		= $this->m_extractor->getReporteDetalleMatrizJumpeoByEECC($iddEECC);
			}



			if (count($listaReporte) > 0)
			{
				foreach ($listaReporte as $fila)
				{

					$col = 0;
					$hoja->setCellValueByColumnAndRow($col, $row, $contador);
	            	$col++;
	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigoSolicitud);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->gestor);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->fase);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaSolicitud);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->etapa);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->empresaColab);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->proyecto);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplan);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->puertoCto);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->odf);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->bandeja);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->modulo);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->cable);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->hilo);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->armario);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->divicau);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->distrito);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->nodo);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->contrataPext);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->codigoCMS);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->nombre_olt);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->shelf);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->slot);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->port);
	            	$col++;
					$hoja->setCellValueByColumnAndRow($col, $row, $fila->eeccpint);
	            	$col++;

	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->estado);
	            	$col++;
	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaDespachoEECC);
	            	$col++;

	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fechaTermino);
	            	$col++;

	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->itemplanPinOt);
	            	$col++;

	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->workOrderISP);
	            	$col++;

	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->evidencia);
	            	$col++;

	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->comentarioObservado);
	            	$col++;

	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->subProyectoDesc);
	            	$col++;

	            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->zonalDesc);
	            	$col++;


					$row++;
					$contador++;
				}
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}

	function getReporteDetalleMatrizPinPex()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Pangeaco')
				->setLastModifiedBy('Pangeaco')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Excel de prueba')
				->setDescription('Excel generado como prueba')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de prueba');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('Formulario Reforzamiento Cto');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'N°');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NODO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CABLE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'HILO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'FECHA JUMPEO');
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

            $hoja->getStyle('A1:AI1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
			$listaReporte = [];
			$contador = 1;

			$listaReporte = $this->m_extractor->getReporteDetalleMatrizPinPex();

			foreach ($listaReporte as $fila)
			{
				$cuenta_pares 	= explode(',', $fila->cuenta_pares);
				$cable 			= $cuenta_pares[0];
				$hilo 			= $cuenta_pares[1];

				$col = 0;
				$hoja->setCellValueByColumnAndRow($col, $row, $contador);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila->nodo);
				$col++;
            	$hoja->setCellValueByColumnAndRow($col, $row, $cable);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $hilo);            	
            	$col++;
            	$hoja->setCellValueByColumnAndRow($col, $row, $fila->fecha_cierre);
            	$col++;
				$row++;
				$contador++;
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
			
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}

		echo json_encode($data);

	}
}
