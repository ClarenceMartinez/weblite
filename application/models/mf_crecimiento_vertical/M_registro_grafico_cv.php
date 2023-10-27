<?php

class M_registro_grafico_cv extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function getBandejaRegGraficoCV($itemplan)
    {
        $sql = "  SELECT tb1.*,
                         irg.id_usuario,
                         u.nombre_completo,
                         irg.fecha_registro,
                         irg.ruta_archivo
                    FROM (
                             SELECT po.itemplan,
                                    po.idEstadoPlan,
                                    esp.estadoPlanDesc,
                                    p.proyectoDesc,
                                    sp.subProyectoDesc, 
                                    ec.empresaColabDesc,
                                    po.has_sirope_fo,
                                    po.has_sirope_coax,
                                    d.idEstacion,
                                    es.estacionDesc,
                                    d.estado
                               FROM planobra po,
                                    diseno d, 
                                    empresacolab ec, 
                                    subproyecto sp, 
                                    proyecto p,
                                    estacion es,
                                    estadoplan esp
                              WHERE po.itemplan = d.itemplan
                                AND po.idEmpresaColab = ec.idEmpresaColab
                                AND po.idSubProyecto = sp.idSubProyecto
                                AND sp.idProyecto = p.idProyecto
                                AND d.idEstacion = es.idEstacion
                                AND po.idEstadoPlan = esp.idEstadoPlan
                                AND d.idEstacion IN (2,5)
                                AND d.estado = '3'
                                AND p.idProyecto = 21
                               #AND po.idEstadoPlan IN (3,9) 
                          ) tb1 
                LEFT JOIN itemplan_registro_grafico_cv irg ON tb1.itemplan = irg.itemplan AND tb1.idEstacion = irg.idEstacion
                LEFT JOIN usuario u ON irg.id_usuario = u.id_usuario 
                    WHERE tb1.itemplan = COALESCE(?,tb1.itemplan)";

        $result = $this->db->query($sql,array($itemplan));
        if ($result->num_rows() > 0) {
			return $result->result();
        } else {
            return null;
        }
    }

    public function getCountTerPotenciaByIP($itemplan)
    {
        $sql = " SELECT COUNT(*) cantidad 
		           FROM log_seguimiento_cv 
				  WHERE itemplan = ? 
				    AND id_motivo_seguimiento = 17 ";

        $result = $this->db->query($sql,array($itemplan));
        if ($result->num_rows() > 0) {
			return $result->row()->cantidad;
        } else {
            return null;
        }
    }

    public function insertItemplanxRegistroGraficoCV($arrayInsert)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->insert('itemplan_registro_grafico_cv', $arrayInsert);
            if ($this->db->affected_rows() <= 0) {
                throw new Exception('Error al insertar en la tabla itemplan_registro_grafico_cv');
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se insertó correctamente!!';
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

}
