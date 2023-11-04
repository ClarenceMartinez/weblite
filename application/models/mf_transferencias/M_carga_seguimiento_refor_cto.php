<?php

class M_carga_seguimiento_refor_cto extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function validateExistCombinatoria($itemplan, $ctoAdjudicado, $tipoReforza, $ctoFinal) {
        $sql = " SELECT fr.id_formulario, fr.has_seguimiento, po.situacion_general_reforzamiento
                    FROM formulario_reforzamientos fr, planobra po
                    WHERE po.itemplan = fr.itemplan
                  AND  fr.itemplan = ?
                  AND   fr.cto_ajudi = ?
                  AND   fr.tipo_refo = ?
                  AND   fr.do_splitter = ?";
        $result = $this->db->query($sql, array($itemplan, $ctoAdjudicado, $tipoReforza, $ctoFinal));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }
    

    function getToUpdateSirope() {
        $sql = "SELECT po.itemplan, te.estado_actual FROM planobra po JOIN tmp_ot_estado te ON po.itemplan = te.itemplan 
                AND te.estado_actual = '4 - Con datos permanentes'
                WHERE po.has_sirope_fo IS NULL
                UNION ALL
                SELECT po.itemplan, te.estado_actual  FROM planobra po JOIN tmp_ot_estado te ON po.itemplan = te.itemplan
                AND te.estado_actual IN ('3 - En construcción','9 - En actualización')
                WHERE po.has_sirope_diseno IS NULL";
        $result = $this->db->query($sql, array());
        return $result->result_array();
    }

    
    function updateSiropeEstadosTransferencia(){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->trans_begin();
            $this->db->query("UPDATE planobra po JOIN tmp_ot_estado te ON po.itemplan = te.itemplan 
                                AND te.estado_actual = '4 - Con datos permanentes'
                                SET po.has_sirope_fo	=	1,	has_sirope_fo_fecha	=	NOW(), ult_codigo_sirope = te.codigo_ot, ult_estado_sirope = te.estado_actual
                                WHERE po.has_sirope_fo IS NULL");         
            if ($this->db->trans_status() === TRUE) {                               
                $this->db->query("UPDATE planobra po JOIN tmp_ot_estado te ON po.itemplan = te.itemplan
                                    AND te.estado_actual IN ('3 - En construcción','9 - En actualización')
                                    SET po.has_sirope_diseno	=	1,	has_sirope_diseno_fecha	=	NOW(), ult_codigo_sirope = te.codigo_ot, ult_estado_sirope = te.estado_actual
                                    WHERE po.has_sirope_diseno IS NULL");         
                if ($this->db->trans_status() === TRUE) {                               
                    $data['msj'] = 'Transaccion Exitosa!';
                    $data['error']= EXIT_SUCCESS;
                    $this->db->trans_commit();
                }else{
                    $this->db->trans_rollback();
                    throw new Exception('ERROR UPDATE FROM planobra - 3');
                }  
            }else{
                $this->db->trans_rollback();
                throw new Exception('ERROR UPDATE FROM planobra - 4');
            }                                      
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    } 
 
}
