<?php

class M_bandeja_exceso extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    } 
     
	function getBandejaExceso($situacion, $tipoPO, $itemplan) {
        $sql = " SELECT po.costo_unitario_mo,
						so.id_solicitud,
						p.proyectoDesc,
						ep.estadoPlanDesc,
						so.itemplan, sp.subProyectoDesc,
						(CASE WHEN so.tipo_po = 1 THEN 'MATERIAL'
							  WHEN so.tipo_po = 2 THEN 'MO' END) AS tipo_po_desc, 
						FORMAT(so.costo_inicial,2)      AS costo_inicial, 
						FORMAT(so.exceso_solicitado,2)  AS exceso_solicitado, 
						FORMAT(so.costo_final,2)        AS costo_final,
						so.costo_final as costo_final_nf,
						UPPER(CONCAT(u_sol.nombre_completo,' ', u_sol.ape_paterno,' ', u_sol.ape_materno)) AS usua_solicita, 
						DATE(so.fecha_solicita) AS fecha_solicita,
						UPPER(CONCAT(u_val.nombre_completo,' ', u_val.ape_paterno,' ', u_val.ape_materno)) AS usua_valida, 
						DATE(so.fecha_valida) AS fecha_valida,
						(CASE WHEN so.estado_valida IS NULL THEN 'PENDIENTE'
									WHEN so.estado_valida = 1 THEN 'APROBADO'
									WHEN so.estado_valida = 2 THEN 'RECHAZADO' END) AS situacion,
						so.origen,
						CASE WHEN so.origen = 1 THEN 'REG PO MAT'
								WHEN so.origen = 2 THEN 'REG PO MO'
								WHEN so.origen = 3 THEN 'REG VR'
								WHEN so.origen = 4 THEN 'LIQUI MO' 
								WHEN so.origen = 5 THEN 'ADIC. PQT'
								WHEN so.origen = 6 THEN 'EDIC. PIN'
								END tipo_origen,
						so.idEstacion,
						so.codigo_po,
						e.empresacolabDesc as eecc,
						z.zonalDesc,
						so.url_archivo,
						es.estacionDesc,
						(CASE WHEN so.origen = 1 THEN (SELECT ROUND(SUM(montoFinal),2)
														 FROM solicitud_exceso_obra_detalle_reg_mat sm
													    WHERE sm.id_solicitud = so.id_solicitud)
				   			  WHEN so.origen = 2 THEN (SELECT ROUND(SUM(montoFinal),2) 
														 FROM solicitud_exceso_obra_detalle_reg_mo sm
													    WHERE sm.id_solicitud = so.id_solicitud)
				               END) costoPo,
						so.tipo_po,
					    CASE WHEN so.tipo_po = 1 THEN COALESCE(( SELECT ROUND(SUM(costo_total),2)
																   FROM planobra_po ppo
																  WHERE ppo.itemplan = po.itemplan
																	AND so.fecha_solicita >= fechaRegistro
																	AND flg_tipo_area = 1
																	AND ppo.estado_po NOT IN (7,8)), 0)
						     WHEN so.tipo_po = 2 THEN COALESCE(( SELECT ROUND(SUM(costo_total),2)
																   FROM planobra_po ppo
																  WHERE ppo.itemplan = po.itemplan
																	AND so.fecha_solicita >= fechaRegistro
																	AND flg_tipo_area = 2
																	AND ppo.estado_po NOT IN (7,8)), 0)
										 END costoActualPo,
						so.comentario_reg
					FROM ( planobra po,
						   proyecto p,
						   subproyecto  sp, 
						   solicitud_exceso_obra so,
						   empresacolab e,
						   zonal z,
						   estacion es,
					       estadoplan ep)
				 LEFT JOIN usuario u_sol ON so.usuario_solicita = u_sol.id_usuario
				 LEFT JOIN usuario u_val ON so.usuario_valida = u_val.id_usuario
					 WHERE po.idSubProyecto = sp.idSubProyecto
					   AND po.itemplan = so.itemplan
					   AND p.idProyecto = sp.idProyecto
					   AND z.idZonal = po.idZonal
					   AND po.idEstadoPlan = ep.idEstadoPlan
					   AND so.idEstacion = es.idEstacion
					   AND e.idEmpresaColab = po.idEmpresaColab
					   AND CASE WHEN ? IS NULL THEN so.estado_valida IS NULL
						   ELSE so.estado_valida = ? END
					   AND CASE WHEN ? IS NULL THEN TRUE
						   ELSE so.tipo_po = ? END
					   AND so.itemplan = COALESCE(?,so.itemplan) ";
           
        $result = $this->db->query($sql, array($situacion,$situacion,$tipoPO,$tipoPO,$itemplan));
        return $result->result_array();           
    }

	function updateSolicitud($dataUpdateSolicitud, $idSolicitud){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->where('id_solicitud', $idSolicitud);
            $this->db->update('solicitud_exceso_obra', $dataUpdateSolicitud);
            if($this->db->affected_rows() != 1) {
                throw new Exception('Error al actualizar en la tabla solicitud_exceso_obra!!');
            }else{              
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!!';
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

	function getInfoObraByIdSolicitud($idSolicitud){
        $sql = '  SELECT se.itemplan,
		                 se.codigo_po,
						 po.indicador,
		                 se.tipo_po, 
						 po.costo_unitario_mat, 
						 po.costo_unitario_mo, 
						 po.costo_unitario_mo_crea_oc, 
						 po.idEstadoPlan, 
						 se.genSolEdic, 
						 se.isFerreteria,
						 s.idProyecto,
						 se.exceso_solicitado
                    FROM planobra po,
                         solicitud_exceso_obra se,
						 subproyecto s
                   WHERE po.itemplan = se.itemplan
					 AND se.id_solicitud = ?
					 AND s.idSubProyecto = po.idSubProyecto
					LIMIT 1';
        $result = $this->db->query($sql,array($idSolicitud));
        if($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

	function getDataSolicitudOc($itemplan) {
		$sql = " SELECT s.*,
						po.idEmpresaColab,
						po.idSubProyecto,
						po.itemplan,
						po.costo_unitario_mo,
		                ixs.posicion
				   FROM solicitud_orden_compra s,
					    itemplan_x_solicitud_oc ixs,
						planobra po
				  WHERE s.codigo_solicitud = ixs.codigo_solicitud_oc
                  AND ixs.itemplan = po.itemplan
                  AND po.solicitud_oc = ixs.codigo_solicitud_oc
                  AND s.estado = 2
                  AND po.orden_compra IS NOT NULL
				  AND po.itemplan = ?
		        LIMIT 1";
		$result = $this->db->query($sql, array($itemplan));
		if($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
	}

	function getValidOcEdic($itemplan) {
		$sql = " SELECT COUNT(1) countPendiente
				   FROM solicitud_orden_compra s,
						itemplan_x_solicitud_oc i
				  WHERE s.codigo_solicitud = i.codigo_solicitud_oc
					AND i.itemplan = ?
					AND s.tipo_solicitud = 2
					AND estado = 1";
		$result = $this->db->query($sql, array($itemplan));
		return $result->row_array()['countPendiente'];
	}

	function getDataSolicitudRegMo($idSolicitud) {
        $sql = " SELECT sed.id_solicitud,
			            sed.codigoPartida,
					    UPPER(pa.descripcion) AS desc_partida,
					    sed.baremo,
					    sed.preciario,
					    sed.cantidadFinal,
					    sed.montoFinal
			       FROM solicitud_exceso_obra_detalle_reg_mo sed,
				        partida pa
		          WHERE sed.codigoPartida = pa.codigoPartida
			        AND sed.id_solicitud = ? ";
			   
        $result = $this->db->query($sql, array($idSolicitud));
        return $result->result_array();           
    }

}
