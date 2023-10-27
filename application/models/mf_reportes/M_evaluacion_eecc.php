<?php

class M_evaluacion_eecc extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    }
 
    function getNivelAltoEvaluacionContratas($contrata, $anio, $mes, $check_tm) {

		$strFormat = '%Y-%m';
		$string = '"';
		
        $Query="SELECT tb2.*,
                    ROUND((tb2.dentro_sla_coti*100/tb2.total_ejecucion_coti)) AS eficiencia_coti,
                    ROUND((tb2.dentro_sla_ejecucion*100/tb2.total_ejecucion)) AS eficiencia_dise,
                    ROUND((tb2.dentro_sla_operaciones*100/tb2.total_operaciones)) AS eficiencia_opera,
                    ROUND((tb2.dentro_sla_expe*100/tb2.total_ejecucion_expe)) AS eficiencia_expe
                    FROM (
                    SELECT tb.proyectoDesc,
                    SUM(CASE WHEN  tipo = 4 THEN 1 ELSE 0 END) as total_ejecucion_coti,
                    SUM(CASE WHEN  tipo = 4 AND dentro_sla = 1 THEN 1 ELSE 0 END) as dentro_sla_coti,
                    SUM(CASE WHEN  tipo = 4 AND fuera_sla = 1 THEN 1 ELSE 0 END) as fuera_sla_coti,
                    SUM(CASE WHEN  tipo = 1 THEN 1 ELSE 0 END) as total_ejecucion,
                    SUM(CASE WHEN  tipo = 1 AND dentro_sla = 1 THEN 1 ELSE 0 END) as dentro_sla_ejecucion,
                    SUM(CASE WHEN  tipo = 1 AND fuera_sla = 1 THEN 1 ELSE 0 END) as fuera_sla_ejecucion,
                    SUM(CASE WHEN  tipo = 2 THEN 1 ELSE 0 END) as total_operaciones,
                    SUM(CASE WHEN  tipo = 2 AND dentro_sla = 1 THEN 1 ELSE 0 END) as dentro_sla_operaciones,
                    SUM(CASE WHEN  tipo = 2 AND fuera_sla = 1 THEN 1 ELSE 0 END) as fuera_sla_operaciones,
                    SUM(CASE WHEN  tipo = 3 THEN 1 ELSE 0 END) as total_ejecucion_expe,
                    SUM(CASE WHEN  tipo = 3 AND dentro_sla = 1 THEN 1 ELSE 0 END) as dentro_sla_expe,
                    SUM(CASE WHEN  tipo = 3 AND fuera_sla = 1 THEN 1 ELSE 0 END) as fuera_sla_expe
                    
                    FROM (
                    SELECT 4 as tipo, tb.proyectoDesc,tb.empresaColabDesc, tb.jefatura,
										tb.itemplan,
										(CASE WHEN tb.cod_cl IS NOT NULL THEN 1 ELSE 
											(CASE WHEN tb.dias_coti <= tb.sla_cotizacion THEN 1 ELSE 0 END)
										END )AS dentro_sla,
										(CASE WHEN tb.cod_cl IS NOT NULL THEN 0 ELSE 
											(CASE WHEN tb.dias_coti > tb.sla_cotizacion THEN 1 ELSE 0 END) 
										END )AS fuera_sla,
										'COTIZACION' as origen,
										'-' AS fecha_adjudicacion,
										'-' AS fecha_liquidacion_diseno,
										'-' AS fecha_preliquidacion,
										'-' AS fecha_registro_expediente,
										tb.fecha_registro  AS fecha_reg_coti,
										tb.fecha_reg_cotizacion  AS fecha_envio_coti,
										tb.dias_gest AS total_dias,
										'-' AS total_dias_muerto,
										(tb.dias_coti) AS dias_efectivos, 
										tb.sla_cotizacion AS sla,                       
										'-' AS dias_paralizados,
										'-' AS dias_gestion_vr,		
										'-' AS dias_bandeja_exceso,
										'-' AS dias_parada_reloj,
										'-' AS dias_expe_rechazado
								FROM 					
									(			
												SELECT 'SISEGOS' AS proyectoDesc,ec.empresaColabDesc, je.jefaturaDesc AS jefatura,
													poc.codigo_cluster AS itemplan,
													(DATEDIFF(poc.fecha_reg_cotizacion, poc.fecha_registro) - COALESCE(tnc.no_cont,0)) dias_coti,
															sla.sla_cotizacion,
															poc.fecha_registro,
															poc.fecha_reg_cotizacion,
															DATEDIFF(poc.fecha_reg_cotizacion, poc.fecha_registro) dias_gest,
															fs.itemplan as cod_cl
											FROM planobra_cluster poc
									LEFT JOIN forzar_dentro_sla fs 		ON poc.codigo_cluster 	= fs.itemplan AND fs.tipo_evaluacion = 1
									LEFT JOIN empresacolab ec 			ON poc.idEmpresaColab 	= ec.idEmpresaColab
									LEFT JOIN central pqt_c 			ON poc.idCentral 		= pqt_c.idCentral
                                    LEFT JOIN jefatura je				ON pqt_c.idJefatura 	= je.idJefatura									
                                    LEFT JOIN subproyecto spc 			ON spc.idProyecto = 3
                                    LEFT JOIN subproyecto_sla_varios sla ON sla.idSubProyecto = spc.idSubProyecto
										LEFT JOIN (
															SELECT tb3.cod_cluster_2, SUM(tb3.dias_no_contabilizados) as no_cont
															FROM (
																SELECT  tb2.cod_cluster_2, tb2.valor_2, DATEDIFF((MAX(fecha)), (MIN(fecha))) as dias_no_contabilizados,
																count(1) as cont
																FROM (
																	SELECT tb.*,        
																	(
																		CASE WHEN @old_cl != tb.codigo_cluster THEN
																			CASE WHEN mod(tb.valor,2) != 0 then 1 end
																		ELSE 
																			CASE WHEN mod(tb.valor,2) != 0 then (@contador_2 := @contador_2 + 1) else @contador_2 end 
																		END
																	) as valor_2, 
																	(@old_cl2 := tb.codigo_cluster) as cod_cluster_2
																		FROM (	            
																			   SELECT lp.* ,						
																					  CASE WHEN @old_cl != lp.codigo_cluster 
																						   THEN  @contador := 1 ELSE (@contador := @contador + 1) END AS valor,
																					 (@old_cl := lp.codigo_cluster) as cod_cluster
																				FROM log_planobra_cotizacion_sisego lp
																				CROSS JOIN (SELECT @contador := 0) r
																				WHERE lp.estado IN (6,7) 
																				ORDER BY lp.id_log                                        
																	) as tb CROSS JOIN (SELECT @contador_2 := 0) r
																) as tb2 
																GROUP BY tb2.cod_cluster_2, tb2.valor_2
																HAVING cont = 2 
															) as tb3 group by tb3.cod_cluster_2
												)tnc ON poc.codigo_cluster = tnc.cod_cluster_2
                                        WHERE DATE_FORMAT((poc.fecha_reg_cotizacion),".$string."".$strFormat."".$string.") = '" . $anio . "-" . $mes . "'
                                            AND poc.idEmpresaColab =  ".$contrata."
											AND poc.flg_robot = 2
								) tb
								
							
		UNION ALL
		SELECT  er.tipo, p.proyectoDesc, ec.empresaColabDesc, jefaturaDesc as jefatura, po.itemplan, 
		(CASE WHEN (CASE WHEN ".$check_tm."	= 1 THEN er.dias_global ELSE er.dias_efectivos END) <= er.sla THEN 1 ELSE 0 END) as dentro_sla, 
		(CASE WHEN (CASE WHEN ".$check_tm."	= 1 THEN er.dias_global ELSE er.dias_efectivos END) > er.sla THEN 1 ELSE 0 END)as fuera_sla, 
		(CASE WHEN er.tipo = 1 THEN 'DISENO'
			  WHEN er.tipo = 2 THEN 'OPREACIONES'
			  WHEN er.tipo = 3 THEN 'EXPEDIENTE'	END) as origen,
		er.fecha_adjudicacion, er.fecha_ejecucion as fecha_liquidacion_diseno, er.fecha_preliquidacion, er.fecha_registro_expediente, '-' as fecha_reg_coti, '-' as fecha_envio_coti,
		er.dias_global as total_dias, er.dias_muerto_global as total_dias_muerto, er.dias_efectivos, er.sla, er.dias_paralizado, er.dias_gestion_vr, er.dias_bandeja_exceso, er.dias_parada_reloj, er.dias_expe_rechazado 
		FROM eva_eecc_detalle_reporte er, planobra po LEFT JOIN planobra_cluster poc ON po.itemplan = poc.itemplan, subproyecto sp, proyecto p, empresacolab ec, central c, jefatura jc
		where er.itemplan = po.itemplan
		and po.idSubProyecto = sp.idSubProyecto
		and po.idCentral = c.idCentral
        and c.idJefatura = jc.idJefatura
		and sp.idProyecto = p.idProyecto
		and po.idEmpresaColab = ec.idEmpresaColab
		AND po.idEmpresaColab = ".$contrata."	
		AND (CASE WHEN er.tipo = 1 THEN DATE_FORMAT((er.fecha_ejecucion),".$string."".$strFormat."".$string.") = '" . $anio . "-" . $mes . "'
				  WHEN er.tipo = 2 THEN DATE_FORMAT((er.fecha_preliquidacion),".$string."".$strFormat."".$string.") = '" . $anio . "-" . $mes . "'
                  WHEN er.tipo = 3 THEN DATE_FORMAT((er.fecha_registro_expediente),".$string."".$strFormat."".$string.") = '" . $anio . "-" . $mes . "' END)
		AND po.paquetizado_fg IN (1,2)
		AND (CASE WHEN sp.idProyecto = 3 THEN poc.id_tipo_diseno NOT IN (8,9) ELSE TRUE END)
		AND sp.idSubProyecto != 693
		AND er.sla is not null
		) as tb 
		group by 1
		) AS tb2;";
		
        $result = $this->db->query($Query);
		log_message('error', $this->db->last_query());
        return $result->result();
    }
}
