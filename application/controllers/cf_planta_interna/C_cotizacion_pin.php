<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_cotizacion_pin extends CI_Controller {
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
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_PLANTA_INTERNA_PADRE, null, ID_COTIZACION_PIN_HIJO, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            //$data['tablaCotizacionPin'] = $this->getTablaCotizacionPin(NULL, NULL, array(1));
			$iddEECC = $this->session->userdata('idEmpresaColabSesion');
            $data['json_bandeja'] = $this->getArrayPoBaCoti($this->m_utils->getPlanobraAll(null, null, array(1), $iddEECC, ID_TIPO_PLANTA_INTERNA));
            $this->load->view('vf_planta_interna/v_cotizacion_pin',$data);        	  
    	 }else{
        	redirect(RUTA_OBRA2, 'refresh');
	    }     
    }

    public function getArrayPoBaCoti($listaCotiPin){
        $listaFinal = array();      
        if($listaCotiPin!=null){
            foreach($listaCotiPin as $poMat){ 
               
                $actions = '<a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ingresar" data-itemplan="'.$poMat['itemplan'].'"
                data-id_empresacolab="'.$poMat['idEmpresaColab'].'" onclick="openModalMaterial($(this));"><i class="fal fa-edit"></i></a>';

                array_push($listaFinal, array($actions,
                    $poMat['itemplan'],$poMat['nombrePlan'], $poMat['subproyectoDesc'], $poMat['empresaColabDesc'],$poMat['estadoPlanDesc'],$poMat['zonalDesc'],$poMat['codigoInversion']));
            }     
        }                                                            
        return $listaFinal;
    }


    function getTablaCotizacionPin($itemplan, $idSubProyecto, $arrayEstadoPlan) {		
		// log_message('error', '$iddEECC:'.$iddEECC);
        $arrayPlanobra = $this->m_utils->getPlanobraAll($itemplan, $idSubProyecto, $arrayEstadoPlan, $iddEECC, ID_TIPO_PLANTA_INTERNA);
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

                    $html .= ' <tr>
                                    <td>
                                        <div class="d-flex demo">
                                            <a class="btn btn-sm btn-outline-primary btn-icon btn-inline-block mr-1" aria-expanded="true" title="Ingresar" data-itemplan="'.$row['itemplan'].'"
                                              data-id_empresacolab="'.$row['idEmpresaColab'].'" onclick="openModalMaterial($(this));"><i class="fal fa-edit"></i></a>
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

    function getDataKitPartida() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan');
			$codigoPO = $this->input->post('codigo_po') ? $this->input->post('codigo_po') : null;
			$origen = $this->input->post('origen') ? $this->input->post('origen') : null;

            if($itemplan == null || $itemplan == '') {
                throw new Exception('itemplan no existente, comunicarse con el programador a cargo');
            }
            $data['error']   = EXIT_SUCCESS;
            $tablaKitPartida = $this->getTablaKitPartida($itemplan);
            list($tablaDetallePo, $arrayDetallePo)  = $this->tablaPoDetalleMo($itemplan,$codigoPO, $origen);
            $data['tablaKitPartida'] = $tablaKitPartida;
            $data['tablaDetallePo']  = $tablaDetallePo;
            $data['arrayDetallePo']  = $arrayDetallePo;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    function getDataKitPartidadetalle() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $itemplan = $this->input->post('itemplan');

            if($itemplan == null || $itemplan == '') {
                throw new Exception('itemplan no existente, comunicarse con el programador a cargo');
            }
            $data['error']   = EXIT_SUCCESS;
            $tablaKitPartida = $this->getTablaKitPartidadetalle($itemplan);

            $data['tablaKitPartida'] = $tablaKitPartida;
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }


    function getTablaKitPartidadetalle($itemplan) {
        $arrayKit = $this->m_utils->getKitPartidasPinByItemplandetalle($itemplan);
        $costo=null;

        $html = '<table id="tbKitPartida" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>Código</th>  
                            <th>Partida</th>                            
                            <th>Baremo</th>
                            <th>Precio</th>
                            <th>cantidad</th>
                            <th>Costo kit</th>
                           
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayKit as $row) {

                        $costo=$row['costo_total'];
                        $html .= '  <tr>
                                        <td>'.$row['codigoPartida'].'</td>
                                        <td>'.$row['descripcion'].'</td>
                                        <td>'.$row['baremo'].'</td>
                                        <td>'.$row['preciario'].'</td>
                                        <td>'.$row['cantidadFinal'].'</td>
                                        <td style="text-align: right;">'.number_format($row['montoInicial'],2).'</td>  
                                      
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>
                        <div style="text-align:right; font-weight: bold;"><strong>TOTAL: S/.</strong> <span>'.number_format($costo,2).'</span></div>';
        return $html;
    }
    
    
    function getTablaKitPartida($itemplan) {
        $arrayKit = $this->m_utils->getKitPartidasPinByItemplan($itemplan);

        $html = '<table id="tbKitPartida" class="table table-bordered table-hover table-striped w-100 table-sm">
                    <thead class="bg-primary-600">
                        <tr>
                            <th>Código</th>  
                            <th>Partida</th>                            
                            <th>Baremo</th>
                            <th>Costo kit</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';

                    foreach ($arrayKit as $row) {

                        $html .= '  <tr>
                                        <td>'.$row['codigoPartida'].'</td>
                                        <td>'.$row['nomPartida'].'</td>
                                        <td>'.$row['baremo'].'</td>
                                        <td>'.$row['costo_material'].'</td>  
                                        <td><a class="btn btn-sm btn-outline-primary btn-icon" aria-expanded="true" data-codigo_partida="'.$row['codigoPartida'].'"
                                               data-baremo="'.$row['baremo'].'" data-costo_material="'.$row['costo_material'].'" data-costo_preciario="'.$row['costoPreciario'].'" 
                                               data-nom_partida="'.$row['nomPartida'].'"
                                               title="Agregar" onclick="agregarPartida($(this));"><i class="fal fa-plus"></i></a></td>         
                                    </tr>';
                    }
                    $html .= '</tbody>
                        </table>';
        return $html;
    }

    function registrarKitPartidaPin() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $this->db->trans_begin();

            $arrayDetallePo = $this->input->post('arrayKitPartida');
            $objPoFinal     = json_decode($this->input->post('objPoFinal'), true);

            $codigoPO    = $this->m_utils->getCodigoPO($objPoFinal['itemplan']);
            $idUsuario   = $this->session->userdata('idPersonaSessionPan');
            $fechaActual = $this->m_utils->fechaActual();
            
            if($codigoPO == null || $codigoPO == '') {
                throw new Exception("No se encontro el codigo de po, comunicarse con el programador a cargo.");
            }

            if($objPoFinal['itemplan'] == null || $objPoFinal['itemplan'] == '') {
                throw new Exception("No se ecncontro el itemplan, verificar.");
            }

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }

            $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($objPoFinal['itemplan'], 'MO', ID_ESTACION_PIN);

            if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                throw new Exception("No tiene configurado un area.");
            }

            $dataPo = array (
                                'codigo_po'      => $codigoPO,
                                'itemplan'       => $objPoFinal['itemplan'],
                                'estado_po'      => ID_ESTADO_PO_REGISTRADO,
                                'idEstacion'     => ID_ESTACION_PIN,
                                'costo_total'    => $objPoFinal['total'],
                                'idUsuario'      => $idUsuario,
                                'fechaRegistro'  => $fechaActual,
                                'flg_tipo_area'  => 2,
                                'idEmpresaColab' => $objPoFinal['idEmpresaColab'],
                                'idArea'         => $dataSubEstacionArea['idArea'],
                                'idSubProyecto'  => $dataSubEstacionArea['idSubProyecto']
                            );
            
            $arrayDetalleInsert = array();
            foreach($arrayDetallePo as $row) {
                $detallePo = array (
                                    'codigo_po'        => $codigoPO,
                                    'codigoPartida'    => $row['codigoPartida'], //ESTADO REGISTRADO
                                    'baremo'           => $row['baremo'],
                                    'costoMo'          => $row['costoMO'],
                                    'costoMat'         => $row['costoMAT'],
                                    'preciario'        => $row['costoPreciario'],
                                    'cantidadInicial' => $row['cantidad'],
                                    'montoInicial'     => $row['total'],
                                    'cantidadFinal'    => $row['cantidad'],
                                    'montoFinal'       => $row['total']
                                );
                array_push($arrayDetalleInsert, $detallePo);
            }

            $dataLogPO =    array(
                                    'codigo_po'         =>  $codigoPO,
                                    'itemplan'          =>  $objPoFinal['itemplan'],
                                    'idUsuario'         =>  $idUsuario,
                                    'fecha_registro'    =>  $fechaActual,
                                    'idPoestado'        =>  ID_ESTADO_PO_REGISTRADO
                                );
            
            $data = $this->m_utils->registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO);
            
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $objPlanObra = array(
                                    'idEstadoPlan' => ID_ESTADO_PLAN_DISENIO,
                                    'idUsuarioLog' => $idUsuario,
                                    'fechaLog'     => $fechaActual,
									'descripcion'  => 'COTIZACION REALIZADA',
                                    'costo_unitario_mo_crea_oc' => $objPoFinal['total'],
                                    'costo_unitario_mo' => $objPoFinal['total']
                                );
            $data = $this->m_utils->actualizarPlanObra($objPoFinal['itemplan'], $objPlanObra);

            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
            $this->db->trans_commit();
        } catch(Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        echo json_encode($data);
    }

    function tablaPoDetalleMo($itemplan,$codigoPO, $origen) {

		$arrayEstadoPO = array(ID_ESTADO_PO_LIQUIDADO);
		if($origen == 1){
			$arrayEstadoPO = array(0);
		}
        $arrayDetalleMo = $this->m_utils->getDataPoDetalleMo($itemplan, $codigoPO, $arrayEstadoPO);

        $html = '<table class="table table-bordered table-sm">
                    <thead class="bg-primary-600">
                    <th style="font-weight: bolder; color: white;text-align: center">Partida</th>
                    <th style="font-weight: bolder; color: white;text-align: center">Precio</th>
                    <th style="font-weight: bolder; color: white;text-align: center">Baremo</th>
                    <th style="font-weight: bolder; color: white;text-align: center; max-width: 100px">Cantidad</th>
                    <th style="font-weight: bolder; color: white;text-align: center">Costo MO</th>
                    <th style="font-weight: bolder; color: white;text-align: center">Precio kit</th>
                    <th style="font-weight: bolder; color: white;text-align: center">Costo MAT</th>
                    <th style="font-weight: bolder; color: white;text-align: center">Total</th>
                    <th ></th>
                </thead>
                <tbody id="tBodyActividades">';
                    $total= 0;
                    $arrayDetallePo = array();
                    foreach ($arrayDetalleMo as $row) {
                        $total = $row['montoFinal'] + $total;

                        $objDetallePo['codigoPartida']  = $row['codigoPartida'];
                        $objDetallePo['baremo']         = $row['baremo'];
                        $objDetallePo['precioKit']      = $row['precioKit'];
                        $objDetallePo['costoPreciario'] = $row['preciario'];
                        $objDetallePo['nomPartida']     = $row['nomPartida'];
                        $objDetallePo['cantidad']       = $row['cantidadFinal'];
                        $objDetallePo['costoMO']        = $row['costoMo'];
                        $objDetallePo['costoMAT']       = $row['costoMat'];
                        $objDetallePo['total']          = $row['montoFinal'];

                        array_push($arrayDetallePo, $objDetallePo);
                        $html .= '  <tr>
                                        <td>'.$row['nomPartida'].'</td>
                                        <td id="costo'.$row['codigoPartida'].'">'.$row['preciario'].'</td>
                                        <td id="baremo'.$row['codigoPartida'].'">'.$row['baremo'].'</td>
                                        <td><input type="text" class="form-control"  id="cantidad'.$row['codigoPartida'].'" 
                                            data-codigo_partida="'.$row['codigoPartida'].'" onkeyup="calculaTotal($(this))" value="'.$row['cantidadFinal'].'"
                                            style="border-style: ridge; border-width: 4px; text-align: center"></td>
                                        
                                        <td id="totalBaremo'.$row['codigoPartida'].'">'.$row['costoMo'].'</td>
                                        <td id="precioKit'.$row['codigoPartida'].'">'.$row['precioKit'].'</td>  
                                        <td id="totalMaterial'.$row['codigoPartida'].'">'.$row['costoMat'].'</td>  
                                        <td id="total'.$row['codigoPartida'].'">'.$row['montoFinal'].'</td>';    
                        $html .=       "<td><a data-codigo_partida='".$row['codigoPartida']."' class='btn btn-sm btn-outline-danger btn-icon btn-inline-block mr-1' title='Eliminar' onclick='eliminarPartidaKit(".json_encode($row).")'><i class='fal fa-times'></i></a></td>
                                    </tr>";
                    }
                    $html .= '</tbody>
                        </table>
                        <div class="row">
                            <div class="col-md-9">
                                <button class="btn btn-success" onclick="guardarKitPartidaPin()">Guardar</button>
                            </div>
                            <div class="col-md-3">
                            <strong>TOTAL: S/.</strong> <label id="montoTotalGeneral">'.$total.'</label>
                            </div>
                        </div>';
        return array($html, $arrayDetallePo);
    }

    function actualizarKitPartidaPin() {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null;
        try {
            $this->db->trans_begin();

            $arrayDetallePo = $this->input->post('arrayKitPartida');

            $codigoPO    = $this->input->post('codigo_po');
            $idUsuario   = $this->session->userdata('idPersonaSessionPan');
            $fechaActual = $this->m_utils->fechaActual();
            
            if($codigoPO == null || $codigoPO == '') {
                throw new Exception("No se encontro el codigo de po, comunicarse con el programador a cargo.");
            }

            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }

            $arrayDetalleInsert = array();
            $totalFinal = 0;
            foreach($arrayDetallePo as $row) {
                $totalFinal = $totalFinal + $row['total'];
                $detallePo = array (
                                    'codigo_po'        => $codigoPO,
                                    'codigoPartida'    => $row['codigoPartida'], //ESTADO REGISTRADO
                                    'baremo'           => $row['baremo'],
                                    'costoMo'          => $row['costoMO'],
                                    'costoMat'         => $row['costoMAT'],
                                    'preciario'        => $row['costoPreciario'],
                                    'cantidadInicial'  => $row['cantidad'],
                                    'montoInicial'     => $row['total'],
                                    'cantidadFinal'    => $row['cantidad'],
                                    'montoFinal'       => $row['total']
                                );
                array_push($arrayDetalleInsert, $detallePo);
            }

            $data = $this->m_utils->deleteDetallePOMO($codigoPO);

            if($data['error'] == EXIT_ERROR){
                throw new Exception($data['msj']);
            }

            $data = $this->m_utils->registrarDetallePoMo($arrayDetalleInsert);
            
            if($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }

            $objPo = array('costo_total' => $totalFinal);
            $data = $this->m_utils->actualizarPoByCodigo($codigoPO, $objPo);

            if($data['error'] == EXIT_ERROR) {
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
