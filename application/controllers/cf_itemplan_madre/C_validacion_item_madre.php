<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_validacion_item_madre extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
		$this->load->model('mf_crecimiento_vertical/m_agenda_cv');
		$this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
		$this->load->model('mf_itemplan_madre/M_consulta_item_madre');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    function index() {
        $idUsuario      = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
			$data['cmbProyecto'] = __buildProyectoAll(NULL, NULL);
            $data['cmbEstadoPlan'] = __buildComboEstadoPlan();
			$data['cmbMotivoCancelaIP'] = __cmbHTML2(__buildComboMotivoCancelaIP(), 'selectMotivoCance', null, 'select2 form-control w-100', 'Motivo Cancelación', null, null);
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 33, null, 37, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoConIpMadre($this->m_utils->getItemplanMadreList()/*$this->m_utils->getItemMadreByCodigo(NULL, NULL, array(0), $idEmpresaColab)*/);
            //$data['tablaAprobacionPin'] = $this->getTablaConsulta(NULL, NULL, array(0), $idEmpresaColab);
			//$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_itemplan_madre/v_validacion_item_madre',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2, 'refresh');
	    }     
    }

    public function getArrayPoConIpMadre($listaSolVr){
        $listaFinal = array();      
        if($listaSolVr!=null){
            foreach($listaSolVr as $row){ 
                $actions = '<a onclick="modalItemplanHijos(' . "'" . $row['itemplan_m'] . "'" . ')" class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1"><i title="Itemplan Hijos" class="fal fa-eye"></i></a>';    
                
                array_push($listaFinal, array($actions,
                    $row['itemplan_m'],$row['num_hijos'], $row['orden_compra'], $row['nombrePlan'],$row['proyectoDesc'], $row['subProyectoDesc'], $row['cantidad_uip'], number_format($row['costo_mo_final'],2),  $row['empresaColabDesc'], $row['fechaRegistro'], $row['codigoInversion'] , $row['estadoPlanDesc']));
            }      
        }                                                            
        return $listaFinal;
    }

    function getTablaConsulta($itemplan_m, $idSubProyecto, $arrayEstadoPlan, $idEmpresaColab) {
        $arrayData = $this->m_utils->getItemMadreByCodigo($itemplan_m, $idSubProyecto, $arrayEstadoPlan, $idEmpresaColab);
        $html = '<table id="tbPlanObra" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th style="width:50px">ACCIÓN</th>
                            <th>ITEMPLAN MADRE</th>
                            <th>ITEMPLAN HIJOS</th>
                            <th>ORDEN COMPRA</th>
                            <th>ESTADO OC</th>
                            <th>NOMBRE</th>
                            <th>PROYECTO</th>
                            <th>SUB PROYECTO</th>
                            <th>MONTO</th>
                            <th>SOLICITUD</th>
                            <th>EECC</th>
                            <th>ENCARGADO</th>
                            <th>FECHA DE RECEPCION</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>';
					$cont = 0;
                    foreach ($arrayData as $row) {
                        $cont++;
                        $prioridad = '';
                        $descarga = '';
                        $orden_compra = '';
                        $gestion = '';
                        $btnCancelar = '';
                        
                        $btnLogOc = '<a data-itemplan_m="' . $row['itemplan_m'] . '" onclick="openModalLogOcObraPub($(this))"><i title="ver Log OC" style="color:#A4A4A4" class="zmdi zmdi-hc-2x zmdi-search-in-file"></i></a>';
                
                        $btnLog = '<a data-itemplan_m="' . $row['itemplan_m'] . '" onclick="openModalLogOP($(this))"><i title="ver Log" style="color:#A4A4A4" class="zmdi zmdi-hc-2x zmdi-search"></i></a>';
            
                        if ($row['idEstadoPlan'] == 7 || $row['idEstadoPlan'] == 22 || $row['idEstadoPlan'] == 23) {
                            $gestion = '<a href="gestionObraPublica?item=' . $row['itemplan_m'] . '" target="_blank"><i title="Gestion" style="color:#A4A4A4" class="zmdi zmdi-hc-2x zmdi-city-alt"></i></a>';
                        } else {
                            $gestion = '';
                        }
            
                        if ($row['idEstadoPlan'] == 2 || $row['idEstadoPlan'] == 8 || $row['idEstadoPlan'] == 12) {
                            $btnCancelar = '<a data-itemplan_m="' . $row['itemplan_m'] . '" onclick="openModalCancelarIPM(this)"><i title="Cancelar ItemPlan Madre" style="color:red" class="zmdi zmdi-hc-2x zmdi-close-circle"></i></a>';
                        } else {
                            $btnCancelar = '';
                        }                       
            
                        $html .= '<tr>
                                        <td width="auto">
                                            <div><a onclick="modalItemplanHijos(' . "'" . $row['itemplan_m'] . "'" . ')"><i title="VER ITEMPLAN HIJOS" style="color:#A4A4A4" class="zmdi zmdi-hc-2x zmdi-eye"></i></a></div>'.$btnLogOc.' '.$btnLog.'
                                        </td>
                                        <td>' . $row['itemplan_m'] . '</td>  
                                        <td>' . $this->m_utils->cantidadItemplanHijos($row['itemplan_m']) . '</td>  
                                        <td style="color:red">' . $row['orden_compra'] . '</td>
                                        <td>' . $row['estado_oc'].'</td>
                                        <td>' . ($row['nombrePlan']) . '</td>
                                        <td>' . (mb_strtoupper($row['proyectoDesc'])) . '</td>
                                        <td>' . $row['subDesc'] . '</td>
                                        <td>' . $row['costoEstimado'] . '</td>
                                        <td>' . $row['codigo_solicitud'] . '</td>
                                        <td>' . $row['empresaColabDesc'] . '</td>
                                        <td>' . $row['cliente'] . '</td>
                                        <td>' . $row['fecha_registro'] . '</td>
                                        <td>' . $row['estadoPlanDesc'] . '</td>
                                </tr>';
                    }
                    $html .= '</tbody></table>';
                    return ($html);
    }

    public function getLogPlanobra(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }

            $data['error'] = EXIT_SUCCESS;
            $data['tbLog'] = $this->getHTMLTablaLog($itemplan);

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getDataHijosItemValida() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $id = $this->input->post('itemplanMadre');
            $data['tablaItemHijos'] = $this->hijosItemMadreDetalle($id);
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
    
    public function hijosItemMadreDetalle($id) {
        $dataConsulta = $this->M_consulta_item_madre->getHijosValidar($id);
        $html = '';
        $totalMA = 0;
        $totalMoDiseno = 0;
        $totalMoOpera = 0;
        if (count($dataConsulta) == 0) {
                        $html .= '<table id="data-table2" class="table table-bordered" >
                                                            <thead class="bg-primary-600">
                                                                <tr>
                                                                    <th>DISEÑO</th>
                                                                    <th>ITEMPLAN</th>
                                                                    <th>PROYECTO</th>
                                                                    <th>SUBPROYECTO</th>
                                                                    <th>EECC</th>
                                                                    <th>ESTADO</th>
                                                                    <th>CANT. UIP</th>
                                                                    <th>COSTO MANO DE OBRA</th>
                                                                </tr>
                                                            </thead>                    
                                <tbody id="tb_body"></tbody></table>';
            } else {
                $html .= '<table id="data-table2" class="table table-bordered">
                                                    <thead class="bg-primary-600">
                                                        <tr>
                                                            <th>DISEÑO</th>
                                                            <th>ITEMPLAN</th>
                                                            <th>PROYECTO</th>
                                                            <th>SUBPROYECTO</th>
                                                            <th>EECC</th>
                                                            <th>ESTADO</th>
                                                            <th>CANT. UIP</th>
                                                            <th>COSTO DISEÑO</th>
                                                            <th>COSTO OPERACIONES</th>
                                                        </tr>
                                                    </thead>                    
                                                    <tbody id="tb_body">';

                foreach ($dataConsulta as $row) {
                    $totalMoDiseno = $totalMoDiseno + $row->total_diseno;
                    $totalMoOpera   = $totalMoOpera + $row->total_operaciones;
                    $ope = NULL;
                    if ($row->flg_valida_diseno == 1) {
                        $dise = '<div title="Aprobado" disabled class="text-center" ><i class="fal fa-lg fa-check" style="color:green"></i></div>';
                        // $ope = '<div style="cursor: pointer;"  title="Validar" class="text-center" onclick="modalValiOpe(' . "'" . $row->itemplan . "'" . ',2)"><i class="zmdi zmdi-hc-2x zmdi-edit"></i></div>';
                    } else if ($row->flg_valida_diseno == 2) {
                        $dise = '<div title="Rechazado:'.$row->comentario_valida_diseno.'" disabled class="text-center" ><i class="fal fa-lg fa-ban" style="color:red"></i></div>';
                    } else {
                        if($row->idEstadoPlan   ==  7){//SOLO SI EL HIJO ESTA EN DISENO EJECUTADO PUEDE VALIDAR
                            $dise = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" title="Validar" aria-expanded="true" onclick="modalValiOpe(' . "'" . $row->itemplan . "'" . ',1)"><i class="fal fa-edit"></i></a>
                                     <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Expediente" download="" href="'.$row->path_expediente_diseno.'"><i class="fal fa-download"></i></a>';
                        }else{
                            $dise = 'PDT EJEC.';
                        }
                    }

                    $html .= '<tr>
                                <td>' . $dise . '</td>
                                <td>' . $row->itemplan . '</td>
                                <td>' . $row->proyectoDesc . '</td>
                                <td>' . $row->subProyectoDesc . '</td>
                                <td>' . $row->empresaColabDesc . '</td>
                                <td>' . $row->estadoPlanDesc . '</td>
                                <td>' . $row->cant_uip . '</td>
                                <td>' . number_format($row->total_diseno,2) . '</td>
                                <td>' . number_format($row->total_operaciones,2) . '</td>
                            </tr>';
                }
                $html .= '</tbody><tfoot class="thead-default">
             <tr class="bg-primary-600">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>               
                <td >TOTAL</td>
                <td><b>' . number_format($totalMoDiseno,2) . '</b></td>
                <td><b>' . number_format($totalMoOpera,2) . '</b></td>
                <td></td>
            </tr>
            </tfoot></table>';
        }
        return utf8_decode($html);
    }


    function validarItemplanPan() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        $number_ip;
        try {
            log_message('error', 'ENTRO VALIDAR IP MADRE');
            $data['error'] = EXIT_SUCCESS;
            
            $this->db->trans_begin();
            $flg_estado = $this->input->post('selectEstado');
            $itemplan   = $this->input->post('itemPlan');
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $comentario = $this->input->post('textComentario');
            $itemplan_m = $this->input->post('itemPlanPE');
			$flg_tipo   = $this->input->post('flg_tipo');         
            $idEstacion =   5;//ITEMPLAN MADRE FTTH SIEMPRE SERA FO
            if($flg_estado == 3) {//RECHAZADO
				if($flg_tipo  == 1) {//DISENO
					$dataPlanObra = array(
											"flg_valida_diseno" 	   => 2,
											"comentario_valida_diseno" => $comentario,
											"fecha_valida_diseno"      => $this->fechaActual(),
											"id_usuario_valida_diseno" => $idUsuario,
                                            "idEstadoPlan"             =>  2,
                                            "idUsuarioLog"             =>  $idUsuario,
                                            "fechaLog"                 =>  $this->fechaActual(),
                                            "descripcion"              =>  "DISEÑO RECHAZADO"
										);

				    $infoDisenoCopy =   $this->M_consulta_item_madre->getInfoDisenoToCopy($itemplan, $idEstacion);
                    $dataDisenoRecha = array('itemplan'             =>      $infoDisenoCopy['itemplan'],
                                             'idEstacion'           =>      $infoDisenoCopy['idEstacion'],
                                             'usuario_ejecucion'    =>   	$infoDisenoCopy['usuario_ejecucion'],
                                             'fecha_ejecucion'      =>   	$infoDisenoCopy['fecha_ejecucion'],
                                             'path_expediente_diseno' =>   	$infoDisenoCopy['path_expediente_diseno'],
                                             'requiere_licencia'    =>   	$infoDisenoCopy['requiere_licencia'],
                                             'usuario_rechazo'	    =>      $idUsuario,
                                             'fecha_rechazo'	    =>      $this->fechaActual(),
                                             'comentario_rechazo'   =>      $comentario);

                    $dataDisenoUpd = array(
                                            'estado'                    =>  2,
                                            'usuario_ejecucion'         =>  null,
                                            'fecha_ejecucion'           =>  null,
                                            'path_expediente_diseno'    =>  null,
                                            'requiere_licencia'         =>  null
                                        );
                    $data   =   $this->M_consulta_item_madre->rechazarItemplanHijo($itemplan, $idEstacion, $dataPlanObra, $dataDisenoRecha, $dataDisenoUpd);                      
                  
				}

            } else {//APPROBADO
				if($flg_tipo  == 1) {//DISENO

                    $costo_crea_oc = $this->M_consulta_item_madre->getCostoPqtFoFromFtthHijo($itemplan);

					$arrayUpdate = array(
											"flg_valida_diseno" 	   => 1,
											"comentario_valida_diseno" => $comentario,
											"fecha_valida_diseno"      => $this->fechaActual(),
											"id_usuario_valida_diseno" => $idUsuario,
                                            "costo_unitario_mo"        => $costo_crea_oc
										);
					$data = $this->m_utils->actualizarPlanObra($itemplan, $arrayUpdate);

					if($data['error'] == EXIT_ERROR) {
						throw new Exception($data['msj']);
					}
					
                    //$costo_crea_oc = $this->M_consulta_item_madre->getCostoPqtFoFromFtthHijo($itemplan);
                    //genera una solicitud de operaciones
                    if($costo_crea_oc > 0){
                        $sol_crea = $this->M_consulta_item_madre->generarSolicitudCreacionOCItemplanPersonalizado($itemplan, $costo_crea_oc, $idUsuario);
                    }
                    
					$arrayCountValida = $this->M_consulta_item_madre->haveDisenoValidaIpMadreHijos($itemplan_m);
                    if($arrayCountValida==null){
                        throw new Exception('No se encontro informacion, refresque la pantalla y vuelva a intentarlo!');
                    }
					if($arrayCountValida['diseno_completo'] == 1) {

						$dataUpdate = array(
												"idEstado"     => ID_ESTADO_PLAN_EN_CERTIFICACION,
												"idUsuarioLog" => $idUsuario,
												"fechaLog"     => $this->fechaActual(),
												"descripcion"  => 'VALIDADO EN ITEMPLAN MADRE'
											);
											
						$data = $this->M_consulta_item_madre->updateItemplanMadre($itemplan_m, $dataUpdate);
                                            
						if($data['error'] == EXIT_ERROR) {
							throw new Exception($data['msj']);
						}
											
						$data = $this->M_consulta_item_madre->updatePoToValidadoBrItemPlanMadre($itemplan_m, $idUsuario);
                        if($data['error'] == EXIT_ERROR) {
							throw new Exception('No se logro actualizar las PO de Diseno asociados al Itemplan Madre');
						}
						

                        $dataTotalDisenoVali = $this->M_consulta_item_madre->getCostoTotalPoValidadasDisenoByItemplanM($itemplan_m);
						$infoItemplanMadre   = $this->M_consulta_item_madre->getInfoItemplanMadre($itemplan_m);

						if($infoItemplanMadre['costo_mo_final'] != $dataTotalDisenoVali['total_diseno_vali']) {
							$flgResp = $this->M_consulta_item_madre->generarSolicitudItemPlanMadrePanEdicion($itemplan_m, $dataTotalDisenoVali['total_diseno_vali'], $idUsuario);
						
							if($flgResp != 1) {
								throw new Exception("No se genero la solicitud oc Edicion");
							}else{
                                $flgResp = $this->M_consulta_item_madre->generarSolicitudItemPlanMadrePanCerti($itemplan_m, $dataTotalDisenoVali['total_diseno_vali'], $idUsuario, 4);//PENDIENTE DE EDICION
                                if($flgResp != 1) {
                                    throw new Exception("No se genero la solicitud oc Cert");
                                }
                            }
						}else{
                            $flgResp = $this->M_consulta_item_madre->generarSolicitudItemPlanMadrePanCerti($itemplan_m, $dataTotalDisenoVali['total_diseno_vali'], $idUsuario, 5);//ESPERA DE ACTA
                            if($flgResp != 1) {
                                throw new Exception("No se genero la solicitud oc Cert");
                            }
                        }
					}
				} else {//OPERACIONES 
                    /*
					$arrayUpdate = array(
											"flg_valida_operacion" 	      => 1,
											"comentario_valida_operacion" => $comentario,
											"fecha_valida_operacion"      => $this->fechaActual(),
											"id_usuario_valida_operacion" => $idUsuario
										);
					$data = $this->m_utils->updateItemHijoPangiaco($itemplan, $arrayUpdate);
					
					if($data['error'] == EXIT_ERROR) {
						throw new Exception($data['msj']);
					}*/
				}
            }
            $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function fechaActual() {
        $zonahoraria = date_default_timezone_get();
        ini_set('date.timezone', 'America/Lima');
        setlocale(LC_TIME, "es_ES", "esp");
        $hoy = strftime("%Y-%m-%d %H:%M:%S");
        return $hoy;
    }
}