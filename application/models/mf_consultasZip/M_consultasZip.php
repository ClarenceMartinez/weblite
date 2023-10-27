<?php

class M_consultasZip extends CI_Model {

    //http://www.codeigniter.com/userguide3/database/results.html
    function __construct() {
        parent::__construct();
    }

    function getLicencias($itemPlan) {        
        $sql = "SELECT * 
		          FROM itemplan_estacion_licencia_det 
				 WHERE itemPlan='$itemPlan' 
				   AND ruta_pdf IS NOT NULL";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function getCluster($idOpex) {
        $sql = "SELECT * FROM planobra_cluster WHERE itemPlan='$idOpex'";
        $result = $this->db->query($sql);
        if ($result->row()) {
            return $result->row()->codigo_cluster;
        } else {
            return 0;
        }
    }
  //function getPtrConsultaPqt($itemPlan,$nombreproyecto,$proy,$subPry,$tipoPlanta,$ideecc, $idUsuario=null, $mesEjec=null, $idFase=null){  
    function getPtrConsultaNoPqt($itemPlan,$nombreproyecto,$nodo,$zonal,$proy,$subPry,$estado,$filtroPrevEjec,$tipoPlanta,$ideecc, $idUsuario=null, $mesEjec=null, $idFase=null){
		$Query = "  SELECT (CASE WHEN py.idProyecto = ".ID_PROYECTO_SISEGOS." THEN (SELECT grafo from sisego_pep2_grafo where sisego = p.indicador LIMIT 1) ELSE null END) as grafo, 
						   py.idProyecto, 
						   p.indicador, 
						   p.itemPlan, 
						   c.idCentral, 
						   p.idSubProyecto, 
						   s.subProyectoDesc, 
						   p.nombreProyecto,
						   (CASE WHEN s.idTipoSubProyecto = 2 THEN ( SELECT empresaColabDesc 
																	   FROM empresacolab 
																	  WHERE idEmpresaColab = c.idEmpresaColabCV) ELSE b.empresaColabDesc  END) as empresaColabDesc ,
						    f.faseDesc,
							c.codigo, 
							c.tipoCentralDesc, 
							e.empresaElecDesc, 
							p.fechaInicio, 
							p.fechaPrevEjec, 
							
							(case when (not p.fechaEjecucion is null and p.fechaEjecucion<'2018-07-07') then p.fechaEjecucion
				 else  date(p.fechaPreliquidacion) end) as fechaEjecucion,
							 
							c.idZonal, 
							z.zonalDesc, 
							p.idEstadoPlan, 
							t.estadoPlanDesc, 
							p.hasAdelanto, 
							s.idTipoPlanta,
							(SELECT 1 
							   FROM siom_obra 
							  WHERE itemplan = p.itemplan limit 1) flg_bandeja_siom,
							s.idTipoSubProyecto,
							p.ubic_tss_cv,
							p.ubic_exped_cv,
							p.comentario_cv
					   FROM planobra p,
					        fase f,
							subproyecto s,
							proyecto py, 
							empresaelec e, 
							central c 
							RIGHT JOIN zonal z 
							ON c.idZonal=z.idZonal 
							RIGHT JOIN empresacolab b on c.idEmpresaColab = b.idEmpresaColab, 
							estadoplan t
					  WHERE s.idSubProyecto = p.idSubProyecto 
					    AND p.idFase = f.idFase 
					    AND c.idCentral     = p.idCentral 
						AND e.idEmpresaElec = p.idEmpresaElec 
						AND p.idEstadoPlan  = t.idEstadoPlan 
						AND s.idProyecto    = py.idProyecto
						AND (p.paquetizado_fg is null or p.paquetizado_fg = 1)
						AND CASE WHEN ".$ideecc." = 0 OR ".$ideecc." = 6 THEN c.idEmpresaColab = c.idEmpresaColab
						         WHEN c.idEmpresaColabFuente = ".$ideecc." THEN  c.idEmpresaColabFuente = ".$ideecc."
								WHEN ".$ideecc." IN (SELECT idSubProyecto 
														FROM subproyecto 
													WHERE idProyecto = 5)
								THEN  c.idEmpresaColabCV =  ".$ideecc."               
								 ELSE c.idEmpresaColab = ".$ideecc." END" ;
		if($zonal!=''){
	            if($zonal == 8 || $zonal == 9 || $zonal == 10 || $zonal == 11 || $zonal == 12 ){
	                $Query .= " AND z.idZonal IN (8,9,10,11,12)";
	            }else{
	                $Query .= " AND z.idZonal IN (".$zonal.")";
	            }
        }
		if($proy!=''){
            $Query .= " AND py.idProyecto = ".$proy;
        }
        if($subPry!=''){
            $Query .= " AND s.idSubProyecto = ".$subPry;
        }
		if($itemPlan!=''){
            $Query .= " AND p.itemPlan ='".$itemPlan."' ";
        }
        if($nombreproyecto!=''){
            $Query .= ' AND p.nombreProyecto LIKE "%'.$nombreproyecto.'%"';
            //$Query .= " AND p.nombreProyecto LIKE '%".$nombreproyecto."%' ";
        }
        if($nodo!=''){
            $Query .= " AND c.idCentral = ".$nodo;
        }
        if($estado!=''){
            $Query .= " AND p.idEstadoPlan = ".$estado;
        }        
        if($tipoPlanta!=''){
            $Query .= " AND s.idTipoPlanta = ".$tipoPlanta;
        } 
        if($filtroPrevEjec!=''){
            $Query .= " ".$filtroPrevEjec." ";
		}
		if($mesEjec!=NULL){
            $Query .=  " AND EXTRACT(MONTH FROM DATE(p.fechaPrevEjec)) = $mesEjec";
		}
		if($idUsuario!=NULL) {
			$Query .=  " AND p.itemplan IN ( SELECT l.itemplan
											   FROM log_planobra l
											  WHERE l.tipoPlanta = ".ID_TIPO_PLANTA_INTERNA."
												AND l.actividad  = 'ingresar'  
												AND l.id_usuario = '".$idUsuario."'
												GROUP BY l.itemplan)";
		}
		if($idFase!=''){
            $Query .= " AND p.idFase = ".$idFase;
        }
		$result = $this->db->query($Query,array());	
	    return $result;
	}
	
        
        function getPtrConsultaPqt($itemPlan,$nombreproyecto,$proy,$subPry,$tipoPlanta,$ideecc, $idUsuario=null, $mesEjec=null, $idFase=null){
	
	    //*p.fechaEjecucion,**//
	
	    $Query = "  SELECT (CASE WHEN py.idProyecto = ".ID_PROYECTO_SISEGOS." THEN (SELECT grafo from sisego_pep2_grafo where sisego = p.indicador LIMIT 1) ELSE null END) as grafo,
						   py.idProyecto,
						   p.indicador,
						   p.itemPlan,
						   c.idCentral,
						   p.idSubProyecto,
						   s.subProyectoDesc,
						   p.nombreProyecto,
						   (CASE WHEN s.idTipoSubProyecto = 2 THEN ( SELECT empresaColabDesc
																	   FROM empresacolab
																	  WHERE idEmpresaColab = c.idEmpresaColabCV) ELSE b.empresaColabDesc  END) as empresaColabDesc ,
						    f.faseDesc,
							c.codigo,
							c.tipoCentralDesc,
							e.empresaElecDesc,
							p.fechaInicio,
							p.fechaPrevEjec,
				
							(case when (not p.fechaEjecucion is null and p.fechaEjecucion<'2018-07-07') then p.fechaEjecucion
				 else  date(p.fechaPreliquidacion) end) as fechaEjecucion,
	
							c.idZonal,
							z.zonalDesc,
							p.idEstadoPlan,
							t.estadoPlanDesc,
							p.hasAdelanto,
							s.idTipoPlanta,
							(SELECT 1
							   FROM siom_obra
							  WHERE itemplan = p.itemplan limit 1) flg_bandeja_siom,
							s.idTipoSubProyecto,
							p.ubic_tss_cv,
							p.ubic_exped_cv,
							p.comentario_cv
					   FROM planobra p
							RIGHT JOIN empresacolab b on p.idEmpresaColab = b.idEmpresaColab,
					        fase f,
							subproyecto s,
							proyecto py,
							empresaelec e,
							pqt_central c
							RIGHT JOIN zonal z
							ON c.idZonal=z.idZonal,
							estadoplan t
					  WHERE p.paquetizado_fg IS NOT NULL AND s.idSubProyecto = p.idSubProyecto
					    AND p.idFase = f.idFase
					    AND c.idCentral     = p.idCentralPqt
						AND e.idEmpresaElec = p.idEmpresaElec
						AND p.idEstadoPlan  = t.idEstadoPlan
						AND s.idProyecto    = py.idProyecto
						AND CASE WHEN ".$ideecc." = 0 OR ".$ideecc." = 6 THEN p.idEmpresaColab = p.idEmpresaColab
								 ELSE p.idEmpresaColab = ".$ideecc." END";
	    if($proy!=''){
	        $Query .= " AND py.idProyecto = ".$proy;
	    }
	    if($subPry!=''){
	        $Query .= " AND s.idSubProyecto = ".$subPry;
	    }
	    if($itemPlan!=''){
	        $Query .= " AND p.itemPlan ='".$itemPlan."' ";
	    }
	    if($nombreproyecto!=''){
	        $Query .= ' AND p.nombreProyecto LIKE "%'.$nombreproyecto.'%"';
	        //$Query .= " AND p.nombreProyecto LIKE '%".$nombreproyecto."%' ";
	    }
	    if($tipoPlanta!=''){
	        $Query .= " AND s.idTipoPlanta = ".$tipoPlanta;
	    }
	    if($mesEjec!=NULL){
	        $Query .=  " AND EXTRACT(MONTH FROM DATE(p.fechaPrevEjec)) = $mesEjec";
	    }
	    if($idUsuario!=NULL) {
	        $Query .=  " AND p.itemplan IN ( SELECT l.itemplan
											   FROM log_planobra l
											  WHERE l.tipoPlanta = ".ID_TIPO_PLANTA_INTERNA."
												AND l.actividad  = 'ingresar'
												AND l.id_usuario = '".$idUsuario."'
												GROUP BY l.itemplan)";
	    }
	    if($idFase!=''){
	        $Query .= " AND p.idFase = ".$idFase;
	    }
	    $result = $this->db->query($Query,array());
			    #_log($this->db->last_query());
	    return $result;
	}
	/*
	function getExpedienteLiquidacion($itemplan, $idEstacion) {
	    $sql = "SELECT * FROM itemplan_expediente WHERE itemplan = ? and idEstacion = ? LIMIT 1";
	    $result = $this->db->query($sql, array($itemplan, $idEstacion));
	    log_message('error', $this->db->last_query());
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
	
	*/
	
	function getExpedienteLiquidacion($itemplan, $idEstacion) {
	    $sql = "SELECT * FROM itemplan_expediente
            	WHERE itemplan = ?
            	AND estado = 'ACTIVO'
        	    AND path_expediente is not null 
				ORDER BY fecha DESC LIMIT 1";
	    $result = $this->db->query($sql, array($itemplan));
	    log_message('error', $this->db->last_query());
	    if($result->row() != null) {
	        return $result->row_array();
	    } else {
	        return null;
	    }
	}
}
