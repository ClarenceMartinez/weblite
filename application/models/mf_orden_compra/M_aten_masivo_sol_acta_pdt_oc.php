<?php

class M_aten_masivo_sol_acta_pdt_oc extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getInfoSolicitudOCCreaByCodigo($codigo_solicitud){
	    $sql = "     SELECT po.itemplan, 
                            po.idEstadoPlan, 
                            po.idSubProyecto, po.paquetizado_fg, 
                            sp.idTipoPlanta, soc.codigo_solicitud, 
                            soc.orden_compra, po.costo_sap,
                            soc.estado, COUNT(1) AS cant 
                       FROM solicitud_orden_compra soc 
                  LEFT JOIN planobra po ON po.solicitud_oc_certi = soc.codigo_solicitud 
                  LEFT JOIN subproyecto sp ON po.idSubProyecto = sp.idSubProyecto 
                      WHERE soc.codigo_solicitud = ?
                      AND soc.tipo_solicitud = 3
                      AND soc.estado = 5
                     GROUP BY 1,2,3,4,5,6,7 
                      LIMIT 1 ";
	    $result = $this->db->query($sql,array($codigo_solicitud));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	} 
	
    function atencionSolicitudOcEdi($arrayUpdateSolicitud, $arrayUpdatePlanObra) {
        $this->db->update_batch('solicitud_orden_compra', $arrayUpdateSolicitud, 'codigo_solicitud');
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'error interno solicitud oc';
            $data['error'] = EXIT_ERROR;
        } else {         
            $this->db->update_batch('planobra', $arrayUpdatePlanObra, 'itemplan');
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se actualizo planobra';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se atendio correctamente';
                $data['error'] = EXIT_SUCCESS;
            }           
        }
        return $data;
    }
}
