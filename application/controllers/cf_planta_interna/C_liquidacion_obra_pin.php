<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_liquidacion_obra_pin extends CI_Controller {
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_PLANTA_INTERNA_PADRE, null, ID_LIQUIDACION_PIN_HIJO, ID_MODULO_DESPLIEGUE_PLANTA);
            
            $data['opciones'] = $result['html'];
            $data['tablaLiquidacionPin'] = $this->getTablaLiquidacion(NULL, NULL, array(ID_ESTADO_PLAN_EN_OBRA));
            $this->load->view('vf_planta_interna/v_liquidacion_obra_pin',$data);        	  
    	 }else{
        	redirect(RUTA_OBRA2, 'refresh');
	    }     
    }

    function getTablaLiquidacion($itemplan, $idSubProyecto, $arrayEstadoPlan) {
        $arrayPlanobra = $this->m_utils->getPlanobraAll($itemplan, $idSubProyecto, $arrayEstadoPlan, null, ID_TIPO_PLANTA_INTERNA);
        $btnAprobar    = NULL;
        $html = '<table id="tbPlanObra" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-info-500">
                        <tr>
                            <th>Acción</th>  
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
                        $btnDescarga = null;
                        $btnLiquidar = '<a class="btn btn-sm btn-outline-primary" 
                                        title="Liquidar Obra" data-itemplan="'.$row['itemplan'].'"
                                        onclick="openMdlLiquidarObraPin($(this));"><i class="fal fa-check"></i></a>';
                        
						if($row['ruta_evidencia'] != null) {
							$btnDescarga = ' <a class="btn btn-sm btn-outline-info btn-icon btn-inline-block mr-1" title="Descargar Evidencia" 
												download href="'.$row['ruta_evidencia'].'"><i class="fal fa-download"></i>
											</a>';
						}
						
                    $html .= ' <tr>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnLiquidar.' '.$btnDescarga.'
                                        </div>
                                    </td>
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

    function openMdlLiquidarObraPin() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan');

            if($itemplan == null || $itemplan == '') {
                throw new Exception('comunicarse con el programador a cargo');
            }
            
            $data['error']   = EXIT_SUCCESS;
            $TabVerticalEstacion = $this->getTabVerticalEstacion($itemplan);

            $data['TabVerticalEstacion'] = $TabVerticalEstacion;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getTabVerticalEstacion($itemplan) {
        $dataSubEstacion = $this->m_utils->getSubProyectoEstaciosByItemplan($itemplan);
        $tab = null;
        $tabContent = null;
        $tab = '<div class="col-auto">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
                    //show active
        $cont = 0;
        foreach($dataSubEstacion as $row) {
            $active = null;
            if($cont == 0) {
                $active = 'active';
            }
            $tab .= '   <a class="nav-link '.$active.'" id="atab_'.$row['idEstacion'].'"  data-toggle="pill" href="#tab_'.$row['idEstacion'].'" role="tab" aria-controls="#tab_'.$row['idEstacion'].'" aria-selected="true">     
                            <span class="hidden-sm-down ml-1">'.$row['estacionDesc'].'</span>
                        </a>';

            $tabContent .= ' <div class="tab-pane fade show '.$active.'" id="tab_'.$row['idEstacion'].'" role="tabpanel" aria-labelledby="atab_'.$row['idEstacion'].'">
                                <h3>
                                    '.$row['estacionDesc'].'
                                </h3>
                                <div class="form-group">
                                    <label class="form-label" for="inputGroupFile01">Subir Evidencia</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="evidencia" aria-describedby="evidencia4" onclick="getEstacion('.$row['idEstacion'].');">
                                            <label class="custom-file-label" for="evidencia"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            $cont++;
        }
        $tab .= '    </div>
                </div>
                <div class="col">
                    <div class="tab-content" id="v-pills-tabContent">
                        '.$tabContent.'
                    </div>
                </div>';
        return $tab;
    }
}