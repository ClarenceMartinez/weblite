<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_consulta_evidencias extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_consulta/m_consulta_evidencias');
        $this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
        $this->load->model('mf_consulta/m_detalle_consulta');
        $this->load->library('lib_utils');
        $this->load->library('zip');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
	    if($idUsuario != null){
            $permisos =  $this->session->userdata('permisosArbolPan');   
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_GESTION_OBRA_PADRE, null, 60, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $data['json_bandeja'] = $this->getArrayPoBaVal(/*$this->m_consulta_evidencias->getConsultaEvidencias(null, null)*/null);
            $data['cmbProyecto'] = __buildProyectoAll(NULL, NULL);            
            $data['cmbEstadoPlan']  = __buildComboEstadoPlan();
           // $data['tablaValidacionObra'] = $this->getTablaConsulta(null, $idUsuario, null);
		//	$data['modulosTopFlotante'] = _getModulosFlotante();
            $this->load->view('vf_consulta/v_consulta_evidencias',$data);        	  
    	 }else{
        	 redirect(RUTA_OBRA2,'refresh');
	    }     
    }

    public function getArrayPoBaVal($listaCotiB2b){
        $listaFinal = array();      
        if($listaCotiB2b!=null){
            foreach($listaCotiB2b as $row){  
                $files = '<a style="color: #E51318" onclick=disenho("' . $row['itemplan'] . '")>Expe_Diseno&nbsp;&nbsp</a><br>
                            <a style="color: #954b97" onclick=licencias("' . $row['itemplan'] . '")>Licencia&nbsp;&nbsp</a><br>
                            <a style="color: #5bc500" onclick=liquidacion("' . $row['itemplan'] . '")>Liquida_obra&nbsp;&nbsp</a><br>
                            <a style="color: #121311" onclick=expedienteLiqui("' . $row['itemplan'] . '","")>Expe_Termino&nbsp</a>';
							
			 $detallePartidas = '<a class="btn btn-sm btn-outline-success btn-icon btn-inline-block mr-1" aria-expanded="true" title="Visualizar Partidas" data-itemplan="'.$row['itemplan'].'"
						onclick="openModalDetallePartidasAll(this)">
						<i class="fal fa-eye"></i>
					</a>';
                 array_push($listaFinal, array($files,$detallePartidas,
                    $row['itemplan'],$row['proyectoDesc'], $row['subProyectoDesc'], $row['indicador'], $row['empresaColabDesc'],$row['jefaturaDesc'],$row['cantFactorPlanificado'], $row['estadoplanDesc']));
            }     
        }                                                            
        return $listaFinal;
    } 

    public function filtrarTabla()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$itemplan       = $this->input->post('cod_obra')      ? $this->input->post('cod_obra') : null;
            $idEstadoPlan   = $this->input->post('idEstadoPlan')  ? $this->input->post('idEstadoPlan') : null; 
            $idProyecto     = $this->input->post('idProyecto')    ? $this->input->post('idProyecto') : null;     
			if($itemplan == null &&  $idEstadoPlan == null && $idProyecto == null){
				throw new Exception('Debe ingresar mínimo un filtro para buscar!!');
			}
            $data['json_bandeja'] = $this->getArrayPoBaVal($this->m_consulta_evidencias->getConsultaEvidencias($itemplan, $idProyecto, $idEstadoPlan));
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
 
	function getPartidasPaquetizadas(){
        $data['error'] = EXIT_ERROR;
        $data['html'] = '';
        try{
          
            $itemplan       = $this->input->post('cod_obra')      ? $this->input->post('cod_obra') : null;
            if($itemplan == null){
				throw new Exception('No se detecto Itemplan!!');
			}

            $allPartidas = $this->m_consulta_evidencias->getAllPartidasMoByItemplan($itemplan);
            $html = '<table id="tbPartidasAll" class="table table-bordered table-hover table-striped w-100">
                        <thead class="bg-primary-600">
                            <tr> 
                                <th>ESTACION</th>
                                <th>CODIGO PO</th>
                                <th>ESTADO PO</th>
                                <th>CÓDIGO PARTIDA</th>
                                <th>NOMBRE PARTIDA</th>                            
                                <th>BAREMO</th>                               
                                <th>PRECIO</th>
                                <th>CANTIDAD</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>';                          
                    foreach($allPartidas as $partida){
                          $html .= '<tr>      
                                        <td>'.$partida['estacionDesc'].'</td>
                                        <td>'.$partida['codigo_po'].'</td>
                                        <td>'.$partida['estado'].'</td>
                                        <td>'.$partida['codigoPartida'].'</td>
                                        <td>'.$partida['descripcion'].'</td>
                                        <td>'.$partida['baremo'].'</td>
                                        <td>'.$partida['preciario'].'</td>
                                        <td>'.$partida['cantidadFinal'].'</td>
                                        <td>'.number_format($partida['montoFinal'],2).'</td>
                                    </tr>';
                    }
            $html   .=  '</tbody>                        
                    </table> ';
 
            $data['html'] = $html;
            $data['error'] = EXIT_SUCCESS;        
        }catch(Exception $e){
            $data['html'] = $e->getMessage();
        }

        echo json_encode($data);
    }
}
