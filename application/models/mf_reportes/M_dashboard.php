<?php

class M_dashboard extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getDataToReportColumEstadoPlan($idProyecto) {
        $sql = "SELECT ep.idEstadoPlan, ep.estadoPlanDesc, CASE WHEN tb.total IS NULL THEN 0 ELSE tb.total END AS total 
        FROM estadoplan ep LEFT JOIN (SELECT po.idEstadoPlan, count(1) as total from planobra po, subproyecto sp
        where po.idSubProyecto = sp.idSubProyecto
        AND sp.idProyecto = ?
        GROUP BY po.idEstadoPlan) as tb ON ep.idEstadoPlan = tb.idEstadoPlan
        WHERE ep.idEstadoPlan NOT IN (5,6,18,21,24,10)        
        ORDER BY ep.secuencia";
        $result = $this->db->query($sql, array($idProyecto));
        return $result->result_array();
    }

    function getDataToReportColumEECC($idProyecto) {
        $sql = "SELECT po.idEstadoPlan, ec.empresaColabDesc, count(1) as total from planobra po, subproyecto sp, empresacolab ec
        where po.idSubProyecto = sp.idSubProyecto
        and po.idEmpresaColab = ec.idEmpresaColab
        and po.idEstadoPlan NOT IN (5,6,18,21,24,10)
        AND sp.idProyecto = ?
        GROUP BY po.idEstadoPlan, po.idEmpresaColab;";
        $result = $this->db->query($sql, array($idProyecto));
        return $result->result_array();
    }

    function getDataToReportColumEstadoPlanUIP($idProyecto) {
        $sql = "SELECT ep.idEstadoPlan, ep.estadoPlanDesc, CASE WHEN tb.total IS NULL THEN 0 ELSE tb.total END AS total 
        FROM estadoplan ep LEFT JOIN (SELECT po.idEstadoPlan, SUM(po.cantFactorPlanificado)  as total from planobra po, subproyecto sp
        where po.idSubProyecto = sp.idSubProyecto
        AND sp.idProyecto = ?
        GROUP BY po.idEstadoPlan) as tb ON ep.idEstadoPlan = tb.idEstadoPlan
        WHERE ep.idEstadoPlan NOT IN (5,6,18,21,24,10)        
        ORDER BY ep.secuencia";
        $result = $this->db->query($sql, array($idProyecto));
        return $result->result_array();
    }

    function getDataToReportColumEECCUIP($idProyecto) {
        $sql = "SELECT po.idEstadoPlan, ec.empresaColabDesc, SUM(po.cantFactorPlanificado)  as total from planobra po, subproyecto sp, empresacolab ec
        where po.idSubProyecto = sp.idSubProyecto
        and po.idEmpresaColab = ec.idEmpresaColab
        and po.idEstadoPlan NOT IN (5,6,18,21,24,10)
        AND sp.idProyecto = ?
        GROUP BY po.idEstadoPlan, po.idEmpresaColab;";
        $result = $this->db->query($sql, array($idProyecto));
        return $result->result_array();
    }
}
