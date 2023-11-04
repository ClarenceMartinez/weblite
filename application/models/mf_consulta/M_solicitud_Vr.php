<?php

class M_solicitud_Vr extends CI_Model {

    function __construct() {
        parent::__construct();
    } 

    function getPosMatByItemplan($itemplan,$idEmpresaColab) {
        $sql = "    SELECT ppo.itemplan,
                           ppo.codigo_po,
                           ppo.vale_reserva,
                           ec.empresaColabDesc,
                           ppo.idEstacion,
                           e.estacionDesc,
                           CONCAT(ppo.codigo_po,' (',e.estacionDesc,')') AS po_estacion,
                           ( SELECT GROUP_CONCAT(je.codAlmacen,'|',je.codCentro,'|', je.idJefatura, '|', je.idEmpresaColab) 
                               FROM jefatura_sap js, 
                                    jefatura_sap_x_empresacolab je 
                              WHERE js.idJefatura = je.idJefatura
                                AND je.idEmpresacolab = ec.idEmpresacolab
                                AND CASE WHEN js.idZonal IS NULL THEN js.descripcion = j.jefaturaDesc
                                    ELSE js.idZonal = po.idZonal END ) dataJefaturaEmp,
                           po.idZonal,
                           po.idCentral,
                           c.idJefatura,
                           j.jefaturaDesc,
                           po.idSubProyecto
                      FROM planobra_po ppo,
                           planobra po,
                           estacion e,
                           empresacolab ec,
                           central c,
                           jefatura j
                     WHERE ppo.itemplan = po.itemplan
                       AND ppo.idEstacion = e.idEstacion
                       AND po.idEmpresaColab = ec.idEmpresaColab
                       AND po.idCentral = c.idCentral
                       AND c.idJefatura = j.idJefatura
                       AND ppo.flg_tipo_area = 1
                       AND ppo.estado_po IN (3,4)
                       AND po.idEstadoPlan IN (11,3, 4, 9, 10, 6, 5, 21, 7, 18)
                       AND CASE WHEN ? = 0 OR ? = 6 OR ? IS NULL THEN TRUE      
                           ELSE po.idEmpresaColab = ? END
                    AND ppo.itemplan = ? 
					UNION ALL
                       SELECT ppo.itemplan,
                           ppo.codigo_po,
                           ppo.vale_reserva,
                           ec.empresaColabDesc,
                           ppo.idEstacion,
                           e.estacionDesc,
                           CONCAT(ppo.codigo_po,' (',e.estacionDesc,')') AS po_estacion,
                           ( SELECT GROUP_CONCAT(je.codAlmacen,'|',je.codCentro,'|', je.idJefatura, '|', je.idEmpresaColab) 
                               FROM jefatura_sap js, 
                                    jefatura_sap_x_empresacolab je 
                              WHERE js.idJefatura = je.idJefatura
                                AND je.idEmpresacolab = ec.idEmpresacolab
                                AND CASE WHEN js.idZonal IS NULL THEN js.descripcion = j.jefaturaDesc
                                    ELSE js.idZonal = po.idZonal END ) dataJefaturaEmp,
                           po.idZonal,
                           po.idCentral,
                           c.idJefatura,
                           j.jefaturaDesc,
                           po.idSubProyecto
                      FROM planobra_po ppo,
                           planobra po,
                           estacion e,
                           empresacolab ec,
                           central c,
                           jefatura j
                     WHERE ppo.itemplan = po.itemplan
                       AND ppo.idEstacion = e.idEstacion
                       AND po.idEmpresaColab = ec.idEmpresaColab
                       AND po.idCentral = c.idCentral
                       AND c.idJefatura = j.idJefatura
                       AND ppo.flg_tipo_area = 1
                       AND ppo.estado_po IN (3,4,5,6)
                       AND ppo.itemplan = '".$itemplan."'
                       AND ppo.itemplan IN ('')";
        $result = $this->db->query($sql, array($idEmpresaColab,$idEmpresaColab,$idEmpresaColab,$idEmpresaColab,$itemplan));
		log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    function getAllTipoSolVr() {
        $sql = " SELECT * FROM tipo_solicitud_vr WHERE estado = 1 ";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
	
	function getOnlyDevolucion() {
        $sql = " SELECT * FROM tipo_solicitud_vr WHERE estado = 1 AND id  = 3 ";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getCodigoSolicitudVr() {
        $sql = " SELECT fn_get_codigo_solicitud_vr() AS codigo_vr ";
        $result = $this->db->query($sql);
        return $result->row()->codigo_vr;
    }

    function registrarSolVr($dataSol,$dataDetalleSol) {
        $data['error'] = EXIT_ERROR;
	    $data['msj'] = null;
	    try{
            $this->db->insert('solicitud_vale_reserva', $dataSol);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'Hubo un error al insertar en la tabla solicitud_vale_reserva';
                $data['error'] = EXIT_ERROR;
            } else {
                $this->db->insert_batch('solicitud_vale_reserva_detalle', $dataDetalleSol);
                if($this->db->affected_rows() <= 0) {
                    $data['msj'] = 'Hubo un error al insertar en la tabla solicitud_vale_reserva_detalle';
                    $data['error'] = EXIT_ERROR;
                } else {
                    $data['msj'] = 'Se registró correctamente la solicitud de VR';
                    $data['error'] = EXIT_SUCCESS;   
                }   
            }
        }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
    }
    
    function getBandejaSolicitudVr($itemplan, $idJefatura, $idEmpresaColab, $idFase, $tipoAtencion) {
        $sql = "     SELECT svr.codigoSolVr,
                            svr.codigo_po,
                            svr.itemplan,
                            svr.vale_reserva,
                            '' AS vr_robot,
                            UPPER(js.descripcion) jefaturaDesc,
                            ec.empresaColabDesc,
                            f.faseDesc,
                            svr.estado,
                            (CASE WHEN svr.estado = 0 THEN 'ATENCIÓN PENDIENTE'
                                  WHEN svr.estado = 1 THEN 'ATENCIÓN PARCIAL'
                                  WHEN svr.estado = 2 THEN 'ATENCIÓN TOTAL'
                                  WHEN svr.estado = 3 THEN 'ATENCIÓN RECHAZADA'
                              END ) AS estadoDesc,
                            (CASE WHEN svr.ult_fecha_atencion IS NULL
                                  THEN TIMEDIFF(ADDTIME(NOW(), '01:00:00'), svr.fecha_registro)
                                  ELSE TIMEDIFF(svr.ult_fecha_atencion, svr.fecha_registro) END) AS tiempoAtencionSVr,
                            svr.fecha_registro,
                            svr.ult_usuario_atencion,
                            svr.ult_fecha_atencion,
                            sp.subProyectoDesc,
                            p.proyectoDesc,
                            u.nombre_completo AS ult_usu_aten
                       FROM solicitud_vale_reserva svr
                  LEFT JOIN usuario u ON svr.ult_usuario_atencion = u.id_usuario,
                            jefatura_sap js,
                            planobra po,
                            empresacolab ec,
                            fase f,
                            subproyecto sp,
                            proyecto p
                      WHERE svr.idJefaturaSap = js.idJefatura
                        AND svr.itemplan = po.itemplan
                        AND svr.idEmpresaColab = ec.idEmpresaColab
                        AND po.idFase = f.idFase
                        AND po.idSubProyecto = sp.idSubProyecto
                        AND sp.idProyecto = p.idProyecto
                        AND svr.itemplan = COALESCE(?,svr.itemplan)
                        AND svr.idJefaturaSap = COALESCE(?,svr.idJefaturaSap)
                        AND svr.idEmpresaColab = COALESCE(?,svr.idEmpresaColab)
                        AND po.idFase = COALESCE(?,po.idFase)
                        AND svr.estado = COALESCE(?,svr.estado) ";
        $result = $this->db->query($sql, array($itemplan,$idJefatura, $idEmpresaColab, $idFase, $tipoAtencion));
        return $result->result_array();
    }

    function getDetalleSolVr($codigoSolVr, $itemplan, $codigoPO) {
        $sql = "     SELECT svr.codigoSolVr,
                            svr.codigo_po,
                            svr.itemplan,
                            svr.vale_reserva,
                            svr.estado AS estadoCabecera,
                            (CASE WHEN svr.estado = 0 THEN 'PENDIENTE'
                                    WHEN svr.estado = 1 THEN 'PARCIALMENTE'
                                        WHEN svr.estado = 2 THEN 'VALIDADO'
                                        WHEN svr.estado = 3 THEN 'RECHAZADO'
                                END
                            ) AS estadoCabeceraDesc,
                            svd.idSolVrDet,
                            svd.codigo_material,
                            m.descrip_material,
                            svd.cantidadInicio AS cantidad,
                            svd.idTipoSolicitudVr,
                            tsv.nombreTipoSolicitud,
                            svd.flg_estado,
                            svd.send_rpa,
                            svd.comentario,
                            svd.cantidadFin,
                            svd.flg_adicion,
                            svd.costoMat
                       FROM solicitud_vale_reserva svr,
                            solicitud_vale_reserva_detalle svd,
                            tipo_solicitud_vr tsv,
                            material m
                      WHERE svr.codigoSolVr = svd.codigoSolVr
                        AND svd.idTipoSolicitudVr = tsv.id
                        AND svd.codigo_material = m.codigo_material
                        AND svr.codigoSolVr = ?
                        AND svr.itemplan = COALESCE(?,svr.itemplan)
                        AND svr.codigo_po = COALESCE(?,svr.codigo_po) ";
        $result = $this->db->query($sql, array($codigoSolVr, $itemplan, $codigoPO));
        return $result->result_array();
    }
    
    function updateDetalleSolVr($dataUpdate, $codigoSolVr, $codigoMaterial){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $this->db->where('codigoSolVr', $codigoSolVr);
            $this->db->where('codigo_material', $codigoMaterial);
            $this->db->update('solicitud_vale_reserva_detalle', $dataUpdate);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al modificar a solicitud_vale_reserva_detalle');
            }else{
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!';
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function updateDetallePoMat($dataUpdate){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            foreach($dataUpdate as $row) {
                $this->db->where('codigo_po', $row['codigo_po']);
                $this->db->where('codigo_material', $row['codigo_material']);
                $this->db->update('planobra_po_detalle_mat', $row);
                if($this->db->trans_status() === FALSE) {
                    throw new Exception('Error al modificar en la tabla planobra_po_detalle_mat');
                }else{
                    $data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se actualizó correctamente!';
                }
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function insertBatchDetallePoMat($arrayDetalleInsert) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $this->db->insert_batch('planobra_po_detalle_mat', $arrayDetalleInsert);
            if($this->db->affected_rows() <= 0) {
                throw new Exception('No se registro el detalle de la po');
            } else {
                $data['msj'] = 'Se registró correctamente';
                $data['error'] = EXIT_SUCCESS;
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function updateBatchDetalleSolicitudVr($dataUpdateSolDet, $dataLog){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $this->db->update_batch('solicitud_vale_reserva_detalle', $dataUpdateSolDet, 'idSolVrDet');
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al modificar la tabla solicitud_vale_reserva_detalle');
            }else{
                _log('paso bien el update batch');
                $this->db->insert_batch('log_solicitud_vr', $dataLog);
                if($this->db->affected_rows() <= 0) {
                    throw new Exception('Error al insertar en la tabla log_solicitud_vr');
                } else {
                    _log('paso bien el insert batch');
                    $data['msj'] = 'Se actualizó correctamente';
                    $data['error'] = EXIT_SUCCESS;
                }
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    public function updateSolicitudVr($arrayData,$codigoSolVr) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try {
            $this->db->where('codigoSolVr', $codigoSolVr);
            $this->db->update('solicitud_vale_reserva', $arrayData);
            if ($this->db->trans_status() === false) {
                throw new Exception('Hubo un error al actualizar la tabla solicitud_vale_reserva.');
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!';
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getCountExistMatInDetallePO($codigoPO, $codigoMaterial) {
        $sql = "SELECT COUNT(*) cantidad
                  FROM planobra_po_detalle_mat
                 WHERE codigo_po = ?
                   AND codigo_material = ? ";
        $result = $this->db->query($sql, array($codigoPO, $codigoMaterial));
        return $result->row()->cantidad;           
    }

    function getJefaturaSapCmb() {
        $sql = "   SELECT idJefatura,
	                      descripcion 
			         FROM jefatura_sap
			     GROUP BY idJefatura
			     ORDER BY descripcion ";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getAllEECCForGestionVr() {
        $sql = "   SELECT *
                     FROM empresacolab
                    WHERE idEmpresaColab NOT IN (0,5,6,12) 
                 ORDER BY empresaColabDesc
                    AND   estado = 1 ";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getPorcentajeByEstacionObra($cod_obra, $idEstacion) {
        $sql = "SELECT itemplan, idEstacion, porcentaje 
                FROM itemplanestacionavance 
                WHERE itemplan = ?
                AND idEstacion = ?";
        $result = $this->db->query($sql, array($cod_obra, $idEstacion));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }    
    }

    function hasSolVRPDTAtencionByItemCodPo($itemplan, $codigo_po) {
		$sql = " SELECT 
                    count(1) as cant
                FROM
                    solicitud_vale_reserva
                WHERE
                    itemplan = ?
                AND estado IN (0)
                AND codigo_po = ?";
		$result = $this->db->query($sql, array($itemplan, $codigo_po));
        return $result->row()->cant;  
	}
	
	function hasExpedienteFinalizacion($itemplan) {
		$sql = " SELECT 
                    count(1) as cant
                FROM
                    itemplan_expediente
                WHERE
                    itemplan = ?
                AND estado = 'ACTIVO'";
		$result = $this->db->query($sql, array($itemplan));
        return $result->row()->cant;  
	} 
    /** NUEVO 18.07.2022 */

    function getBandejaVRLight($itemplan, $idJefatura, $idEmpresaColab, $idFase, $tipoAtencion) {
        $sql = " SELECT svr.codigoSolVr,
                        svr.codigo_po,
                        svr.itemplan,
                        svr.vale_reserva,
                        ec.empresaColabDesc,
                        f.faseDesc,
                        svr.estado,
                        (CASE WHEN svr.estado = 0 THEN 'ATENCIÓN PENDIENTE'
                            WHEN svr.estado = 1 THEN 'ATENCIÓN PARCIAL'
                            WHEN svr.estado = 2 THEN 'ATENCIÓN TOTAL'
                            WHEN svr.estado = 3 THEN 'ATENCIÓN RECHAZADA'
                        END ) AS estadoDesc,    
                        svr.idUsuario,
                        svr.fecha_registro,
                        svr.ult_usuario_atencion,
                        svr.ult_fecha_atencion,
                        sp.subProyectoDesc,
                        p.proyectoDesc,
                        u.nombre_completo AS ult_usu_aten,
                        u2.nombre_completo AS usu_registro
                        FROM solicitud_vale_reserva svr
                        LEFT JOIN usuario u ON svr.ult_usuario_atencion = u.id_usuario
                        LEFT JOIN usuario u2 ON svr.idUsuario = u2.id_usuario,
                        jefatura_sap js,
                        planobra po,
                        empresacolab ec,
                        fase f,
                        subproyecto sp,
                        proyecto p
                WHERE svr.idJefaturaSap = js.idJefatura
                    AND svr.itemplan = po.itemplan
                    AND svr.idEmpresaColab = ec.idEmpresaColab
                    AND po.idFase = f.idFase
                    AND po.idSubProyecto = sp.idSubProyecto
                    AND sp.idProyecto = p.idProyecto                   
                    AND svr.itemplan = COALESCE(?,svr.itemplan)
                    AND svr.idJefaturaSap = COALESCE(?,svr.idJefaturaSap)
                    AND svr.idEmpresaColab = COALESCE(?,svr.idEmpresaColab)
                    AND po.idFase = COALESCE(?,po.idFase)
                    AND svr.estado = COALESCE(?,svr.estado)";
               
        $result = $this->db->query($sql, array($itemplan,$idJefatura, $idEmpresaColab, $idFase, $tipoAtencion));
        //log_message('error', $this->db->last_query());
        return $result->result_array();
    }
    
}
