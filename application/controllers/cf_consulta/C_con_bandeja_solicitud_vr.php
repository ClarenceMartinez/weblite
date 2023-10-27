<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_con_bandeja_solicitud_vr extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_solicitud_Vr');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $data['cmbJefaturaSap'] = __cmbHTML2(__buildComboJefaturaSap(), 'selectJefaturaSap', null, 'select2 form-control w-100', 'Jefatura', null, null);
            $data['cmbEECC'] = __cmbHTML2(__buildComboEECC(), 'selectEECC', null, 'select2 form-control w-100', 'EECC', null, null);
            $data['cmbFase'] = __cmbHTML2(__buildComboFase(), 'selectFase', null, 'select2 form-control w-100', 'Fase', null, null);
            $data['cmbEstadoSolVr'] = __cmbHTML2(__buildComboEstadoSolVr(), 'selectEstadoSolVr', null, 'select2 form-control w-100', 'Estado', null, null);

            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_VR_PADRE, null, 51, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
			$data['idEmpresaColab']	=	$idEmpresaColab;
            $data['json_bandeja'] = $this->getArrayPoBaVR(null);//$this->m_solicitud_Vr->getBandejaVRLight(null,null,null, null,null)
        //  $data['tablaBandejaSolVr'] = $this->getTablaConsulta(null);
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_consulta/v_con_bandeja_solicitud_Vr',$data);        	  
    	 }else{
        	 redirect('login','refresh');
	    }     
    }

    public function getArrayPoBaVR($listaSolVr){
        $listaFinal = array();      
        if($listaSolVr!=null){
            foreach($listaSolVr as $poMat){ 
                $actions = '';    
                $actions .= '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$poMat['itemplan'].'"
                                onclick="viewDetalleConSolVr(this)" data-codigo="'.$poMat['codigoSolVr'].'" data-codigo_po="'.$poMat['codigo_po'].'">
                                <i class="fal fa-pencil"></i>
                            </a>';
                array_push($listaFinal, array($actions,
                    $poMat['codigoSolVr'],$poMat['proyectoDesc'], $poMat['subProyectoDesc'], $poMat['codigo_po'],$poMat['itemplan'],$poMat['vale_reserva'],$poMat['empresaColabDesc'], $poMat['faseDesc'], $poMat['estadoDesc'], $poMat['usu_registro'], $poMat['fecha_registro']
                ,$poMat['ult_usu_aten'], $poMat['ult_fecha_atencion']));
            }     
        }                                                            
        return $listaFinal;
    }

    function getTablaConsulta($itemplan, $idJefatura = null, $idEmpresaColab = null, $idFase = null, $tipoAtencion = null) {

        if($itemplan == null && $idJefatura == null && $idEmpresaColab == null && $idFase == null && $tipoAtencion == null){
            $arrayData = array();
        }else{
            $arrayData = $this->m_solicitud_Vr->getBandejaSolicitudVr($itemplan, $idJefatura, $idEmpresaColab, $idFase, $tipoAtencion);
        }
        
        
        $html = '<table id="tbBandejaSolVr" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>
                            <th>CÓDIGO SOL VR</th>
                            <th>PROYECTO</th>
                            <th>SUBPROYECTO</th>
                            <th>CÓDIGO PO</th>
                            <th>ITEMPLAN</th>
                            <th>VR</th>                          
                            <th>EECC</th>
                            <th>FASE</th>
                            <th>ESTADO</th>
                            <th>USUARIO REGISTRO</th>
                            <th>FECHA REGISTRO</th>
                            <th>USUARIO ATENCIÓN</th>                           
                            <th>FECHA ATENCIÓN</th>                          
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($arrayData as $row) {

            $actions = '';
            $style = 'style="background:"';
            if($row['estado'] == 0){//PENDIENTE
                $style = '';
            }else if($row['estado'] == 1){//PARCIAL
                $style = 'style="background:#FFE033"';
            }else if($row['estado'] == 2){//TOTAL
                $style = 'style="background:#8CE857"';
            }else if($row['estado'] == 3){//RECHAZADO
                $style = 'style="background:#F6A5A5"';
            }
            $actions .= '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                            onclick="viewDetalleConSolVr(this)" data-codigo="'.$row['codigoSolVr'].'" data-codigo_po="'.$row['codigo_po'].'">
                            <i class="fal fa-pencil"></i>
                        </a>';

            $html .= ' <tr '.$style.'>
                            <td>
                                <div class="d-flex demo">
                                    '.$actions.'
                                </div>
                            </td>
                            <td>'.$row['codigoSolVr'].'</td>
                            <td>'.$row['codigo_po'].'</td>
                            <td>'.$row['itemplan'].'</td>
                            <td>'.$row['vale_reserva'].'</td> 
                            <td>'.''.'</td>
                            <td>'.$row['jefaturaDesc'].'</td>
                            <td>'.$row['empresaColabDesc'].'</td>
                            <td>'.$row['faseDesc'].'</td>
                            <td>'.$row['estadoDesc'].'</td>
                            <td>'.''.'</td>
                            <td>'.$row['tiempoAtencionSVr'].'</td>
                            <td>'.$row['ult_usu_aten'].'</td>
                            <td>'.$row['fecha_registro'].'</td>
                            <td>'.$row['ult_fecha_atencion'].'</td>
                            <td>'.$row['proyectoDesc'].'</td>
                            <td>'.$row['subProyectoDesc'].'</td>
                        </tr>';
        }
        $html .= '</tbody>
            </table>';
        return $html;
    }

    public function getDetalleSolicitudVr()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $codigoPO = $this->input->post('codigoPO') ? $this->input->post('codigoPO') : null;
            $codigoSolVr = $this->input->post('codigoSolVr') ? $this->input->post('codigoSolVr') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
			if($itemplan == null){
				throw new Exception('Hubo un error al recibir el itemplan!!');
			}
            if($codigoPO == null){
				throw new Exception('Hubo un error al recibir el código de po!!');
			}
            if($codigoSolVr == null){
				throw new Exception('Hubo un error al recibir el código de solicitud!!');
			}

            list($html,$arrayDetalleSol) = $this->getTablaDetalleSolVr($itemplan, $codigoPO, $codigoSolVr);
            $data['tablaDetalleSolVr'] = $html;
            $data['arrayDetalleSol'] = json_encode($arrayDetalleSol);
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    
    function getTablaDetalleSolVr($itemplan,$codigoPO, $codigoSolVr){
        $html = '';
        $arrayDetalleSol = $this->m_solicitud_Vr->getDetalleSolVr($codigoSolVr, $itemplan, $codigoPO);
        $html .= '<table id="tbDetalleSolVr" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr> 
                          
                            <th>CÓDIGO PO</th>                                         
                            <th>CÓDIGO MATERIAL</th>
                            <th>MATERIAL</th>
                            <th>CANTIDAD</th>
                            <th>TIPO SOLICITUD</th>
                            <th>VALE DE RESERVA</th>
                            <th>ESTADO</th>
                            <th>COMENTARIO</th>
                            <!--
                            <th>ACCIÓN</th>
                            -->
                        </tr>
                    </thead>
                    <tbody id="BodyDetalle">';
        $montoTotal = 0;
        $count = 0;
        $arrayDataMat = array();
        foreach($arrayDetalleSol as $row){
            $arrayDataMat []= array(
                "idSolVrDet" => $row['idSolVrDet'],
                "codigoSolVr" => $row['codigoSolVr'],
                "itemplan" => $row['itemplan'],
                "codigo_po" => $row['codigo_po'],
                "codigo_material" => $row['codigo_material'],
                "comentario" => $row['comentario'],
                "flg_estado" => $row['flg_estado'],
                "cantidadFin" => $row['cantidadFin'],
                "flg_adicion" => $row['flg_adicion'],
                "costoMat" => $row['costoMat'],
                "flg_evaluar" => ($row['flg_estado'] == 0 ? 1 : 0)
            );
           
            $checked  = ($row['flg_estado'] == 1) ? 'checked' : null;
            $disabled = ($row['flg_estado'] == 1 || $row['flg_estado'] == 3) ? 'disabled' : null;

            $valSelect   = $row['flg_estado'];             
            $disabledSel = ($row['flg_estado'] == 1 || $row['flg_estado'] == 3) ? 'disabled' : null;


            $html .= '  <tr>                            
                            <td>'.$row['codigo_po'].'</td>                           
                            <td>'.$row['codigo_material'].'</td>
                            <td>'.$row['descrip_material'].'</td>
                            <td>'.$row['cantidad'].'</td>
                            <td>'.$row['nombreTipoSolicitud'].'</td>
                            <td>'.$row['vale_reserva'].'</td>
                            <td>'
                                    .'<select disabled id="selAteVr_'.$count.'" name="selAteVr_'.$count.'" data-pos="'.$count.'" class="select2 form-control w-100">
                                            <option value="">Seleccionar</option>
                                            <option '.(($valSelect  ==  1)  ?  'selected' :   '').' value="1">ATENDIDO</option>
                                            <option '.(($valSelect  ==  3)  ?  'selected' :   '').' value="3">RECHAZADO</option>
                                    </select>'.
                                '</td>
                            <td>
                                <textarea type="text" class="form-control" placeholder="" id="txtRechazo_'.$count.'" rows="2" disabled
                                data-codigo="'.$row['codigoSolVr'].'" data-codigo_material="'.$row['codigo_material'].'" data-pos="'.$count.'" >'.$row['comentario'].'</textarea>
                            </td>
                            <!--
                            <td>
                                <div class="demo">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="chckBx_'.$count.'" data-codigo="'.$row['codigoSolVr'].'" '.$checked.'  '.$disabled.'
                                        data-codigo_material="'.$row['codigo_material'].'" data-pos="'.$count.'">
                                        <label class="custom-control-label" for="chckBx_'.$count.'">Validar</label>
                                    </div>
                                </div>
                            </td>
                            -->
                        </tr>';
            $count++;
        }

        $html .= '  </tbody>
                    </table>
                    <!--<div class="form-group" align="center">
                        <button class="btn btn-primary" id="btnSave" onclick="atenderDetalleSolVr()">Guardar</button>
                    </div>-->';
        
        return array($html,$arrayDataMat);
    }

    function actualizarDetalleSolVr(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
			$codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
            $codigoSolVr = $this->input->post('codigoSolVr') ? $this->input->post('codigoSolVr') : null;
			$codigoMaterial = $this->input->post('codigo_material') ? $this->input->post('codigo_material') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $fechaActual = $this->m_utils->fechaActual();

            $this->db->trans_begin();

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesión ha expirado, cargar nuevamente la página.");
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan.');
            }
            if($codigoPO == null){
                throw new Exception('Hubo un error al recibir el código de po.');
            }
            if($codigoSolVr == null){
                throw new Exception('Hubo un error al recibir el código de solicitud vr.');
            }
            if($codigoMaterial == null){
                throw new Exception('Hubo un error al recibir el material.');
            }
            $arrayUpdate = array(
                'send_rpa' => 1,
                'usua_valida_rpa' => $idUsuario,
                'fec_valida_rpa' => $fechaActual
            );
            $data = $this->m_solicitud_Vr->updateDetalleSolVr($arrayUpdate,$codigoSolVr,$codigoMaterial);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }

            $this->db->trans_commit();
            list($html,$arrayDetalleSol) = $this->getTablaDetalleSolVr($itemplan, $codigoPO, $codigoSolVr);
            $data['tablaDetalleSolVr'] = $html;
            $data['arrayDetalleSol'] = json_encode($arrayDetalleSol);
        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    

    function atenderDetalleSolVr() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
			$codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
            $codigoSolVr = $this->input->post('codigoSolVr') ? $this->input->post('codigoSolVr') : null;

            $arrayDetalleSol = json_decode($this->input->post('arrayDetalleSol'), true);
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $fechaActual = $this->m_utils->fechaActual();

            $this->db->trans_begin();
            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesión ha expirado, cargar nuevamente la página.");
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan.');
            }
            if($codigoPO == null){
                throw new Exception('Hubo un error al recibir el código de po.');
            }
            if($codigoSolVr == null){
                throw new Exception('Hubo un error al recibir el código de solicitud vr.');
            }

			// _log(print_r($itemplan,true));
            // _log(print_r($codigoPO,true));
            // _log(print_r($codigoSolVr,true));
			// _log(print_r($arrayDetalleSol,true));

            $arrayLogSolVr = array();
            $arrayUpdateDetSolVr = array();
            $arrayInsertDetallePo = array();
            $arrayUpdateDetallePo = array();
            foreach($arrayDetalleSol as $row) {
                $arrayTempLog = array (
                    'idSolVrDet'        => $row['idSolVrDet'],
					'itemplan'          => $itemplan,
					'codigo_po'         => $codigoPO,
                    'codigo_material'   => $row['codigo_material'],
					'comentario'        => $row['comentario'],
					'flg_estado'        => $row['flg_estado'],
					'idUsuario'         => $idUsuario,
					'fecha_registro'    => $fechaActual
				);
                $arrayLogSolVr[] = $arrayTempLog;
                $arrayTemp = array (
                    'idSolVrDet'        => $row['idSolVrDet'],
					'comentario'        => $row['comentario'],
					'flg_estado'        => $row['flg_estado'],
					'usuario_atencion'  => $idUsuario,
					'fecha_atencion'    => $fechaActual
				);
                
                if($row['flg_adicion'] == 1) {
                    $arrayInsertDetallePo[] = array(
                        'codigo_po' => $codigoPO,
                        'codigo_material' => $row['codigo_material'],
                        'cantidadInicial' => $row['cantidadFin'],
                        'cantidadFinal' => $row['cantidadFin'],
                        'costoMat' => $row['costoMat'],
                        'montoFinal' => round($row['costoMat']*$row['cantidadFin'],2)
                    );
                    $arrayTemp['flg_adicion'] = 2;
                }else{
                    $contExiste = $this->m_solicitud_Vr->getCountExistMatInDetallePO($codigoPO,$row['codigo_material']);
                    if($contExiste > 0){
                        $arrayUpdateDetallePo[] = array(
                            'codigo_po' => $codigoPO,
                            'codigo_material' => $row['codigo_material'],
                            'cantidadFinal' => $row['cantidadFin'],
                            'costoMat' => $row['costoMat'],
                            'montoFinal' => round($row['costoMat']*$row['cantidadFin'],2)
                        );
                    }
                }

                $arrayUpdateDetSolVr[] = $arrayTemp;

            }

            
            if(count($arrayUpdateDetallePo) > 0){
                $data = $this->m_solicitud_Vr->updateDetallePoMat($arrayUpdateDetallePo);
                if($data['error'] == EXIT_ERROR){
                    throw new Exception($data['msj']);
                }
            }
            if(count($arrayInsertDetallePo) > 0){
                $data = $this->m_solicitud_Vr->insertBatchDetallePoMat($arrayInsertDetallePo);
                if($data['error'] == EXIT_ERROR){
                    throw new Exception($data['msj']);
                }
            }
            $data = $this->m_solicitud_Vr->updateBatchDetalleSolicitudVr($arrayUpdateDetSolVr,$arrayLogSolVr);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $data = $this->m_utils->updateCostoTotalPoMat($codigoPO);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $arrayUpdateSolVr = array(
                'ult_usuario_atencion' => $idUsuario,
                'ult_fecha_atencion' => $fechaActual,
            );
            $nuevoEstado = $this->m_utils->evaluarNewEstadoSolVr($codigoSolVr);
            if($nuevoEstado != null){
                $arrayUpdateSolVr['estado'] = $nuevoEstado;
            }
            $data = $this->m_solicitud_Vr->updateSolicitudVr($arrayUpdateSolVr,$codigoSolVr);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $data['tablaBandejaSolVr'] = $this->getTablaConsulta($itemplan);
            $this->db->trans_commit();

        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        echo json_encode($data);
    }

    public function filtrarTabla()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

            log_message('error', $this->input->post('estado'));
			$itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idJefaturaSap = $this->input->post('idJefaturaSap') ? $this->input->post('idJefaturaSap') : null;
            $idEECC = $this->input->post('idEECC') ? $this->input->post('idEECC') : null;
			$idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
			if($idEmpresaColab	!=	6){
				$idEECC	=	$idEmpresaColab;
			}
            $idFase = $this->input->post('idFase') ? $this->input->post('idFase') : null;
            $estado = (($this->input->post('estado')=='') ? null : $this->input->post('estado'));
            log_message('error', $estado);
			$idUsuario = $this->session->userdata('idPersonaSessionPan');
            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesión ha expirado, cargar nuevamente la página.");
            }
            $data['json_bandeja'] = $this->getArrayPoBaVR($this->m_solicitud_Vr->getBandejaVRLight($itemplan, $idJefaturaSap, $idEECC, $idFase, $estado));
            //$data['tablaBandejaSolVr'] = $this->getTablaConsulta($itemplan, $idJefaturaSap, $idEECC, $idFase, $estado);
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

}
