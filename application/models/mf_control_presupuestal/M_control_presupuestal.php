<?php
class M_control_presupuestal extends CI_Model{
	//http://www.codeigniter.com/userguide3/database/results.html
	function __construct(){
		parent::__construct();
		
    }

    function getBandejaControlPresupuestal($situacion, $area, $itemplan) {
        $Query = "SELECT t.*,
							CASE WHEN  t.origen = 5 AND costo_pqt <> costoActualPo THEN FORMAT(ROUND(costo_pqt + costoActualPo, 2),2)
								WHEN  t.origen = 5 AND costo_pqt = costoActualPo THEN FORMAT(costo_pqt,2)
								ELSE t.costo_final_nf END AS costo_final_f,
							CASE WHEN  t.origen = 5 AND costo_pqt <> costoActualPo THEN costo_pqt
								WHEN  t.origen = 5 AND costo_pqt = costoActualPo  THEN 0
								ELSE FORMAT(ROUND(t.costo_final_nf - costoActualPo, 2),2) END excesoPo
					FROM (
							SELECT po.costo_unitario_mo,
									so.id_solicitud,
									p.proyectoDesc,
									ep.estadoPlanDesc,
									so.itemplan, sp.subProyectoDesc,
								(CASE   WHEN so.tipo_po = 1 THEN 'MATERIAL'
										WHEN so.tipo_po = 2 THEN 'MO' END) as tipo_po, 
								FORMAT(so.costo_inicial,2)      as costo_inicial, 
								FORMAT(so.exceso_solicitado,2)  as exceso_solicitado, 
								FORMAT(so.costo_final,2)        as costo_final,
								CASE WHEN so.origen = 5 THEN (SELECT montoFinal 
																FROM solicitud_exceso_obra_detalle_liqui sl
																WHERE sl.id_solicitud = so.id_solicitud)
										ELSE 0  END AS costo_pqt,
								so.costo_final as costo_final_nf,
								UPPER(CONCAT(u_sol.nombre_completo,' ', u_sol.ape_paterno,' ', u_sol.ape_materno)) AS usua_solicita, 
								DATE(so.fecha_solicita) AS fecha_solicita,
								UPPER(CONCAT(u_val.nombre_completo,' ', u_val.ape_paterno,' ', u_val.ape_materno)) AS usua_valida, 
								DATE(so.fecha_valida) AS fecha_valida,
								(CASE   WHEN so.estado_valida IS NULL THEN 'PENDIENTE'
										WHEN so.estado_valida = 1 THEN 'APROBADO'
										WHEN so.estado_valida = 2 THEN 'RECHAZADO' END) as situacion,
								so.origen,
								CASE WHEN so.origen = 1 THEN 'REG PO MAT'
										WHEN so.origen = 2 THEN 'REG PO MO'
										WHEN so.origen = 3 THEN 'REG VR'
										WHEN so.origen = 4 THEN 'LIQUI MO' 
										WHEN so.origen = 5 THEN 'ADIC. PQT'
										WHEN so.origen = 6 THEN 'EDIC. PIN'
										END tipo_origen,
								so.idEstacion,
								so.codigo_po,
								e.empresacolabDesc as eecc,
								z.zonalDesc,
								so.url_archivo,
								es.estacionDesc,
								CASE WHEN so.origen = 1 THEN (SELECT ROUND(SUM(cantidadFinal*costoMat),2)
																FROM solicitud_exceso_obra_detalle_reg_mat sm
																WHERE sm.id_solicitud = so.id_solicitud)
										WHEN so.origen = 2 THEN (SELECT ROUND(SUM(baremo*preciario*cantidadFinal),2) 
																FROM solicitud_exceso_obra_detalle_reg_mo sm
																WHERE sm.id_solicitud = so.id_solicitud)
										WHEN so.origen = 3 THEN '-'
										WHEN so.origen = 4 THEN (SELECT ROUND(SUM(baremo*preciario*cantidadFinal),2) 
																FROM solicitud_exceso_obra_detalle_liqui sm
																WHERE sm.id_solicitud = so.id_solicitud) 
									WHEN so.origen = 5 THEN '-'
									END costoPo,
									CASE WHEN so.tipo_po = 1 THEN COALESCE((SELECT ROUND(SUM(costo_total),2)
																				FROM planobra_po ppo
																				WHERE ppo.itemplan = po.itemplan
																				AND so.fecha_solicita >= fechaRegistro
																				AND flg_tipo_area = 1
																				AND ppo.estado_po NOT IN (7,8)), 0)
											WHEN so.tipo_po = 2 THEN COALESCE(( SELECT ROUND(SUM(costo_total),2)
																				FROM planobra_po ppo
																				WHERE ppo.itemplan = po.itemplan
																					AND so.fecha_solicita >= fechaRegistro
																					AND flg_tipo_area = 2
																					AND ppo.estado_po NOT IN (7,8)), 0)
											END costoActualPo
						FROM (planobra po,
								proyecto p,
								subproyecto  sp, 
								solicitud_exceso_obra so,
								empresacolab e,
								zonal z,
								estacion es,
								estadoplan ep)
						LEFT JOIN  usuario u_sol on so.usuario_solicita =  u_sol.id_usuario
						LEFT JOIN  usuario u_val on so.usuario_valida 	=  u_val.id_usuario
						WHERE 
						po.idSubProyecto  =  sp.idSubProyecto
						and  po.itemplan  =  so.itemplan
						AND p.idProyecto  = sp.idProyecto
						AND z.idZonal     = po.idZonal
						AND po.idEstadoPlan = ep.idEstadoPlan
						AND so.idEstacion = es.idEstacion
						and e.idEmpresaColab = po.idEmpresaColab";
            if($situacion!=null){//SITUACION
                if($situacion   ==  0){//PENDIENTE
                    $Query .= " AND  so.estado_valida is null";
                }else if($situacion   ==  1){//APROBADA
                    $Query .= " AND  so.estado_valida = 1";
                }else if($situacion   ==  2){//VALIDADA
                    $Query .= " AND  so.estado_valida = 2";
                }
            }
            if($area!=null){
                if($area   ==  1){//MATERIAL
                    $Query .= " AND  so.tipo_po =1";
                }else if($area   ==  2){//MO
                    $Query .= " AND  so.tipo_po = 2";
                }
            }
            if($itemplan!=null){
                $Query .= " AND so.itemplan = '".$itemplan."'";
            }
			
			$Query .= ')t';
        $result = $this->db->query($Query, array());
		//log_message('error', $this->db->last_query());
		return $result->result_array();
    }
    
    function getInfoObraByIdSolicitud($idSolicitud){
        $Query = 'SELECT se.itemplan,
						 po.indicador,
		                 se.tipo_po, 
						 po.costo_unitario_mat, 
						 po.costo_unitario_mo, 
						 po.costo_unitario_mo_crea_oc, 
						 po.idEstadoPlan, 
						 se.genSolEdic, 
						 se.isFerreteria,
						 s.idProyecto,
						 po.idEmpresaColab,
						 po.idSubProyecto
                    FROM
                        planobra po,
                        solicitud_exceso_obra se,
						subproyecto s
                    WHERE po.itemplan = se.itemplan
					  AND se.id_solicitud = ?
					  AND s.idSubProyecto = po.idSubProyecto
					LIMIT 1';
        $result = $this->db->query($Query,array($idSolicitud));
        if($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
    }
        
    function registrarSolicitudCP($dataInsert){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
        	// $this->db->trans_begin();
            $this->db->insert('solicitud_exceso_obra', $dataInsert);
            if($this->db->affected_rows() != 1) {
                $this->db->trans_rollback();
                throw new Exception('Error al insertar en solicitud_exceso_obra');
            }else{               
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizo correctamente!';
				$data['id_solicitud'] = $this->db->insert_id();
                // $this->db->trans_commit();            
            }
        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }
	
	function regDetalleLiquiMo($dataDetalleSolicitud) {
		$this->db->insert_batch('solicitud_exceso_obra_detalle_liqui', $dataDetalleSolicitud);
		if($this->db->affected_rows() > 0) {
			$data['error'] = EXIT_SUCCESS;
			$data['msj'] = 'Se actualizo correctamente!';
		}else{
			$data['error'] = EXIT_ERROR;
			$data['msj'] = 'Error al insertar la solicitud.';
		}
		return $data;
	}
	
	function regDetalleRegPo($dataDetalleSolicitud) {
		$this->db->insert_batch('solicitud_exceso_obra_detalle_reg_mat', $dataDetalleSolicitud);
		//log_message('error', $this->db->last_query());
		if($this->db->affected_rows() > 0) {
			$data['error'] = EXIT_SUCCESS;
			$data['msj'] = 'Se actualizo correctamente!';
		}else{
			$data['error'] = EXIT_ERROR;
			$data['msj'] = 'Error al insertar la solicitud.';
		}
		return $data;
	}
	
	function regDetalleVr($dataDetalleSolicitud) {
		//log_message('error', print_r($dataDetalleSolicitud,true));
		$this->db->insert_batch('solicitud_exceso_obra_detalle_vr', $dataDetalleSolicitud);
		//log_message('error', $this->db->last_query());
		if($this->db->affected_rows() > 0) {
			$data['error'] = EXIT_SUCCESS;
			$data['msj'] = 'Se actualizo correctamente!';
		}else{
			$data['error'] = EXIT_ERROR;
			$data['msj'] = 'Error al insertar la solicitud.';
		}
		return $data;
	}
    
	function regDetallePoMo($dataDetalleSolicitud) {
		$this->db->insert_batch('solicitud_exceso_obra_detalle_reg_mo', $dataDetalleSolicitud);
		if($this->db->affected_rows() > 0) {
			$data['error'] = EXIT_SUCCESS;
			$data['msj'] = 'Se actualizo correctamente!';
		}else{
			$data['error'] = EXIT_ERROR;
			$data['msj'] = 'Error al insertar la solicitud.';
		}
		return $data;
	}
	
	function getDataSolicitudLiqui($id_solicitud) {
		$sql = "SELECT pa.codigoPartida,
						UPPER(pa.descripcion) descripcion,
						s.baremo,
						s.preciario,
						s.cantidadFinal,
						CASE WHEN pmo.cantidadFinal IS NULL THEN 0
							ELSE pmo.cantidadFinal END as cantidad_actual,
						CASE WHEN pmo.cantidadFinal IS NULL THEN 0 
							ELSE ROUND(pmo.baremo*pmo.preciario*pmo.cantidadFinal, 2) END total_actual,
						ROUND(s.baremo*s.preciario*s.cantidadFinal, 2) total_partida,
						so.comentario_reg
				FROM (solicitud_exceso_obra_detalle_liqui s,
						partida pa,
						solicitud_exceso_obra so)
				LEFT JOIN planobra_po_detalle_mo pmo ON (s.codigo_po = pmo.codigo_po AND pmo.codigoPartida = s.codigoPartida)
				WHERE s.codigoPartida = pa.codigoPartida
					AND so.id_solicitud = s.id_solicitud
					AND s.id_solicitud = ?
				
				UNION ALL
				
				SELECT pa.codigoPartida,
						UPPER(pa.descripcion) descripcion,
						pmo.baremo,
						pmo.preciario,
						s.cantidadFinal,
						CASE WHEN pmo.cantidadFinal IS NULL THEN 0
							ELSE pmo.cantidadFinal END as cantidad_actual,
						CASE WHEN pmo.cantidadFinal IS NULL THEN 0 
							ELSE ROUND(pmo.baremo*pmo.preciario*pmo.cantidadFinal, 2) END total_actual,
						ROUND(s.baremo*s.preciario*s.cantidadFinal, 2) total_partida,
						so.comentario_reg
				FROM (planobra_po_detalle_mo pmo,
						partida pa)
				LEFT JOIN (solicitud_exceso_obra_detalle_liqui s,
						solicitud_exceso_obra so) 
					ON (s.codigo_po = pmo.codigo_po AND pmo.codigoPartida = s.codigoPartida AND so.id_solicitud = s.id_solicitud)
				WHERE pmo.codigoPartida = pa.codigoPartida
					AND pmo.codigo_po = (SELECT codigo_po FROM solicitud_exceso_obra_detalle_liqui WHERE id_solicitud = ? limit 1)
					AND s.id_solicitud IS NULL";
		$result = $this->db->query($sql, array($id_solicitud, $id_solicitud));
		return $result->result_array();
	}
	
	function getDataSolicitudRegMat($id_solicitud) {
		$sql = "SELECT 	se.codigo_material,
						se.cantidadInicial,
						se.cantidadFinal,
						se.costoMat,
						ROUND(se.cantidadFinal*se.costoMat, 2) total_mat,
						UPPER(m.descrip_material)	as descrip_material,
						s.comentario_reg
				FROM 	solicitud_exceso_obra_detalle_reg_mat se,
						solicitud_exceso_obra s,
						material m
				WHERE 	se.id_solicitud 	= s.id_solicitud 
				AND 	se.codigo_material 	= m.codigo_material
				AND 	se.id_solicitud 	= ?";
		$result = $this->db->query($sql, array($id_solicitud));
		return $result->result_array();
	}
	
	function getDataSolicitudRegMo($id_solicitud) {
		$sql ="SELECT pa.codigoPartida,
					se.id_solicitud,
					se.cantidadFinal,
					se.preciario,
					se.baremo,
					ROUND(se.cantidadFinal*se.baremo*se.preciario, 2) total_partida,
					UPPER(pa.descripcion) as descripcion,
					s.comentario_reg
			FROM solicitud_exceso_obra_detalle_reg_mo se,
					solicitud_exceso_obra s,
					partida pa
			WHERE se.id_solicitud = s.id_solicitud 
				AND pa.codigoPartida  = se.codigoPartida
				AND se.id_solicitud =  ?";
		$result = $this->db->query($sql, array($id_solicitud));
		return $result->result_array();
	}
	
	function getDataSolicitudVr($id_solicitud) {
		$sql = "SELECT se.id_solicitud,
					se.ptr,
					se.itemplan,
					pd.codigo_material,
					CASE WHEN pd.cantidadFinal IS NULL THEN 0 ELSE pd.cantidadFinal END as cant_actual,
					(pd.cantidadFinal*pd.costoMat) as totalPo,
					se.cantidadInicio as cantidadIngresado,
					se.cantidadFin,
					(se.cantidadFin*pd.costoMat) as totalSolVr,
					se.vr,
					UPPER(ma.descrip_material)as descrip_material,
					s.comentario_reg,
					pd.costoMat,				 
					t.nombreTipoSolicitud AS desc_tipo_solicitud
			FROM (planobra_po_detalle_mat pd, 
					material ma)
			LEFT JOIN (solicitud_exceso_obra_detalle_vr se, 
					solicitud_exceso_obra s,
					tipo_solicitud_vr t) ON (se.ptr = pd.codigo_po 
												AND se.id_solicitud = ?
												AND se.codigoMaterial = pd.codigo_material
												AND se.idTipoSolicitudVr = t.id
												AND s.id_solicitud = se.id_solicitud)
			WHERE ma.codigo_material = pd.codigo_material
				AND pd.codigo_po = (SELECT ptr FROM solicitud_exceso_obra_detalle_vr WHERE id_solicitud = ? limit 1)
			
			UNION ALL
			
			SELECT se.id_solicitud,
					se.ptr,
					se.itemplan,
					se.codigoMaterial as cod_material,
					CASE WHEN pd.cantidadFinal IS NULL THEN 0 ELSE pd.cantidadFinal END as cant_actual,
					(pd.cantidadFinal*pd.costoMat) as totalPo,
					se.cantidadInicio as cantidadIngresado,
					se.cantidadFin,
					(se.cantidadFin*se.costoMaterial) as totalSolVr,
					se.vr,
					UPPER(ma.descrip_material)as descrip_material,
					s.comentario_reg,
					se.costoMaterial,				 
					t.nombreTipoSolicitud AS desc_tipo_solicitud
			FROM (solicitud_exceso_obra_detalle_vr se,
					material ma,
					solicitud_exceso_obra s,
					tipo_solicitud_vr t)
			LEFT JOIN planobra_po_detalle_mat pd ON (se.ptr = pd.codigo_po AND se.codigoMaterial = pd.codigo_material) 
			WHERE se.id_solicitud = ?
				AND s.id_solicitud = se.id_solicitud
				AND ma.codigo_material = se.codigoMaterial
				AND se.idTipoSolicitudVr = t.id
				AND pd.codigo_material IS NULL";
		$result = $this->db->query($sql, array($id_solicitud, $id_solicitud, $id_solicitud));
		return $result->result_array();
	}
	
	function getCodPoSolicitudLiqui($id_solicitud) {
		$sql = "SELECT codigo_po 
		          FROM solicitud_exceso_obra_detalle_liqui
				 WHERE id_solicitud = ?
				 limit 1";
		$result = $this->db->query($sql, array($id_solicitud));
		return $result->row_array()['codigo_po'];
	}
	
	function getDataDetalleLiqui($id_solicitud) {
        $Query = " SELECT 	codigo_po,
							codigoPartida,
                            baremo,
                            preciario,
                            cantidadInicial,
                            montoInicial,
                            cantidadFinal,
                            montoFinal 
                     FROM solicitud_exceso_obra_detalle_liqui
                    WHERE id_solicitud = ?";
          
        $result = $this->db->query($Query, array($id_solicitud));
        return $result->result_array();           
    }
	
	function getDataDetalleRegMo($id_solicitud) {
        $Query = " SELECT codigoPartida,
                          baremo,
                          preciario,
                          cantidadInicial,
                          montoInicial,
                          cantidadFinal,
                          montoFinal 
                     FROM solicitud_exceso_obra_detalle_reg_mo
                    WHERE id_solicitud = ?";
          
        $result = $this->db->query($Query, array($id_solicitud));
        return $result->result_array();           
    }
	
	function getDataDetalleRegMat($id_solicitud) {
		$Query = " SELECT * 
                     FROM solicitud_exceso_obra_detalle_reg_mat
                    WHERE id_solicitud = ?
					  AND cantidadFinal <> 0";
          
        $result = $this->db->query($Query, array($id_solicitud));
        return $result->result_array(); 
	}
	
	function getDataDetalleVr($id_solicitud) {
		$Query = " SELECT *
                     FROM solicitud_exceso_obra_detalle_vr
                    WHERE id_solicitud = ?";
          
        $result = $this->db->query($Query, array($id_solicitud));
        return $result->result_array();
	}
	
	function updateEstadoSolicitudLiqui($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $codigo_po, $costoTotal,$flgCostoUnMayo=null){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            // $this->db->trans_begin();
            $data = $this->aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $flgCostoUnMayo);
			
            if($data['error'] == EXIT_SUCCESS) {
                $sql = "  DELETE ppd
                            FROM planobra_po_detalle_mo ppd
                           WHERE codigo_po = ?";
                $this->db->query($sql, array($codigo_po));
                if($this->db->trans_status() === TRUE) {
                    $this->db->where('codigo_po', $codigo_po);
                    $this->db->update('planobra_po', array('costo_total' => $costoTotal));					
                    if($this->db->affected_rows() != 1) {
                        $this->db->trans_rollback();
                        throw new Exception('Error al actualizar la informacion.');
                    } else {
                        $dataArray = $this->getDataDetalleLiqui($idSolicitud);
                        foreach($dataArray as $row) {                            
                            $this->db->where('codigo_po' , $codigo_po);              
                            $this->db->insert('planobra_po_detalle_mo', array( 'codigo_po'       => $row['codigo_po'],
                                                                               'codigoPartida' 	 => $row['codigoPartida'],
																			   'baremo' 	 	 => $row['baremo'],
																			   'preciario' 		 => $row['preciario'],
																			   'cantidadInicial' => $row['cantidadInicial'],
																			   'cantidadFinal'   => $row['cantidadFinal'],
																			   'montoFinal' 	 => $row['montoFinal']));
                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                throw new Exception('Error al actualizar la informacion.');
                            } else {
                                $data['error'] = EXIT_SUCCESS;
                                $data['msj'] = 'Se actualizo correctamente!';
                                // $this->db->trans_commit();							
                            }
                            
                        }
                    }
                } else {
                    $this->db->trans_rollback();
                    throw new Exception('Error al actualizar la informacion.');
                }
            } else {
                $this->db->trans_rollback();
                throw new Exception('Error al actualizar la informacion.');
            }
        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }
	
