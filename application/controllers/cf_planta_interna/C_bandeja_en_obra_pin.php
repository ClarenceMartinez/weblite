<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_bandeja_en_obra_pin extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');
                 
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_PLANTA_INTERNA_PADRE, null, 61, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
        //    $data['tablaAprobacionPin'] = $this->getTablaAprobacion(NULL, NULL, array(ID_ESTADO_PLAN_DISENIO, ID_ESTADO_PLAN_PDT_OC));
            $data['json_bandeja'] = $this->getArrayPoAprobBaCoti($this->m_utils->getPlanobraAll(null, null, array(3), null, ID_TIPO_PLANTA_INTERNA));
            $this->load->view('vf_planta_interna/v_bandeja_en_obra_pin',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2, 'refresh');
	    }     
    }

    public function getArrayPoAprobBaCoti($listaCotiPin){
        $listaFinal = array();      
        if($listaCotiPin!=null){
            foreach($listaCotiPin as $poMat){ 

                $btnAprobar = null;
                $btnDetalle = null;
                /*
                if($poMat['idEstadoPlan'] == 2) {
                    $btnAprobar = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" 
                                    aria-expanded="true" title="Aprobar" data-itemplan="'.$poMat['itemplan'].'"
                                    onclick="openConfirmarAprobacion($(this));"><i class="fal fa-check"></i></a>';
                }*/
                $btnDetalle = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" 
                aria-expanded="true" title="Detalle" data-itemplan="'.$poMat['itemplan'].'"
                onclick="openModalDetalle($(this));"><i class="fal fa-paperclip"></i></a>';

                array_push($listaFinal, array($btnAprobar.$btnDetalle,
                    $poMat['itemplan'],$poMat['nombrePlan'], $poMat['subproyectoDesc'], $poMat['empresaColabDesc'],$poMat['estadoPlanDesc'],$poMat['zonalDesc'],$poMat['codigoInversion'], number_format($poMat['costo_unitario_mo'],2)));
            }     
        }                                                            
        return $listaFinal;
    }

    function getTablaAprobacion($itemplan, $idSubProyecto, $arrayEstadoPlan) {
        $arrayPlanobra = $this->m_utils->getPlanobraAll($itemplan, $idSubProyecto, $arrayEstadoPlan, null, ID_TIPO_PLANTA_INTERNA);
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
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayPlanobra as $row) {
                        $btnAprobar = null;
                        $btnDetalle = null;
                        if($row['idEstadoPlan'] == 2) {
                            $btnAprobar = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" 
                                            aria-expanded="true" title="Aprobar" data-itemplan="'.$row['itemplan'].'"
                                            onclick="openConfirmarAprobacion($(this));"><i class="fal fa-check"></i></a>';
                        }
                        $btnDetalle = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" 
                        aria-expanded="true" title="Detalle" data-itemplan="'.$row['itemplan'].'"
                        onclick="openModalDetalle($(this));"><i class="fal fa-paperclip"></i></a>';

                    $html .= ' <tr>
                                    <td>
                                        <div class="d-flex demo">
                                            '.$btnAprobar.' '.$btnDetalle.'
                                        </div>
                                    </td>
                                    <td>'.$row['itemplan'].'</td>
                                    <td>'.$row['nombrePlan'].'</td>
                                    <td>'.$row['subproyectoDesc'].'</td>
                                    <td>'.$row['empresaColabDesc'].'</td>  
                                    <td>'.$row['estadoPlanDesc'].'</td>
                                    <td>'.$row['zonalDesc'].'</td>
                                    <td>'.$row['codigoInversion'].'</td> 
                                    <td>'.$row['costo_unitario_mo'].'</td>             
                                </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function aprobarObraPin() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->trans_begin();
            $itemplan    = $this->input->post('itemplan');
            $idUsuario   = $this->session->userdata('idPersonaSessionPan');
            $fechaActual = $this->m_utils->fechaActual();

            if($itemplan == null || $itemplan == '') {
                throw new Exception('No se encuentra el itemplan, verificar.');
            }

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }

            $objPlanObra = array(
                'idEstadoPlan' => ID_ESTADO_PLAN_PDT_OC,
                'idUsuarioLog' => $idUsuario,
                'fechaLog'     => $fechaActual,
				'descripcion'  => 'COTIZACION APROBADA'
            );

            $data = $this->m_utils->actualizarPlanObra($itemplan, $objPlanObra);

            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $objPo = array(
                'estado_po' => ID_ESTADO_PO_APROBADO
            );

            $flg_area = 2;
            $data = $this->m_utils->actualizarPoByItemplan($itemplan, ID_ESTADO_PO_REGISTRADO, $objPo, ID_ESTACION_PIN, $flg_area, $idUsuario);
            
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $resp = $this->m_utils->fnRegistrarSolicitudOc($itemplan, NULL, $idUsuario, 'PIN');

            if($resp != 1) {
                throw new Exception('No se creo la Solicitud OC, Verificar');
            }

            $this->db->trans_commit();
        } catch(Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
}