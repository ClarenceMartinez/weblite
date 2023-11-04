<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_consulta_control_presupuestal extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_control_presupuestal/m_consulta_control_presupuestal');
		$this->load->model('mf_consulta/m_solicitud_Vr');
        $this->load->library('lib_utils');
        $this->load->library('zip');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, 66, null, 68, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoBaVal($this->m_consulta_control_presupuestal->getBandejaControlPresupuestal('0',null,null,$idEmpresaColab));
           // $data['tablaValidacionObra'] = $this->getTablaConsulta(null, $idUsuario, null);
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_control_presupuestal/v_consulta_control_presupuestal',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    public function getArrayPoBaVal($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $row){ 
                
                $actions = '';
                $btnArchivo = '';
                if($row['url_archivo'] != null) {
					$btnArchivo = '<a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" download="" href="'.$row['url_archivo'].'"><i class="fal fa-download"></i></a>';
				}
                $btnVerDetalle = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ver Detalle Exceso" 
                                        onclick="openMdlDetalleExceso(this)" data-id_solicitud="'.$row['id_solicitud'].'" data-origen ="'.$row['origen'].'">
                                        <i class="fal fa-eye"></i>
                                    </a>';

                $btnAtender = '';              
                $actions = $btnArchivo.$btnVerDetalle.$btnAtender;
                 array_push($listaFinal, array($actions,  
                    $row['itemplan'],$row['estadoPlanDesc'], $row['tipo_origen'], (($row['codigo_po']=='null') ? '' : $row['codigo_po']), $row['proyectoDesc'],$row['subProyectoDesc'],$row['estacionDesc'], $row['tipo_po'], $row['eecc']
                   ,$row['zonalDesc'],$row['costo_unitario_mo'],$row['costoActualPo'],$row['excesoPo'],$row['costo_final_f'],$row['usua_solicita'],$row['fecha_solicita'],$row['usua_valida'],$row['fecha_valida'],$row['situacion']));
            }     
        }                                                            
        return $listaFinal;
    }

    function openMdlDetalleExceso() {
		$data['msj'] = null;
        $data['error'] = EXIT_ERROR;
		try {
			$id_solicitud = $this->input->post('id_solicitud');
			$origen       = $this->input->post('origen');
			
			if($id_solicitud == null || $id_solicitud == '') {
				throw new Exception('sin codigo po, comunicarse con el programador a cargo.');
			}
			
			if($origen == null || $origen == '') {
				throw new Exception('sin origen, comunicarse con el programador a cargo.');
			}

			if($origen == 4) {//LIQUIDACION PO MO
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleLiqui($id_solicitud);
			} else if($origen == 1) {//REGISTRO PO MAT
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleRegMat($id_solicitud);
			} else if($origen == 2) {//REGISTRO PO MO
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleRegMo($id_solicitud);
			} else if($origen == 3) {//SOLICITUD DE VR
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleVr($id_solicitud);
			} else if($origen == 5) {
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetalleAdicPqt($id_solicitud);
			} else if($origen == 6) {
				list($tablaDetalleSolicitud, $htmlComentario) = $this->getTablaDetallePin($id_solicitud);
			}

			$data['error'] = EXIT_SUCCESS;
			
			// $data['tablaDetallePo'] 	   = $tablaDetallePo;
			$data['tablaDetalleSolicitud'] = $tablaDetalleSolicitud;
			$data['htmlComentario']		   = $htmlComentario;
		} catch(Exception $e) {
			$data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));		
	}
    
    function getTablaDetalleRegMo($id_solicitud) {
		$ListaDetallePO = $this->m_consulta_control_presupuestal->getDataSolicitudRegMo($id_solicitud);
		$htmlDetallePO  = null;
		$comentario     = null;
		
		$htmlDetallePO .= '<table id="tbDetalleSolicitud" class="table table-bordered table-hover table-striped w-100">
							<thead class="bg-primary-600">
								<tr>
									<th>CODIGO</th>
									<th>DESCRIPCION</th>
									<th>BAREMO</th>
									<th>COSTO</th>
									<th>CANTIDAD NUEVA</th>
									<th>TOTAL NUEVO</th>
								</tr>
							</thead>
							<tbody>';

		foreach ($ListaDetallePO as $row) {
			$comentario = $row['comentario_reg'];
			$htmlDetallePO .= ' <tr>
									<th>' . $row['codigoPartida']. '</th>
									<td>' . utf8_decode($row['descripcion']) . '</td>
									<td>' . $row['baremo'] . '</td>
									<td>' . $row['preciario'] . '</td>
									<td style="background:#FAB8AA">' . $row['cantidadFinal'] . '</td>
									<td style="background:#FAB8AA">' . $row['total_partida'] . '</td>
								</tr>';
		}
		$htmlDetallePO .= '</tbody>
                    </table>';
		$areaComentario = '<textarea class="form-control input-mask" rows="4" disabled>'.utf8_decode($comentario).'</textarea>';			
		return array($htmlDetallePO, $areaComentario) ;
		
	}

    function fechaActual() {
        $zonahoraria = date_default_timezone_get();
        ini_set('date.timezone', 'America/Lima');
        setlocale(LC_TIME, "es_ES", "esp");
        $hoy = strftime("%Y-%m-%d %H:%M:%S");
        return $hoy;
    }

    function filtrarBandejaExceso() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');        
        $itemplan    = ($this->input->post('itemplan') != '' ? $this->input->post('itemplan') : null);       
		$idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
		$estado      = ($this->input->post('estado') != '' ? $this->input->post('estado') : null);  
        if($idUsuario == null) {
            throw new Exception('La sesión a expirado, recargue la página');
        }
 
        $data['json_bandeja']   = $this->getArrayPoBaVal($this->m_consulta_control_presupuestal->getBandejaControlPresupuestal($estado,null,$itemplan,$idEmpresaColab));
        $data['error']          = EXIT_SUCCESS;
        } catch(Exception $e) {
             $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

	function getTablaDetalleLiqui($id_solicitud) {
		$ListaDetallePO = $this->m_consulta_control_presupuestal->getDataSolicitudLiqui($id_solicitud);
		$htmlDetallePO  = null;
		$comentario     = null;
		
		$htmlDetallePO .= '<table id="tbDetalleSolicitud" class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th>CODIGO</th>
									<th>DESCRIPCION</th>
									<th>BAREMO</th>
									<th>COSTO</th>
									<th>CANTIDAD ACTUAL</th>
									<th>TOTAL ACTUAL</th>
									<th>CANTIDAD NUEVA</th>
									<th>TOTAL NUEVO</th>
								</tr>
							</thead>
							<tbody>';

		foreach ($ListaDetallePO as $row) {
			$comentario = $row['comentario_reg'];
			$htmlDetallePO .= ' <tr>
									<th>' . $row['codigoPartida']. '</th>
									<td>' . utf8_decode($row['descripcion']) . '</td>
									<td>' . $row['baremo'] . '</td>
									<td>' . $row['preciario'] . '</td>
									<td>' . $row['cantidad_actual'] . '</td>
									<td>' . $row['total_actual'] . '</td>
									<td style="background:#FAB8AA">' . $row['cantidadFinal'] . '</td>
									<td style="background:#FAB8AA">' . $row['total_partida'] . '</td>
								</tr>';
		}
		$htmlDetallePO .= '</tbody>
                    </table>';
		$areaComentario = '<textarea class="form-control input-mask" rows="4" disabled>'.utf8_decode($comentario).'</textarea>';			
		return array($htmlDetallePO, $areaComentario) ;
	}

	function getTablaDetalleRegMat($id_solicitud) {
		$ListaDetallePO = $this->m_consulta_control_presupuestal->getDataSolicitudRegMat($id_solicitud);
		$htmlDetallePO  = null;
		$comentario     = null;
		
		$htmlDetallePO .= '<table id="tbDetalleSolicitud" class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th>CODIGO MAT</th>
									<th>DESCRIPCION</th>
									<th>COSTO</th>
									<th>CANTIDAD</th>
									<th>TOTAL</th>
								</tr>
							</thead>
							<tbody>';

		foreach ($ListaDetallePO as $row) {
			$comentario = $row['comentario_reg'];
			$htmlDetallePO .= ' <tr>
									<th>' . $row['codigo_material']. '</th>
									<td>' . utf8_decode($row['descrip_material']) . '</td>
									<td>' . $row['costoMat'] . '</td>
									<td style="background:#FAB8AA">' . $row['cantidadFinal'] . '</td>
									<td style="background:#FAB8AA">' . $row['total_mat'] . '</td>
								</tr>';
		}
		$htmlDetallePO .= '</tbody>
                    </table>';
		$areaComentario = '<textarea class="form-control input-mask" rows="4" disabled>'.utf8_decode($comentario).'</textarea>';			
		return array($htmlDetallePO, $areaComentario) ;
		
	}

	function getTablaDetalleVr($id_solicitud) {
		$ListaDetallePO = $this->m_consulta_control_presupuestal->getDataSolicitudVr($id_solicitud);
		$htmlDetallePO  = null;
		$comentario     = null;
		
		$htmlDetallePO .= '<table id="tbDetalleSolicitud" class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th>CODIGO MAT</th>
									<th>DESCRIPCION</th>
									<th>COSTO</th>
									<th>CANTIDAD</th>
									<th>TIPO</th>
									<th>CANTIDAD INGRESADO SOL VR</th>
									<th>CANTIDAD FINAL</th>
									<th>TOTAL</th>
								</tr>
							</thead>
							<tbody>';

		foreach ($ListaDetallePO as $row) {
			$style = null;
			if($row['cantidadFin'] != null && $row['cantidadFin'] != '') {
				$style = 'style="background:#FAB8AA"';
			}
			$comentario = $row['comentario_reg'];
			$htmlDetallePO .= ' <tr>
									<th>' . $row['codigo_material']. '</th>
									<td>' . utf8_decode($row['descrip_material']) . '</td>
									<td>' . $row['costoMat'] . '</td>
									<td>' . $row['cant_actual'] . '</td>
									<td '.$style.'>' . $row['desc_tipo_solicitud'] . '</td>
									<td '.$style.'>' . $row['cantidadIngresado'] . '</td>
									<td '.$style.'>' . $row['cantidadFin'] . '</td>
									<td '.$style.'>' . $row['totalSolVr'] . '</td>
								</tr>';
		}
		$htmlDetallePO .= '</tbody>
                    </table>';
		$areaComentario = '<textarea class="form-control input-mask" rows="4" disabled>'.utf8_decode($comentario).'</textarea>';			
		return array($htmlDetallePO, $areaComentario) ;
		
	}
}
