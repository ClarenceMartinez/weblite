<?php

class M_registro_po_mo extends CI_Model {

    function __construct() {
        parent::__construct();
    } 

    function getPartidasToCreatePoMoByItemplanEstacion($itemplan, $idEstacion) {
	    $sql = "  SELECT pa.codigoPartida, pa.descripcion AS partidaDesc, tp.descripcion as tipo, pre.costo, pa.baremo 
                    FROM proyecto_estacion_partida_mo pep, partida pa, planobra po, subproyecto sp, pqt_tipo_preciario tp, pqt_preciario pre, central c
                    WHERE pep.codigoPartida = pa.codigoPartida
                    AND pep.idProyecto 	    = sp.idProyecto
                    AND sp.idSubProyecto    = po.idSubProyecto
                    AND pa.idTipoPreciario  = tp.id
                    AND pre.idEmpresaColab  = po.idEmpresaColab
                    AND pre.idTipoPreciario = pa.idTipoPreciario
                    AND po.idCentral        = c.idCentral
                    AND pre.tipoJefatura    = (CASE WHEN c.idJefatura = 13 /*LIMA*/ THEN 1 ELSE 2 END)
                    AND po.itemplan         = ?
                    AND pep.idEstacion      = ? ";
	    $result = $this->db->query($sql, array($itemplan, $idEstacion));
	    return $result->result();
	}

    public function getInfoPartidaExisteByItemplanEstacionPartida($itemplan, $idEstacion, $codigoPartida) {
        $sql = " SELECT pa.codigoPartida, pa.descripcion AS partidaDesc, tp.descripcion as tipo, pre.costo, pa.baremo 
                FROM proyecto_estacion_partida_mo pep, partida pa, planobra po, subproyecto sp, pqt_tipo_preciario tp, pqt_preciario pre, central c
                WHERE pep.codigoPartida = pa.codigoPartida
                AND pep.idProyecto 	    = sp.idProyecto
                AND sp.idSubProyecto    = po.idSubProyecto
                AND pa.idTipoPreciario  = tp.id
                AND pre.idEmpresaColab  = po.idEmpresaColab
                AND pre.idTipoPreciario = pa.idTipoPreciario
                AND po.idCentral        = c.idCentral
                AND pre.tipoJefatura = (CASE WHEN c.idJefatura = 13 /*LIMA*/ THEN 1 ELSE 2 END)
                AND po.itemplan = ?
                AND pep.idEstacion = ?
                AND pa.codigoPartida = ? ";
        $result = $this->db->query($sql, array($itemplan, $idEstacion, $codigoPartida));
        if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
    }

    function   getPartidasBasicByPtr($codigo_po){
	    $Query = 'SELECT    pa.codigoPartida,pa.descripcion as descPartida, pd.descripcion as tipoPartida, pod.preciario as precio, 
                            pod.baremo, pod.cantidadInicial as cantidad_ingresada, pod.montoInicial as costo_total
                FROM        planobra_po_detalle_mo pod, partida pa, pqt_tipo_preciario pd
                where       pod.codigoPartida = pa.codigoPartida
                AND 	    pa.idTipoPreciario = pd.id
                AND         pod.codigo_po =  ?';	
	    $result = $this->db->query($Query,array($codigo_po));
        return $result->result_array();
	}

    function getPartidasToLiquiPoMo($codigo_po, $itemplan, $idEstacion) {
	    $sql = " SELECT pa.codigoPartida, pa.descripcion AS partidaDesc, tp.descripcion as tipo, pre.costo, pa.baremo,pdmo.cantidadInicial 
                    FROM proyecto_estacion_partida_mo pep, partida pa LEFT JOIN 	planobra_po_detalle_mo pdmo 
                                    ON 			pa.codigoPartida = pdmo.codigoPartida 
                                    AND 		pdmo.codigo_po = ?, planobra po
                    , subproyecto sp, pqt_tipo_preciario tp, pqt_preciario pre, central c
                    WHERE pep.codigoPartida = pa.codigoPartida
                    AND pep.idProyecto 	    = sp.idProyecto
                    AND sp.idSubProyecto    = po.idSubProyecto
                    AND pa.idTipoPreciario  = tp.id
                    AND pre.idEmpresaColab  = po.idEmpresaColab
                    AND pre.idTipoPreciario = pa.idTipoPreciario
                    AND po.idCentral        = c.idCentral
                    AND pre.tipoJefatura    = (CASE WHEN c.idJefatura = 13 /*LIMA*/ THEN 1 ELSE 2 END)
                    AND po.itemplan         = ?
                    AND pep.idEstacion      = ?;";
	    $result = $this->db->query($sql, array($codigo_po, $itemplan, $idEstacion));
        log_message('error', $this->db->last_query());
	    return $result->result();
	}

    function editPoMo($dataPo, $arrayDetalleInsert, $dataLogPO) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try {
            $this->db->where('codigo_po', $dataPo['codigo_po']);
            $this->db->update('planobra_po', $dataPo);
            if ($this->db->trans_status() === false) {
                $data['msj'] = 'error update planobra_po';
                $data['error'] = EXIT_ERROR;
            } else {
                $this->db->where('codigo_po', $dataPo['codigo_po']);
                $this->db->delete('planobra_po_detalle_mo');
    
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Hubo un error al eliminar en la tabla planobra_po_detalle_mo!!');
                }else{

                    $this->db->insert_batch('planobra_po_detalle_mo', $arrayDetalleInsert);
                    if($this->db->affected_rows() <= 0) {
                        $data['msj'] = 'No se registro la po';
                        $data['error'] = EXIT_ERROR;
                    } else {
                        $this->db->insert('log_planobra_po_edit', $dataLogPO);
                        if($this->db->affected_rows() <= 0) {
                            $data['msj'] = 'No se registro el log de la PO';
                            $data['error'] = EXIT_ERROR;
                        } else {
                            $data['msj'] = 'Se registro correctamente';
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
    
    function liquidarPO($dataLogPO, $dataUpdate, $codigo_po, $itemplan){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();
	        $this->db->insert('log_planobra_po', $dataLogPO);
	        if($this->db->affected_rows() != 1) {
	            $this->db->trans_rollback();
	            throw new Exception('Error al insertar en log_planobra_po');
	        }else{
	            $this->db->where('codigo_po', $codigo_po);
	            $this->db->where('itemplan', $itemplan);
	            $this->db->update('planobra_po', $dataUpdate);
	             
	            if($this->db->trans_status() === FALSE) {
	                $this->db->trans_rollback();
	                throw new Exception('Error al modificar el updateEstadoPlanObra');
	            }else{
	                    $data['error'] = EXIT_SUCCESS;
	                    $data['msj'] = 'Se actualizo correctamente!';
	                    $this->db->trans_commit();
                }	            
	        }
	    }catch(Exception $e){
	        $data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $data;
	}
	
	function gestEstadoPoByItemplanPoCod($itemplan, $codigo_po){
	    $Query = "SELECT estado_po FROM planobra_po WHERE itemplan = ? and codigo_po = ?";
	    $result = $this->db->query($Query,array($itemplan, $codigo_po));
	    if($result->row() != null) {
	        return $result->row_array()['estado_po'];
	    } else {
	        return null;
	    }
	}
    
}
