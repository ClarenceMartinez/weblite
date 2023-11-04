<?php

class M_matriz_seguimiento extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    }

    function getByItemPlan($itemplan) {
        $sql = "SELECT divicau FROM matrizseguimiento WHERE itemplan = ?";
        
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
        
    }

    function getInfoMatrizSegByItemPlan($itemplan, $divicau) {

        // $sql = "SELECT * FROM matrizseguimiento WHERE itemplan = ? AND divicau = ?";

        $sql = "SELECT ms.*, c.centralDesc, ec.empresaColabDesc, po.cantfactorplanificado
                -- , c.departamento, c.distrito, UPPER(pr.provinciaDesc) as provincia
                ,ms.departamento, ms.provincia, ms.distrito
                FROM matrizseguimiento ms
                LEFT JOIN planobra po ON (po.itemplan = ms.itemplan)
                LEFT JOIN central c ON (c.idCentral  = po.idCentral)
                LEFT JOIN empresacolab ec ON (ec.idEmpresaColab = po.idEmpresaColab)
                WHERE ms.itemplan = ? AND ms.divicau = ?";
        
        $result = $this->db->query($sql, array($itemplan, $divicau));

        return $result->result_array();
        
    }

    function getInfoMatrizSegByItemPlan2($itemplan) {

        // $sql = "SELECT * FROM matrizseguimiento WHERE itemplan = ? AND divicau = ?";

        $sql = "SELECT ms.*, c.centralDesc, ec.empresaColabDesc, po.cantfactorplanificado
                -- , c.departamento, c.distrito, UPPER(pr.provinciaDesc) as provincia
                ,ms.departamento, ms.provincia, ms.distrito
                FROM matrizseguimiento ms
                LEFT JOIN planobra po ON (po.itemplan = ms.itemplan)
                LEFT JOIN central c ON (c.idCentral  = po.idCentral)
                LEFT JOIN empresacolab ec ON (ec.idEmpresaColab = po.idEmpresaColab)
                
                WHERE ms.itemplan = ?";
        
        $result = $this->db->query($sql, array($itemplan));
        log_message('error',$this->db->last_query());
        return $result->result_array();
        
    }



    function getInfoMatrizSegByItemPlanLog($itemplan, $divicau) {
        $sql = "SELECT msl.*, u.nombre_completo FROM matrizseguimiento_log msl
                INNER JOIN usuario u ON (msl.usuario_id = u.id_usuario)";
        $result = $this->db->query($sql, array($itemplan, $divicau));
        return $result->result_array();
    }

    function getInfoMatrizSegByItemPlanLogByID($id) {
        $sql = "SELECT msl.*, u.nombre_completo FROM matrizseguimiento_log msl
                INNER JOIN usuario u ON (msl.usuario_id = u.id_usuario) WHERE msl.matrizSeguimiento_id = ?";
        $result = $this->db->query($sql, array($id));
        return $result->result_array();
    }


    function getInfoMatrizSegLogByItemPlan($itemplan) {
        $sql = "SELECT msl.*, u.nombre_completo FROM matrizseguimiento_log msl
                INNER JOIN usuario u ON (msl.usuario_id = u.id_usuario) WHERE msl.itemplan = ?";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function listNodos()
    {
        $sql = "SELECT nodo FROM matrizseguimiento  GROUP BY nodo";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getCableByNodo($nodo)
    {
        $sql = "SELECT cable FROM matrizseguimiento WHERE nodo = ? GROUP BY cable";
        $result = $this->db->query($sql, array($nodo));
        return $result->result_array();
    }


    function getInfoCableNodo($nodo, $cable)
    {
        $sql = "SELECT * FROM matrizseguimiento WHERE nodo = ? AND cable = ? ORDER BY 1 ASC";
        $result = $this->db->query($sql, array($nodo, $cable));
        return $result->result_array();
    }

    function getInfoCableNodoV2($nodo, $cable, $divicau)
    {
        $and = " AND numHilosPuertoOLT = '0'";
        if ($divicau != null)
        {
            $and = " AND divicau='{$divicau}'";
        }
        $sql = "SELECT * FROM matrizseguimiento WHERE nodo = ? AND cable = ? $and ORDER BY 1 ASC";
        $result = $this->db->query($sql, array($nodo, $cable));
        log_message('error',$this->db->last_query());

        return $result->result_array();
    }



    function verifyItemPanDivicau($itemplan, $divicau) {

        $sql = "SELECT * FROM matrizseguimiento WHERE itemplan = ? AND divicau = ?";
        
        $result = $this->db->query($sql, array($itemplan, $divicau));

        // log_message('error',$this->db->last_query());
        return $result->result_array();
        
    }

    function verifyByItemplan($itemplan) {

        $sql = "SELECT * FROM matrizseguimiento WHERE itemplan = ?";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
        
    }

    function searchDepartamentoProvinciaDistrito($departamento, $provincia, $distrito)
    {
        $sql = "";
    }


    function verifyItemPanInPlanObra($itemplan) {

        $sql = "SELECT * FROM planobra WHERE itemplan = ?";
        
        $result = $this->db->query($sql, array($itemplan));

        return $result->result_array();
        
    }

    function getInfoMatrizSegAll() {

        $sql = "SELECT ms.*, c.centralDesc, ec.empresaColabDesc, po.cantfactorplanificado
                -- , c.departamento, c.distrito, UPPER(pr.provinciaDesc) as provincia
                ,ms.departamento, ms.provincia, ms.distrito
                FROM matrizseguimiento ms
                INNER JOIN planobra po ON (po.itemplan = ms.itemplan)
                INNER JOIN central c ON (c.idCentral  = po.idCentral)
                INNER JOIN empresacolab ec ON (ec.idEmpresaColab = po.idEmpresaColab)
                LEFT JOIN distrito d ON (d.idDistrito = c.idDistrito)
                LEFT JOIN provincia pr ON (pr.idProvincia = d.idProvincia)";
        
        $result = $this->db->query($sql, array());

        log_message('error',$this->db->last_query());
        return $result->result_array();
        
    }

    function actualizaMatrizSeguimiento($dataUpdate, $data_old, $usuario_id, $id, $modulo)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->where('id', $dataUpdate['id']);
            $this->db->update('matrizseguimiento', $dataUpdate);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se actualizo la entidad matrizseguimiento.';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se actualizo correctamente';

                insertar_logMatrizSeguimiento([
                    'modulo' => (string)$modulo,
                    'mensaje' => (string)'Ha Actualizado el módulo '.$modulo,
                    'usuario_id' => $usuario_id,
                    'matrizSeguimiento_id' => $id,
                    'fecha_registro' => date('Y-m-d H:i:s'),
                    'data_new' => json_encode($dataUpdate),
                    'data_old' => json_encode($data_old),
                ]);

                $data['error'] = EXIT_SUCCESS;
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function actualizaMatrizSeguimientoPIN1($dataUpdate, $cable, $nodo)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->where('numHilosPuertoOLT', '0');
            $this->db->where('cable', $cable);
            $this->db->where('nodo', $nodo);
            $this->db->update('matrizseguimiento', $dataUpdate);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se actualizo la entidad matrizseguimiento.';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se actualizo correctamente';

                // insertar_logMatrizSeguimiento([
                //     'modulo' => (string)$modulo,
                //     'mensaje' => (string)'Ha Actualizado el módulo '.$modulo,
                //     'usuario_id' => $usuario_id,
                //     'matrizSeguimiento_id' => $id,
                //     'fecha_registro' => date('Y-m-d H:i:s'),
                //     'data_new' => json_encode($dataUpdate),
                //     'data_old' => json_encode($data_old),
                // ]);

                $data['error'] = EXIT_SUCCESS;
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data; 
    }

    function actualizaMatrizSeguimientoPIN1V2($dataUpdate, $obj)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->where('id', $obj['id']);
            $this->db->update('matrizseguimiento', $dataUpdate);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se actualizo la entidad matrizseguimiento.';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se actualizo correctamente';

                $data['error'] = EXIT_SUCCESS;
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data; 
    }

    function actualizaMatrizSeguimientoPIN2($dataUpdate, $id)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->where('id', $id);
            $this->db->update('matrizseguimiento', $dataUpdate);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se actualizo la entidad matrizseguimiento.';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se actualizo correctamente';
                $data['error'] = EXIT_SUCCESS;
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function actualizaMatrizSeguimientoMasiva($dataUpdate, $data_old, $usuario_id, $id, $modulo)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->where('itemplan', $dataUpdate['itemplan']);
            if (isset($dataUpdate['divicau']) && !empty($dataUpdate['divicau']))
            {
                $this->db->where('divicau', $dataUpdate['divicau']);
            }


            // pre($dataUpdate);
            $this->db->update('matrizseguimiento', $dataUpdate);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se actualizo la entidad matrizseguimiento.';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se actualizo correctamente';

                insertar_logMatrizSeguimiento([
                    'modulo' => (string)$modulo,
                    'mensaje' => (string)'Ha Actualizado el módulo '.$modulo,
                    'usuario_id' => $usuario_id,
                    'matrizSeguimiento_id' => $id,
                    'fecha_registro' => date('Y-m-d H:i:s'),
                    'data_new' => json_encode($dataUpdate),
                    'data_old' => json_encode($data_old),
                    'itemplan' => @$dataUpdate['itemplan']
                ]);

                $data['error'] = EXIT_SUCCESS;
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function insertTabla($arrayTabla)
    {
        // pre($arrayTabla);
        $this->db->insert('matrizseguimiento', $arrayTabla);
    }
}
?>