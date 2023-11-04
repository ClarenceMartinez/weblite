<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_validacion_pin extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_orden_compra/m_bandeja_solicitud_oc');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');

            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_PLANTA_INTERNA_PADRE, null, ID_VALIDACION_PIN_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
           // $data['tablaLiquidacionPin'] = $this->getTablaValidacion(NULL, NULL, array(ID_ESTADO_PLAN_PRE_LIQUIDADO));
            $data['json_bandeja'] = $this->getArrayPoBaCoti($this->m_utils->getPlanobraAll(null, null, array(ID_ESTADO_PLAN_PRE_LIQUIDADO), null, ID_TIPO_PLANTA_INTERNA));
            $this->load->view('vf_planta_interna/v_validacion_pin',$data);        	  
    	 }else{
        	redirect('login', 'refresh');
	    }     
    }

    public function getArrayPoBaCoti($listaCotiPin){
        $listaFinal = array();      
        if($listaCotiPin!=null){
            foreach($listaCotiPin as $row){ 
               if($row['has_rechazo_liquidacion_pin']   ==  1){
                    $all_buttons    =   'RECHAZADO';
               }else {
                    $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Liquidacion" 
                                        aria-expanded="true" data-itemplan="'.$row['itemplan'].'" data-sirope="'.$row['has_sirope_fo'].'"
                                        onclick="validarItemplanPin($(this));"><i class="fal fa-check"></i>
                                    </a>';                        
                    $btnEditPo = ' <a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" title="Editar Cotizacion" 
                                        aria-expanded="true" data-id_empresacolab="'.$row['idEmpresaColab'].'" data-itemplan="'.$row['itemplan'].'"
                                        onclick="openModalPO($(this));"><i class="fal fa-edit"></i>
                                    </a>';
                    $btnDescarga = ' <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" 
                                        download href="'.$row['ruta_evidencia'].'"><i class="fal fa-download"></i>
                                    </a>';
                    $btnRechazar = ' <a class="btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1" title="Rechazar Liquidacion" aria-expanded="true" data-itemplan="'.$row['itemplan'].'" onclick="rechazarLiquidacionPin($(this));">
                                        <i class="fal fa-times"></i>
                                    </a>';

                    $all_buttons = $btnValidar.' '.$btnEditPo.' '.$btnDescarga.' '.$btnRechazar;
               }

                array_push($listaFinal, array($all_buttons,
                (($row['has_sirope_fo']==1) ? 'SI' : 'NO'),$row['itemplan'],$row['nombrePlan'], $row['subproyectoDesc'], $row['empresaColabDesc'],$row['estadoPlanDesc'],$row['zonalDesc'],$row['codigoInversion']));
            }     
        }                                                            
        return $listaFinal;
    }

    function getTablaValidacion($itemplan, $idSubProyecto, $arrayEstadoPlan) {
        $arrayPlanobra = $this->m_utils->getPlanobraAll($itemplan, $idSubProyecto, $arrayEstadoPlan, null, ID_TIPO_PLANTA_INTERNA);
        $btnAprobar    = NULL;
        $html = '<table id="tbPlanObra" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-500">
                        <tr>
                            <th>Acción</th>  
                            <th>OT-04</th> 
                            <th>Itemplan</th>                            
                            <th>Nombre Plan</th>
                            <th>Subproyecto</th>
                            <th>EECC</th>
                            <th>Estado</th>
                            <th>Zonal</th>
                            <th>Código de Inversión</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayPlanobra as $row) {
                        $btnValidar = ' <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" title="Validar Itemplan" 
                                            aria-expanded="true" data-itemplan="'.$row['itemplan'].'"
                                            onclick="validarItemplanPin($(this));"><i class="fal fa-check"></i>
                                        </a>';
                        
                        $btnEditPo = ' <a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" title="Editar Cotizacion" 
                                            aria-expanded="true" data-id_empresacolab="'.$row['idEmpresaColab'].'" data-itemplan="'.$row['itemplan'].'"
                                            onclick="openModalPO($(this));"><i class="fal fa-edit"></i>
                                        </a>';
                        $btnDescarga = ' <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" 
                                            download href="'.$row['ruta_evidencia'].'"><i class="fal fa-download"></i>
                                        </a>';
										
                    $html .= ' <tr>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnValidar.' '.$btnEditPo.' '.$btnDescarga .'
                                        </div>
                                    </td>
                                    <td>'.$row['has_sirope_fo'].'</td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['nombrePlan'].'</td>
                                    <td>'.$row['subproyectoDesc'].'</td>
                                    <td>'.$row['empresaColabDesc'].'</td>  
                                    <td>'.$row['estadoPlanDesc'].'</td>
                                    <td>'.$row['zonalDesc'].'</td>
                                    <td>'.$row['codigoInversion'].'</td>             
                                </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function getTablaPoValidacion() {
        $data['error'] = EXIT_ERROR;
		$data['msj']      = null;
		try {
			$itemplan = $this->input->post('itemplan');

			if($itemplan == null || $itemplan == '') {
				throw new Exception('comunicarse con el programador a cargo');
			}
			
			$data['error']   = EXIT_SUCCESS;
			$tablaPo = $this->tablaPo($itemplan);

			$data['tablaPo'] = $tablaPo;
		} catch(Exception $e) {
			$data['msj'] = $e->getMessage();
		}
		echo json_encode($data);
    }

    function tablaPo($itemplan) {
        $flg_tipo_area = 2;
        $arrayDataPO = $this->m_utils->getDataPoByItemplan($itemplan, array(0), $flg_tipo_area);
        $cant = 0;
        $html = '<table id="tbPlanObra" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-500">
                        <tr>
                            <th>Nro</th>  
                            <th>Codigo Po</th>                            
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayDataPO as $row) {
                        $cant++;
                        $btnEditPo = ' <a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" data-toggle="tooltip" data-original-title="Editar Cotizacion" 
                                            aria-expanded="true" data-codigo_po="'.$row['codigo_po'].'" data-itemplan="'.$row['itemplan'].'"
                                            onclick="openModalCotizacion($(this));"><i class="fal fa-edit"></i>
                                        </a>';
                        
                        $html .= ' <tr>
                                        <td>'.$cant.'</td>
                                        <td>'.$row['codigo_po'].'</td>
                                        <td>'.$btnEditPo.'</td>           
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }
}