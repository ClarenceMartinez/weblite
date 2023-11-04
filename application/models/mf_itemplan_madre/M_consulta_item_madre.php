<?php

class M_consulta_item_madre extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    }

    function getDataTablaItemMadre($ItemplanmMadre) {

        if ($ItemplanmMadre) {
            $sql = "SELECT
                            proyectoDesc,
                            subproyectoDesc AS subDesc,
                            i.itemplan_m,
                            DATE_FORMAT(de.fecha_recepcion,'%Y/%m/%d') fecha_registro,
                            i.nombre,
                            i.idPrioridad,
                            de.nombre_cliente,
                            de.numero_carta,
                            i.carta_pdf,
                            eecc.empresaColabDesc,
                            compra.codigo_solicitud,
                            compra.orden_compra,
                            FORMAT( i.costoEstimado, 2 ) costoEstimado,
                                CASE
                                WHEN i.idPrioridad = 1 THEN
                                'SI' ELSE 'NO' 
                            END AS prioridad 
                      FROM itemplan_madre_externa i
                INNER JOIN subproyecto s ON s.idSubProyecto = i.idSubProyecto
                INNER JOIN proyecto p ON p.idProyecto = s.idProyecto
                INNER JOIN empresacolab eecc ON eecc.idEmpresaColab = i.idEmpresaColab
                LEFT JOIN itemplan_madre_detalle_obras_publicas de ON de.itemplan = i.itemplan_m #INNER JOIN empresacolab eecc on eecc.
                LEFT JOIN itemplan_madre_solicitud_orden_compra compra ON compra.codigo_solicitud = i.solicitud_oc
                            WHERE
                            (
                            SELECT
                            COUNT(*) total
                                FROM
                            planobra po
                            INNER JOIN subproyecto sb ON po.idSubProyecto = sb.idSubProyecto
                            INNER JOIN proyecto pr ON sb.idProyecto = pr.idProyecto
                            INNER JOIN empresacolab em ON em.idEmpresaColab = po.idEmpresaColab
                            INNER JOIN estadoplan est ON est.idEstadoPlan = po.idEstadoPlan
                            LEFT JOIN itemplan_vali_madre vali ON po.itemPlan = vali.itemplan WHERE  i.itemplan_m=TRIM('$ItemplanmMadre') AND
                            po.itemplan_m = i.itemplan_m -- AND po.idEstadoPlan=7
                            AND ( ( CASE WHEN vali.estado_di THEN vali.estado_di ELSE 0 END ) + ( CASE WHEN vali.estado_ope THEN vali.estado_ope ELSE 0 END ) ) IN(0,2,3,5,6) 
                            AND NOT ( ( CASE WHEN vali.estado_di THEN vali.estado_di ELSE 0 END ) + ( CASE WHEN vali.estado_ope THEN vali.estado_ope ELSE 0 END ) ) = 4
                            )
                            ORDER BY i.fecha_registro DESC";
        } else {
            $sql = "SELECT
	proyectoDesc,
	subproyectoDesc AS subDesc,
	i.itemplan_m,
	DATE_FORMAT(de.fecha_recepcion,'%Y/%m/%d') fecha_registro,
	i.nombre,
	i.idPrioridad,
	de.nombre_cliente,
	de.numero_carta,
	i.carta_pdf,
	eecc.empresaColabDesc,
	compra.codigo_solicitud,
	compra.orden_compra,
	FORMAT( i.costoEstimado, 2 ) costoEstimado,
        CASE
		WHEN i.idPrioridad = 1 THEN
		'SI' ELSE 'NO' 
	END AS prioridad 
        FROM
	itemplan_madre i
	INNER JOIN subproyecto s ON s.idSubProyecto = i.idSubProyecto
	INNER JOIN proyecto p ON p.idProyecto = s.idProyecto
	INNER JOIN empresacolab eecc ON eecc.idEmpresaColab = i.idEmpresaColab
	LEFT JOIN itemplan_madre_detalle_obras_publicas de ON de.itemplan = i.itemplan_m #INNER JOIN empresacolab eecc on eecc.
	LEFT JOIN itemplan_madre_solicitud_orden_compra compra ON compra.codigo_solicitud = i.solicitud_oc
	WHERE
	(
	SELECT
	COUNT(*) total
        FROM
	planobra po
	INNER JOIN subproyecto sb ON po.idSubProyecto = sb.idSubProyecto
	INNER JOIN proyecto pr ON sb.idProyecto = pr.idProyecto
	INNER JOIN empresacolab em ON em.idEmpresaColab = po.idEmpresaColab
	INNER JOIN estadoplan est ON est.idEstadoPlan = po.idEstadoPlan
	LEFT JOIN itemplan_vali_madre vali ON po.itemPlan = vali.itemplan WHERE 
	po.itemplan_m = i.itemplan_m AND po.idEstadoPlan=7
    AND ( ( CASE WHEN vali.estado_di THEN vali.estado_di ELSE 0 END ) + ( CASE WHEN vali.estado_ope THEN vali.estado_ope ELSE 0 END ) ) IN(0,2,3,5,6) 
	AND NOT ( ( CASE WHEN vali.estado_di THEN vali.estado_di ELSE 0 END ) + ( CASE WHEN vali.estado_ope THEN vali.estado_ope ELSE 0 END ) ) = 4
	)
	ORDER BY i.fecha_registro DESC";
        }

        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getSAPdetalle($pep) {
        $Query = "SELECT * FROM sap_detalle WHERE pep1='$pep';";
        $result = $this->db->query($Query, array());
        return $result->result_array();
    }

    function hijosItemMadreDetalle($itemplan_madre) {
        $Query = "SELECT
	po.*,
	sb.subProyectoDesc,
	pr.proyectoDesc,
	em.empresaColabDesc,
	est.estadoPlanDesc,
	vali.estado_di,
        vali.estado_ope
        FROM
	planobra po
	INNER JOIN subproyecto sb ON po.idSubProyecto = sb.idSubProyecto
	INNER JOIN proyecto pr ON sb.idProyecto = pr.idProyecto
	INNER JOIN empresacolab em ON em.idEmpresaColab = po.idEmpresaColab
	INNER JOIN estadoplan est ON est.idEstadoPlan = po.idEstadoPlan
	LEFT JOIN itemplan_vali_madre vali ON po.itemPlan = vali.itemplan WHERE
	po.itemplan_m = '$itemplan_madre' AND po.idEstadoPlan=7
        AND ( ( CASE WHEN vali.estado_di THEN vali.estado_di ELSE 0 END ) + ( CASE WHEN vali.estado_ope THEN vali.estado_ope ELSE 0 END ) ) IN(0,2,3,5,6) 
	AND NOT ( ( CASE WHEN vali.estado_di THEN vali.estado_di ELSE 0 END ) + ( CASE WHEN vali.estado_ope THEN vali.estado_ope ELSE 0 END ) ) = 4;";
        $result = $this->db->query($Query, array());
        return $result->result();
    }
	
	function getHijosValidar($itemplan_madre) {
		$sql = "SELECT 	po.itemplan, 
                        po.flg_valida_diseno,
                        po.comentario_valida_diseno,
                        po.idEstadoPlan,
                        s.subProyectoDesc, 
                        p.proyectoDesc, 
                        e.empresaColabDesc, 
                        es.estadoPlanDesc,                      
                        po.cantFactorPlanificado as cant_uip,
                        d.path_expediente_diseno,
                        SUM(CASE WHEN ppo.idEstacion = 1 THEN ppo.costo_total ELSE 0 END) as total_diseno,
                        SUM(CASE WHEN ppo.idEstacion <> 1 THEN ppo.costo_total ELSE 0 END) as total_operaciones
                FROM planobra po LEFT JOIN diseno d ON po.itemplan = d.itemplan and d.idEstacion = 5,
                        subproyecto s,
                        proyecto p,
                        empresacolab e,
                        estadoplan es,
                        planobra_po ppo
                WHERE itemplan_m = ?
                AND po.idSubProyecto = s.idSubProyecto
                AND s.idProyecto = p.idProyecto
                AND e.idEmpresaColab = po.idEmpresaColab
                AND es.idEstadoPlan = po.idEstadoPlan
                AND ppo.itemplan = po.itemplan
                AND ppo.estado_po NOT IN (7,8) 
                AND flg_tipo_area = 2
                GROUP BY po.itemplan,
						po.flg_valida_diseno,
                        po.comentario_valida_diseno,
                        po.idEstadoPlan,
                        s.subProyectoDesc, 
                        p.proyectoDesc, 
                        e.empresaColabDesc, 
                        es.estadoPlanDesc,                      
						cant_uip,
                        d.path_expediente_diseno";
		$result = $this->db->query($sql, array($itemplan_madre));
        return $result->result();
	}

    function montoToltal($itemplan) {
        $sql = "SELECT DISTINCT 
                      ppo.codigo_po,
                      e.estacionDesc,
                      a.tipoArea,
                      poe.estado,
                      ppo.pep2,
                      ppo.grafo,
                      CASE WHEN flg_tipo_area = 1 THEN ppo.costo_total 
                           ELSE 0 END AS total_mat,
                      CASE WHEN flg_tipo_area = 2 THEN ppo.costo_total 
                           ELSE 0 END AS total_mo
                  FROM planobra_po ppo,
                       estacion e,
                       po_estado poe,
                       detalleplan dp,
                       subproyectoestacion sp,
                       estacionarea ea,
                	   area a
                WHERE e.idEstacion      = ppo.idEstacion
                  AND poe.idPoEstado    = ppo.estado_po
                  AND dp.poCod          = ppo.codigo_po
                  AND dp.itemplan       = ppo.itemplan
                  AND sp.idEstacionarea = ea.idEstacionArea
                  AND a.idArea          = ea.idArea
                  AND ppo.estado_po    <> 8
                  AND sp.idSubProyectoEstacion = dp.idSubProyectoEstacion
                  AND ppo.itemplan = COALESCE(?, ppo.itemplan)
                  UNION ALL
                  SELECT we.ptr,
                        e.estacionDesc,
                        we.desc_area,
                        ep.estadoPoDesc,
                        we.pep,
                        we.grafo,
                        valoriz_material AS total_mat,
                        valoriz_m_o AS total_mo 
                    FROM web_unificada we,
                        detalleplan dp,
                        estadoptr ep,
                        estacion e,
                        estacionarea ea,
                        subproyectoestacion se
                    WHERE we.ptr                 = dp.poCod
                    AND we.idEstadoPtr           = ep.idEstadoPo
                    AND e.idEstacion             = ea.idEstacion 
                    AND ea.idEstacionArea        = se.idEstacionArea
                    AND dp.idSubProyectoEstacion = se.idSubProyectoEstacion
                    AND ep.idEstadoPo           <> 6
                    AND dp.itemPlan             = COALESCE(?, dp.itemPlan)";
        $result = $this->db->query($sql, array($itemplan, $itemplan));
        return $result->result();
    }

    function updateConPrioridad($objReg, $itemplanM) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->trans_begin();
            $this->db->where('itemplan_m', $itemplanM);
            $this->db->update('itemplan_madre', $objReg);
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Hubo un error al actualizar la carta del Itemplan Madre.');
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizo correctamente!';
                $this->db->trans_commit();
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }

    function cantidadItemplanHijos($Itemplan) {
        $Query = 'SELECT
	COUNT( * ) total 
        FROM
	planobra po
	INNER JOIN subproyecto sb ON po.idSubProyecto = sb.idSubProyecto
	INNER JOIN proyecto pr ON sb.idProyecto = pr.idProyecto
	INNER JOIN empresacolab em ON em.idEmpresaColab = po.idEmpresaColab
	INNER JOIN estadoplan est ON est.idEstadoPlan = po.idEstadoPlan
	LEFT JOIN itemplan_vali_madre vali ON po.itemPlan = vali.itemplan 
        WHERE
	po.itemplan_m = ?
	-- AND po.idEstadoPlan = 7 
	AND ( ( CASE WHEN vali.estado_di THEN vali.estado_di ELSE 0 END ) + ( CASE WHEN vali.estado_ope THEN vali.estado_ope ELSE 0 END ) ) IN(0,2,3,5,6) 
	AND NOT ( ( CASE WHEN vali.estado_di THEN vali.estado_di ELSE 0 END ) + ( CASE WHEN vali.estado_ope THEN vali.estado_ope ELSE 0 END ) ) = 4;';
        $result = $this->db->query($Query, array($Itemplan));
        $idEstadoPlan = $result->row()->total;
        return $idEstadoPlan;
    }

    function updateConPrioridadDetalle($objReg, $itemplanM) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->trans_begin();
            $this->db->where('itemplan', $itemplanM);
            $this->db->update('itemplan_madre_detalle_obras_publicas', $objReg);
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Hubo un error al actualizar la carta del Itemplan Madre.');
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizo correctamente!';
                $this->db->trans_commit();
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }

    public function getEditItemplanMadre($ItemplanmMadre) {
        $sql = "SELECT
        i.idSubProyecto,
	proyectoDesc,
	subproyectoDesc AS subDesc,
	i.itemplan_m,
	DATE_FORMAT(de.fecha_recepcion,'%Y/%m/%d') fecha_registro,
	i.nombre,
	i.idPrioridad,
	de.nombre_cliente,
	de.numero_carta,
	i.carta_pdf,
	eecc.empresaColabDesc,
	compra.codigo_solicitud,
	compra.orden_compra,
	FORMAT( i.costoEstimado, 2 ) costoEstimado,
        CASE
        WHEN i.idPrioridad = 1 THEN
		'SI' ELSE 'NO' 
	END AS prioridad 
        FROM
	itemplan_madre i
	INNER JOIN subproyecto s ON s.idSubProyecto = i.idSubProyecto
	INNER JOIN proyecto p ON p.idProyecto = s.idProyecto
	INNER JOIN empresacolab eecc ON eecc.idEmpresaColab = i.idEmpresaColab
	LEFT JOIN itemplan_madre_detalle_obras_publicas de ON de.itemplan = i.itemplan_m #INNER JOIN empresacolab eecc on eecc.
	LEFT JOIN itemplan_madre_solicitud_orden_compra compra ON compra.codigo_solicitud = i.solicitud_oc
        WHERE i.itemplan_m=TRIM('$ItemplanmMadre')";
        $result = $this->db->query($sql);
        return $result->result();
    }

    function validarItemplan($dataInsert) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->insert('itemplan_vali_madre', $dataInsert);
            if ($this->db->affected_rows() != 1) {
                $data['error'] = EXIT_ERROR;
                $data['msj'] = 'Error al insertar el itemfault';
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se registro correctamente correctamente!';
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function cantidadItemplanVali($Itemplan) {
        $Query = 'SELECT COUNT(*) total FROM itemplan_vali_madre WHERE itemplan = ?';
        $result = $this->db->query($Query, array($Itemplan));
        $idEstadoPlan = $result->row()->total;
        return $idEstadoPlan;
    }

    function UpdatevalidarItemplan($objReg, $itemplanM) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->where('itemplan', $itemplanM);
            $this->db->update('itemplan_vali_madre', $objReg);
            if ($this->db->trans_status() === FALSE) {
                $data['error'] = EXIT_ERROR;
                $data['msj'] = 'Hubo un error al actualizar la validacion ' . $itemplanM . print_r($objReg, true);
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizo correctamente!';
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function updateItemplanMadre($itemplan_m, $dataUpdate) {
        $this->db->where('itemplan_m', $itemplan_m);
        $this->db->update('itemplan_madre', $dataUpdate);
        if ($this->db->affected_rows() == 0) {
            $data['error'] = EXIT_ERROR;
            $data['msj'] = 'Error al insertar el itemfault';
        } else {
            $data['error'] = EXIT_SUCCESS;
            $data['msj'] = 'Se registro correctamente correctamente!';
        }
        return $data;
    }

    function getCountPendienteValidaHijo($itemplan_m) {
        $sql = "   SELECT itemplan_m,
                          CASE WHEN sum_valida_diseno = cant_hijo THEN 1
                               ELSE 0 END flg_valida_diseno_completo,
						   CASE WHEN sum_valida_operacion = cant_hijo THEN 1
                               ELSE 0 END flg_valida_operacion_completo
                     FROM (
							SELECT itemplan_m,
								   SUM(CASE WHEN flg_valida_diseno = 1 THEN 1 ELSE 0 END) sum_valida_diseno,
                                   SUM(CASE WHEN flg_valida_operacion = 1 THEN 1 ELSE 0 END) sum_valida_operacion,
								   COUNT(1) cant_hijo
							  FROM planobra
							 WHERE itemplan_m = ?
							   AND idEstadoPlan <> 6
							 GROUP BY itemplan_m
						  )t";
        $result = $this->db->query($sql, array($itemplan_m));
        return $result->row_array();
    }

    function haveDisenoValidaIpMadreHijos($itemplan_m) {
        $sql = "SELECT itemplan_m, 
                        (CASE WHEN SUM(CASE WHEN flg_valida_diseno = 1 THEN 1 ELSE 0 END) 	=	COUNT(1)	THEN 1 ELSE 0 END) as diseno_completo
                FROM planobra
                WHERE itemplan_m = ?
                    AND idEstadoPlan <> 6
                GROUP BY itemplan_m;";
        $result = $this->db->query($sql, array($itemplan_m));
        if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
    }
	
	function updatePoToValidadoBrItemPlanMadre($itemplan_m, $idUsuario) {
		$sql = "INSERT INTO log_planobra_po(itemplan, idUsuario, fecha_registro, idPoestado, controlador, codigo_po)
                SELECT po.itemplan, ?, NOW(), 5, 'VALIDADO DESDE VALID ITEM MADRE DISENO', codigo_po
                FROM planobra po, planobra_po ppo
                WHERE ppo.itemplan = po.itemplan
                AND ppo.idEstacion = 1
                AND ppo.estado_po  = 1
                AND po.itemplan_m  = ?";
		$this->db->query($sql, array($idUsuario, $itemplan_m));
		if($this->db->trans_status() === FALSE) {
			$data['error'] = EXIT_ERROR;
			$data['msj'] = 'No se inserto el log po!';
		} else {
			$sql = "UPDATE planobra po, planobra_po ppo
                        SET ppo.estado_po = 5
                    WHERE ppo.itemplan = po.itemplan
                        AND ppo.idEstacion = 1
                        AND ppo.estado_po = 1
                        AND po.itemplan_m = ?";
            $this->db->query($sql, array($itemplan_m));		
            if($this->db->trans_status() === FALSE) {
                $data['error'] = EXIT_ERROR;
                $data['msj'] = 'No se actualizo el estado po!';
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'correcto!';
            }
		}
		return $data;
	}

    function getCostoTotalPoValidadasDisenoByItemplanM($itemplan_m) {
        $sql = "SELECT po.itemplan_m, sum(ppo.costo_total) as total_diseno_vali
                    FROM planobra po, planobra_po ppo
                WHERE ppo.itemplan = po.itemplan
                    AND ppo.idEstacion = 1
                    AND ppo.estado_po  = 5
                    AND po.idEstadoPlan not in (6)
                    AND po.itemplan_m  = ?";
        $result = $this->db->query($sql, array($itemplan_m));
        if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
    }

    function getInfoItemplanMadre($itemplan_m) {
        $sql = "SELECT * 
                  FROM  itemplan_madre
                WHERE   itemplan_m    = ?";
        $result = $this->db->query($sql, array($itemplan_m));
        if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
    }
	 
    function generarSolicitudItemPlanMadrePanEdicion($itemplan_madre, $costo_mo, $idUsuario) {
        $sql = "SELECT fn_create_solicitud_oc_itemplan_madre_edi(?, ?, ?) AS flgValida";
        $result = $this->db->query($sql, array($itemplan_madre, $costo_mo, $idUsuario));
        return $result->row_array()['flgValida'];
    }
	
	function generarSolicitudItemPlanMadrePanCerti($itemplan_madre, $costo_mo, $idUsuario, $estadoSolicitud) {
        $sql = "SELECT fn_create_solicitud_oc_itemplan_madre_cert(?,?,?,?) AS flgValida";
        $result = $this->db->query($sql, array($itemplan_madre, $costo_mo, $idUsuario, $estadoSolicitud));
        return $result->row_array()['flgValida'];
    }

    function generarSolicitudCreacionOCItemplanPersonalizado($itemplan, $costo_mo, $idUsuario) {
        $sql = "SELECT fn_create_solicitud_oc_personalizado(?,?,?) AS flgValida";
        log_message('error', $this->db->last_query());
        $result = $this->db->query($sql, array($itemplan, $costo_mo, $idUsuario));
        return $result->row_array()['flgValida'];
    }

    function getCostoPqtFoFromFtthHijo($itemplan) {
        $sql = "SELECT  SUM(costo_total) as total_mo
                FROM    planobra_po 
                WHERE   itemplan        = ? 
                AND     estado_po       = 1
                AND     flg_tipo_area   = 2
                AND     idEstacion not in (1)";
        $result = $this->db->query($sql, array($itemplan));
        if($result->row() != null) {
	        return $result->row_array()['total_mo'];
	    } else {
	        return null;
	    }
    }
    
    function getInfoDisenoToCopy($itemplan, $idEstacion) {
	    $sql = "SELECT  d.*
                FROM    diseno d
                WHERE   d.itemplan = ?
                AND     d.idEstacion = ?";
	    $result = $this->db->query($sql, array($itemplan, $idEstacion));
	    if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function rechazarItemplanHijo($itemplan, $idEstacion, $dataPlanObra, $dataDisenoRecha, $dataDiseno) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {	
            $this->db->where('itemplan', $itemplan);
            $this->db->update('planobra', $dataPlanObra);
            if($this->db->affected_rows() <= 0) {
                $this->db->trans_rollback();            
                throw new Exception('No se actualizo Plan Obra');
            } else {
                $this->db->insert('diseno_rechazado', $dataDisenoRecha);
                if($this->db->affected_rows() <= 0) {
                    $this->db->trans_rollback();                     
                    throw new Exception('No se registro en diseno_rechazo');
                } else {
                    $this->db->where('itemplan', $itemplan);
                    $this->db->where('idEstacion', $idEstacion);
                    $this->db->update('diseno', $dataDiseno);
                    if($this->db->affected_rows() <= 0) {
                        $this->db->trans_rollback();                       
                        throw new Exception('No se actualizo diseno');
                    } else {
                        $this->db->where('itemplan', $itemplan);
                        $this->db->where('idEstacion', $idEstacion);
                        $this->db->delete('entidad_itemplan_estacion');
                        if ($this->db->trans_status() === false) {
                            $this->db->trans_rollback();
                            throw new Exception('Hubo un error al eliminar el entidad_estacion.');
                        }else{
                            $this->db->trans_commit();
                            $data['msj'] = 'Se actualizo correctamente!';
                            $data['error'] = EXIT_SUCCESS;
                        }
                    }
                }
                
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }


}
