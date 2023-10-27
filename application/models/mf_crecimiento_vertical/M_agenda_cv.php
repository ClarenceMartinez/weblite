<?php

class M_agenda_cv extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
    function getInfoItemplanByItem($itemplan){
	    $sql = "   SELECT po.*,sp.subProyectoDesc, ec.empresaColabDesc,
                          (CASE WHEN po.orden_compra IS NOT NULL AND soc.estado = 2 AND soc.tipo_solicitud = 1
						        THEN 1 ELSE 2  END) AS flg_tiene_oc
	                 FROM empresacolab ec, subproyecto sp, proyecto p,
                          planobra po
                LEFT JOIN solicitud_orden_compra soc ON po.solicitud_oc = soc.codigo_solicitud
                	WHERE po.idEmpresaColab = ec.idEmpresaColab
                	  AND po.idSubProyecto = sp.idSubProyecto
					  AND sp.idProyecto = p.idProyecto
					  AND p.idProyecto = 21#solo crecimiento cv(cableado de edificios) 
                	  AND po.itemplan = ?
                	LIMIT 1";
	    $result = $this->db->query($sql,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function getAllBandaHorariaCV(){
	    $sql = " SELECT * 
                   FROM banda_horaria_cv
	              WHERE estado = 1 ";
	    $result = $this->db->query($sql);
	    return $result->result();
	}

    function getInfoBandaHorariaByID($bandaHoraria){
	    $sql = "SELECT *
                    FROM banda_horaria_cv
        	       WHERE id  = ?
                	LIMIT 1";
	    $result = $this->db->query($sql,array($bandaHoraria));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function haveCitaPendiente($itemplan) {
        $sql = " SELECT COUNT(1) AS count 
                  FROM agenda_cv_item 
                 WHERE itemplan = ? 
                   AND estado IN (1,5) ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->row()->count;
    }

    function existOnBandejaDevolucion($itemplan) {
	    $sql = " SELECT COUNT(1) AS count 
                   FROM cv_bandeja_devolucion
                  WHERE itemplan = ? ";
	    $result = $this->db->query($sql, array($itemplan));
	    return $result->row()->count;
	}

    function createCVCita($dataCvItem) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->insert('agenda_cv_item', $dataCvItem);
	        if($this->db->affected_rows() != 1) {
	            throw new Exception('Error al insertar en agenda_cv_item');	      
            }else{
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!';
            } 
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function createSeguimientoCV($dataCvItem) {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->insert('log_seguimiento_cv', $dataCvItem);
            if($this->db->affected_rows() != 1) {
                throw new Exception('Error al insertar en log_seguimiento_cv');
            }else{
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se insertó correctamente!';
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getDetalleCitasByItemplan($itemplan){
	    $sql = " SELECT ac.*, 
                        UPPER(concat(u1.nombre_completo, ' ', u1.ape_paterno, ' ', u1.ape_materno)) AS usuario_registro,
                        UPPER(concat(u2.nombre_completo, ' ', u2.ape_paterno, ' ', u2.ape_materno)) AS usuario_update,
                        acm.descripcion AS motivo_cancela, acmr.descripcion AS motivo_reagenda_desc,
                        msc.desc_motivo,
                        sp.subProyectoDesc,
                        ec.empresaColabDesc
                        FROM planobra po,
                             subproyecto sp,
                             empresacolab ec,
                             agenda_cv_item ac 
                        LEFT JOIN usuario u1 ON ac.usuario_registro = u1.id_usuario
                        LEFT JOIN usuario u2 ON ac.usuario_ultimo_estado = u2.id_usuario
                        LEFT JOIN agenda_cv_motivo_cancela acm ON ac.motivo_cancela = acm.idMotivo
                        LEFT JOIN agenda_cv_motivo_reagendar acmr ON ac.motivo_reagenda = acmr.idMotivo
                        LEFT JOIN motivo_cv_sincontacto msc ON ac.id_motivo_sincontacto = msc.id
                        WHERE po.itemplan = ac.itemplan
                          AND po.idSubProyecto = sp.idSubProyecto
                          AND po.idEmpresaColab = ec.idEmpresaColab
                          AND ac.itemplan = ?
                     ORDER BY ac.fecha_cita DESC, ac.fecha_registro DESC " ;
	    $result = $this->db->query($sql,array($itemplan));
	    return $result->result();
	}

    function updateCita($dataCita){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{    
            $this->db->where('id_agenda_cv_item', $dataCita['id_agenda_cv_item']);
            $this->db->update('agenda_cv_item', $dataCita);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al actualizar la tabla agenda_cv_item');
            }else{
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!';
            }	        
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function getMotivoCancela(){
	    $sql = " SELECT *
                   FROM agenda_cv_motivo_cancela
	              WHERE estado = 1 " ;
	    $result = $this->db->query($sql,array());
	    return $result->result();
	}
    
    function insertBandejaDev($dataInsert) {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->insert('cv_bandeja_devolucion', $dataInsert);
            if($this->db->affected_rows() != 1) {
                throw new Exception('Error al insertar en cv_bandeja_devolucion');
            }else{
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se insertó correctamente!';
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getAllMotivoReagendar(){
        $sql = " SELECT * 
                   FROM agenda_cv_motivo_reagendar
                  WHERE estado = 1 ";
        $result = $this->db->query($sql,array());
        return $result->result();
    }

    public function getMotivosCVSC()
    {
        $sql = "  SELECT id,desc_motivo,estado
		            FROM motivo_cv_sincontacto
				   WHERE estado = '1' ";

        $result = $this->db->query($sql);
        return $result->result();
    }

    function getCountSinContactoAndCantConfig($itemplan) {
        $sql = "  SELECT COUNT(1) AS count,
		                 (SELECT cantidad FROM config_cantidad_sin_contaco_cv) cant_config
		            FROM agenda_cv_item 
				   WHERE itemplan = ? 
				     AND estado = 5 ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->row_array();
    }
}
