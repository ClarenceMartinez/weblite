<?php

class M_registro_manual_po_mat extends CI_Model {

    function __construct() {
        parent::__construct();
    } 

    function getMaterialesBySubProyectoEstacion($idSubProyecto, $idEstacion) {
        $sql = "  SELECT m.codigo_material,
                         m.descrip_material,
                         m.costo_material,
                         m.unidad_medida AS unidad_medida,
                         m.flg_tipo,
                         (CASE WHEN m.flg_tipo = 1 THEN 'BUCLE'
                               WHEN m.flg_tipo = 0 THEN 'NO BUCLE'
                               ELSE 'SIN ESTADO' END) AS tipo_material,
                         (ROUND(km.cantidad_kit,0)) AS cant_kit_material
                    FROM material m,
                         kit_material km
                   WHERE m.codigo_material = km.codigo_material
					 AND m.estado_material = 1
                     AND m.estado_material != 'Phase out'
                     AND m.flg_tipo != 1
                     AND km.idSubProyecto = ?
                     AND km.idEstacion = ? ";
        $result = $this->db->query($sql, array($idSubProyecto, $idEstacion));
        return $result->result();           
    }

    function countMaterialByCod($codigoMaterial) {
        $sql = "SELECT COUNT(1) cantidad
				  FROM material
				 WHERE codigo_material = ? ";
        $result = $this->db->query($sql,array($codigoMaterial));
        return $result->row()->cantidad;
    }

    public function getDetalleMaterial($codMaterial, $idSubProyecto, $idEstacion) {
        $sql = " SELECT m.codigo_material,
						m.descrip_material,
						m.costo_material,
                        m.unidad_medida AS unidad_medida,
                        m.flg_tipo,
						(CASE WHEN m.flg_tipo = 1 THEN 'BUCLE'
							  WHEN m.flg_tipo = 0 THEN 'NO BUCLE'
						 ELSE 'SIN ESTADO' END) AS tipo_material,
						(ROUND(km.cantidad_kit,0)) AS cant_kit_material,
						km.factor_porcentual
				   FROM material m,
		                kit_material km
                  WHERE m.codigo_material = km.codigo_material
                    AND m.estado_material != 'phase out'
                    AND km.idSubProyecto = ?
                    AND km.idEstacion = ? 
                    AND m.codigo_material = ? ";
        $result = $this->db->query($sql, array($idSubProyecto, $idEstacion, $codMaterial));
		log_message('error', $this->db->last_query());
        return $result->row_array();
    }

    public function countMatPhaseOut($codMaterial) {
        $sql = " SELECT COUNT(codigo_material) AS cantidad
		           FROM material
                  WHERE codigo_material = ?
                    AND estado_material = 'phase out' ";
        $result = $this->db->query($sql,array($codMaterial));
        return $result->row()->cantidad;
    }
}
