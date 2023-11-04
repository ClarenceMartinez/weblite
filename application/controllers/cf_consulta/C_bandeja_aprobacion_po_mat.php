<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_bandeja_aprobacion_po_mat extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_bandeja_aprobacion_po_mat');
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, 26, null, 22, null);
            $data['opciones'] = $result['html'];
            $data['header']     = $this->lib_utils->getHeader();
            //$data['tablaAprobacionPo'] = $this->getTablaConsulta(null, null);
            $data['json_bandeja'] = $this->getArrayPoMat($this->m_bandeja_aprobacion_po_mat->getBandejaAprobPoJson(null,null));
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_consulta/v_bandeja_aprobacion_po_mat',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    public function getArrayPoMat($listaPoMat){
        $listaFinal = array();       
        foreach($listaPoMat as $poMat){
            array_push($listaFinal, array('<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Descargar Lista Materiales" data-itemplan="'.$poMat['itemplan'].'" onclick="getReporteMateriales(this)" data-codigo_po="'.$poMat['codigo_po'].'" data-id_empresacolab="'.$poMat['idEmpresaColab'].'">
                                                <i class="fal fa-file-excel"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Aprobar PO" data-itemplan="'.$poMat['itemplan'].'" onclick="openModalAprobPO(this)" data-codigo_po="'.$poMat['codigo_po'].'" data-id_empresacolab="'.$poMat['idEmpresaColab'].'">
                                                <i class="fal fa-check"></i>
                                            </a>',
                                            $poMat['codigo_po'],$poMat['itemplan'],$poMat['estadoPlanDesc'],$poMat['subproyectoDesc'],$poMat['empresaColabDesc'], $poMat['zonalDesc'], $poMat['faseDesc'], $poMat['areaDesc'], $poMat['costo_total'], $poMat['desc_estado_po']));
        }
        return $listaFinal;
    }


    function getTablaConsulta($itemplan, $codigoPO) {
        if($itemplan == null && $codigoPO == null){
            $arrayData = array();
        }else{
            $arrayData = $this->m_bandeja_aprobacion_po_mat->getBandejaAprobPo($itemplan, $codigoPO);
        }
        
        $html = '<table id="tbBandejaAprob" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-500">
                        <tr>
                            <th>ACCIÓN</th>  
                            <th>CÓDIGO PO</th>
                            <th>ITEMPLAN</th>                            
                            <th>ESTADO PLAN</th>
                            <th>SUBPROYECTO</th>
                            <th>EECC</th>
                            <th>ZONAL</th>
                            <th>FASE</th>
                            <th>ÁREA</th>
                            <th>COSTO TOTAL</th>
                            <th>ESTADO PO</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($arrayData as $row) {

            $actions = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Descargar Lista Materiales" data-itemplan="'.$row['itemplan'].'"
                            onclick="getReporteMateriales(this)" data-codigo_po="'.$row['codigo_po'].'" data-id_empresacolab="'.$row['idEmpresaColab'].'">
                            <i class="fal fa-file-excel"></i>
                        </a>';
            if($row['estado_po'] == ID_ESTADO_PO_PRE_APROBADO){
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Aprobar PO" data-itemplan="'.$row['itemplan'].'"
                                onclick="openModalAprobPO(this)" data-codigo_po="'.$row['codigo_po'].'" data-id_empresacolab="'.$row['idEmpresaColab'].'">
                                <i class="fal fa-check"></i>
                            </a>';
            }
            $html .= ' <tr>
                            <td>
                                <div class="d-flex demo">
                                    '.$actions.'
                                </div>
                            </td>
                            <td>'.$row['codigo_po'].'</td>
                            <td>'.$row['itemplan'].'</td>
                            <td>'.$row['estadoPlanDesc'].'</td>
                            <td>'.$row['subproyectoDesc'].'</td>
                            <td>'.$row['empresaColabDesc'].'</td>  
                            <td>'.$row['zonalDesc'].'</td>
                            <td>'.$row['faseDesc'].'</td>
                            <td>'.$row['areaDesc'].'</td>
                            <td>'.number_format($row['costo_total'],2).'</td>
                            <td>'.$row['desc_estado_po'].'</td>       
                        </tr>';
        }
        $html .= '</tbody>
            </table>';
        return $html;
    }

	public function aprobarPOMat(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        
        try{
            $codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $valeReserva = $this->input->post('txtValeReserva') ? $this->input->post('txtValeReserva') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($codigoPO == null){
                throw new Exception('Hubo un error al recibir el código po.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan.');
            }
            if($valeReserva == null){
                throw new Exception('Hubo un error al recibir el vale de reserva.');
            }
            $fechaActual = $this->m_utils->fechaActual();

            $arrayUpdatePO = array(
                "vale_reserva" => $valeReserva,
                "estado_po" => ID_ESTADO_PO_APROBADO,
                "fecha_reg_vr"  => $fechaActual
            );
            $data = $this->m_utils->actualizarPoByCodigo($codigoPO, $arrayUpdatePO);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $arrayInsertLog = array(
                "codigo_po" => $codigoPO,
                "itemplan" => $itemplan,
                "idUsuario" => $idUsuario,
                "fecha_registro" => $fechaActual,
                "idPoestado" => ID_ESTADO_PO_APROBADO,
                "controlador" => 'bandeja aprobación po mat'
            );
            $data = $this->m_utils->registrarLogPO($arrayInsertLog);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem['idEstadoPlan'] == ID_ESTADO_PLAN_EN_APROBACION){
                $hasVR = $this->m_utils->countMatPoAprobConVR($itemplan);
                if($hasVR > 0){
                    $arrayUpdatePO = array(
                        'idEstadoPlan' => ID_ESTADO_PLAN_EN_OBRA, 
                        'idUsuarioLog' => $idUsuario, 
                        'fechaLog' => $fechaActual,
                        'descripcion' => 'GENERACIÓN DE VR'
                    );
                    $data = $this->m_utils->actualizarPlanObra($itemplan,$arrayUpdatePO);
                    if($data['error'] == EXIT_ERROR) {
                        throw new Exception($data['msj']);
                    }
                }
            }
            if($data['error'] == EXIT_SUCCESS){
                $this->db->trans_commit();
               //$data['tbBandejaAprob'] = $this->getTablaConsulta($itemplan,$codigoPO);
               $data['json_bandeja'] = $this->getArrayPoMat($this->m_bandeja_aprobacion_po_mat->getBandejaAprobPoJson(null,null));
            }         
        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }          
        echo json_encode($data);
    }
	
	public function filtrarTabla()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $codigoPO = $this->input->post('codigoPO') ? $this->input->post('codigoPO') : null;
			if($itemplan == null &&  $codigoPO == null){
				throw new Exception('Debe ingresar mínimo un filtro para buscar!!');
			}
            $data['tbBandejaAprob'] = $this->getTablaConsulta($itemplan,$codigoPO);
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
	
	function getReporteMateriales()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {
            $codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;

			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
			if($iddEECC == null || $iddEECC == ''){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($codigoPO == null){
                throw new Exception('Hubo un error al recibir el código po.');
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
			$hoja->setTitle('detalle_mat');

			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO INVERSIÓN');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO PO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO MATERIAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CANTIDAD MATERIAL');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO ALMACEN');
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
			$listaReporte = $this->m_bandeja_aprobacion_po_mat->getDetalleMateriales($codigoPO);

			foreach ($listaReporte as $fila) {

				$col = 0;
                $dataAlmacen = explode('|', $fila['dataJefaturaEmp']);
				$hoja->setCellValueByColumnAndRow($col, $row, $fila['codigoInversion']);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila['codigo_po_recortado']);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila['codigo_material']);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $fila['cantidadFinal']);
            	$col++;
				$hoja->setCellValueByColumnAndRow($col, $row, $dataAlmacen[0]);
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
}
