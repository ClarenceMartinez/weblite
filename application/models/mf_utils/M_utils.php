<?php

class M_utils extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
		
		
    }
	
	function getUsuario() {
		$db_2 = $this->load->database('db_planobra', TRUE);
		$sql = "SELECT * FROM usuario";
		$result = $db_2->query($sql);
		return $result->result_array();
	}
     
    function getFaseAll() {
        $sql = "SELECT idFase,
                       faseDesc
                  FROM fase f
                 WHERE DATE(fecha_inicio) <= CURDATE()
                   AND DATE(fecha_fin) >= CURDATE()
                   AND estado = ".ESTADO_CONFIG;
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getProyectoAll($idProyecto, $idTipoPlanta) {
        $sql = " SELECT idProyecto,
                        proyectoDesc,
                        estado
                   FROM proyecto
                  WHERE idTipoPlanta = COALESCE(?, idTipoPlanta)
                    AND idProyecto = COALESCE(?, idProyecto)
                    AND estado = ".ESTADO_CONFIG."
                    ORDER BY proyectoDesc";
        $result = $this->db->query($sql, array($idTipoPlanta, $idProyecto));
        return $result->result_array();
    }

    function getProyectoAllCreateIP($idProyecto, $idTipoPlanta) {
        $sql = " SELECT idProyecto,
                        proyectoDesc,
                        estado
                   FROM proyecto
                  WHERE idTipoPlanta = COALESCE(?, idTipoPlanta)
                    AND idProyecto = COALESCE(?, idProyecto)
                    AND estado = ".ESTADO_CONFIG."
					AND idProyecto NOT IN (3,21)
                    ORDER BY proyectoDesc";
        $result = $this->db->query($sql, array($idTipoPlanta, $idProyecto));
        return $result->result_array();
    }

    function getSubProyectoAll($idProyecto, $idTipoPlanta) {
        $query = "   SELECT idSubProyecto,
                            subProyectoDesc,
                            idProyecto,
                            idTipoPlanta
                       FROM subproyecto
                      WHERE idTipoPlanta = COALESCE(?, idTipoPlanta)
                        AND idProyecto   = COALESCE(?, idProyecto)
                        AND estado       = 1
                    ORDER BY subProyectoDesc";
        $result = $this->db->query($query, array($idTipoPlanta, $idProyecto));
        return $result->result_array();
    }

    function getCodigoInversionByEmpresaColab($idEmpresaColab) {
		$sql = "SELECT exi.idEmpresaColab,
                       exi.codigoInversion,
                       i.orden_inversion
		          FROM empresacolab_x_inversion exi,
                       inversion i
			 	 WHERE idEmpresaColab = ?
                   AND exi.codigoInversion= i.codigo
                   AND i.estado = 1";
		$result = $this->db->query($sql, array($idEmpresaColab));
        return $result->result_array();
	}

    function getMdfCercanoByCoord($long, $lat) {
        $sql = "     SELECT ROUND((atan2(sqrt(a), b))*6371000,0) as distancia, codigo, latitud, longitud,
                            centralDesc,
                            idZonal,
                            idEmpresaColab,
                            region,
                            idCentral,
                            empresaColabDesc,
                            zonalDesc,
                            UPPER(distritoDesc) as distrito,
                            UPPER(departamentoDesc) as departamento,
                            UPPER(provinciaDesc) as provincia
                    FROM (
                            SELECT pow(cos(latTo) * sin(lonDelta), 2) +
                                    pow(cos(latFrom) * sin(latTo) - sin(latFrom) * cos(latTo) * cos(lonDelta), 2) a,
                                    sin(latFrom) * sin(latTo) + cos(latFrom) * cos(latTo) * cos(lonDelta) b,
                                    codigo,
                                    latitud,
                                    longitud,
                                    centralDesc,
                                    idZonal,
                                    idEmpresaColab,
                                    distrito,
                                    departamento,
                                    region,
                                    idCentral,
                                    empresaColabDesc,
                                    zonalDesc,
                                    distritoDesc,
                                    departamentoDesc,
                                    provinciaDesc
                             FROM (
                                        SELECT radians(latitud)  AS latFrom,
                                                radians(" . $long . ") - radians(longitud) AS lonDelta,
                                                radians(" . $long . ") lonTo,
                                                radians(" . $lat . ") as  latTo,
                                                codigo,
                                                latitud,
                                                longitud,
                                                centralDesc,
                                                z.idZonal,
                                                e.idEmpresaColab,
                                                distrito,
                                                departamento,
                                                region,
                                                idCentral,
                                                empresaColabDesc,
                                                zonalDesc,
                                                distritoDesc,
                                                departamentoDesc,
                                                provinciaDesc
                                        FROM (central c,
                                                empresacolab e,
                                                zonal z)
                                    LEFT JOIN (
                                                    SELECT di.idDistrito,
                                                        di.distritoDesc, 
                                                        de.departamentoDesc, 
                                                        pr.provinciaDesc
                                                    FROM distrito di, 
                                                        departamento de, 
                                                        provincia pr
                                                    WHERE di.idProvincia = pr.idProvincia
                                                    AND di.idDepartamento = de.idDepartamento
                                                    )di
                                            ON di.idDistrito = c.idDistrito
                                        WHERE idTipoCentral = 1
                                            AND e.idEmpresaColab = c.idEmpresaColab
                                            AND z.idZonal        = c.idZonal
                                    )t
                            )tt
                    ORDER BY distancia ASC
                    limit 1";
        $result = $this->db->query($sql);
        return $result->row_array();
    }

    function getCodigoItemplan($idZonal, $idProyecto) {
        $sql = "SELECT fn_get_itemplan(?, ?) as cod_itemplan";
        $result = $this->db->query($sql, array($idZonal, $idProyecto));
        return $result->row_array()['cod_itemplan'];
    }

    function registrarItemplan($dataInsert) {
        // pre($dataInsert);
        $this->db->insert('planobra', $dataInsert);
        // log_message('error', $this->db->last_query());
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = "No se ingreso el registro";
            $data['error'] = EXIT_ERROR;
        }else{
            $data['msj'] = "Se registró correctamente el itemplan";
            $data['error'] = EXIT_SUCCESS;
        }

        return $data;
    }

    function getPlanobraAll($itemplan, $idSubProyecto, $arrayEstadoPlan, $idEmpresaColab=null, $idTipoPlanta=null) {
        $sql = " SELECT po.itemplan,
                        po.nombrePlan,
                        po.longitud,
                        po.latitud,
                        po.costo_unitario_mo,
                        po.fechaInicio,
                        po.fechaPrevEjecucion,
                        f.faseDesc,
                        po.codigoInversion,
                        e.empresaColabDesc,
                        z.zonalDesc,
                        es.estadoPlanDesc,
                        s.subproyectoDesc,
                        e.idEmpresaColab,
                        po.idEstadoPlan,
						es.class_color,
						s.idSubProyecto,
                        s.idProyecto,
						( SELECT COUNT(1) count 
                            FROM itemplanestacionavance i
                           WHERE i.itemplan = po.itemplan
                             AND porcentaje = 100) count_porcentaje,
						( SELECT ruta_evidencia
                            FROM itemplanestacionavance i
                           WHERE i.itemplan = po.itemplan) ruta_evidencia,
                        po.flg_update_mat,
                        p.proyectoDesc,
						po.ult_codigo_sirope,
						po.ult_estado_sirope,
						po.has_sirope_coax, 
						po.ult_estado_sirope_coax, 
						po.ult_codigo_sirope_coax,
						po.orden_compra,
                        po.has_sirope_fo,
                        lr.estado as has_rechazo_liquidacion_pin
                   FROM planobra po LEFT JOIN liquidacion_rechazo lr ON po.itemplan = lr.itemplan AND lr.estado = 1,
                        empresacolab e,
                        zonal z,
                        fase f,
                        estadoplan es,
                        subproyecto s,
						proyecto p
                  WHERE p.idProyecto     = s.idProyecto
				    AND po.itemplan      = COALESCE(?, po.itemplan)
                    AND po.idSubProyecto = COALESCE(?, po.idSubProyecto)
                    AND CASE WHEN ? IN (0,6) THEN true 
                             ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) END
                    AND CASE WHEN 0 IN ? THEN true 
                             ELSE po.idEstadoPlan IN ? END
					AND p.idTipoPlanta = COALESCE(?, p.idTipoPlanta)
                    AND s.idSubProyecto  = po.idSubProyecto
                    AND e.idEmpresaColab = po.idEmpresaColab
                    AND z.idZonal 		 = po.idZonal
                    AND f.idFase  		 = po.idFase
                    AND es.idEstadoPlan  = po.idEstadoPlan";
        $result = $this->db->query($sql, array($itemplan, $idSubProyecto, $idEmpresaColab, $idEmpresaColab, $arrayEstadoPlan, $arrayEstadoPlan, $idTipoPlanta));
        return $result->result_array();
    }

    function getPlanObraByItemplan($itemplan, $idEstadoPo=null) {
        $sql = " SELECT po.itemplan,
                        po.nombrePlan,
                        po.longitud,
                        po.latitud,
                        po.fechaInicio,
                        po.fechaPrevEjecucion,
                        f.faseDesc,
                        po.codigoInversion,
                        e.empresaColabDesc,
                        z.zonalDesc,
                        es.estadoPlanDesc,
                        s.subproyectoDesc,
                        e.idEmpresaColab,
                        po.fechaRegistro,
                        fechaLog,
                        ce.codigo,
                        s.idProyecto,
                        pro.idTipoPlanta,
                        CASE WHEN po.costo_unitario_mo < (
                                                            SELECT SUM(costo_total) 
                                                              FROM planobra_po 
                                                             WHERE estado_po = COALESCE(?, estado_po)
                                                               AND flg_tipo_area = 2
                                                               AND itemplan = po.itemplan
                                                         ) THEN 1 
                             ELSE 0 END flg_solicitud_edit,
						po.idEstadoPlan,
						po.paquetizado_fg,
                        (CASE WHEN ce.idJefatura = 13 THEN 1 ELSE 2 END) AS tipoJefatura,
                        po.cantFactorPlanificado,
                        s.idTipoSubProyecto,
                        po.cant_cto,
                        (CASE WHEN s.subProyectoDesc LIKE '%OVERLAY%' THEN '1' ELSE '2' END) AS tipo_edificio,
                        s.idSubProyecto,
						s.idTipoComplejidad,
						s.flg_tipo,
                        s.flg_flujo,
						po.has_sirope_fo,
                        po.has_sirope_diseno,
                        po.has_sirope_coax,
                        po.has_sirope_coax_diseno,
                        po.has_sirope_ac,
                        po.ult_codigo_sirope_coax,
                        po.ult_codigo_sirope,
						po.costo_unitario_mo,
                        po.orden_compra,
                        po.orden_compra_diseno,
                        po.itemplan_m,
                        po.is_habilitacion
                   FROM planobra po,
                        empresacolab e,
                        zonal z,
                        fase f,
                        estadoplan es,
                        subproyecto s,
                        central ce,
                        proyecto pro,
						jefatura j
                  WHERE po.itemplan      = ?
                    AND pro.idProyecto   = s.idProyecto
                    AND s.idSubProyecto  = po.idSubProyecto
                    AND e.idEmpresaColab = po.idEmpresaColab
                    AND z.idZonal 		 = po.idZonal
                    AND f.idFase  		 = po.idFase
                    AND es.idEstadoPlan  = po.idEstadoPlan
                    AND po.idCentral     = ce.idCentral
					AND ce.idJefatura    = j.idJefatura";
        $result = $this->db->query($sql, array($idEstadoPo, $itemplan));
        // log_message('error', "===============================");
        log_message('error', $this->db->last_query());
        // log_message('error', "===============================");
        return $result->row_array();
    }

    function getKitPartidasPinByItemplan($itemplan) {
        $sql = " SELECT pa.codigoPartida,
                        pa.descripcion as nomPartida,
                        baremo,
                        costo_material,
                        pre.costo as costoPreciario
                   FROM kit_partida k,
                        planobra po,
                        partida pa,
                        preciario_pin pre
                WHERE k.idSubProyecto    = po.idSubProyecto
                  AND pa.codigoPartida   = k.codigoPartida
                  AND pre.idEmpresaColab = po.idEmpresaColab
                  AND pre.idZonal        = po.idZonal
                  AND po.itemplan        = ?";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function getCodigoPO($itemplan) {
        $Query = "SELECT fn_get_codigo_po(?) as codigoPO";
        // pre($Query);
        $result = $this->db->query($Query, array($itemplan));
        if ($result->row() != null) {
            return $result->row_array()['codigoPO'];
        } else {
            return null;
        }
    }
    
    function getSubProyectoNuevoFLujoCV($flg_tipo) {
        $sql = " SELECT *
                   FROM subproyecto
                  WHERE flg_flujo = 1
                    AND flg_tipo = COALESCE(?,flg_tipo) ";
        $result = $this->db->query($sql,array($flg_tipo));
        return $result->result_array();
    }

    function getEECCNuevoFLujoCV($flg_cv) {
        $sql = " SELECT *
                   FROM empresacolab
                  WHERE flg_cv = ? ";
        $result = $this->db->query($sql,array($flg_cv));
        return $result->result_array();
    }

    function getDataSubProyectoById($idSubProyecto) {
        $sql = "SELECT *, (CASE WHEN subProyectoDesc LIKE '%OVERLAY%' THEN '1' ELSE '2' END) AS flg_overlay
                  FROM subproyecto 
                 WHERE idSubProyecto = ? ";
        $result = $this->db->query($sql, array($idSubProyecto));
        return $result->row_array();
    }

    function getIdSubProyectoBySubProyectoDesc($descripcion) {
        $sql = "  SELECT idSubProyecto 
		            FROM subproyecto 
				   WHERE REPLACE(subproyectoDesc,' ', '') = REPLACE(?,' ', '')
				     AND estado = 1 ";
        $result = $this->db->query($sql, array($descripcion));
        if ($result->row() != null) {
            return $result->row_array()['idSubProyecto'];
        } else {
            return null;
        }
    }

    function fechaActual() {
        $zonahoraria = date_default_timezone_get();
        ini_set('date.timezone', 'America/Lima');
        setlocale(LC_TIME, "es_ES", "esp");
        $hoy = strftime("%Y-%m-%d %H:%M:%S");
        return $hoy;
    }

    function registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO) {
        $this->db->insert('planobra_po', $dataPo);
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'No se registro el detalle de la po';
            $data['error'] = EXIT_ERROR;
        } else {
            $this->db->insert_batch('planobra_po_detalle_mo', $arrayDetalleInsert);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se registro la po';
                $data['error'] = EXIT_ERROR;
            } else {
                $this->db->insert('log_planobra_po', $dataLogPO);
                if($this->db->affected_rows() <= 0) {
                    $data['msj'] = 'No se registro el log de la PO';
                    $data['error'] = EXIT_ERROR;
                } else {
                    $data['msj'] = 'Se registro correctamente';
                    $data['error'] = EXIT_SUCCESS;
                    
                }
            }
        }

        return $data;
    }


    function actualizarPoByItemplan($itemplan, $estadoPoActual, $objPo, $idEstacion, $flg_area, $idUsuario) {
        $data = $this->insertLogPOMasivo($itemplan, $idEstacion, $idUsuario, $estadoPoActual, $objPo['estado_po'], $flg_area);
        
        if($data['error'] == EXIT_SUCCESS) {
            $this->db->where('estado_po'    , $estadoPoActual);
            $this->db->where('flg_tipo_area', $flg_area);
            $this->db->where('itemplan'     , $itemplan);
            $this->db->update('planobra_po' , $objPo);

            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se encontro ninguna PO liquidada';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se actualizó correctamente';
                $data['error'] = EXIT_SUCCESS;
            }
        }
        return $data;
    }

    function actualizarPoByCodigo($codigoPo, $objPo) {
        $this->db->where('codigo_po'    , $codigoPo);
        $this->db->update('planobra_po' , $objPo);

        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'No se actualizo la po';
            $data['error'] = EXIT_ERROR;
        } else {
            $data['msj'] = 'Se actualizó correctamente';
            $data['error'] = EXIT_SUCCESS;
        }
        
        return $data;
    }

    function insertLogPOMasivo($itemplan, $idEstacion, $idUsuario, $estadoPoActual, $estadoPoNuevo, $flgTipoArea) {
		$sql = "INSERT log_planobra_po
				SELECT '', codigo_po, itemplan, ".$idUsuario.", NOW(), ".$estadoPoNuevo.", 'liquidacion PO auto',null 
				  FROM planobra_po
				 WHERE idEstacion = ".$idEstacion."
				   AND itemplan = '".$itemplan."'
				   AND flg_tipo_area = ".$flgTipoArea."
				   AND estado_po     = ".$estadoPoActual;
		$result = $this->db->query($sql);
		if($this->db->trans_status() === FALSE) {
			$data['msj'] = 'No se registro el log de la PO';
            $data['error'] = EXIT_ERROR;
		} else {
			$data['msj'] = 'Se registro correctamente';
            $data['error'] = EXIT_SUCCESS;
		}
        return $data;
	}

    function actualizarPlanObra($itemplan, $dataPlanObra) {
        $this->db->where('itemplan', $itemplan);
        $this->db->update('planobra', $dataPlanObra);
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'No se actualizo Plan Obra';
            $data['error'] = EXIT_ERROR;
        } else {
            $data['msj'] = 'Se actualizo correctamente';
            $data['error'] = EXIT_SUCCESS;
            
        }
        return $data;
    }

    function getEmpresaColabByDesc($eecc) {
        $sql = " SELECT idEmpresaColab 
                   FROM empresacolab 
                  WHERE REPLACE(empresaColabDesc,' ', '') = UPPER(REPLACE(?,' ', ''))";
        $result = $this->db->query($sql, array($eecc));
		log_message('error', $this->db->last_query());
        return $result->row_array()['idEmpresaColab'];
    }

    function getDataCoordenadasNodo() {
        $sql = "SELECT DISTINCT
					   c.idCentral,
					   c.codigo,
					   c.latitud,
					   c.longitud,
					   c.idZonal,
					   c.idEmpresaColab,
					   CONCAT(c.codigo,' - ',tc.tipoCentralDesc) nom_central,
					   c.departamento,
					   c.distrito
				  FROM central c,
                       tipo_central tc 
				 WHERE c.idTipoCentral = tc.idTipoCentral
                   AND c.latitud <> ''
				   AND c.latitud IS NOT NULL
				   AND c.idTipoCentral IN (1)";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getIdCentralByCentralDescPqt($codigo) {
        $sql = "SELECT 	c.idCentral,
						po.paquetizado_fg,
						c.idZonal, 
						c.idEmpresaColab,
						tc.tipoCentralDesc,
						c.latitud,
						c.longitud,
						c.codigo, 
						j.jefaturaDesc AS jefatura,
						c.idEmpresaColabCV,
						CONCAT(c.codigo,' - ',tc.tipoCentralDesc) nom_central,
						c.departamento,
						c.distrito
				   FROM tipo_central tc,
                        jefatura j,
                        central c 
			  LEFT JOIN planobra po on po.idCentral = c.idCentral
				  WHERE c.idTipoCentral = tc.idTipoCentral
                    AND c.idJefatura = j.idJefatura
                    AND UPPER(c.codigo) = UPPER(?)
				  LIMIT 1 ";
        $result = $this->db->query($sql, array($codigo));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    function getNextCodSolicitud() {
        $sql = " SELECT fn_get_codigo_solicitud_oc() AS codigoSolicitud";
        $result = $this->db->query($sql, array());
        if ($result->row() != null) {
            return $result->row_array()['codigoSolicitud'];
        } else {
            return null;
        }
    }

    function fnRegistrarSolicitudOc($itemplan, $costo, $idUsuario, $nomSolicitud) {
        $sql = "SELECT fn_create_solicitud_oc(?, ?, ?, ?) as resp;";
        $result = $this->db->query($sql, array($itemplan, $costo, $idUsuario, $nomSolicitud));
        return $result->row_array()['resp'];
    }

    function getCountAgendaEjecutadaByIP($itemplan) {
		$sql = " SELECT COUNT(*) AS cantidad 
		           FROM agenda_cv_item 
		          WHERE estado = '4' 
				    AND itemplan = ? ";
		$result = $this->db->query($sql, array($itemplan));
        return $result->row()->cantidad;  
	}
    
    function getSubProyectoEstaciosByItemplan($itemplan) {
        $sql = " SELECT e.estacionDesc,
                        sa.idEstacion,
                        iea.porcentaje,
                        GROUP_CONCAT(DISTINCT sa.idArea) as arrayIdArea,
                        GROUP_CONCAT(DISTINCT a.tipoArea) as arrayTipoArea,
                        GROUP_CONCAT(DISTINCT a.areaDesc) as arrayAreaDesc,
                        GROUP_CONCAT(DISTINCT CONCAT(sa.idArea,'|', a.areaDesc)) AS arrayAreaDet
                FROM (subproyecto_area_estacion sa,
                        area a,
                        estacion e,
                        planobra po)
                LEFT JOIN itemplanestacionavance iea ON (iea.idEstacion = e.idEstacion AND iea.itemplan = po.itemplan)
                WHERE sa.idArea = a.idArea
                    AND e.idEstacion = sa.idEstacion
                    AND po.idSubProyecto = sa.idSubProyecto
                    AND po.itemplan = ?
                GROUP BY sa.idEstacion";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function insertItemplanEstacionAvance($objDetalle) {
        $this->db->insert('itemplanestacionavance', $objDetalle);
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'No se registro detalle';
            $data['error'] = EXIT_ERROR;
        } else {
            $data['msj'] = 'Se actualizo correctamente';
            $data['error'] = EXIT_SUCCESS;
            
        }
        return $data;
    }

    function actualizarItemplanEstacionAvance($itemplan, $idEstacion, $objDetalle) {
        $this->db->where('itemplan', $itemplan);
        $this->db->where('idEstacion', $idEstacion);
        $this->db->update('itemplanestacionavance', $objDetalle);
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'No se actualizo el detalle';
            $data['error'] = EXIT_ERROR;
        } else {
            $data['msj'] = 'Se actualizo correctamente';
            $data['error'] = EXIT_SUCCESS;
            
        }
        return $data;
    }

    function countItemplanEstacionAvance($itemplan, $idEstacion) {
        $sql = "SELECT COUNT(1) count 
                  FROM itemplanestacionavance 
                 WHERE itemplan = ? 
                   AND idEstacion = ?";
        $result = $this->db->query($sql, array($itemplan, $idEstacion));
        return $result->row_array()['count'];
    }

    function getLogPlanobra($itemplan) {
        $sql = " SELECT lp.itemplan,
                        ep.estadoPlanDesc AS estado_actual_ip,
                        ep2.estadoPlanDesc AS estado_prev_ip,
                        fechaReg AS fecha_upd,
                        u.usuario,
                        COALESCE(lp.descripcion,'') comentario
                   FROM planobra po,
                        estadoplan ep,
                        log_planobra lp
              LEFT JOIN usuario u ON lp.idUsuarioReg = u.id_usuario
              LEFT JOIN estadoplan ep2 ON lp.idEstadoPlan = ep2.idEstadoPlan
                  WHERE po.itemplan = lp.itemplan
                    AND po.idEstadoPlan = ep.idEstadoPlan
                    AND po.itemplan = ?
               ORDER BY 4 ASC ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function fnRegistrarSolicitudOcEdicCerti($itemplan, $costo, $idUsuario, $nomSolicitud, $tipoSolicitud) {
        $sql = "SELECT fn_create_solicitud_certi_oc(?, ?, ?, ?, ?) as resp;";
        $result = $this->db->query($sql, array($itemplan, $nomSolicitud, $tipoSolicitud, $costo, $idUsuario));
        return $result->row_array()['resp'];
    }

    function getDataPoByItemplan($itemplan, $arrayEstado, $flg_tipo_area = null) {
        $sql = " SELECT ppo.codigo_po,
                        ppo.costo_total,
                        ppo.itemplan
                   FROM planobra_po ppo
                  WHERE ppo.itemplan  = COALESCE(?, ppo.itemplan)
                    AND ppo.flg_tipo_area = COALESCE(?, ppo.flg_tipo_area)
                    AND CASE WHEN ? IN (0) THEN true
                             ELSE ppo.estado_po IN ? END";
        $result = $this->db->query($sql, array($itemplan, $flg_tipo_area, $arrayEstado, $arrayEstado));
		return $result->result_array();
    }

    function getDataPoDetalleMo($itemplan, $codigoPo, $arrayEstado) {
        $sql = " SELECT ppo.codigo_po,
                        ppd.codigoPartida,
                        pa.descripcion as nomPartida,
                        ppd.baremo,
                        ppd.preciario,
                        ppd.cantidadFinal,
                        ppd.cantidadInicial,
                        ppd.costoMo,
                        ppd.costoMat,
                        ppd.montoFinal,
                        pa.costo_material,
                        (costoMat/cantidadFinal) as precioKit,
						ppo.costo_total,
						ppd.montoInicial
                   FROM planobra_po_detalle_mo ppd,
                        planobra_po ppo,
                        partida pa
                  WHERE ppo.codigo_po = ppd.codigo_po
                    AND pa.codigoPartida = ppd.codigoPartida
                    AND ppo.itemplan  = COALESCE(?, ppo.itemplan)
                    AND ppo.codigo_po = COALESCE(?, ppo.codigo_po)
                    AND CASE WHEN ? IN (0) THEN true
                             ELSE ppo.estado_po IN ? END";
        $result = $this->db->query($sql, array($itemplan, $codigoPo, $arrayEstado, $arrayEstado));
		return $result->result_array();
    }

	function getArrayPOByFiltros($itemplan, $idEstacion, $idArea) {
        $sql = " 	 SELECT ppo.*,
							(CASE WHEN estado_po = 1 THEN '#FF8989'
								  WHEN estado_po = 2 THEN '#FF8989'
								  WHEN estado_po = 3 THEN '#1CDDC5'
								  WHEN estado_po = 4 THEN '#78E900'
								  WHEN estado_po = 5 THEN '#767680'
								  WHEN estado_po = 6 THEN '#d4d61c'
								  WHEN estado_po = 7 THEN 'steelblue'
								  WHEN estado_po = 8 THEN 'steelblue'
								   END ) color_po,
							(CASE WHEN estado_po = 1 THEN '#ffcece'
										WHEN estado_po = 2 THEN '#ffcece'
												WHEN estado_po = 3 THEN '#c7f7f2'
												WHEN estado_po = 4 THEN '#cef1a9'
												WHEN estado_po = 5 THEN '#cacacd'
												WHEN estado_po = 6 THEN '#f8fa90'
												WHEN estado_po = 7 THEN '#acc8df'
												WHEN estado_po = 8 THEN '#acc8df'
										END) contraste_color,
							UPPER(poe.estado) estadoDesc,
							e.empresaColabDesc
					   FROM planobra_po ppo,
					   		po_estado poe,
                            empresacolab e
					  WHERE ppo.estado_po = poe.idEstadoPo
					    AND ppo.itemplan = ?
						AND ppo.idEstacion = ?
						AND ppo.idArea = ?
                        AND e.idEmpresaColab = ppo.idEmpresaColab";
        $result = $this->db->query($sql, array($itemplan, $idEstacion, $idArea));
        return $result->result_array();
    }

	function getLogPO($codigoPO) {
		$sql = "     SELECT lppo.codigo_po,
							lppo.itemplan,
							lppo.idUsuario,
							UPPER(u.nombre_completo) usuario_registro,
							lppo.fecha_registro,
							lppo.idPoestado,
							UPPER(poe.estado) AS estado_po
					   FROM log_planobra_po lppo,
						    usuario u,
							po_estado poe
					  WHERE lppo.idUsuario = u.id_usuario
					    AND lppo.idPoestado = poe.idEstadoPo
					    AND lppo.codigo_po = ?
				   ORDER BY 5 ASC ";
        $result = $this->db->query($sql, array($codigoPO));
		return $result->result_array();
    }

    function getDataItemplanByCodOcCerti($codigoSolicitudCerti) {
        $sql = "SELECT itemplan, 
		               idEstadoPlan
                  FROM planobra 
                 WHERE solicitud_oc_certi = ?";
        $result = $this->db->query($sql, array($codigoSolicitudCerti));
        return $result->row_array();         
    }
    
    function getDataItemplanByCodOcCertiDiseno($codigoSolicitudCerti) {
        $sql = "SELECT itemplan, 
                       idEstadoPlan
                  FROM planobra 
                 WHERE solicitud_oc_certi_diseno = ?";
        $result = $this->db->query($sql, array($codigoSolicitudCerti));
        return $result->row_array();         
    }

    function getCountPoByItemplanAndEstado($itemplan, $estado_po) {
		$sql = "SELECT COUNT(1) as count
		          FROM planobra_po 
				 WHERE itemplan  = ?
				   AND estado_po = ?";
		$result = $this->db->query($sql, array($itemplan, $estado_po));
		return $result->row_array()['count'];
	}

    function getAreaByItemplanTipoArea($itemplan, $tipoArea, $idEstacion) {
        $sql = " SELECT s.idEstacion,
                        s.idArea,
                        s.idSubProyecto 
                   FROM subproyecto_area_estacion s,
                        planobra po,
                        area a
                  WHERE s.idSubProyecto = po.idSubProyecto
                    AND a.idArea        = s.idArea
                    AND po.itemplan  = ?
                    AND a.tipoArea   = ?
                    AND s.idEstacion = ?
                    AND (case when s.idEstacion = 1 THEN a.idArea = 2 ELSE TRUE end)";
        $result = $this->db->query($sql, array($itemplan, $tipoArea, $idEstacion));
        return $result->row_array();
    }

    function getKitPartidasPinByItemplandetalle($itemplan) {
        $sql = "SELECT *
        FROM planobra_po_detalle_mo 
        INNER JOIN planobra_po
        ON planobra_po_detalle_mo.codigo_po = planobra_po.codigo_po
        
        inner join partida
        on planobra_po_detalle_mo.codigoPartida=partida.codigoPartida 
        where planobra_po.itemplan=?";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function actualizarSolicitudPlanObraMasivo($solicitudes_list, $itemplanList) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();            
            $this->db->update_batch('solicitud_orden_compra',$solicitudes_list, 'codigo_solicitud');
            if($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception('Error al modificar el solicitud_orden_compra');
            }else{
               $this->db->update_batch('planobra',$itemplanList, 'itemplan');
				if($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					throw new Exception('Error al modificar en planobra');
				}else{	  			
					$data['error'] = EXIT_SUCCESS;
					$data['msj'] = 'Se actualizo correctamente!';
					$this->db->trans_commit(); 
				}		       
            }            
	    }catch(Exception $e){
	        $data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $data;
	}

    function getSolicitudOcByCodigo($codigoSolicitud) {
		$sql = "SELECT estado
                  FROM solicitud_orden_compra
                 WHERE codigo_solicitud = ?";
		$result = $this->db->query($sql, array($codigoSolicitud));
		return $result->row_array();
	}

	/**
     * @param tipoArea MO = 2 o MAT = 1
     * @return datos necearios para validacion de creacion de po mo y mat
     */
    function getVariablesCostoUnitario($itemplan, $tipoArea, $codigo_po) {
        $sql = "SELECT po.itemplan, po.idSubProyecto, po.idEstadoPlan, po.costo_unitario_mat, po.costo_unitario_mo, tb.total
                    FROM planobra po    LEFT JOIN (SELECT ppo.itemplan, SUM(costo_total) AS total FROM planobra_po ppo
                                                    WHERE ppo.itemplan = ? 
                                                    AND ppo.estado_po NOT IN (" . ID_ESTADO_PO_PRE_CANCELADO . "," . ID_ESTADO_PO_CANCELADO . ")
                                                    AND ppo.flg_tipo_area = ?
                                                    AND ppo.codigo_po NOT IN (?)
                                                    GROUP BY ppo.itemplan) AS tb
                                        ON po.itemplan = tb.itemplan
                    WHERE po.itemplan = ?
                    LIMIT 1;";
        $result = $this->db->query($sql, array($itemplan, $tipoArea, (($codigo_po != null) ? $codigo_po : 0), $itemplan));
        //log_message('error', $this->db->last_query());
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    public function hasSolExceActivo($itemplan, $tipo_po) {
        $sql = "SELECT  count(1) as cant
                FROM    solicitud_exceso_obra 
                WHERE   itemplan = ?
                #AND     tipo_po = ?
                AND     estado_valida IS NULL";
        $result = $this->db->query($sql, array($itemplan, $tipo_po));
        return $result->row_array()['cant'];
    }
    
	function getCountExcesoPdt($itemplan) {
        $sql = " SELECT COUNT(1) AS cant
                   FROM solicitud_exceso_obra 
                  WHERE itemplan = ?
                    AND estado_valida IS NULL";
        $result = $this->db->query($sql, array($itemplan));
        return $result->row()->cant;
    }

	function registrarSolicitudCP($dataInsert){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            $this->db->insert('solicitud_exceso_obra', $dataInsert);
            if($this->db->affected_rows() <= 0) {
                throw new Exception('Error al insertar en solicitud_exceso_obra');
            }else{               
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se insertó correctamente!';
				$data['id_solicitud'] = $this->db->insert_id();
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

	function regDetalleRegPoMo($dataDetalleSolicitud) {
		$data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
			$this->db->insert_batch('solicitud_exceso_obra_detalle_reg_mo', $dataDetalleSolicitud);
			if($this->db->affected_rows() > 0) {
				$data['error'] = EXIT_SUCCESS;
				$data['msj'] = 'Se insertó correctamente!';
			}else{
				$data['error'] = EXIT_ERROR;
				$data['msj'] = 'Error al insertar en la tabla solicitud_exceso_obra_detalle_reg_mo.';
			}
		}catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
		return $data;
	}

	public function deleteDetallePOMO($codigoPO) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->where('codigo_po', $codigoPO);
            $this->db->delete('planobra_po_detalle_mo');
            if ($this->db->trans_status() === true) {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se eliminó correctamente detalle po en planobra_po_detalle_mo!!';
            } else {
                throw new Exception('Error al eliminar detalle en la tabla planobra_po_detalle_mo');
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

	function registrarDetallePoMo($arrayDetalleInsert) {
		$data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
			$this->db->insert_batch('planobra_po_detalle_mo', $arrayDetalleInsert);
			if($this->db->affected_rows() <= 0) {
				$data['msj'] = 'Error al insertar en la tabla planobra_po_detalle_mo';
				$data['error'] = EXIT_ERROR;
			} else {
				$data['msj'] = 'Se registró correctamente';
				$data['error'] = EXIT_SUCCESS;
			}
		} catch (Exception $e) {
			$data['msj'] = $e->getMessage();
		}
        return $data;
    }

	function getDataDetalleRegMo($id_solicitud) {
        $sql = "   SELECT codigoPartida,
                          baremo,
                          preciario,
                          cantidadInicial,
                          montoInicial,
                          cantidadFinal,
                          montoFinal,
						  costoMo,
						  costoMat 
                     FROM solicitud_exceso_obra_detalle_reg_mo
                    WHERE id_solicitud = ? ";
          
        $result = $this->db->query($sql, array($id_solicitud));
        return $result->result_array();
    }
    
    function getModulos() {
		$db_2 = $this->load->database('db_planobra', TRUE);
		
        $sql = "SELECT idModulo, 
                       nomModulo, 
                       estado, 
                       routeBienvenido,
                       rutaImagen,
					   abrv
                  FROM pan_modulo
                 WHERE estado = 1";
        $result = $db_2->query($sql);
        return $result->result_array();
    }

    function getValidOcEdic($itemplan, $tipoSolicitud) {
		$sql = " SELECT COUNT(1) countPendiente
				   FROM solicitud_orden_compra s,
						itemplan_x_solicitud_oc i
				  WHERE s.codigo_solicitud = i.codigo_solicitud_oc
					AND i.itemplan = ?
					AND s.tipo_solicitud = ?
					AND estado = 1";
		$result = $this->db->query($sql, array($itemplan, $tipoSolicitud));
		return $result->row_array()['countPendiente'];
	}

    function getTotalMoByItemplan($itemplan) {
        $sql = "SELECT SUM(montoFinal) AS total_mo
                  FROM planobra_po ppo,
                       planobra_po_detalle_mo ppd
                 WHERE ppo.codigo_po = ppd.codigo_po
                   AND ppo.estado_po NOT IN (7,8)
                   AND ppo.itemplan  = ?";
        $result = $this->db->query($sql, array($itemplan));
        return $result->row_array()['total_mo'];
    }
		
	//nuevo cableado edificios
	
	function getEstacionesAnclasByItemplan($itemplan) {
        $sql = "  SELECT DISTINCT
		                 po.itemplan, se.idEstacion, e.estacionDesc
                    FROM planobra po,
						 subproyecto sp,
						 subproyecto_area_estacion se,
						 estacion e
                   WHERE po.idSubProyecto = sp.idSubProyecto
				     AND sp.idSubProyecto = se.idSubProyecto
	                 AND se.idEstacion = e.idEstacion
	                 AND se.idEstacion IN (2,5)
	                 AND po.itemplan = ? ";
        $result = $this->db->query($sql,array($itemplan));
        return $result->result_array();
    }
	
	function hasPoPqtActive($itemplan, $idEstacion) {
	    $sql = " SELECT COUNT(1) count
                   FROM planobra_po
			      WHERE itemplan = ?
	                AND idEstacion = ?
	                AND isPoPqt = 1
	                AND estado_po NOT IN (7,8)";
	    $result = $this->db->query($sql, array($itemplan, $idEstacion));
	    return $result->row()->count;
	}

    function hasPoActive($itemplan, $idEstacion) {
	    $sql = " SELECT COUNT(1) count
                   FROM planobra_po
			      WHERE itemplan = ?
	                AND idEstacion = ?	                 
	                AND estado_po NOT IN (7,8)";
	    $result = $this->db->query($sql, array($itemplan, $idEstacion));
	    return $result->row()->count;
	}
	
	function getEntidadAll($itemplan, $idEstacion) {
        $sql = "SELECT e.idEntidad,
                       e.entidadDesc 
                  FROM entidad e
                 WHERE  e.estado = 1 ";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
	
	function registrarEntidad($arrayData) {
        $this->db->insert_batch('entidad_itemplan_estacion', $arrayData);
        if($this->db->affected_rows() > 0) {
            $data['error'] = EXIT_SUCCESS;
            $data['msj'] = 'Se insertó correctamente!';
        }else{
            $data['error'] = EXIT_ERROR;
            $data['msj'] = 'Error al insertar en la tabla entidad.';
        }

        return $data;
    }
	
	public function getListaMaterialesToEdificios($tipo_edificio, $cant_cto) {
        $sql = "  SELECT ed.codigo_material, 
                         ed.cantidad, 
                         1 AS cantidad_kit, 
                         80 AS factor_porcentual, 
                         '' AS motivo,
                         m.descrip_material,
                         m.costo_material,
                         m.estado_material,
                         m.flg_tipo,
                         m.unidad_medida,
                         m.id_udm,
                         m.flg_auto,
                         m.flg_lic_eia,
                         m.paquetizado,m.costo_anterior,
                         ROUND(m.costo_material*ed.cantidad,2) AS total				
                    FROM edificios_materiales_automatico ed, 
                         material m 
                   WHERE ed.codigo_material = m.codigo_material
                     AND tipo_edificio = ?
                     AND (CASE WHEN tipo_edificio IN (1,2) THEN nro_cto = ? ELSE TRUE END)";
				
        $result = $this->db->query($sql, array($tipo_edificio, $cant_cto));
        return $result->result_array();
    }
	
	public function countMatxKit($codMaterial, $idSubProyecto, $idEstacion) {
        $sql = " SELECT COUNT(codigo_material) AS cantidad
		           FROM kit_material
                  WHERE idSubProyecto = ?
                    AND idEstacion = ?
				    AND codigo_material = ? ";
        $result = $this->db->query($sql,array($idSubProyecto,$idEstacion,$codMaterial));
        return $result->row()->cantidad;
    }
	
	function registrarPoMat($dataPo, $arrayDetalleInsert, $dataLogPO) {
        $this->db->insert('planobra_po', $dataPo);
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'No se registro la po';
            $data['error'] = EXIT_ERROR;
        } else {
            $this->db->insert_batch('planobra_po_detalle_mat', $arrayDetalleInsert);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se registro el detalle de la po';
                $data['error'] = EXIT_ERROR;
            } else {
                $this->db->insert('log_planobra_po', $dataLogPO);
                if($this->db->affected_rows() <= 0) {
                    $data['msj'] = 'No se registro el log de la PO';
                    $data['error'] = EXIT_ERROR;
                } else {
                    $data['msj'] = 'Se registró correctamente';
                    $data['error'] = EXIT_SUCCESS;
                    
                }
            }
        }

        return $data;
    }
	
	function getEntidadByItemplanEstacion($itemplan, $idEstacion, $idEntidad = null, $id = null) {
        $sql = "SELECT ei.*,
                       e.entidadDesc,
                       t.flg_comprobante,
                       po.idEstadoPlan                        
                  FROM (entidad_itemplan_estacion ei,
                       entidad e,
					   planobra po)
             LEFT JOIN tipo_entidad t ON (t.idTipoEntidad = ei.idTipoEntidad)
                 WHERE ei.itemplan   = ?
                   AND ei.idEstacion = ?
                   AND ei.idEntidad = COALESCE(?, ei.idEntidad)
				   AND ei.id = COALESCE(?, ei.id)
                   AND ei.idEntidad = e.idEntidad
				   AND ei.itemplan = po.itemplan ";
        $result = $this->db->query($sql, array($itemplan, $idEstacion, $idEntidad, $id));
        log_message('error', $this->db->last_query());
        return $result->result_array();
    }
	
	function getTipoEntidadAll() {
        $sql = " SELECT t.idTipoEntidad, 
                        t.tipoEntidadDesc, 
                        t.estado
                   FROM tipo_entidad t
                  WHERE t.estado = 1";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
	
	function getDistritoAll() {
        $sql = " SELECT d.idDistrito, 
                        d.distritoDesc, 
                        d.estado
                   FROM distrito d
                  WHERE d.estado = 1 ";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
	
	function registrarLogPO($dataLogPO) {
        $data['error'] = EXIT_ERROR;
	    $data['msj'] = null;
	    try{
            $this->db->insert('log_planobra_po', $dataLogPO);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se registró el log de la PO';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se registró correctamente';
                $data['error'] = EXIT_SUCCESS;   
            }
        }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
    }
	
	function getDataPOByCod($codigoPO, $arrayEstado) {
        $sql = " SELECT ppo.*
                   FROM planobra_po ppo
                  WHERE ppo.codigo_po = ?
                    AND CASE WHEN ? IN (0) THEN true
                             ELSE ppo.estado_po IN ? END";
        $result = $this->db->query($sql, array($codigoPO, $arrayEstado, $arrayEstado));
		return $result->row_array();
    }
	
	function getDataPoDetalleMat($itemplan, $codigoPo, $arrayEstado) {
        $sql = " SELECT ppo.codigo_po,
                        ppd.codigo_material,
                        m.descrip_material,
                        m.unidad_medida,
                        ppd.costoMat,
                        ppd.cantidadFinal,
                        ppd.cantidadInicial,
                        ppd.montoFinal,
                        ppo.costo_total
                   FROM planobra_po_detalle_mat ppd,
                        planobra_po ppo,
                        material m
                  WHERE ppo.codigo_po = ppd.codigo_po
                    AND ppd.codigo_material = m.codigo_material
                    AND ppo.itemplan = COALESCE(?, ppo.itemplan)
                    AND ppo.codigo_po = COALESCE(?, ppo.codigo_po)
                    AND CASE WHEN ? IN (0) THEN true
                             ELSE ppo.estado_po IN ? END";
        $result = $this->db->query($sql, array($itemplan, $codigoPo, $arrayEstado, $arrayEstado));
		return $result->result_array();
    }
	
	function actualizarLicencia($jsonExp) {
        $this->db->where('id', $jsonExp['id']);
        $this->db->update('entidad_itemplan_estacion', $jsonExp);
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = 'No se actualizo la entidad.';
            $data['error'] = EXIT_ERROR;
        } else {
            $data['msj'] = 'Se registro correctamente';
            $data['error'] = EXIT_SUCCESS;
        }

        return $data;
    }
    function getDisenoByItemplan($itemplan) {
        $sql = "   SELECT d.*,
                          e.estacionDesc,
                          (CASE WHEN po.orden_compra IS NOT NULL THEN 1 ELSE 0 END) flg_orden_compra,
                          u.nombre_completo AS usu_reg,
                          u1.nombre_completo AS usu_ejecucion,
                          (CASE WHEN d.requiere_licencia = 2 THEN 'NO' ELSE 'SI' END) AS licencia,
                          po.idSubProyecto
                     FROM estacion e,
                          planobra po,
                          diseno d
                LEFT JOIN usuario u ON d.usuario_registro = u.id_usuario
                LEFT JOIN usuario u1 ON d.usuario_ejecucion = u1.id_usuario
                    WHERE d.idEstacion = e.idEstacion
                      AND d.itemplan = po.itemplan
                      AND d.itemplan = ? ";
        $result = $this->db->query($sql, array($itemplan));
		return $result->result_array();
    }

    function getEntidades($itemplan,$idEstacion){
        $sql = "  SELECT e.*,
                         (  SELECT 1
                              FROM entidad_itemplan_estacion
                             WHERE idEntidad = e.idEntidad
                               AND itemplan = ?
                               AND idEstacion = ?
                             LIMIT 1) AS marcado
                    FROM entidad e
                   WHERE e.estado = 1 ";
        $result = $this->db->query($sql,array($itemplan,$idEstacion));
		return $result->result();
    }

    function getCountPOsEstacionesByItemplan($itemplan){
        $sql = "  SELECT tb1.itemplan,
                         SUM(CASE WHEN tb1.idEstacion = 5 THEN 1 ELSE 0 END) conFo,
                         SUM(CASE WHEN tb1.idEstacion = 2 THEN 1 ELSE 0 END) conCoax
                    FROM 
                         (	
                            SELECT po.itemplan,
                                   po.idSubProyecto,
                                   sae.idEstacion,
                                   sae.idArea,
                                   ppo.codigo_po
                              FROM planobra po,
                                   subproyecto sp,
                                   subproyecto_area_estacion sae,
                                   planobra_po ppo
                             WHERE po.idSubProyecto = sp.idSubProyecto
                               AND sp.idSubProyecto = sae.idSubProyecto
                               AND sae.idEstacion = ppo.idEstacion
                               AND sae.idArea = ppo.idArea
                               AND po.itemplan = ppo.itemplan
                               AND po.itemplan = ? ) tb1
                          GROUP BY 1 ";
	    $result = $this->db->query($sql,array($itemplan));
	    return $result->row_array();
	}

    function getCountPOsMatEstacionesByItemplan($itemplan){
        $sql = "  SELECT tb1.itemplan,
                         SUM(CASE WHEN tb1.idEstacion = 5 THEN 1 ELSE 0 END) conFo,
                         SUM(CASE WHEN tb1.idEstacion = 2 THEN 1 ELSE 0 END) conCoax,
                         SUM(CASE WHEN tb1.idEstacion = 16 THEN 1 ELSE 0 END) conFoDist,
                         SUM(CASE WHEN tb1.idEstacion = 15 THEN 1 ELSE 0 END) conFoAlim,
                         SUM(CASE WHEN tb1.idEstacion = 1 THEN 1 ELSE 0 END) conDiseno
                    FROM 
                         (	
                            SELECT po.itemplan,
                                   po.idSubProyecto,
                                   sae.idEstacion,
                                   sae.idArea,
                                   ppo.codigo_po
                              FROM planobra po,
                                   subproyecto sp,
                                   subproyecto_area_estacion sae,
                                   planobra_po ppo
                             WHERE po.idSubProyecto = sp.idSubProyecto
                               AND sp.idSubProyecto = sae.idSubProyecto
                               AND sae.idEstacion = ppo.idEstacion
                               AND sae.idArea = ppo.idArea
                               AND po.itemplan = ppo.itemplan
                               #AND ppo.flg_tipo_area = 1
                               AND CASE WHEN po.idSubProyecto = 737 then ppo.flg_tipo_area = 2 else ppo.flg_tipo_area = 1 END
							   AND ppo.estado_po NOT IN (7,8)
                               AND po.itemplan = ? ) tb1
                          GROUP BY 1 ";
	    $result = $this->db->query($sql,array($itemplan));
	    return $result->row_array();
	}

    function getInfoEstacionById($idEstacion){
        $sql = "  SELECT es.*
                    FROM estacion es
                   WHERE es.idEstacion = ? ";
        $result = $this->db->query($sql,array($idEstacion));
		return $result->row_array();
    }

    function getSubProyAreaEstacionDisenoByFiltros($idSubProyecto,$idEstacion){
        $sql = " SELECT * 
                   FROM subproyecto_area_estacion 
                  WHERE idSubProyecto = ? 
                    AND idEstacion = 1
                    AND CASE WHEN ? = 5 THEN idArea = 2
                             WHEN ? = 2 THEN idArea = 1 END ";
        $result = $this->db->query($sql,array($idSubProyecto,$idEstacion,$idEstacion));
		return $result->row_array();
    }

    function getLogSeguimientoCV($itemplan) {
        $sql = "       SELECT ls.itemplan,
                              ls.id_motivo_seguimiento,
                              ms.situacion_general,
                              ms.desc_motivo,
                              ls.usuario_registro,
                              u.nombre_completo,
                              ls.fecha_registro,
                              ls.comentario_incidencia,
                              eps.estadoPlanDesc AS estadoPlanMomentoReg,
                              ep.estadoPlanDesc AS estadoPlanActual
                         FROM log_seguimiento_cv ls
                    LEFT JOIN usuario u ON ls.usuario_registro = u.id_usuario,
                              motivo_seguimiento_cv ms,
                              estadoplan eps,
                              planobra po,
                              estadoplan ep
                        WHERE ls.id_motivo_seguimiento = ms.id
                          AND ls.idEstadoPlan = eps.idEstadoPlan
                          AND ls.itemplan = po.itemplan
                          AND po.idEstadoPlan = ep.idEstadoPlan
                          AND ls.itemplan = ? ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function getMotivoSeguiByIdEstadoPlan($idEstadoPlan,$itemplan) {
        $sql = " SELECT m.* 
                   FROM motivo_seguimiento_cv m
                  WHERE m.flg_automatico = 0
                    AND m.idEstadoPlan = ?
                    AND NOT EXISTS ( SELECT ls.id_motivo_seguimiento
									   FROM log_seguimiento_cv ls
									  WHERE ls.itemplan = ?
										AND ls.id_motivo_seguimiento = m.id
								    ) ";
        $result = $this->db->query($sql, array($idEstadoPlan,$itemplan));
        return $result->result();
    }
	
	function getEvaluacionLicenciasPorEstacionAncla($itemplan) {
        $sql = " SELECT tb1.*,
                        d.liquido_licencia
                   FROM (
                            SELECT po.itemplan,
                                   eie.idEstacion,
                                   COUNT(*) AS total_licencias,
                                   SUM(CASE WHEN eie.estado = 2 THEN 1 ELSE 0 END) AS total_lic_liqui,
                                   SUM(CASE WHEN eie.estado = 2 AND eie.idEntidad IN (2,6) AND eie.idTipoEntidad = 2 THEN 1 ELSE 0 END) lic_liqui_MD_MP
                              FROM entidad_itemplan_estacion eie,
                                   planobra po
                             WHERE eie.itemplan = po.itemplan
                               AND po.itemplan = ?
                          GROUP BY 1,2) tb1
               LEFT JOIN diseno d ON tb1.itemplan = d.itemplan AND tb1.idEstacion = d.idEstacion ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result();
    }

    public function updateDiseno($itemplan,$idEstacion,$arrayData) {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;

        try {
            $this->db->where('itemplan', $itemplan);
            $this->db->where('idEstacion', $idEstacion);
            $this->db->update('diseno', $arrayData);
            if ($this->db->trans_status() === false) {
                throw new Exception('Hubo un error al actualizar la tabla diseno.');
            } else {
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!';
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }
	
	public function countMatPoAprobConVR($itemplan) {
        $sql = " SELECT COUNT(*) AS cantidad
		           FROM planobra_po
                  WHERE flg_tipo_area = 1
                    AND idEstacion IN (2,5,15,16)
                    AND estado_po = 3
				    AND itemplan = ? ";
        $result = $this->db->query($sql,array($itemplan));
        return $result->row()->cantidad;
    }
	
	function getCountPo($itemplan, $idEstacion, $area = 1) {
        $sql = "SELECT COUNT(1) count 
		          FROM planobra_po 
				 WHERE itemplan   = COALESCE(?, itemplan) 
				   AND idEstacion = COALESCE(?, idEstacion)
				   AND flg_tipo_area = COALESCE(?, flg_tipo_area)
				   AND estado_po NOT IN (7,8)";
        $result = $this->db->query($sql, array($itemplan, $idEstacion, $area));
        return $result->row_array()['count'];
    }

    function getPorcentajeByItemplanAndEstacion($itemplan, $idEstacion) {
        $sql = "SELECT COUNT(1) count
				  FROM itemplanestacionavance
                 WHERE idEstacion = ?
                   AND itemplan   = ?
                   AND porcentaje = 100 ";
        $result = $this->db->query($sql, array($idEstacion, $itemplan));
        return $result->row_array()['count'];
    }
	
	function getEstacionesByItemplan($itemplan) {
        $sql = "     SELECT tb1.*,
                            COALESCE(iea.porcentaje,0) porcentaje,
                            iea.fecha,
                            iea.idUsuarioLog,
                            iea.comentario,
                            iea.ruta_evidencia,
                            iea.path_pdf_pruebas,
                            iea.path_pdf_perfil
                       FROM 			
                            (
                                  SELECT po.itemplan,
                                         po.idEstadoPlan,
                                         po.idSubProyecto,
                                         sae.idEstacion,
                                         e.estacionDesc,
                                         sp.idTipoSubProyecto
                                    FROM planobra po,
                                         subproyecto sp,
                                         subproyecto_area_estacion sae,
                                         estacion e
                                   WHERE po.idSubProyecto = sp.idSubProyecto
                                     AND sp.idSubProyecto = sae.idSubProyecto
                                     AND sae.idEstacion = e.idEstacion
                                     AND po.itemplan = ?
                                GROUP BY sae.idEstacion 
                            ) tb1
                  LEFT JOIN itemplanestacionavance iea ON tb1.itemplan = iea.itemplan AND tb1.idEstacion = iea.idEstacion ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function getPorcentajeLiqui() {
        $sql = " SELECT id,
                        porcentaje 
                   FROM porcentaje_liqui";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getPorcentajeAvanceByItemplanEstacion($itemplan, $idEstacion) {
        $sql = "   SELECT i.*
		             FROM itemplanestacionavance i,
					      planobra po
                    WHERE i.itemplan = ?
                      AND i.idEstacion = ?
					  AND po.itemplan = i.itemplan
                    LIMIT 1 ";
        $result = $this->db->query($sql, array($itemplan, $idEstacion));
        return $result->row_array();
    }

    function getAllPoByItemplanEstacion($itemplan, $idEstacion) {
        $sql = " SELECT *
                   FROM planobra_po
                  WHERE	idEstacion = ?
                    AND itemplan   = ?
                    AND flg_tipo_area IN (1,2)
                    AND CASE WHEN flg_tipo_area = 2 THEN isPoPqt = 1
                             ELSE TRUE END
                    AND CASE WHEN flg_tipo_area = 2 THEN estado_po = 1
                             WHEN flg_tipo_area = 1 THEN estado_po = 3 END";
        $result = $this->db->query($sql,array($idEstacion, $itemplan));
        return $result->result();
    }

    function updateBatchPo($updateDataPo, $insertDataLog){
	    $data['error'] = EXIT_ERROR;
	    $data['msj'] = null;
	    try{   
            $this->db->update_batch('planobra_po', $updateDataPo, 'codigo_po');
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Hubo un error al update planobra_po.');
            }else{	               
                $this->db->insert_batch('log_planobra_po', $insertDataLog);    
                if($this->db->trans_status() === FALSE) {
                    throw new Exception('Error al insertar en log_planobra_po');
                }else{
                    $data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se actualizó correctamente!!';
                }
            }
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function getCountItemplanAptopLiquida($itemplan, $idEstacion) {
		$sql = "SELECT COUNT(1) count_vr
				  FROM planobra_po ppo
				 WHERE flg_tipo_area = 1
				   AND idEstacion = (CASE WHEN ? = 2 THEN 5
										  WHEN ? = 5 THEN 2 END)
				   AND estado_po IN (3,4,5,6)						 
				   AND ppo.itemplan = ?
				   AND (SELECT COUNT(1)
						  FROM itemplanestacionavance i
						 WHERE i.itemplan = ppo.itemplan
						   AND ppo.idEstacion = i.idEstacion
						   AND porcentaje <> 100) = 0";
		$result = $this->db->query($sql, array($idEstacion, $idEstacion, $itemplan));
        return $result->row_array()['count_vr'];
	}
	
	function getVericaPo($itemplan, $arrayIdEsta) {
        $sql = "      SELECT ppo.itemplan,
                             SUM(CASE WHEN ppo.flg_tipo_area = 1 THEN ( SELECT COUNT(1)
                                                                          FROM solicitud_vale_reserva
                                                                         WHERE estado = 0
                                                                           AND itemplan = ppo.itemplan
                                                                           AND codigo_po = ppo.codigo_po
                                                                       ) 
                                      ELSE 0 END) numVrPdt,
                             SUM(CASE WHEN ppo.flg_tipo_area = 2 AND ppo.estado_po IN (1,2,3) THEN 1 ELSE 0 END) numPoNoLiqui,
                             SUM(CASE WHEN ppo.flg_tipo_area = 1 AND ppo.estado_po IN (1,2) THEN 1 ELSE 0 END) numPoMatPdtAnular
                        FROM planobra_po ppo
                       WHERE ppo.itemplan  = ?
                         AND ppo.estado_po NOT IN (7,8)
                         AND ppo.idEstacion IN ?
                    GROUP BY 1 ";
        $result = $this->db->query($sql, array($itemplan, $arrayIdEsta));
        return $result->row_array();
    }

    function registrarLogLicencia($dataLog) {
        $data['error'] = EXIT_ERROR;
	    $data['msj'] = null;
	    try{
            $this->db->insert('log_entidad_itemplan_estacion', $dataLog);
            if($this->db->affected_rows() <= 0) {
                $data['msj'] = 'No se registró el log en la tabla log_entidad_itemplan_estacion';
                $data['error'] = EXIT_ERROR;
            } else {
                $data['msj'] = 'Se registró correctamente';
                $data['error'] = EXIT_SUCCESS;   
            }
        }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
    }

    public function eliminarEntidadItemplanEstacionById($id)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $this->db->where('id', $id);
            $this->db->delete('entidad_itemplan_estacion');

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Hubo un error al eliminar en la tabla entidad_itemplan_estacion!!');
            } else {
				$data['error'] = EXIT_SUCCESS;
				$data['msj'] = 'Se eliminó correctamente!';
            }

        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }

        return $data;
    }
	
	function getAllEstadoPlan() {
        $sql = "   SELECT * 
                     FROM estadoplan
                    WHERE estado = 1
                 ORDER BY estadoPlanDesc";
        $result = $this->db->query($sql);
        return $result->result();
    }

    function evaluarPaseTerminadoItemplan($itemplan) {
        $sql = "SELECT fn_evaluarObraPaseTerminado(?) AS beTerminado";
        $result = $this->db->query($sql, array($itemplan));
        return $result->row()->beTerminado;
    }
	
	function getInfoItemToSendSiropeEjecucionDiseno($idEstacion, $itemplan) {
        $sql = "  SELECT po.itemplan,
                         DATE(po.fechaInicio) fechaInicio,
                         j.jefaturaDesc,
                         DATE_FORMAT(d.fecha_prevista_atencion, '%Y-%m-%d') AS fecha_prevista
                    FROM planobra po,
                         central c,
                         diseno d,
                         jefatura j
                   WHERE po.idCentral = c.idCentral
                     AND c.idJefatura = j.idJefatura
                     AND d.itemplan = po.itemplan
                     AND d.idEstacion = ?
                     AND po.itemplan = ? ";
        $result = $this->db->query($sql, array($idEstacion, $itemplan));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    function getDataItemplanByCodOcEdicion($codigoSolicitudEdic) {
        $sql = "SELECT itemplan, 
		               idEstadoPlan
                  FROM planobra 
                 WHERE solicitud_oc_dev = ?";
        $result = $this->db->query($sql, array($codigoSolicitudEdic));
        return $result->row_array();         
    }

    function getDataItemplanByCodOcEdicionDiseno($codigoSolicitudEdic) {
        $sql = "SELECT itemplan, 
                       idEstadoPlan
                  FROM planobra 
                 WHERE solicitud_oc_dev_diseno = ?";
        $result = $this->db->query($sql, array($codigoSolicitudEdic));
        return $result->row_array();         
    }
	
	function getDataItemplanByCodOcAnula($codigoSolicitudEdic) {
        $sql = "SELECT itemplan, 
		               idEstadoPlan
                  FROM planobra 
                 WHERE solicitud_oc_anula_pos = ?";
        $result = $this->db->query($sql, array($codigoSolicitudEdic));
        return $result->row_array();         
    }

    function getPartidasAdicIntegral($itemplan) {
        $sql = "  SELECT pa.codigoPartida,pa.descripcion AS nomPartida, pepi.precio, 1 AS baremo
                    FROM partida pa,
                         partida_x_subproyecto_integral pasp,
                         precio_x_eecc_x_partida_integral pepi,
                         planobra po
                   WHERE pa.codigoPartida = pasp.codigoPartida
                     AND pa.codigoPartida = pepi.codigoPartida
                     AND pa.flg_tipo = 4
                     AND pasp.idSubProyecto = po.idSubProyecto
                     AND pepi.idEmpresaColab = po.idEmpresaColab
                     AND po.itemplan = ? ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    public function insertLogPartidasAdicIntegral($codigoPO,$idUsuario)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try {
            $sql = " INSERT INTO log_edit_partida_adic_integral
                          SELECT null AS id,
                                 podm.*, 
                                 ? AS id_usuario_reg,
                                 NOW() AS fecha_registro
                            FROM planobra_po_detalle_mo podm
                           WHERE podm.codigo_po = ? ";
            $result = $this->db->query($sql, array($idUsuario,$codigoPO));
            if ($this->db->affected_rows() <= 0) {
                throw new Exception('Hubo un error al insertar en la tabla log_edit_partida_adic_integral!!');
            } else {
				$data['error'] = EXIT_SUCCESS;
				$data['msj'] = 'Se insertó correctamente!';
            }
        } catch (Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }

    function getInfoOtsCreadosONoSirope($itemplan) {
	    $sql = "   SELECT itemplan, 
                          SUM(CASE WHEN codigo_ot LIKE '%FO%' THEN 1 ELSE 0 END) has_fo, 
                          SUM(CASE WHEN codigo_ot LIKE '%COAX%' THEN 1 ELSE 0 END) has_coax, 
                          SUM(CASE WHEN codigo_ot LIKE '%AC%' THEN 1 ELSE 0 END) has_ac,
                          SUM(CASE WHEN codigo_ot LIKE '%ITF%' THEN 1 ELSE 0 END) has_itf
                     FROM log_tramas_sirope 
                    WHERE itemplan = ?
                      AND estado IN (1,2,3)
                 GROUP BY itemplan ";
	    $result = $this->db->query($sql, array($itemplan));
        return $result->row_array();
	}
	
	function updateCostoTotalPoMat($codigoPO){
	    $data['error'] = EXIT_ERROR;
	    $data['msj'] = null;
	    try{ 
            $sql = " UPDATE planobra_po ppo,
                            (    SELECT pd.codigo_po, ROUND(SUM(pd.cantidadFinal*pd.costoMat),2) total
                                   FROM planobra_po_detalle_mat pd
								  WHERE codigo_po = ?
                               GROUP BY codigo_po 
                            ) t
                        SET ppo.costo_total = t.total
                      WHERE ppo.codigo_po = t.codigo_po 
                        AND t.codigo_po = ? ";
            $this->db->query($sql, array($codigoPO,$codigoPO)); 
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Hubo un error al actualizar el costo total de la po');
            }else{	               
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó correctamente!!';
            }
	    }catch(Exception $e){
	        $data['msj'] = $e->getMessage();
	    }
	    return $data;
	}

    function evaluarNewEstadoSolVr($codigoSolVr) {
        $sql = " SELECT fn_evaluarNewEstadoSolVr(?) AS newEstado";
        $result = $this->db->query($sql, array($codigoSolVr));
        return $result->row()->newEstado;
    }
	
	function getMotivoCancelaIP() {
        $sql = " SELECT * FROM motivo ORDER BY motivoDesc ";
        $result = $this->db->query($sql);
        return $result->result();         
    }

    function fnCancelarPOByItemplan($itemplan, $idUsuario) {
        $sql = "SELECT fn_cancelarPOByItemplan(?, ?) AS rpta;";
        $result = $this->db->query($sql, array($itemplan, $idUsuario));
        return $result->row_array()['rpta'];
    }
	
	function getInfoSolicitudOCByIP($itemplan) {
	    $sql = " SELECT ixs.itemplan, 
                        (CASE WHEN soc.tipo_solicitud = 1 AND soc.estado IN (2) THEN soc.orden_compra ELSE null END) AS orden_compra, 
                        SUM(CASE WHEN soc.tipo_solicitud = 1 AND soc.estado IN (1,2) THEN 1 ELSE 0 END) AS has_sol_creacion, 
                        SUM(CASE WHEN soc.tipo_solicitud = 1 AND soc.estado = 1 THEN 1 ELSE 0 END) AS has_sol_creacion_pdt,
                        SUM(CASE WHEN soc.tipo_solicitud = 1 AND soc.estado = 2 THEN 1 ELSE 0 END) AS has_sol_creacion_aten,
                        SUM(CASE WHEN soc.tipo_solicitud = 2 AND soc.estado IN (1,2) THEN 1 ELSE 0 END) AS has_sol_edicion,
                        SUM(CASE WHEN soc.tipo_solicitud = 2 AND soc.estado = 1 THEN 1 ELSE 0 END) AS has_sol_edicion_pdt,
                        SUM(CASE WHEN soc.tipo_solicitud = 2 AND soc.estado = 2 THEN 1 ELSE 0 END) AS has_sol_edicion_aten,
                        SUM(CASE WHEN soc.tipo_solicitud = 4 AND soc.estado IN (1,2) THEN 1 ELSE 0 END) AS has_sol_anulacion,
                        SUM(CASE WHEN soc.tipo_solicitud = 4 AND soc.estado IN (1) THEN 1 ELSE 0 END) AS has_sol_anulacion_pdt,
                        SUM(CASE WHEN soc.tipo_solicitud = 4 AND soc.estado IN (2) THEN 1 ELSE 0 END) AS has_sol_anulacion_aten,
                        SUM(CASE WHEN soc.tipo_solicitud = 3 AND soc.estado IN (1,2,4,5) THEN 1 ELSE 0 END) AS has_sol_certificacion,
                        SUM(CASE WHEN soc.tipo_solicitud = 3 AND soc.estado IN (1,4,5) THEN 1 ELSE 0 END) 	AS has_sol_certificacion_pdt,
                        SUM(CASE WHEN soc.tipo_solicitud = 3 AND soc.estado = 2 THEN 1 ELSE 0 END) 		AS has_sol_certificacion_aten
                   FROM planobra po, 
                        itemplan_x_solicitud_oc ixs, 
                        solicitud_orden_compra soc 
                  WHERE ixs.codigo_solicitud_oc = soc.codigo_solicitud
                    AND po.itemplan = ixs.itemplan
                    AND ixs.itemplan = ?
                    AND (CASE WHEN po.orden_compra IS NOT NULL THEN po.orden_compra = soc.orden_compra ELSE TRUE END)
               GROUP BY ixs.itemplan, orden_compra
                  LIMIT 1 ";
	    $result = $this->db->query($sql, array($itemplan));
	    if($result->num_rows() > 0) {
            return $result->row_array();
        } else {
            return null;
        }
	}
	
	function fnCreatePartidasIntegralesByItemplan($itemplan) {
        $sql = "SELECT createPartidasIntegralesByItemplan(?) AS rpta ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->row_array()['rpta'];
    }
	
		function getInfoSiropeByItemplan($itemplan) {
	    $Query = "SELECT 
                        po.itemplan, po.idSubProyecto,  po.ult_estado_sirope,
                        po.has_sirope_diseno, po.has_sirope_diseno_fecha,
                        po.has_sirope_ac, po.fecha_sirope_ac, po.has_sirope_ac_diseno, po.fecha_sirope_ac_diseno,
                        lt1.codigo_ot as ot_prin, lt2.codigo_ot as ot_ac, lt3.codigo_ot as ot_coax,
                        po.has_sirope_coax, po.ult_estado_sirope_coax, po.ult_codigo_sirope_coax, 
                        lt4.codigo_ot as ot_mn
                    FROM
                        planobra po
                    	LEFT JOIN log_tramas_sirope lt1 on lt1.codigo_ot = CONCAT(po.itemplan,'FO')   and lt1.estado = 1
                        LEFT JOIN log_tramas_sirope lt2 on lt2.codigo_ot = CONCAT(po.itemplan,'AC')   and lt2.estado = 1
                        LEFT JOIN log_tramas_sirope lt3 on lt3.codigo_ot = CONCAT(po.itemplan,'COAX') and lt3.estado = 1
						LEFT JOIN log_tramas_sirope lt4 on lt4.codigo_ot = CONCAT(po.itemplan,'MN')   and lt4.estado = 1
                    WHERE
                        po.itemplan = ?
                        LIMIT 1";
	    $result = $this->db->query($Query, array($itemplan));
	    if ($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function generarItemMadre() {
        $sql = "     SELECT CONCAT('M-EXT-',
                            DATE_FORMAT(CURDATE(),'%y'),'-',
                            CASE WHEN LENGTH(MONTH(NOW())) = 1 THEN CONCAT('0',MONTH(NOW()))
                                ELSE MONTH(NOW()) END,
                                CASE WHEN LENGTH( 
                                                    (  SELECT COUNT(1) 
                                                        FROM itemplan_madre
                                                        WHERE MONTH(fechaRegistro) = MONTH(NOW())
                                                    )
                                                ) IN (0,1) THEN '-00'
                                        WHEN LENGTH( 
                                                    (  SELECT COUNT(1) 
                                                        FROM itemplan_madre
                                                        WHERE MONTH(fechaRegistro) = MONTH(NOW())
                                                    )
                                                    
                                                    )  = 2 THEN '-0'
                                                    
                                        WHEN	LENGTH( 
                                                    (  SELECT COUNT(1) 
                                                        FROM itemplan_madre
                                                        WHERE MONTH(fechaRegistro) = MONTH(NOW())
                                                    )
                                                    
                                                    ) = 3 THEN '-' END,
                                    (
                                        SELECT COUNT(1) 
                                        FROM itemplan_madre
                                        WHERE MONTH(fechaRegistro) = MONTH(NOW())
                                    )
                            ) as itemplan_madre";
        $result = $this->db->query($sql);
        return $result->row_array()['itemplan_madre'];
    }

    function getPreciarioPqt($idEmpresaColab, $jefatura, $tipoPreciario) {
        $sql = "  SELECT * 
                    FROM pqt_preciario pr
                   WHERE pr.idEmpresaColab = ?
                     AND tipoJefatura  = CASE WHEN 13 = ? THEN 1 ELSE 2 END
                     AND idTipoPreciario = ?";
        $result = $this->db->query($sql, array($idEmpresaColab, $jefatura, $tipoPreciario));
        return $result->row_array();
    }

    function getCentralByIdCodigo($idCentral, $codigo) {
        $sql = "  SELECT *
                    FROM central
                   WHERE idCentral = COALESCE(?, idCentral)
                     AND codigo    = COALESCE(?, codigo)";
        $result = $this->db->query($sql, array($idCentral, $codigo));
        return $result->row_array();
    }

    function registrarItemplanMadre($dataInsert) {
        $this->db->insert('itemplan_madre', $dataInsert);

        if($this->db->affected_rows() <= 0) {
            $data['msj'] = "No se ingreso el registro";
            $data['error'] = EXIT_ERROR;
        }else{
            $data['msj'] = "Se registró correctamente el itemplan";
            $data['error'] = EXIT_SUCCESS;
        }

        return $data;
    }

    function generarSolicitudItemPlanMadre($itemplan, $costo_diseno, $idUsuario, $codInvercion) {
        $sql = "SELECT fn_create_solicitud_oc_itemplan_madre(?, ?, ?, ?) AS flgValida";
        $result = $this->db->query($sql, array($itemplan, $costo_diseno, $idUsuario, $codInvercion));
        return $result->row_array()['flgValida'];
    }

    function getItemMadreByCodigo($itemplan_m, $idSubProyecto, $arrayEstadoPlan, $idEmpresaColab) {
        $sql = " SELECT
                        proyectoDesc,
                        subproyectoDesc AS subDesc,
                        i.itemplan_m,
                        DATE_FORMAT(i.fechaRecepcion,'%Y/%m/%d') fecha_registro,
                        i.nombrePlan,
                        i.cliente,
                        i.carta_pdf,
                        eecc.empresaColabDesc,
                        i.solicitud_oc as codigo_solicitud,
                        i.orden_compra,
                        FORMAT( i.costo_mo_final, 2 ) costoEstimado,
                        compra.pep1,
                        es.estadoPlanDesc,
                        es.idEstadoPlan,
                        COALESCE(	
                                    (   SELECT CASE   WHEN tipo_solicitud = 4 AND estado = 1 THEN 'PDT OC ANULADA'
                                                    WHEN tipo_solicitud = 1 AND estado = 1 THEN 'SOLICITUD OC CREADA'
                                                    WHEN (tipo_solicitud = 1 AND estado = 2) OR (tipo_solicitud IN (2,3,4) AND estado = 1) THEN 'OC CREADA'
                                                    WHEN tipo_solicitud = 3 AND estado = 2 THEN 'OC CERTIFICADA'
                                                    WHEN tipo_solicitud = 4 AND estado = 2 THEN 'OC ANULADA' END estado_oc
                                        FROM itemplan_madre_solicitud_orden_compra so,
                                            itemplan_madre_x_solicitud_oc_madre ixs
                                        WHERE ixs.codigo_solicitud_oc = so.codigo_solicitud
                                        AND i.itemplan_m = ixs.itemplan_m
                                        AND so.estado <> 3
                                        AND tipo_solicitud <> 2
                                        AND CASE WHEN i.orden_compra IS NOT NULL THEN so.orden_compra = i.orden_compra
                                                ELSE true END
                                        AND CASE WHEN tipo_solicitud = 3 THEN so.estado = 2 ELSE tipo_solicitud <> 3 END -- SOLO QUE ME TOME EN CUENTA LA CERTIFICADAS APROBADAS
                                        ORDER BY fecha_creacion DESC
                                        limit 1), 'SIN SOLICITUD OC'
                                    ) estado_oc
                     FROM itemplan_madre i
                LEFT JOIN subproyecto s ON s.idSubProyecto = i.idSubProyecto
                LEFT JOIN proyecto p ON p.idProyecto = s.idProyecto
                LEFT JOIN empresacolab eecc ON eecc.idEmpresaColab = i.idEmpresaColab
                LEFT JOIN estadoplan es ON es.idEstadoPlan = i.idEstado
                LEFT JOIN itemplan_madre_solicitud_orden_compra compra ON compra.codigo_solicitud = i.solicitud_oc
                    WHERE i.itemplan_m = COALESCE(?, i.itemplan_m)
                      AND i.idSubProyecto = COALESCE(?, i.idSubProyecto)
                      AND CASE WHEN ? IN (0,6) THEN true 
                               ELSE i.idEmpresaColab = COALESCE(?, i.idEmpresaColab) END
                      AND CASE WHEN 0 IN ? THEN true 
                               ELSE i.idEstado IN ? END
                            ORDER BY i.fechaRegistro DESC";
        $result = $this->db->query($sql, array($itemplan_m, $idSubProyecto, $idEmpresaColab, $idEmpresaColab, $arrayEstadoPlan, $arrayEstadoPlan));
        log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    function getItemplanMadreList() {
        $sql = "SELECT i.itemplan_m, (select count(1) from planobra where itemplan_m = i.itemplan_m and idEstadoPlan not in (6)) as num_hijos, i.orden_compra, i.nombrePlan, p.proyectoDesc, s.subProyectoDesc, i.cantidad_uip, i.costo_mo_final, eecc.empresaColabDesc, i.fechaRegistro, i.codigoInversion, es.estadoPlanDesc 
                FROM itemplan_madre i, subproyecto s, proyecto p, empresacolab eecc, estadoplan es
                WHERE s.idSubProyecto = i.idSubProyecto
                AND	p.idProyecto = s.idProyecto
                AND	eecc.idEmpresaColab = i.idEmpresaColab
                AND	es.idEstadoPlan = i.idEstado
                ORDER BY i.fechaRegistro DESC";
        $result = $this->db->query($sql);
        //log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    function cantidadItemplanHijos($Itemplan) {
        $Query = 'SELECT COUNT(*) total 
                    FROM planobra 
                   WHERE itemplan_m = ?';
        $result = $this->db->query($Query, array($Itemplan));
        $idEstadoPlan = $result->row()->total;
        return $idEstadoPlan;
    }

    function getDataItemplanMadre($idProyecto, $idSubProyecto, $idEstado) {
        $sql = "  SELECT * 
                    FROM itemplan_madre
                   WHERE idProyecto    = COALESCE(?, idProyecto)
                     AND idSubProyecto = COALESCE(?, idSubProyecto)
                     AND idEstado      = COALESCE(?, idEstado)";
        $result = $this->db->query($sql, array($idProyecto, $idSubProyecto, $idEstado));
        return $result->result_array();
    }

    function getDataItemplanMadreRefo() {
        $sql = "select itemplan from planobra where idEstadoPlan IN (1,3) and idSubProyecto = 747";
        $result = $this->db->query($sql, array());
        return $result->result_array();
    }

    function getEmpresaElectricaAll() {
        $sql = "  SELECT idEmpresaElec,
                         empresaElecDesc
                    FROM empresaelectrica
                   WHERE estado = 1";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getFactorDeMedicionXIdSubProyecto($idSubProyecto) {
        $query = "SELECT fm.idPqtTipoFactorMedicion, 
                         fm.descPqtTipoFactorMedicion
                    FROM subproyecto s, 
                         pqt_tipo_factor_medicion fm 
                   WHERE s.idSubProyecto = ?
                     AND s.idPqtTipoFactorMedicion = fm.idPqtTipoFactorMedicion;";
        $result = $this->db->query($query, array($idSubProyecto));
        return $result->row_array();
    }

    function getTipoRequerimientoAll($idTipoReq) {
        $query = "  SELECT id_tipo_requerimiento,
                           requerimientoDesc,
                           sla
                      FROM cap_tipo_requerimiento
                     WHERE id_tipo_requerimiento = COALESCE(?, id_tipo_requerimiento)
                       AND estado = ".ESTADO_CONFIG_ACTIVO;
        $result = $this->db->query($query, array($idTipoReq));
        return $result->result_array();
    }

    function getTipoRequerimientoById($idTipoReq) {
        $query = "  SELECT id_tipo_requerimiento,
                           requerimientoDesc,
                           sla
                      FROM cap_tipo_requerimiento
                     WHERE id_tipo_requerimiento = COALESCE(?, id_tipo_requerimiento)
                       AND estado = ".ESTADO_CONFIG_ACTIVO;
        $result = $this->db->query($query, array($idTipoReq));
        return $result->row_array();
    }

    function getTipoProyectoAll($idTipoProy) {
        $query = "  SELECT id_tipo_proyecto,
                           tipoProyectoDesc,
                           estado
                      FROM cap_tipo_proyecto
                     WHERE id_tipo_proyecto = COALESCE(?, id_tipo_proyecto)
                       AND estado = ".ESTADO_CONFIG_ACTIVO;
        $result = $this->db->query($query, array($idTipoProy));
        return $result->result_array();
    }

    function getAreaReqAll($idAreaReq) {
        $query = " SELECT id_area_requerimiento,
                          areaRequeDesc,
                          estado
                    FROM cap_area_requerimiento
                   WHERE id_area_requerimiento = COALESCE(?, id_area_requerimiento)
                     AND estado = ".ESTADO_CONFIG_ACTIVO;
        $result = $this->db->query($query, array($idAreaReq));
        return $result->result_array();
    }

    function registrarRequerimientoCap($dataArray) {
        $this->db->insert('cap_requerimiento', $dataArray);
        if($this->db->affected_rows() <= 0) {
            $data['msj'] = "No se ingreso el registro";
            $data['error'] = EXIT_ERROR;
        }else{
            $data['msj'] = "Se registró correctamente el itemplan";
            $data['error'] = EXIT_SUCCESS;
        }

        return $data;
    }

    function getMotivoReqByAreaReq($idAreaReq) {
        $query = " SELECT m.id_motivo_requerimiento, 
                          m.motivoDesc
                     FROM cap_area_req_x_motivo c,
                          cap_motivo_requerimiento m
                    WHERE m.id_motivo_requerimiento = c.id_motivo_requerimiento
                      AND c.id_area_requerimiento = ?
                      AND estado = ".ESTADO_CONFIG_ACTIVO;
        $result = $this->db->query($query, array($idAreaReq));
        return $result->result_array();
    }

    function getCodigoRequerimiento() {
        $sql = "SELECT fn_get_codigo_requerimiento() as codigo";
        $result = $this->db->query($sql);
        return $result->row_array()['codigo'];
    }

    function getResponsableCapByMotivo($idMotivo) {
        $query = "  SELECT m.idUsuario,
                           u.nombre_completo
                      FROM cap_motivo_requerimiento m,
                           usuario u
                     WHERE m.idUsuario = u.id_usuario
                       AND m.id_motivo_requerimiento = ?";
        $result = $this->db->query($query, array($idMotivo));
        return $result->row_array();
    }

    function getTipoEstadoAll() {
        $query = " SELECT id_estado_requerimiento,
		                  estadoDesc
	                 FROM cap_estado_requerimiento";
        $result = $this->db->query($query);
        return $result->result_array();
    }

    function getInfoRequerimiento($id_area_requerimiento)
    {
        $query = "SELECT t.*,
                        CASE WHEN t.sla < dias_transcurridos AND t.id_estado_requerimiento = ".CAP_ESTADO_PENDIENTE." THEN 'red' 
                              ELSE '' END color_sla,
                        CASE WHEN t.sla < dias_transcurridos THEN 'NO'
                             ELSE 'SI' END rsp_sla
                    FROM (
                             SELECT t1.codigo_requerimiento,
                                    t1.fecha_registro,
                                    -- DATE_FORMAT(t1.fecha_registro, '%d-%m-%Y') AS fecha_registro,
                                    t2.requerimientoDesc,
                                    t3.tipoProyectoDesc,
                                    t4.areaRequeDesc,
                                    '' AS motivoDesc,
                                    t1.idUsuarioValida,
                                    t1.comentario_valida,
                                    t1.fecha_valida,
                                    CASE WHEN t1.id_estado_requerimiento = ".CAP_ESTADO_PENDIENTE." 
                                        THEN DATEDIFF(NOW(), t1.fecha_registro)
                                        WHEN t1.id_estado_requerimiento IN (".CAP_ESTADO_ATENDIDO.", ".CAP_ESTADO_RECHAZADO.") 
                                        THEN DATEDIFF(fecha_valida, t1.fecha_registro) END dias_transcurridos,
                                    t1.horas_esfuerzo,
                                    t1.id_estado_requerimiento,
                                    t1.sla,
                                    t5.estadoDesc,
                                    t6.nombre_completo,
                                    t1.comentario_registro
                              FROM cap_requerimiento t1
                              JOIN cap_tipo_requerimiento t2    ON t1.id_tipo_requerimiento   = t2.id_tipo_requerimiento
                              JOIN cap_tipo_proyecto t3         ON t1.id_tipo_proyecto        = t3.id_tipo_proyecto
                              JOIN cap_area_requerimiento t4    ON t1.id_area_requerimiento   = t4.id_area_requerimiento
                              JOIN cap_estado_requerimiento t5  ON t1.id_estado_requerimiento = t5.id_estado_requerimiento
                         LEFT JOIN usuario t6                   ON t1.idUsuarioValida         = t6.id_usuario
                             WHERE t1.id_area_requerimiento = COALESCE(?, t1.id_area_requerimiento)
                         )t ";
        $result = $this->db->query($query, array($id_area_requerimiento));
        return $result->result_array();
    }

    function getEstadoPlanByItemplan($itemplan) {
        $Query = "SELECT idEstadoPlan 
		            FROM planobra 
				   WHERE itemplan = ?";
        $result = $this->db->query($Query, array($itemplan));
        if ($result->row() != null) {
            return $result->row_array()['idEstadoPlan'];
        } else {
            return null;
        }
    }

    function getAllByItemplanFull($itemplan) {
        $query = "SELECT po.itemplan,
                        po.nombrePlan,
                        po.longitud,
                        po.latitud,
                        po.costo_unitario_mo,
                        po.fechaInicio,
                        po.fechaPrevEjecucion,
                        f.faseDesc,
                        po.codigoInversion,
                        e.empresaColabDesc,
                        z.zonalDesc,
                        es.estadoPlanDesc,
                        s.subproyectoDesc,
                        e.idEmpresaColab,
                        po.idEstadoPlan,
                        es.class_color,
                        s.idSubProyecto,
                        s.idProyecto,
                        ( SELECT COUNT(1) count 
                            FROM itemplanestacionavance i
                        WHERE i.itemplan = po.itemplan
                            AND porcentaje = 100) count_porcentaje,
                        ( SELECT ruta_evidencia
                            FROM itemplanestacionavance i
                        WHERE i.itemplan = po.itemplan) ruta_evidencia,
                        po.flg_update_mat,
                        p.proyectoDesc,
                        po.ult_codigo_sirope,
                        po.ult_estado_sirope,
                        po.has_sirope_coax, 
                        po.ult_estado_sirope_coax, 
                        po.ult_codigo_sirope_coax,
                        po.orden_compra
                FROM planobra po,
                        empresacolab e,
                        zonal z,
                        fase f,
                        estadoplan es,
                        subproyecto s,
                        proyecto p
                WHERE p.idProyecto     = s.idProyecto
                    AND po.itemplan      = ?

                    AND s.idSubProyecto  = po.idSubProyecto
                    AND e.idEmpresaColab = po.idEmpresaColab
                    AND z.idZonal 		 = po.idZonal
                    AND f.idFase  		 = po.idFase
                    AND es.idEstadoPlan  = po.idEstadoPlan";
        $result = $this->db->query($query, array($itemplan));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }
    
    function getEstacionesToLicenciaByItemplan($itemplan){
        $query = "SELECT e.estacionDesc,
                        sa.idEstacion,
                        GROUP_CONCAT(DISTINCT sa.idArea) as arrayIdArea,
						GROUP_CONCAT(DISTINCT a.tipoArea) as arrayTipoArea,
                        GROUP_CONCAT(DISTINCT a.areaDesc) as arrayAreaDesc
                   FROM (subproyecto_area_estacion sa,
                        area a,
                        estacion e,
                        diseno d,
                        planobra po)			  
                  WHERE sa.idArea = a.idArea
                    AND e.idEstacion = sa.idEstacion
                    AND po.idSubProyecto = sa.idSubProyecto
                    and po.itemplan = d.itemplan
                    AND d.idEstacion = e.idEstacion
                    AND po.itemplan = ?
                GROUP BY sa.idEstacion";

        $result = $this->db->query($query, array($itemplan));
        return $result->result_array();
    }

    /**
     * $tipo_partidas  ->   1   =   SOLO DISENO,2   =   TODO MENOS DISENO,3   =  TODAS LAS PARTIDAS
     * 
     */
	 /*
    function getPartidasToPoPqtByTipo($idEstacion, $itemplan, $tipo_partidas){
        $query = "SELECT 
                    tb.id_tipo_partida,
                    tb.tipoPreciario,
                    tb.partidaPqt,
                    tb.baremo,
                    tb.idEmpresaColab,
                    tb.idZonal,
                    tb.cantFactorPlanificado,
                    tb.costo,
                    (tb.baremo * tb.cantFactorPlanificado * tb.costo) AS total,
                                        FORMAT((tb.baremo * tb.cantFactorPlanificado * tb.costo),2) as form,
                                        ROUND((tb.baremo * tb.cantFactorPlanificado * tb.costo),2) as round,
                    tb.codigoPartida
                    FROM (
                            SELECT po.idEmpresaColab, 
                                po.idZonal,
                                pqe.id_tipo_partida,
                                ptp.descripcion as tipoPreciario,
                                pqe.descripcion as partidaPqt,
                                pbs.baremo as baremo,
                                (CASE WHEN sp.idTipoSubProyecto = 1 AND po.idSubProyecto NOT IN (663,665) THEN 1 ELSE	po.cantFactorPlanificado END) as cantFactorPlanificado,
                                pre.costo,                 
                                pqe.codigoPartida
                            FROM
                                planobra po,
                                central c,
                                subproyecto sp,
                                pqt_baremo_x_subpro_x_partida_mo pbs,
                                pqt_partidas_paquetizadas_x_estacion pqe,
                                pqt_tipo_preciario ptp,
                                pqt_preciario pre
                            WHERE
                                po.idSubProyecto    = pbs.idSubProyecto
                            AND po.idSubProyecto	= sp.idSubProyecto
                            AND pbs.id_pqt_partida_mo_x_estacion    = pqe.id_tipo_partida
                            AND pqe.id_pqt_tipo_preciario           = ptp.id
                            AND ptp.id              = pre.idTipoPreciario
                            AND	po.idCentral		= c.idCentral
                            AND pqe.idEstacion      = ?
                            AND pbs.idSubProyecto   = po.idSubProyecto
                            AND pre.idEmpresaColab  = po.idEmpresaColab
                            AND pre.tipoJefatura    = (CASE WHEN c.idJefatura = 13 THEN 1 ELSE 2 END)
                            AND po.itemplan         = ?
                            AND CASE WHEN ? = 1 THEN pqe.id_tipo_partida IN (1,5)
                                    WHEN ? = 2 THEN pqe.id_tipo_partida NOT IN (1,5)
                                    ELSE TRUE END
                    ) as tb";

        $result = $this->db->query($query, array($idEstacion, $itemplan, $tipo_partidas, $tipo_partidas));
        return $result->result_array();
    }
	*/
	
	function getPartidasToPoPqtByTipo($idEstacion, $itemplan, $tipo_partidas){
        $query = "SELECT 
                    tb.id_tipo_partida,
                    tb.tipoPreciario,
                    tb.partidaPqt,
                    tb.baremo,
                    tb.idEmpresaColab,
                    tb.idZonal,
                    tb.cantFactorPlanificado,
                    tb.costo,
                    (tb.baremo * tb.cantFactorPlanificado * tb.costo) AS total,
                                        FORMAT((tb.baremo * tb.cantFactorPlanificado * tb.costo),2) as form,
                                        ROUND((tb.baremo * tb.cantFactorPlanificado * tb.costo),2) as round,
                    tb.codigoPartida
                    FROM (
                            SELECT po.idEmpresaColab, 
                                po.idZonal,
                                pqe.id_tipo_partida,
                                ptp.descripcion as tipoPreciario,
                                pqe.descripcion as partidaPqt,
                                (CASE  WHEN po.idSubProyecto IN (750,751) THEN  (CASE WHEN po.cantFactorPlanificado BETWEEN 0 AND 10 THEN pbs.baremo
                                                                                    WHEN po.cantFactorPlanificado BETWEEN 11 AND 25 THEN pbs.baremo_cv_11_25
                                                                                    WHEN po.cantFactorPlanificado > 25 THEN (((po.cantFactorPlanificado - 25) * pbs.baremo_cv_dpto_adic) + pbs.baremo_cv_11_25) ELSE 0 END)
                                    ELSE pbs.baremo END)  as baremo,
                                (CASE WHEN po.idSubProyecto IN (750,751) THEN 1 ELSE	po.cantFactorPlanificado END) as cantFactorPlanificado,
                                pre.costo,                 
                                pqe.codigoPartida
                            FROM
                                planobra po,
                                central c,
                                subproyecto sp,
                                pqt_baremo_x_subpro_x_partida_mo pbs,
                                pqt_partidas_paquetizadas_x_estacion pqe,
                                pqt_tipo_preciario ptp,
                                pqt_preciario pre
                            WHERE
                                po.idSubProyecto    = pbs.idSubProyecto
                            AND po.idSubProyecto	= sp.idSubProyecto
                            AND pbs.id_pqt_partida_mo_x_estacion    = pqe.id_tipo_partida
                            AND pqe.id_pqt_tipo_preciario           = ptp.id
                            AND ptp.id              = pre.idTipoPreciario
                            AND	po.idCentral		= c.idCentral
                            AND pqe.idEstacion      = ?
                            AND pbs.idSubProyecto   = po.idSubProyecto
                            AND pre.idEmpresaColab  = po.idEmpresaColab
                            AND pre.tipoJefatura    = (CASE WHEN c.idJefatura = 13/*LIMA*/ THEN 1 ELSE 2 END)
                            AND po.itemplan         = ?
                            AND CASE WHEN ? = 1 THEN pqe.id_tipo_partida IN (1,5)
                                    WHEN ? = 2 THEN pqe.id_tipo_partida NOT IN (1,5)
                                    ELSE TRUE END
                    ) as tb";

        $result = $this->db->query($query, array($idEstacion, $itemplan, $tipo_partidas, $tipo_partidas));
        return $result->result_array();
    }
	
    function hasEstacionesAnclasBySubProyecto($idSubProyecto){
	    $sql = " SELECT  COUNT(1) as has_ancla
                FROM subproyecto sp, 
                        subproyecto_area_estacion se
                WHERE sp.idSubProyecto = se.idSubProyecto
                    AND se.idEstacion IN (5,2)
                    AND sp.idSubProyecto = ?";
	    $result = $this->db->query($sql,array($idSubProyecto));
	    if($result->row() != null) {
	        return $result->row_array()['has_ancla'];
	    } else {
	        return null;
	    }
	}    

    function numPoByItemplanEstacion($itemplan, $idEstacion){
	    $sql = "SELECT count(1) total_po 
                from planobra_po 
                where itemplan = ?
                and idEstacion = ?
				and estado_po not in (8)";
	    $result = $this->db->query($sql,array($itemplan, $idEstacion));
	    if($result->row() != null) {
	        return $result->row_array()['total_po'];
	    } else {
	        return null;
	    }
	}    

    function updateUsuario($idUsuario, $dataUpdate){
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try{
            $this->db->where('id_usuario', $idUsuario);           
            $this->db->update('usuario', $dataUpdate);
            if($this->db->trans_status() === FALSE) {
                throw new Exception('Error al modificar a usuario');
            }else{
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizó la contraseña!';
            }
        }catch(Exception $e){
            $data['msj'] = $e->getMessage();
        }
        return $data;
    }
    
    function getAllCentralByTipoCentral($idTipoCentral) {
        $sql = "SELECT * 
                FROM central 
                WHERE idTipoCentral = ?";
        $result = $this->db->query($sql, array($idTipoCentral));
        return $result;
    }

    function inserRechazoLiquidacion($datosInsert){
	    $rpta['error'] = EXIT_ERROR;
	    $rpta['msj']   = null;
	    try{
	        $this->db->trans_begin();
	        $this->db->insert('liquidacion_rechazo',$datosInsert);
	        if($this->db->affected_rows() != 1) {
	            $this->db->trans_rollback();
	            throw new Exception('Error al insertar en liquidacion_rechazo');
	        }else{
	            $this->db->trans_commit();
	            $rpta['error']    = EXIT_SUCCESS;
	            $rpta['msj']      = 'Se agrego correctamente!';
	        }
	    }catch(Exception $e){
	        $rpta['msj'] = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $rpta;
	}

    function hasLiquiRechazadoActivaPIN($itemplan){
	    $sql = "SELECT lr.*, concat(u.nombre_completo, ' ', u.ape_paterno) as usua_rechazo 
                FROM liquidacion_rechazo lr, usuario u 
                WHERE lr.usuario_rechazo = u.id_usuario 
                AND lr.itemplan = ? 
                AND lr.estado = 1";
	    $result = $this->db->query($sql,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}

    function desactivarUltimaLiquiPin($itemplan) {
        $this->db->where('itemplan', $itemplan); 
        $this->db->update('liquidacion_rechazo', array('estado' => 0));
        if($this->db->trans_status() === FALSE) {
            $data['msj'] = 'No se actualizo el detalle';
            $data['error'] = EXIT_ERROR;
        } else {
            $data['msj'] = 'Se actualizo correctamente';
            $data['error'] = EXIT_SUCCESS;
            
        }
        return $data;
    }

    function getLogSeguimientoCVHijo($itemplan_hijo) {
        $sql = "       SELECT ls.itemplan_hijo,
                              ls.itemplan,
                              ls.id_motivo_seguimiento,
                              ms.situacion_general,
                              ms.desc_motivo,
                              ls.usuario_registro,
                              u.nombre_completo,
                              ls.fecha_registro,
                              ls.comentario_incidencia,
                              eps.estadoPlanDesc AS estadoPlanMomentoReg,
                              ep.estadoPlanDesc AS estadoPlanActual,
                              ls.path_file_quiebre
                         FROM log_seguimiento_cv ls
                    LEFT JOIN usuario u ON ls.usuario_registro = u.id_usuario,
                              motivo_seguimiento_cv ms,
                              estadoplan eps,
                              planobra po,
                              estadoplan ep
                        WHERE ls.id_motivo_seguimiento = ms.id
                          AND ls.idEstadoPlan = eps.idEstadoPlan
                          AND ls.itemplan = po.itemplan
                          AND po.idEstadoPlan = ep.idEstadoPlan
                          AND ls.itemplan_hijo = ? 
                          ORDER BY ls.fecha_registro DESC";                        
        $result = $this->db->query($sql, array($itemplan_hijo));
        return $result->result_array();
    }

    function getLogSeguimientoB2bHijo($itemplan) {
        $sql = "    SELECT ls.itemplan_hijo,
                            ls.itemplan,
                            ls.id_motivo_seguimiento,
                            ms.situacion_general,
                            ms.desc_motivo,
                            ls.usuario_registro,
                            u.nombre_completo,
                            ls.fecha_registro,
                            ls.comentario_incidencia,
                            eps.estadoPlanDesc AS estadoPlanMomentoReg,
                            ep.estadoPlanDesc AS estadoPlanActual
                    FROM log_seguimiento_b2b ls
                    LEFT JOIN usuario u ON ls.usuario_registro = u.id_usuario,
                            motivo_seguimiento_b2b ms,
                            estadoplan eps,
                            planobra po,
                            estadoplan ep
                    WHERE ls.id_motivo_seguimiento = ms.id
                        AND ls.idEstadoPlan = eps.idEstadoPlan
                        AND ls.itemplan = po.itemplan
                        AND po.idEstadoPlan = ep.idEstadoPlan
                        AND ls.itemplan = ? 
                    ORDER BY ls.fecha_registro DESC";
        $result = $this->db->query($sql, array($itemplan));
       // log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    function getLogSeguimientoReforzamiento($itemplan) {
        $sql = "     SELECT ls.itemplan,
                            ls.itemplan,
                            ls.id_motivo_seguimiento,
                            ms.situacion_general,                            
                            ls.usuario_registro,
                            concat(u.nombre_completo,' ',u.ape_paterno) as nombre_completo,
                            ls.fecha_registro,
                            ls.comentario_incidencia                            
                    FROM log_seguimiento_reforzamiento ls
                    LEFT JOIN usuario u ON ls.usuario_registro = u.id_usuario,
                            motivo_seguimiento_reforzamiento  ms
                    WHERE ls.id_motivo_seguimiento = ms.id   
                        AND ls.itemplan = ? 
                    ORDER BY ls.fecha_registro DESC";
        $result = $this->db->query($sql, array($itemplan));
        //log_message('error', $this->db->last_query());
        return $result->result_array();
    }

    public function getCodCluster() {
        $sql = "  SELECT (CASE
								WHEN max(id_planobra_cluster)+1 < 10 THEN CONCAT('CL-',FLOOR(100 + RAND() * (999 - 100 + 1)),'00',max(id_planobra_cluster)+1)
								WHEN max(id_planobra_cluster)+1 < 100 THEN CONCAT('CL-',FLOOR(100 + RAND() * (999 - 100 + 1)),'0',max(id_planobra_cluster)+1)
								WHEN max(id_planobra_cluster)+1 < 1000 THEN CONCAT('CL-',FLOOR(100 + RAND() * (999 - 100 + 1)),max(id_planobra_cluster)+1)
								WHEN max(id_planobra_cluster)+1 < 10000 THEN CONCAT('CL-',FLOOR(10 + RAND() * (99 - 10 + 1)),max(id_planobra_cluster)+1)
								WHEN max(id_planobra_cluster)+1 < 100000 THEN CONCAT('CL-',FLOOR(1 + RAND() * (9 - 1 + 1)),max(id_planobra_cluster)+1)
							ELSE CONCAT('CL-',FLOOR(100000 + RAND() * (999999 - 100000 + 1))+1) END) as cod_cluster
					FROM planobra_cluster";
        $result = $this->db->query($sql);
        return $result->row_array()['cod_cluster'];
    }

    function getDataCoordenadasCto() {
        $sql = "SELECT id_t,
					   latitud,
					   longitud,
					   codigo
				  FROM cto_ubicacion";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getCtoByCoordMasCercano($lat, $long) {
        $sql = " 
				  SELECT (atan2(sqrt(a), b))*6371000 as distancia, codigo, latitud, longitud
					FROM (
							SELECT pow(cos(latTo) * sin(lonDelta), 2) +
								   pow(cos(latFrom) * sin(latTo) - sin(latFrom) * cos(latTo) * cos(lonDelta), 2) a,
								   sin(latFrom) * sin(latTo) + cos(latFrom) * cos(latTo) * cos(lonDelta) b,
								   codigo,
								   latitud, longitud
							  FROM (
									SELECT radians(latitud)  AS latFrom,
										   radians(" . $long . ") - radians(longitud) AS lonDelta,
										   radians(" . $long . ") lonTo,
										  
										   radians(" . $lat . ") as  latTo,
										   codigo,
										   latitud, longitud
									  FROM cto_ubicacion
								   )t
						 )tt
					 ORDER BY distancia ASC
					 limit 1";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    
    function getDataCentralPqtById($idCentral) {
        $sql = "SELECT c.idCentral, 
                        c.idTipoCentral, 
                        c.idJefatura,
                        c.idEmpresaColab,
                        c.distrito,
                        c.departamento,
                        c.idZonal,
                        c.codigo,
                        ec.empresaColabDesc,
                        j.jefaturaDesc,
                        c.centralDesc
                FROM 	central c, empresacolab ec, jefatura j
                WHERE 	c.idEmpresaColab = ec.idEmpresaColab
                AND     c.idJefatura = j.idJefatura
                AND 	c.idCentral = ?";
        $result = $this->db->query($sql, array($idCentral));
        return $result->row_array();
    }

    function getAllCentralPqt($idEmpresaColab = NULL) {
        $Query = "  SELECT idCentral ,
		                   CONCAT(codigo,'-',centralDesc) as tipoCentralDesc  
		              FROM central
					 WHERE idTipoCentral IN (1)
					   AND CASE WHEN ? IN (0,6) THEN true 
					            ELSE idEmpresaColab = COALESCE(?, idEmpresaColab) END;";
        $result = $this->db->query($Query, array($idEmpresaColab, $idEmpresaColab));
        return $result;
    }
	
	function getAllCentralPqtCotizacionSisego($idEmpresaColab = NULL) {
        $Query = "  SELECT idCentral ,
		                   CONCAT(codigo,'-',centralDesc) as tipoCentralDesc  
		              FROM central
					 WHERE idTipoCentral IN (1)
					   AND codigo NOT IN (SELECT codigo 
					                        FROM central_pqt_no_cotiza_sisego)
					   AND CASE WHEN ? IN (0,6) THEN true 
					            ELSE idEmpresaColab = COALESCE(?, idEmpresaColab) END;";
        $result = $this->db->query($Query, array($idEmpresaColab, $idEmpresaColab));
        return $result;
    }

    function getDataCentralById($idCentral) {
        $sql = "SELECT c.idCentral, 
                       c.idTipoCentral,                      
                       c.idJefatura,
                       c.flgTipoZona as flg_tipo_zona,
                       c.idEmpresaColab,
                       j.jefaturaDesc as jefatura
                  FROM central c, jefatura j
                 WHERE c.idJefatura = j.idJefatura
                 AND    c.idCentral = ?";
        $result = $this->db->query($sql, array($idCentral));
        return $result->row_array();
    }

    function getDiasMatriz($totalMetros, $seia, $mtc, $inc, $flgTipoZona, $jefatura) {
        $sql = "SELECT seia, mtc, dias 
                  FROM cotizacion_matriz_dias 
                  WHERE CASE WHEN met_in IS NULL THEN ? <= met_fin
                            WHEN met_fin IS NULL THEN met_in < ?
                            WHEN met_in IS NOT NULL AND met_fin IS NOT NULL AND ? = met_in  THEN ? BETWEEN met_in+1 AND met_fin 
                            WHEN met_in IS NOT NULL AND met_fin IS NOT NULL AND ? <> met_in THEN ? BETWEEN met_in AND met_fin END
                   AND seia = COALESCE(?, seia)
                   AND mtc  = COALESCE(?, mtc)
                   AND inc  = COALESCE(?, inc)
                   AND flg_tipo_zona = ?
                   AND CASE WHEN ? = 'LIMA' THEN flg_lima_provincia = 1
                            ELSE flg_lima_provincia = 2 END
                #GROUP BY seia, mtc";
        $result = $this->db->query($sql, array($totalMetros, $totalMetros, $totalMetros, $totalMetros, $totalMetros, $totalMetros, $seia,
            $mtc, $inc, $flgTipoZona, $jefatura));
           // log_message('error', $this->db->last_query());
        return $result->row_array();
    }

    function getTipoDiseno($idTipoDiseno = null) {
        $sql = "SELECT id_tipo_diseno,
                       descripcion
                  FROM tipo_diseno
                 WHERE id_tipo_diseno = COALESCE(?, id_tipo_diseno)
                   AND flg_activo = 1";
        $result = $this->db->query($sql, array($idTipoDiseno));
        log_message('error', $this->db->last_query());
        return $result->result_array();
    }
    
    function getEbcByDistrito($departamento) {
        $sql = "SELECT codigo,
                       nom_estacion
                  FROM ebc_ubicacion 
                 WHERE UPPER(departamento) = UPPER(?)";
        $result = $this->db->query($sql, array($departamento));
        return $result->result_array();
    }

    function getDataCotizacionByCodigo($codigo_cluster, $costoMo, $costoMat) {
		$sql = "SELECT  ROUND(COALESCE(costo_materiales, 0)+
							  COALESCE(costo_mat_edif, 0)+
							  COALESCE(costo_oc_edif, 0), 2)costo_materiales,	
					   ROUND( COALESCE(costo_mano_obra, 0)+
							  COALESCE(costo_diseno, 0)+
							  COALESCE(costo_expe_seia_cira_pam, 0)+
							  COALESCE(costo_adicional_rural, 0)+
							  COALESCE(costo_oc, 0), 2)costo_mo, 
					   costo_total,
					   itemplan,
					   CASE WHEN costo_mo < ".$costoMo." THEN 1 ELSE 0 END flg_mayor_mo,
                       CASE WHEN costo_mat < ".$costoMat." THEN 1 ELSE 0 END flg_mayor_mat,
					   pc.idCentral,
					   pc.tipo_enlace
				  FROM planobra_cluster pc
		     LEFT JOIN habilitacion_hilo_costo h ON h.id_habilitacion_hilo = pc.id_habilitacion_hilo
				 WHERE codigo_cluster = ?";
        $result = $this->db->query($sql, array($codigo_cluster));
        return $result->row_array();
	}

    function getDataPqtCostoByCodCoti($cod_coti) {
		$sql = " SELECT pc.codigo_cluster,
                        idSubProyecto,
                        c.idEmpresaColab,
                        j.jefaturaDesc as jefatura,
                        distancia_lineal,
                        pc.clasificacion,
                        pc.tipo_cliente,
                        pc.longitud,
                        pc.latitud,
                        pc.nombre_estudio,
                        pc.tipo_enlace
                FROM    planobra_cluster pc,
                        central c,
                        jefatura j
                WHERE   codigo_cluster = ?
                AND     pc.idCentral 	= c.idCentral
                AND     c.idJefatura 	= j.idJefatura";
		$result = $this->db->query($sql, array($cod_coti));
	    return $result->row_array();
	}

    function getDataCotizacionIndividual($sisego, $codigo, $flgDetalle = null, $idSubProyecto = null, $estado = null, $idJefatura = null, $idEmpresaColab = null, $flgBandConf = null, $itemplan = null) {
        $ideecc  = $this->session->userdata("idEmpresaColabSesion");
		$sql = "SELECT DISTINCT
                    pc.codigo_cluster, 
                    pc.itemplan,
                    pc.sisego,
                    pc.cliente, 
                    (SELECT concat(codigo,'-',centralDesc) 
                    FROM central c
                    WHERE c.idCentral = pc.nodo_principal) AS nodo_principal,
                    (SELECT concat(codigo,'-',centralDesc) 
                    FROM central c
                    WHERE c.idCentral = pc.nodo_respaldo) AS nodo_respaldo,
                    pc.facilidades_de_red,
                    pc.cant_cto,
                    pc.longitud,
                    pc.latitud,
                    pc.clasificacion,	 
                    CASE WHEN pc.flg_lan_to_lan = 1 THEN pc.nombre_estudio 
                        WHEN pc.flg_principal  = 0 THEN 'PRINCIPAL'
                        WHEN pc.flg_principal  = 1 THEN 'RESPALDO' END flg_principal,
                    CASE WHEN pc.flg_robot = 1 THEN 'ROBOT'
                        WHEN pc.flg_robot = 2 THEN 'EECC' END hizo_coti,						
                    pc.metro_tendido_aereo,
                    ce.codigo,
                    pc.metro_tendido_subterraneo,
                    pc.metors_canalizacion,
                    pc.cant_camaras_nuevas,
                    pc.cant_postes_nuevos,
                    pc.cant_postes_apoyo,
                    pc.cant_apertura_camara,
                    pc.requiere_seia,
                    pc.requiere_aprob_mml_mtc,
                    pc.requiere_aprob_inc,
                    pc.duracion,
                    UPPER(t.descripcion) AS tipo_diseno_desc,
                    COALESCE(pc.costo_materiales, 0)+ COALESCE(pc.costo_mat_edif, 0) as costo_materiales,
                    COALESCE(pc.costo_mano_obra, 0)+ COALESCE(pc.costo_mo_edif, 0) +  COALESCE(pc.costo_oc, 0)+ COALESCE(pc.costo_oc_edif, 0) costo_mano_obra,
                    pc.costo_diseno,
                    pc.costo_expe_seia_cira_pam,
                    pc.costo_adicional_rural,
                    pc.costo_total,
                    pc.ubic_perfil,
                    pc.ubic_sisego,
                    pc.ubic_rutas,
                    pc.fecha_registro,
                    fecha_envio_cotizacion,
                    pc.comentario_cancela,
                     null as operador,
                    pc.acceso_cliente,
                    pc.nombre_estudio,
                    pc.tendido_externo,
                    pc.tipo_cliente,
                    pc.departamento,
                    pc.segmento,
                    pc.ubic_perfil,
                    pc.ubic_sisego,
                    pc.ubic_rutas,
                    pc.tipo_requerimiento,
                    pc.tipo_enlace,
                    pc.tipo_enlace_2,
                    (SELECT u.nombre_completo 
                    FROM usuario u
                    WHERE u.id_usuario = pc.usuario_envio_cotizacion)AS nombreUsuarioEnvioCoti,
                    CASE WHEN pc.estado  = 0 THEN 'PDT COTIZACION'
                        WHEN pc.estado  = 1 THEN 'PDT APROBACION'
                        WHEN pc.estado  = 2 THEN 'APROBADO'
                        WHEN pc.estado  = 3 THEN 'RECHAZADO' 
                        WHEN pc.estado  = 4 THEN 'PDT CONFIRMACION' 
                        WHEN pc.estado  = 8 THEN 'CANCELADO' END estado,
                    UPPER(pc.comentario) AS comentario,
                    (SELECT UPPER(nom_estacion) 
                    FROM ebc_ubicacion ee
                    WHERE ee.codigo = pc.facilidades_de_red limit 1) nom_ebc,
                    e.empresacolabDesc,
                    pc.estado,
                    (SELECT nombre_nodo 
                    FROM central_otro_operador 
                    WHERE codigo = pc.nodo_otro_operador) nom_nodo,
                    CASE WHEN pc.flg_nodo_otro_operador = 1 THEN 'SI' ELSE 'NO' END as flg_nodo_otro_operador,
                    pc.cant_cto,
                    pc.cant_divicau,
                    pc.cant_empame_1632,
                    pc.cant_empalme_64,
                    pc.cant_empalme_128,
                    pc.cant_empalme_256,
                    pc.cant_cruceta,
                    pc.cant_postes_telefonico,
                    pc.cant_puntos_apoyo,
                    pc.operador_aereo,
                    pc.cant_postes_electricos,
                    pc.empresa_electrica,
                    pc.cant_ducto_2_pul,
                    pc.cant_ducto_3_pul,
                    pc.cant_ducto_4_pul,
                    pc.operador_subte
                FROM (planobra_cluster pc,
                    subproyecto s,
                    empresacolab e)
            LEFT JOIN central ce ON (ce.idCentral = pc.idCentral)  		
            LEFT JOIN tipo_diseno t ON (pc.id_tipo_diseno = t.id_tipo_diseno) 
            LEFT JOIN planobra po
                ON (pc.itemplan      = po.itemplan)
                WHERE pc.flg_tipo       = 2 -- REGISTRO INDIVIDUAL
                AND CASE WHEN pc.sisego IS NOT NULL THEN pc.sisego = COALESCE(?, pc.sisego)
                            ELSE true END
                AND s.idSubProyecto   = pc.idSubProyecto       
                AND pc.codigo_cluster = COALESCE(?, pc.codigo_cluster)
                AND pc.idSubProyecto  = COALESCE(?, pc.idSubProyecto)
                AND pc.estado         = COALESCE(?, pc.estado)
                AND CASE WHEN ? = '' OR ? IS NULL THEN true ELSE pc.itemplan = ? END
                AND pc.flg_paquetizado IN (1,2)
                AND pc.idEmpresaColab = e.idEmpresaColab
                AND CASE WHEN ce.idCentral IS NOT NULL THEN ce.idJefatura = COALESCE(?, ce.idJefatura)
                            ELSE true END
                AND pc.idEmpresaColab = COALESCE(?, e.idEmpresaColab)

                AND CASE WHEN ? IS NOT NULL THEN pc.flg_rech_conf_ban_conf = ?
                            ELSE TRUE END
                AND CASE WHEN ".$ideecc." = 0 OR ".$ideecc." = 6 THEN true
                        ELSE pc.idEmpresaColab = ".$ideecc." END
                AND pc.flg_cotizacion_pin IS NULL";
        $result = $this->db->query($sql, array($sisego, $codigo, $idSubProyecto, $estado, $itemplan, $itemplan, $itemplan, $idJefatura, $idEmpresaColab, $flgBandConf, $flgBandConf));	
           // log_message('error', $this->db->last_query());
		if ($flgDetalle == 1) {
            return $result->row_array();
        } else {
            return $result->result_array();
        }
    }

    function getCountCotizacionByCod($codigo_cluster, $estado = null) {
        $sql = "SELECT COUNT(1) count
                  FROM planobra_cluster 
                 WHERE codigo_cluster = ?
				   AND estado         = COALESCE(?,estado)";
        $result = $this->db->query($sql, array($codigo_cluster, $estado));
        return $result->row_array()['count'];
    }

    function getEstacionesConPoNoCanceladas($itemPlan) {
	    $Query = "SELECT tb.*, iea.porcentaje FROM (SELECT DISTINCT ppo.itemplan, e.idEstacion, e.estacionDesc 
	               from planobra_po ppo, estacion e 
                    where ppo.idEstacion = e.idEstacion
                    and ppo.estado_po not in (7,8)
                    and ppo.itemplan = ?
	                AND ppo.idEstacion not in (1,20)) as tb
                    LEFT JOIN itemplanestacionavance iea on iea.itemplan = tb.itemplan and iea.idEstacion = tb.idEstacion";
	    $result = $this->db->query($Query, array($itemPlan));
	    return $result->result_array();
	}

    function getDataCotizacionByCod($codigo_coti) {
        $sql = "SELECT c.*,
                        pc.sisego,
                        pc.cliente,
                        pc.itemplan,
                        pc.distancia_lineal + (pc.distancia_lineal*0.30) distancia,
                        pc.id_habilitacion_hilo,
                        pc.flg_cotizacion_pin,
                        pc.fecha_registro,
                        pc.fecha_aprobacion,
                        pc.idSubProyecto,
                        (pc.costo_mano_obra + pc.costo_oc) as costo_sol_oc,
                        pc.costo_materiales,
                        UPPER(t.descripcion) AS tipo_diseno_desc, t.id_tipo_diseno,
                        CASE WHEN(pc.metro_tendido_aereo + pc.metro_tendido_subterraneo) > 4000 AND flg_robot = 2 THEN 1
                            WHEN (t.id_tipo_diseno  IN (4, 8, 9, 5) OR t.id_tipo_diseno IS NULL) THEN 1
                            WHEN clasificacion = 'CATV' THEN 1 
                            -- WHEN UPPER(pc.clasificacion) = 'ESTUDIO DE CAMPO' THEN 1 
                            ELSE 2 END flg_paquetizado,				 
                        CASE WHEN t.id_tipo_diseno NOT IN (8, 9) AND (COALESCE(pc.metro_tendido_aereo,0) + COALESCE(pc.metro_tendido_subterraneo, 0)) > 4000 THEN 2 
                            ELSE NULL END complejidad
                FROM (planobra_cluster pc, central c)
                LEFT JOIN tipo_diseno t ON  t.id_tipo_diseno = pc.id_tipo_diseno
                WHERE codigo_cluster = ?
                    AND pc.idCentral = c.idCentral";
        $result = $this->db->query($sql, array($codigo_coti));
        return $result->row_array();
    }

    function getCentraoToCotib2b($idEmpresaColab) {
        $Query = "  SELECT idCentral ,
		                   CONCAT(codigo,'-',centralDesc) as tipoCentralDesc  
		              FROM central
					 WHERE flg_coti_b2b = 1
					   AND CASE WHEN ? IN (0,6) THEN true 
					            ELSE idEmpresaColab = COALESCE(?, idEmpresaColab) END;";
        $result = $this->db->query($Query, array($idEmpresaColab, $idEmpresaColab));
        return $result;
    }

    function getFacilidadXCentral($idCentral) {
        $sql = "SELECT  *
                FROM 	facilidad_x_central 
                WHERE 	idCentral = ?";
        $result = $this->db->query($sql, array($idCentral));
        return $result;
    }

    function getDataPartidaDisenoMediaComplejidad($itemplan) {
        $query = " SELECT po.idEmpresaColab, po.idSubProyecto, p.codigoPartida, p.baremo, pre.costo from planobra po, central c, pqt_tipo_preciario ptp, pqt_preciario pre, partida p
                    WHERE po.idCentral		= c.idCentral
                    AND pre.idEmpresaColab  = po.idEmpresaColab
                    AND pre.tipoJefatura    = (CASE WHEN c.idJefatura = 13/*LIMA*/ THEN 1 ELSE 2 END)
                    AND	ptp.id              = pre.idTipoPreciario
                    AND po.itemplan         = ?
                    AND p.idTipoPreciario   = pre.idTipoPreciario
                    and p.codigoPartida     = '10001-3';";
        $result = $this->db->query($query, array($itemplan));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }   

    function getDataPartidaDisenoByCodPartidaItemplan($itemplan, $codPartida) {
        $query = " SELECT po.idEmpresaColab, po.idSubProyecto, p.codigoPartida, p.baremo, pre.costo from planobra po, central c, pqt_tipo_preciario ptp, pqt_preciario pre, partida p
                    WHERE po.idCentral		= c.idCentral
                    AND pre.idEmpresaColab  = po.idEmpresaColab
                    AND pre.tipoJefatura    = (CASE WHEN c.idJefatura = 13/*LIMA*/ THEN 1 ELSE 2 END)
                    AND	ptp.id              = pre.idTipoPreciario
                    AND po.itemplan         = ?
                    AND p.idTipoPreciario   = pre.idTipoPreciario
                    and p.codigoPartida     = ?";
        $result = $this->db->query($query, array($itemplan, $codPartida));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }   

    function getMotivoCancelaIPByTipo($tipo) {
        $sql = " SELECT * FROM motivo  
                 WHERE 1 = 1
                 AND flg_tipo = COALESCE(?, flg_tipo)
                 ORDER BY motivoDesc ";
        $result = $this->db->query($sql,array($tipo));
        return $result->result();         
    }
	
	function getEstacionesByItemplanLiquidacion($itemplan) {
        $sql = "     SELECT tb1.*,
                            COALESCE(iea.porcentaje,0) porcentaje,
                            iea.fecha,
                            iea.idUsuarioLog,
                            iea.comentario,
                            iea.ruta_evidencia,
                            iea.path_pdf_pruebas,
                            iea.path_pdf_perfil
                       FROM 			
                            (
                                  SELECT po.itemplan,
                                         po.idEstadoPlan,
                                         po.idSubProyecto,
                                         sae.idEstacion,
                                         e.estacionDesc,
                                         sp.idTipoSubProyecto
                                    FROM planobra po,
                                         subproyecto sp,
                                         subproyecto_area_estacion sae,
                                         estacion e
                                   WHERE po.idSubProyecto = sp.idSubProyecto
                                     AND sp.idSubProyecto = sae.idSubProyecto
                                     AND sae.idEstacion = e.idEstacion
                                     AND e.idEstacion NOT IN (1)
                                     AND po.itemplan = ?
                                GROUP BY sae.idEstacion 
                            ) tb1
                  LEFT JOIN itemplanestacionavance iea ON tb1.itemplan = iea.itemplan AND tb1.idEstacion = iea.idEstacion ";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function getMotivoAll($flgTipo) {
        $sql = "SELECT idMotivo,
                       idSisego,
		               UPPER(motivoDesc) as motivoDesc 
				  FROM motivo
				 WHERE flg_tipo = " . $flgTipo . "
				 ORDER BY motivoDesc asc";
        $result = $this->db->query($sql);
        return $result->result();
    }

    public function getEstadoPO($codigoPO) {
        $sql = " SELECT estado_po AS estado_po
		           FROM planobra_po
                  WHERE codigo_po = ?";
        $result = $this->db->query($sql, array($codigoPO));
        return $result->row_array()['estado_po'];
    }
	
	public function getInfoClusterB2bByItemplan($itemplan) {
        $sql = " SELECT *
		           FROM planobra_cluster
                  WHERE itemplan = ?";
        $result = $this->db->query($sql, array($itemplan));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }
	
	function haveSolictudRechazadoExpediente($itemplan){
	    $Query = "select  count(1) as total from pqt_solicitud_aprob_partidas_adicionales where itemplan = ? and estado in (3,4) and activo = 1;";
	    $result = $this->db->query($Query,array($itemplan));
	    if($result->row() != null) {
	        return $result->row_array()['total'];
	    } else {
	        return null;
	    }
	}

    function getEstadosValidosToCanTrunSus($idEstadoPlanActual, $idEstadoPlanAnterior, $idTipoPlanta, $idSubProyecto) {
        $sql = "SELECT * FROM estadoplan 
                WHERE CASE WHEN 1 = ? THEN idEstadoPlan = 6 
                    WHEN 24 = ? THEN idEstadoPlan = 6 
                    WHEN 2 = ? THEN idEstadoPlan IN (6,10,18)
                    WHEN 7 = ? THEN idEstadoPlan IN (6,10,18)
                    WHEN 3 = ? THEN CASE WHEN 2 = ? THEN idEstadoPlan IN (6,10,18) WHEN 748 = ? THEN idEstadoPlan IN (6,10,18) ELSE idEstadoPlan IN (10,18) END
                    WHEN 19 = ? THEN idEstadoPlan IN (10,18)
                    WHEN 20 = ? THEN idEstadoPlan IN (10,18)
                    WHEN 10 = ? THEN idEstadoPlan IN (6)
                    WHEN 18 = ? THEN idEstadoPlan IN (6,10,?) ELSE FALSE END";
        $result = $this->db->query($sql,array($idEstadoPlanActual, $idEstadoPlanActual,$idEstadoPlanActual,$idEstadoPlanActual,$idEstadoPlanActual, $idTipoPlanta,$idSubProyecto,
                                              $idEstadoPlanActual, $idEstadoPlanActual,$idEstadoPlanActual,$idEstadoPlanActual,$idEstadoPlanAnterior));
        return $result->result();         
    }

    function getLogPlanobraToRevertSus($itemplan, $idEstadoPlan){
	    $Query = "SELECT * FROM log_planobra 
                    WHERE itemplan = ? 
                    AND idEstadoPlan = ? 
                    ORDER BY fechaReg DESC LIMIT 1;";
	    $result = $this->db->query($Query,array($itemplan, $idEstadoPlan));
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
	
	function getAllDivCauOyM() {
        $query = "   SELECT *
                       FROM oym_divcau";
        $result = $this->db->query($query, array());
        return $result->result_array();
    }

    function updateBatchSeguiFormulario($arrayDetToFormut, $itemUpdReg, $logFirstReg) {
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        $this->db->trans_begin();            
            $this->db->update_batch('formulario_reforzamientos',$arrayDetToFormut, 'id_formulario');
            if($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception('Error al modificar el formulario_reforzamientos');
            }else{                			
                $this->db->update_batch('planobra',$itemUpdReg, 'itemplan');
                if($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    throw new Exception('Error al modificar el planobra');
                }else{        
                    $this->db->insert_batch('log_seguimiento_reforzamiento', $logFirstReg);
                    if($this->db->trans_status() === FALSE) {
                        $data['msj'] = 'No se log_seguimiento_reforzamiento';
                        $data['error'] = EXIT_ERROR;
                    } else {        			
                            $data['error'] = EXIT_SUCCESS;
                            $data['msj'] = 'Se actualizo correctamente!';
                            $this->db->trans_commit(); 		
                    }		 		       
                }         			 		       
            }            
	    }catch(Exception $e){
	        $data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $data;
	}

    public function getInfoPoByCodigoPo($codigo_po) {
        $Query = "SELECT * FROM planobra_po WHERE codigo_po = ?";
        $result = $this->db->query($Query, array($codigo_po));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }
    
    public function getInfoJefaturaByItemplan($itemplan) {
		$sql = "SELECT po.itemplan, j.jefaturaDesc,  ( SELECT GROUP_CONCAT(je.codAlmacen,'|',je.codCentro,'|', je.idJefatura, '|', je.idEmpresaColab) 
					FROM jefatura_sap js, 
						jefatura_sap_x_empresacolab je 
				WHERE js.idJefatura = je.idJefatura
					AND je.idEmpresacolab = po.idEmpresaColab
					AND CASE WHEN js.idZonal IS NULL THEN js.descripcion = j.jefaturaDesc
						ELSE js.idZonal = po.idZonal END ) dataJefaturaEmp from planobra po, central c, jefatura j
				where po.idCentral = c.idCentral
				and c.idJefatura = j.idJefatura
				and po.itemplan = ?";
		$result = $this->db->query($sql, array($itemplan));
		return $result->row_array();
	}

    function getInfoItemplanMadreRefo($itemplan_m) {
        $query = "SELECT * FROM planobra where itemplan = ? AND idSubProyecto = 747";
        $result = $this->db->query($query, array($itemplan_m));
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }

    function getInfoEECCByIdEECC($idEECC) {
        $sql = " SELECT * 
                   FROM empresacolab 
                  WHERE idEmpresaColab = ?";
        $result = $this->db->query($sql, array($idEECC));
		log_message('error', $this->db->last_query());
        return $result->row_array();
    }

    function getPartidasToPoPqtReforzamientoExpress($itemplan, $nro_cto) {
	    $sql = " SELECT pa.codigoPartida, pa.descripcion AS partidaDesc, tp.descripcion as tipo, pre.costo, pa.baremo,(pep.cantidad*?) as cantidad, ( pre.costo*pa.baremo*pep.cantidad*?) as total_partida
        FROM kit_po_pqt_reforzamiento_cto_express pep, partida pa, planobra po, pqt_tipo_preciario tp, pqt_preciario pre, central c
        WHERE pep.codigo_partida = pa.codigoPartida
        AND pa.idTipoPreciario  = tp.id
        AND pre.idEmpresaColab  = po.idEmpresaColab
        AND pre.idTipoPreciario = pa.idTipoPreciario
        AND po.idCentral        = c.idCentral
        AND pre.tipoJefatura    = (CASE WHEN c.idJefatura = 13 /*LIMA*/ THEN 1 ELSE 2 END)
        AND po.itemplan         = ?";
	    $result = $this->db->query($sql, array($nro_cto, $nro_cto, $itemplan));
	    return $result->result_array();
	}

    function getCostoTotalToOCMoPqtReforzamientoExpress($idCentral, $idEmpresaColab, $nro_cto) {
	    $sql = "SELECT sum(( pre.costo*pa.baremo*pep.cantidad*?)) as total_mo
                FROM kit_po_pqt_reforzamiento_cto_express pep, partida pa, pqt_tipo_preciario tp, pqt_preciario pre, central c
                WHERE pep.codigo_partida    = pa.codigoPartida
                AND pa.idTipoPreciario      = tp.id
                AND pre.idEmpresaColab      = ?
                AND pre.idTipoPreciario     = pa.idTipoPreciario
                AND c.idCentral             = ?
                AND pre.tipoJefatura        = (CASE WHEN c.idJefatura = 13 /*LIMA*/ THEN 1 ELSE 2 END)";
	    $result = $this->db->query($sql, array($nro_cto, $idEmpresaColab, $idCentral));
	    return $result->row_array()['total_mo'];
	}

    function getMaterialesToReforzamientoExpress($nro_cto) {
	    $sql = "SELECT m.*,(kit.cantidad*?) as cantidad FROM kit_po_pqt_reforzamiento_cto_express kit, material m
                where kit.codigo_material = m.codigo_material 
                and kit.tipo = 1
                and m.estado_material= 1";
	    $result = $this->db->query($sql, array($nro_cto));
	    return $result->result_array();
	}
    
	function getEmpresaColabById($idEmpresaColab) {
		$sql = "SELECT idEmpresaColab, 
		               empresaColabDesc 
				  FROM empresacolab
				 WHERE CASE WHEN ? NOT IN (0,6) THEN idEmpresaColab = ?
							WHEN ? IN (0,6) THEN idEmpresaColab NOT IN (5,6,7,8,9, 12) END";
		$result = $this->db->query($sql, array($idEmpresaColab, $idEmpresaColab, $idEmpresaColab));
        return $result->result_array();
	}
	
	function getCostoTotalMatToCostounitarioMatReforzamientoMadre($nro_cto) {
	    $sql = "SELECT (kit.cantidad*?*m.costo_material) as total_mat 
                FROM kit_po_pqt_reforzamiento_cto_express kit, material m
                                where kit.codigo_material = m.codigo_material 
                                and kit.tipo = 1
                                and m.estado_material= 1";
	    $result = $this->db->query($sql, array($nro_cto));
        return $result->row_array()['total_mat'];
	}
	
	function getCostoTotalToOCMoPqt($idEstacionPartidas, $itemplan, $tipo_partidas) {
	    $sql = "SELECT 
                    ROUND(SUM(tb.baremo * tb.cantFactorPlanificado * tb.costo), 2) AS total_mo
                    FROM (
                            SELECT po.idEmpresaColab, 
                                po.idZonal,
                                pqe.id_tipo_partida,
                                ptp.descripcion as tipoPreciario,
                                pqe.descripcion as partidaPqt,
                                (CASE  WHEN po.idSubProyecto IN (750,751) THEN  (CASE WHEN po.cantFactorPlanificado BETWEEN 0 AND 10 THEN pbs.baremo
                                                                                    WHEN po.cantFactorPlanificado BETWEEN 11 AND 25 THEN pbs.baremo_cv_11_25
                                                                                    WHEN po.cantFactorPlanificado > 25 THEN (((po.cantFactorPlanificado - 25) * pbs.baremo_cv_dpto_adic) + pbs.baremo_cv_11_25) ELSE 0 END)
                                        ELSE pbs.baremo END)  as baremo,
                                (CASE WHEN po.idSubProyecto IN (750,751) THEN 1 ELSE	po.cantFactorPlanificado END) as cantFactorPlanificado,
                                pre.costo,                 
                                pqe.codigoPartida
                            FROM
                                planobra po,
                                central c,
                                subproyecto sp,
                                pqt_baremo_x_subpro_x_partida_mo pbs,
                                pqt_partidas_paquetizadas_x_estacion pqe,
                                pqt_tipo_preciario ptp,
                                pqt_preciario pre
                            WHERE
                                po.idSubProyecto    = pbs.idSubProyecto
                            AND po.idSubProyecto	= sp.idSubProyecto
                            AND pbs.id_pqt_partida_mo_x_estacion    = pqe.id_tipo_partida
                            AND pqe.id_pqt_tipo_preciario           = ptp.id
                            AND ptp.id              = pre.idTipoPreciario
                            AND	po.idCentral		= c.idCentral
                            AND pqe.idEstacion      = ?
                            AND pbs.idSubProyecto   = po.idSubProyecto
                            AND pre.idEmpresaColab  = po.idEmpresaColab
                            AND pre.tipoJefatura    = (CASE WHEN c.idJefatura = 13/*LIMA*/ THEN 1 ELSE 2 END)
                            AND po.itemplan         = ?
                            AND CASE WHEN ? = 1 THEN pqe.id_tipo_partida IN (1,5)
                                    WHEN ? = 2 THEN pqe.id_tipo_partida NOT IN (1,5)
                                    ELSE TRUE END
                    ) as tb";
	    $result = $this->db->query($sql, array($idEstacionPartidas, $itemplan, $tipo_partidas, $tipo_partidas));
	    return $result->row_array()['total_mo'];
	}

    function getMaterialFerreteriaPoPqtCvBucle($itemplan) {
	    $sql = "SELECT pmat.*, po.cantFactorPlanificado, 
                (CASE WHEN po.cantFactorPlanificado BETWEEN 0 AND 10 THEN monto
                    WHEN po.cantFactorPlanificado BETWEEN 11 AND 25 THEN monto_cv_11_25
                    WHEN po.cantFactorPlanificado > 25 THEN (((po.cantFactorPlanificado - 25) * monto_cv_adic) + monto_cv_11_25) ELSE 0 END) as baremo_final
                FROM pqt_precio_max_mat_x_subpro pmat, planobra po
                WHERE pmat.idSubProyecto = po.idSubProyecto
                and po.itemplan =  ?";
	    $result = $this->db->query($sql, array($itemplan));        
        if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
	}
	
	 function getLogExpedientes($itemplan) {
        $sql = "SELECT pqt.fec_registro, u1.nombre_completo  as usua_reg_expediente, (CASE WHEN pqt.estado = 0 THEN 'PDT VAL NIVEL 1'
                        WHEN pqt.estado = 1 THEN 'PDT VAL NIVEL 2'
                        WHEN pqt.estado = 2 THEN 'APROBADO'
                        WHEN pqt.estado = 3 THEN 'RECHAZADO' 
                    ELSE '-'END) as estado_expe,
                    pqt.comentario, pqt.fec_val_nivel_1, u2.nombre_completo  as usua_val_niv1, pqt.fec_val_nivel_2, u3.nombre_completo as usua_val_niv2, pqt.costo_total, ie.path_expediente 
                    FROM pqt_solicitud_aprob_partidas_adicionales pqt	
                LEFT JOIN usuario u1 ON pqt.usua_registro = u1.id_usuario  
                LEFT JOIN usuario u2 ON pqt.usua_val_nivel_1 = u2.id_usuario  
                LEFT JOIN usuario u3 ON pqt.usua_val_nivel_2 = u3.id_usuario  
                LEFT JOIN itemplan_expediente ie ON pqt.fec_registro = ie.fecha
                where pqt.itemplan = ?";
        $result = $this->db->query($sql, array($itemplan));
        return $result->result_array();
    }

    function obtenerUltimaFechaArchivoMatrizJumpeo($tipo)
    {
        $sql = "SELECT fecha_registro FROM matriz_jumpeo_file  WHERE tipo = ? ORDER BY 1 DESC LIMIT 1";
        $result = $this->db->query($sql, array($tipo));
        return $result->result_array();
    }
	
}
