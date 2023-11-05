<?php

class M_aten_masivo_sol_crea_oc_diseno extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getInfoSolicitudOCCreaByCodigo($codigo_solicitud){
	    $sql = "     SELECT po.itemplan, 
                            po.idEstadoPlan, 
                            po.idSubProyecto, po.paquetizado_fg, 
                            sp.idTipoPlanta, soc.codigo_solicitud, 
                            soc.estado, COUNT(1) AS cant 
                       FROM solicitud_orden_compra_diseno soc 
                  LEFT JOIN planobra po ON po.solicitud_oc_diseno = soc.codigo_solicitud 
                  LEFT JOIN subproyecto sp ON po.idSubProyecto = sp.idSubProyecto 
                      WHERE soc.codigo_solicitud = ?
                        AND tipo_solicitud = 1 
                     GROUP BY 1,2,3,4,5,6,7 
                      LIMIT 1 ";
	    $result = $this->db->query($sql,array($codigo_solicitud));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function existeItemplanConOC($ordenCompra) {
        $sql = " SELECT COUNT(*) cantidad
		           FROM planobra WHERE orden_compra = ? ";
        $result = $this->db->query($sql, array($ordenCompra));
        return $result->row()->cantidad;
    }
	
}
