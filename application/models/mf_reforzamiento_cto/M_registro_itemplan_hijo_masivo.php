<?php

class M_registro_itemplan_hijo_masivo extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
     
    function getCtoByFlgTipoAndNroDepa($nroDepa, $flgTipo){
	    $sql= " SELECT id,nro_departamentos,nro_cto_colocar 
		          FROM config_cto_x_nro_depa_cv 
				 WHERE nro_departamentos = ?
				   AND flg_tipo = ? ";
	   $result = $this->db->query($sql,array($nroDepa, $flgTipo));
	   if($result->num_rows() > 0) {
	       return $result->row_array();
	   } else {
	       return null;
	   }
	}

    function getCostoxDptoByIdEECCAndSubProy($idSubProyecto, $idEECC, $num_depa){

	    $sql = "  SELECT * 
		            FROM cv_costo_x_subproy_x_eecc_x_depa
				   WHERE idSubProyecto = ?
					 AND idEmpresaColab = ?
			         AND (CASE WHEN ? > 100 THEN min_depa > 100
						       ELSE (? >= min_depa AND ? <= max_depa) END)
				   LIMIT 1 ";
	    $result = $this->db->query($sql,array($idSubProyecto, $idEECC, $num_depa, $num_depa, $num_depa));
		//log_message('error', $this->db->last_query());
	    if($result->num_rows() > 0) {	       
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
	
	function getCodigoInversionxEECC($codigoInversion, $idEECC){

	    $sql = "  SELECT COUNT(*) cantidad
		            FROM empresacolab_x_inversion 
		           WHERE codigoInversion = ?
		             AND idEmpresaColab = ? ";
	    $result = $this->db->query($sql,array($codigoInversion, $idEECC));     
	    return $result->row()->cantidad;
	}

	function registrarItemplanMasivoCV($dataInsert) {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;
		try{
			$this->db->trans_begin();
			$this->db->insert('planobra', $dataInsert);
			if($this->db->affected_rows() != 1) {
				$this->db->trans_rollback();
				throw new Exception('Hubo un error al registrar en la tabla planobra');
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

	function insertLogItemplanCvMasivo($dataArray){
		$this->db->insert_batch('log_registro_itemplan_masivo_cv', $dataArray);
	}

	function crearSolCreacionForItemplan($dataPlanobra, $solicitud_oc_creacion, $item_x_sol){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
    
            $this->db->trans_begin();               
            $this->db->where('itemplan', $dataPlanobra['itemplan']);
            $this->db->update('planobra',$dataPlanobra);
            if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
                throw new Exception('Hubo un error al actualizar en planobra.');
            }else{
                $this->db->insert('solicitud_orden_compra', $solicitud_oc_creacion);
                if($this->db->affected_rows() != 1) {
                    $this->db->trans_rollback();
                    throw new Exception('Error al insertar en solicitud_orden_compra');
                }else{
                    $this->db->insert('itemplan_x_solicitud_oc', $item_x_sol);
                    if($this->db->affected_rows() != 1) {
                        $this->db->trans_rollback();
                        throw new Exception('Error al insertar en itemplan_x_solicitud_oc');
                    }else{
                		$data['error'] = EXIT_SUCCESS;
						$data['msj'] = 'Se actualizó correctamente!!';
						$this->db->trans_commit();
                    }
                }
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }

	function crearSolCreacionForItemplanb2b($dataPlanobra, $solicitud_oc_creacion, $item_x_sol, $upd_cluster, $registroDetalleb2b, $logSeguimientoB2b){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
    
            $this->db->trans_begin();               
            $this->db->where('itemplan', $dataPlanobra['itemplan']);
            $this->db->update('planobra',$dataPlanobra);
            if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
                throw new Exception('Hubo un error al actualizar en planobra.');
            }else{
                $this->db->insert('solicitud_orden_compra', $solicitud_oc_creacion);
                if($this->db->affected_rows() != 1) {
                    $this->db->trans_rollback();
                    throw new Exception('Error al insertar en solicitud_orden_compra');
                }else{
                    $this->db->insert('itemplan_x_solicitud_oc', $item_x_sol);
                    if($this->db->affected_rows() != 1) {
                        $this->db->trans_rollback();
                        throw new Exception('Error al insertar en itemplan_x_solicitud_oc');
                    }else{
						$this->db->where('codigo_cluster', $upd_cluster['codigo_cluster']);
						$this->db->update('planobra_cluster',$upd_cluster);
						if ($this->db->trans_status() === FALSE) {
							$this->db->trans_rollback();
							throw new Exception('Hubo un error al actualizar en planobra.');
						}else{
							$this->db->insert('planobra_detalle_b2b', $registroDetalleb2b);
							if($this->db->affected_rows() != 1) {
								$this->db->trans_rollback();
								throw new Exception('Hubo un error al registrar en la tabla detalle_cv_hijo');
							}else{
								$this->db->insert('log_seguimiento_b2b', $logSeguimientoB2b);
								if($this->db->affected_rows() != 1) {
									throw new Exception('Hubo un error al registrar en la tabla log_seguimiento_cv');
								}else{
									$this->db->trans_commit();
									$data['msj'] = "Se registró correctamente el itemplan";
									$data['error'] = EXIT_SUCCESS;
								}
							}			 
						}
                    }
                }
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }
    
}
