<?php

class M_bolsa_pep extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    }
 
    function getBolsaPep($estado, $pep1, $pep2, $proyecto, $subproyecto, $fase) { 
		
        $Query="SELECT bp.id, bp.pep1, bp.pep2, m.descripcion as mes, sp.subProyectoDesc, f.faseDesc, 
				(CASE WHEN bp.tipo_pep = 1 THEN 'MAT'
				WHEN bp.tipo_pep = 2 THEN 'MO'
				WHEN bp.tipo_pep = 3 THEN 'MAT Y MO' END) as tipo_pep, 
				(CASE WHEN bp.estado = 1 THEN 'ACTIVO'
					WHEN bp.estado = 2 THEN 'INACTIVO' END) as estado, bp.fecha_registro, u.nombre_completo, bp.estado as estado_id
				from bolsa_pep bp, subproyecto sp, fase f, mes m, usuario u
				where bp.idSubProyecto = sp.idSubProyecto
				and bp.idFase 	= f.idFase
				and bp.usuario_registro = u.id_usuario
				and bp.mes 		      = m.id
				and bp.estado 		  = ?
				AND bp.pep1           = COALESCE(?, bp.pep1)
				AND bp.pep2           = COALESCE(?,bp.pep2)
				AND sp.idProyecto     = COALESCE(?,sp.idProyecto)
				AND bp.idSubProyecto  = COALESCE(?,bp.idSubProyecto)
				AND bp.idFase         = COALESCE(?,bp.idFase )";
			
			 
        $result = $this->db->query($Query, array($estado, $pep1, $pep2, $proyecto, $subproyecto, $fase));
		log_message('error', $this->db->last_query());
        return $result->result_array();
    }

	function regBolsaPepConfig($listConfigBoPep) {
		$data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
			$this->db->insert_batch('bolsa_pep', $listConfigBoPep);
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
			$this->db->update('bolsa_pep', $dataUpdate);
			if($this->db->affected_rows() <= 0) {
				$data['msj'] = 'No se actualizo la entidad bolsa_pep.';
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
