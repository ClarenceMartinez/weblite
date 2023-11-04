<?php

class M_detalle_consulta extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getInfoPartidaByCodPartida($codigoPartida){
	    $sql = " SELECT pa.*
                   FROM partida pa
                  WHERE pa.codigoPartida = ? ";
	    $result = $this->db->query($sql,array($codigoPartida));
	    return $result->row_array();
	}

    function getSolicitudPartidasAdicionales($itemplan, $idEstacion){
	    $sql = "       SELECT tmp.*, 
                              (usu_1.nombre_completo) AS usuario_nivel_1, 
                              (usu_2.nombre_completo) AS usuario_nivel_2 
                         FROM pqt_solicitud_aprob_partidas_adicionales tmp
                    LEFT JOIN usuario usu_1 ON usu_1.id_usuario = tmp.usua_val_nivel_1
                    LEFT JOIN usuario usu_2 ON usu_1.id_usuario = tmp.usua_val_nivel_2
                        WHERE tmp.itemplan = ?
                          AND tmp.idEstacion = ?
                          AND tmp.activo = 1
                        LIMIT 1 ";
	    $result = $this->db->query($sql,array($itemplan, $idEstacion));
	    return $result->row_array();
	}

    function getSolicitudPartidasAdicionalesNoEstacion($itemplan){
	    $sql = "       SELECT tmp.*, 
                              (usu_1.nombre_completo) AS usuario_nivel_1, 
                              (usu_2.nombre_completo) AS usuario_nivel_2 
                         FROM pqt_solicitud_aprob_partidas_adicionales tmp
                    LEFT JOIN usuario usu_1 ON usu_1.id_usuario = tmp.usua_val_nivel_1
                    LEFT JOIN usuario usu_2 ON usu_1.id_usuario = tmp.usua_val_nivel_2
                        WHERE tmp.itemplan = ?                         
                          AND tmp.activo = 1
                        LIMIT 1 ";
	    $result = $this->db->query($sql,array($itemplan));
	    return $result->row_array();
	}

    function getInfoPoMoPqtLiquidadoByItemplan($itemplan,$idEstacion) {
        $sql = " SELECT ppo.codigo_po,
                        ppo.costo_total,
                        ppo.itemplan,
                        ppo.flg_tipo_area,
                        ppo.estado_po
                   FROM planobra_po ppo
                  WHERE ppo.flg_tipo_area = 2
                    AND ppo.estado_po IN (4)
                    AND ppo.isPoPqt = 1
                    AND ppo.itemplan  = ?
                    AND ppo.idEstacion = ? ";
        $result = $this->db->query($sql, array($itemplan, $idEstacion));
		return $result->row_array();
    }

    function getEstaTrabajadasFO($itemplan) {
	    $sql = " SELECT DISTINCT idEstacion
                   FROM planobra_po 
                  WHERE estado_po IN (4,5) 
	                AND idEstacion IN (5,15,16,6,13,14) 
	                AND itemplan = ? ";
	    $result = $this->db->query($sql, array($itemplan));
	    return $result->result();
	}
	
	function getEstaTrabajadasCOAX($itemplan) {
	    $sql = " SELECT DISTINCT idEstacion
                   FROM planobra_po 
                  WHERE estado_po IN (4,5)
	                AND idEstacion IN (2,7,4,9,10,8,3,12,18)
	                AND itemplan = ? ";
	    $result = $this->db->query($sql, array($itemplan));
	    return $result->result();
	}

    function sendValidarPartidasAdicionales($itemplan, $idEstacion, $codigoPO, $dataSolValidacion, $expediente, $arrayPartidasInsert) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();
            $this->db->where('itemplan',   $itemplan);
            $this->db->where('idEstacion', $idEstacion);
            $this->db->where('activo', 1);
            $this->db->update('pqt_solicitud_aprob_partidas_adicionales', array('activo' => 0));
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al desactivar solicitudes pendientes.');
            }else{
                $this->db->insert('pqt_solicitud_aprob_partidas_adicionales', $dataSolValidacion);
                if($this->db->affected_rows() <= 0) {
                    throw new Exception('Error al insertar en pqt_solicitud_aprob_partidas_adicionales');
                }else{
                    $this->db->insert_batch('itemplan_expediente', $expediente);
                    if($this->db->trans_status() === FALSE) {
                        throw new Exception('Error al insertar en itemplan_expediente');
                    }else{
                        $sql = " DELETE pdmo FROM planobra_po_detalle_mo pdmo JOIN partida p ON pdmo.codigoPartida = p.codigoPartida AND p.flg_tipo = 3 WHERE pdmo.codigo_po = ?";
                        $result = $this->db->query($sql, array($codigoPO));
                        if($this->db->trans_status() === FALSE) {
                            throw new Exception('Error al modificar en planobra_po_detalle_mo');
                        }else{
                            $this->db->insert_batch('planobra_po_detalle_mo', $arrayPartidasInsert);
                                if($this->db->trans_status() === FALSE) {
                                throw new Exception('Error al insertar en planobra_po_detalle_mo');
                            }else{
                                $sql = " UPDATE planobra_po SET costo_total = (SELECT SUM(montoFinal) FROM planobra_po_detalle_mo WHERE codigo_po = ?) WHERE codigo_po = ?";
                                $result = $this->db->query($sql, array($codigoPO,$codigoPO));
                                if($this->db->trans_status() === FALSE) {
                                    $this->db->trans_rollback();
                                    throw new Exception('Error al actualizar en planobra_po');
                                }else{
                                    $data['error'] = EXIT_SUCCESS;
                                    $data['msj'] = 'Se actualizó correctamente!';
                                    $this->db->trans_commit();	                        
                                }	                
                            }
                        }                                          
                    }
                }
            } 
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function validarMontoForCertificacion($itemplan,$tipoBucle) {

        if($tipoBucle == 1){//OVERLAY
            $sum = ' SUM(CASE WHEN flg_tipo_area = 2 and isPoPqt = 1 THEN  (costo_total)  ELSE 0 END) AS mo_pqt,
                     SUM(CASE WHEN flg_tipo_area = 2 and isPoPqt is null THEN  (costo_total)  ELSE 0 END) AS mo_nopqt ';
        }else{//NUEVO
            $sum = ' SUM(CASE WHEN flg_tipo_area = 2 THEN  (costo_total)  ELSE 0 END) AS manobra ';
        }
        $sql = "   SELECT itemplan,
                          SUM(CASE WHEN flg_tipo_area = 1 THEN  (costo_total)  ELSE 0 END) AS material,
                          ".$sum."
                     FROM planobra_po 
                    WHERE estado_po NOT IN (6,7,8) 
                      AND itemplan = ?
                 GROUP BY itemplan ";
        $result = $this->db->query($sql, array($itemplan));
        if($result->num_rows() > 0) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    function getIdSolicitudByItemplan($itemPlan) {
        $sql = "  SELECT id_solicitud,idEstacion
                    FROM pqt_solicitud_aprob_partidas_adicionales 
                   WHERE activo = 1
                     AND estado NOT IN (3,4)
                     AND itemplan = ?";
        $result = $this->db->query($sql, array($itemPlan));
        if($result->num_rows() > 0) {
            return $result->row_array();
        } else {
            return null;
        }
    }

        
    function validateNivel1($dataUpdate, $idSolicitud){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $this->db->where('id_solicitud', $idSolicitud);
            $this->db->update('pqt_solicitud_aprob_partidas_adicionales', $dataUpdate);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al modificar a pqt_solicitud_aprob_partidas_adicionales');
            }else{
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!';
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getInfoSolCreacionByItem($itemplan){
	    $sql = " SELECT soc.*, po.itemplan, po.idEstadoPlan, po.costo_unitario_mo, po.posicion
                   FROM solicitud_orden_compra soc,
                	    planobra po
                  WHERE soc.codigo_solicitud = po.solicitud_oc
                    AND po.orden_compra = soc.orden_compra
                    AND soc.estado = 2
                	AND po.itemplan = ?
                  LIMIT 1 ";
	    $result = $this->db->query($sql,array($itemplan));
        log_message('error',$this->db->last_query());
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function getInfoSolCreacionByItemDiseno($itemplan){
        $sql = " SELECT soc.*, po.itemplan, po.idEstadoPlan, po.costo_unitario_mo, po.posicion
                   FROM solicitud_orden_compra_diseno soc,
                        planobra po
                  WHERE soc.codigo_solicitud = po.solicitud_oc_diseno
                    AND po.orden_compra_diseno = soc.orden_compra
                    AND soc.estado = 2
                    AND po.itemplan = ?
                  LIMIT 1 ";
        $result = $this->db->query($sql,array($itemplan));
        log_message('error',$this->db->last_query());
        if($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    function getPOToValidateToFOByItemplan($itemPlan) {
	    $sql = " SELECT * 
                   FROM planobra_po
	              WHERE itemplan = ?
            	    AND idEstacion IN (5,6)
            	    AND estado_po = 4";
	    $result = $this->db->query($sql, array($itemPlan));
	    return $result->result();
	}

    function getPOToValidateFTTHToFOByItemplan($itemPlan) {
	    $sql = " SELECT * 
                   FROM planobra_po
	              WHERE itemplan = ?
            	    AND idEstacion NOT IN (1)
            	    AND estado_po = 4";
	    $result = $this->db->query($sql, array($itemPlan));
	    return $result->result();
	}
	
	function getPOToValidateToCOAXByItemplan($itemPlan) {
	    $sql = "  SELECT * 
                    FROM planobra_po
	               WHERE itemplan = ?
            	     AND idEstacion IN (2,3,4,7)
            	     AND estado_po = 4";
	    $result = $this->db->query($sql, array($itemPlan));
	    return $result->result();
	}

    function validarEstacionFOPqt2NivelsINoc($arrayPoInserLogPo, $itemplan, $arrayPoUpdate, $idEstacion, $dataSolicitud, $dataExpediente){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{      
	        $this->db->insert_batch('log_planobra_po', $arrayPoInserLogPo);
	        if($this->db->trans_status() === FALSE) {
	            throw new Exception('Error al insertar en log_planobra_po');
	        }else{  
                $this->db->update_batch('planobra_po', $arrayPoUpdate, 'codigo_po');
                if($this->db->trans_status() === FALSE) {
                    throw new Exception('Error al actualizar en la tabla planobra_po');
                }else{
                    $this->db->where('itemplan', $itemplan);
                    $this->db->where('idEstacion', $idEstacion);
                    $this->db->where('estado', 1);//suponiendo que solo cuenta con una solicitud en ese itemplan estacion en estado 1
                    $this->db->update('pqt_solicitud_aprob_partidas_adicionales', $dataSolicitud);
                    if($this->db->trans_status() === FALSE) {
                        throw new Exception('Error al actualizar en la tabla pqt_solicitud_aprob_partidas_adicionales');
                    }else{
                        $this->db->where('idEstacion', $idEstacion);
                        $this->db->where('itemplan', $itemplan);
                        $this->db->update('itemplan_expediente', $dataExpediente);
                        if($this->db->trans_status() === FALSE) {
                            throw new Exception('Error al actualizar en la tabla itemplan_expediente');
                        }else{
                            $data['error'] = EXIT_SUCCESS;
                            $data['msj'] = 'Se actualizó correctamente!!';
                        }
                    }
    	        }
	        }
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function getDataToSolicitudEdicionCertiOC($itemplan){
	    $sql = "    SELECT po.itemplan,
                           po.costo_unitario_mat,
                           po.costo_unitario_mo, 
                           po.costo_unitario_mo_diseno, 
                           tb.total
                      FROM planobra po    
                 LEFT JOIN (
							    SELECT ppo.itemplan, SUM(costo_total) AS total 
                                  FROM planobra_po ppo, planobra po, subproyecto sp
							     WHERE po.itemplan = ppo.itemplan
							       AND po.idSubProyecto = sp.idsubProyecto
							       AND ppo.itemplan = ?
							       AND ppo.estado_po IN (4,5)
							       AND ppo.flg_tipo_area = 2
							       AND (CASE WHEN sp.idProyecto = 52 THEN ppo.idEstacion NOT IN (1) ELSE TRUE END)
							  GROUP BY ppo.itemplan
                           ) AS tb
                        ON po.itemplan = tb.itemplan
                     WHERE po.itemplan = ?
                     LIMIT 1 ";
	    $result = $this->db->query($sql,array($itemplan, $itemplan));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function getDataToSolicitudEdicionCertiOCDiseno($itemplan){
        $sql = "    SELECT po.itemplan,
                           po.costo_unitario_mat,
                           po.costo_unitario_mo, 
                           po.costo_unitario_mo_diseno, 
                           tb.total
                      FROM planobra po    
                 LEFT JOIN (
                                SELECT ppo.itemplan, SUM(costo_total) AS total 
                                  FROM planobra_po ppo, planobra po, subproyecto sp
                                 WHERE po.itemplan = ppo.itemplan
                                   AND po.idSubProyecto = sp.idsubProyecto
                                   AND ppo.itemplan = ?
                                   AND ppo.estado_po IN (4,5)
                                   AND ppo.flg_tipo_area = 2
                                   AND sp.idProyecto = 52
                                   -- AND (CASE WHEN sp.idProyecto = 52 THEN ppo.idEstacion NOT IN (1) ELSE TRUE END)
                              GROUP BY ppo.itemplan
                           ) AS tb
                        ON po.itemplan = tb.itemplan
                     WHERE po.itemplan = ?
                     LIMIT 1 ";
        $result = $this->db->query($sql,array($itemplan, $itemplan));
        if($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    function validarEstacionFOPqt2Nivel($arraySolicitud, $arrayItemXSolicitud, $dataItemplan, $itemplan, $arrayPoInserLogPo, $arrayPoUpdate, $idEstacion, $dataSolicitud, $dataExpediente){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->insert_batch('solicitud_orden_compra', $arraySolicitud);
	        if($this->db->affected_rows() == 0) {
	            throw new Exception('Error al insertar en solicitud_orden_compra');
	        }else{
	            $this->db->insert_batch('itemplan_x_solicitud_oc', $arrayItemXSolicitud);
	            if($this->db->affected_rows() == 0) {
	                throw new Exception('Error al insertar en itemplan_x_solicitud_oc');
	            }else{
    	            $this->db->where('itemplan', $itemplan);
    	            $this->db->update('planobra', $dataItemplan);	
    	            if($this->db->trans_status() === FALSE) {
    	                throw new Exception('Error al modificar el planobra');
    	            }else{
        	            $this->db->insert_batch('log_planobra_po', $arrayPoInserLogPo);
            	        if($this->db->trans_status() === FALSE) {
            	            throw new Exception('Error al insertar en log_planobra_po');
            	        }else{
            	            $this->db->update_batch('planobra_po', $arrayPoUpdate, 'codigo_po');            	             
            	            if($this->db->trans_status() === FALSE) {
            	                throw new Exception('Error al modificar en la tabla planobra_po');
            	            }else{
            	                $this->db->where('itemplan', $itemplan);
            	                $this->db->where('idEstacion', $idEstacion);
            	                $this->db->where('estado', 1);//suponiendo que solo cuenta con una solicitud en ese itemplan estacion en estado 1
            	                $this->db->update('pqt_solicitud_aprob_partidas_adicionales', $dataSolicitud);
            	                if($this->db->trans_status() === FALSE) {
            	                    throw new Exception('Error al modificar a pqt_solicitud_aprob_partidas_adicionales');
            	                }else{ 
            	                    $this->db->where('idEstacion', $idEstacion);
                                    $this->db->where('itemplan', $itemplan);
                                    $this->db->update('itemplan_expediente', $dataExpediente);                	             
                                    if($this->db->trans_status() === FALSE) {
                                        throw new Exception('Error al actualizar a itemplan_expediente');
                                    }else{
                                        $data['error'] = EXIT_SUCCESS;
                                        $data['msj'] = 'Se actualizó correctamente!';
                                    }
            	                }
                            }	            
            	        }
    	            }
	            }
	        }
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

        function validarEstacionFOPqt2NivelDiseno($arraySolicitud, $arrayItemXSolicitud, $dataItemplan, $itemplan){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->insert_batch('solicitud_orden_compra_diseno', $arraySolicitud);
            if($this->db->affected_rows() == 0) {
                throw new Exception('Error al insertar en solicitud_orden_compra_diseno');
            }else{
                $this->db->insert_batch('itemplan_x_solicitud_oc_diseno', $arrayItemXSolicitud);
                if($this->db->affected_rows() == 0) {
                    throw new Exception('Error al insertar en itemplan_x_solicitud_oc_diseno');
                }else{
                    $this->db->where('itemplan', $itemplan);
                    $this->db->update('planobra', $dataItemplan);   
                    if($this->db->trans_status() === FALSE) {
                        throw new Exception('Error al modificar el planobra');
                    }else{
                        $data['error'] = EXIT_SUCCESS;
                        $data['msj'] = 'Se actualizó correctamente!';
                    }
                }
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getInfoSiropeByItemplan($itemplan) {
	    $sql = "   SELECT 
                          po.itemplan, po.idSubProyecto, po.has_sirope_fo, po.has_sirope_fo_fecha, po.ult_estado_sirope, po.ult_codigo_sirope,
                          po.has_sirope_diseno, po.has_sirope_diseno_fecha, po.ult_codigo_sirope_ac, po.ult_estado_sirope_ac,
                          po.has_sirope_ac, po.fecha_sirope_ac, po.has_sirope_ac_diseno, po.fecha_sirope_ac_diseno,
                          lt1.codigo_ot AS ot_prin, lt2.codigo_ot AS ot_ac, lt3.codigo_ot AS ot_coax,
                          po.has_sirope_coax, po.has_sirope_coax_fecha, po.ult_estado_sirope_coax, po.ult_codigo_sirope_coax, 
                          lt4.codigo_ot AS ot_mn
                     FROM planobra po
                    LEFT JOIN log_tramas_sirope lt1 ON lt1.codigo_ot = CONCAT(po.itemplan,'FO')   AND lt1.estado = 1
                    LEFT JOIN log_tramas_sirope lt2 ON lt2.codigo_ot = CONCAT(po.itemplan,'AC')   AND lt2.estado = 1
                    LEFT JOIN log_tramas_sirope lt3 ON lt3.codigo_ot = CONCAT(po.itemplan,'COAX') AND lt3.estado = 1
                    LEFT JOIN log_tramas_sirope lt4 ON lt4.codigo_ot = CONCAT(po.itemplan,'MN')   AND lt4.estado = 1
                    WHERE po.itemplan = ?
                    LIMIT 1 ";
	    $result = $this->db->query($sql, array($itemplan));
	    if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function getInfoDiseno($itemplan, $idEstacion) {
	    $sql = " SELECT * 
                   FROM diseno 
                  WHERE itemplan = ? 
                    AND idEstacion = ? 
                  LIMIT 1 ";
	    $result = $this->db->query($sql, array($itemplan, $idEstacion));
	    if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function getPartidasAdicionales($codigoPO) {
	    $sql = "  SELECT podm.*, 
                         pa.descripcion, FORMAT(podm.montoFinal,2) AS montoFinalFormat
                    FROM planobra_po_detalle_mo podm, 
                         partida pa
                   WHERE podm.codigoPartida = pa.codigoPartida
                     AND pa.flg_tipo NOT IN (3)
                     AND podm.codigo_po = ? ";
	    $result = $this->db->query($sql, array($codigoPO));
	    return $result->result_array();
	}    

    function getInfoDisenoRechazado($itemplan, $idEstacion) {
	    $sql = " SELECT dr.*, (CASE WHEN dr.requiere_licencia = 2 THEN 'NO' ELSE 'SI' END) as licencia, u1.nombre_completo as usu_ejecucion, u2.nombre_completo as usu_rechazo 
                FROM diseno_rechazado dr 
                LEFT JOIN usuario u1 ON dr.usuario_ejecucion = u1.id_usuario 
                LEFT JOIN usuario u2 ON dr.usuario_rechazo = u2.id_usuario 
                WHERE dr.itemplan = ? 
                AND dr.idEstacion = ?";
	    $result = $this->db->query($sql, array($itemplan, $idEstacion));
        log_message('error',$this->db->last_query());
	    if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	} 

    function updateToPoDeleteDise($itemplan, $idEstacion, $dataPlanObra) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {	
            $this->db->where('itemplan', $itemplan);
            $this->db->update('planobra', $dataPlanObra);
            if($this->db->affected_rows() <= 0) {
                $this->db->trans_rollback();            
                throw new Exception('No se actualizo Plan Obra');
            } else {        
                $this->db->where('itemplan', $itemplan);
                $this->db->where_in('idEstacion', $idEstacion);
                $this->db->delete('diseno_rechazado');
                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    throw new Exception('Hubo un error al eliminar el entidad_estacion.');
                }else{
                    $this->db->trans_commit();
                    $data['msj'] = 'Se actualizo correctamente!';
                    $data['error'] = EXIT_SUCCESS;
                }                   
            }
                
            
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }
    
    function getHijosCvSeguimiento($itemplan) {
	    $sql = "SELECT  dcv.ult_situa_especifica, dcv.itemplan, dcv.ip_hijo, ms.situacion_general, ms.desc_motivo as situa_especifica,
                        dcv.nro_depa, dcv.tipo, dcv.contacto, dcv.telefono_contacto, dcv.email_contacto, dcv.ultimo_comentario, dcv.fecha_entrega_comercial, dcv.has_quiebre_evidencia
                FROM planobra_detalle_cv_hijo dcv, motivo_seguimiento_cv ms
                WHERE dcv.ult_situa_especifica = ms.id
                and dcv.itemplan = ? ";
	    $result = $this->db->query($sql, array($itemplan));
	    return $result->result_array();
	}   

    function getHijosB2bSeguimiento($itemplan) {
	    $sql = "SELECT  po.indicador, dcv.itemplan,  ec.empresaColabDesc, c.departamento, c.distrito,  ms.situacion_general, ms.desc_motivo as situa_especifica, dcv.ultimo_comentario                
                FROM    planobra po, empresacolab ec, central c, planobra_detalle_b2b dcv, motivo_seguimiento_b2b ms
                WHERE   po.itemplan = dcv.itemplan
                AND	    po.idEmpresaColab = ec.idEmpresaColab
                AND     po.idCentral = c.idCentral
                AND     dcv.ult_situa_especifica = ms.id
                AND     dcv.itemplan = ?";
	    $result = $this->db->query($sql, array($itemplan));
	    return $result->result_array();
	}   
    
    function getHijosReforzamientoSeguimiento($itemplan) {
	    $sql = "SELECT  po.itemplan, ec.empresaColabDesc, fr.cto_ajudi, fr.tipo_refo, msr.situacion_general, msrc.situacion_especifica, fr.id_formulario, fr.situacion_especifica as id_situacion_especifica, fr.do_splitter
                FROM    formulario_reforzamientos fr, planobra po, empresacolab ec, motivo_seguimiento_reforzamiento msr, motivo_seguimiento_reforzamiento_cto msrc
                WHERE   po.itemplan = fr.itemplan
                and     fr.has_seguimiento = 1 
                and     po.idEmpresaColab = ec.idEmpresaColab
                and     po.situacion_general_reforzamiento = msr.id
                and     fr.situacion_especifica = msrc.id
                AND     po.itemplan = ?";
	    $result = $this->db->query($sql, array($itemplan));
	    return $result->result_array();
	}   

    function haveSolPdtValidacionByObra($itemplan){
	    $Query = "SELECT
                    COUNT(1) as cant
                FROM
                    pqt_solicitud_aprob_partidas_adicionales
                WHERE
                    itemplan    = ?
                AND estado IN (0 , 1)";
	    $result = $this->db->query($Query,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array()['cant'];
	    } else {
	        return null;
	    }
	} 

    function haveSolAprobadaByObra($itemplan){
	    $Query = "SELECT
                    COUNT(1) as cant
                FROM
                    pqt_solicitud_aprob_partidas_adicionales
                WHERE
                    itemplan    = ?
                AND estado IN (2)";
	    $result = $this->db->query($Query,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array()['cant'];
	    } else {
	        return null;
	    }
	}

    function getAllPoMoBySoloItemplanFTTHPangea($itemplan){
	    $Query = "SELECT
                        e.estacionDesc, (CASE WHEN ppo.flg_tipo_area = 1 THEN 'MAT'
                                            WHEN ppo.flg_tipo_area = 2 THEN 'MO' END) as tipoPo,
                                            ppo.codigo_po, ep.estado, ppo.costo_total, ppo.estado_po
                    FROM
                        planobra po, subproyecto sp, planobra_po ppo, po_estado ep, estacion e
                    WHERE
                        po.itemplan = ppo.itemplan
                    AND po.idSubProyecto = sp.idsubProyecto
                    AND	ppo.itemplan = ?
                    AND	ppo.estado_po = ep.idEstadoPo
                    AND ppo.idEstacion = e.idEstacion            
                    AND ppo.flg_tipo_area = 2
                    AND ppo.idEstacion not in (1)
                    AND ppo.estado_po NOT IN (7,8)
                    AND (CASE WHEN sp.idProyecto = 4 THEN ppo.idEstacion not in (1) ELSE TRUE END)";
	    $result = $this->db->query($Query, array($itemplan));
	    return $result->result();
	}

    function sendValidarPartidasAdicionalesNoPqt($itemplan, $idEstacion, $dataSolValidacion, $expediente) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();
            $this->db->where('itemplan',   $itemplan);
            $this->db->where('idEstacion', $idEstacion);
            $this->db->where('activo', 1);
            $this->db->update('pqt_solicitud_aprob_partidas_adicionales', array('activo' => 0));
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al desactivar solicitudes pendientes.');
            }else{
                $this->db->insert('pqt_solicitud_aprob_partidas_adicionales', $dataSolValidacion);
                if($this->db->affected_rows() <= 0) {
                    throw new Exception('Error al insertar en pqt_solicitud_aprob_partidas_adicionales');
                }else{
                    $this->db->insert_batch('itemplan_expediente', $expediente);
                    if($this->db->trans_status() === FALSE) {
                        throw new Exception('Error al insertar en itemplan_expediente');
                    }else{
                        $data['error'] = EXIT_SUCCESS;
                        $data['msj'] = 'Se actualizó correctamente!';
                        $this->db->trans_commit();            
                    }
                }
            } 
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function deleteRechaLiquidacion($itemplan) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {	               
            $this->db->where('itemplan', $itemplan);
             $this->db->delete('validacion_operaciones');
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                throw new Exception('Hubo un error al eliminar el validacion_operaciones.');
            }else{
                $this->db->trans_commit();
                $data['msj'] = 'Se actualizo correctamente!';
                $data['error'] = EXIT_SUCCESS;
            }               
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getInfoValOpeB2b($itemplan) {
	    $sql = "SELECT 
                        po.itemplan, sp.idProyecto, vo.estado, sp.idSubProyecto
                    FROM
                        planobra po	LEFT JOIN validacion_operaciones vo ON po.itemplan = vo.itemplan, subproyecto sp 
                    WHERE	po.idSubProyecto = sp.idSubProyecto 
                    AND 	(sp.idProyecto in (3,52) OR sp.idSubProyecto IN (734))
                    #AND 	po.idEstadoPlan = 9
                    AND     po.itemplan = ?";
	    $result = $this->db->query($sql, array($itemplan));
	    if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
    
    function preCancelarPoMat($poUpdate, $arrayInsertLog, $arrayInsertPoCan) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();
            $this->db->where('codigo_po',   $poUpdate['codigo_po']);
            $this->db->update('planobra_po', $poUpdate);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al actualizar planobra_po.');
            }else{
                $this->db->insert('log_planobra_po', $arrayInsertLog);
                if($this->db->affected_rows() <= 0) {
                    throw new Exception('Error al insertar en log_planobra_po');
                }else{
                    $this->db->insert('po_cancelar', $arrayInsertPoCan);
                    if($this->db->affected_rows() <= 0) {
                        throw new Exception('Error al insertar en po_cancelar');
                    }else{
                        $data['error'] = EXIT_SUCCESS;
                        $data['msj'] = 'Se actualizó correctamente!';
                        $this->db->trans_commit();            
                    }
                }
            } 
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    
    function generateOcEdicionCerti($arraySolicitud, $arrayItemXSolicitud, $updatePlanObra){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->insert_batch('solicitud_orden_compra', $arraySolicitud);
	        if($this->db->affected_rows() == 0) {
	            throw new Exception('Error al insertar en solicitud_orden_compra');
	        }else{
	            $this->db->insert_batch('itemplan_x_solicitud_oc', $arrayItemXSolicitud);
	            if($this->db->affected_rows() == 0) {
	                throw new Exception('Error al insertar en itemplan_x_solicitud_oc');
	            }else{    	          
                    $this->db->where('itemplan', $updatePlanObra['itemplan']);
    	            $this->db->update('planobra', $updatePlanObra);	
    	            if($this->db->trans_status() === FALSE) {
    	                throw new Exception('Error al modificar el planobra');
    	            }else{
                        $data['error'] = EXIT_SUCCESS;
                        $data['msj'] = 'Se actualizó correctamente!';
                    }
                }            	              
	        }
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}
	
	function getSolicitudPartidasAdicionales_2bucles($itemplan){
	    $Query = "SELECT   tmp.*, 
                            CONCAT(usu_1.nombre_completo, ' ', usu_1.ape_paterno, ' ', usu_1.ape_materno) as usuario_nivel_1, 
                            CONCAT(usu_2.nombre_completo, ' ', usu_2.ape_paterno, ' ', usu_2.ape_materno) as usuario_nivel_2 
	               FROM    pqt_solicitud_aprob_partidas_adicionales tmp
                   LEFT JOIN usuario usu_1 ON usu_1.id_usuario = tmp.usua_val_nivel_1
                   LEFT JOIN usuario usu_2 ON usu_1.id_usuario = tmp.usua_val_nivel_2
	               WHERE   tmp.itemplan    = ?	               
	               AND     tmp.activo = 1
	               LIMIT 1";
	    $result = $this->db->query($Query,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function getSolicitudPartidasAdicionales_2buclesOnlyRechazo($itemplan){
	    $Query = "SELECT   tmp.*, 
                            CONCAT(usu_1.nombre_completo, ' ', usu_1.ape_paterno, ' ', usu_1.ape_materno) as usuario_nivel_1, 
                            CONCAT(usu_2.nombre_completo, ' ', usu_2.ape_paterno, ' ', usu_2.ape_materno) as usuario_nivel_2 
	               FROM    pqt_solicitud_aprob_partidas_adicionales tmp
                   LEFT JOIN usuario usu_1 ON usu_1.id_usuario = tmp.usua_val_nivel_1
                   LEFT JOIN usuario usu_2 ON usu_1.id_usuario = tmp.usua_val_nivel_2
	               WHERE   tmp.itemplan    = ?	               
	               AND     tmp.estado IN (3,4)";
	    $result = $this->db->query($Query,array($itemplan));
        
	    if($result->row() != null) {
	        return $result->result_array();
	    } else {
	        return null;
	    }
	}

    
    function updateQuiebreCV($dataUpdate, $dataUpdateLog) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();
            $this->db->where('ip_hijo',   $dataUpdate['ip_hijo']);
            $this->db->update('planobra_detalle_cv_hijo', $dataUpdate);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al actualizar planobra_detalle_cv_hijo.');
            }else{       
                $this->db->where('id',   $dataUpdateLog['id']);
                $this->db->update('log_seguimiento_cv', $dataUpdateLog);
                if($this->db->trans_status() === FALSE) {
                    throw new Exception('Error al actualizar log_seguimiento_cv.');
                }else{          
                    $data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se actualizó correctamente!';
                    $this->db->trans_commit();  
                }
            } 
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function getLastLogByItemplanHijoCv($itemplan_hijo){
	    $Query = "SELECT lcv.* 
                    FROM planobra_detalle_cv_hijo dcv, log_seguimiento_cv lcv
                WHERE dcv.ip_hijo =  lcv.itemplan_hijo
                AND dcv.ip_hijo = ?
                AND dcv.ult_situa_especifica = lcv.id_motivo_seguimiento
                ORDER BY id DESC LIMIT 1";
	    $result = $this->db->query($Query,array($itemplan_hijo));
        
	    if($result->row() != null) {
            return $result->row_array();
	    } else {
	        return null;
	    }
	}      
	
	function insertFormularioReforzamiento($itemplan, $arrayFormularios) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {	                  
            $this->db->where('itemplan', $itemplan);
            $this->db->delete('formulario_reforzamientos');
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                throw new Exception('Hubo un error al eliminar el formulario_reforzamientos.');
            }else{ 
                $this->db->insert_batch('formulario_reforzamientos', $arrayFormularios);
                if($this->db->affected_rows() <= 0) {
                    throw new Exception('Error al insertar en formulario_reforzamientos');                
                }else{  
                    $this->db->trans_commit();
                    $data['msj'] = 'Se actualizo correctamente!';
                    $data['error'] = EXIT_SUCCESS;
                }                   
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getInfoFormularioReforzamiento($itemplan) {
	    $sql = " SELECT * 
                   FROM formulario_reforzamientos 
                  WHERE itemplan = ?";
	    $result = $this->db->query($sql, array($itemplan));
	    if ($result->row() != null) {
	        return $result->result_array();
	    } else {
	        return null;
	    }
	}

    function updSituacionSegReforzamiento($arrayUpdate) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();
            $this->db->where('id_formulario',   $arrayUpdate['id_formulario']);
            $this->db->update('formulario_reforzamientos', $arrayUpdate);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al actualizar planobra_detalle_cv_hijo.');
            }else{
                  $data['error'] = EXIT_SUCCESS;
                  $data['msj'] = 'Se actualizó correctamente!';
                  $this->db->trans_commit();  
            } 
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function getTotalMatByItemplan($itemplan) {
	    $sql = " SELECT sum(costo_total) as costo_total_mat 
                FROM    planobra_po 
                WHERE   itemplan = ?
                AND     flg_tipo_area = 1
                AND     estado_po NOT IN (7,8)";
	    $result = $this->db->query($sql, array($itemplan));
	    if ($result->row() != null) {
	        return $result->row_array()['costo_total_mat'];
	    } else {
	        return null;
	    }
	}

    
    function getHijosOt4ByItemMadreReforzaExpress($itemplan_m) {
	    $sql = "SELECT itemplan_m, count(1) as total_hijos, sum(case when has_sirope_fo = 1 then 1 else 0 end) as total_hijos_4 FROM planobra where itemplan_m = ?";
	    $result = $this->db->query($sql, array($itemplan_m));
	    if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function actualizarDisenoFTTH($itemplan, $uip)
    {   
        $data = [];
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        $codigoPO = $this->obtenerCodigoPO($itemplan);
        if ($codigoPO == null)
        {
            $data['msj']   = "No se pudo encontrar el valor del Itemplan";
        }
        else
        {
            $data  = $this->actualizarcantUIPDiseno($codigoPO, $uip);
        }

        return $data;

    }


    public function obtenerCodigoPO($itemplan)
    {
        $sql = "SELECT * FROM planobra_po WHERE itemplan = ? 
                AND idEstacion = 1 
                AND flg_tipo_area = 2 AND isPoPqt = 1 AND estado_po = 1 LIMIT 1";
        $result = $this->db->query($sql, array($itemplan));
        if ($result->row() != null) {
            return $result->row_array()['codigo_po'];
        } else {
            return null;
        }
    }

    public function actualizarcantUIPDiseno($codigoPO, $uip)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $sql = "UPDATE planobra_po_detalle_mo 
                    SET cantidadFinal = ?, 
                    montoFinal = (baremo * preciario * ?), 
                    costoMo = (baremo * preciario * ?) 
                    WHERE codigo_po = ?";
                                $result = $this->db->query($sql, array($uip, $uip, $uip, $codigoPO));
                                if($this->db->trans_status() === FALSE)
                                {
                                    $this->db->trans_rollback();
                                    throw new Exception('Error al actualizar en planobra_po_detalle_mo');
                                }
                                else
                                {

                                    $this->db->trans_commit(); 
                                    $sql2 = "UPDATE planobra_po SET costo_total = (SELECT SUM(montoFinal) 
                                            FROM planobra_po_detalle_mo WHERE codigo_po = ? GROUP BY codigo_po) WHERE codigo_po = ?";

                                    $result = $this->db->query($sql2, array($codigoPO, $codigoPO));

                                    if($this->db->trans_status() === FALSE)
                                    {
                                        $this->db->trans_rollback();
                                        throw new Exception('Error al actualizar en planobra_po');
                                    }
                                    else
                                    {
                                        $data['error'] = EXIT_SUCCESS;
                                        $data['msj'] = 'Se actualizó correctamente!';
                                        $this->db->trans_commit(); 
                                    }

                        
                                }
        }
        catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }
    
    

}
