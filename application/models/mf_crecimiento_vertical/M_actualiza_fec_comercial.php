<?php

class M_actualiza_fec_comercial extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
	function updateFechaEntregaComercial($dataPoDetCvHijo, $dataLogFecCom) {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;
		try{
			$this->db->trans_begin();
		 
			$this->db->where('ip_hijo', $dataPoDetCvHijo['ip_hijo']);
            $this->db->update('planobra_detalle_cv_hijo', $dataPoDetCvHijo);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al actualizar la planobra_detalle_cv_hijo agenda_cv_item');
            }else{
				$this->db->insert('log_fecha_entrega_comercial_cv', $dataLogFecCom);
				if($this->db->affected_rows() != 1) {
					throw new Exception('Hubo un error al registrar en la tabla log_fecha_entrega_comercial_cv');
				}else{
					$this->db->trans_commit();
					$data['msj'] = "Se registró correctamente el itemplan";
					$data['error'] = EXIT_SUCCESS;
				}
			}			 
		}catch(Exception $e){
			$data['msj'] = $e->getMessage();
			$this->db->trans_rollback();
		}
        return $data;
    }

	function existItemplan($itemplan){
	    $sql = "SELECT count(1) exist_ip 
                from planobra 
                where itemplan = ?";
	    $result = $this->db->query($sql,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array()['exist_ip'];
	    } else {
	        return null;
	    }
	}	
	
	function existItemplanHijo($itemplan_hijo){
	    $sql = "SELECT count(1) exist_ip 
                from planobra_detalle_cv_hijo 
                where ip_hijo = ?";
	    $result = $this->db->query($sql,array($itemplan_hijo));
	    if($result->row() != null) {
	        return $result->row_array()['exist_ip'];
	    } else {
	        return null;
	    }
	}	 
    
	function getBasicInfoItemplan($itemplan){
	    $sql = "SELECT po.itemplan, sp.subProyectoDesc, ec.empresaColabDesc, po.codigoInversion 
				FROM planobra po, subproyecto sp, empresacolab ec
				WHERE po.itemplan = ?
				AND po.idSubProyecto = sp.idSubProyecto
				and po.idEmpresaColab = ec.idEmpresaColab";
	    $result = $this->db->query($sql,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

	function exist_ip_hijo_info($ip_hijo) {
	    $sql = "SELECT dcv.itemplan, dcv.ip_hijo, ms.desc_motivo, sp.subProyectoDesc, ec.empresaColabDesc, po.idEstadoPlan
				FROM planobra_detalle_cv_hijo dcv, motivo_seguimiento_cv ms, planobra po, subproyecto sp, empresacolab ec
				WHERE dcv.ult_situa_especifica = ms.id
				AND dcv.itemplan = po.itemplan
				AND po.idSubProyecto = sp.idSubProyecto
				AND po.idEmpresaColab = ec.idEmpresaColab
                AND dcv.ip_hijo = ? ";
	    $result = $this->db->query($sql, array($ip_hijo));
		if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

	function existSituacionEspecifica($descripcion){
	    $sql = "SELECT * FROM motivo_seguimiento_cv WHERE desc_motivo = TRIM(UPPER(?));";
	    $result = $this->db->query($sql,array($descripcion));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
	
}
