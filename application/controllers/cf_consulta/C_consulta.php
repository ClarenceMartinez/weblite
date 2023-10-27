<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_consulta extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
		$this->load->model('mf_crecimiento_vertical/m_agenda_cv');
		$this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
		$this->load->model('mf_servicios/m_integracion_sirope');
        $this->load->model('mf_consulta/m_detalle_consulta');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario      = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
			$data['cmbProyecto'] = __buildProyectoAll(NULL, NULL);
            $data['cmbEstadoPlan'] = __buildComboEstadoPlan();
			$data['cmbMotivoCancelaIP'] = __cmbHTML2(__buildComboMotivoCancelaIP(), 'selectMotivoCance', null, 'select2 form-control w-100', 'Motivo Cancelación', null, null);
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_OBRA_PADRE, null, ID_CONSULTA_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['tablaAprobacionPin'] = $this->getTablaConsulta(NULL, NULL, array(null), $idEmpresaColab);
            $this->load->view('vf_consulta/v_consulta',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2, 'refresh');
	    }
    }

    function getTablaConsulta($itemplan, $idSubProyecto, $arrayEstadoPlan, $idEmpresaColab) {
        $arrayPlanobra = $this->m_utils->getPlanobraAll($itemplan, $idSubProyecto, $arrayEstadoPlan, $idEmpresaColab);
        $html = '<table id="tbPlanObra" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ACCIÓN</th>  
                            <th>ITEMPLAN</th>                            
                            <th>NOMBRE PLAN</th>
                            <th>SUBPROYECTO</th>
                            <th>EECC</th>
							<th>ORDEN COMPRA</th>
                            <th>ESTADO</th>
                            <th>ZONAL</th>
                            <th>CÓDIGO DE INVERSIÓN</th>
							<th>CÓDIGO DE OT</th>
                            <th>ULT. ESTADO DE OT FO</th>
                            <th>CÓDIGO DE OT COAX</th>
                            <th>ULT. ESTADO DE OT COAX</th>
                        </tr>
                    </thead>
                    <tbody>';
					
					$estadosValidos = array(ID_ESTADO_PLAN_DISENIO,ID_ESTADO_PLAN_EN_LICENCIA, ID_ESTADO_PLAN_EN_APROBACION, ID_ESTADO_PLAN_EN_OBRA);

                    foreach ($arrayPlanobra as $row) {
                        $btnDescarga = ' <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" 
                                            download href="'.$row['ruta_evidencia'].'"><i class="fal fa-download"></i>
                                        </a>';

						$btnSeguimientoCv = null;
                        if($row['idProyecto'] == ID_PROYECTO_CABLEADO_DE_EDIFICIOS){
                            //if(in_array($row['idEstadoPlan'],$estadosValidos)){
                                $btnSeguimientoCv = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver log seguimiento" data-itemplan="'.$row['itemplan'].'"
                                                        data-id_empresacolab="'.$row['idEmpresaColab'].'" onclick="getLogSeguimientoCV(this)">
                                                        <i class="fal fa-joystick"></i>
                                                    </a>';
                            //}
                        }
						$btnCancelar = null;
						if(in_array($row['idEstadoPlan'],array(ID_ESTADO_PLAN_PRE_REGISTRO,ID_ESTADO_PLAN_DISENIO, ID_ESTADO_PLAN_EN_LICENCIA, ID_ESTADO_PLAN_EN_APROBACION, ID_ESTADO_PLAN_EN_OBRA))){
                            $btnCancelar = '<a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" aria-expanded="true" title="Cancelar Itemplan" data-itemplan="'.$row['itemplan'].'"
                                                data-id_empresacolab="'.$row['idEmpresaColab'].'" onclick="openModalCancelarIP(this)">
                                                <i class="fal fa-ban"></i>
                                            </a>';

                        }
					
					/**crear ot mn**/
					$btnSendOtMN = null;
					$array_estado_ot_mn_permitidos = array(ID_ESTADO_PLAN_EN_OBRA, ID_ESTADO_PLAN_DISENIO, ID_ESTADO_PLAN_EN_LICENCIA, ID_ESTADO_PLAN_EN_APROBACION, ID_ESTADO_PLAN_PRE_LIQUIDADO, ID_ESTADO_PLAN_TERMINADO);
					if(in_array($row['idEstadoPlan'], $array_estado_ot_mn_permitidos)){
						$inforSiropeOT = $this->m_utils->getInfoSiropeByItemplan($row['itemplan']);
						if($inforSiropeOT['ot_mn'] == null){
							$btnSendOtMN = '<br><a  style="cursor:pointer;" data-item="' . $row['itemplan'] . '" data-accion="Crear ot Mantenimiento" onclick="openModalReenviarTramaMN($(this))">OT-MN(0)</a>';                        
						}else{
							$btnSendOtMN = '<br><a title="'.$inforSiropeOT['ot_mn'].'">OT-MN(1)</a>';
						}                
					}
                    $html .= ' <tr>
                                    <td>
                                        <div class="d-flex demo">
                                            <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ingresar" data-itemplan="'.$row['itemplan'].'"
                                              data-id_empresacolab="'.$row['idEmpresaColab'].'" href="getDetalleConsulta?itemplan='.$row['itemplan'].'" target="_blank"><i class="fal fa-search"></i></a>
                                            <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver log" data-itemplan="'.$row['itemplan'].'"
                                              data-id_empresacolab="'.$row['idEmpresaColab'].'" onclick="getLogPlanobra(this)">
                                              <i class="fal fa-clipboard-list"></i>
                                            </a>
											<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver Estaciones" data-itemplan="'.$row['itemplan'].'"
                                              data-id_empresacolab="'.$row['idEmpresaColab'].'" onclick="getTabEstaciones(this)">
                                              <i class="fal fa-warehouse"></i>
                                            </a>
											'.$btnDescarga.' '.$btnSeguimientoCv.$btnCancelar.' '.$btnSendOtMN.'
                                        </div>
                                    </td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['nombrePlan'].'</td>
                                    <td>'.$row['subproyectoDesc'].'</td>
                                    <td>'.$row['empresaColabDesc'].'</td>  
									<td>'.$row['orden_compra'].'</td>  
                                    <td><span class="'.$row['class_color'].'">'.$row['estadoPlanDesc'].'</span></td>
                                    <td>'.$row['zonalDesc'].'</td>
                                    <td>'.$row['codigoInversion'].'</td>
									<td>'.$row['ult_codigo_sirope'].'</td>
                                    <td>'.$row['ult_estado_sirope'].'</td>
                                    <td>'.$row['ult_codigo_sirope_coax'].'</td>
                                    <td>'.$row['ult_estado_sirope_coax'].'</td>									
                                </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
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

    function getHTMLTablaLog($itemplan) {
        $arrayLog = $this->m_utils->getLogPlanobra($itemplan);
        $html = '<table id="tb_log_itemplan" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ESTADO</th>
							<th>FECHA</th>
                            <th>USUARIO</th>
							<th>MOTIVO</th>
                            <th>COMENTARIO</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                    
        foreach($arrayLog as $row){            
            $html .=' <tr>                          
                        <td>'.$row['estado_prev_ip'].'</td>			
                        <td>'.$row['fecha_upd'].'</td>				
                        <td>'.$row['usuario'].'</td>
                        <td>'.''.'</td>
                        <td>'.$row['comentario'].'</td>
                    </tr>';
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }

	public function getDetallePO(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($codigoPO == null){
                throw new Exception('Hubo un error al recibir el código po');
            }

            $arrayDataPO = $this->m_utils->getDataPOByCod($codigoPO,array(0));
            if($arrayDataPO == null){
                throw new Exception('Hubo un error al traer la información de la po');
            }
            if($arrayDataPO['flg_tipo_area'] == 1){
                $data['tb_detalle'] = $this->tablaDetallePoMat($codigoPO);
            }else if($arrayDataPO['flg_tipo_area'] == 2){
                $data['tb_detalle'] = $this->tablaDetallePoMo($codigoPO);
            }
            
            $data['tb_log_po'] = $this->getHTMLTablaLogPO($codigoPO);
            $data['error'] = EXIT_SUCCESS;

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

	function getHTMLTablaLogPO($codigoPO) {
        $arrayLog = $this->m_utils->getLogPO($codigoPO);
        $html = '<table id="tb_log_po" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>ESTADO</th>
							<th>FECHA</th>
                            <th>USUARIO</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                    
        foreach($arrayLog as $row){            
            $html .=' <tr>                          
                        <td>'.$row['estado_po'].'</td>			
                        <td>'.$row['fecha_registro'].'</td>				
                        <td>'.$row['usuario_registro'].'</td>
                    </tr>';
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }

	function registrarEdicionPO() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $this->db->trans_begin();

            $arrayDetallePo = $this->input->post('arrayKitPartida');
            $objPoFinal     = json_decode($this->input->post('objPoFinal'), true);
            
            $idUsuario   = $this->session->userdata('idPersonaSessionPan');
            $fechaActual = $this->m_utils->fechaActual();
            
            if($objPoFinal['codigoPO'] == null || $objPoFinal['codigoPO'] == '') {
                throw new Excepion("No se encontro el codigo de po, comunicarse con el programador a cargo.");
            }

            if($objPoFinal['itemplan'] == null || $objPoFinal['itemplan'] == '') {
                throw new Exception("No se ecncontro el itemplan, verificar.");
            }

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesión ha expirado, cargar nuevamente la página.");
            }

			//_log(print_r($objPoFinal,true));
			//_log(print_r($arrayDetallePo,true));

            $dataDetalleSolicitud = array();
			$idSolicitud = null;
            $arrayDetalleInsert = array();
            foreach($arrayDetallePo as $row) {
                $detallePo = array (
					'codigo_po'        => $objPoFinal['codigoPO'],
					'codigoPartida'    => $row['codigoPartida'],
					'baremo'           => $row['baremo'],
					'costoMo'          => $row['costoMO'],
					'costoMat'         => $row['costoMAT'],
					'preciario'        => $row['costoPreciario'],
					'cantidadInicial'  => $row['cantidad'],
					'montoInicial'     => $row['total'],
					'cantidadFinal'    => $row['cantidad'],
					'montoFinal'       => $row['total']
				);
                $arrayDetalleInsert[] = $detallePo;

				$arrayDetSolTemp = array(
					'id_solicitud' 	   => &$idSolicitud,
					'codigoPartida'    => $row['codigoPartida'],
					'baremo'           => $row['baremo'],
					'costoMo'          => $row['costoMO'],
					'costoMat'         => $row['costoMAT'],
					'preciario'        => $row['costoPreciario'],
					'cantidadInicial'  => $row['cantidad'],
					'montoInicial'     => $row['total'],
					'cantidadFinal'    => $row['cantidad'],
					'montoFinal'       => $row['total']
				);
				$dataDetalleSolicitud []= $arrayDetSolTemp;
            }


			$infoCU = $this->m_utils->getVariablesCostoUnitario($objPoFinal['itemplan'], 2, null);
			$costoUnitarioObra = $infoCU['costo_unitario_mo'];//costo limite de la obra
			if($costoUnitarioObra == null || $costoUnitarioObra == 0){
				throw new Exception('La Obra no cuenta con Costo Unitario de Mo Registrado.');
			}

			$hasSolActivo = $this->m_utils->getCountExcesoPdt($objPoFinal['itemplan']);
			if($hasSolActivo > 0){
				throw new Exception('No se pueden aplicar los cambios, debido ah que cuenta con una Solicitud de Exceso Pendiente de Aprobacion.');
			}
			$costoTotalAllPo = $infoCU['total'];//costo actual de todas las po mo
			$nuevoCostoTotalAllPo = $costoTotalAllPo + $objPoFinal['total'];

			if($nuevoCostoTotalAllPo > $costoUnitarioObra){// HAY EXCESO, CREAMOS SOLICITUD DE EXCESO
				$exceso = $nuevoCostoTotalAllPo - $costoUnitarioObra;
				$dataInsert = array(
					'itemplan' 		=> $objPoFinal['itemplan'],
					'codigo_po'     => $objPoFinal['codigoPO'],
					'tipo_po'  		=> 2,
					'costo_inicial' => $costoUnitarioObra,
					'exceso_solicitado' => $exceso,
					'costo_final' => ($costoUnitarioObra+$exceso),
					'usuario_solicita' => $idUsuario,
					'fecha_solicita' => $fechaActual,
					'comentario_reg' => 'exceso generado por el costo de las partidas',
					'idEstacion'     => $objPoFinal['idEstacion'],
					'origen'	     =>	2,
					'url_archivo'    => null
				);
				$responseInsertSol = $this->m_utils->registrarSolicitudCP($dataInsert);
				if($responseInsertSol['error'] == EXIT_ERROR){
					throw new Exception($responseInsertSol['msj']);
				}
				$idSolicitud = $responseInsertSol['id_solicitud'];
				$data = $this->m_utils->regDetalleRegPoMo($dataDetalleSolicitud);
				if($data['error'] == EXIT_ERROR){
					throw new Exception($data['msj']);
				}
				$data['msj'] = 'Se registró la solicitud de exceso, atiendala para poder aplicar los cambios en la po!!';
			}else{
				$responseDelete = $this->m_utils->deleteDetallePOMO($objPoFinal['codigoPO']);
				if($responseDelete['error'] == EXIT_ERROR){
					throw new Exception($responseDelete['msj']);
				}
				$data = $this->m_utils->registrarDetallePoMo($arrayDetalleInsert);
				if($data['error'] == EXIT_ERROR){
					throw new Exception($data['msj']);
				}
				$data['msj'] = 'Se actulizó correctamente los datos de la po!!';
			}
            $this->db->trans_commit();
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        echo json_encode($data);
    }

    function tablaDetallePoMo($codigo_po) {
        $arrayKit = $this->m_utils->getDataPoDetalleMo(null, $codigo_po, array(0));
        $costo=null;

        $html = '<table id="tbKitPartida" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>Código</th>  
                            <th>Partida</th>                            
                            <th>Baremo</th>
                            <th>Precio</th>
                            <th>Cantidad Inicial</th>
                            <th>Cantidad Final</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayKit as $row) {

                        $costo=$row['costo_total'];
                        $html .= '  <tr>
                                        <td>'.$row['codigoPartida'].'</td>
                                        <td>'.$row['nomPartida'].'</td>
                                        <td>'.$row['baremo'].'</td>
                                        <td>'.$row['preciario'].'</td>
                                        <td>'.$row['cantidadInicial'].'</td>
                                        <td>'.$row['cantidadFinal'].'</td>
                                        <td>'.$row['montoFinal'].'</td>  
                                      
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>
                        <div><strong>TOTAL: S/.</strong> <span>'.$costo.'</span></div>';
        return $html;
    }
	
	function tablaDetallePoMat($codigo_po) {
        $arrayKit = $this->m_utils->getDataPoDetalleMat(null, $codigo_po, array(0));
        $costo = null;

        $html = '<table id="tbKitPartida" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>MATERIAL</th>  
                            <th>DESCRIPCIÓN</th>                            
                            <th>UDM</th>
                            <th>PRECIO</th>
                            <th>CANTIDAD INICIAL</th>
                            <th>CANTIDAD FINAL</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayKit as $row) {

                        $costo = $row['costo_total'];
                        $html .= '  <tr>
                                        <td>'.$row['codigo_material'].'</td>
                                        <td>'.$row['descrip_material'].'</td>
                                        <td>'.$row['unidad_medida'].'</td>
                                        <td>'.$row['costoMat'].'</td>
                                        <td>'.$row['cantidadInicial'].'</td>
                                        <td>'.$row['cantidadFinal'].'</td>
                                        <td>'.$row['montoFinal'].'</td>  
                                      
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>
                        <div><strong>TOTAL: S/.</strong> <span>'.(number_format($costo,2)).'</span></div>';
        return $html;
    }
	
	public function getLogSeguimientoCV(){
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
            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información el itemplan');
            }
			$estadosValidos = array(ID_ESTADO_PLAN_DISENIO,ID_ESTADO_PLAN_EN_LICENCIA, ID_ESTADO_PLAN_EN_APROBACION, ID_ESTADO_PLAN_EN_OBRA);
            $btnSave = '';
            $style = 'none';
            if(in_array($infoItem['idEstadoPlan'],$estadosValidos)){
                $btnSave = '<button class="btn btn-primary ml-auto waves-effect waves-themed" type="button" onclick="regLogSeguimientoCV(this)" id="btnSeguimiento">Guardar</button>';
                $style = 'block';
            }

            $data['error'] = EXIT_SUCCESS;
            $data['tbLog'] = $this->getHTMLTablaLogSeguimientoCV($itemplan);
            $data['cmbMotivoSegui'] = '<option value="">Seleccione Motivo</option>'.__buildComboMotivoSeguiCV($infoItem['idEstadoPlan'],$itemplan);
			$data['btnSaveSegui'] = $btnSave;
            $data['style'] = $style;

        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getHTMLTablaLogSeguimientoCV($itemplan) {
        $arrayLog = $this->m_utils->getLogSeguimientoCV($itemplan);
        $html = '<table id="tb_log_segui_cv" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>#</th>
							<th>MOTIVO</th>
                            <th>USUARIO REGISTRO</th>
                            <th>FECHA REGISTRO</th>
							<th>COMENTARIO</th>
                            <th>ESTADO PLAN DEL MOMENTO</th>
                            <th>ESTADO PLAN ACTUAL</th>
                        </tr>
                    </thead>                    
                    <tbody>';
        $count = 1;                                                                   
        foreach($arrayLog as $row){            
            $html .=' <tr>
                        <td>'.$count.'</td>                         
                        <td>'.$row['desc_motivo'].'</td>			
                        <td>'.$row['nombre_completo'].'</td>				
                        <td>'.$row['fecha_registro'].'</td>
                        <td>'.$row['comentario_incidencia'].'</td>
                        <td>'.$row['estadoPlanMomentoReg'].'</td>
                        <td>'.$row['estadoPlanActual'].'</td>
                    </tr>';
            $count++;
        }
        $html .='</tbody>
            </table>';
                
        return $html;
    }
	
	public function registrarLogSeguimientoCV(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idMotivo = $this->input->post('selectMotivoSegui') ? $this->input->post('selectMotivoSegui') : null;
            $comentario = $this->input->post('txtComentario') ? $this->input->post('txtComentario') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información el itemplan');
            }
			$estadosValidos = array(ID_ESTADO_PLAN_DISENIO,ID_ESTADO_PLAN_EN_LICENCIA, ID_ESTADO_PLAN_EN_APROBACION, ID_ESTADO_PLAN_EN_OBRA);
            if(!in_array($infoItem['idEstadoPlan'],$estadosValidos)){
                throw new Exception('El itemplan se encuentra en un estado no válido para registrar.');
            }
			
            $fechaActual = $this->m_utils->fechaActual();

            $dataInsert = array(  
                'itemplan'              =>  $itemplan,
                'idEstadoPlan'          =>  $infoItem['idEstadoPlan'],
                'usuario_registro'      =>  $idUsuario,
                'fecha_registro'        =>  $fechaActual,
                'id_motivo_seguimiento' =>  $idMotivo,
                'comentario_incidencia' =>  $comentario
            );
            $data = $this->m_agenda_cv->createSeguimientoCV($dataInsert);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();
            $data['tbLog'] = $this->getHTMLTablaLogSeguimientoCV($itemplan);
            $data['cmbMotivoSegui'] = '<option value="">Seleccione Motivo</option>'.__buildComboMotivoSeguiCV($infoItem['idEstadoPlan'],$itemplan);

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
	
	public function cancelarPO(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
            $idEstacion = $this->input->post('estacion') ? $this->input->post('estacion') : null;

            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($codigoPO == null){
                throw new Exception('Hubo un error al recibir el código de po');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            if($idEstacion == null){
                throw new Exception('Hubo un error al recibir la estación');
            }

            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información el itemplan');
            }
            $fechaActual = $this->m_utils->fechaActual();

            $arrayDataPO = $this->m_utils->getDataPOByCod($codigoPO,array(0));
            if($arrayDataPO == null){
                throw new Exception('Hubo un error al traer la información de la po');
            }

            $hasSolActivo = $this->m_utils->getCountExcesoPdt($itemplan);
            if($hasSolActivo > 0) {
				throw new Exception('Tiene una solicitud de exceso activa, verificar.');
			}

            $dataUp = array(
                'estado_po' =>  ID_ESTADO_PO_CANCELADO,
                'codigo_po' =>  $codigoPO
            );
            $updateDataPo[] = $dataUp;
            $dataIn = array(
                'codigo_po' =>  $codigoPO,
                'itemplan' =>  $itemplan,
                'idUsuario' => $idUsuario,
                'fecha_registro' => $fechaActual,
                'idPoestado'    =>  ID_ESTADO_PO_CANCELADO,
                'controlador'   => 'cancelación po'
            );
            $insertDataLog[] = $dataIn;
            $data = $this->m_utils->updateBatchPo($updateDataPo, $insertDataLog);
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();

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
            $idSubProyecto = $this->input->post('idSubProyecto') ? $this->input->post('idSubProyecto') : null;
            $arrayEstadoPlan = $this->input->post('arrayEstadoPlan') ? json_decode($this->input->post('arrayEstadoPlan'),true) : array("");
            $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');

            if(count($arrayEstadoPlan) == 0){
                $arrayEstadoPlan = array("");
            }
			if($itemplan != null){
                $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
                if($infoItem == null){
                    throw new Exception('Hubo un error al traer la información del itemplan!!');
                }
                $hasTramasOt = $this->m_utils->getInfoOtsCreadosONoSirope($itemplan);
				$arrayIpException = array('22-2121300002','22-2111100037','22-2111100116','22-2111100040','22-2111100041','22-2111100042','22-2111100044','22-2111100117','22-2111100234','22-2111100046','22-2111100118','22-2111100119','22-2111100120','22-2111100081','22-2111100082','22-2111100083','22-2111200012','22-2111100085','22-2111100086','22-2111100123','22-2111100087','22-2110900030','22-2111100084','22-2111100205','22-2111100207','22-2111100208','22-2111100209','22-2111200022','22-2111100210','22-2111100213','22-2111100217','22-2111200023','22-2111100226','22-2111100227','22-2111100228','22-2111100229','22-2111100230');
                if(in_array($infoItem['idEstadoPlan'],array(ID_ESTADO_PLAN_DISENIO,ID_ESTADO_PLAN_EN_OBRA,ID_ESTADO_PLAN_PRE_LIQUIDADO)) || in_array($itemplan,$arrayIpException)){
                    if($hasTramasOt != null){
                        if($hasTramasOt['has_ac'] > 0){
                            if($infoItem['has_sirope_ac'] != '1'){
                                $this->m_integracion_sirope->execWsFilter($itemplan, $itemplan.'AC');//que siempre guarde los intentos
                            }
                        }
                        if($hasTramasOt['has_coax'] > 0 || $infoItem['ult_codigo_sirope_coax'] != null){
                            if($infoItem['has_sirope_coax'] != '1'){
                                $this->m_integracion_sirope->execWsFilter($itemplan, $itemplan.'COAX');//que siempre guarde los intentos
                            }
                        }
                        if($hasTramasOt['has_fo'] > 0 || $infoItem['ult_codigo_sirope'] != null){
                            if($infoItem['has_sirope_fo'] != '1'){
                                $this->m_integracion_sirope->execWsFilter($itemplan, $itemplan.'FO');//que siempre guarde los intentos
                            }
                        }
                    }
                }
            }
			
            $data['tbConsulta'] = $this->getTablaConsulta($itemplan,$idSubProyecto,$arrayEstadoPlan,$idEmpresaColab);
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
	
	function getDataPartidaAdicIntegral() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan');
			$codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
			$origen = $this->input->post('origen') ? $this->input->post('origen') : null;

            if($codigoPO == null){
                throw new Exception('Hubo un error al recibir el código de po');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información del itemplan!!');
            }

            $arrayCosto = $this->m_registro_itemplan_masivo->getCostoxDptoByIdEECCAndSubProy($infoItem['idSubProyecto'], $infoItem['idEmpresaColab'], $infoItem['cantFactorPlanificado']);
            if($arrayCosto == null){
                throw new Exception('LA OBRA NO CUENTA CON UN PRECIO CONFIGURADO PARA EL SUBPROYECTO, CONTRATA Y DPTO.');
            }

            $data['error'] = EXIT_SUCCESS;
            $tablaPartidaAdic = $this->getTablaPartidaAdicIntegral($itemplan);
            list($tablaDetallePo, $arrayDetallePo, $totalDetPO)  = $this->tablaPoDetalleMo($itemplan,$codigoPO, $arrayCosto);
            $data['tablaPartidaAdic'] = $tablaPartidaAdic;
            $data['tablaDetallePo']  = $tablaDetallePo;
            $data['arrayDetallePo']  = $arrayDetallePo;
			$data['totalDetPO'] =  $totalDetPO;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getTablaPartidaAdicIntegral($itemplan) {
        $arrayKit = $this->m_utils->getPartidasAdicIntegral($itemplan);

        $html = '<table id="tbPartidaAdicIntegral" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>CÓDIGO</th>  
                            <th>PARTIDA</th>
                            <th>BAREMO</th>                       
                            <th>COSTO</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayKit as $row) {

                        $html .= '  <tr>
                                        <td>'.$row['codigoPartida'].'</td>
                                        <td>'.$row['nomPartida'].'</td>
                                        <td>'.$row['baremo'].'</td>
                                        <td>'.$row['precio'].'</td>  
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary btn-icon" aria-expanded="true" data-codigo_partida="'.$row['codigoPartida'].'"
                                               data-baremo="'.$row['baremo'].'" data-precio="'.$row['precio'].'" data-nom_partida="'.$row['nomPartida'].'"
                                               title="Agregar" onclick="agregarPartidaAdicIntegral(this);"><i class="fal fa-plus"></i>
                                            </a>
                                        </td>         
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function tablaPoDetalleMo($itemplan,$codigoPO, $arrayCosto) {

	    $arrayEstadoPO = array(0);
        $arrayDetalleMo = $this->m_utils->getDataPoDetalleMo($itemplan, $codigoPO, $arrayEstadoPO);
        

        $html = '<table class="table table-bordered">
                    <thead class="bg-primary-600">
                        <th>ACCIÓN</th>
                        <th>CÓDIGO</th>
                        <th>PARTIDA</th>
                        <th>BAREMO</th>
                        <th>PRECIO</th>
                        <th>CANTIDAD INICIAL</th>
                        <th>CANTIDAD FINAL</th>
                        <th style="text-align: center">TOTAL</th>
                    </thead>
                <tbody id="tBodyPoDetalle">';
                    $total= 0;
                    $arrayDetallePo = array();
                    foreach ($arrayDetalleMo as $row) {
                        $total = $row['montoFinal'] + $total;

                        $objDetallePo['codigoPartida']   = $row['codigoPartida'];
                        $objDetallePo['baremo']          = $row['baremo'];
                        $objDetallePo['preciario']       = $row['preciario'];
                        $objDetallePo['cantidadInicial'] = $row['cantidadInicial'];
                        $objDetallePo['montoInicial']    = $row['montoInicial'];
                        $objDetallePo['cantidadFinal']   = $row['cantidadFinal'];
                        $objDetallePo['montoFinal']      = $row['montoFinal'];
                        $objDetallePo['costoMo']         = $row['costoMo'];

                        $arrayDetallePo[]= $objDetallePo;
                        $idTr = 'id="tr_'.$row['codigoPartida'].'"';
                        $trCantFinal = '<input type="text" class="form-control"  id="cantidadFinal'.$row['codigoPartida'].'" 
                                        data-codigo_partida="'.$row['codigoPartida'].'" onkeyup="calculaTotalPartAdic(this)" value="'.$row['cantidadFinal'].'"
                                        style="border-style: ridge; border-width: 4px; text-align: center">';
                        $btnEliminar = '<a data-codigo_partida="'.$row['codigoPartida'].'" class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Eliminar" onclick="eliminarPartidaAdicIntegral(this)"><i class="fal fa-trash"></i></a>';
                        if($row['codigoPartida'] == $arrayCosto['codigoPartida']){
                            $trCantFinal = $row['cantidadFinal'];
                            $btnEliminar = '';
                            $idTr = '';
                        }

                        $html .= '  <tr '.$idTr.'>
                                        <td>'.$btnEliminar.'</td>
                                        <td>'.$row['codigoPartida'].'</td>
                                        <td>'.$row['nomPartida'].'</td>
                                        <td id="baremo'.$row['codigoPartida'].'">'.$row['baremo'].'</td>
                                        <td id="costo'.$row['codigoPartida'].'">'.$row['preciario'].'</td>
                                        <td id="cantidadIni'.$row['codigoPartida'].'">'.$row['cantidadInicial'].'</td>
                                        <td>'.$trCantFinal.'</td>
                                        <td id="total'.$row['codigoPartida'].'">'.$row['montoFinal'].'</td>
                                    </tr>';
                    }
                    $html .= '</tbody>
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
                                                <h4 class="m-0 fw-700 h2 keep-print-font" id="costoTolalPo">S/.'.number_format($total,2).'</h4>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group" align="center">
                            <button class="btn btn-primary" onclick="guardarPartidaAdicIntegral()">Guardar</button>
                        </div>
                       ';
        return array($html, $arrayDetallePo, $total);
    }

    function regEditPartidaAdicIntegral() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {

            $this->db->trans_begin();

            $arrayDetallePo = json_decode($this->input->post('arrayDetallePO'), true);
            $objPoFinal = json_decode($this->input->post('objPoFinal'), true);
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            $fechaActual = $this->m_utils->fechaActual();
            
            if($objPoFinal['codigoPO'] == null || $objPoFinal['codigoPO'] == '') {
                throw new Excepion("No se encontro el codigo de po, comunicarse con el programador a cargo.");
            }

            if($objPoFinal['itemplan'] == null || $objPoFinal['itemplan'] == '') {
                throw new Exception("No se ecncontro el itemplan, verificar.");
            }

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesión ha expirado, cargar nuevamente la página.");
            }

			// _log(print_r($objPoFinal,true));
			// _log(print_r($arrayDetallePo,true));

            // throw new Exception("GAAAAAAAAAAAAAAAAAAAAA");

            $data = $this->m_utils->insertLogPartidasAdicIntegral($objPoFinal['codigoPO'],$idUsuario);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }

            $arrayDetalleInsert = array();
            foreach($arrayDetallePo as $row) {
                $detallePo = array (
					'codigo_po'        => $objPoFinal['codigoPO'],
					'codigoPartida'    => $row['codigoPartida'],
					'baremo'           => $row['baremo'],
					'preciario'        => $row['preciario'],
					'cantidadInicial'  => $row['cantidadInicial'],
					'montoInicial'     => $row['montoInicial'],
					'cantidadFinal'    => $row['cantidadFinal'],
					'montoFinal'       => $row['montoFinal'],
                    'costoMo'          => $row['costoMo'],
					'costoMat'         => null
				);
                $arrayDetalleInsert[] = $detallePo;
            }

            $data = $this->m_utils->deleteDetallePOMO($objPoFinal['codigoPO']);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $data = $this->m_utils->registrarDetallePoMo($arrayDetalleInsert);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $arrayUpdatePO = array(
                'costo_total' => $objPoFinal['total']
            );
            $data = $this->m_utils->actualizarPoByCodigo($objPoFinal['codigoPO'],$arrayUpdatePO);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }
            $data['msj'] = 'Se actualizó correctamente los datos de la po!!';
            $this->db->trans_commit();

        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        echo json_encode($data);
    }
	
	public function cancelarItemplan(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;
            $idEstadoPlanNew = $this->input->post('selectEstadoUpd') ? $this->input->post('selectEstadoUpd') : null;
            $idMotivo = $this->input->post('selectMotivoCance') ? $this->input->post('selectMotivoCance') : null;
            $comentario = $this->input->post('txtComentario2') ? $this->input->post('txtComentario2') : null;
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null;
            $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');

            $this->db->trans_begin();

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }
            if($idMotivo == null){
                throw new Exception('Hubo un error al recibir el motivo de cancelación');
            }
            if($comentario == null){
                throw new Exception('Hubo un error al recibir el comentario');
            }
            if(count($_FILES) == 0){
                throw new Exception('Hubo un error al recibir la evidencia!!');
            }

            $infoItem = $this->m_utils->getPlanObraByItemplan($itemplan);
            if($infoItem == null){
                throw new Exception('Hubo un error al traer la información el itemplan.');
            }
            $estadosValidos = array(24, ID_ESTADO_PLAN_PRE_REGISTRO,ID_ESTADO_PLAN_DISENIO,ID_ESTADO_PLAN_EN_LICENCIA,ID_ESTADO_PLAN_EN_APROBACION,ID_ESTADO_PLAN_EN_OBRA, 7, 10, 18);
            if(!in_array($infoItem['idEstadoPlan'],$estadosValidos)){
                throw new Exception('El itemplan se encuentra en un estado no válido para la cancelación.');
            }
            $fechaActual = $this->m_utils->fechaActual();

            if (!file_exists("uploads/evidencia_cancelar_ip")) {
                if (!mkdir("uploads/evidencia_cancelar_ip")) {
                    throw new Exception('Hubo un error al crear la carpeta evidencia_cancelar_ip!!');
                }
            }

            if (!file_exists("uploads/evidencia_cancelar_ip/".$itemplan)) {
                if (!mkdir("uploads/evidencia_cancelar_ip/".$itemplan)) {
                    throw new Exception('Hubo un error al crear la carpeta evidencia_cancelar_ip/'.$itemplan.'!!');
                }
            }
            $rutaFinalArchivo = null;
            if(count($_FILES) > 0){
                $nombreArchivo = $_FILES['file']['name'];
                $tipoArchivo = $_FILES['file']['type'];
                $nombreArchivoTemp = $_FILES['file']['tmp_name'];
                $tamano_archivo = $_FILES['file']['size'];
                $nombreFinalArchivo = date("Y_m_d_His_").$nombreArchivo;
                $rutaFinalArchivo = "uploads/evidencia_cancelar_ip/".$itemplan."/".$nombreFinalArchivo;
                if (!move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {
                    throw new Exception('No se pudo subir el archivo: ' . $nombreFinalArchivo . ' !!');
                }
            }
            

            $dataUpdatePO = array(  
                'idEstadoPlan'          =>  $idEstadoPlanNew,
                'idUsuarioLog'          =>  $idUsuario,
                'fechaLog'              =>  $fechaActual,
                'idMotivo'              =>  $idMotivo,
                'descripcion'           =>  $comentario,
                'ruta_archivo'          =>  $rutaFinalArchivo
            );
            $data = $this->m_utils->actualizarPlanObra($itemplan,$dataUpdatePO);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }

            if($idEstadoPlanNew ==  6){
                $infoSolicitud = $this->m_utils->getInfoSolicitudOCByIP($itemplan);
                if($infoSolicitud != null){
                    $rsp = $this->m_utils->fnRegistrarSolicitudOcEdicCerti($itemplan, $infoItem['costo_unitario_mo'], $idUsuario, 'PLAN', 4);
                    if($rsp != 1) {
                        $data['error'] = EXIT_ERROR;
                        throw new Exception('No se pudo anular/cancelar la solicitud del itemplan.');
                    }
                }

                $rsp2 = $this->m_utils->fnCancelarPOByItemplan($itemplan, $idUsuario);
                if($rsp2 != 1) {
                    $data['error'] = EXIT_ERROR;
                    throw new Exception('Hubo un error al cancelar las po del itemplan.');
                }
            }
			

           
            $this->db->trans_commit();
            $data['tbConsulta'] = $this->getTablaConsulta($itemplan,null,array(null),$idEmpresaColab);

        }catch(Exception $e){
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function saveQuiebreCVR(){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try{
            $itemplanGen = $this->input->post('itemplanGen') ? $this->input->post('itemplanGen') : null;  
            $itemplan = $this->input->post('itemplan') ? $this->input->post('itemplan') : null;           
            $idUsuario = $this->session->userdata('idPersonaSessionPan') ? $this->session->userdata('idPersonaSessionPan') : null; 
            $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');

            if($idUsuario == null){
                throw new Exception('Su sesion ha expirado, porfavor vuelva a logearse.');
            }
            if($itemplan == null){
                throw new Exception('Hubo un error al recibir el itemplan');
            }            
            if(count($_FILES) == 0){
                throw new Exception('Hubo un error al recibir la evidencia!!');
            }

            $ult_log_cv = $this->m_detalle_consulta->getLastLogByItemplanHijoCv($itemplan);
            if($ult_log_cv  ==  null){
                throw new Exception('No se detecto log del ultimo movimiento, comunicarse con sopoerte!!');
            }

            $fechaActual = $this->m_utils->fechaActual();

            if (!file_exists("uploads/evidencia_quiebre_cv")) {
                if (!mkdir("uploads/evidencia_quiebre_cv")) {
                    throw new Exception('Hubo un error al crear la carpeta evidencia_quiebre_cv!!');
                }
            }

            if (!file_exists("uploads/evidencia_quiebre_cv/".$itemplan)) {
                if (!mkdir("uploads/evidencia_quiebre_cv/".$itemplan)) {
                    throw new Exception('Hubo un error al crear la carpeta evidencia_quiebre_cv/'.$itemplan.'!!');
                }
            }
            $rutaFinalArchivo = null;
            if(count($_FILES) > 0){
                $nombreArchivo = $_FILES['file']['name'];
                $tipoArchivo = $_FILES['file']['type'];
                $nombreArchivoTemp = $_FILES['file']['tmp_name'];
                $tamano_archivo = $_FILES['file']['size'];
                $nombreFinalArchivo = date("Y_m_d_His_").$nombreArchivo;
                $rutaFinalArchivo = "uploads/evidencia_quiebre_cv/".$itemplan."/".$nombreFinalArchivo;
                if (!move_uploaded_file($nombreArchivoTemp, $rutaFinalArchivo)) {
                    throw new Exception('No se pudo subir el archivo: ' . $nombreFinalArchivo . ' !!');
                }
            }

            $dataUpdate = array(  
                'ip_hijo'                   =>  $itemplan,
                'has_quiebre_evidencia'     =>  1
            );

            $dataUpdateLog = array(
                'id'                        =>  $ult_log_cv['id'],
                'usua_reg_file_quiebre'     =>  $idUsuario,
                'fec_reg_file_quiebre'      =>  $fechaActual, 
                'path_file_quiebre'         =>  $rutaFinalArchivo
            );

            $data = $this->m_detalle_consulta->updateQuiebreCV($dataUpdate, $dataUpdateLog);
            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }			
            $data['tbConsulta'] = $this->getTablaConsulta($itemplanGen,null,array(null),$idEmpresaColab);

        }catch(Exception $e){        
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
	
	    public function excuteSiropeFilterMasivo()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$arrayItemplan = array('22-2110900016','22-2110900017','22-2110900018','22-2110900019','22-2110900020','22-2110900021','22-2110900022','22-2110900023','22-2110900025','22-2110900026','22-2110900027','22-2110900028','22-2110900029','22-2110900030','22-2110900031','22-2110900032','22-2110900033','22-2110900034','22-2110900035','22-2110900036','22-2110900037','22-2110900038','22-2110900039','22-2110900040','22-2110900041','22-2110900042','22-2110900043','22-2110900044','22-2110900045','22-2110900046','22-2110900047','22-2110900048','22-2110900049','22-2110900050','22-2110900051','22-2110900052','22-2110900053','22-2110900054','22-2110900055','22-2110900056','22-2110900057','22-2111100034','22-2111100035','22-2111100036','22-2111100037','22-2111100038','22-2111100039','22-2111100040','22-2111100041','22-2111100042','22-2111100043','22-2111100044','22-2111100045','22-2111100046','22-2111100047','22-2111100048','22-2111100049','22-2111100050','22-2111100051','22-2111100052','22-2111100053','22-2111100054','22-2111100055','22-2111100056','22-2111100057','22-2111100058','22-2111100059','22-2111100060','22-2111100061','22-2111100062','22-2111100063','22-2111100064','22-2111100065','22-2111100066','22-2111100067','22-2111100068','22-2111100069','22-2111100072','22-2111200005','22-2121300002');
            foreach($arrayItemplan as $item){
                if($item != null){
                    $infoItem = $this->m_utils->getPlanObraByItemplan($item);
                    if($infoItem == null){
                        throw new Exception('Hubo un error al traer la información del itemplan!!');
                    }
					$this->m_integracion_sirope->execWsFilter($item, $item.'FO');
					/*
                    $hasTramasOt = $this->m_utils->getInfoOtsCreadosONoSirope($item);
                    if(in_array($infoItem['idEstadoPlan'],array(ID_ESTADO_PLAN_DISENIO,ID_ESTADO_PLAN_EN_OBRA,ID_ESTADO_PLAN_PRE_LIQUIDADO))){
                        if($hasTramasOt != null){
                            if($hasTramasOt['has_ac'] > 0){
                                if($infoItem['has_sirope_ac'] != '1'){
                                    $this->m_integracion_sirope->execWsFilter($item, $item.'AC');//que siempre guarde los intentos
                                }
                            }
                            if($hasTramasOt['has_coax'] > 0 || $infoItem['ult_codigo_sirope_coax'] != null){
                                if($infoItem['has_sirope_coax'] != '1'){
                                    $this->m_integracion_sirope->execWsFilter($item, $item.'COAX');//que siempre guarde los intentos
                                }
                            }
                            if($hasTramasOt['has_fo'] > 0 || $infoItem['ult_codigo_sirope'] != null){
                                if($infoItem['has_sirope_fo'] != '1'){
                                    $this->m_integracion_sirope->execWsFilter($item, $item.'FO');//que siempre guarde los intentos
                                }
                            }
                        }
                    }*/
                }
            }
            
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
}
