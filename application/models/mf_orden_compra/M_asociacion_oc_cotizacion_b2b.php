<?php

class M_asociacion_oc_cotizacion_b2b extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getInfoClusterToPutOC($codigo_solicitud){
	    $sql = "SELECT 
                        pc.codigo_cluster, pc.sisego, ec.empresaColabDesc, pc.estado, CASE	 WHEN pc.estado  = 0 THEN 'PDT COTIZACION'
                                                                    WHEN pc.estado  = 1 THEN 'PDT APROBACION'
                                                                    WHEN pc.estado  = 2 THEN 'APROBADO'
                                                                    WHEN pc.estado  = 3 THEN 'RECHAZADO'
                                                                    WHEN pc.estado  = 4 THEN 'PDT CONFIRMACION' END estadoDesc, pc.orden_compra from planobra_cluster pc, empresacolab ec
                                                                    WHERE pc.idEmpresaColab = ec.idEmpresaColab
                                                                    AND pc.codigo_cluster = ?
        
                LIMIT 1 ";
	    $result = $this->db->query($sql,array($codigo_solicitud));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function existeCLConOC($ordenCompra) {
        $sql = " SELECT COUNT(*) cantidad
		           FROM planobra_cluster WHERE orden_compra = ? ";
        $result = $this->db->query($sql, array($ordenCompra));
        return $result->row()->cantidad;
    }
	   
    function updateClusterB2b($arrayListClusterUpdate){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();	      
            $this->db->update_batch('planobra_cluster', $arrayListClusterUpdate, 'codigo_cluster');
            if($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception('Error al modificar el updateEstadoPlanObra');
            }else{	                        
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizo correctamente!';
                $this->db->trans_commit();	
            }
	    }catch(Exception $e){
	        $data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $data;
	}

}