	function updateEstadoSolicitudRegMo($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud,
										$dataPO, $dataLogPO, $codigo_po, $flgCostoUnMayo=null) {
		$data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            // $this->db->trans_begin();
            $data = $this->aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $flgCostoUnMayo);
            if($data['error'] == EXIT_SUCCESS) {
				$this->db->insert('planobra_po', $dataPO);
				if($this->db->affected_rows() != 1) {
					$this->db->trans_rollback();
					throw new Exception('Error al insertar en Planobra Po');
				}else{
					$this->db->insert('log_planobra_po', $dataLogPO);
					if($this->db->affected_rows() != 1) {
						$this->db->trans_rollback();
						throw new Exception('Error al insertar en log_planobra_po');
					}else{					 
							$dataArray = $this->getDataDetalleRegMo($idSolicitud);
							foreach($dataArray as $row) {             
							$this->db->insert('planobra_po_detalle_mo', array( 'codigo_po'        => $codigo_po,
																				'codigoPartida'   => $row['codigoPartida'],
																				'baremo' 	 	  => $row['baremo'],
																				'preciario' 	  => $row['preciario'],
																				'cantidadInicial' => $row['cantidadInicial'],
																				'cantidadFinal'   => $row['cantidadFinal'],
																				'montoInicial'    => $row['montoInicial'],
																				'montoFinal' 	  => $row['montoFinal']));
							if ($this->db->trans_status() === FALSE) {
								$this->db->trans_rollback();
								throw new Exception('Error al actualizar la informacion.');
							} else {
								$data['error'] = EXIT_SUCCESS;
								$data['msj'] = 'Se actualizo correctamente!';
								// $this->db->trans_commit();
							}
						}
					}
				} 
            } else {
                $this->db->trans_rollback();
                throw new Exception('Error al actualizar la informacion.');
            }
        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
	}
	
	function updateEstadoSolicitudRegMat($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud,
										$dataPO, $dataLogPO, $codigo_po) {
		$data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            // $this->db->trans_begin();
            $data = $this->aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud);

            if($data['error'] == EXIT_SUCCESS) {
				$this->db->insert('planobra_po', $dataPO);
				if($this->db->affected_rows() != 1) {
					$this->db->trans_rollback();
					throw new Exception('Error al insertar en Planobra Po');
				}else{
					$this->db->insert('log_planobra_po', $dataLogPO);
					if($this->db->affected_rows() != 1) {
						$this->db->trans_rollback();
						throw new Exception('Error al insertar en log_planobra_po');
					}else{
						 
						$dataArray = $this->getDataDetalleRegMat($idSolicitud);
						foreach($dataArray as $row) {             
							$this->db->insert('planobra_po_detalle_mat', array( 'codigo_po'        	=> $codigo_po,
																				'codigo_material'  	=> $row['codigo_material'],
																				'costoMat'   		=> $row['costoMat'],
																				'cantidadInicial' 	=> $row['cantidadInicial'],
																				'cantidadFinal'   	=> $row['cantidadFinal'],
																				'montoFinal'   		=> $row['montoFinal']));
							if ($this->db->trans_status() === FALSE) {
								$this->db->trans_rollback();
								throw new Exception('Error al actualizar la informacion.');
							} else {
								$data['error'] = EXIT_SUCCESS;
								$data['msj'] = 'Se actualizo correctamente!';
 							}							 
						}
					}
				} 
            } else {
                $this->db->trans_rollback();
                throw new Exception('Error al actualizar la informacion.');
            }
        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
	}
	
	function updateEstadoSolicitudVr($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $dataSolVr, $arrayDetalleInsert, $flgCostoUnMayo=null){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            // $this->db->trans_begin();
            $data = $this->aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $flgCostoUnMayo);
            if($data['error'] == EXIT_SUCCESS) {
				$this->db->insert('solicitud_vale_reserva', $dataSolVr);
				if($this->db->affected_rows() != 1) {
					$this->db->trans_rollback();
					throw new Exception('Error al insertar en Planobra Po');
				}else{					 
					$this->db->insert_batch('solicitud_vale_reserva_detalle', $arrayDetalleInsert);
					if($this->db->affected_rows() <= 0) {
						$data['msj'] = 'Hubo un error al insertar en la tabla solicitud_vale_reserva_detalle';
						$data['error'] = EXIT_ERROR;
					} else {
						$data['msj'] = 'Se registró correctamente la solicitud de VR';
						$data['error'] = EXIT_SUCCESS;   
					}
				}				 
            } else {
                $this->db->trans_rollback();
                throw new Exception('Error al actualizar la informacion.');
            }
        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }
	
	function aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $flgCostoUnMayor = null){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            // $this->db->trans_begin();
						log_message('error','aqui3!!');

            $this->db->where('id_solicitud', $idSolicitud);
	        $this->db->update('solicitud_exceso_obra', $dataUpdateSolicitud);	
log_message('error',$this->db->last_query());			
            if($this->db->affected_rows() != 1) {
                $this->db->trans_rollback();
                throw new Exception('Error al actualizar la informacion.');
            }else{
				if($flgCostoUnMayor == 1) {log_message('error','aqui5!!');
					$data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se actualizo correctamente!';
				} else {	log_message('error','aqui4!!');
					$this->db->where('itemplan', $itemplan);
					$this->db->update('planobra', $dataItemplan);
					if($this->db->trans_status() === FALSE) {
						$this->db->trans_rollback();
						throw new Exception('Error al actualizar la informacion.');
					}else{log_message('error','aqui6!');
						$data['error'] = EXIT_SUCCESS;
						$data['msj'] = 'Se actualizo correctamente!';
						// $this->db->trans_commit();
					}
				}
            }
        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }
	
	// function aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud){
		// $this->db->where('id_solicitud', $idSolicitud);
		// $this->db->update('solicitud_exceso_obra', $dataUpdateSolicitud);	          
		// if($this->db->affected_rows() != 1) {
			// $data['error'] = EXIT_ERROR;
			// $data['msj'] = 'Error al actualizar la informacion.';
		// }else{
			// $this->db->where('itemplan', $itemplan);
			// $this->db->update('planobra', $dataItemplan);
			// if($this->db->affected_rows() != 1) {
			   // $data['error'] = EXIT_ERROR;
				// $data['msj'] = 'Error al actualizar la informacion.';
			// }else{
				// $data['error'] = EXIT_SUCCESS;
				// $data['msj'] = 'Se actualizo correctamente!';
			// }
		// }
        // return $data;
    // }
	
	function rejectSolicitud($dataUpdateSolicitud, $idSolicitud){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            // $this->db->trans_begin();
            $this->db->where('id_solicitud', $idSolicitud);
            $this->db->update('solicitud_exceso_obra', $dataUpdateSolicitud);
            if($this->db->affected_rows() != 1) {
                $this->db->trans_rollback();
                throw new Exception('Error al actualizar la informacion.');
            }else{              
                $data['error'] = EXIT_SUCCESS;
                $data['msj'] = 'Se actualizo correctamente!';
                // $this->db->trans_commit();
            }
        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
    }
	
	function getDataPlanObra($itemplan){
	    $Query = "SELECT   po.idEmpresaColabDiseno, 
	                       c.idEmpresaColab,
						   c.idEmpresaColabFuente,
						   c.jefatura,
						   po.idSubProyecto
	               FROM    planobra po, 
	                       central c 
	               WHERE   po.idCentral    = c.idCentral
	               AND     po.itemplan     = ?
                   AND	   po.paquetizado_fg is null                   
                   UNION ALL                   
                   SELECT  po.idEmpresaColabDiseno, 
	                       c.idEmpresaColab,
						   c.idEmpresaColabFuente,
						   c.jefatura,
						   po.idSubProyecto
	               FROM    planobra po, 
	                       pqt_central c 
	               WHERE   po.idCentralPqt    = c.idCentral
	               AND     po.itemplan     = ?
                   AND	   po.paquetizado_fg IN (1,2)";
	    $result = $this->db->query($Query,array($itemplan, $itemplan));
	    return $result->row_array();
	}
	
	function getDataPlanObraToPo($itemplan){
	    $Query = "SELECT   po.idEmpresaColabDiseno, 
	                       po.idEmpresaColab,
						   c.idEmpresaColabFuente,
						   c.jefatura,
						   po.idSubProyecto
	               FROM    planobra po, 
	                       central c 
	               WHERE   po.idCentral    = c.idCentral
	               AND     po.itemplan     = ?
                   AND	   po.paquetizado_fg is null                   
                   UNION ALL                   
                   SELECT  po.idEmpresaColabDiseno, 
	                       po.idEmpresaColab,
						   c.idEmpresaColabFuente,
						   c.jefatura,
						   po.idSubProyecto
	               FROM    planobra po, 
	                       pqt_central c 
	               WHERE   po.idCentralPqt    = c.idCentral
	               AND     po.itemplan     = ?
                   AND	   po.paquetizado_fg IN (1,2)";
	    $result = $this->db->query($Query,array($itemplan, $itemplan));
	    return $result->row_array();
	}
	
	/***pqt czavala 22.06.2020**/
	
	function updateEstadoSolicitudLiquiAdocPqt($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $codigo_po, $isFerreteria, $flgCostoUnMayor=null){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        // $this->db->trans_begin();
	        $data = $this->aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $flgCostoUnMayor);	
	        if($data['error'] == EXIT_SUCCESS) {
	            $sql = "  DELETE ppd
                            FROM planobra_po_detalle_mo ppd JOIN partidas pa ON ppd.idActividad = pa.idActividad
                           WHERE ppd.codigo_po = ?
	                        AND pa.flg_tipo not in (3)
	                        AND ppd.idActividad ".(($isFerreteria) ? '' : 'NOT')." IN (".ID_PARTIDA_FERRETERIA.")";
	            $this->db->query($sql, array($codigo_po));
	            
	            if($this->db->trans_status() === TRUE) {
	                    $dataArray = $this->getDataDetalleLiqui($idSolicitud);
	                    foreach($dataArray as $row) {
	
	                        $this->db->where('codigo_po' , $codigo_po);
	                        $this->db->insert('planobra_po_detalle_mo', array( 'codigo_po'        => $row['codigo_po'],
	                            'idActividad' 	  => $row['idActividad'],
	                            'baremo' 	 	  => $row['baremo'],
	                            'costo' 		 	  => $row['costo'],
	                            'cantidad_inicial' => $row['cantidad_inicial'],
	                            'cantidad_final'   => $row['cantidad_final'],
	                            'monto_final' 	  => $row['monto_final']));
	                        if ($this->db->trans_status() === FALSE) {
	                            $this->db->trans_rollback();
	                            throw new Exception('Error al actualizar la informacion.');
	                        } else {
	                            $sql = "update planobra_po set costo_total = (select sum(monto_final) 
	                                    from planobra_po_detalle_mo where codigo_po = ?)
	                                    where codigo_po = ?";
                	                       $this->db->query($sql, array($codigo_po, $codigo_po));
            	
            	                if($this->db->trans_status() === FALSE) {
            	                    $this->db->trans_rollback();
            	                    throw new Exception('Error al actualizar la informacion.');
            	                } else {$data['error'] = EXIT_SUCCESS;
    	                            $data['msj'] = 'Se actualizo correctamente!';
    	                            // $this->db->trans_commit();
            	                }  
	                        }
	
	                    }
	               }
	             //   }	           
	        } else {
	            $this->db->trans_rollback();
	            throw new Exception('Error al actualizar la informacion.');
	        }
	    }catch(Exception $e){
	        $data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $data;
	}
	
	function getDataSolicitudAdicPqt($id_solicitud) {
	    $sql = "SELECT pa.codigo,
					   UPPER(pa.descripcion) descripcion,
					   s.baremo,
					   s.costo,
					   s.cantidad_final,
                       CASE WHEN pmo.cantidad_final IS NULL THEN 0
					        ELSE pmo.cantidad_final END as cantidad_actual,
                       CASE WHEN pmo.cantidad_final IS NULL THEN 0
					        ELSE ROUND(pmo.baremo*pmo.costo*pmo.cantidad_final, 2) END total_actual,
					   ROUND(s.baremo*s.costo*s.cantidad_final, 2) total_partida,
					   so.comentario_reg
				  FROM (solicitud_exceso_obra_detalle_liqui s,
					   partidas pa,
					   solicitud_exceso_obra so)
			 LEFT JOIN planobra_po_detalle_mo pmo ON (s.codigo_po = pmo.codigo_po AND pmo.idActividad = s.idActividad)
				 WHERE s.idActividad = pa.idActividad
				   AND so.id_solicitud = s.id_solicitud
				   AND s.id_solicitud = ?
	
				  UNION ALL
	
				SELECT pa.codigo,
					   UPPER(pa.descripcion) descripcion,
					   pmo.baremo,
					   pmo.costo,
					   null AS canridad_final,
                       CASE WHEN pmo.cantidad_final IS NULL THEN 0
					        ELSE pmo.cantidad_final END as cantidad_actual,
                       CASE WHEN pmo.cantidad_final IS NULL THEN 0
					        ELSE ROUND(pmo.baremo*pmo.costo*pmo.cantidad_final, 2) END total_actual,
					   null as total_partida,
					   null
				  FROM (planobra_po_detalle_mo pmo,
					   partidas pa)
				 WHERE pmo.idActividad = pa.idActividad
				   AND pmo.codigo_po = (SELECT codigo_po FROM solicitud_exceso_obra_detalle_liqui WHERE id_solicitud = ? limit 1)
				   AND pmo.idACtividad not in (select idACtividad from  solicitud_exceso_obra_detalle_liqui where id_solicitud = ?)";
	    $result = $this->db->query($sql, array($id_solicitud, $id_solicitud, $id_solicitud));
	    return $result->result_array();
	}
	
	function deletTmpFerreteria($itemplan, $idEstacion){
	    $data['error'] = EXIT_ERROR;
	    $data['msj']   = null;
	    try{
	        // $this->db->trans_begin();
	        $this->db->where('itemplan', $itemplan);
	        $this->db->where('idEstacion', $idEstacion);
	        $this->db->delete('itemplan_material_x_estacion_pqt_detalle');
	        if($this->db->trans_status() === FALSE){
	            $this->db->trans_rollback();
	            throw new Exception('Error al elimar itemplan_material_x_estacion_pqt_detalle.');
	        } else {
    	        $this->db->where('itemplan', $itemplan);
    	        $this->db->where('idEstacion', $idEstacion);
    	        $this->db->delete('itemplan_material_x_estacion_pqt');
    	        if($this->db->trans_status() === FALSE){
    	            $this->db->trans_rollback();
    	            throw new Exception('Error al eliminar itemplan_material_x_estacion_pqt.');
    	        } else {
    	            $data['error'] = EXIT_SUCCESS;
    	            $data['msj'] = 'Se actualizo correctamente!';
                    // $this->db->trans_commit();
    	        }
	        }
	    }catch(Exception $e){
	        $data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $data;
	}

	/*
	function getDataSolicitudOc($itemplan) {
		$sql = " SELECT s.estado,
						s.pep1,
						s.pep2,
						s.cesta,
						po.idEmpresaColab,
						po.idSubProyecto,
						po.itemplan,
						s.orden_compra,
						po.costo_unitario_mo
				   FROM solicitud_orden_compra s,
						planobra po
				  WHERE s.codigo_solicitud = po.solicitud_oc
					AND po.itemplan = ?
					AND s.orden_compra IS NOT NULL";
		$result = $this->db->query($sql, array($itemplan));
		return $result->row_array();
	}*/
	
	function getDataSolicitudOc($itemplan) {
		$sql = "SELECT s.estado,
						s.pep1,
						s.pep2,
						s.cesta,
						po.idEmpresaColab,
						po.idSubProyecto,
						po.itemplan,
						s.orden_compra,
						po.costo_unitario_mo,
		                ixs.posicion
				   FROM solicitud_orden_compra s,
					    itemplan_x_solicitud_oc ixs,
						planobra po
				  WHERE s.codigo_solicitud = ixs.codigo_solicitud_oc
                  AND ixs.itemplan = po.itemplan
                  AND po.solicitud_oc = ixs.codigo_solicitud_oc
                  AND s.estado = 2
                  AND po.orden_compra is not null
				  AND po.itemplan = ?
		          LIMIT 1";
		$result = $this->db->query($sql, array($itemplan));
		return $result->row_array();
	}

	function getValidOcEdic($itemplan) {
		$sql = " SELECT COUNT(1) countPendiente
				   FROM solicitud_orden_compra s,
						itemplan_x_solicitud_oc i
				  WHERE s.codigo_solicitud = i.codigo_solicitud_oc
					AND i.itemplan = ?
					AND s.tipo_solicitud = 2
					AND estado = 1";
		$result = $this->db->query($sql, array($itemplan));
		return $result->row_array()['countPendiente'];
	}

	function insertSolicitudOcEdi($arrayData, $fecha, $cod_solicitud, $costoFinal, $itemplan) {
		try {
			$idUsuario = $this->session->userdata('idPersonaSession');
			$data['error'] = EXIT_ERROR;
	    	$data['msj']   = null;
			if($arrayData['pep1'] == null || $arrayData['pep1'] == '' || strlen($arrayData['pep1']) == 0 ){
				throw new Exception("Hubo un error al recibir la pep1.");
			}
			$sql = "INSERT INTO solicitud_orden_compra (codigo_solicitud, orden_compra, idEmpresaColab, estado, fecha_creacion, idSubProyecto, plan, pep1, pep2, tipo_solicitud, 
			                                            estatus_solicitud, cesta, usuario_registro)
					VALUES (?, ?, ?, 1, ?, ?, 'PLAN', ?, ?, 2, 'NUEVO',?, ?);";
			$this->db->query($sql, array($cod_solicitud, $arrayData['orden_compra'], $arrayData['idEmpresaColab'], $fecha, $arrayData['idSubProyecto'], 
										 $arrayData['pep1'], $arrayData['pep2'], $arrayData['cesta'], $idUsuario));
										
			if($this->db->affected_rows() > 0) {
				$sql = "INSERT INTO itemplan_x_solicitud_oc(itemplan, codigo_solicitud_oc, costo_unitario_mo, posicion)
						VALUES (?, ?, ?, ?)";
				$this->db->query($sql, array($arrayData['itemplan'], $cod_solicitud, $costoFinal, $arrayData['posicion']));

				if($this->db->affected_rows() < 1) {
					throw new Exception("No ingreso la solicitud OC.");
				} else {
					$this->db->where('itemplan', $itemplan);
					$this->db->update('planobra', array(
															'solicitud_oc_dev' => $cod_solicitud,
															'costo_devolucion' => $costoFinal,
															'estado_oc_dev'    => 'PENDIENTE'
														));
					$data['error'] = EXIT_SUCCESS;
    	            $data['msj']   = 'Se ingreso correctamente!';
				}
			}
		} catch(Exception $e) {
			$data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
		}

		return $data;
	}
	
	function getExcedente($id_solicitud, $pep1) {
		$sql = "  SELECT CASE WHEN (costo_final-costo_inicial) > (SELECT monto_temporal
																	FROM sap_detalle 
																   WHERE pep1 = ?) THEN 0
					          ELSE 1 END flg_presupuesto,
							(costo_final-costo_inicial) excedente
					FROM solicitud_exceso_obra s 
				   WHERE id_solicitud = ?";
		$result = $this->db->query($sql, array($pep1, $id_solicitud));
		return $result->row_array();	  
	}

	function regDetalleEditPin($dataDetalleSolicitud) {
		$this->db->insert_batch('solicitud_exceso_obra_detalle_pin', $dataDetalleSolicitud);
		if($this->db->affected_rows() > 0) {
			$data['error'] = EXIT_SUCCESS;
			$data['msj'] = 'Se actualizo correctamente!';
		}else{
			$data['error'] = EXIT_ERROR;
			$data['msj'] = 'Error al insertar la solicitud.';
		}
		return $data;
	}

	function getCodPoSolicitudPin($id_solicitud) {
		$sql = "SELECT codigo_po 
		          FROM solicitud_exceso_obra_detalle_pin
				 WHERE id_solicitud = ?
				 limit 1";
		$result = $this->db->query($sql, array($id_solicitud));
		return $result->row_array()['codigo_po'];
	}

	function getDataDetallePin($id_solicitud) {
        $Query = " 
					SELECT t.*, 
					       ROUND(costo_mat+costo_mo, 2) as costo_total 
					  FROM (
							SELECT codigo_po,
								idActividad,
								baremo,
								costo,
								cantidad_inicial,
								cantidad_final,
								id_ptr_x_actividades_x_zonal,
								ROUND(costo_kit*cantidad_final,2) as costo_mat,
								ROUND(baremo*costo*cantidad_final,2) as costo_mo
							FROM solicitud_exceso_obra_detalle_pin
							WHERE id_solicitud = ?
							)t";
          
        $result = $this->db->query($Query, array($id_solicitud));
        return $result->result_array();           
    }

	function updateEstadoSolicitudPin($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $flgCostoUnMayor =null){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try{
            // $this->db->trans_begin();
            $data = $this->aprobSolicitud($dataItemplan, $itemplan, $dataUpdateSolicitud, $idSolicitud, $flgCostoUnMayor);

            if($data['error'] == EXIT_SUCCESS) {
				$dataArray = $this->getDataDetallePin($idSolicitud);
				foreach($dataArray as $row) {
					if($row['id_ptr_x_actividades_x_zonal'] == null || $row['id_ptr_x_actividades_x_zonal'] == '') {
						$this->db->insert('ptr_x_actividades_x_zonal', array("ptr"         => $row['codigo_po'],
																			"id_actividad" =>  $row['idActividad'],
																			"cantidad"     => $row['cantidad_final'],
																			"costo_mo"     =>  $row['costo_mo'],
																			"costo_mat"    => $row['costo_mat'],
																			"total"        => $row['costo_total'],
																			"precio"       => $row['costo'],
																			"baremo"       => $row['baremo'],
																			"itemplan"     => $itemplan,
																			"cantidad_final" => $row['cantidad_final'],
																			"cantidad_tdp_tmp" => $row['cantidad_final']));
					} else {
						$this->db->where('id_ptr_x_actividades_x_zonal', $row['id_ptr_x_actividades_x_zonal']);
						$this->db->update('ptr_x_actividades_x_zonal', array(	"ptr"          => $row['codigo_po'],
																				"id_actividad" =>  $row['idActividad'],
																				"cantidad"     => $row['cantidad_final'],
																				"costo_mo"     =>  $row['costo_mo'],
																				"costo_mat"    => $row['costo_mat'],
																				"total"        => $row['costo_total'],
																				"precio"       => $row['costo'],
																				"baremo"       => $row['baremo'],
																				"itemplan"     => $itemplan,
																				"cantidad_final" => $row['cantidad_final'],
																				"cantidad_tdp_tmp" => $row['cantidad_final']));
					}

					if ($this->db->trans_status() === FALSE) {
						$flgValidDetalle = 0;
					} else {
						$flgValidDetalle = 1;
					}
				}

				if ($flgValidDetalle == 0) {
					$this->db->trans_rollback();
					throw new Exception('Error al actualizar la informacion.');
				} else {
					$data['error'] = EXIT_SUCCESS;
					$data['msj'] = 'Se actualizo correctamente!';
					// $this->db->trans_commit();
				}
            } else {
                $this->db->trans_rollback();
                throw new Exception('Error al actualizar la informacion.');
            }
        }catch(Exception $e){
            $data['msj']   = $e->getMessage();
            $this->db->trans_rollback();
        }
        return $data;
	}
	
	function getCountValida($itemplan, $codigo_po) {
		$sql = "SELECT COUNT(1) count
				  FROM solicitud_exceso_obra
				 WHERE itemplan  = ?
				   AND CASE WHEN ? IS NULL THEN true 
				            ELSE codigo_po = ? END
				   AND estado_valida IS NULL";
		$result = $this->db->query($sql, array($itemplan, $codigo_po, $codigo_po));
        return $result->row_array()['count'];     

	}

	function insertSolicitudOcCerti($arrayData, $fecha, $cod_solicitud, $itemplan, $costo_mo, $estadoCerti) {
		try {
			$idUsuario = $this->session->userdata('idPersonaSession');
			
			$data['error'] = EXIT_ERROR;
	    	$data['msj']   = null;

			$sql = "INSERT INTO solicitud_orden_compra (codigo_solicitud, orden_compra, idEmpresaColab, estado, fecha_creacion, idSubProyecto, plan, pep1, pep2, tipo_solicitud, 
														estatus_solicitud, cesta, usuario_registro)
					VALUES (?, ?, ?, ?, ?, ?, 'PLAN', ?, ?, 3, 'NUEVO',?, ?);";
			$this->db->query($sql, array($cod_solicitud, $arrayData['orden_compra'], $arrayData['idEmpresaColab'], $estadoCerti, $fecha, $arrayData['idSubProyecto'], 
										 $arrayData['pep1'], $arrayData['pep2'], $arrayData['cesta'], $idUsuario));
										
			if($this->db->affected_rows() > 0) {
				$sql = "INSERT INTO itemplan_x_solicitud_oc(itemplan, codigo_solicitud_oc, costo_unitario_mo, posicion)
						VALUES (?, ?, ?, ?)";
				$this->db->query($sql, array($arrayData['itemplan'], $cod_solicitud, $costo_mo, $arrayData['posicion']));

				if($this->db->affected_rows() < 1) {
					throw new Exception("No ingreso la solicitud OC.");
				} else {
					$this->db->where('itemplan', $itemplan);
					$this->db->update('planobra', array(
															'solicitud_oc_certi'      => $cod_solicitud,
															'costo_unitario_mo_certi' => $costo_mo
														));
					$data['error'] = EXIT_SUCCESS;
    	            $data['msj']   = 'Se ingreso correctamente!';
				}
			}
		} catch(Exception $e) {
			$data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
		}

		return $data;
	}

	function getDataSolicitudPin($id_solicitud) {
		$sql = " SELECT DISTINCT
		                pa.codigo,
						UPPER(pa.descripcion) descripcion,
						UPPER(pa.descripcion) descripcion,
						s.baremo,
						s.costo,
						s.cantidad_final,
						ppi.cantidad_final as cantidad_actual,
						CASE WHEN s.cantidad_final IS NULL THEN 0 
							 ELSE ROUND(s.baremo*s.costo*s.cantidad_final, 2) END total_actual_mo,
						CASE WHEN s.cantidad_final IS NULL THEN 0 
							 ELSE ROUND(s.costo_kit*s.cantidad_final, 2) END total_actual_mat,
						(ROUND(s.baremo*s.costo*COALESCE(s.cantidad_final,0), 2) + ROUND(s.costo_kit*COALESCE(s.cantidad_final, 0), 2))total_partida,

						so.comentario_reg
				FROM (solicitud_exceso_obra_detalle_pin s,
					  partidas pa,
					  solicitud_exceso_obra so)
			LEFT JOIN ptr_x_actividades_x_zonal ppi
				   ON (s.codigo_po = ppi.ptr
					  AND s.idActividad = ppi.id_actividad)
				WHERE s.idActividad = pa.idActividad
				  AND so.id_solicitud = s.id_solicitud
				  AND s.id_solicitud = ?";
		$result = $this->db->query($sql, array($id_solicitud));
		return $result->result_array();
	}
	
	function insertSolicitudOcEdiCertiOpex($arraySolicitud, $arrayItemXSolicitud, $dataPlanObra, $itemplan) {
		try {
			$data['error'] = EXIT_ERROR;
	    	$data['msj']   = null;

			$this->db->insert('itemplan_solicitud_orden_compra', $arraySolicitud);
			if($this->db->affected_rows() > 0) {
				$this->db->insert('itemplan_x_solicitud_oc', $arrayItemXSolicitud);

				if($this->db->affected_rows() < 1) {
					throw new Exception("No ingreso la solicitud OC.");
				} else {
					$this->db->where('itemplan', $itemplan);
					$this->db->update('planobra', $dataPlanObra);
					$data['error'] = EXIT_SUCCESS;
    	            $data['msj']   = 'Se ingreso correctamente!';
				}
			}
		} catch(Exception $e) {
			$data['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
		}

		return $data;
	}

	function getCodPoSolicitudVR($id_solicitud) {
		$sql = "SELECT ptr, vr, idJefaturaSap
		          FROM solicitud_exceso_obra_detalle_vr
				 WHERE id_solicitud = ?
				 limit 1";
		$result = $this->db->query($sql, array($id_solicitud));
		if ($result->row() != null) {
            return $result->row_array();
        } else {
            return null;
        }
	}
	
}