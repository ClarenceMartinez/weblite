<?php

class M_bandeja_devolucion_cv extends CI_Model {

    function __construct() {
        parent::__construct();
    } 

    function getObrasToBandejaDevolucion() {
        $sql = "  SELECT cv.id, po.itemplan, po.orden_compra, sp.subProyectoDesc,po.nombrePlan, 
		                 ec.empresaColabDesc, f.faseDesc, ep.estadoPlanDesc, 
                         (CASE WHEN cv.estado = 1 THEN 'PENDIENTE'
                		       WHEN cv.estado = 2 THEN 'ATENDIDO' END) AS estadoDesc,
                         (CASE WHEN cv.accion = 1 THEN 'AGENDADO'
                		       WHEN cv.accion = 2 THEN 'CERRADO' END) AS situacion,
                         cv.estado,
						 COUNT(aci.itemplan) AS cantSinContacto                                                       
                    FROM cv_bandeja_devolucion	cv
			   LEFT JOIN agenda_cv_item aci ON cv.itemplan = aci.itemplan AND aci.estado = 5, 
					     planobra po, subproyecto sp, empresacolab ec, fase f, estadoplan ep 
                   WHERE cv.itemplan = po.itemplan
                     AND po.idSubProyecto = sp.idSubProyecto
                     AND po.idFase = f.idFase
                     AND po.idEstadoPlan = ep.idEstadoPlan
                     AND po.idEmpresaColab = ec.idEmpresaColab
				GROUP BY 1,2 ";
        $result = $this->db->query($sql, array());
        return $result->result();           
    }

    function getInfoSolCreacionByItem($itemplan){
	    $sql = " SELECT soc.*, po.itemplan, po.idEstadoPlan, po.costo_unitario_mo, po.posicion
                   FROM solicitud_orden_compra soc,
                	    planobra po
                  WHERE soc.codigo_solicitud = po.solicitud_oc
                    AND po.orden_compra = soc.orden_compra	                
                    AND soc.estado IN (1,2)
                	AND po.itemplan = ?
                	LIMIT 1 ";
	    $result = $this->db->query($sql,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
    
    function registrarSolAnulacion($dataPlanobra, $solicitud_oc_creacion, $item_x_sol){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->insert('solicitud_orden_compra', $solicitud_oc_creacion);
            if($this->db->affected_rows() != 1) {
                throw new Exception('Error al insertar en solicitud_orden_compra');
            }else{
                $this->db->insert('itemplan_x_solicitud_oc', $item_x_sol);
                if($this->db->affected_rows() != 1) {
                    throw new Exception('Error al insertar en itemplan_x_solicitud_oc');
                }else{
                    $this->db->where('itemplan', $dataPlanobra['itemplan']);
                    $this->db->update('planobra',$dataPlanobra);
                    if ($this->db->trans_status() === FALSE) {
                        throw new Exception('Hubo un error al actualizar en planobra.');
                    }else{
                        $data['error'] = EXIT_SUCCESS;
                        $data['msj'] = 'Se actualizó correctamente!!';
                        $this->db->trans_commit();
                    }
                }
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function cancelarSolCreacion($dataPlanobra,$dataSolicitud){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{    
            $this->db->where('codigo_solicitud', $dataSolicitud['codigo_solicitud']);
            $this->db->update('solicitud_orden_compra', $dataSolicitud);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al actualizar la tabla solicitud_orden_compra');
            }else{
                $this->db->where('itemplan', $dataPlanobra['itemplan']);
                $this->db->update('planobra',$dataPlanobra);
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Hubo un error al actualizar en planobra.');
                }else{
                    $data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se actualizó correctamente!!';
                    $this->db->trans_commit();
                }
            }	        
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function cerrarObraBanDev($dataPlanobra, $dataDevo){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $this->db->where('itemplan', $dataPlanobra['itemplan']);
            $this->db->update('planobra',$dataPlanobra);
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Hubo un error al actualizar en planobra.');
            }else{
                $this->db->where('id', $dataDevo['id']);
                $this->db->update('cv_bandeja_devolucion', $dataDevo);
                if($this->db->affected_rows() != 1) {
                    throw new Exception('Error al actualizar en cv_bandeja_devolucion');
                }else{
                    $data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se actualizó correctamente!!';
                }
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function updateBandejaDev($dataDevo){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $this->db->where('id', $dataDevo['id']);
            $this->db->update('cv_bandeja_devolucion', $dataDevo);
            if($this->db->affected_rows() != 1) {
                throw new Exception('Error al actualizar en cv_bandeja_devolucion');
            }else{
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!!';
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }
}
