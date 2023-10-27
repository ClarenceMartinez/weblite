<?php

class M_itemplan_pep2 extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    }
 
    function getBolsaPep($estado, $pep1, $pep2, $proyecto, $subproyecto, $fase) { 
		
        $Query="SELECT po.itemplan, bp.id, bp.pep1, bp.pep2, sp.subProyectoDesc, f.faseDesc, 
				(CASE WHEN bp.tipo_pep = 1 THEN 'MAT'
				WHEN bp.tipo_pep = 2 THEN 'MO'
				WHEN bp.tipo_pep = 3 THEN 'MAT Y MO' END) as tipo_pep, 
				(CASE WHEN bp.estado = 1 THEN 'ACTIVO'
					WHEN bp.estado = 2 THEN 'INACTIVO' END) as estado, bp.fecha_registro, u.nombre_completo, bp.estado as estado_id
				from itemplan_pep2_grafo bp, planobra po, subproyecto sp, fase f, usuario u
				where po.itemplan = bp.itemplan
				and po.idFase = f.idFase
				and  po.idSubProyecto = sp.idSubProyecto
				and bp.usuario_registro = u.id_usuario
				and bp.estado 		  = ?
				AND bp.pep1           = COALESCE(?, bp.pep1)
				AND bp.pep2           = COALESCE(?,bp.pep2)
				AND sp.idProyecto     = COALESCE(?,sp.idProyecto)
				AND po.idSubProyecto  = COALESCE(?,po.idSubProyecto)
				AND po.idFase         = COALESCE(?,po.idFase )";
			
			 
        $result = $this->db->query($Query, array($estado, $pep1, $pep2, $proyecto, $subproyecto, $fase));
		//log_message('error', $this->db->last_query());
        return $result->result_array();
    }

	function regBolsaPepConfig($listConfigBoPep) {
		$data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
			$this->db->insert_batch('itemplan_pep2_grafo', $listConfigBoPep);
			if($this->db->affected_rows() > 0) {
				$data['error'] = EXIT_SUCCESS;
				$data['msj'] = 'Se insertó correctamente!';
			}else{
				$data['error'] = EXIT_ERROR;
				$data['msj'] = 'Error al insertar en la tabla bolsa_pep.';
			}
		}catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
		return $data;
	}

	function actualizaConfigBoPep($dataUpdate) {
		$data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
			$this->db->where('id', $dataUpdate['id']);
			$this->db->update('itemplan_pep2_grafo', $dataUpdate);
			if($this->db->affected_rows() <= 0) {
				$data['msj'] = 'No se actualizo la entidad itemplan_pep2_grafo.';
				$data['error'] = EXIT_ERROR;
			} else {
				$data['msj'] = 'Se registro correctamente';
				$data['error'] = EXIT_SUCCESS;
			}
		}catch(Exception $e){
			$data['msj'] = $e->getMessage();
		}
        return $data;
    }

}
