<?php

class M_extractor extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getReportePlanobraCV($idEmpresaColab) {
        $sql = "    SELECT po.itemplan,
                           pro.proyectoDesc,
                           s.subproyectoDesc,
                           po.indicador,
                           po.sisego,
                           UPPER(po.nombrePlan) nombrePlan,
                           f.faseDesc,
                           po.cantFactorPlanificado AS cant_uip,
                           po.longitud,
                           po.latitud,
                           es.estadoPlanDesc,
                           po.fechaRegistro,
                           po.fechaInicio,
                           po.fec_ult_ejec_diseno,
                           po.fechaPrevEjecucion,
                           ce.centralDesc,
                           j.jefaturaDesc,
                           z.zonalDesc,
                           e.empresaColabDesc,
                           po.codigoInversion,
                           ce.codigo AS codigo_mdf,
                           (CASE WHEN po.paquetizado_fg = 2 THEN 'SI' ELSE 'NO' END) paquetizado,
						   po.orden_compra,
                           po.ult_codigo_sirope,
                           po.ult_estado_sirope,
                           po.ult_codigo_sirope_coax,
                           po.ult_estado_sirope_coax,
                           po.fechaPreLiquidacion,
						   po.itemplan_m,
                           CONCAT(u.nombre_completo, ' ',u.ape_paterno) as usuario_cre, 
                           (CASE WHEN pqt.estado IS NULL THEN 'PDT CARGA EXPEDIENTE'
							WHEN pqt.estado = 0 THEN 'PDT VALIDACION N1'
                            WHEN pqt.estado = 1 THEN 'PDT VALIDACION N2'
                            WHEN pqt.estado = 2 THEN 'EXPEDIENTE APROBADO'
                            WHEN pqt.estado = 3 THEN 'EXPEDIENTE RECHAZADO'
                            ELSE '' END) as situacion_expediente
                      FROM planobra po LEFT JOIN usuario u ON po.usua_crea_obra = u.id_usuario LEFT JOIN pqt_solicitud_aprob_partidas_adicionales pqt ON po.itemplan = pqt.itemplan and pqt.activo = 1,
                           empresacolab e,
                           zonal z,
                           fase f,
                           estadoplan es,
                           subproyecto s,
                           central ce,
                           proyecto pro,
                           jefatura j
                     WHERE pro.idProyecto = s.idProyecto
                       AND s.idSubProyecto = po.idSubProyecto
                       AND e.idEmpresaColab = po.idEmpresaColab
                       AND z.idZonal = po.idZonal
                       AND f.idFase = po.idFase
                       AND es.idEstadoPlan = po.idEstadoPlan
                       AND po.idCentral = ce.idCentral
                       AND ce.idJefatura  = j.idJefatura
                      -- AND pro.idProyecto = 21
                       AND CASE WHEN ? = 6 THEN TRUE 
						   ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
						    END  ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }

    function getReportePoMo($idEmpresaColab) {
        $sql = "     SELECT po.itemplan,
                            es.estadoPlanDesc,
                            f.faseDesc,
                            po.codigoInversion,
                            e.empresaColabDesc,
                            z.zonalDesc,
                            s.subproyectoDesc,
                            e.idEmpresaColab,
                            po.fechaRegistro,
                            fechaLog,
                            ce.codigo,
                            s.idProyecto,
                            pro.idTipoPlanta,
                            po.idEstadoPlan,
                            po.paquetizado_fg,
                            (CASE WHEN ce.idJefatura = 13 THEN 'LIMA' ELSE 'PROVINCIA' END) AS tipoJefatura,
                            po.cantFactorPlanificado,
                            s.idTipoSubProyecto,
                            s.idSubProyecto,
                            s.idTipoComplejidad,
                            ppo.codigo_po,
                            UPPER(poe.estado) estado_po,
                            a.areaDesc,
                            ppd.codigoPartida,
                            UPPER(pa.descripcion) AS nomPartida,
                            ppd.baremo,
                            ppd.cantidadFinal,
                            ppd.preciario,
                            ppd.montoFinal,
                            ppo.costo_total
                       FROM planobra po,
                            empresacolab e,
                            zonal z,
                            fase f,
                            estadoplan es,
                            subproyecto s,
                            central ce,
                            proyecto pro,
                            jefatura j,
                            planobra_po_detalle_mo ppd,
                            planobra_po ppo,
                            partida pa,
                            po_estado poe,
                            area a
                      WHERE pro.idProyecto = s.idProyecto
                        AND s.idSubProyecto  = po.idSubProyecto
                        AND e.idEmpresaColab = po.idEmpresaColab
                        AND z.idZonal = po.idZonal
                        AND f.idFase = po.idFase
                        AND es.idEstadoPlan = po.idEstadoPlan
                        AND po.idCentral = ce.idCentral
                        AND ce.idJefatura = j.idJefatura
                        AND po.itemplan = ppo.itemplan
                        AND ppo.codigo_po = ppd.codigo_po
                        AND pa.codigoPartida = ppd.codigoPartida
                        AND ppo.estado_po = poe.idEstadoPo
                        AND ppo.idArea = a.idArea
                        -- AND pro.idProyecto = 21
                        AND CASE WHEN ? = 6 THEN TRUE 
						    ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
						     END  ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }

    function getReportePoMat($idEmpresaColab) {
        $sql = "   SELECT ppo.itemplan,
                          ppo.codigo_po,
                          ppd.codigo_material,
                          m.descrip_material,
                          m.unidad_medida,
                          ppd.cantidadInicial,
                          ppd.cantidadFinal,
                          ppd.costoMat,
                          ppd.montoFinal,
                          ppo.costo_total,
                          a.areaDesc,
                          ppo.vale_reserva,
                          UPPER(poe.estado) estado_po
                     FROM planobra_po_detalle_mat ppd,
                          planobra_po ppo,
                          material m,
                          area a,
                          po_estado poe,
                          planobra po,
                          subproyecto sp
                    WHERE ppo.codigo_po = ppd.codigo_po
                      AND ppd.codigo_material = m.codigo_material
                      AND ppo.idArea = a.idArea
                      AND ppo.estado_po = poe.idEstadoPo
                      AND ppo.itemplan = po.itemplan
                      AND po.idSubProyecto = sp.idSubProyecto
                      -- AND sp.idProyecto = 21
                      AND CASE WHEN ? = 6 THEN TRUE 
						  ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
						   END ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }

    function getReporteDetallePlan($idEmpresaColab) {
        $sql = "    SELECT po.itemplan,
                           ppo.codigo_po,
                           a.areaDesc,
                           pro.proyectoDesc,
                           s.subproyectoDesc,
                           f.faseDesc,
                           po.indicador,
                           po.fechaRegistro,
                           j.jefaturaDesc,
                           z.zonalDesc,
                           ce.codigo AS codigo_mdf,
                           e.empresaColabDesc,
                           ppo.flg_tipo_area,
                           ppo.costo_total,
                           ppo.vale_reserva,
                           UPPER(poe.estado) estado_po,
                           ppo.fechaRegistro AS fechaRegPo,
                           u.nombre_completo AS usu_reg_po,
                           es.estadoPlanDesc,
                           po.codigoInversion,
                           po.nombrePlan
                      FROM planobra po,
                           empresacolab e,
                           zonal z,
                           fase f,
                           estadoplan es,
                           subproyecto s,
                           central ce,
                           proyecto pro,
                           jefatura j,
                           planobra_po ppo,
                           area a,
                           po_estado poe,
                           usuario u
                     WHERE pro.idProyecto = s.idProyecto
                       AND s.idSubProyecto = po.idSubProyecto
                       AND e.idEmpresaColab = po.idEmpresaColab
                       AND z.idZonal = po.idZonal
                       AND f.idFase = po.idFase
                       AND es.idEstadoPlan = po.idEstadoPlan
                       AND po.idCentral = ce.idCentral
                       AND ce.idJefatura  = j.idJefatura
                       AND po.itemplan = ppo.itemplan
                       AND ppo.idArea = a.idArea
                       AND ppo.estado_po = poe.idEstadoPo
                       AND ppo.idUsuario = u.id_usuario
                      -- AND pro.idProyecto = 21
                       AND CASE WHEN ? = 6 THEN TRUE 
						        ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
							    END ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }
	
	function getReporteExtractorOCOnline($idEmpresaColab) {
        $sql = "   	 SELECT soc.costo_sap,
							soc.codigo_solicitud, 
							(CASE WHEN soc.tipo_solicitud = 1 THEN 'CREACION OC'
								  WHEN soc.tipo_solicitud = 2 THEN 'EDICION OC'
								  WHEN soc.tipo_solicitud = 3 THEN 'CERTIFICACION OC'
								  WHEN soc.tipo_solicitud = 4 THEN 'ANULA POS OC'	END) AS tipo_pc, 
							DATE(soc.fecha_creacion) AS fecha_creacion, 
							DATE(soc.fecha_valida) AS fecha_validacion, 
							
							soc.cesta, 
							soc.orden_compra, 
							soc.codigo_certificacion, 
							soe.descripcion AS estado_solicitud,
							ixs.posicion, po.itemplan,
							ixs.costo_unitario_mo AS costo, 
							p.proyectoDesc, 
							sp.subProyectoDesc, 
							e.empresaColabDesc,
							soc.codigoInversion,
                            po.precio_obra AS P,
			                ROUND(( ixs.costo_unitario_mo / po.precio_obra),2) AS Q
					   FROM proyecto p, 
							subproyecto sp, 
							empresacolab e,
							planobra po, 
                            solicitud_orden_compra soc, 
							estado_solicitud_orden_compra soe, 
							itemplan_x_solicitud_oc ixs
					  WHERE sp.idSubProyecto = po.idSubProyecto 
						AND po.itemplan = ixs.itemplan
						AND soc.estado = soe.id
						AND ixs.codigo_solicitud_oc = soc.codigo_solicitud
						AND sp.idProyecto = p.idProyecto
						AND po.idEmpresaColab = e.idEmpresaColab
						#AND po.solicitud_oc is not null
						AND CASE WHEN ? = 6 THEN true 
						         ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
							END";
        $result = $this->db->query($sql, array($idEmpresaColab, $idEmpresaColab));
        return $result->result();
    }

    function getReporteDetHijoCV($idEmpresaColab) {
        $sql = "SELECT 
                    pd.*,
                    ec.empresaColabDesc,
                    ms.situacion_general,
                    ms.desc_motivo AS situacion_especifica,
                    po.ult_codigo_sirope,
                    po.ult_estado_sirope,
                    CASE WHEN pd.has_quiebre_evidencia is null then 'NO' ELSE 'SI' END as has_evi_quiebre
                FROM
                    planobra_detalle_cv_hijo pd,
                    motivo_seguimiento_cv ms,
                    planobra po,
                    empresacolab ec
                WHERE
                    po.itemplan = pd.itemplan
				AND po.idEmpresaColab = ec.idEmpresaColab
                AND pd.ult_situa_especifica = ms.id
                AND CASE WHEN ? = 6 THEN TRUE 
                        ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
                    END  ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }

    function getReporteDetHijoB2b($idEmpresaColab) {
        $sql = "SELECT 	
                        po.indicador,
                        pd.itemplan,
                        ep.estadoPlanDesc,
                        ec.empresaColabDesc,
                        c.departamento,
                        c.distrito,
                        ms.situacion_general,
                        ms.desc_motivo AS situacion_especifica,
                        po.fechaRegistro,            
                        (po.fechaRegistro + interval (CASE WHEN pc.duracion IS NULL THEN 60 ELSE pc.duracion END) day) as fec_prev_liquidacion,
                        soc.fecha_valida as fec_atencion_oc,
                        (soc.fecha_valida + interval 7 day) as fec_prev_ejec_dise,
                        po.fec_ult_ejec_diseno,
                        po.fechaPreLiquidacion,
                        pd.ultimo_comentario
                    
                FROM
                        planobra_detalle_b2b pd,
                        motivo_seguimiento_b2b ms,
                        planobra po 
                        LEFT JOIN solicitud_orden_compra soc ON po.solicitud_oc = soc.codigo_solicitud AND soc.estado = 2 AND soc.tipo_solicitud = 1
                        LEFT JOIN planobra_cluster pc		 ON	po.itemplan = pc.itemplan,
                        empresacolab ec,
                        central c,
                        estadoplan ep
                WHERE
                        po.itemplan = pd.itemplan
                AND 	po.idEmpresaColab = ec.idEmpresaColab
                AND 	po.idEstadoPlan = ep.idEstadoPlan
                AND 	po.idCentral = c.idCentral
                AND 	pd.ult_situa_especifica = ms.id
                AND CASE WHEN ? = 6 THEN TRUE 
                        ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
                    END  ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }

    function getReporteDetallePoMat($idEmpresaColab) {
        $sql = "SELECT ppo.itemplan, ppo.codigo_po, pe.estado, ec.empresaColabDesc, pdm.codigo_material, ma.descrip_material, pdm.cantidadFinal, ppo.vale_reserva, ppo.fechaRegistro , ppo.fecha_reg_vr
                FROM planobra_po ppo, planobra_po_detalle_mat pdm, po_estado pe, material ma, empresacolab ec
                where ppo.codigo_po = pdm.codigo_po
                and ppo.estado_po = pe.idEstadoPo
                and ppo.idEmpresaColab = ec.idEmpresaColab
                and pdm.codigo_material = ma.codigo_material
                and ppo.flg_tipo_area = 1
                AND CASE WHEN ? = 6 THEN TRUE 
                    ELSE ppo.idEmpresaColab = COALESCE(?, ppo.idEmpresaColab) 
                END  ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }
	
	function getReporteCotizaciones($idEmpresaColab) {
        $sql = "SELECT 
                    pc.idEmpresaColab, pc.itemplan, pc.nombre_estudio, pc.fecha_aprobacion, pc.estado, pc.codigo_cluster, sp.subproyectoDesc, pc.sisego, e.empresaColabDesc, c.codigo, pc.distrito, pc.tipo_enlace, 
                    ROUND(COALESCE(pc.costo_materiales, 0)+ COALESCE(pc.costo_mat_edif, 0), 2) as costo_materiales,
                    ROUND(COALESCE(pc.costo_mano_obra, 0)+ COALESCE(pc.costo_mo_edif, 0) + COALESCE(pc.costo_oc, 0) + COALESCE(pc.costo_oc_edif, 0), 2) costo_mano_obra,
                    pc.clasificacion, pc.fecha_registro,   
                    CASE	 WHEN pc.estado  = 0 THEN 'PDT COTIZACION'
                    WHEN pc.estado  = 1 THEN 'PDT APROBACION'
                    WHEN pc.estado  = 2 THEN 'APROBADO'
                    WHEN pc.estado  = 3 THEN 'RECHAZADO'
                    WHEN pc.estado  = 4 THEN 'PDT CONFIRMACION'
                    WHEN pc.estado  = 8 THEN 'CANCELADO' END estadoDesc, pc.duracion, pc.tipo_enlace_2, (pre.costo*2.3) as total_coti,
                    (SELECT concat(codigo,'-',centralDesc) 
                        FROM central c
                        WHERE c.idCentral = pc.nodo_principal) AS nodo_principal,
                    pc.cant_puntos_apoyo,
                    pc.operador_aereo,
                    pc.cant_postes_electricos,
                    pc.empresa_electrica,
                    pc.cant_ducto_2_pul,
                    pc.cant_ducto_3_pul,
                    pc.cant_ducto_4_pul,
                    pc.operador_subte,
                    pc.orden_compra,
                    pc.fecha_reg_cotizacion,
                    (CASE WHEN pc.estado = 8 THEN pc.comentario_cancela 
                        WHEN pc.estado = 3 THEN pc.comentario_rechazo
                        WHEN pc.estado = 0 AND pc.comentario_devolucion IS NOT NULL THEN pc.comentario_devolucion
                        ELSE '' END) as comentario_final,
                    pc.fec_reg_oc
                FROM
                    planobra_cluster pc,
                    subproyecto sp,
                    empresacolab e,
                    central c, 
                    pqt_preciario pre 
                WHERE	
                    pc.idSubProyecto = sp.idSubProyecto
                AND	pc.idEmpresaColab = e.idEmpresaColab
                AND pc.idCentral = c.idCentral
                AND	pc.idEmpresaColab = pre.idEmpresaColab 
                AND pre.idTipoPreciario = 1
                AND pre.tipoJefatura = 1           
                AND CASE WHEN ? = 6 THEN TRUE 
			    ELSE pc.idEmpresaColab = COALESCE(?, pc.idEmpresaColab) 
                END ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }
	
	 function getReportePoMo2($idEmpresaColab) {
        $sql = "   SELECT ppo.itemplan,
                        ppo.codigo_po,      
                        p.codigoPartida,
                        p.descripcion,
                        tp.descripcion as tipoPreciario,
                        p.baremo,
                        ppd.preciario,
                        ppd.cantidadFinal,
                        ppd.montoFinal,
                        a.areaDesc,
                        
                        UPPER(poe.estado) estado_po
                
                FROM planobra_po_detalle_mo ppd,
                
                        planobra_po ppo, 
                        partida p left join pqt_tipo_preciario tp ON tp.id = p.idTipoPreciario,
                        area a,
                        po_estado poe,
                        planobra po,
                        subproyecto sp
                WHERE ppo.codigo_po = ppd.codigo_po
                    AND ppd.codigoPartida = p.codigoPartida            
                    AND ppo.idArea = a.idArea
                    AND ppo.estado_po = poe.idEstadoPo
                    AND ppo.itemplan = po.itemplan
                    AND po.idSubProyecto = sp.idSubProyecto
                    AND CASE WHEN ? = 6 THEN TRUE 
                        ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
                        END ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }
	
	function getReporteDetalleLicencias($idEmpresaColab) {
        $sql = "SELECT ei.itemplan, p.proyectoDesc,sp.subProyectoDesc, ec.empresaColabDesc, ep.estadoPlanDesc, es.estacionDesc, e.entidadDesc,  te.tipoEntidadDesc, ei.nroExpediente,  ei.fechaInicio, ei.fechaFin, ei.nroComprobante, ei.fechaEmisionComp, ei.montoComp, 
                fecha_termino, 
                (CASE WHEN ei.has_termino_lic IS NULL THEN 'NO' ELSE 'SI' END) as has_termino_lic, 
                #DATEDIFF(now(), fecha_termino) as dias,
                (CASE WHEN ei.has_termino_lic IS NULL THEN (CASE WHEN DATEDIFF(now(), fecha_termino) <= 15 THEN 'SI' ELSE 'NO' END) 
                    ELSE (CASE WHEN DATEDIFF(fechaTermino, fecha_termino) <= 15 THEN 'SI' ELSE 'NO' END) END) as dentro_sla_termino, ei.fechaTermino
                FROM entidad_itemplan_estacion ei, planobra po, entidad e, estacion es, tipo_entidad te, subproyecto sp, proyecto p, estadoplan ep, empresacolab ec 
                where ei.itemplan = po.itemplan
                and ei.idEntidad = e.idEntidad
                and ei.idEstacion = es.idEstacion
                and ei.idTipoEntidad = te.idTipoEntidad
                and po.idSubProyecto = sp.idSubProyecto
                and sp.idProyecto = p.idProyecto
                and po.idEstadoPlan = ep.idEstadoPlan
                and po.idEmpresaColab = ec.idEmpresaColab
                AND CASE WHEN ? = 6 THEN TRUE 
                    ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
                    END ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }
	
	function getReporteDetalleFormReforzamientoCto($idEmpresaColab) {
        $sql = "SELECT po.itemplan, p.proyectoDesc, sp.subProyectoDesc, c.centralDesc,  c.codigo, ec.empresaColabDesc, ep.estadoPlanDesc,  fr.cto_ajudi, fr.divcau,fr.tipo_refo, fr.do_splitter, fr.observacion, fr.fecha_registro,
                 (CASE WHEN fr.has_seguimiento = 1 THEN 'SI' ELSE 'NO' END) as has_seguimiento, msr.situacion_general, msrc.situacion_especifica, fr.fase, fr.codigo_divisor, fr.cable, fr.hilo, po.ult_codigo_sirope, po.ult_estado_sirope,
				(CASE WHEN po.has_sirope_fo_fecha IS NOT NULL THEN  po.has_sirope_fo_fecha 
					   WHEN po.has_sirope_diseno_fecha IS NOT NULL THEN  po.has_sirope_diseno_fecha 
                       ELSE '' END) as fec_estado_ot, fr.cto_final, fr.fec_upd_sit_especifica
				FROM planobra po 
				left join motivo_seguimiento_reforzamiento msr ON po.situacion_general_reforzamiento = msr.id, estadoplan ep, empresacolab ec, subproyecto sp, proyecto p, central c, formulario_reforzamientos fr left join motivo_seguimiento_reforzamiento_cto msrc ON fr.situacion_especifica = msrc.id
				WHERE po.idsubProyecto = sp.idsubProyecto
				and sp.idProyecto = p.idProyecto
				and po.idEmpresaColab = ec.idEmpresaColab
				and po.idEstadoPlan = ep.idEstadoPlan
				and  po.itemplan = fr.itemplan 
				and po.idCentral = c.idCentral
                AND CASE WHEN ? = 6 THEN TRUE 
                    ELSE po.idEmpresaColab = COALESCE(?, po.idEmpresaColab) 
                    END ";
        $result = $this->db->query($sql,array($idEmpresaColab,$idEmpresaColab));
        return $result->result();
    }

    function getReporteDetalleMatrizSeguimiento()
    {
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
        return $result->result();
    }

    function getReporteDetalleMatrizJumpeo()
    {
        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2 
                FROM matrizjumpeo m
                INNER JOIN planobra po ON (po.itemplan = m.itemplan)
                INNER JOIN central c ON (c.idCentral  = po.idCentral)
                INNER JOIN zonal z ON (z.idZonal = po.idZonal)
                INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto)
                LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
                LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto)";
        
        $result = $this->db->query($sql, array());

        // log_message('error',$this->db->last_query());
        return $result->result();
    }

    function getReporteDetalleMatrizJumpeoByEECC($empresaColab)
    {
        $sql = "SELECT m.*, s.subProyectoDesc, z.zonalDesc, s2.subProyectoDesc as subProyectoDesc2 
                FROM matrizjumpeo m
                INNER JOIN planobra po ON (po.itemplan = m.itemplan)
                INNER JOIN central c ON (c.idCentral  = po.idCentral)
                INNER JOIN zonal z ON (z.idZonal = po.idZonal)
                INNER JOIN subproyecto s ON (s.idSubProyecto = po.idSubProyecto)
                LEFT JOIN planobra po2 ON (po2.itemplan = m.itemplanPinOt)
                LEFT JOIN subproyecto s2 ON (s2.idSubProyecto = po2.idSubProyecto) WHERE m.idcontrataPint = ? OR m.idcontrataPext= ?";
        
        $result = $this->db->query($sql, array($empresaColab, $empresaColab));

        // log_message('error',$this->db->last_query());
        return $result->result();
    }

    function getReporteDetalleMatrizPinPex()
    {
        $sql = "SELECT mpp.nodo, mpp.cuenta_pares, mpp.fecha_registro, DATE(mj.fechaSolicitud) AS fechaSolicitud, mj.fecha_cierre
                FROM matriz_pin_pex mpp
                LEFT JOIN matrizjumpeo mj ON (mj.nodo = mpp.nodo AND mpp.cuenta_pares = CONCAT(mj.cable, ', ',mj.hilo));";
        
        $result = $this->db->query($sql, array());

        // log_message('error',$this->db->last_query());
        return $result->result();
    }
}
