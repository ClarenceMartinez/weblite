<?php

class M_registro_solicitud_cotizacion extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
	function insertSolicitudCotizacion($dataInsert) {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;
		try{
			$this->db->trans_begin();	 
			$this->db->insert('planobra_cluster', $dataInsert);
			if($this->db->affected_rows() != 1) {
				throw new Exception('Hubo un error al registrar en la tabla planobra_cluster');
			}else{
				$this->db->trans_commit();
				$data['msj'] = "Se registró correctamente el itemplan";
				$data['error'] = EXIT_SUCCESS;			 
			}			 
		}catch(Exception $e){
			$data['msj'] = $e->getMessage();
			$this->db->trans_rollback();
		}
        return $data;
    }

	function existSisegoEstudioActivo($sisego, $estudio){
	    $sql = "SELECT 	count(1) exist_cotizacion 
				FROM 	planobra_cluster pc LEFT JOIN planobra po ON pc.itemplan = po.itemplan
				WHERE 	pc.sisego = ?
				AND 	pc.nombre_estudio = ?				
				AND 	pc.estado not in (3)
				AND     (CASE WHEN po.itemplan IS NOT NULL THEN po.idEstadoPlan NOT IN (6) ELSE TRUE END)";
	    $result = $this->db->query($sql,array($sisego, $estudio));
	    if($result->row() != null) {
	        return $result->row_array()['exist_cotizacion'];
	    } else {
	        return null;
	    }
	}
	
}
