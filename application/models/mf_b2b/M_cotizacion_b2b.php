<?php

class M_cotizacion_b2b extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
    function getAllCotizaciones($estado, $sisego, $cod_solis, $iddEECC) {
        $sql = "SELECT 
                pc.idEmpresaColab, pc.itemplan, pc.nombre_estudio, pc.fecha_aprobacion, pc.estado, pc.codigo_cluster, sp.subproyectoDesc, pc.sisego, e.empresaColabDesc, c.codigo, pc.distrito, pc.tipo_enlace, 
                ROUND(COALESCE(pc.costo_materiales, 0)+ COALESCE(pc.costo_mat_edif, 0), 2) as costo_materiales,
                ROUND(COALESCE(pc.costo_mano_obra, 0)+ COALESCE(pc.costo_mo_edif, 0) + COALESCE(pc.costo_oc, 0) + COALESCE(pc.costo_oc_edif, 0), 2) costo_mano_obra,
                pc.clasificacion, pc.fecha_registro,   CASE	 WHEN pc.estado  = 0 THEN 'PDT COTIZACION'
                                                            WHEN pc.estado  = 1 THEN 'PDT APROBACION'
                                                            WHEN pc.estado  = 2 THEN 'APROBADO'
                                                            WHEN pc.estado  = 3 THEN 'RECHAZADO'
                                                            WHEN pc.estado  = 4 THEN 'PDT CONFIRMACION'
                                                            WHEN pc.estado  = 8 THEN 'CANCELADO' END estadoDesc, pc.duracion, pc.tipo_enlace_2, (pre.costo*2.3) as total_coti,
                                                            (SELECT concat(codigo,'-',centralDesc) 
                    FROM central c
                    WHERE c.idCentral = pc.nodo_principal) AS nodo_principal,
                    pc.cant_puntos_apoyo,
                    pc.operador_aereo,
                    pc.cant_postes_electricos,
                    pc.empresa_electrica,
                    pc.cant_ducto_2_pul,
                    pc.cant_ducto_3_pul,
                    pc.cant_ducto_4_pul,
                    pc.operador_subte,
                    pc.orden_compra,
                    pc.fecha_reg_cotizacion,
                    (CASE WHEN pc.estado = 8 THEN pc.comentario_cancela 
                          WHEN pc.estado = 3 THEN pc.comentario_rechazo
						  WHEN pc.estado = 0 AND pc.comentario_devolucion IS NOT NULL THEN pc.comentario_devolucion
                          ELSE '' END) as comentario_final
            FROM
                planobra_cluster pc,
                subproyecto sp,
                empresacolab e,
                central c, 
				pqt_preciario pre 
            WHERE	
                pc.idSubProyecto = sp.idSubProyecto
            AND	pc.idEmpresaColab = e.idEmpresaColab
            AND pc.idCentral = c.idCentral
			AND	pc.idEmpresaColab = pre.idEmpresaColab 
			AND pre.idTipoPreciario = 1
			AND pre.tipoJefatura = 1
            AND pc.estado           = COALESCE(?, pc.estado)
            AND pc.sisego           = COALESCE(?, pc.sisego)
            AND pc.codigo_cluster   = COALESCE(?, pc.codigo_cluster)
			AND CASE WHEN ? = 6 THEN TRUE 
			ELSE pc.idEmpresaColab = COALESCE(?, pc.idEmpresaColab) 
			 END ";
        $result = $this->db->query($sql, array($estado, $sisego, $cod_solis, $iddEECC, $iddEECC));
        return $result->result_array();
    }

    function getAllCotizacionesByRangeDate($estado,$fec_inicio, $fec_fin) {
        $sql = "SELECT 
                pc.nombre_estudio, pc.fecha_aprobacion, pc.estado, pc.codigo_cluster, sp.subproyectoDesc, pc.sisego, e.empresaColabDesc, c.codigo, pc.distrito, pc.tipo_enlace, 
                ROUND(COALESCE(pc.costo_materiales, 0)+ COALESCE(pc.costo_mat_edif, 0), 2) as costo_materiales, pc.fecha_reg_cotizacion,
                ROUND(COALESCE(pc.costo_mano_obra, 0)+ COALESCE(pc.costo_mo_edif, 0) + COALESCE(pc.costo_oc, 0) + COALESCE(pc.costo_oc_edif, 0), 2) costo_mano_obra,
                pc.clasificacion, pc.fecha_registro,   CASE	 WHEN pc.estado  = 0 THEN 'PDT COTIZACION'
                                                            WHEN pc.estado  = 1 THEN 'PDT APROBACION'
                                                            WHEN pc.estado  = 2 THEN 'APROBADO'
                                                            WHEN pc.estado  = 3 THEN 'RECHAZADO'
                                                            WHEN pc.estado  = 4 THEN 'PDT CONFIRMACION' END estadoDesc, (pre.costo*2.3) as total_coti ,
                                                            (SELECT concat(codigo,'-',centralDesc) 
                    FROM central c
                    WHERE c.idCentral = pc.nodo_principal) AS nodo_principal,
                    pc.cant_puntos_apoyo,
                    pc.operador_aereo,
                    pc.cant_postes_electricos,
                    pc.empresa_electrica,
                    pc.cant_ducto_2_pul,
                    pc.cant_ducto_3_pul,
                    pc.cant_ducto_4_pul,
                    pc.operador_subte,
                    (CASE WHEN pc.estado = 8 THEN pc.comentario_cancela 
                          WHEN pc.estado = 3 THEN pc.comentario_rechazo
						  WHEN pc.estado = 0 AND pc.comentario_devolucion IS NOT NULL THEN pc.comentario_devolucion
                    ELSE '' END) as comentario_final
            FROM
                planobra_cluster pc,
                subproyecto sp,
                empresacolab e,
                central c, 
				pqt_preciario pre 
            WHERE	
                pc.idSubProyecto = sp.idSubProyecto
            AND	pc.idEmpresaColab = e.idEmpresaColab
            AND pc.idCentral = c.idCentral
			AND	pc.idEmpresaColab = pre.idEmpresaColab 
			AND pre.idTipoPreciario = 1
			AND pre.tipoJefatura = 1
            AND pc.estado           = COALESCE(?, pc.estado)
            AND DATE(pc.fecha_aprobacion) >= date_format(str_to_date(?,'%m/%d/%Y'),'%Y-%m-%d')
            AND DATE(pc.fecha_aprobacion) <= date_format(str_to_date(?,'%m/%d/%Y'),'%Y-%m-%d')";
            
        $result = $this->db->query($sql, array($estado, $fec_inicio, $fec_fin));
        return $result->result_array();
    }
    
    function updateClusterPadre($codigo_cluster, $dataCluster, $log_coti_b2b){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->trans_begin();
            $this->db->where('codigo_cluster', $codigo_cluster);
			$this->db->update('planobra_cluster', $dataCluster);			
            if($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception('Error al modificar el updateClusterPadre');
            }else{
                $this->db->insert('log_cotizacion_b2b', $log_coti_b2b);
                if($this->db->affected_rows() != 1) {
                    throw new Exception('Hubo un error al registrar en la tabla log_cotizacion_b2b');
                }else{
                    $this->db->trans_commit();
                    $data['error']    = EXIT_SUCCESS;
                    $data['msj']      = 'Se inserto correctamente!';
                }
            }

        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }

    function getCountConfirmaSisego($codigo) {
        $sql = "SELECT COUNT(1) count
                  FROM cotizacion_validar
                 WHERE codigo_cluster = ?
                   AND flg_validacion <> 2 ";
        $result = $this->db->query($sql, array($codigo));
        return $result->row_array()['count'];
    }
	
}
