<?php

class M_bandeja_valida_obra extends CI_Model {

    function __construct() {
        parent::__construct();
    } 

    function getBandejaValidaObra($itemplan, $idUsuario, $idEmpresaColab, $flgFiltro = 1) {
        $sql = "    SELECT tb1.*,
                           SUM(CASE WHEN ppo.estado_po IN (4,5) THEN ppo.costo_total ELSE 0 END) total_mo_validado
                      FROM (
                                 SELECT DISTINCT
                                        pa.*,
                                        po.indicador,
                                        sp.subProyectoDesc,
                                        p.proyectoDesc,
                                        ec.empresaColabDesc,
                                        c.idJefatura,
                                        j.jefaturaDesc,
                                        FORMAT(pa.costo_inicial, 2) AS costo_inicial_form,
                                        FORMAT(pa.costo_adicional, 2) AS costo_adicional_form,
                                        FORMAT(pa.costo_total, 2) AS costo_total_form,
                                        (CASE	WHEN pa.estado = 0 THEN 'PDT VAL NIVEL 1'
                                                    WHEN pa.estado = 1 THEN 'PDT VAL NIVEL 2'
                                                    WHEN pa.estado = 2 THEN 'APROBADO'
                                                    WHEN pa.estado = 3 THEN 'RECHAZADO NIVEL 1'
                                                    WHEN pa.estado = 4 THEN 'RECHAZADO NIVEL 2'
                                        END) AS situacion,
                                        u.nombre_completo,
                                        po.cantFactorPlanificado,
                                        FORMAT(po.costo_unitario_mo,2) as costo_unitario_mo
                                   FROM planobra po,
                                        central c,
                                        jefatura j,
                                        subproyecto sp,
                                        proyecto p,
                                        empresacolab ec,
                                        usuario_validador_pqt uv,
                                        pqt_solicitud_aprob_partidas_adicionales pa
                              LEFT JOIN usuario u ON pa.usua_registro = u.id_usuario
                                  WHERE pa.itemplan = po.itemplan
                                    AND po.idCentral = c.idCentral
                                    AND c.idJefatura = j.idJefatura
                                    AND po.idSubProyecto = sp.idSubProyecto
                                    AND sp.idProyecto = p.idProyecto
                                    AND po.idEmpresaColab = ec.idEmpresaColab        
                                    AND c.idJefatura 	= uv.idJefatura
                                    AND sp.idProyecto 	= uv.idProyecto
                                    AND uv.idUsuario = ?
                                    AND CASE WHEN ? IS NULL THEN TRUE ELSE pa.estado IN (0,1) END
                                    AND (CASE WHEN pa.estado IN (0,1)
                                              THEN (CASE WHEN uv.nivel_validacion = 1 THEN pa.estado = 0
                                                         WHEN uv.nivel_validacion = 2 THEN pa.estado = 1
                                                         END)
                                              ELSE TRUE END)
                                    AND pa.activo = 1
                                    AND po.itemplan = COALESCE(?, po.itemplan)
                                    AND po.idEmpresaColab  = COALESCE(?, po.idEmpresaColab)
                            ) tb1
                  LEFT JOIN planobra_po ppo ON tb1.itemplan = ppo.itemplan AND ppo.flg_tipo_area = 2			
                   GROUP BY 1,11 ";
        $result = $this->db->query($sql, array($idUsuario, $flgFiltro, $itemplan, $idEmpresaColab));
        //log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    function getInfoExpedienteLiquidacion($itemplan, $idEstacion) {
        $sql = "    SELECT * 
                      FROM itemplan_expediente 
                     WHERE itemplan = ? 
                       AND idEstacion = ?
				       AND estado = 'ACTIVO'
                       AND path_expediente IS NOT NULL
				  ORDER BY fecha DESC
				     LIMIT 1 ";
        $result = $this->db->query($sql, array($itemplan, $idEstacion));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    function getDataToSolicitudEdicionCertiOC($itemplan){
	    $sql = "  SELECT po.itemplan,po.costo_unitario_mat,po.costo_unitario_mo,tb.total
                    FROM planobra po    
                    LEFT JOIN (
                                  SELECT ppo.itemplan, SUM(costo_total) AS total 
                                    FROM planobra_po ppo, planobra po, subproyecto sp
                                   WHERE po.itemplan = ppo.itemplan
                                     AND po.idSubProyecto = sp.idsubProyecto
                                     AND ppo.itemplan = ?
                                     AND ppo.estado_po IN (4,5)
                                     AND ppo.flg_tipo_area = 2
                                     AND (CASE WHEN sp.idProyecto = 52 THEN ppo.idEstacion not in (1) ELSE TRUE END)
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

    function getEstaTrabajadasConExpeFO($itemPlan) {
        $sql = "  SELECT *
                    FROM itemplan_expediente
                    WHERE itemplan = ?
                    AND idEstacion IN (5 , 15, 16, 6, 13, 14)
                    AND estado = 'ACTIVO'
                    AND estado_final = 'PENDIENTE';";
        $result = $this->db->query($sql, array($itemPlan));
        return $result->result();
    }
    
    function getEstaTrabajadasConExpeCOAX($itemPlan) {
        $sql = "  SELECT *
                    FROM itemplan_expediente
                    WHERE itemplan = ?
                    AND idEstacion IN (2,7,4,9,10,8,3,12,18)
                    AND estado = 'ACTIVO'
                    AND estado_final = 'PENDIENTE';";
        $result = $this->db->query($sql, array($itemPlan));
        return $result->result();
    }

    function rechazarSolicitud($dataUpdate, $idSolicitud, $data_expediente_update){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->where('id_solicitud', $idSolicitud);
            $this->db->update('pqt_solicitud_aprob_partidas_adicionales', $dataUpdate);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al modificar a pqt_solicitud_aprob_partidas_adicionales');
            }else{
                $this->db->update_batch('itemplan_expediente', $data_expediente_update, 'id');
                if($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    throw new Exception('Error al modificar el itemplan_expediente');
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

    function getSolicitudPartidasAdicionalesByItemplanSolo($idSolicitud){
	    $Query = "SELECT   tmp.*,
                            CONCAT(usu_1.nombre_completo, ' ', usu_1.ape_paterno, ' ', usu_1.ape_materno) as usuario_nivel_1,
                            CONCAT(usu_2.nombre_completo, ' ', usu_2.ape_paterno, ' ', usu_2.ape_materno) as usuario_nivel_2
	               FROM    pqt_solicitud_aprob_partidas_adicionales tmp
                   LEFT JOIN usuario usu_1 ON usu_1.id_usuario = tmp.usua_val_nivel_1
                   LEFT JOIN usuario usu_2 ON usu_1.id_usuario = tmp.usua_val_nivel_2
	               WHERE   tmp.id_solicitud    = ?
	               AND     tmp.activo = 1
	               LIMIT 1";
	    $result = $this->db->query($Query,array($idSolicitud));
	  //  log_message('error', $this->db->last_query());
	     
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function getAllPoMoBySoloItemplan($itemplan){
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
                	AND ppo.estado_po NOT IN (7,8)
                    AND (CASE WHEN sp.idProyecto IN (52) THEN ppo.idEstacion not in (1) ELSE TRUE END)";
                  
	    $result = $this->db->query($Query, array($itemplan));
	    return $result->result();
	}
	
	function getInfoExpedienteLiquidacionNoPqtByItem($itemplan) {
        $Query = "SELECT * FROM itemplan_expediente
                  WHERE itemplan = ?
                  #AND idEstacion IS NULL
                  AND estado = 'ACTIVO'
                  AND path_expediente is not null 
				  ORDER BY fecha DESC LIMIT 1";
        $result = $this->db->query($Query, array($itemplan));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    function getCountMatPDTContabilizarByItem($itemplan) {

        $sql = "   SELECT COUNT(*) cantidad
		             FROM planobra_po ppo,
						  (  SELECT mfs.material,
									mfs.texto_breve_de_material,
									CAST(REPLACE(SUBSTRING_INDEX(TRIM(mfs.ctd_red),',',1), '.', '') AS SIGNED) AS ctd_red, 
									CAST(REPLACE(SUBSTRING_INDEX(TRIM(mfs.ctd_nec),',',1), '.', '') AS SIGNED) AS ctd_nec,
									CAST(REPLACE(SUBSTRING_INDEX(TRIM(mfs.ctd_dif),',',1), '.', '') AS SIGNED) AS ctd_dif,
									mfs.reserva
							   FROM materiales_fija_sap mfs
							  WHERE mfs.cmv IN ('281','282')
							    AND CAST(REPLACE(SUBSTRING_INDEX(TRIM(mfs.ctd_dif),',',1), '.', '') AS SIGNED) > 0
						   ) maf
                     WHERE ppo.vale_reserva = maf.reserva 
	                   AND ppo.vale_reserva IS NOT NULL
	                   AND ppo.flg_tipo_area = 1
	                   AND ppo.itemplan = ?	";

        $result = $this->db->query($sql, array($itemplan));
        if ($result->num_rows() > 0) {
            return $result->row()->cantidad;
        } else {
            return 0;
        }
    }

    function getPOToValidateToItemplanNoPqt($itemPlan) {
	    $Query = "SELECT * FROM	planobra_po
	               WHERE   itemplan = ?
            	    AND    estado_po = 4";
	    $result = $this->db->query($Query, array($itemPlan));
	    return $result->result();
	}

    function hasSolicituExoeRechazada($itemplan) {
        $Query = "select count(1) as cant
                    from pqt_solicitud_aprob_partidas_adicionales 
                    where itemplan = ? and estado = 3;";
        $result = $this->db->query($Query, array($itemplan));
        if ($result->row() != null) {
            return $result->row_array()['cant'];
        } else {
            return null;
        }
    }

    function getLicencias($itemPlan) {        
        $sql = "SELECT * 
		          FROM entidad_itemplan_estacion 
				 WHERE itemPlan='$itemPlan' 
				   AND ubicacion_evidencia IS NOT NULL";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getExpedienteLiquidacion($itemplan, $idEstacion) {
	    $sql = "SELECT * FROM itemplan_expediente
            	WHERE itemplan = ?
            	AND estado = 'ACTIVO'
        	    AND path_expediente is not null 
				ORDER BY fecha DESC LIMIT 1";
	    $result = $this->db->query($sql, array($itemplan));
	    log_message('error', $this->db->last_query());
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
}
