<?php

class M_bandeja_cancelacion_po_mat extends CI_Model {

    function __construct() {
        parent::__construct();
    } 

    function getBandejaAprobPoJson($itemplan, $codigoPO) {
        $sql = "SELECT ppo.idEmpresaColab, ppo.vale_reserva, ppo.codigo_po, ppo.itemplan, m.motivoDesc, poc.observacion, concat(u.nombre_completo, ' ', u.ape_paterno) as usua_reg, poc.fecha_registro, sp.subProyectoDesc, ec.empresaColabDesc, a.areaDesc, poe.estado, ppo.costo_total
                    FROM planobra_po ppo,
                        planobra po,
                        subproyecto sp,                            
                        estacion e,
                        area a,
                        central c,
                        zonal z,
                        empresacolab ec,
                        fase f,
                        po_estado poe,
                        po_cancelar poc,
                        motivo m,
                        usuario u
                WHERE po.idSubProyecto = sp.idSubproyecto
                    AND ppo.itemplan = po.itemplan
                    AND ppo.idArea = a.idArea 
                    AND	ppo.idSubProyecto = po.idSubproyecto
                    AND ppo.idEstacion = e.idEstacion
                    AND ppo.estado_po = poe.idEstadoPo   
                    AND ppo.idEmpresaColab = po.idEmpresaColab
                    AND ppo.idEmpresaColab = ec.idEmpresaColab
                    AND po.idCentral = c.idCentral
                    AND c.idZonal = z.idZonal           
                    AND po.idFase = f.idFase                                     
                    AND ppo.itemplan = poc.itemplan 
                    AND ppo.codigo_po = poc.codigo_po
                    AND poc.idMotivo = m.idMotivo
                    AND poc.id_usuario = u.id_usuario
                    AND ppo.estado_po = 7
                    AND poc.idPoestado = 7
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
                        #AND ppo.estado_po = 2
                        AND ppo.codigo_po = ? ";
        $result = $this->db->query($sql, array($codigoPO));
        log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    function cancelarPoMat($poUpdate, $arrayInsertLog) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();
            $this->db->where('codigo_po',   $poUpdate['codigo_po']);
            $this->db->update('planobra_po', $poUpdate);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al actualizar planobra_po.');
            }else{
                $this->db->insert('log_planobra_po', $arrayInsertLog);
                if($this->db->affected_rows() <= 0) {
                    throw new Exception('Error al insertar en log_planobra_po');
                }else{                     
                    $data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se actualizó correctamente!';
                    $this->db->trans_commit();
                }
            } 
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}
}
