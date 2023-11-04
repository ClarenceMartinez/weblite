<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_carga_estados_ot_sirope extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_transferencias/m_carga_estados_ot_sirope'); 
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
            $data['tbObservacion'] = $this->getTablaSolicitudOc(null)['html'];

            $permisos = $this->session->userdata('permisosArbolPan');
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_ORDEN_COMPRA_PADRE, null, ID_ATEN_SOL_OC_CREA_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_transferencias/v_carga_estados_ot_sirope',$data);        	  
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
            $hoja->setCellValueByColumnAndRow($col, $row, 'SOLPED');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ORDEN COMPRA');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'COSTO SAP');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'POSICIÓN');
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
            $data['nombreArchivo'] = 'Formato_Atencion_Masiva_Sol_OC_Creacion' . date("YmdHis") . '.xls';
            
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

			$idUsuarioSession = $this->session->userdata('idPersonaSessionPan');

			if (!isset($idUsuarioSession)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }
			if(count($_FILES) == 0){
				throw new Exception('Debe seleccionar un archivo para procesar data!!');
			}        

            $uploaddir = 'uploads/sirope_estados/'; //ruta final del file
            $uploadfile = $uploaddir . basename($_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {			
                $fp = fopen($uploadfile, "r");
                $linea = fgets($fp);                   
                $comp = preg_split("/[,]/", $linea);            
                fclose($fp);
                if (count($comp) == 4) {//4 COLUMNAS
                 
                   
                    $data = $this->m_carga_estados_ot_sirope->uploadFileSiropeToTmpTable($uploadfile);
                    if($data['error']   == EXIT_ERROR){
                        throw new Exception('Error al procesar el archivo, refresque la pantalla y vuelva a intentarlo.');
                    }                 
                    $listToUpdate = $this->m_carga_estados_ot_sirope->getToUpdateSirope();
                    $infoUpdate  = $this->getTablaSolicitudOc($listToUpdate); ;
                    $data['tbObservacion'] = $infoUpdate['html'];
                    $data['titulo'] = 'Se muestran la cantidad registros a cargar ('.$infoUpdate['total'].') ';
                    $data['total_upd'] = $infoUpdate['total'];
                    //$data['error'] = EXIT_SUCCESS;

                }else{
                    throw new Exception('El numero de columnas no es valido, debe contener 4 columnas delimitadas por ";".');
                }
            }			
	
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getTablaSolicitudOc($listToUpdate) {
        $arrayExit = array();
        $contador = 0;
        $html = '<table id="tablaResultado" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ITEMPLAN</th>  
                            <th>ESTADO OT</th> 						
                        </tr>
                    </thead>
                    <tbody>';
                if($listToUpdate!=null){
                    foreach ($listToUpdate as $row) {
            
                        $html .= '<tr> 
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['estado_actual'].'</td>
                                    </tr>';
                        $contador++;
                    }
                }
                    $html .= '</tbody>
                        </table>';
        $arrayExit['html']  =    $html;
        $arrayExit['total'] =    $contador;
        return $arrayExit;
    }
 
    function actualizarEstadosSirope(){
		$data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
			
            $idUsuarioSession = $this->session->userdata('idPersonaSessionPan');

			if (!isset($idUsuarioSession)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }

            $data = $this->m_carga_estados_ot_sirope->updateSiropeEstadosTransferencia();
        } catch(Exception $e) {
			$this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
		echo json_encode($data);
	}
}
