<?php

class M_solicitud_oc_item_madre extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    }

    function getSolicitudOc($itemplan, $codigoSolicitud) {
        $sql = " SELECT so.codigo_solicitud,
                        so.plan,
                        so.cesta,
                        so.orden_compra,
                        so.tipo_solicitud,
                        so.estatus_solicitud,
                        so.codigoInversion,
                        so.fecha_valida,
                        so.codigo_certificacion,
                        so.fecha_cancelacion,
                        so.estado,
                        DATE_FORMAT(fecha_creacion, '%d-%m-%Y %h:%m:%s') fecha_creacion,
                        so.costo_sap,
                        (CASE WHEN tipo_solicitud = 1 THEN 'CREACION OC'
                            WHEN tipo_solicitud = 2 THEN 'EDICION OC'
                            WHEN tipo_solicitud = 3 THEN 'CERTIFICACION OC' 
                            WHEN tipo_solicitud = 4 THEN 'ANULACION POS. OC' END) as tipoSolicitud,
                        p.proyectoDesc, sp.subProyectoDesc, e.empresaColabDesc, 
                        soe.descripcion as estado_sol,
                        u.nombre_completo AS nombreCompleto,
                        FORMAT((SELECT SUM(ii.costo_unitario_mo)
                                    FROM itemplan_madre_x_solicitud_oc_madre ii
                                    WHERE ii.codigo_solicitud_oc = so.codigo_solicitud),2) AS costo_total,
                        (SELECT COUNT(1)
                            FROM itemplan_madre_x_solicitud_oc_madre ii
                            WHERE ii.codigo_solicitud_oc = so.codigo_solicitud) AS numItemplan,
                        CASE WHEN po.solicitud_oc_dev = so.codigo_solicitud THEN 1 
                             ELSE 0 END flg_pdf_edi,
						po.preciario AS P,
			            ROUND(((SELECT SUM(ii.costo_unitario_mo)
                                  FROM itemplan_madre_x_solicitud_oc_madre ii
                                 WHERE ii.codigo_solicitud_oc = so.codigo_solicitud) / po.preciario),2) AS Q,
                        po.itemplan_m
                   FROM  (empresacolab e,
                          subproyecto sp, 
                          proyecto p, 
                          itemplan_madre_solicitud_orden_compra so,
                          estado_solicitud_orden_compra soe,
                          itemplan_madre_x_solicitud_oc_madre i,
                          itemplan_madre po) 
                LEFT JOIN usuario u on so.usuario_valida = u.id_usuario
                    WHERE so.idEmpresaColab     = e.idEmpresaColab
                      AND so.idSubProyecto      = sp.idSubProyecto
                      AND so.estado             = soe.id
                      AND sp.idProyecto         = p.idProyecto
                      AND i.codigo_solicitud_oc = so.codigo_solicitud 
                      AND i.itemplan_m            = po.itemplan_m
                      AND i.itemplan_m            = COALESCE(?, i.itemplan_m)
                      AND so.codigo_solicitud   = COALESCE(?, so.codigo_solicitud)";
        $result = $this->db->query($sql, array($itemplan, $codigoSolicitud));
        return $result->result_array();
    }

    function getDetalleSolicitudOc($codigoSolicitud) {
        $sql = " SELECT po.itemplan_m, 
                        soc.codigo_solicitud, 
                        po.nombrePlan, 
                        sp.subProyectoDesc, 
                        FORMAT(i.costo_unitario_mo,2) as limite_costo_mo,
                        FORMAT(i.costo_unitario_mo,2) as costo_mo_ix, 
                        i.posicion as posicion_ix, 
                        soc.orden_compra as oc_sol, 
                        soc.cesta as cesta_sol,
                        soc.estado
                   FROM itemplan_madre po, 
                        subproyecto sp, 
                        itemplan_madre_x_solicitud_oc_madre i, 
                        itemplan_madre_solicitud_orden_compra soc
                  WHERE po.idSubProyecto = sp.idSubProyecto
                    AND i.itemplan_m = po.itemplan_m
                    AND i.codigo_solicitud_oc = soc.codigo_solicitud
                    AND i.codigo_solicitud_oc = ?";
        $result = $this->db->query($sql, array($codigoSolicitud));
        return $result->result_array();
    }

    function atencionSolicitudOcCrea($arrayUpdateSolicitud, $arrayUpdateSolicitudxItem, $arrayUpdatePlanObra) {
        $this->db->update_batch('itemplan_madre_solicitud_orden_compra', $arrayUpdateSolicitud, 'codigo_solicitud');
        if($this->db->trans_status() === FALSE) {
            $data['msj'] = 'error interno solicitud oc';
            $data['error'] = EXIT_ERROR;
        } else {
            $this->db->update_batch('itemplan_madre_x_solicitud_oc_madre', $arrayUpdateSolicitudxItem, 'codigo_solicitud_oc');
           
            if($this->db->trans_status() === FALSE) {
                $data['msj'] = 'error interno ixoc';
                $data['error'] = EXIT_ERROR;
            } else {
                $this->db->update_batch('itemplan_madre', $arrayUpdatePlanObra, 'itemplan_m');
               // _log($this->db->last_query());
                if($this->db->trans_status() === FALSE) {
                    $data['msj'] = 'No se actualizo planobra';
                    $data['error'] = EXIT_ERROR;
                } else {
                    $data['msj'] = 'Se atendio correctamente';
                    $data['error'] = EXIT_SUCCESS;
                }
            }
        }
        return $data;
    }
   
}