<?php

class M_bandeja_solicitud_oc_diseno extends CI_Model {

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
                        po.itemplan,
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
                                    FROM itemplan_x_solicitud_oc_diseno ii
                                    WHERE ii.codigo_solicitud_oc = so.codigo_solicitud),2) AS costo_total,
                        (SELECT COUNT(1)
                            FROM itemplan_x_solicitud_oc_diseno ii
                            WHERE ii.codigo_solicitud_oc = so.codigo_solicitud) AS numItemplan,
                        CASE WHEN po.solicitud_oc_dev = so.codigo_solicitud THEN 1 
                             ELSE 0 END flg_pdf_edi,
						po.precio_obra AS P,
			            ROUND(((SELECT SUM(ii.costo_unitario_mo)
                                  FROM itemplan_x_solicitud_oc_diseno ii
                                 WHERE ii.codigo_solicitud_oc = so.codigo_solicitud) / po.precio_obra),2) AS Q
                   FROM  (empresacolab e,
                          subproyecto sp, 
                          proyecto p, 
                          solicitud_orden_compra_diseno so,
                          estado_solicitud_orden_compra soe,
                          itemplan_x_solicitud_oc_diseno i,
                          planobra po) 
                LEFT JOIN usuario u on so.usuario_valida = u.id_usuario
                    WHERE so.idEmpresaColab     = e.idEmpresaColab
                      AND so.idSubProyecto      = sp.idSubProyecto
                      AND so.estado             = soe.id
                      AND sp.idProyecto         = p.idProyecto
                      AND i.codigo_solicitud_oc = so.codigo_solicitud 
                      AND i.itemplan            = po.itemplan                    
                      AND i.itemplan            = COALESCE(?, i.itemplan)
                      AND so.codigo_solicitud   = COALESCE(?, so.codigo_solicitud)";
        $result = $this->db->query($sql, array($itemplan, $codigoSolicitud));
        //log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    function getSolicitudOcNew($estado_so, $itemplan, $codigoSolicitud) {
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
                        po.itemplan,
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
                                    FROM itemplan_x_solicitud_oc_diseno ii
                                    WHERE ii.codigo_solicitud_oc = so.codigo_solicitud),2) AS costo_total,
                        (SELECT COUNT(1)
                            FROM itemplan_x_solicitud_oc ii
                            WHERE ii.codigo_solicitud_oc = so.codigo_solicitud) AS numItemplan,
                        CASE WHEN po.solicitud_oc_dev = so.codigo_solicitud THEN 1 
                             ELSE 0 END flg_pdf_edi,
                        po.precio_obra AS P,
                        ROUND(((SELECT SUM(ii.costo_unitario_mo)
                                  FROM itemplan_x_solicitud_oc ii
                                 WHERE ii.codigo_solicitud_oc = so.codigo_solicitud) / po.precio_obra),2) AS Q
                   FROM  (empresacolab e,
                          subproyecto sp, 
                          proyecto p, 
                          solicitud_orden_compra_diseno so,
                          estado_solicitud_orden_compra soe,
                          itemplan_x_solicitud_oc_diseno i,
                          planobra po) 
                LEFT JOIN usuario u on so.usuario_valida = u.id_usuario
                    WHERE so.idEmpresaColab     = e.idEmpresaColab
                      AND so.idSubProyecto      = sp.idSubProyecto
                      AND so.estado             = soe.id
                      AND sp.idProyecto         = p.idProyecto
                      AND i.codigo_solicitud_oc = so.codigo_solicitud 
                      AND i.itemplan            = po.itemplan
                      AND so.estado             = COALESCE(?, so.estado)
                      AND i.itemplan            = COALESCE(?, i.itemplan)
                      AND so.codigo_solicitud   = COALESCE(?, so.codigo_solicitud)";
        $result = $this->db->query($sql, array($estado_so, $itemplan, $codigoSolicitud));
        //log_message('error', $this->db->last_query());
        return $result->result_array();
    }


    function getDetalleSolicitudOc($codigoSolicitud) {
        $sql = " SELECT po.itemplan, 
                        soc.codigo_solicitud, 
                        po.nombrePlan, 
                        sp.subProyectoDesc, 
                        FORMAT(i.costo_unitario_mo,2) as limite_costo_mo, 
                        FORMAT(po.costo_unitario_mat,2) as limite_costo_mat, 
                        FORMAT(i.costo_unitario_mo,2) as costo_mo_ix, 
                        i.posicion as posicion_ix, 
                        soc.orden_compra as oc_sol, 
                        soc.cesta as cesta_sol,
                        soc.estado
                   FROM planobra po, 
                        subproyecto sp, 
                        itemplan_x_solicitud_oc_diseno i, 
                        solicitud_orden_compra_diseno soc
                  WHERE po.idSubProyecto = sp.idSubProyecto
                    AND i.itemplan = po.itemplan
                    AND i.codigo_solicitud_oc = soc.codigo_solicitud
                    AND i.codigo_solicitud_oc = ?";
        $result = $this->db->query($sql, array($codigoSolicitud));
        return $result->result_array();
    }

    function atencionSolicitudOcCrea($arrayUpdateSolicitud, $arrayUpdateSolicitudxItem, $arrayUpdatePlanObra) {
	//	log_message('error', print_r($arrayUpdatePlanObra,true));
        $this->db->update_batch('solicitud_orden_compra_diseno', $arrayUpdateSolicitud, 'codigo_solicitud');
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'error interno solicitud oc';
            $data['error'] = EXIT_ERROR;
        } else {
            $this->db->update_batch('itemplan_x_solicitud_oc_diseno', $arrayUpdateSolicitudxItem, 'codigo_solicitud_oc');
           

           log_message('error', $this->db->last_query());
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'error interno ixoc';
                $data['error'] = EXIT_ERROR;
            } else {
                $this->db->update_batch('planobra', $arrayUpdatePlanObra, 'itemplan');
                if($this->db->affected_rows() <= 0) {
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

    function updatePlanObraPo($arrayUpdatePlanObraPO)
    {
        // $this->db->update_batch('planobra_po', $arrayUpdatePlanObraPO, 'itemplan');


        // $this->db->where('itemplan', $arrayUpdatePlanObraPO['itemplan']);
        // $this->db->where('codigo_po', $arrayUpdatePlanObraPO['itemplan']);
        // $this->db->where('itemplan', $arrayUpdatePlanObraPO['itemplan']);
        //     $this->db->update('planobra_po', $arrayUpdatePlanObraPO);

        // if($this->db->affected_rows() <= 0)
        // {
        //     $data['msj'] = 'error interno ixoc';
        //     $data['error'] = EXIT_ERROR;
        // } else
        // {
        //     $data['msj'] = 'Se atendio correctamente';
        //     $data['error'] = EXIT_SUCCESS;
        // }
    }

	function hasEstacionesAnclasByItemplan($itemplan){
	    $sql = " SELECT DISTINCT
                        SUM(DISTINCT CASE WHEN idEstacion = 2 THEN 1 ELSE 0 END) AS coaxial,
                        SUM(DISTINCT CASE WHEN idEstacion = 5 THEN 1 ELSE 0 END) AS fo
                   FROM planobra po, 
                        subproyecto sp, 
                        subproyecto_area_estacion se
                  WHERE po.idSubProyecto = sp.idSubProyecto
                    AND sp.idSubProyecto = se.idSubProyecto
                    AND se.idEstacion IN (5,2)
                    AND po.itemplan = ? ";
	    $result = $this->db->query($sql,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function registroMasivoIncidenciaCV($arrayInsert) {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->insert_batch('log_seguimiento_cv', $arrayInsert);
            if($this->db->affected_rows() <= 0) {
                throw new Exception('Error al insertar en log_seguimiento_cv');
            }else{  
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se insertó correctamente!';     
            }
        }catch(Exception $e){
            $data['msj']  = $e->getMessage();
        }
        return $data;
    }

    function update_solicitud_oc_certi($codigo_solicitud, $arrayData, $dataPo, $idUsuario, $countPoValidadas){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->where('codigo_solicitud', $codigo_solicitud);
	        $this->db->update('solicitud_orden_compra_diseno', $arrayData);
	        if($this->db->affected_rows() != 1) {
	            throw new Exception('Error al actualizar la informacion 2.');
	        }else{
	            $this->db->where('solicitud_oc_certi_diseno', $dataPo['solicitud_oc_certi']);
	            $this->db->update('planobra', $dataPo);
                // log_message('error', $this->db->last_query());

	            if($this->db->affected_rows() != 1) {
	                throw new Exception('Error al actualizar la informacion 1.');
	            }else{
					if($countPoValidadas > 0) {
						$data = $this->insertLogPO($codigo_solicitud, ID_ESTADO_PO_VALIDADO, 'CERTIFICADO OC', $idUsuario, ID_ESTADO_PO_CERTIFICADO);

						if($data['error'] == EXIT_ERROR) {
							throw new Exception($data['msj']);
						} else {
							$data = $this->updatePOByItemplan($codigo_solicitud, ID_ESTADO_PO_VALIDADO, ID_ESTADO_PO_CERTIFICADO);
	
							if($data['error'] == EXIT_ERROR) {
								throw new Exception($data['msj']);
							} else {
								$data['error'] = EXIT_SUCCESS;
								$data['msj'] = 'Se actualizo correctamente!';
							}
						}
					} else {
						$data['msj'] = 'Se actualizo correctamente, ojo que no se encontro POs validadas para pasar a certificado.';
						$data['error'] = EXIT_SUCCESS;
					}
	            }
	        }
	    }catch(Exception $e){
	        $data['msj']   = $e->getMessage();
	    }
	    return $data;
	}

	function updatePOByItemplan($codigo_solicitud, $estado_actual, $estado_final) {
		$sql = "UPDATE planobra_po ppo,
				       planobra po
				   SET ppo.estado_po = ?
				 WHERE ppo.estado_po = ?
				   AND ppo.itemplan = po.itemplan
                   AND ppo.idEstacion = 1
			  	   AND po.solicitud_oc_certi_diseno  = ?";
		$result = $this->db->query($sql,array($estado_final, $estado_actual, $codigo_solicitud));
		if($this->db->affected_rows() < 1) {
			$data['error'] = EXIT_ERROR;
			$data['msj'] = 'No hay POs validadas, las POs deben estar validadas para ser certificadas.';
		}else{              
			$data['error'] = EXIT_SUCCESS;
			$data['msj'] = 'Se actualizo correctamente!';
		}
		return $data;
	}

    function insertLogPO($codigo_solicitud, $po_estado_actual, $descripcion, $idUsuario, $po_estado_final) {
		$sql = "INSERT INTO log_planobra_po (codigo_po, itemplan, 
											 idUsuario, fecha_registro, 
											 idPoestado, controlador)
				SELECT ppo.codigo_po, po.itemplan,?, NOW(), ?, ? 
				  FROM planobra_po ppo,
				       planobra po
				 WHERE ppo.estado_po = ?
				   AND po.itemplan = ppo.itemplan
                   AND ppo.idEstacion = 1
			  	   AND po.solicitud_oc_certi_diseno  = ?";
		$result = $this->db->query($sql,array($idUsuario, $po_estado_final, $descripcion, $po_estado_actual, $codigo_solicitud));
		if($this->db->affected_rows() == 0) {
			$data['error'] = EXIT_ERROR;
			$data['msj']   = 'error al registrar el log de po';
		}else{
			$data['error'] = EXIT_SUCCESS;
			$data['msj']   = 'Se registro correctamente!';
		}
		return $data;
	}

	function actualizarSolicitud($codigo_solicitud, $arrayData){
	    $data['error'] = EXIT_ERROR;
	    $data['msj'] = null;
	    try{
	        $this->db->where('codigo_solicitud', $codigo_solicitud);
	        $this->db->update('solicitud_orden_compra_diseno', $arrayData);

            //log_message('error', $this->db->last_query());
	        if($this->db->affected_rows() <= 0) {
	            throw new Exception('Error al actualizar en la tabla solicitud_orden_compra_diseno!!');
	        }else{
	            $data['msj'] = 'Se actualizó correctamente la solicitud!!';
				$data['error'] = EXIT_SUCCESS;
	        }
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}
	
	function insertMasiveDiseno($arrayInsert) {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->insert_batch('diseno', $arrayInsert);
            if($this->db->affected_rows() <= 0) {
                throw new Exception('Error al insertar en la tabla diseno');
            }else{  
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se insertó correctamente!';     
            }
        }catch(Exception $e){
            $data['msj']  = $e->getMessage();
        }
        return $data;
    }

    function getSolCertPndEdicion($cod_obra) {
	    $Query = "SELECT 
                        soc.codigo_solicitud
                    FROM
                        solicitud_orden_compra_diseno soc,
                        itemplan_x_solicitud_oc_diseno ixs,
                        planobra po
                    WHERE
                        po.itemplan = ixs.itemplan
                    AND soc.codigo_solicitud = ixs.codigo_solicitud_oc
                    AND po.itemplan = ?
                    AND po.orden_compra_diseno = soc.orden_compra
                    AND soc.tipo_solicitud = 3
                    and soc.estado = 4 LIMIT 1";
	    $result = $this->db->query($Query, array($cod_obra));
        // log_message('error', $this->db->last_query());
	    if ($result->row() != null) {
	        return $result->row_array()['codigo_solicitud'];
	    } else {
	        return null;
	    }
	}

    function getDisenoInfoLiquiByItemEstacion($itemplan, $idEstacion) {
	    $Query = "SELECT * FROM diseno  where itemplan = ? and idEstacion = ? LIMIT 1";
	    $result = $this->db->query($Query, array($itemplan, $idEstacion));
	    if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
 
}
