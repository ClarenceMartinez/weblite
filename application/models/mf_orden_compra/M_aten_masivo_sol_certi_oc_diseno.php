<?php

class M_aten_masivo_sol_certi_oc_diseno extends CI_Model {

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
                       FROM solicitud_orden_compra_diseno soc 
                  LEFT JOIN planobra po ON po.solicitud_oc_certi_diseno = soc.codigo_solicitud 
                  LEFT JOIN subproyecto sp ON po.idSubProyecto = sp.idSubProyecto 
                      WHERE soc.codigo_solicitud = ?
                        AND soc.tipo_solicitud = 3 
                        AND soc.estado = 1
                     GROUP BY 1,2,3,4,5,6,7 
                      LIMIT 1 ";
	    $result = $this->db->query($sql,array($codigo_solicitud));
        // log_message('error', $this->db->last_query());

	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	} 
	
    function atencionSolicitudOcEdi($arrayUpdateSolicitud, $arrayUpdatePlanObra, $arraySolicitudes, $idUsuario) {
        $this->db->update_batch('solicitud_orden_compra_diseno', $arrayUpdateSolicitud, 'codigo_solicitud');
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'error interno solicitud oc';
            $data['error'] = EXIT_ERROR;
        } else {         
            $this->db->update_batch('planobra', $arrayUpdatePlanObra, 'itemplan');
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se actualizo planobra';
                $data['error'] = EXIT_ERROR;
            } else {
                $sql = "INSERT INTO log_planobra_po (codigo_po, itemplan, idUsuario, fecha_registro, idPoestado, controlador)
                        SELECT ppo.codigo_po, po.itemplan,?, NOW(), ?, ? 
                        FROM planobra_po ppo,
                        planobra po
                        WHERE ppo.estado_po = ?
                        AND po.itemplan = ppo.itemplan
                        AND po.solicitud_oc_certi_diseno  IN ?";
                        $result = $this->db->query($sql,array($idUsuario, ID_ESTADO_PO_CERTIFICADO, 'CERTIFICADO OC', ID_ESTADO_PO_VALIDADO, $arraySolicitudes));
                        if($this->db->affected_rows() == 0) {
                            $data['error'] = EXIT_ERROR;
                            $data['msj']   = 'error al registrar el log de po';                
                        }else{
                            $sql = "UPDATE  planobra_po ppo,
                                            planobra po
                                        SET ppo.estado_po = ?
                                    WHERE ppo.estado_po = ?
                                        AND ppo.itemplan = po.itemplan
                                        AND po.solicitud_oc_certi_diseno IN ?";
                            $result = $this->db->query($sql,array(ID_ESTADO_PO_CERTIFICADO, ID_ESTADO_PO_VALIDADO, $arraySolicitudes));
                            if($this->db->affected_rows() < 1) {
                                $data['error'] = EXIT_ERROR;
                                $data['msj'] = 'No hay POs validadas, las POs deben estar validadas para ser certificadas.';
                            }else{              
                                $data['error'] = EXIT_SUCCESS;
                                $data['msj'] = 'Se actualizo correctamente!';
                            }
                        }
            }
            return $data;
        }
    }
}
