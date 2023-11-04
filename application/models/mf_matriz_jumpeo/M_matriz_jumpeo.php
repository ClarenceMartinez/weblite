<?php

class M_matriz_jumpeo extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    }

    function lista($estado = null)
    {
        $and = "";
        if ($estado != null)
        {
            if ($estado != 'ALL')
            {
                $and = " WHERE m.estado = '{$estado}'";
            }
        }
        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2 
                FROM matrizjumpeo m
                INNER JOIN planobra po ON (po.itemplan = m.itemplan)
                INNER JOIN central c ON (c.idCentral  = po.idCentral)
                INNER JOIN zonal z ON (z.idZonal = po.idZonal)
                INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto)
                LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
                LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) $and";

        $result = $this->db->query($sql);
        return $result->result_array();
        
    }

    function listaByEmpresaColab($idEmpresaColab, $estado)
    {
        $and = "";
        if ($estado != null)
        {
            if ($estado != 'ALL')
            {
                $and = " AND m.estado = '{$estado}'";
            }
        }

        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2 
                FROM matrizjumpeo m
                INNER JOIN planobra po ON (po.itemplan = m.itemplan)
                INNER JOIN central c ON (c.idCentral  = po.idCentral)
                INNER JOIN zonal z ON (z.idZonal = po.idZonal)
                INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto)
                LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
                LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) WHERE m.idcontrataPext = ? $and";
        
        $result = $this->db->query($sql, array($idEmpresaColab));
        return $result->result_array();
        
    }

    function getMatrizProgramada()
    {
        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2
                FROM matrizjumpeo m
                INNER JOIN planobra po ON (po.itemplan = m.itemplan)
                INNER JOIN central c ON (c.idCentral  = po.idCentral)
                INNER JOIN zonal z ON (z.idZonal = po.idZonal)
                INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto) 
                LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
                LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) 
                WHERE m.estado IN ('PROGRAMADO', 'OBSERVADO')";
        
        $result = $this->db->query($sql);
        return $result->result_array();
        
    }

    function getMatrizProgramadaByEmpresaColab($empresaColaboradora, $estado)
    {
        
        $and = "";
        if ($estado != null)
        {
            if ($estado != 'ALL')
            {
                $and = " AND m.estado = '{$estado}'";
            }
        }

        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2
                FROM matrizjumpeo m
                INNER JOIN planobra po ON (po.itemplan = m.itemplan)
                INNER JOIN central c ON (c.idCentral  = po.idCentral)
                INNER JOIN zonal z ON (z.idZonal = po.idZonal)
                INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto) 
                LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
                LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) 
                WHERE m.idcontrataPint = ? $and";
        $result = $this->db->query($sql, array($empresaColaboradora));
        // pre($sql);
        // pre($empresaColaboradora);
        log_message('error',$this->db->last_query());
        return $result->result_array();
        
    }


    function obtenerCodNodo($itemplan)
    {
        $sql = "SELECT po.itemplan, sp.subProyectoDesc, z.zonalDesc, UPPER(d.distritoDesc) AS distrito, 
                po.nombrePlan, ec.empresaColabDesc, c.codNodo, ec.idEmpresaColab
                FROM planobra po, subproyecto sp, zonal z, central c, distrito d, empresacolab ec 
                WHERE po.idSubProyecto = sp.idSubProyecto
                AND po.idEmpresaColab = ec.idEmpresaColab
                AND po.idZonal = z.idZonal
                AND po.idCentral = c.idCentral
                AND c.idDistrito = d.idDistrito
                AND po.itemplan = ?";
        $result = $this->db->query($sql, array($itemplan));

        return $result->result_array();

    }



    function obtenerInfoNodo($itemplan, $codNodo)
    {
        $sql = "SELECT po.itemplan, sp.subProyectoDesc, z.zonalDesc, UPPER(d.distritoDesc) AS distrito, 
                po.nombrePlan, ec.empresaColabDesc, c.codNodo, ec.idEmpresaColab
                FROM planobra po, subproyecto sp, zonal z, central c, distrito d, empresacolab ec 
                WHERE po.idSubProyecto = sp.idSubProyecto
                AND po.idEmpresaColab = ec.idEmpresaColab
                AND po.idZonal = z.idZonal
                AND po.idCentral = c.idCentral
                AND c.idDistrito = d.idDistrito
                AND po.itemplan = ? AND c.codNodo = ?";
        $result = $this->db->query($sql, array($itemplan, $codNodo));

        return $result->result_array();

    }

    function verifyItemPanInPlanObra($itemplan) {

        $sql = "SELECT * FROM planobra WHERE itemplan = ?";
        
        $result = $this->db->query($sql, array($itemplan));

        return $result->result_array();
        
    }

    function obtenerMatrizPinPext($codNodo, $cuentaPares)
    {
        $sql = "SELECT * FROM matriz_pin_pex WHERE nodo = ? AND cuenta_pares = ?";
        $result = $this->db->query($sql, array($codNodo, $cuentaPares));
        log_message('error',$this->db->last_query());
        return $result->result_array();
    }

    function obtenerMatrizJumpeoLLave($nodo, $cable, $hilo)
    {
        $sql = "SELECT estado FROM matrizjumpeo WHERE nodo = ? AND cable = ? AND hilo = ? ";
        $result = $this->db->query($sql, array($nodo, $cable, $hilo));
        return $result->result_array();
    }


    function getMatrizJumpeada()
    {
        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2 
            FROM matrizjumpeo m
            INNER JOIN planobra po ON (po.itemplan = m.itemplan)
            INNER JOIN central c ON (c.idCentral  = po.idCentral)
            INNER JOIN zonal z ON (z.idZonal = po.idZonal)
            INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto)
            LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
            LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) WHERE m.estado = 'JUMPEADO'";
        
        $result = $this->db->query($sql);
        return $result->result_array();
        
    }

    function getMatrizJumpeoByEECCPext($idcontrataPext, $estado)
    {
        $and = "";
        if ($estado != null)
        {
            if ($estado != 'ALL')
            {
                $and = " AND m.estado = '{$estado}'";
            }
        }

        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2 
            FROM matrizjumpeo m
            INNER JOIN planobra po ON (po.itemplan = m.itemplan)
            INNER JOIN central c ON (c.idCentral  = po.idCentral)
            INNER JOIN zonal z ON (z.idZonal = po.idZonal)
            INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto)
            LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
            LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) WHERE m.idcontrataPext = ? $and";

        
        $result = $this->db->query($sql, array($idcontrataPext));
		log_message('error', $this->db->last_query());
        return $result->result_array();
        
    }


    function getMatrizByFilterEstado($estado, $idEmpresaColab, $empresaColab)
    {
        $and = '';

        if ($idEmpresaColab != 6)
        {
            $and .= " AND m.estado <> 'NUEVO'";
            $and .= " AND m.eeccpint = '{$empresaColab}' OR m.contrataPext= '{$empresaColab}'";
        }
        if ($estado == 'ALL')
        {
            $and .= '';
        } else
        {
            $and .= "  AND m.estado = '{$estado}'";
        }



        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2 
            FROM matrizjumpeo m
            INNER JOIN planobra po ON (po.itemplan = m.itemplan)
            INNER JOIN central c ON (c.idCentral  = po.idCentral)
            INNER JOIN zonal z ON (z.idZonal = po.idZonal)
            INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto)
            LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
            LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) WHERE m.id > 0 $and";

        $result = $this->db->query($sql);
        return $result->result_array();
        
    }

    function getMatrizByFilter($codigo, $estado)
    {
        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2 
            FROM matrizjumpeo m
            INNER JOIN planobra po ON (po.itemplan = m.itemplan)
            INNER JOIN central c ON (c.idCentral  = po.idCentral)
            INNER JOIN zonal z ON (z.idZonal = po.idZonal)
            INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto)
            LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
            LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) WHERE m.codigoSolicitud = ? AND m.estado = ?";
        
        $result = $this->db->query($sql, array($codigo, $estado));
        return $result->result_array();
        
    }


    function getMatrizPinPexBase()
    {
        $sql = "SELECT * FROM matriz_pin_pex_base WHERE procesado = 0";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getMatrizPinPexBaseInsert()
    {
        $sqlx = "TRUNCATE TABLE matriz_pin_pex";
        $this->db->query($sqlx);

        $sql = "INSERT INTO matriz_pin_pex (nodo,odf,  modulo, bandeja, ot_n1, cuenta_pares, divicau, olt, slot, puerto, puerto_olt)
                SELECT DISTINCT nodo, dgo AS odf,  modulo, bandeja, ot_n1, cuentaPares AS cuenta_pares, terminalN1 AS divicau, olt, olt_slot AS slot, puerto, puerto_olt 
                FROM matriz_pin_pex_base"; 

        $result = $this->db->query($sql);
    }

    function obtenerEECCByItemPlan($itemplan)
    {
        $sql = "SELECT empresaColabDesc, ult_codigo_sirope 
                FROM planobra po
                INNER JOIN empresacolab ec ON (po.idEmpresaColab = ec.idEmpresaColab)
                WHERE po.itemplan = ?";
        
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
        
    }

    function obtenercmsEECCByItemPlan($itemplan)
    {
        $sql = "SELECT cms 
                FROM cms_base_olt LIMIT 1";
        
        $result = $this->db->query($sql);
        return $result->result_array();
        
    }

    function obtenerLogMatrizJumpeoById($id)
    {
        $sql = "SELECT m.*, u.nombre_completo FROM matrizjumpeo_history m 
                INNER JOIN usuario u ON(u.id_usuario = m.usuario_id)
                WHERE m.matrizjumpeo_id = ?";
        $result = $this->db->query($sql, array($id));
        return $result->result_array();
    }

    function getInfoMatrizJumpeoById($id)
    {
        $sql = "SELECT * FROM matrizjumpeo m
                WHERE m.id = ?";
        $result = $this->db->query($sql, array($id));
        return $result->result_array();
    }

    function actualizaMatriz($dataUpdate)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->where('id', $dataUpdate['id']);
            $this->db->update('matrizjumpeo', $dataUpdate);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se actualizo la entidad matrizjumpeo.';
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

    function verificarItemPlan($itemplan)
    {
        $sql = "SELECT * FROM matrizjumpeo WHERE itemplanPinOt = ?";
        
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function verificarItemPlanObra($itemplan)
    {
        $sql = "SELECT * FROM planobra WHERE itemplan = ?";
        
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function insertTabla($arrayTabla)
    {
        $this->db->insert('matrizjumpeo', $arrayTabla);
        // return  $this->db->insert_id();
        $data['insert_id'] = $this->db->insert_id();
        $data['error'] = EXIT_SUCCESS;
        return $data;
    }

    function insertMatrizPINPEX($arrayTabla)
    {
        $this->db->insert('matriz_pin_pex_base', $arrayTabla);
        // return  $this->db->insert_id();
        $data['insert_id'] = $this->db->insert_id();
        $data['error'] = EXIT_SUCCESS;
        return $data;
    }

    
}
?>