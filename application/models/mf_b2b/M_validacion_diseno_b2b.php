<?php

class M_validacion_diseno_b2b extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
    function getAllCotizaciones($itemplan, $sisego) {
        $sql = "SELECT 	po.itemplan, po.indicador, po.nombrePlan, po.idEmpresaColab, sp.subproyectoDesc,  e.empresaColabDesc, c.codigo,
                        pc.codigo_cluster,pc.nombre_estudio, pc.distrito, pc.tipo_enlace_2, d.path_expediente_diseno, sp.idProyecto, 
                    ROUND(COALESCE(pc.costo_materiales, 0)+ COALESCE(pc.costo_mat_edif, 0), 2) as costo_materiales,
                    (SELECT SUM(costo_total) FROM planobra_po where itemplan = po.itemplan AND flg_tipo_area = 1 and estado_po NOT IN (7,8)) as costo_po_mat,
                    (SELECT SUM(costo_total) FROM planobra_po where itemplan = po.itemplan AND flg_tipo_area = 2 and estado_po NOT IN (7,8)) as costo_po_mo,
                    ROUND(COALESCE(pc.costo_mano_obra, 0)+ COALESCE(pc.costo_mo_edif, 0) + COALESCE(pc.costo_oc, 0) + COALESCE(pc.costo_oc_edif, 0), 2) costo_mano_obra

                FROM
                    planobra po LEFT JOIN planobra_cluster pc ON  po.itemplan = pc.itemplan,		
                    diseno d,
                    subproyecto sp,
                    empresacolab e,
                    central c
                WHERE
                    po.idSubProyecto = sp.idSubProyecto
                AND	po.idEmpresaColab = e.idEmpresaColab
                AND po.idCentral = c.idCentral
                AND po.itemplan = d.itemplan
                AND d.idEstacion  = 5
                AND po.idEstadoPlan = 7
                AND (sp.idProyecto 	= 3 OR sp.idSubProyecto = 734 OR sp.idSubProyecto = 741 OR sp.idSubProyecto = 737 OR sp.idSubProyecto = 743)
                AND po.itemplan           = COALESCE(?, po.itemplan)
                AND po.indicador           = COALESCE(?, po.indicador)";
        $result = $this->db->query($sql, array($itemplan, $sisego));
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

    function validarOperaciones($arrayUpdate) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {	
            $this->db->insert('validacion_operaciones', $arrayUpdate);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se registro en validacion_operaciones';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se registró correctamente';
                $data['error'] = EXIT_SUCCESS;
                
            }        
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }	

    function rechazarOperaciones($validaOpera, $dataPlanObra, $itemplan) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {	
            $this->db->trans_begin();
            $this->db->where('itemplan', $dataPlanObra['itemplan']);
            $this->db->update('planobra', $dataPlanObra);
            if($this->db->affected_rows() <= 0) {                     
                throw new Exception('No se actualizo Plan Obra');
            } else {               
                $this->db->insert('validacion_operaciones', $validaOpera);
                if($this->db->affected_rows() <= 0) {
                    $data['msj'] = 'No se registro en validacion_operaciones';
                    $data['error'] = EXIT_ERROR;
                } else {
                    $this->db->where('itemplan', $itemplan);
                    $this->db->update('itemplanestacionavance', array('path_pdf_pruebas' => null, 'path_pdf_perfil'   => null));                      
                    if ($this->db->trans_status() === false) {                        
                        throw new Exception('Hubo un error al actualizar itemplanestacionavance.');
                    }else{
                        $this->db->trans_commit();
                        $data['msj'] = 'Se actualizo correctamente!';
                        $data['error'] = EXIT_SUCCESS;
                    }
                }             
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }
    
}
