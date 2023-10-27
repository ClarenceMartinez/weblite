<?php

class M_carga_estados_ot_sirope extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function   uploadFileSiropeToTmpTable($pathFinal){
        $data ['error']= EXIT_ERROR;
        $data['msj'] = null;
        try{
            $this->db->trans_begin();
            $this->db->from('tmp_ot_estado');
            $this->db->truncate();
            if ($this->db->trans_status() === TRUE) {
				//log_message('error', 'path:'.$pathFinal);
                $string_1 = '\n';
                $string_2 = '"';
                $this->db->query("LOAD DATA LOCAL INFILE '".$pathFinal."' INTO TABLE tmp_ot_estado
                                    CHARACTER SET latin1
                                    FIELDS TERMINATED BY ',' 
                                    OPTIONALLY ENCLOSED BY '".$string_2."'
                                    LINES TERMINATED BY '".$string_1."'
                                    IGNORE 1 LINES
                                    (`id`, `codigo_ot`, `caracteristica`, `estado_actual`);");
				//log_message('error', 'path:'.$pathFinal);
				//log_message('error', $this->db->last_query());				
                if ($this->db->trans_status() === TRUE) {
                    $this->db->query("DELETE FROM tmp_ot_estado WHERE codigo_ot NOT LIKE '%FO'");
                    //log_message('error', $this->db->last_query());		
                    if ($this->db->trans_status() === TRUE) {
                        $this->db->query("UPDATE tmp_ot_estado	SET	estado_actual	=	replace(estado_actual,'\r',''), itemplan = SUBSTRING_INDEX(codigo_ot,'FO',1);");
                        //log_message('error', $this->db->last_query());		
                        if ($this->db->trans_status() === TRUE) {                               
                                $data['msj'] = 'Transaccion Exitosa!';
                                $data['error']= EXIT_SUCCESS;
                                $this->db->trans_commit();
                            }else{
                                $this->db->trans_rollback();
                                throw new Exception('ERROR UPDATE FROM tmp_ot_estado 2');
                            }                           
                        }else{
                            $this->db->trans_rollback();
                            throw new Exception('ERROR DELETE FROM tmp_ot_estado 1');
                        }
                }else{
                    $this->db->trans_rollback();
                    throw new Exception('ERROR LOAD DATA LOCAL INFILE');
                }
            } else {
                $this->db->trans_rollback();
                throw new Exception('ERROR TRUNCATE tmp_ot_estado');
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
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
