<?php

class M_validacion_operaciones_b2b extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
    function getAllCotizaciones($itemplan) {
        $sql = "SELECT 
                    po.itemplan, po.indicador, po.nombrePlan, sp.subProyectoDesc, po.fechaPreliquidacion, ec.empresaColabDesc, j.jefaturaDesc, c.centralDesc,
                    FORMAT((SELECT SUM(costo_total) FROM planobra_po where itemplan = po.itemplan AND flg_tipo_area = 1 and estado_po NOT IN (7,8)),2) as costo_po_mat,
                    FORMAT((SELECT SUM(costo_total) FROM planobra_po where itemplan = po.itemplan AND flg_tipo_area = 2 and estado_po NOT IN (7,8)),2) as costo_po_mo,  
                    vo.estado,
                    (CASE   WHEN vo.estado IS NULL THEN 'PDT APROBACION'
                            WHEN vo.estado = 1 THEN 'APROBADO'
                            WHEN vo.estado = 2 THEN 'RECHAZADO' END) as estado_desc, sp.idProyecto 
                FROM
                    planobra po	LEFT JOIN validacion_operaciones vo ON po.itemplan = vo.itemplan, subproyecto sp, empresacolab ec, central c, jefatura j
                WHERE	po.idSubProyecto = sp.idSubProyecto
                AND		po.idEmpresaColab = ec.idEmpresaColab
                AND		po.idCentral = c.idCentral
                AND		c.idJefatura = j.idJefatura
                AND 	(sp.idProyecto IN (3,52) OR sp.idSubProyecto IN (734))
                AND 	po.idEstadoPlan = 9
                AND po.itemplan           = COALESCE(?, po.itemplan)";
        $result = $this->db->query($sql, array($itemplan));
        //log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    function rechazarItemplandISEvAL($itemplan, $idEstacion, $dataPlanObra, $dataDisenoRecha, $dataDiseno) {
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

    function actualizarEstadoPo($arrayUpdatePo, $arrayInsertLog) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {	
            $this->db->where('itemplan',    $arrayUpdatePo['itemplan']);
            $this->db->where('codigo_po',   $arrayUpdatePo['codigo_po']);
            $this->db->update('planobra_po',     $arrayUpdatePo);
            if($this->db->affected_rows() <= 0) {
                $this->db->trans_rollback();                       
                throw new Exception('No se actualizo diseno');
            } else {
                    $this->db->insert('log_planobra_po', $arrayInsertLog);
                    if($this->db->affected_rows() <= 0) {
                        $data['msj'] = 'No se registro el log de la PO';
                        $data['error'] = EXIT_ERROR;
                    } else {
                        $data['msj'] = 'Se registró correctamente';
                        $data['error'] = EXIT_SUCCESS;
                        
                    }
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }
	
    function getPoDisenoToValidate($itemplan) {
	    $sql = "SELECT codigo_po FROM planobra_po 
                WHERE itemplan      = ? 
                AND idEstacion      = 1 
                AND flg_tipo_area   = 2 
                AND estado_po       = 1;";
	    $result = $this->db->query($sql, array($itemplan));
	    if ($result->row() != null) {
	        return $result->row_array()['codigo_po'];
	    } else {
	        return null;
	    }
	}
    
}
