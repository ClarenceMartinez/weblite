<?php

class M_bandeja_pre_aprobacion_po_mat extends CI_Model {

    function __construct() {
        parent::__construct();
    } 

    function getBandejaAprobPo($itemplan, $codigoPO) {
        $sql = " SELECT po.itemplan,
                        po.nombrePlan,
                        po.fechaInicio,
                        po.fechaPrevEjecucion,
                        f.faseDesc,
                        po.codigoInversion,
                        e.empresaColabDesc,
                        z.zonalDesc,
                        ep.estadoPlanDesc,
                        s.subproyectoDesc,
                        e.idEmpresaColab,
                        po.fechaRegistro,
                        ce.codigo,
                        s.idProyecto,
                        pro.idTipoPlanta,
                        po.idEstadoPlan,
                        po.paquetizado_fg,
                        (CASE WHEN ce.idJefatura = 13 THEN 1 ELSE 2 END) AS tipoJefatura,
                        s.idTipoSubProyecto,
                        s.idSubProyecto,
                        ppo.codigo_po,
                        ppo.estado_po,
                        UPPER(poe.estado) AS desc_estado_po,
                        ppo.idEstacion,
                        es.estacionDesc,
                        ppo.costo_total,
                        ppo.idArea,
                        a.areaDesc
                   FROM planobra po,
                        empresacolab e,
                        zonal z,
                        fase f,
                        estadoplan ep,
                        subproyecto s,
                        central ce,
                        proyecto pro,
                        jefatura j,
                        planobra_po ppo,
                        po_estado poe,
                        estacion es,
                        area a
                  WHERE pro.idProyecto = s.idProyecto
                    AND s.idSubProyecto  = po.idSubProyecto
                    AND e.idEmpresaColab = po.idEmpresaColab
                    AND z.idZonal = po.idZonal
                    AND f.idFase = po.idFase
                    AND po.idEstadoPlan = ep.idEstadoPlan
                    AND po.idCentral = ce.idCentral
                    AND ce.idJefatura = j.idJefatura
                    AND po.itemplan = ppo.itemplan
                    AND ppo.estado_po = poe.idEstadoPo
                    AND ppo.idEstacion = es.idEstacion
                    AND ppo.idArea = a.idArea
                    AND ppo.flg_tipo_area = 1
                    AND ppo.estado_po = 1
                    AND po.itemplan = COALESCE(?,po.itemplan)
                    AND ppo.codigo_po = COALESCE(?,ppo.codigo_po) ";
        $result = $this->db->query($sql, array($itemplan, $codigoPO));
        return $result->result_array();
    }

    function getBandejaAprobPoJson($itemplan, $codigoPO) {
        $sql = "SELECT po.itemplan,                        
                        f.faseDesc,
                        po.codigoInversion,
                        e.empresaColabDesc,
                        z.zonalDesc,
                        ep.estadoPlanDesc,
                        s.subproyectoDesc,
                        e.idEmpresaColab, 
                        ppo.codigo_po,                     
                        UPPER(poe.estado) AS desc_estado_po,
                        ppo.costo_total,                      
                        a.areaDesc
                FROM planobra po,
                        empresacolab e,
                        zonal z,
                        fase f,
                        estadoplan ep,
                        subproyecto s,
                        central ce,
                        proyecto pro,
                        jefatura j,
                        planobra_po ppo,
                        po_estado poe,
                        estacion es,
                        area a
                WHERE pro.idProyecto = s.idProyecto
                    AND s.idSubProyecto  = po.idSubProyecto
                    AND e.idEmpresaColab = po.idEmpresaColab
                    AND z.idZonal = po.idZonal
                    AND f.idFase = po.idFase
                    AND po.idEstadoPlan = ep.idEstadoPlan
                    AND po.idCentral = ce.idCentral
                    AND ce.idJefatura = j.idJefatura
                    AND po.itemplan = ppo.itemplan
                    AND ppo.estado_po = poe.idEstadoPo
                    AND ppo.idEstacion = es.idEstacion
                    AND ppo.idArea = a.idArea
                    AND ppo.flg_tipo_area = 1
                    AND ppo.estado_po = 1
					AND (CASE WHEN s.idProyecto = 21 THEN TRUE ELSE po.idEstadoPlan IN (20,3) END)
                    AND po.itemplan = COALESCE(?,po.itemplan)
                    AND ppo.codigo_po = COALESCE(?,ppo.codigo_po) ";
        $result = $this->db->query($sql, array($itemplan, $codigoPO));
        return $result->result_array();
    }    
	
	function getDetalleMateriales($codigoPO) {
        $sql = " SELECT po.itemplan,
                        po.codigoInversion,
                        ppo.codigo_po,
                        UPPER(poe.estado) AS desc_estado_po,
                        ppo.idEstacion,
                        es.estacionDesc,
                        ppo.costo_total,
                        ppo.idArea,
                        a.areaDesc,
                        ( SELECT GROUP_CONCAT(je.codAlmacen,'|',je.codCentro,'|', je.idJefatura, '|', je.idEmpresaColab) 
                            FROM jefatura_sap js, 
                                 jefatura_sap_x_empresacolab je 
                           WHERE js.idJefatura = je.idJefatura
                             AND je.idEmpresacolab = e.idEmpresacolab
                             AND CASE WHEN js.idZonal IS NULL THEN js.descripcion = j.jefaturaDesc
                                 ELSE js.idZonal = po.idZonal END ) dataJefaturaEmp,
                        pd.codigo_material,
                        pd.cantidadFinal,
                        REPLACE(ppo.codigo_po, '-21', 'E') AS codigo_po_recortado
                        FROM planobra po,
                        empresacolab e,
                        zonal z,
                        fase f,
                        estadoplan ep,
                        subproyecto s,
                        central ce,
                        proyecto pro,
                        jefatura j,
                        planobra_po ppo,
                        po_estado poe,
                        estacion es,
                        area a,
                        planobra_po_detalle_mat pd
                        WHERE pro.idProyecto = s.idProyecto
                        AND s.idSubProyecto  = po.idSubProyecto
                        AND e.idEmpresaColab = po.idEmpresaColab
                        AND z.idZonal = po.idZonal
                        AND f.idFase = po.idFase
                        AND po.idEstadoPlan = ep.idEstadoPlan
                        AND po.idCentral = ce.idCentral
                        AND ce.idJefatura = j.idJefatura
                        AND po.itemplan = ppo.itemplan
                        AND ppo.estado_po = poe.idEstadoPo
                        AND ppo.idEstacion = es.idEstacion
                        AND ppo.codigo_po = pd.codigo_po
                        AND ppo.idArea = a.idArea
                        AND ppo.flg_tipo_area = 1
                        AND ppo.estado_po = 2
                        AND ppo.codigo_po = ? ";
        $result = $this->db->query($sql, array($codigoPO));
        return $result->result_array();
    }
}
