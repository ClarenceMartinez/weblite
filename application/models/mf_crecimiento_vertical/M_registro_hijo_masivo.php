<?php

class M_registro_hijo_masivo extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
  

	function registroHijosMasivo($dataRegistro, $dataInsertSeguimiento) {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;
		try{
			$this->db->trans_begin();
		 
			$this->db->insert('planobra_detalle_cv_hijo', $dataRegistro);
			if($this->db->affected_rows() != 1) {
				$this->db->trans_rollback();
				throw new Exception('Hubo un error al registrar en la tabla detalle_cv_hijo');
			}else{
				$this->db->insert('log_seguimiento_cv', $dataInsertSeguimiento);
				if($this->db->affected_rows() != 1) {
					throw new Exception('Hubo un error al registrar en la tabla log_seguimiento_cv');
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
	    $sql = "SELECT po.itemplan, sp.subProyectoDesc, ec.empresaColabDesc, po.codigoInversion, po.orden_compra 
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
	
}
