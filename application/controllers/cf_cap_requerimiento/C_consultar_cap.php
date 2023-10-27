<?php

defined('BASEPATH') or exit('No direct script access allowed');

class C_consultar_cap extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_utils/m_utils');
        $this->load->model('mf_cap_requerimiento/m_requerimiento');
        $this->load->library('lib_utils');
        $this->load->library('excel');
        $this->load->helper('url');
    }

    public function index()
    {
        $idUsuario = $this->session->userdata('idPersonaSessionPan');
        $idEmpresaColab = $this->session->userdata('idEmpresaColabSesion');
        if ($idUsuario != null) {
            $permisos = $this->session->userdata('permisosArbolPan');
            $result   = $this->lib_utils->getHTMLPermisos($permisos, ID_CAP_REQUERIMIENTO_PADRE, null, ID_CONSULTAR_CAP_HIJO, ID_MODULO_DESPLIEGUE_PLANTA);
            
            $data['opciones']           = $result['html'];
            $data['tablaRequerimiento'] = $this->getTablaConsulta(null);
            $data['cmbTipoAreaReq']     = __buildComboAreaReq(null);
            $data['modulosTopFlotante'] = _getModulosFlotante();

            $this->load->view('vf_cap_requerimiento/v_consultar_cap', $data);
        } else {
            redirect(RUTA_OBRA2, 'refresh');
        }
    }

    function getTablaConsulta($id_area_requerimiento)
    {
        $arrayRequerimiento = $this->m_utils->getInfoRequerimiento($id_area_requerimiento);
        $html = '<table id="tbRequerimiento" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr class="text-center">
                            <th>CÓDIGO REQUERIMIENTO</th>  
                            <th>FECHA REGISTRO</th>  
                            <th>TIPO REQUERIMIENTO</th>                            
                            <th>TIPO PROYECTO</th>
                            <th>AREA REQUERIMIENTO</th>
                            <th>DESCRIPCION REQUERIMIENTO</th>
							<th>NOMBRE DEL GESTOR</th>
                            <th>RESPUESTA</th>
                            <th>FECHA RESPUESTA</th>
                            <th>DIAS TRANSCURRIDOS</th>
                            <th>DENTRO DE SLA?</th>
							<th>HORAS DE ESFUERZO</th>
                            <th>ESTADO</th>
                            <th>Responder</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($arrayRequerimiento as $row) {

            $estado = ($row['id_estado_requerimiento'] == 1) ? '
                            <span class="badge badge-warning">' . $row['estadoDesc'] . '</span>
                        ' : (($row['id_estado_requerimiento'] == 2) ?
                '<span class="badge badge-success">' . $row['estadoDesc'] . '</span>' :
                '<span class="badge badge-danger">' . $row['estadoDesc'] . '</span>');

            $btn_respuesta = '<a class="btn btn-outline-primary btn-xs" href="#" onclick="openModalRequerimiento($(this))"; data-id="' . $row['codigo_requerimiento'] . '"><i class="fal fa-pen-square"></i></a>';

            $html .= ' <tr>
                            <td>' . $row['codigo_requerimiento'] . '</td>
                            <td>' . $row['fecha_registro'] . '</td>
                            <td>' . $row['requerimientoDesc'] . '</td>
                            <td>' . $row['tipoProyectoDesc'] . '</td>
                            <td>' . $row['areaRequeDesc'] . '</td>
                            <td>' . $row['comentario_registro'] . '</td>  
                            <td>' . $row['nombre_completo'] . '</td>  
                            <td>' . $row['comentario_valida'] . '</span></td>
                            <td>' . $row['fecha_valida'] . '</td>
                            <td style="color:'.$row['color_sla'].'">' . $row['dias_transcurridos'] . '</td>
                            <td>' . $row['rsp_sla'] . '</td>
                            <td>' . $row['horas_esfuerzo'] . '</td>
                            <td>' . $estado . '</td>
                            <td>' . ($row['id_estado_requerimiento'] == 1 ? $btn_respuesta : '') . '</td>
                        </tr>';
        }
        $html .= '</tbody> </table>';
        return $html;
    }

    function getFormRespuesta()
    {
        $codigoRequerimiento = $this->input->post('codigoRequerimiento');

        $arrayEstado = $this->m_utils->getTipoEstadoAll();

        $htmlEstado = '';
        foreach ($arrayEstado as $est) {
            if ($est['id_estado_requerimiento'] != 1) {            
                $htmlEstado .= '<option value="' . $est['id_estado_requerimiento'] . '">' . $est['estadoDesc'] . '</option>';
            }
        }

        $html = '
            <form method="POST" id="frmRespuesta">
                <input type="hidden" id="txtCodigoRequerimiento" value="' . $codigoRequerimiento . '" />
                <div class="form-group row justify-content-center">
                    <label class="col-sm-3 col-form-label">Horas de esfuerzo</label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control" id="txtHoraEsfuerzo" name="txtHoraEsfuerzo">
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-sm-3 col-form-label">Estado</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="cmbEstadoReq" name="cmbEstadoReq" required>
                            <option>Seleccione</option>
                            ' . $htmlEstado . '
                        </select>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-sm-3 col-form-label">Descripcion de respuesta</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" rows="3" id="txtComentarioValida" name="txtComentarioValida"></textarea>
                    </div>
                </div>
            </form>
            ';

        $data['htmlFormRespuesta'] = $html;

        echo json_encode(array_map('utf8_encode', $data));
    }

    function actualizarRequerimiento()
    {
        $data['msj'] = null;
        $data['error'] = EXIT_ERROR;
        try {

            $codigo_requerimiento = $this->input->post('codigoRequerimiento');
            $horasEsfuerzo        = $this->input->post('horasEsfuerzo');
            $idEstadoReq         = $this->input->post('cmbEstadoReq');
            $comentarioValida     = $this->input->post('comentarioValida');

            $idUsuario            = $this->session->userdata('idPersonaSessionPan');
            $fechaActual          = _fecha_actual();

            if ($idUsuario == null || $idUsuario == '') {
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }
            if ($horasEsfuerzo == null || $horasEsfuerzo == '') {
                throw new Exception("Hubo un error al recibir la solicitud.");
            }
            if ($idEstadoReq == null || $idEstadoReq == '') {
                throw new Exception("Hubo un error al recibir la solicitud.");
            }

            $arrayData = array(
                                'horas_esfuerzo'          => $horasEsfuerzo,
                                'id_estado_requerimiento' => $idEstadoReq,
                                'comentario_valida'       => $comentarioValida,
                                'fecha_valida'            => $fechaActual,
                                'idUsuarioValida'         => $idUsuario
                            );

            $data = $this->m_requerimiento->actualizarRequerimiento($codigo_requerimiento, $arrayData);

            if ($data['error'] == EXIT_ERROR) {
                throw new Exception($data['msj']);
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function filtrarTabla()
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {

			$cmbTipoAreaReq = $this->input->post('cmbTipoAreaReq');

            $data['tbRequerimiento'] = $this->getTablaConsulta($cmbTipoAreaReq);
            $data['error'] = EXIT_SUCCESS;
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }
}
