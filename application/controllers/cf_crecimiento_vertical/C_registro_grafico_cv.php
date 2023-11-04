<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registro_grafico_cv extends CI_Controller {

    var $login;

    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_crecimiento_vertical/m_registro_grafico_cv');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){
            // $data['cmbBandaHoraria'] = __cmbHTML2(__buildComboBandaHorariaCV(), 'selectBandaHoraria', null, 'select2 form-control w-100', 'Banda Horaria', null, null);           
            // $data['cmbMotivoCierre'] = __cmbHTML2(__buildComboMotivoCVSC(), 'selectMotivoCierre', null, 'select2 form-control w-100', 'Motivo Cierre', null, null);
            $data['tablaData'] = $this->getHTMLTablaRegGrafico(null);

            $permisos = $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 7, null, 23, ID_MODULO_DESPLIEGUE_PLANTA);
            $data['opciones'] = $result['html'];
            $this->load->view('vf_crecimiento_vertical/v_registro_grafico_cv',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    function getHTMLTablaRegGrafico($listaRegGraf) {
        
        $html = '<table id="tb_bandeja_reg_graf" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>
							<th>ITEMPLAN</th>
                            <th>ESTADO PLAN</th>
                            <th>PROYECTO</th>
							<th>SUBPROYECTO</th>
                            <th>EECC</th>
                            <th>ESTACIÓN</th>  
                        </tr>
                    </thead>                    
                    <tbody>';
             if($listaRegGraf != null){                                                                        
                foreach($listaRegGraf as $row){
                    $actions = '';
                    if($row->ruta_archivo == null){
                        if($row->idEstacion == 5){
                            if($row->has_sirope_fo == 1){
                                $actions .= '<a onclick="openModalUpdate(this)" data-itemplan="' . $row->itemplan . '" data-siropecoax="' . $row->has_sirope_coax . '"
                                                data-estacion="' . $row->idEstacion . '" data-estadesc="' . $row->estacionDesc . '" data-siropefo="' . $row->has_sirope_fo . '"
                                                class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Subir Archivo">
                                                <i class="fal fa-file-upload"></i>
                                            </a>';
                            }else{
                                $actions .= '';
                            }
                        }else if($row->idEstacion == 2){// COAXIAL
                            if($row->has_sirope_coax == 1){
                                $actions .= '<a onclick="openModalUpdate(this)" data-itemplan="' . $row->itemplan . '" data-siropecoax="' . $row->has_sirope_coax . '"
                                                data-estacion="' . $row->idEstacion . '" data-estadesc="' . $row->estacionDesc . '" data-siropefo="' . $row->has_sirope_fo . '"
                                                class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Subir Archivo">
                                                <i class="fal fa-file-upload"></i>
                                            </a>';
                            }else{
                                $actions .= '';
                            }
                        }
                    }else{
                        $actions .= '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Descargar Evidencia"
                                        href="' . $row->ruta_archivo . '">
                                        <i class="fal fa-download"></i>
                                    </a>';
                    }

                    // if($row->estado ==  1) {
                    //     $actions .= '<a onclick="openModalAgenda(this)" data-id="'.$row->id.'" data-ip="'.$row->itemplan.'" data-eecc="'.$row->empresaColabDesc.'" data-subproy="'.$row->subProyectoDesc.'"
                    //                     class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Agendar">
                    //                     <i class="fal fa-calendar-week"></i>
                    //                 </a>
                    //                 <a onclick="openModalCerrarObra(this)" data-id="'.$row->id.'" data-ip="'.$row->itemplan.'" class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Cerrar Obra">
                    //                     <i class="fal fa-times-circle"></i>
                    //                 </a>
                    //                 ';
                    // }
                    
                    $html .=' <tr>         
                                    <td>
                                        <div class="d-flex demo">
										    '.$actions.'
                                        </div>                                       
                                    </td>                  
        							<td>'.$row->itemplan.'</td>			
    							    <td>'.$row->estadoPlanDesc.'</td>
                                    <td>'.$row->proyectoDesc.'</td>				
        							<td>'.$row->subProyectoDesc.'</td>
								    <td>'.$row->empresaColabDesc.'</td>
								    <td>'.$row->estacionDesc.'</td>
                            </tr>';
                }
             }
            $html .='</tbody>
                </table>';
                    
            return $html;
    }


    public function filtrarTabla()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;

			if($itemplan == null){
				throw new Exception('Hubo un error al recibir el itemplan!!');
			}

			$hasTerminadoConPotencia = $this->m_registro_grafico_cv->getCountTerPotenciaByIP($itemplan);
			if($hasTerminadoConPotencia > 0){
				$listaReporte = $this->m_registro_grafico_cv->getBandejaRegGraficoCV($itemplan);
			}else{
				$listaReporte = null;
			}

            $data['tbReporte'] = $this->getHTMLTablaRegGrafico($listaReporte);
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function insertIPxRegGraficoCV()
    {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;

        try {

            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
			$idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
			$has_sirope_fo = $this->input->post('has_sirope_fo') ? $this->input->post('has_sirope_fo') : null;
			$has_sirope_coax = $this->input->post('has_sirope_coax') ? $this->input->post('has_sirope_coax') : null;
			$descEstacion = $this->input->post('descEstacion') ? $this->input->post('descEstacion') : null;

			$idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

			if (!isset($idUsuario)) {
                throw new Exception('Su sesión ha expirado, ingrese nuevamente!!');
            }

			if ($itemplan == null) {
				throw new Exception('Hubo un error al recibir el itemplan!!');
			}

			if ($idEstacion == null || $idEstacion == '' || $descEstacion == null || $descEstacion == '') {
				throw new Exception('Hubo un error al recibir la estación!!');
			}

			if($idEstacion == 5){//FO fibra optica
				if($has_sirope_fo != 1){
					throw new Exception('No puede registrar el archivo porque no tiene el estado de sirope!!');
				}
			}else if($idEstacion == 2){// COAXIAL
				if($has_sirope_coax != 1){
					throw new Exception('No puede registrar el archivo porque no tiene el estado de sirope!!');
				}
			}

			if(count($_FILES) == 0){
				throw new Exception('Debe seleccionar un archivo para guardar!!');
			}

			
			if (!file_exists("./uploads/evidencia_registro_grafico_cv")) {
				if (!mkdir("./uploads/evidencia_registro_grafico_cv")) {
					throw new Exception('Hubo un error al crear la carpeta evidencia_registro_grafico_cv!!');
				}
			}

			if (!file_exists("./uploads/evidencia_registro_grafico_cv/".$itemplan)) {
				if (!mkdir("./uploads/evidencia_registro_grafico_cv/".$itemplan)) {
					throw new Exception('Hubo un error al crear la carpeta '.$itemplan.'!!');
				}
			}

			if (!file_exists("./uploads/evidencia_registro_grafico_cv/".$itemplan.'/'.$descEstacion)) {
				if (!mkdir("./uploads/evidencia_registro_grafico_cv/".$itemplan.'/'.$descEstacion)) {
					throw new Exception('Hubo un error al crear la carpeta '.$descEstacion.'!!');
				}
			}
			

			$nombreArchivo = $_FILES['file']['name'];
			$tipoArchivo = $_FILES['file']['type'];
			$nombreFicheroTemp = $_FILES['file']['tmp_name'];
			$tamano_archivo = $_FILES['file']['size'];
			$arryNombreArchivo = explode(".", $nombreArchivo);

			$rutaDestinoArchivo = 'uploads/evidencia_registro_grafico_cv/'.$itemplan.'/'.$descEstacion .'/'. $nombreArchivo;
			$files = glob("uploads/evidencia_registro_grafico_cv/".$itemplan.'/'.$descEstacion.'/*'); //obtenemos todos los nombres de los ficheros
			foreach($files as $file){
				if(is_file($file)){
					unlink($file); //elimino el fichero
				}
			}
			if (!move_uploaded_file($nombreFicheroTemp, $rutaDestinoArchivo)) {
				throw new Exception('No se pudo subir el archivo: ' . $nombreArchivo . '!!');
			}
				
			$arrayInsert = array(
				"itemplan" => $itemplan,
				"idEstacion" => $idEstacion,
				"id_usuario" => $idUsuario,
				"fecha_registro" => date("Y-m-d H:i:s"),
				"ruta_archivo" => $rutaDestinoArchivo 
			);

			$responseInsert = $this->m_registro_grafico_cv->insertItemplanxRegistroGraficoCV($arrayInsert);
			if ($responseInsert['error'] == EXIT_ERROR) {
				throw new Exception($responseInsert['msj']);
			}

			$data['msj'] = $responseInsert['msj'];
			$data['error'] = $responseInsert['error'];


            if ($data['error'] == EXIT_SUCCESS) {
                $this->db->trans_commit();
                $data['tbReporte'] =  $this->getHTMLTablaRegGrafico($this->m_registro_grafico_cv->getBandejaRegGraficoCV($itemplan));
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
}
