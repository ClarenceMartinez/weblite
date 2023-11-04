<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_consultaZip extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=ISO-8859-1');
     //   $this->load->model('mf_plan_obra/m_consulta');
        $this->load->model('mf_consultasZip/M_consultasZip', 'm_consultaszip');
      //  $this->load->model('mf_pqt_plan_obra/m_pqt_consulta');
        $this->load->model('mf_utils/m_utils');
        //$this->load->model('mf_log/m_log_ingfix');
        $this->load->library('lib_utils');
        $this->load->helper('url');
        $this->load->library('zip');
    }

    public function index() {
        $logedUser = $this->session->userdata('usernameSession');
        if ($logedUser != null) {
            //$data['listaItemplan'] = $this->m_utils->getAllItemplan();
            $data['listaNombres'] = $this->m_utils->getAllNombreDeProyectos();
            $data['listaEECC'] = $this->m_utils->getAllEECC();
            $data['listaNodos'] = $this->m_utils->getAllNodos();
            $data['listaProyectos'] = '';
            $data['listaEstados'] = $this->m_utils->getEstadosItemplan();
            $data['listaTipoPlanta'] = $this->m_utils->getAllTipoPlantal();
            $data['listafase'] = $this->m_utils->getAllFase();

            // Trayendo zonas permitidas al usuario
            $zonas = $this->session->userdata('zonasSession');
            $data['listaZonal'] = $this->m_utils->getAllZonal();
            $data['listaSubProy'] = '';
            $data['tablaAsigGrafo'] = '';
            $data['tablaAsigGrafo'] = $this->makeHTLMTablaConsulta('');
            $data['nombreUsuario'] = $this->session->userdata('usernameSession');
            $data['perfilUsuario'] = $this->session->userdata('descPerfilSession');
            $permisos = $this->session->userdata('permisosArbol');
            $result = $this->lib_utils->getHTMLPermisos($permisos, ID_PERMISO_PADRE_PLAN_DE_OBRA, ID_PERMISO_HIJO_CONSULTAS);
            $data['opciones'] = $result['html'];

            $this->load->view('vf_consultaZip/v_consultaZip', $data);
        } else {

            redirect('login', 'refresh');
        }
    }

    public function asignarExpediente() {
        $logedUser = $this->session->userdata('usernameSession');

        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {

            $jsonptr = $this->input->post('jsonptr');
            $comentario = $this->input->post('comentario');

            $arrayPTRItem = json_decode($jsonptr, true);
            //log_message('error', 'loged user es: '.$logedUser);
            $data = $this->m_consulta->insertExpediente($comentario, $logedUser);

            foreach ($arrayPTRItem as $row) {
                $subrows = explode("%", $row);
                $ptr = $subrows[0];
                $item = (($subrows[1] != null) ? $subrows[1] : null);
                $fecsol = $subrows[2];
                $subproyecto = $subrows[3];
                $zonal = $subrows[4];
                $eecc = $subrows[5];
                $area = $subrows[6];

                //aquir recibir en una variable la wu.f_ult_est para enviar al insert()
                /*
                  log_message('error', '===============================================================');

                  log_message('error', '-->ptr recibida en controllador es : '.$ptr);
                  log_message('error', '-->itemplan decodificado es : '.$item);
                  log_message('error', '-->comentario recibido en controllador es : '.$comentario);
                  log_message('error', '-->fecsol recibido en controllador es : '.$fecsol);

                  log_message('error', '-->subproyecto decodificado es : '.$subproyecto);
                  log_message('error', '-->zonal recibido en controllador es : '.$zonal);
                  log_message('error', '-->eecc recibido en controllador es : '.$eecc);
                  log_message('error', '-->area recibido en controllador es : '.$area);
                  log_message('error', '==============================================================='); */
                $this->m_consulta->insertPTR($ptr, $item, $fecsol, $subproyecto, $zonal, $eecc, $area);
            }
            //  log_message('error', 'fin foreach ');
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function makeHTLMTablaConsulta($listaPTR) {

        $html = '<table id="data-table" class="table table-bordered" style="width:100%">
                    <thead class="thead-default">
                        <tr>
                            <th style="width:100%">DESCARGA</th>
                            <th>ITEMPLAN</th>
                            <th>SUBPROYECTO</th>
                            <th>NOMBRE</th>
                            <th>MDF/NODO</th>
                            <th>ZONAL</th>
                            <th>EECC</th>
                            <th>A&Ntilde;O</th>
                            <th>FEC. INICIO</th>
                            <th>FEC. PREV. EJECUCION</th>
                            <th>FEC LIQUIDA.</th>
                            <th>ESTADO</th>
                            <th>PRIORIZADO</th>
                            <th>BANDEJA SIOM</th>
                        </tr>
                    </thead>

                    <tbody>';
        if ($listaPTR != '') {
            foreach ($listaPTR->result() as $row) {

                $flg_bandeja_siom = 'NO';
                if ($row->flg_bandeja_siom == 1) {
                    $flg_bandeja_siom = 'SI';
                }



                $html .= '
                        <tr>
                            <td>
                                <div>
                                <a style="color: #005C84" onclick=cotizacion("' . $row->itemPlan . '")>Cotizacion&nbsp;&nbsp</a>
                                <a style="color: #E51318" onclick=disenho("' . $row->itemPlan . '")>Expe_Diseno&nbsp;&nbsp</a>
                                <a style="color: #954b97" onclick=licencias("' . $row->itemPlan . '")>Licencia&nbsp;&nbsp</a>
                                <a style="color: #5bc500" onclick=liquidacion("' . $row->itemPlan . '")>Liquida_obra&nbsp;&nbsp</a>
								<a style="color: #121311" onclick=expedienteLiqui("' . $row->itemPlan . '","")>Expe_Termino&nbsp</a> 
                                </div>
                            </td>
                            <td>' . $row->itemPlan . '</td>
                            <td>' . $row->subProyectoDesc . '</td>
                            <td>' . $row->nombreProyecto . '</td>
                            <td>' . $row->codigo . '-' . $row->tipoCentralDesc . '</td>
                            <td>' . $row->zonalDesc . '</td>
                            <td>' . $row->empresaColabDesc . '</td>
                            <td>' . $row->faseDesc . '</td>
                            <th>' . $row->fechaInicio . '</th>
                            <th>' . $row->fechaPrevEjec . '</th>
                            <th>' . $row->fechaEjecucion . '</th>
                            <th>' . $row->estadoPlanDesc . '</th>
                            <th style="text-align: center;' . (($row->hasAdelanto == '1') ? 'color: GREEN;font-size: large' : 'color: currentColor;font-size: initial ') . ';font-weight: bold;">' . (($row->hasAdelanto == '1') ? 'SI' : 'NO') . '</th>
                            <th>' . $flg_bandeja_siom . '</th>


                        </tr>
                        ';
            }
            $html .= '</tbody>
                </table>';
        } else {
            $html .= '</tbody>
                </table>';
        }

        return utf8_decode($html);
    }

    public function cotizacion() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $filename = $this->input->post('itemPlan');
            $pat = $this->m_consultaszip->getCluster($filename);
            log_message('error', $pat);
            if ($pat !== 0) {
                $path = 'uploads/sisego/cotizacion_individual/' . $pat . '/';
                log_message('error', $path);
                if (file_exists($path)) {
                    $data['path'] = 1;
                } else {
                    $data['path'] = 2;
                }
            } else {
                $data['path'] = 2;
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function cotizacion_download() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $pat = $this->m_consultaszip->getCluster($filename);
        log_message('error', $filename);
        $path = 'uploads/sisego/cotizacion_individual/' . $pat . '/';
        $this->zip->read_dir($path, false);
        $this->zip->download($filename . '.zip');
    }

    public function licencias() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $filename = $this->input->post('itemPlan');
            $path = $this->m_consultaszip->getLicencias($filename);
//            log_message('error', 'itemPlan: ' . $filename);
//            log_message('error', 'count: ' . count($path));
            log_message('error', print_r($path, true));
            if (count($path) == 0) {
                $data['path'] = 2;
            } else {
                $data['path'] = 1;
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function licencias_download() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $dataArray = array();
        $path = $this->m_consultaszip->getLicencias($filename);
        foreach ($path as $row) {
//            $dataArray[] = $row['ruta_pdf'];
            $this->zip->read_file($row['ruta_pdf']);
        }
        log_message('error', print_r($dataArray, true));
        $this->zip->download($filename . '.zip');
    }

    public function disenho() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $filename = $this->input->post('itemPlan');
            $path = 'uploads/expedientes_diseno/' . $filename . '/';
            log_message('error', $path);
            if (file_exists($path)) {
                $data['path'] = 1;
            } else {
                $data['path'] = 2;
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function disenho_download() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $path = 'uploads/expedientes_diseno/' . $filename . '/';
        $this->zip->read_dir($path, false);
        $this->zip->download($filename . '.zip');
    }

    public function liquidacion() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $filename = $this->input->post('itemPlan');
            $path = 'uploads/evidencia_liquidacion/' . $filename . '/';
            log_message('error', file_exists($path));
            if (file_exists($path)) {
                $data['path'] = 1;
            } else {
                $data['path'] = 2;
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function liquidacion_download() {
        $filename = (isset($_GET['itemPlan']) ? $_GET['itemPlan'] : '');
        $path = 'uploads/evidencia_liquidacion/' . $filename . '/';
        $this->zip->read_dir($path, false);
        $this->zip->download($filename . '.zip');
    }

    function validarItemplanPerteneceAPaquetizado() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            _log("ENTRO AL LOG");
            $itemPlan = $this->input->post('itemplan');
            $indicador = $this->input->post('nombreproyecto');

            if (!$itemPlan) {
                $itemPlan = null;
            }

            if (!$indicador) {
                $indicador = null;
            }

            _log("ENTRO - SISEGO :" . $indicador);
            $dato = $this->m_pqt_consulta->isContratoPaquetizadoItemPlan($itemPlan, $indicador);
            //SI EL VALOR DE COUNT ES 0 --> PERMITIR CONSULTAR
            //SI EL VALOR DE COUNT ES MAYOR IGUAL A 1 --> NO PERMITIR CONSULTAR
            $data['permitir'] = $dato['count'];
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function filtrarTabla() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {

            //$zonas = $this->session->userdata('zonasSession');
            $itemPlan = $this->input->post('itemplan');
            $nombreproyecto = $this->input->post('nombreproyecto');
            $nodo = $this->input->post('nodo');
            $zonal = $this->input->post('zonal');
            $proy = $this->input->post('proy');
            $subProy = $this->input->post('subProy');
            $estado = $this->input->post('estado');
            $tipoPlanta = $this->input->post('tipoPlanta');
            //$selectMesPrevEjec = $this->input->post('selectMesPrevEjec');
            $filtroPrevEjec = $this->input->post('filtroPrevEjec');
            $idFase = $this->input->post('idFase');
            $idEECC = $this->session->userdata("eeccSession");
            $permitir = $this->input->post("permitir");
            //
            log_message('error', 'permitir: '.$permitir);
            if ($idEECC == null || $idEECC == '') {
                throw new Exception("Su sesi&oacute;n a caducado, actualizar  la p&aacute;gina");
            }

            // log_message('error',' -->EL FILTRO RECIBIDO SERIA: '.$filtroPrevEjec);
            //log_message('error', '-->datos enviados al modelo son : itemplan '.$itemPlan.' , nombreproyecto'.$nombreproyecto.' , nodo es '.$nodo.' , zonal es '.$zonal.' , proyecto es '.$proy.' , subproyecto es '.$subProy.' ,estado es :'.$estado.' , selectMesPrevEejec es '.$selectMesPrevEjec);
            //$estado = $this->input->post('estado');
            if ($permitir === "0") {
                $data['tablaAsigGrafo'] = $this->makeHTLMTablaConsulta($this->m_consultaszip->getPtrConsultaPqt($itemPlan, $nombreproyecto, $proy, $subProy, $tipoPlanta,$idEECC, null, null, $idFase));

            } else {
               $data['tablaAsigGrafo'] = $this->makeHTLMTablaConsulta($this->m_consultaszip->getPtrConsultaNoPqt($itemPlan, $nombreproyecto, $nodo, $zonal, $proy, $subProy, $estado, $filtroPrevEjec, $tipoPlanta, $idEECC, null, null, $idFase));

             
            }
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    /*     * **********************LOG SISTEMA************************* */

    public function mostrarLogItemPlanConsulta() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {

            $itemPlan = $this->input->post('itemplan');

            $this->session->set_flashdata('itemPlan', $itemPlan);

            // log_message('error',' ejecuto dato1');

            $data['listaLog'] = $this->getHTMLTabsLog();

            // log_message('error',' ejecuto dato');

            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function getHTMLTabsLog() {

        $planobralog = $this->getAllDataLogPlanObra2();
        //$ptrlog = $this->getAllDataLogPTR();
        $porcentajelog = $this->getAllDataLogPorcentajeAvance();
        //$poVRlog = $this->getAllDataLogPOVR();

        $html = '';
        $htmlTabCoti = '';
        $htmlCotizacion = '';
        $flgCotizacion = $this->m_log_ingfix->getFlgCotizacion($this->session->flashdata('itemPlan'));

        if ($flgCotizacion == '1') {

            $htmlTabCoti = '<li class="nav-item">
                                    <a class="nav-link " data-toggle="tab" href="#tabCoti" role="tab">COTIZACION</a>
                            </li>';

            $arrayCotizacion = $this->m_log_ingfix->getDetalleCotizacion($this->session->flashdata('itemPlan'));
            $htmlCotizacion .= ' <table id="table-cotizacion" class="table table-bordered">
                                    <thead class="thead-default">
                                        <tr>
                                        <th style="font-size:12px;">ESTADO</th>
                                        <th style="font-size:12px;">FECHA</th>
                                        <th style="font-size:12px;">USUARIO.</th>
                                        <th style="font-size:12px;">PDF</th>
                                        <th style="font-size:12px;">MONTO</th>
                                    </tr>
                                    </thead>
                                    <tbody>';


            $htmlCotizacion .= '<tr>
                                                    <th>COTIZACION SOLICITADA</th>
                                                    <td>' . $arrayCotizacion['fecha_creacion'] . '</td>
                                                    <td>' . $arrayCotizacion['usuario_registra'] . '</td>
                                                    <td style="text-align:center">
                                                        <a href="' . $arrayCotizacion['path_pdf_to_cotiza'] . '" download><i class="zmdi zmdi-hc-2x zmdi-collection-pdf"></i></a>
                                                    </td>
                                                    <td>-</td>
                                            </tr>';

            if ($arrayCotizacion['confirma_recep'] == 1) {
                $htmlCotizacion .= '<tr>                        
                                                    <th>SOLICITUD RECEPCIONADA</th>
                                                    <td>' . $arrayCotizacion['fecha_confirma_recep'] . '</td>
                                                    <td>' . $arrayCotizacion['usuario_confirma_recep'] . '</td>
                                                    <td style="text-align:center">
                                                        -
                                                    </td>
                                                    <td>-</td>
                                            </tr>';
            }
            if ($arrayCotizacion['fecha_registro_pdf'] != null) {
                $htmlCotizacion .= '<tr>
                                                    <th>COTIZACION ELABORADA</th>
                                                    <td>' . $arrayCotizacion['fecha_registro_pdf'] . '</td>
                                                    <td>' . $arrayCotizacion['usuario_registro_pdf'] . '</td>
                                                    <td style="text-align:center">
                                                        <a href="' . $arrayCotizacion['ruta_pdf'] . '" download><i class="zmdi zmdi-hc-2x zmdi-collection-pdf"></i></a>
                                                    </td>
                                                    <td>' . number_format($arrayCotizacion['monto'], 2) . '</td>
                                            </tr>';
            }
            if ($arrayCotizacion['fecha_envio_cotizacion'] != null && $arrayCotizacion['estado'] >= 3) {
                $htmlCotizacion .= '<tr>
                                                    <th>COTIZACION ENVIADA</th>
                                                    <td>' . $arrayCotizacion['fecha_envio_cotizacion'] . '</td>
                                                    <td>' . $arrayCotizacion['usuario_envio_cotizacion'] . '</td>
                                                    <td style="text-align:center">
                                                         -
                                                    </td>
                                                    <td>' . number_format($arrayCotizacion['monto'], 2) . '</td>
                                            </tr>';
            }

            if ($arrayCotizacion['fecha_aprueba_cotizacion'] != null && $arrayCotizacion['estado'] >= 4) {
                if ($arrayCotizacion['estado'] == 4) {
                    $estadoCot = 'COTIZACION APROBADA';
                } else if ($arrayCotizacion['estado'] == 5) {
                    $estadoCot = 'COTIZACION DEVUELTA';
                } else if ($arrayCotizacion['estado'] == 6) {
                    $estadoCot = 'COTIZACION RECHAZADA';
                }
                $htmlCotizacion .= '<tr>
                                                    <th>' . $estadoCot . '</th>
                                                    <td>' . $arrayCotizacion['fecha_aprueba_cotizacion'] . '</td>
                                                    <td>' . $arrayCotizacion['usua_aprueba_cotizacion'] . '</td>
                                                    <td style="text-align:center">
                                                         -
                                                    </td>
                                                    <td>' . number_format($arrayCotizacion['monto'], 2) . '</td>
                                            </tr>';
            }


            $htmlCotizacion .= '</tbody>
                            </table>';
        }


        $html0 = '<div class="tab-container">
                            <ul class="nav nav-tabs nav-fill" role="tablist">
                                <li class="nav-item">
                                     <a class="nav-link" data-toggle="tab" href="#tabDetallePO" role="tab">DETALLE IP</a>
                                </li>
                                <li class="nav-item">
                                     <a class="nav-link active" data-toggle="tab" href="#tabPlanObra" role="tab">PLAN DE OBRA</a>
                                </li>
                                <li class="nav-item">
                                     <a class="nav-link " data-toggle="tab" href="#tabAvance" role="tab">% AVANCE</a>
                                </li>
                                ' . $htmlTabCoti . '
                            </ul>

                        <div class="tab-content">';

        $arrayDetIP = $this->m_log_ingfix->getDetalleIP($this->session->flashdata('itemPlan'));


        $htmlP = '<div class="tab-pane fade show" id="tabDetallePO" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label>FECHA DE CREACION</label>
                                    <input id="fechaCreacion" type="text" class="form-control" value="' . $arrayDetIP['fecha_creacion'] . '" disabled>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label>FECHA DE INICIO</label>
                                    <input id="fechaInicio" type="text" class="form-control" value="' . $arrayDetIP['fechaInicio'] . '" disabled>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label>FECHA PREV. EJEC.</label>
                                    <input id="fechaPrevEjec" type="text" class="form-control" value="' . $arrayDetIP['fechaPrevEjec'] . '" disabled>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label>FECHA TERMINO</label>
                                    <input id="fechaEjecucion" type="text" class="form-control" value="' . $arrayDetIP['fechaEjecucion'] . '" disabled>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label>FECHA PRE LIQUIDACION</label>
                                    <input id="fechaPreliquidacion" type="text" class="form-control" value="' . $arrayDetIP['fechaPreliquidacion'] . '" disabled>
                                </div>
                            </div>
                        </div>
                  </div>';

        $htmlA = '<div class="tab-pane active fade show" id="tabPlanObra" role="tabpanel">
                                  <div class="row" style="overflow-y: scroll;height: 300px;">' . $planobralog . '</div>
                        </div>';

        // $htmlB = '<div class="tab-pane  fade show" id="tabPTR" role="tabpanel">
        //                           <div class="row" style="overflow-y: scroll;height: 300px;">' . $ptrlog . '</div>
        //                 </div>';

        $htmlC = '<div class="tab-pane  fade show" id="tabAvance" role="tabpanel">
                                  <div class="row" style="overflow-y: scroll;height: 300px;">' . $porcentajelog . '</div>
                        </div>';

        // $htmlD = '<div class="tab-pane  fade show" id="tabVR" role="tabpanel">
        //                 <div class="row" style="overflow-y: scroll;height: 300px;">' . $poVRlog . '</div>
        //              </div>';

        $htmlE = '<div class="tab-pane fade show" id="tabCoti" role="tabpanel">
                        <div class="row" style="overflow-y: scroll;height: 300px;">' . $htmlCotizacion . '</div>
                    </div>';

        $html = $html0 . $htmlP . $htmlA . $htmlC . $htmlE . '</div></div>';

        return $html;
    }

    public function getAllDataLogPlanObra() {

        try {

            $logPreRegistro = $this->m_log_ingfix->getPreRegistroLog($this->session->flashdata('itemPlan'));
            $logPreDiseno = $this->m_log_ingfix->getPreDisenoLog($this->session->flashdata('itemPlan'));
            $logPredisenoUpdate = $this->m_log_ingfix->getPreDisenoUpdateLog($this->session->flashdata('itemPlan'));
            $logDisenoUpdate = $this->m_log_ingfix->getDisenoUpdateLog($this->session->flashdata('itemPlan'));

            $logDiseno = $this->m_log_ingfix->getDisenoLog($this->session->flashdata('itemPlan'));
            $logDisenoEjecutado = $this->m_log_ingfix->getDisenoEjecutadoLog($this->session->flashdata('itemPlan'));
            $logDisenoParcial = $this->m_log_ingfix->getDisenoParcialLog($this->session->flashdata('itemPlan'));

            $logEnObra = $this->m_log_ingfix->getEnObraLog($this->session->flashdata('itemPlan'));
            $logPreLiquidacion = $this->m_log_ingfix->getPreLiquidacionLog($this->session->flashdata('itemPlan'));
            $logTerminado = $this->m_log_ingfix->getTerminadoLog($this->session->flashdata('itemPlan'));

            $logTrunco = $this->m_log_ingfix->getTruncoLog($this->session->flashdata('itemPlan'));
            $logCancelado = $this->m_log_ingfix->getCanceladoLog($this->session->flashdata('itemPlan'));
            $logCerrado = $this->m_log_ingfix->getCerradoLog($this->session->flashdata('itemPlan'));

            $logFichaTecnicaReg = $this->m_log_ingfix->getFichaTecnicaLogReg($this->session->flashdata('itemPlan'));
            $logFichaTecnicaVal = $this->m_log_ingfix->getFichaTecnicaLogVal($this->session->flashdata('itemPlan'));

            $logCertificacion = $this->m_log_ingfix->getCertificacionLog($this->session->flashdata('itemPlan'));

            $htmlCabcer = '
                             <table id="data-tablePO"  class="table table-bordered"  style="font-size: smaller;">
                                <thead class="thead-default">
                                    <tr>
                                       <th onclick="w3.sortHTML(' . "'" . '#data-tablePO' . "'" . ', ' . "'" . '.item' . "'" . ', ' . "'" . 'td:nth-child(3)' . "'" . ')" style="cursor:pointer">ACCION <i class="zmdi zmdi-unfold-more"></i></th>

                                         <th onclick="w3.sortHTML(' . "'" . '#data-tablePO' . "'" . ', ' . "'" . '.item' . "'" . ', ' . "'" . 'td:nth-child(2)' . "'" . ')" style="cursor:pointer">ESTADO <i class="zmdi zmdi-unfold-more"></i></th>

                                        <th>USUARIO</th>

                                         <th onclick="w3.sortHTML(' . "'" . '#data-tablePO' . "'" . ', ' . "'" . '.item' . "'" . ', ' . "'" . 'td:nth-child(1)' . "'" . ')" style="cursor:pointer">FECHA REGISTRO <i class="zmdi zmdi-unfold-more"></i></th>

                                        <th>OBSERVACIONES</th>
                                    </tr>
                                </thead>
                                <tbody>';

            $htmlPreReg = "";
            $htmlPreDiseno = "";
            $htmlPreDisenoUpdate = "";
            $htmlDisenoUpdate = "";
            $htmlDiseno = "";
            $htmlDisenoEjecu = "";
            $htmlDisenoParcial = "";

            $htmlFichaTecnicaReg = "";
            $htmlFichaTecnicaVal = "";
            $htmlCertificacion = "";

            $htmlEnObra = "";
            $htmlPreLiquida = "";
            $htmlTerminado = "";
            $htmlTrunco = "";
            $htmlCancelado = "";
            $htmlCerrado = "";

            /*

              $arrayLog1 = array();
              $arrayLog2 = array();
              $arrayLog3 = array();
              $arrayLog4 = array();
              $arrayLog5 = array();
              $arrayLog6 = array();
              $arrayLog7 = array();
              $arrayLog8 = array();
              $arrayLog9 = array();
              $arrayLog10 = array();
              $arrayLog11 = array();
              $arrayLog12 = array();
              $arrayLog13 = array();

              if(count($logPreRegistro->result()) >0){
              foreach($logPreRegistro->result() as $row){
              array_push($arrayLog1,$row->fecha_creacion, $row->registro,$row->estado,$row->usua_crea,'');
              }
              }

              if(count($logPreDiseno->result()) >0){
              foreach($logPreDiseno->result() as $row){
              array_push($arrayLog2,$row->fecha_creacion, $row->registro, $row->estado,$row->nombre,'');
              }
              }

              if(count($logPredisenoUpdate->result()) >0){
              foreach($logPredisenoUpdate->result() as $row){
              array_push($arrayLog3, $row->fecha_creacion,'REGISTRO',$row->estado,$row->nombre,
              'RETORNO A PREDISEÑO PARA REASIGNACION DE DISEÑO');

              }
              }

              if(count($logDisenoUpdate->result()) >0){
              foreach($logDisenoUpdate->result() as $row){
              array_push($arrayLog4,$row->fecha_registro, strtoupper($row->estado),  $row->estado,$row->nombre,
              'RETORNO A DISEÑO PARA MODIFICACION');
              }
              }

              if(count($logDiseno->result()) >0){
              foreach($logDisenoUpdate->result() as $row){
              array_push($arrayLog5,$row->fecha_adjudicacion, $row->registro,  $row->estado,$row->usuario_adjudicacion,
              'ADJUDICACION EN LA ESTACION '.$row->estacionDesc);
              }
              }

              if(count($logDisenoEjecutado->result()) >0){
              foreach($logDisenoEjecutado->result() as $row){
              array_push($arrayLog6,$row->fecha_ejecucion, $row->registro, $row->estado,$row->usuario_ejecucion,
              'EJECUCION EN LA ESTACION '.$row->estacionDesc);
              }
              }

              if(count($logDisenoParcial->result()) >0){
              foreach($logDisenoParcial->result() as $row){
              array_push($arrayLog7, $row->fecha_registro, $row->registro,  $row->estado,$row->nombre,
              '');
              }
              }

              if(count($logEnObra->result()) >0){
              foreach($logEnObra->result() as $row){
              array_push($arrayLog8, $row->fecha_registro, $row->registro, $row->estado,$row->nombre,
              '');
              }
              }

              if(count($logPreLiquidacion->result()) >0){

              foreach($logPreLiquidacion->result() as $row){
              $fecha=$row->fecha_registro;

              if($this->validaFechaTermino($fecha)){
              array_push($arrayLog9,$row->fecha_registro, $row->registro,  $row->estado,$row->nombre,
              '');
              }else{
              array_push($arrayLog9, $row->fecha_registro,$row->registro, 'Terminado',$row->nombre,
              '');
              }
              }
              }

              if(count($logTerminado->result()) >0){

              foreach($logTerminado->result() as $row){
              array_push($arrayLog10,$row->fecha_registro, $row->registro,  $row->estado,$row->nombre,
              '');
              }

              }

              if(count($logTrunco->result()) >0){

              foreach($logTrunco->result() as $row){

              $logMotivoTrunco= $this->m_log_ingfix->getMotivoTruncoCanceladoLog($this->session->flashdata('itemPlan'),
              $row->fecha_registro,10);
              $boton='';
              if(count($logMotivoTrunco->result()) >0){
              $boton='<button data-fechaT='.$row->fecha_registro.' data-itemT='.$this->session->flashdata('itemPlan').' style="color: white;background-color: var(--verde_telefonica)" class="btn btn-secondary waves-effect" data-toggle="modal" onclick="verMotivoTrunco(this)">Ver Motivo</button>';
              }
              array_push($arrayLog11, $row->fecha_registro, 'OBRA TRUNCA', tr_replace("|","<br>",$row->accion),$row->nombre,
              $boton);

              }

              }

              if(count($logCancelado->result()) >0){

              foreach($logCancelado->result() as $row){

              $logMotivoCancelado= $this->m_log_ingfix->getMotivoTruncoCanceladoLog($this->session->flashdata('itemPlan'),
              $row->fecha_registro,6);
              $boton='';
              if(count($logMotivoCancelado->result()) >0){
              $boton='<button data-fechaC='.$row->fecha_registro.' data-itemC='.$this->session->flashdata('itemPlan').' style="color: white;background-color: var(--verde_telefonica);" class="btn btn-secondary waves-effect" data-toggle="modal" onclick="verMotivoCancelar(this)">Ver Motivo</button>';
              }
              array_push($arrayLog12,$row->fecha_registro, 'OBRA CANCELADA',  $row->estado,$row->nombre,
              $boton);
              }

              }

              if(count($logCerrado->result()) >0){

              foreach($logCerrado->result() as $row){
              array_push($arrayLog13,$row->fecha_registro, $row->registro,  $row->estado,$row->nombre,
              '');
              }

              }

              $arrayLogMatriz = array($arrayLog1,
              $arrayLog2,
              $arrayLog3,
              $arrayLog4,
              $arrayLog5,
              $arrayLog6,
              $arrayLog7,
              $arrayLog8,
              $arrayLog9,
              $arrayLog10,
              $arrayLog11,
              $arrayLog12,
              $arrayLog13);

              array_multisort($arrayLogMatriz, SORT_DESC,$arrayLogMatriz[0][1] );
             */

            // array_multisort( $arrayLogMatriz);

            /*             * ******************************************************************************************************** */

            if (count($logPreRegistro->result()) > 0) {

                foreach ($logPreRegistro->result() as $row) {
                    $htmlPreReg .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->usua_crea . '</td>
                                   <td>' . $row->fecha_creacion . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logPreDiseno->result()) > 0) {

                foreach ($logPreDiseno->result() as $row) {
                    $htmlPreDiseno .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logPredisenoUpdate->result()) > 0) {

                foreach ($logPredisenoUpdate->result() as $row) {
                    $htmlPreDisenoUpdate .= '<tr class="item">
                                   <td>REGISTRO</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> RETORNO A PREDISEÑO PARA REASIGNACION DE DISEÑO </td>
                                   </tr>';
                }
            }

            if (count($logDisenoUpdate->result()) > 0) {

                foreach ($logDisenoUpdate->result() as $row) {
                    $htmlDisenoUpdate .= '<tr class="item">
                                   <td>' . strtoupper($row->estado) . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> RETORNO A DISEÑO PARA MODIFICACION</td>
                                   </tr>';
                }
            }

            if (count($logDiseno->result()) > 0) {

                foreach ($logDiseno->result() as $row) {
                    $htmlDiseno .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->usuario_adjudicacion . '</td>
                                   <td>' . $row->fecha_adjudicacion . '</td>
                                   <td>ADJUDICACION EN LA ESTACION ' . $row->estacionDesc . ' </td>
                                   </tr>';
                }
            }

            if (count($logDisenoEjecutado->result()) > 0) {

                foreach ($logDisenoEjecutado->result() as $row) {
                    $htmlDisenoEjecu .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->usuario_ejecucion . '</td>
                                   <td>' . $row->fecha_ejecucion . '</td>
                                   <td>EJECUCION EN LA ESTACION ' . $row->estacionDesc . ' </td>
                                   </tr>';
                }
            }

            if (count($logDisenoParcial->result()) > 0) {

                foreach ($logDisenoParcial->result() as $row) {
                    $htmlDisenoParcial .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logEnObra->result()) > 0) {

                foreach ($logEnObra->result() as $row) {
                    $htmlEnObra .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logPreLiquidacion->result()) > 0) {

                foreach ($logPreLiquidacion->result() as $row) {
                    $fecha = $row->fecha_registro;

                    if ($this->validaFechaTermino($fecha)) {
                        $htmlPreLiquida .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                    } else {
                        $htmlPreLiquida .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>Terminado</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                    }
                }
            }

            if (count($logTerminado->result()) > 0) {

                foreach ($logTerminado->result() as $row) {
                    $htmlTerminado .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logTrunco->result()) > 0) {

                foreach ($logTrunco->result() as $row) {

                    $logMotivoTrunco = $this->m_log_ingfix->getMotivoTruncoCanceladoLog($this->session->flashdata('itemPlan'), $row->fecha_registro, 10);
                    $boton = '';
                    if (count($logMotivoTrunco->result()) > 0) {
                        $boton = '<button data-fechaT=' . $row->fecha_registro . ' data-itemT=' . $this->session->flashdata('itemPlan') . ' style="color: white;background-color: var(--verde_telefonica)" class="btn btn-secondary waves-effect" data-toggle="modal" onclick="verMotivoTrunco(this)">Ver Motivo</button>';
                    }

                    $htmlTrunco .= '<tr class="item">
                                   <td>OBRA TRUNCA</td>
                                   <td>' . str_replace("|", "<br>", $row->accion) . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                    <td>' . $boton . ' </td>
                                   </tr>';
                }
            }

            if (count($logCancelado->result()) > 0) {

                foreach ($logCancelado->result() as $row) {

                    $logMotivoCancelado = $this->m_log_ingfix->getMotivoTruncoCanceladoLog($this->session->flashdata('itemPlan'), $row->fecha_registro, 6);
                    $boton = '';
                    if (count($logMotivoCancelado->result()) > 0) {
                        $boton = '<button data-fechaC=' . $row->fecha_registro . ' data-itemC=' . $this->session->flashdata('itemPlan') . ' style="color: white;background-color: var(--verde_telefonica);" class="btn btn-secondary waves-effect" data-toggle="modal" onclick="verMotivoCancelar(this)">Ver Motivo</button>';
                    }

                    $htmlCancelado .= '<tr class="item">
                                   <td>OBRA CANCELADA</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td>' . $boton . ' </td>
                                   </tr>';
                }
            }

            if (count($logCerrado->result()) > 0) {

                foreach ($logCerrado->result() as $row) {
                    $htmlCerrado .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logFichaTecnicaReg->result()) > 0) {

                foreach ($logFichaTecnicaReg->result() as $row) {
                    $htmlFichaTecnicaReg .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estadoPlanDesc . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logFichaTecnicaVal->result()) > 0) {

                foreach ($logFichaTecnicaVal->result() as $row) {
                    $htmlFichaTecnicaVal .= '<tr class="item">
                                   <td>' . $row->validacion . '</td>
                                   <td>' . $row->estadoPlanDesc . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_validacion . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logCertificacion->result()) > 0) {

                foreach ($logCertificacion->result() as $row) {
                    $htmlCertificacion .= '<tr class="item">
                                   <td>' . $row->accion . '</td>
                                   <td>' . $row->estado_final . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_valida . '</td>
                                   <td>' . $row->observacion . '</td>
                                   </tr>';
                }
            }

            $html = trim($htmlCabcer . $htmlPreReg . $htmlPreDiseno . $htmlPreDisenoUpdate .
                    $htmlDisenoUpdate . $htmlDiseno . $htmlDisenoEjecu . $htmlDisenoParcial . $htmlEnObra . $htmlPreLiquida .
                    $htmlTerminado . $htmlTrunco . $htmlCancelado . $htmlCerrado . $htmlFichaTecnicaReg . $htmlFichaTecnicaVal . $htmlCertificacion . '</tbody>
                         </table><br><br>');

            return utf8_decode($html);
        } catch (Exception $e) {
            throw new Exception('Hubo un error ejecutarse la funcion getAllDataLogPlanObra');
            return utf8_decode("null");
        }
    }

    public function getAllDataLogPOVR() {
        try {
            $logCertificacion = $this->m_log_ingfix->getSolicitudVRByIP($this->session->flashdata('itemPlan'));

            $html = '<table id="data-tableVR"  class="table table-bordered"  style="font-size: smaller;">
                        <thead class="thead-default">
                            <th>VALE DE RESERVA</th>
                            <th>PTR</th>
                            <th>COD MATERIAL</th>
                            <th>DESCRIPCION</th>
                            <th>ESTADO</th>
                            <th>USUARIO</th>
                            <th>FECHA DE REGISTRO</th>
                            <th>FECHA DE ATENCION</th>
                            <th>ACCION</th>
                        </thead>
                        <tbody>';
            if (count($logCertificacion) > 0) {
                foreach ($logCertificacion as $row) {
                    $html .= '
                        <tr>
                            <td>' . $row->vr . '</td>
                            <td>' . $row->ptr . '</td>
                            <td>' . $row->material . '</td>
                            <td>' . $row->desc_material . '</td>
                            <td>' . $row->estado . '</td>
                            <td>' . $row->nombre . '</td>
                            <td>' . $row->fecha_registro . '</td>
                            <td>' . $row->fecha_atencion . '</td>
                            <td style="text-align:center">
                                <a style="cursor:pointer; color: var(--verde_telefonica)" data-idsolvr="' . $row->idSolicitudValeReserva . '" onclick="openModalDetLogVR(this)"><i class="zmdi zmdi-hc-2x zmdi-eye"></i></a>
                            </td>
                        </tr>
                        ';
                }
            }
            $html .= '</tbody>
                </table>';

            return utf8_decode($html);
        } catch (Exception $e) {
            throw new Exception('Hubo un error ejecutarse la funcion getAllDataLogPOVR');
            return utf8_decode("null");
        }
    }

    public function getDetalleVRById() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {

            $idSolVR = $this->input->post('idSolVR');

            $data['tablaDetVR'] = $this->makeHTLMTablaDetVR($this->m_log_ingfix->getDetalleLogVR($idSolVR));

            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function makeHTLMTablaDetVR($listaDetalleVR) {

        $html = '<table id="data-tableDetVR"  class="table table-bordered"  style="font-size: smaller;">
                    <thead class="thead-default">
                        <th>PTR</th>
                        <th>USUARIO</th>
                        <th>FECHA DE REGISTRO</th>
                        <th>COMENTARIO</th>
                        <th>ESTADO</th>
                    </thead>
                    <tbody>';

        if (count($listaDetalleVR) > 0) {
            foreach ($listaDetalleVR as $row) {
                $html .= '
                        <tr>
                            <td>' . $row->ptr . '</td>
                            <td>' . $row->nombre . '</td>
                            <td>' . $row->fecha_registro . '</td>
                            <td>' . $row->comentario . '</td>
                            <td>' . $row->estado . '</td>
                        </tr>
                        ';
            }
        }
        $html .= '</tbody>
                </table>';

        return utf8_decode($html);
    }

    public function getMotivoCancelConsulta() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try {

            $itemPlan = $this->input->post('itemplan');
            $fechaCancel = $this->input->post('fecha');

            $this->session->set_flashdata('fechaCancel', $fechaCancel);
            $this->session->set_flashdata('itemPlan', $itemPlan);

            // log_message('error', 'fecha'.$this->session->flashdata('fechaCancel'));

            $data['motivoCancel'] = $this->gettHTMLMotivoCancel();

            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function getMotivoTruncoConsulta() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try {

            $itemPlan = $this->input->post('itemplan');
            $fechaTrunco = $this->input->post('fecha');

            $this->session->set_flashdata('fechaTrunco', $fechaTrunco);
            $this->session->set_flashdata('itemPlan', $itemPlan);

            $data['motivoTrunco'] = $this->gettHTMLMotivoTrunco();

            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function gettHTMLMotivoCancel() {

        $htmlCabcerMotivo = ' <div>MOTIVO CANCELACION PLAN OBRA</div>
                            <table class="table table-bordered" >
                                <thead class="thead-default">
                                    <tr>
                                        <th>COMENTARIO</th>
                                        <th>USUARIO</th>
                                        <th>FECHA REGISTRO</th>
                                    </tr>
                                </thead>
                                <tbody>';

        $logMotivoCancel = $this->m_log_ingfix->getMotivoTruncoCanceladoLog($this->session->flashdata('itemPlan'), $this->session->flashdata('fechaCancel'), 6);
        $htmlMotivoCancel = "";

        if (count($logMotivoCancel->result()) > 0) {

            foreach ($logMotivoCancel->result() as $row) {
                $htmlMotivoCancel .= '<tr>
                                   <td>' . $row->comentario . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha . '</td>
                                   </tr>';
            }

            $htmlCancel = $htmlCabcerMotivo . $htmlMotivoCancel . '</tbody>
                         </table><br><br>';

            return utf8_decode($htmlCancel);
        } else {
            $htmlCancel = 'No existe motivo';
            return utf8_decode($htmlCancel);
        }
    }

    public function gettHTMLMotivoTrunco() {

        $htmlCabcerMotivo = ' <div>MOTIVO TRUNCO PLAN OBRA</div>
                            <table class="table table-bordered" >
                                <thead class="thead-default">
                                    <tr>
                                        <th>COMENTARIO</th>
                                        <th>USUARIO</th>
                                        <th>FECHA REGISTRO</th>
                                    </tr>
                                </thead>
                                <tbody>';

        $logMotivoTrunco = $this->m_log_ingfix->getMotivoTruncoCanceladoLog($this->session->flashdata('itemPlan'), $this->session->flashdata('fechaTrunco'), 10);
        $htmlMotivoTrunco = "";

        if (count($logMotivoTrunco->result()) > 0) {

            foreach ($logMotivoTrunco->result() as $row) {
                $htmlMotivoTrunco .= '<tr>
                                   <td>' . $row->comentario . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha . '</td>
                                   </tr>';
            }

            $htmlTrunco = $htmlCabcerMotivo . $htmlMotivoTrunco . '</tbody>
                         </table><br><br>';

            return utf8_decode($htmlTrunco);
        } else {
            $htmlCancel = 'No existe motivo';
            return utf8_decode($htmlTrunco);
        }
    }

    /*     * **************************************************TABPTR************************************************************* */

    public function getAllDataLogPTR() {

        try {

            $logPTR = $this->m_log_ingfix->getPTRMOVIMIENTO($this->session->flashdata('itemPlan'));
            $htmlPTRTRAMA = "";

            $htmlPTR = "";

            /*

              $logPTR= $this->m_log_ingfix->getListaLogPTR( $this->session->flashdata('itemPlan'));
              $logPTRTrama= $this->m_log_ingfix->getListaLogPTRTrama( $this->session->flashdata('itemPlan'));
              $logPTRWebUnificada= $this->m_log_ingfix->getListaLogWebUnificadaDet( $this->session->flashdata('itemPlan'));

              $html="";
              $htmlPTR="";
              $htmlPTRTRAMA="";
              $htmlPTRWUDET="";

              if(count($logPTRTrama->result()) >0){
              $htmlPTR = '<div>LOG TRAMA SIGOPLUS</div>
              <table id="data-tablePTRTrama" class="table table-bordered" style="font-size: smaller;">
              <thead class="thead-default">
              <tr>
              <th>ORIGEN</th>
              <th>PTR</th>
              <th>DESCRIPCION</th>
              <th>FECHA REGISTRO</th>
              </tr>
              </thead>
              <tbody>';
              foreach($logPTRTrama->result() as $row){
              $htmlPTR .='<tr>
              <td>'.$row->origen.'</td>
              <td>'.$row->ptr.'</td>
              <td>'.$row->descripcion.'</td>
              <td>'.$row->fecha_registro.'</td>
              </tr>';
              }
              $htmlPTR     .='</tbody>
              </table><br><br>';

              }

              if(count($logPTR->result()) >0){
              $htmlPTR = '<div>LOG PTR</div>
              <table id="data-tablePTR" class="table table-bordered" style="font-size: smaller;">
              <thead class="thead-default">
              <tr>
              <th>PTR</th>
              <th>USUARIO</th>
              <th>FECHA REGISTRO</th>

              </tr>
              </thead>
              <tbody>';
              foreach($logPTR->result() as $row){
              $htmlPTR .='<tr>
              <td>'.$row->ptr.'</td>
              <td>'.$row->usuario.'</td>
              <td>'.$row->fecha_registro.'</td>
              </tr>';
              }
              $htmlPTR     .='</tbody>
              </table><br><br>';

              }

              if(count($logPTRWebUnificada->result()) >0){

              $htmlPTRWUDET = '<div>LOG WEB UNIFICADA DET</div>
              <table id="data-tableWUDET" class="table table-bordered" style="font-size: smaller;">
              <thead class="thead-default">
              <tr>
              <th>ESTADO</th>
              <th>PTR</th>
              <th>USUARIO APROBADOR</th>
              <th>FECHA APROBACION</th>
              </tr>
              </thead>
              <tbody>';
              foreach($logPTRWebUnificada->result() as $row){
              $htmlPTRWUDET .='<tr>
              <td>'.$row->ESTADO.'</td>
              <td>'.$row->PTR.'</td>
              <td>'.$row->USUA_APROB.'</td>
              <td>'.$row->fec_aprob.'</td>
              </tr>';
              }
              $htmlPTRWUDET     .='</tbody>
              </table><br><br>';

              }

              $html=  $htmlPTRTRAMA.$htmlPTR.$htmlPTRWUDET;
             */

            $varaux = '';

            if (count($logPTR->result()) > 0) {
                $htmlPTR = '
                                <table id="data-tablePTR" class="table table-bordered" style="font-size: smaller;">
                                 <tbody>';
                foreach ($logPTR->result() as $row) {

                    if ($varaux == '') {
                        $varaux = $row->poCod;
                        $htmlPTR .= '<tr class="thead-default"><th colspan=3>' . $varaux . '</th></tr><tr class="thead-default"><th>ACCION</th><th>USUARIO</th><th>FECHA DE REGISTRO</th></tr>';
                    } else {
                        if ($varaux != $row->poCod) {
                            $varaux = $row->poCod;
                            $htmlPTR .= '<tr class="thead-default"><th colspan=3>' . $varaux . '</th></tr><tr class="thead-default"><th>ACCION</th><th>USUARIO</th><th>FECHA DE REGISTRO</th></tr>';
                        }
                    }

                    $htmlPTR .= '<tr>
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->usuario . '</td>
                                   <td>' . $row->fecharegistro . '</td>
                                   </tr>';
                }
                $htmlPTR .= '</tbody>
                         </table><br><br>';
            }

            $html = $htmlPTR;

            if ($html != '') {
                return utf8_decode($html);
            } else {
                $html = 'No cuenta con registro de PTR';
                return utf8_decode($html);
            }
        } catch (Exception $e) {
            throw new Exception('Hubo un error ejecutarse la funcion getAllDataLogPlanObra');
            return utf8_decode("null");
        }
    }

    public function getAllDataLogPorcentajeAvance() {

        try {

            $logPorcentajeAvance = $this->m_log_ingfix->getListaLogPorcentajeAvance2($this->session->flashdata('itemPlan'));

            $html = "";
            $htmlPorcentajeAvance = "";

            if (count($logPorcentajeAvance->result()) > 0) {
                $htmlPorcentajeAvance = '
                                <table id="data-tableAvance" class="table table-bordered" style="font-size: smaller;">
                                <thead class="thead-default">
                                    <tr>
                                        <th>ESTACION</th>
                                        <th>PORCENTAJE</th>
                                        <th>NOMBRE</th>
                                        <th>FECHA REGISTRO</th>
                                    </tr>
                                </thead>
                                <tbody>';
                foreach ($logPorcentajeAvance->result() as $row) {
                    $htmlPorcentajeAvance .= '<tr>
                                   <td>' . $row->estacionDesc . '</td>
                                   <td>' . $row->porcentaje . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   </tr>';
                }
                $htmlPorcentajeAvance .= '</tbody>
                         </table><br><br>';
            }

            $html = $htmlPorcentajeAvance;

            if ($html != '') {
                return utf8_decode($html);
            } else {
                $html = 'No cuenta con porcentaje de avances';
                return utf8_decode($html);
            }

            return utf8_decode($html);
        } catch (Exception $e) {
            throw new Exception(' un error ejecutarse la funcion getAllDataLogPlanObra');
            return utf8_decode("null");
        }
    }

    /*     * ********************FIN LOG PLANOBRA**************************** */

    public function getHTMLProyectoConsulta() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $idplanta = $this->input->post('tipoplanta');
            $listaProy = $this->m_utils->getProyectoxTipoPlanta($idplanta);
            $html = '<option>&nbsp;</option>';
            foreach ($listaProy->result() as $row) {
                $html .= '<option value="' . $row->idproyecto . '">' . $row->proyectoDesc . '</option>';
            }
            $data['listaProyectos'] = $html;
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function getHTMLSubProyectoConsulta() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $idProyecto = $this->input->post('proyecto');

            $listaSubProy = $this->m_utils->getAllSubProyectoByProyecto($idProyecto);
            $html = '<option>&nbsp;</option>';
            foreach ($listaSubProy->result() as $row) {
                $html .= '<option value="' . $row->idSubProyecto . '">' . $row->subProyectoDesc . '</option>';
            }
            $data['listaSubProy'] = $html;
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    public function validaFechaTermino($fecha1) {
        $fecha2 = '2018-07-06';
        return (strtotime($fecha1) > strtotime($fecha2));
    }

    public function sort_by_orden($a, $b) {
        return $a['orden'] - $b['orden'];
    }

    function getDataSiom() {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {
            $itemplan = $this->input->post('itemplan');

            if ($itemplan == null) {
                throw new Exception("ERROR ITEMPLAN");
            }
            $tablaSiom = $this->getTablaConsultaSiom($itemplan);
            $data['error'] = EXIT_SUCCESS;
            $data['tablaSiom'] = $tablaSiom;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getTablaConsultaSiom($itemplan) {
        $array = $this->m_utils->getDataSiomAll($itemplan);
        $cont = 0;

        if ($this->session->userdata('eeccSession') == 6 || $this->session->userdata('eeccSession') == 0) {
            $disabled = NULL;
        } else {
            $disabled = 'disabled';
        }
        /*
          $html = '<table id="data-table" class="table table-bordered container">
          <thead class="thead-default">
          <tr>
          <th>ITEMPLAN</th>
          <th>ESTACI&Oacute;N</th>
          <th>C&Oacute;DIGO SIOM</th>
          <th>ACTUALIZAR</th>
          </tr>
          </thead>

          <tbody>';
          foreach($array as $row){
          $cont++;
          $html .=' <tr>
          <td>'.$row->itemplan.'</td>
          <td>'.$row->estacionDesc.'</td>
          <td><input id="inputCodigoSiom_'.$cont.'" type="text" class="form-control" value="'.$row->codigoSiom.'"/></td>
          <td><button class="btn btn-success" data-itemplan="'.$row->itemplan.'" data-codigo_siom="'.$row->codigoSiom.'"
          data-id_estacion="'.$row->idEstacion.'" onclick="actualizarCodigoSiom($(this),'.$cont.')" '.$disabled.'>Aceptar</button></td>
          </tr>';
          } */
        $html = '<table id="data-table" class="table table-bordered container">
                        <thead class="thead-default">
                            <tr>
                                <th>ITEMPLAN</th>
                                <th>ESTACI&Oacute;N</th>                            
                                <th>C&Oacute;DIGO SIOM</th>
                                <th>ULTIMO ESTADO</th>
                                <th>FEC. ULTIMO ESTADO</th>
                            </tr>
                        </thead>
                        
                        <tbody>';
        foreach ($array as $row) {
            $cont++;
            $html .= ' <tr>
                            <td>' . $row->itemplan . '</td>      
                            <td>' . $row->estacionDesc . '</td>  
                            <td>' . $row->codigoSiom . '</td>
                            <td>' . $row->ultimo_estado . '</td>  
                            <td>' . $row->fecha_ultimo_estado . '</td>
                         </tr>';
        }

        $html .= '</tbody>
                </table>';
        return utf8_decode($html);
    }

    function hasDisenoAdjudicado() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $itemplan = $this->input->post('itemplan');
            $hasAdju = $this->m_utils->hasDisenoAdjudicado($itemplan);
            $data['hasAdju'] = $hasAdju;
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function getDataSisego() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $html = null;
            $itemplan = $this->input->post('itemplan');

            if ($itemplan == null) {
                throw new Exception('itemplan null, comunicarse con el programador');
            }

            $arrayPlanObra = $this->m_utils->getInfoItemplan($itemplan);
            $html .= '<div class="row form-group">
                        <div class="col-md-6">
                            <label>OPERADOR: </label>
                            <label style="color:blue">' . strtoupper($arrayPlanObra['operador']) . '</label>
                        </div>
                        <div class="col-md-6">
                            <label>TIPO DISE&Ntilde;O:</label>
                            <label style="color:blue">' . strtoupper($arrayPlanObra['tipo_diseno']) . '</label>
                        </div>
                      </div>
                      <div class="row form-group">
                        <div class="col-md-6">
                            <label>NOMBRE ESTUDIO: </label>
                            <label style="color:blue">' . strtoupper($arrayPlanObra['nombre_estudio']) . '</label>
                        </div>
                        <div class="col-md-6">
                            <label>DURACI&Oacute;N: </label>
                            <label style="color:blue">' . strtoupper($arrayPlanObra['duracion']) . '</label>
                        </div>
                      </div>
                     
                      <div class="row form-group">
                        <div class="col-md-6">
                            <label>ACCESO CLIENTE: </label>
                            <label style="color:blue">' . strtoupper($arrayPlanObra['acceso_cliente']) . '</label>
                        </div>
                        <div class="col-md-6">
                            <label>TENDIDO EXTERNO: </label>
                            <label style="color:blue">' . strtoupper($arrayPlanObra['tendido_externo']) . '</label>
                        </div>
                      </div>
                      <div class="row form-group">
                         <div class="col-md-6">
                            <label>TIPO CLIENTE: </label>
                            <label style="color:blue">' . strtoupper($arrayPlanObra['tipo_cliente']) . '</label>
                        </div>
                      </div>';
            $data['dataInfoSisego'] = $html;
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }

    function actualizarCodigoSiom() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $itemplan = $this->input->post('itemplan');
            $idEstacion = $this->input->post('idEstacion');
            $codigoSiom = $this->input->post('codigoSiom');

            $this->db->trans_begin();
            if ($itemplan == null) {
                throw new Exception('itemplan null, comunicarse con el programador');
            }

            if ($idEstacion == null) {
                throw new Exception('idEstacion null, comunicarse con el programador');
            }

            if ($codigoSiom == null) {
                throw new Exception('codigoSiom null, comunicarse con el programador');
            }

            $data = $this->m_consulta->actualizarCodigoSiom($itemplan, $idEstacion, $codigoSiom);
            $arrayDataLog = array(
                'tabla' => 'siom_obra',
                'actividad' => 'Actualizar Codigo Siom',
                'itemplan' => $itemplan,
                'fecha_registro' => $this->fechaActual(),
                'id_usuario' => $this->session->userdata('idPersonaSession'),
                'codigo_siom' => $codigoSiom
            );

            $countRows = $this->m_utils->registrarLogPlanObra($arrayDataLog);
            if ($countRows == 0) {
                throw new Exception('error no se registro el log');
            }

            $this->db->trans_commit();
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
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

    public function getAllDataLogPlanObra2() {

        try {

            $logCreacionIP = $this->m_log_ingfix->getLogCreacionIP($this->session->flashdata('itemPlan'));
            $logAdjudicacion = $this->m_log_ingfix->getAdjuLog($this->session->flashdata('itemPlan'));
            $logEjecDiseno = $this->m_log_ingfix->getDisenoEjectLog($this->session->flashdata('itemPlan'));
            $logDisenoParcial = $this->m_log_ingfix->getDisenoParcialLog2($this->session->flashdata('itemPlan'));
            $logEnObra = $this->m_log_ingfix->getEnObraLog($this->session->flashdata('itemPlan'));
            $logPreLiquidacion = $this->m_log_ingfix->getPreLiquidacionLog2($this->session->flashdata('itemPlan'));
            $logTerminado = $this->m_log_ingfix->getTerminadoLog2($this->session->flashdata('itemPlan'));

            $logTrunco = $this->m_log_ingfix->getTruncoLog($this->session->flashdata('itemPlan'));
            $logCancelado = $this->m_log_ingfix->getCanceladoLog($this->session->flashdata('itemPlan'));
            $logCerrado = $this->m_log_ingfix->getCerradoLog($this->session->flashdata('itemPlan'));

            $logFichaTecnicaReg = $this->m_log_ingfix->getFichaTecnicaLogReg($this->session->flashdata('itemPlan'));
            $logFichaTecnicaVal = $this->m_log_ingfix->getFichaTecnicaLogVal($this->session->flashdata('itemPlan'));

            $logCertificacion = $this->m_log_ingfix->getCertificacionLog($this->session->flashdata('itemPlan'));

            $htmlCabcer = '
                             <table id="data-tablePO"  class="table table-bordered"  style="font-size: smaller;">
                                <thead class="thead-default">
                                    <tr>
                                       <th onclick="w3.sortHTML(' . "'" . '#data-tablePO' . "'" . ', ' . "'" . '.item' . "'" . ', ' . "'" . 'td:nth-child(3)' . "'" . ')" style="cursor:pointer">ACCION <i class="zmdi zmdi-unfold-more"></i></th>
                                       <th>USUARIO</th>
                                       <th onclick="w3.sortHTML(' . "'" . '#data-tablePO' . "'" . ', ' . "'" . '.item' . "'" . ', ' . "'" . 'td:nth-child(1)' . "'" . ')" style="cursor:pointer">FECHA REGISTRO <i class="zmdi zmdi-unfold-more"></i></th>
                                       <th>OBSERVACIONES</th>
                                    </tr>
                                </thead>
                                <tbody>';

            //NUEVAS VARIABLES

            $htmlCreacionIP = "";
            $htmlAjud = "";
            $htmlDisenoEjecu = "";
            $htmlDisenoParcial = "";
            $htmlEnObra = "";
            $htmlPreLiquida = "";
            $htmlTerminado = "";
            $htmlTrunco = "";
            $htmlCancelado = "";
            $htmlCerrado = "";
            $htmlFichaTecnicaReg = "";
            $htmlFichaTecnicaVal = "";
            $htmlCertificacion = "";


            if (count($logCreacionIP) > 0) {

                foreach ($logCreacionIP as $row) {
                    $htmlCreacionIP .= '<tr class="item">
                                        <td>CREACION</td>
                                        <td>' . $row->usuario . '</td>
                                        <td>' . $row->fecha_registro . '</td>
                                        <td></td>
                                 </tr>';
                }
            }

            if (count($logAdjudicacion) > 0) {

                foreach ($logAdjudicacion as $row) {
                    $htmlAjud .= '<tr class="item">
                                        <td>ADJUDICACION</td>
                                        <td>' . $row->usuario_adjudicacion . '</td>
                                        <td>' . $row->fecha_adjudicacion . '</td>
                                        <td> ESTACION ' . $row->estacionDesc . ' </td>
                                 </tr>';
                }
            }

            if (count($logEjecDiseno) > 0) {

                foreach ($logEjecDiseno as $row) {
                    $htmlDisenoEjecu .= '<tr class="item">
                                            <td>EJECUCION</td>
                                            <td>' . $row->usuario_ejecucion . '</td>
                                            <td>' . $row->fecha_ejecucion . '</td>
                                            <td>ESTACION ' . $row->estacionDesc . (($row->path_expediente_diseno != null) ?
                            '<br>EXPEDIENTE     <a href="' . $row->path_expediente_diseno . '" download=""><i class="zmdi zmdi-hc-2x zmdi-download"></i></a>' : '') . '</td>
                                        </tr>';
                }
            }

            if (count($logDisenoParcial) > 0) {

                foreach ($logDisenoParcial as $row) {
                    $arrayDescrip = explode('|', $row->itemplan_default);

                    $htmlDisenoParcial .= '<tr class="item">
                                   <td>DISENO PARCIAL</td>
                                   <td>' . $row->usuario . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td>' . (count($arrayDescrip) > 0 ? $arrayDescrip[1] : '') . '</td>
                                   </tr>';
                }
            }

            if (count($logEnObra->result()) > 0) {

                foreach ($logEnObra->result() as $row) {
                    $htmlEnObra .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logPreLiquidacion->result()) > 0) {

                foreach ($logPreLiquidacion->result() as $row) {
                    $fecha = $row->fecha_registro;
                    $htmlPreLiquida .= '<tr class="item">
                                <td>LIQUIDACION</td>
                                <td>' . $row->usuario . '</td>
                                <td>' . $row->fecha_registro . '</td>
                                <td> </td>
                                </tr>';
                }
            }

            if (count($logTerminado->result()) > 0) {

                foreach ($logTerminado->result() as $row) {
                    $htmlTerminado .= '<tr class="item">
										<td>TERMINADO</td>
									   <td>' . $row->usuario . '</td>
									   <td>' . $row->fecha_registro . '</td>
									   <td> </td>
									   </tr>';
                }
            }

            if (count($logTrunco->result()) > 0) {

                foreach ($logTrunco->result() as $row) {

                    $logMotivoTrunco = $this->m_log_ingfix->getMotivoTruncoCanceladoLog($this->session->flashdata('itemPlan'), $row->fecha_registro, 10);
                    $boton = '';
                    if (count($logMotivoTrunco->result()) > 0) {
                        $boton = '<button data-fechaT=' . $row->fecha_registro . ' data-itemT=' . $this->session->flashdata('itemPlan') . ' style="color: white;background-color: var(--verde_telefonica)" class="btn btn-secondary waves-effect" data-toggle="modal" onclick="verMotivoTrunco(this)">Ver Motivo</button>';
                    }

                    $htmlTrunco .= '<tr class="item">
                                   <td>OBRA TRUNCA</td>
                                   <td>' . str_replace("|", "<br>", $row->accion) . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                    <td>' . $boton . ' </td>
                                   </tr>';
                }
            }

            if (count($logCancelado->result()) > 0) {

                foreach ($logCancelado->result() as $row) {

                    $logMotivoCancelado = $this->m_log_ingfix->getMotivoTruncoCanceladoLog($this->session->flashdata('itemPlan'), $row->fecha_registro, 6);
                    $boton = '';
                    if (count($logMotivoCancelado->result()) > 0) {
                        $boton = '<button data-fechaC=' . $row->fecha_registro . ' data-itemC=' . $this->session->flashdata('itemPlan') . ' style="color: white;background-color: var(--verde_telefonica);" class="btn btn-secondary waves-effect" data-toggle="modal" onclick="verMotivoCancelar(this)">Ver Motivo</button>';
                    }

                    $htmlCancelado .= '<tr class="item">
                                   <td>OBRA CANCELADA</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td>' . $boton . ' </td>
                                   </tr>';
                }
            }

            if (count($logCerrado->result()) > 0) {

                foreach ($logCerrado->result() as $row) {
                    $htmlCerrado .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->estado . '</td>
                                   <td>' . $row->nombre . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logFichaTecnicaReg->result()) > 0) {

                foreach ($logFichaTecnicaReg->result() as $row) {
                    $htmlFichaTecnicaReg .= '<tr class="item">
                                   <td>' . $row->registro . '</td>
                                   <td>' . $row->usuario . '</td>
                                   <td>' . $row->fecha_registro . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logFichaTecnicaVal->result()) > 0) {

                foreach ($logFichaTecnicaVal->result() as $row) {
                    $htmlFichaTecnicaVal .= '<tr class="item">
                                   <td>' . $row->validacion . '</td>
                                   <td>' . $row->usuario . '</td>
                                   <td>' . $row->fecha_validacion . '</td>
                                   <td> </td>
                                   </tr>';
                }
            }

            if (count($logCertificacion->result()) > 0) {

                foreach ($logCertificacion->result() as $row) {
                    $htmlCertificacion .= '<tr class="item">
                                   <td>' . $row->accion . '</td>
                                   <td>' . $row->usuario . '</td>
                                   <td>' . $row->fecha_valida . '</td>
                                   <td>' . $row->observacion . '</td>
                                   </tr>';
                }
            }


            $html = trim($htmlCabcer . $htmlCreacionIP . $htmlAjud . $htmlDisenoEjecu . $htmlDisenoParcial . $htmlEnObra .
                    $htmlPreLiquida . $htmlTerminado . $htmlTrunco . $htmlCancelado . $htmlCerrado . $htmlFichaTecnicaReg .
                    $htmlFichaTecnicaVal . $htmlCertificacion . '</tbody></table><br><br>');

            return utf8_decode($html);
        } catch (Exception $e) {
            throw new Exception('Hubo un error ejecutarse la funcion getAllDataLogPlanObra');
            return utf8_decode("null");
        }
    }
	/**nuevo**/
    public function expediente_liquidacion() {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        $data['cabecera'] = null;
        try {
            $itemplan = $this->input->post('itemplan');
            $idEstacion = $this->input->post('idEstacion');
            $infoExpediente = $this->m_consultaszip->getExpedienteLiquidacion($itemplan, $idEstacion);
            log_message('error', print_r($infoExpediente, true));
            if($infoExpediente==null){
                $data['path'] = 2;
                throw new Exception('No se encontro expediente.');
            }else{
                $path = $infoExpediente['path_expediente'];
                log_message('error', $infoExpediente['path_expediente']);
                if (file_exists($path)) {
                    $data['path'] = 1;
                    $data['ruta'] = $path;
                } else {
                    $data['path'] = 2;
                }
            }            
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode(array_map('utf8_encode', $data));
    }
}
