<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_solicitud_Vr extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_solicitud_Vr');
        $this->load->model('mf_crecimiento_vertical/m_registro_manual_po_mat');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_VR_PADRE, null, 27, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['tablaValidacionObra'] = $this->getTablaConsulta(null, $idUsuario, null);
			//$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_consulta/v_solicitud_Vr',$data);        	  
    	 }else{
        	 redirect('login','refresh');
	    }     
    }

    function getTablaConsulta($itemplan, $idUsuario, $idEmpresaColab, $flgFiltro = 1) {

        $arrayData = array();
        // $this->m_bandeja_valida_obra->getBandejaValidaObra($itemplan, $idUsuario, $idEmpresaColab, $flgFiltro);
        
        $html = '<table id="tbBandejaValObra" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>  
                            <th>FILES</th>
                            <th>ITEMPLAN</th>                            
                            <th>PROYECTO</th>
                            <th>SUBPROYECTO</th>
                            <th>INDICADOR</th>
                            <th>EECC</th>
                            <th>JEFATURA</th>
                            <th>UIP</th>
                            <th>COSTO TOTAL</th>
                            <th>FECHA REGISTRO</th>
                            <th>USUARIO REGISTRO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($arrayData as $row) {

            $actions = '';
            if($row['estado'] == 0){
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                <i class="fal fa-eye"></i>
                            </a>';
            }else if($row['estado'] == 1){
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                <i class="fal fa-eye"></i>
                            </a>';
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Editar" data-itemplan="'.$row['itemplan'].'"
                            onclick="getPosByItemplan(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                            <i class="fal fa-clipboard"></i>
                        </a>';
            }else {
                $actions .= '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
                                onclick="viewDetallePartidas(this)" data-idSol="'.$row['id_solicitud'].'" data-esta="'.$row['idEstacion'].'">
                                <i class="fal fa-eye"></i>
                            </a>';
            }

            $html .= ' <tr>
                            <td>
                                <div class="d-flex demo">
                                    '.$actions.'
                                </div>
                            </td>
                            <td>
                                <div class="d-flex demo">
                                <a class="" aria-expanded="true" title="Aprobar PO" data-itemplan="'.$row['itemplan'].'"
                                    onclick="descargarExpediseno(this)">
                                    Expe_diseno
                                </a>
                                </div>
                            </td>
                            <td>'.$row['itemplan'].'</td>
                            <td>'.$row['proyectoDesc'].'</td>
                            <td>'.$row['subProyectoDesc'].'</td>
                            <td>'.$row['indicador'].'</td> 
                            <td>'.$row['empresaColabDesc'].'</td> 
                            <td>'.$row['jefaturaDesc'].'</td>
                            <td>'.$row['cantFactorPlanificado'].'</td>
                            <td>'.$row['total_mo_validado'].'</td>
                            <td>'.$row['fec_registro'].'</td>
                            <td>'.$row['nombre_completo'].'</td>
                            <td>'.$row['situacion'].'</td> 
                        </tr>';
        }
        $html .= '</tbody>
            </table>';
        return $html;
    }

    public function getComboPO()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
			if($itemplan == null){
				throw new Exception('Debe ingresar mínimo un filtro para buscar!!');
			}
            $cmbPO = null;
            $arrayDataPO = $this->m_solicitud_Vr->getPosMatByItemplan($itemplan,$idEmpresaColab);
            $cmbPO .= '<option value="">Seleccionar PO</option>';
            foreach($arrayDataPO as $row) {
                if($row['codigo_po'] != null && $row['po_estacion'] != null) {
                    $data['empresacolab'] = $row['empresaColabDesc'];
                    $data['jefatura']     = $row['jefaturaDesc'];
					log_message('error', print_r($row['dataJefaturaEmp'], true));
                    $dataAlmCen = explode('|', $row['dataJefaturaEmp']);
					log_message('error', print_r($dataAlmCen, true));
                    $data['codAlmacen']   = $dataAlmCen[0];
                    $data['codCentro']    = $dataAlmCen[1];
                    $data['idEmpresaColab'] = $dataAlmCen[3];
                    $data['idJefatura']     = $dataAlmCen[2];
                    $data['vr'] = $row['vale_reserva'];
                    $cmbPO.= '<option data-id_estacion="'.$row['idEstacion'].'" data-id_subproyecto="'.$row['idSubProyecto'].'" data-vale_reserva="'.$row['vale_reserva'].'" value="'.$row['codigo_po'].'">'.$row['po_estacion'].'</option>';
                }
            }

            $data['cmbPO'] = $cmbPO;
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getDetallePoMatForSolicitudVR() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
                $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
                $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
                $idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;
				$codigoPO = $this->input->post('codigoPO') ? $this->input->post('codigoPO') : null;
                if($idUsuario == null){
                    throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
                }
                if($itemplan == null){
                    throw new Exception('Hubo un error al recibir el itemplan.');
                }
                if($idEstacion == null){
                    throw new Exception('Hubo un error al recibir la estación.');
                }
                if($codigoPO == null){
                    throw new Exception('Hubo un error al recibir el código PO.');
                }
				
				$idEstadoPlan = $this->m_utils->getEstadoPlanByItemplan($itemplan);//nuevo czavalacas
                $only_devolucion = false;//nuevo czavalacas
                $perEsta = $this->m_solicitud_Vr->getPorcentajeByEstacionObra($itemplan, $idEstacion);
                if($perEsta !=  null){
                     if($perEsta['porcentaje']  ==  '100'){
                        if($idEstadoPlan == 9){//pre liquidado
							if(in_array($itemplan, array('P-23-5495364886'))){
								$only_devolucion    =   false;
							}else{
								$only_devolucion    =   true;
							}
                            
                        }else if($idEstadoPlan == 4){//terminado
                            $has_expe = $this->m_solicitud_Vr->hasExpedienteFinalizacion($itemplan);
                            if($has_expe == 0){
								if(in_array($itemplan, array('P-23-2140012617','P-23-2169048847'))){
									$only_devolucion    =   false;
								}else{
									$only_devolucion    =   true;
								}
                            }else{
                                throw new Exception('No puede generar una Gestion de VR, El itemplan cuenta con EXPEDIENTE.');
                            }                            
                        }else if($idEstadoPlan == 22    || $idEstadoPlan == 23){
                            if(in_array($itemplan, array(''))){
                                                            $only_devolucion    =   true;
                                                        }
                        }else{
                            throw new Exception('No puede generar una Gestion de VR, La estacion se encuentra Liquidada.');
                        }    
                     }
                }

                $hasSolActiva = $this->m_solicitud_Vr->hasSolVRPDTAtencionByItemCodPo($itemplan, $codigoPO);
                if($hasSolActiva > 0){
                    throw new Exception('El VR Ya cuenta con una solicitud Pendiente de Atencion.');
                }
                
                $dataMaterial = $this->getContenidoTbMat($itemplan, $idEstacion, $codigoPO, $only_devolucion);
                $data['tablaMat'] = $dataMaterial['html'];
                $data['costoTotal'] = $dataMaterial['costoTotal'];
                $data['arrayDataMat'] = json_encode($dataMaterial['arrayDataMat']);
                
                $data['error'] = EXIT_SUCCESS;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
    
    function getContenidoTbMat($itemplan,$idEstacion, $codigoPO, $only_devolucion){
        $data['error'] = EXIT_ERROR;
        $data['html'] = '';
        try{
            $html = ' <div class="row mb-3">
                        <div class="col-sm-3 col-md-3">
                            <button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" data-idestacion="'.$idEstacion.'"
                            data-itemplan="'.$itemplan.'" onclick="getKitMaterial(this)" id="btnKit">AÑADIR MATERIAL</button>
                        </div>
                      </div>
                      <div class="table-responsive">';
            $arrayMateriales = $this->m_utils->getDataPoDetalleMat($itemplan, $codigoPO, array(0));
            $html .= '<table id="tbMateriales" class="table table-bordered table-hover table-striped w-100">
                        <thead class="bg-primary-600">
                            <tr> 
                                <th>NRO</th>
                                <th>CÓDIGO</th>                            
                                <th>MATERIAL</th>
                                <th>COSTO</th>
                                <th>CANTIDAD PARA OBRA</th>
                                <th>CANTIDAD SOBRANTE</th>
                                <th>TIPO SOLICITUD</th>
                                <th>CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody id="BodyDetalle">';
            $montoTotal = 0;
            $count = 1;
            $arrayDataMat = array ();
            foreach($arrayMateriales as $row){
                $arrayDataMat []= array(
                    "codigoMaterial" => $row['codigo_material'],
                    "idTipoSolicitudVr" => null,
                    "cantidadInicio" => null,
                    "cantidadFin" => null,
                    "costoMaterial" => $row['costoMat'],
                    "send_rpa" => 0
                );
                $html .= '  <tr>
                                <td>'.$count.'</td>
                                <td>'.$row['codigo_material'].'</td>
                                <td>'.$row['descrip_material'].'</td>
                                <td>'.$row['costoMat'].'</td>
                                <td>'
                                    .'<input id="cantidadObra_'.$row['codigo_material'].'" data-cantidad="'.round($row['cantidadFinal']).'" class="form-control soloEntero" type="text" value="'.round($row['cantidadFinal']).'" readonly />'.
                                '</td>
                                <td>'
                                    .'<input id="cantidadIngreso_'.$row['codigo_material'].'" class="form-control soloDecimal" type="text" value="" readonly />'.
                                '</td>  
                                <td>'
                                    .'<select id="selectTipoSolVr_'.$row['codigo_material'].'" name="selectTipoSolVr_'.$row['codigo_material'].'" class="select2 form-control w-100"  
                                        data-itemplan="'.$itemplan.'" data-codigo_material="'.$row['codigo_material'].'"  onchange="getTipoSolVr(this);">
                                            <option value="">Seleccionar</option>
                                            '.__buildCmbTipoSolVr($only_devolucion).'
                                    </select>'.
                                '</td>
                                <td>'
                                    .'<input id="inputCantidad_'.$row['codigo_material'].'" data-codigo_material="'.$row['codigo_material'].'" data-costo_material="'.$row['costoMat'].'"
                                        class="form-control soloEntero" type="text" value="" placeholder="Ingresar cantidad" onkeyup="getDataDetSol(this)"/>'.
                                '</td>
                            </tr>';
                $montoTotal = $row['montoFinal'] + $montoTotal;
                $count++;
            }

            $html .= '  </tbody>
                        </table>
                        <div class="row" id="ctnDetalleCosto">
                            <div class="col-sm-4 ml-sm-auto">
                                <table class="table table-clean">
                                    <tbody>
                                        <tr class="table-scale-border-top border-left-0 border-right-0 border-bottom-0">
                                            <td class="text-left keep-print-font">
                                                <h4 class="m-0 fw-700 h2 keep-print-font color-primary-700">TOTAL</h4>
                                            </td>
                                            <td class="text-right keep-print-font">
                                                <h4 class="m-0 fw-700 h2 keep-print-font" id="costoTolalPo">S/.'.number_format($montoTotal,2).'</h4>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group" align="center">
                            <button class="btn btn-primary" onclick="guardarSolicitudVR()">Guardar</button>
                        </div>
                        </div>';
            
            $data['html'] = $html;
            $data['costoTotal'] = round($montoTotal,2);
            $data['arrayDataMat'] = $arrayDataMat;
        
        }catch(Exception $e){
            $data['html'] = $e->getMessage();
        }

        return $data;
    }

    function getKitMaterialForSolVr() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
			$idEstacion = $this->input->post('idEstacion') ? $this->input->post('idEstacion') : null;

            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan.');
            }
            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información del itemplan!!');
            }

            $data['error'] = EXIT_SUCCESS;
            list($tablaKitMat,$arrayKitMat) = $this->getTablaKitMaterial($infoItem['idSubProyecto'],$idEstacion);
            $data['tablaKitMat'] = $tablaKitMat;
            $data['arrayKitMat']  = json_encode($arrayKitMat);
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getTablaKitMaterial($idSubProyecto, $idEstacion) {

        $arrayKit = json_decode(json_encode($this->m_registro_manual_po_mat->getMaterialesBySubProyectoEstacion($idSubProyecto, $idEstacion)),true);

        $html = '<table class="table table-bordered" id="tbKitMat">
                    <thead class="bg-primary-600">
                        <th>NRO</th>
                        <th>CÓDIGO</th>
                        <th>MATERIAL</th>
                        <th>COSTO</th>
                        <th>ACCIÓN</th>
                    </thead>
                <tbody id="tBodyKitMat">';
        $count = 1;
        $arrayDetalleKit = array();
        foreach ($arrayKit as $row) {

            $objDetallePo['codigo_material']   = $row['codigo_material'];
            $objDetallePo['descrip_material']  = $row['descrip_material'];
            $objDetallePo['costo_material']    = $row['costo_material'];
            $objDetallePo['cantidad']          = 0;

            $arrayDetalleKit[]= $objDetallePo;

            $idTr = 'id="tr_'.$row['codigo_material'].'"';
            $html .= '  <tr '.$idTr.'>
                            <td>'.$count.'</td>
                            <td>'.$row['codigo_material'].'</td>
                            <td>'.$row['descrip_material'].'</td>
                            <td>'.$row['costo_material'].'</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary btn-icon" aria-expanded="true" data-codigo_material="'.$row['codigo_material'].'"
                                    data-descrip_material="'.$row['descrip_material'].'" data-costo_material="'.$row['costo_material'].'"
                                    title="Agregar" onclick="agregarKitMat(this);"><i class="fal fa-plus"></i>
                                </a>
                            </td>
                        </tr>';
            $count++;
        }
        $html .= '</tbody>
            </table>';
        return array($html, $arrayDetalleKit);
    }

    function registrarSolicitudVr() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {

            $this->db->trans_begin();

            $arrayDetalleSol = json_decode($this->input->post('arrayDetalleSol'), true);
            $objSolicitud = json_decode($this->input->post('objSolicitud'), true);
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $fechaActual = $this->m_utils->fechaActual();
            
            if($objSolicitud['codigo_po'] == null || $objSolicitud['codigo_po'] == '') {
                throw new Excepion("No se encontro el codigo de po.");
            }

            if($objSolicitud['itemplan'] == null || $objSolicitud['itemplan'] == '') {
                throw new Exception("No se encontro el itemplan, verificar.");
            }

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesión ha expirado, cargar nuevamente la página.");
            }

            $codigoSol = $this->m_solicitud_Vr->getCodigoSolicitudVr();
            if($codigoSol == null || $codigoSol == '') {
                throw new Exception('Error codigo de solicitud de vr.');
            }
            $objSolicitud['codigoSolVr']    = $codigoSol;
            $objSolicitud['estado']         = 0;//atencion pdt
            $objSolicitud['idUsuario']      = $idUsuario;
            $objSolicitud['fecha_registro'] = $fechaActual;

			// _log(print_r($objSolicitud,true));
			 //_log(print_r($arrayDetalleSol,true));
             //_log(print_r($objSolicitud,true));
			// _log(print_r($codigoSol,true));

            // throw new Exception("GAAAAAAAAAAAAAAAAAAAAA");

            $arrayDetalleInsert = array();
            foreach($arrayDetalleSol as $row) {
                $arrayTemp = array (
					'codigoSolVr'       => $codigoSol,
					'codigo_material'   => $row['codigoMaterial'],
					'cantidadInicio'    => $row['cantidadInicio'],
					'cantidadFin'       => $row['cantidadFin'],
					'flg_estado'        => 0,//pdt
					'idTipoSolicitudVr' => $row['idTipoSolicitudVr'],
					'flg_adicion'       => isset($row['flg_adicion']) ? $row['flg_adicion'] : null,
					'send_rpa'          => isset($row['send_rpa']) ? $row['send_rpa'] : null,
                    'orden_prioridad'   => 1,
                    'costoMat'          => $row['costoMaterial']
				);
                $arrayDetalleInsert[] = $arrayTemp;
            }
            if(count($arrayDetalleInsert) == 0){
                throw new Exception('No hay un detalle de solicitud de vr para registrar!!');
            }
            $data = $this->m_solicitud_Vr->registrarSolVr($objSolicitud,$arrayDetalleInsert);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();

        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        echo json_encode($data);
    }

}
