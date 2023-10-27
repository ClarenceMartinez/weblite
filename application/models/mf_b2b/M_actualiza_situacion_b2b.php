<?php

class M_actualiza_situacion_b2b extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
	function registroSeguiHijosMasivo($dataPoDetCvHijo, $dataInsertSeguimiento) {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;
		try{
			$this->db->trans_begin();
		 
			$this->db->where('itemplan', $dataPoDetCvHijo['itemplan']);
            $this->db->update('planobra_detalle_b2b', $dataPoDetCvHijo);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al actualizar la planobra_detalle_b2b agenda_cv_item');
            }else{
				$this->db->insert('log_seguimiento_b2b', $dataInsertSeguimiento);
				if($this->db->affected_rows() != 1) {
					throw new Exception('Hubo un error al registrar en la tabla log_seguimiento_b2b');
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

	function exist_ip_hijo_b2b($ip_hijo) {
	    $sql = "SELECT dcv.itemplan, ms.desc_motivo, sp.subProyectoDesc, ec.empresaColabDesc, po.idEstadoPlan
				FROM planobra_detalle_b2b dcv, motivo_seguimiento_b2b ms, planobra po, subproyecto sp, empresacolab ec
				WHERE dcv.ult_situa_especifica = ms.id
				AND dcv.itemplan = po.itemplan
				AND po.idSubProyecto = sp.idSubProyecto
				AND po.idEmpresaColab = ec.idEmpresaColab
				AND dcv.itemplan =  ? ";
	    $result = $this->db->query($sql, array($ip_hijo));
		if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

	function existSituacionEspecifica($descripcion){
	    $sql = "SELECT * FROM motivo_seguimiento_b2b WHERE estado = 1 and flg_automatico = 2 and desc_motivo = TRIM(UPPER(?));";
	    $result = $this->db->query($sql,array($descripcion));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}	
	
}
