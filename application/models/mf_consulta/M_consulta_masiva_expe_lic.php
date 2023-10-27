<?php

class M_consulta_masiva_expe_lic extends CI_Model {

    function __construct() {
        parent::__construct();
    } 

    function getEvidenciasLicenciaByItemLite($itemplanList){
        $sql = "SELECT itemplan,ubicacion_evidencia, SUBSTRING_INDEX(ubicacion_evidencia, concat('uploads/evidencia_licencia/',itemplan,'/'), -1) AS file_name  FROM entidad_itemplan_estacion where ubicacion_evidencia is not null and itemplan in ?";
        $result = $this->db->query($sql,array($itemplanList));
        return $result->result();        
    }

    function getEvidenciasLicenciaByItemLiteToTable($itemplanList){
        $sql = "SELECT po.itemplan, p.proyectoDesc, sp.subProyectoDesc, ec.empresaColabDesc, e.entidadDesc, eie.ubicacion_evidencia, case when eie.ubicacion_evidencia is not null then 'SI' ELSE 'NO' END has_expe_lic, SUBSTRING_INDEX(eie.ubicacion_evidencia, concat('uploads/evidencia_licencia/',eie.itemplan,'/'), -1) AS file_name 
        FROM proyecto p, subproyecto sp, empresacolab ec, planobra po left join entidad_itemplan_estacion eie ON po.itemplan = eie.itemplan left join entidad e ON eie.idEntidad = e.idEntidad
        where po.idSubProyecto = sp.idSubProyecto
        and sp.idProyecto = p.idProyecto
        and po.idEmpresaColab = ec.idEmpresaColab
        and po.itemplan IN ?";
        $result = $this->db->query($sql,array($itemplanList));
        return $result->result_array();   
    }
}
