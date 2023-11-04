<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('_log')) {
    function _log($var) {
        $CI =& get_instance();
        /*$clase = $CI->router->fetch_class();
        $metodo = $CI->router->fetch_method();*/
        $dbgt = debug_backtrace();
        if(isset($dbgt[1]['class'])) {
        	$class = $dbgt[1]['class'];
        } else {
        	$ruta = explode("/", $dbgt[0]['file']);
        	$class = end($ruta);
        }
        log_message('error', '( '.$class.' -> '.$dbgt[1]['function'].') (linea: '.$dbgt[0]['line'].') >> '.$var);
    }
}

if(!function_exists('_getDataKmzxAreaNodo')) {
    function _getDataKmzxAreaNodo($longitud, $latitud) {
        $CI =& get_instance();
        $CI->load->library('map_utils/coordenadas_utils');
        $codigo = $CI->coordenadas_utils->getBuscarArea([$longitud, $latitud]);
		
        $arrayIdCentral = $CI->m_utils->getIdCentralByCentralDescPqt($codigo);
        return array($arrayIdCentral);
    }
}

if(!function_exists('_getCentralCercana')) {
    function _getCentralCercana($longitud, $latitud) {
        $CI =& get_instance();
        $CI->load->library('map_utils/coordenadas_utils');
		
		$dataArray = $CI->m_utils->getDataCoordenadasNodo();
		$distanciaMenor = null;
		
		foreach($dataArray as $row) {
			//$distancia = _calculoDitanciaKmz();
			$distancia = _ditanciaVicenty($latitud, $longitud, $row['latitud'], $row['longitud']);
			
			if($distanciaMenor == null) {
				$distanciaMenor = $distancia;
				$codigo         = $row['codigo'];
			}
			
			if($distanciaMenor > $distancia) {
				$distanciaMenor = $distancia;
				$codigo         = $row['codigo'];
			}
		}
		
        $arrayIdCentral = $CI->m_utils->getIdCentralByCentralDescPqt($codigo);
        return $arrayIdCentral;
    }
}

if(!function_exists('_getDataKmz')) {
    function _getDataKmz($latitud, $longitud) {
        $CI =& get_instance();
        $CI->load->library('map_utils/coordenadas_utils');
        //$codigo = $CI->coordenadas_utils->getBuscarArea([$longitud, $latitud]);
		list($codigo, $distanciaCtoCli, $latitudCTO, $longitudCTO) = _getCtoDistanciaCTO($longitud, $latitud);
		
		

		if($codigo == null || $codigo == '') {//si es null capturo el más cercano
			$dataArray = $CI->m_utils->getDataCoordenadasNodo();
			
			$distanciaMenor = null;
			foreach($dataArray as $row) {
				//$distancia = _calculoDitanciaKmz();
				$distancia = _ditanciaVicenty($latitud, $longitud, $row['latitud'], $row['longitud']);
				
				if($distanciaMenor == null) {
					$distanciaMenor = $distancia;
					$codigo         = $row['codigo'];
				}
				
				if($distanciaMenor > $distancia) {
					$distanciaMenor = $distancia;
					$codigo         = $row['codigo'];
				}
			}
			$distanciaCtoCli = $distanciaMenor;
		}
        $arrayIdCentral = $CI->m_utils->getIdCentralByCentralDescPqt($codigo);
        return array($arrayIdCentral, $distanciaCtoCli);
    }
}

if(!function_exists('_calculoDitanciaKmz')) {
    function _calculoDitanciaKmz($latitud, $longitud, $latitudNodo, $longitudNodo) {
        $CI =& get_instance();
        $CI->load->library('map_utils/coordenadas_utils');
        $distancia = _ditanciaVicenty($latitud, $longitud, $latitudNodo, $longitudNodo);

        $metrosTendidos = $distancia + ($distancia * 0.30); //FORMULA PARA SACAR LOS METROS TENDIDOS

        return $metrosTendidos;
    }
}

if(!function_exists('_ditanciaVicenty')) {
    function _ditanciaVicenty($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
        // Se convierte a radianes
        $latFrom = deg2rad(floatval($latitudeFrom));
        $lonFrom = deg2rad(floatval($longitudeFrom));
        $latTo   = deg2rad(floatval($latitudeTo));
        $lonTo   = deg2rad(floatval($longitudeTo));
      
        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
             pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
      
        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }
}


if(!function_exists('_getCtoInfo')) {
    function _getCtoInfo($longitud, $latitud) {
        $CI =& get_instance();
        //$CI->load->library('map_utils/coordenadas_utils');

		$dataArray = $CI->m_utils->getDataCoordenadasCto();
		
		// $distanciaMenor = null;
		// $cant_cto = 0;

		$arrayCantCto = $CI->m_utils->getCtoByCoord($latitud, $longitud);

		// $arrayCantCto = array();
		// foreach($dataArray as $row) {
			//$distancia = _calculoDitanciaKmz();
			// $distancia = _ditanciaVicenty($latitud, $longitud, $row['latitud'], $row['longitud']);
			
			// if($distancia <= 600) {
				// $cant_cto++;
				// array_push($arrayCantCto, $row['codigo']);
				// if($cant_cto == 5) {
					// break;
				// }
			// }	
		// }
		
        return $arrayCantCto;
    }
}


// TRAIGO LA DISTANCIA DEL MENOR CTO.
if(!function_exists('_getCtoDistanciaCTO')) {
    function _getCtoDistanciaCTO($longitud, $latitud) {
        $CI =& get_instance();
        $CI->load->library('map_utils/coordenadas_utils');

		//$dataArray = $CI->m_utils->getDataCoordenadasCto();
		$codigo = null;
		$latitudCTO = null;
        $longitudCTO = null;
		$distanciaMenor = null;
		$cant_cto = 0;
		$arrayCantCto = array();
		$codigoCTO = null;
		
		$dataArrayCto = $CI->m_utils->getCtoByCoordMasCercano($latitud, $longitud);

		if($dataArrayCto['distancia'] <= 600) {
			$distanciaMenor = $dataArrayCto['distancia'];
			$latitudCTO     = $dataArrayCto['latitud'];
			$longitudCTO    = $dataArrayCto['longitud'];
			$codigoCTO      = $dataArrayCto['codigo'];
			
			$arrayCentral   = _getCentralCercana($longitudCTO, $latitudCTO);
			$codigo         = $arrayCentral['codigo'];
		}
		
	    return array($codigo, $distanciaMenor, $latitudCTO, $longitudCTO);
    }
}


if(!function_exists('_getDataKmzArqueologico')) {
    function _getDataKmzArqueologico($longitud, $latitud) {
        $CI =& get_instance();
        $CI->load->library('map_utils/coordenadas_utils');
        
		$nomb = $CI->coordenadas_utils->getBuscarAreaSitiosArqueo([$longitud, $latitud]);
		
        if($nomb) {
			$necesita = 'SI';
		} else {
			$necesita = 'NO';
		}

        return $necesita;
    }
}

if(!function_exists('_getDataKmzByCod')) {
    function _getDataKmzByCod($codigo) {
        $CI =& get_instance();
        $CI->load->library('map_utils/coordenadas_utils');
        //$codigo = $CI->coordenadas_utils->getBuscarArea([$longitud, $latitud]);
		list($codigo, $distancia, $latitudCTO, $longitudCTO) = _getCtoDistanciaCTO($longitud, $latitud);
		
		if($codigo == null || $codigo == '') {
			$dataArray = $CI->m_utils->getDataCoordenadasNodo();
			
			$distanciaMenor = null;
			foreach($dataArray as $row) {
				//$distancia = _calculoDitanciaKmz();
				$distancia = _ditanciaVicenty($latitud, $longitud, $row['latitud'], $row['longitud']);
				
				if($distanciaMenor == null) {
					$distanciaMenor = $distancia;
				}
				
				if($distanciaMenor > $distancia) {
					$distanciaMenor = $distancia;
					$codigo         = $row['codigo'];
				}
			}
		}
        $arrayIdCentral = $CI->m_utils->getIdCentralByCentralDesc($codigo);

        return $arrayIdCentral;
    }
}

    //FUNCION QUE BUSCA EL PUNTO MÁS LEJANO DEL NODO EN EL KMZ
    if(!function_exists('_getCoordenadasByCod')) {
        function _getCoordenadasByCod() {
            $stringFile = "";
            $CI =& get_instance();
            $CI->load->library('map_utils/coordenadas_utils');

            $JSON_COORDENADAS = $CI->coordenadas_utils->getJsonKmz();
                        
            $arrayData = array();
            $dataArray = $CI->m_utils->getDataCoordenadasNodo();
            foreach($dataArray as $row) {
                $distanciaMayor = null;
                for($i=0; $i < count($JSON_COORDENADAS['features']); $i++) {

                    if(trim($JSON_COORDENADAS['features'][$i]['properties']['MDF']) == $row['codigo']) {
                        for($j=0; $j < count($JSON_COORDENADAS['features'][$i]['geometry']['coordinates'][0][0]); $j++) {
                            $mdf_kmz = $JSON_COORDENADAS['features'][$i]['properties']['MDF'];
                          
                            $coord = $JSON_COORDENADAS['features'][$i]['geometry']['coordinates'][0][0][$j];
                            // EL MAYOR ES LONG[1] Y MENOS LAT[0]
                            $distancia = _ditanciaVicenty($row['latitud'], $row['longitud'], $coord[1], $coord[0]);

                            if($distanciaMayor == null) {
                                $distanciaMayor = $distancia;
                                $codigo         = $mdf_kmz;
                                $latitud        = $coord[1];
                                $longitud       = $coord[0];
                            }
                            
                            if($distanciaMayor <= $distancia) {
                                $distanciaMayor = $distancia;
                                $codigo         = $mdf_kmz;
                                $latitud        = $coord[1];
                                $longitud       = $coord[0];
                            }   
                        }
                        $JSON['data']['mdf']   = $codigo;
                        $JSON['data']['coord']['latitud']   = $latitud;
                        $JSON['data']['coord']['longitud']  = $longitud;
                        $JSON['data']['distancia'] = $distanciaMayor;
                        
                        array_push($arrayData, $JSON);
                    }
                }
            }
            return $arrayData;
        }
    }
	
	if(!function_exists('_trama_sisego')) {
        function _trama_sisego($arrayDataTrama, $urlSisego, $flg_trama_tipo, $itemplan, $nombreTrama, $sisego) {
            $CI =& get_instance();
                      
            $response = $CI->m_utils->sendDataToURL($urlSisego, $arrayDataTrama);//EL ARRAYDATA DEBE SER ASOCIATIVA    
            
			
			if(is_object($response)){
				if($response->error == EXIT_SUCCESS){
					$CI->m_utils->saveLogSigoplus($nombreTrama, null, $itemplan, null, $sisego, null, null, 
												'TRAMA COMPLETADA', 'OPERACION REALIZADA CON EXITO', 1, $flg_trama_tipo, $response);
				}else{
					$CI->m_utils->saveLogSigoplus($nombreTrama, null, $itemplan, null, $sisego, null, null, 'FALLA EN LA RESPUESTA DEL HOSTING', strtoupper($response->mensaje), 2, $flg_trama_tipo, $response);
				}
			}else{
				$CI->m_utils->saveLogSigoplus($nombreTrama, null, $itemplan, null, $sisego, null, null, 'FALLA DE CONEXION', 'OPERACION NO COMPLETADA, SE PERDIO LA CONEXCION CON SIGOPLUS', 3, $flg_trama_tipo, $response);
			}
			return $response;
		}
    }
	
	if(!function_exists('__data_robot_coti_v2')) {
		function __data_robot_coti_v2($clasificacion, $tipo_cliente, $longitud, $latitud, $idCentral=null, $idSubProyecto=null, $req_inc=null, $tipo_habil_tras=null) {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			$tipo      = NULL;
			$codigo    = NULL;
			$distancia = NULL;
			$hilo_disp = NULL;
			$id_terminal = NULL;
			
			$clasificacion = strtoupper(trim($clasificacion));

			$rowCtOTipoDiseno = $CI->m_utils->getCTOCotizacion_v2($clasificacion, $tipo_cliente);
			if(($clasificacion == 'NAP EXPRESS VERDE' || $clasificacion == 'ESTUDIO ESPECIAL GRIS'
				|| $clasificacion == 'ESTUDIO DE CAMPO' || $clasificacion == 'NAP EXPRESS AMARILLO' || $clasificacion == 'HABILITACION DE HILO') && $tipo_cliente != 'Centro Comercial') {
				
				if($rowCtOTipoDiseno['id_tipo_diseno'] == 6 || $rowCtOTipoDiseno['id_tipo_diseno'] == 7) {//EDIFICIOS
					$dataRobot = _trama_distancia_robot($latitud, $longitud, 1);
				} else {
					$dataRobot = _trama_distancia_robot($latitud, $longitud, 2);
				}
				
				$fac_red = $dataRobot['codigo'];
				
            } else {//LOGICA ANTERIOR
				list($arrayCen, $distancia) = _getDataKmz($longitud, $latitud);//TAIGO LA DISTANCIA DEL CLIENTE A LA CTO 
				$codigo = $arrayCen['codigo'];
				$fac_red = _getCtoInfo($longitud, $latitud);
				
				if($fac_red == null || $fac_red == '') {
					$fac_red = $codigo;
					$dataRobot['lat_ctoResEbcMdf'] = $arrayCen['latitud'];
					$dataRobot['lon_ctoResEbcMdf'] = $arrayCen['longitud'];
				} else {
					$dataRobot['lat_ctoResEbcMdf'] = null;
					$dataRobot['lon_ctoResEbcMdf'] = null;
				}

				$dataRobot['codigo']    = $codigo;
				$dataRobot['hilo_disp'] = null;
				$dataRobot['tipo']      = null;
				$dataRobot['distancia'] = $distancia;
				$dataRobot['id_terminal'] = null;
            }
			
			$dataRobot['fac_red'] = $fac_red;
			
			// LOGICA DE LOS COSTOS
			$dataCostosFin = array();
			
			if($idCentral != null) {
				$dataCostos['clasificacion'] = $clasificacion;
				$dataCostos['tipo_cliente'] = $tipo_cliente;
				$dataCostos['distancia'] = $dataRobot['distancia'];
				$dataCostos['longitud'] = $longitud;
				$dataCostos['latitud'] = $latitud;
				$dataCostos['hilo_disp'] = $dataRobot['hilo_disp'];
				$dataCostos['idCentral'] = $idCentral;
				$dataCostos['idSubProyecto'] = $idSubProyecto;
				$dataCostos['tipo'] = $dataRobot['tipo'];
				$dataCostos['req_inc'] = $req_inc;
				
				$dataCostosFin = _getCostosRobot($dataCostos, null, null, null, $tipo_habil_tras);
				///////////	
			}
			
			$arrayData = $dataRobot;
			
			return array($arrayData, $dataCostosFin);
		}
	}
	
	///////////////////SIMULACION/////////////////////////
	
	if(!function_exists('__data_robot_coti_v2_simu')) {
		function __data_robot_coti_v2_simu($clasificacion, $tipo_cliente, $longitud, $latitud, $idCentral=null, $idSubProyecto=null, $req_inc=null) {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			$tipo      = NULL;
			$codigo    = NULL;
			$distancia = NULL;
			$hilo_disp = NULL;
			$id_terminal = NULL;
			
			$clasificacion = strtoupper(trim($clasificacion));
			$rowCtOTipoDiseno = $CI->m_utils->getCTOCotizacion_v2($clasificacion, $tipo_cliente);
			if(($clasificacion == 'NAP EXPRESS VERDE' || $clasificacion == 'ESTUDIO ESPECIAL GRIS' || $clasificacion == 'ESTUDIO DE CAMPO' || $clasificacion == 'NAP EXPRESS AMARILLO') && $tipo_cliente != 'Centro Comercial') {
				if($rowCtOTipoDiseno['id_tipo_diseno'] == 6 || $rowCtOTipoDiseno['id_tipo_diseno'] == 7) {//EDIFICIOS
					$dataRobot = _trama_distancia_robotSimu($latitud, $longitud, 1);
				} else {
					$dataRobot = _trama_distancia_robotSimu($latitud, $longitud, 2);
				}
				$fac_red = $dataRobot['codigo'];
            } else {//LOGICA ANTERIOR
				list($arrayCen, $distancia) = _getDataKmz($longitud, $latitud);//TAIGO LA DISTANCIA DEL CLIENTE A LA CTO 
				$codigo = $arrayCen['codigo'];
				$fac_red = _getCtoInfo($longitud, $latitud);
				
				if($fac_red == null || $fac_red == '') {
					$fac_red = $codigo;
					$dataRobot['lat_ctoResEbcMdf'] = $arrayCen['latitud'];
					$dataRobot['lon_ctoResEbcMdf'] = $arrayCen['longitud'];
				} else {
					$dataRobot['lat_ctoResEbcMdf'] = null;
					$dataRobot['lon_ctoResEbcMdf'] = null;
				}

				$dataRobot['codigo']    = $codigo;
				$dataRobot['hilo_disp'] = null;
				$dataRobot['tipo']      = null;
				$dataRobot['distancia'] = $distancia;
				$dataRobot['id_terminal'] = null;
            }
			
			$dataRobot['fac_red'] = $fac_red;
			
			// LOGICA DE LOS COSTOS
			$dataCostosFin = array();
			if($idCentral != null) {
				$dataCostos['clasificacion'] = $clasificacion;
				$dataCostos['tipo_cliente'] = $tipo_cliente;
				$dataCostos['distancia'] = $dataRobot['distancia'];
				$dataCostos['longitud'] = $longitud;
				$dataCostos['latitud'] = $latitud;
				$dataCostos['hilo_disp'] = $dataRobot['hilo_disp'];
				$dataCostos['idCentral'] = $idCentral;
				$dataCostos['idSubProyecto'] = $idSubProyecto;
				$dataCostos['tipo'] = $dataRobot['tipo'];
				$dataCostos['req_inc'] = $req_inc;
				
				$flgSimu = 1;
				$dataCostosFin = _getCostosRobot($dataCostos, $flgSimu, $dataRobot['flg_tipo_diseno'], $dataRobot['tecnologia']);
				///////////	
			}
			$arrayData = $dataRobot;
							  
			return array($arrayData, $dataCostosFin);
		}
	}
	
	if(!function_exists('_trama_distancia_robotSimu')) {
		function _trama_distancia_robotSimu($latitud, $longitud, $flg_edif) {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			
			$flg_tipo_diseno = null;
			$tecnologia      = null;
			if($flg_edif == 1) {//EDIFICIOS
				$arrayCto = $CI->m_utils->getCtoByCoordEdifSimu($latitud, $longitud);
				$flg_tipo_diseno = $arrayCto['flg_tipo_diseno'];
				$tecnologia      = $arrayCto['tecnologia'];
			} else {
				$arrayCto = $CI->m_utils->getCtoByCoordV2Simu($latitud, $longitud);
				$flg_tipo_diseno = $arrayCto['flg_tipo_diseno'];
				$tecnologia      = $arrayCto['tecnologia'];
			}
			$hilo_disp = null;
			$lat_ctoResEbcMdf = null;
			$lon_ctoResEbcMdf = null;
			$id_terminal 	  = null;
			
			$arrayReserva = $CI->m_utils->getReservasByCoordSimuV2($latitud, $longitud, $flg_edif);
			$arrayEbc = $CI->m_utils->getEbcByCoordSimuV2($latitud, $longitud);

			if($arrayCto['codigo'] == null || $arrayCto['codigo'] == '') {
				if($arrayReserva['codigo'] == null || $arrayReserva['codigo'] == '') {
					if($arrayEbc['codigo'] == null || $arrayEbc['codigo'] == '') {// SI LA COLUMNA DE CODIGO EBC ES NULL
						$distanciaEbc = $arrayEbc['distancia'];
						// $codigo = $CI->coordenadas_utils->getBuscarArea([$longitud, $latitud]);
						// $coord  = $CI->m_cotizacion_alcance_robot->getDataPqtCentralByCodigo($codigo);
						
						// if($codigo) {
							// getMdfCoord($lat, $long);
							// $distancia = _ditanciaVicenty($coord['latitud'], $coord['longitud'], $latitud, $longitud);
							// $distancia = round($distancia, 0);
							// $tipo      = 'MDF';
							// $lat_ctoResEbcMdf = $coord['latitud'];
							// $lon_ctoResEbcMdf = $coord['longitud'];
						// }
						

						$arrayMdf  = $CI->m_utils->getMdfCoordSimu($latitud, $longitud);
						if($distanciaEbc < $arrayMdf['distancia'] && $distanciaEbc != null) { //COMPARO SI LA DISTANCIA DEL MDF ES MAS CORTA QUe LA DEL EBC

							$arrayEbc  = $CI->m_utils->getDistanciaEbcCodigo($latitud, $longitud);
							$codigo    = $arrayEbc['codigo'];
							$distancia = $arrayEbc['distancia'];
							$lat_ctoResEbcMdf = $arrayEbc['latitud'];
							$lon_ctoResEbcMdf = $arrayEbc['longitud'];
							$tipo      = 'EBC';
						} else {
							$codigo    = $arrayMdf['codigo'];
							$distancia = $arrayMdf['distancia'];
							$lat_ctoResEbcMdf = $arrayMdf['latitud'];
							$lon_ctoResEbcMdf = $arrayMdf['longitud'];
							$tipo      = 'MDF';
						}
					} else {
						$codigo    = $arrayEbc['codigo'];
						$distancia = $arrayEbc['distancia'];
						$lat_ctoResEbcMdf = $arrayEbc['latitud'];
						$lon_ctoResEbcMdf = $arrayEbc['longitud'];
						$tipo      = 'EBC';
					}
				} else {
					if($arrayReserva['distancia'] >  $arrayEbc['distancia']) { // SI LA RESERVA ESTA MAS LEJOS 
						$arrayEbc  = $CI->m_utils->getDistanciaEbcCodigo($latitud, $longitud);
						$codigo    = $arrayEbc['codigo'];
						$distancia = $arrayEbc['distancia'];
						$lat_ctoResEbcMdf = $arrayEbc['latitud'];
						$lon_ctoResEbcMdf = $arrayEbc['longitud'];
						$tipo      = 'EBC';
					} else {
						$codigo    = $arrayReserva['codigo'];
						$distancia = $arrayReserva['distancia'];
						$hilo_disp = $arrayReserva['hilos_disponibles'];
						$lat_ctoResEbcMdf = $arrayReserva['latitud'];
						$lon_ctoResEbcMdf = $arrayReserva['longitud'];
						$id_terminal      = $arrayReserva['id_terminal'];
						$tipo      = 'RESERVA';
					}
				}
			} else {
				$codigo    = $arrayCto['codigo'];
				$distancia = $arrayCto['distancia'];
				$hilo_disp = $arrayCto['disponible_hilos'];
				$lat_ctoResEbcMdf = $arrayCto['latitud'];
				$lon_ctoResEbcMdf = $arrayCto['longitud'];
				$id_terminal      = $arrayCto['id_terminal'];
				$tipo      = 'CTO';
			}
			
			$arrayData = array(
								'codigo'    => $codigo,
								'distancia' => $distancia,
								'tipo'      => $tipo,
								'hilo_disp' => $hilo_disp,
								'lat_ctoResEbcMdf' => $lat_ctoResEbcMdf,
								'lon_ctoResEbcMdf' => $lon_ctoResEbcMdf,
								'id_terminal'      => $id_terminal,
								'flg_tipo_diseno' => $flg_tipo_diseno,
								'tecnologia'      => $tecnologia
							  );
			return $arrayData;		  
		}
	}
	////////////////////////////////////////////////
	
	if(!function_exists('_trama_distancia_robot')) {
		function _trama_distancia_robot($latitud, $longitud, $flg_edif) {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			
			if($flg_edif == 1) {//EDIFICIOS
				$arrayCto = $CI->m_utils->getCtoByCoordEdif($latitud, $longitud);
			} else {
				$arrayCto = $CI->m_utils->getCtoByCoordV2($latitud, $longitud);
			}
			$hilo_disp = null;
			$lat_ctoResEbcMdf = null;
			$lon_ctoResEbcMdf = null;
			$id_terminal 	  = null;
			
			$arrayReserva = $CI->m_utils->getReservasByCoordV2($latitud, $longitud, $flg_edif);
			$arrayEbc = $CI->m_utils->getEbcByCoordV2($latitud, $longitud);
			
			if($arrayCto['codigo'] == null || $arrayCto['codigo'] == '') {
				if($arrayReserva['codigo'] == null || $arrayReserva['codigo'] == '') {
					if($arrayEbc['codigo'] == null || $arrayEbc['codigo'] == '') {
						$distanciaEbc = $arrayEbc['distancia'];
						// $codigo = $CI->coordenadas_utils->getBuscarArea([$longitud, $latitud]);
						// $coord  = $CI->m_cotizacion_alcance_robot->getDataPqtCentralByCodigo($codigo);
						
						// if($codigo) {
							// getMdfCoord($lat, $long);
							// $distancia = _ditanciaVicenty($coord['latitud'], $coord['longitud'], $latitud, $longitud);
							// $distancia = round($distancia, 0);
							// $tipo      = 'MDF';
							// $lat_ctoResEbcMdf = $coord['latitud'];
							// $lon_ctoResEbcMdf = $coord['longitud'];
						// }
						

						$arrayMdf  = $CI->m_utils->getMdfCoord($latitud, $longitud);
						
						if($distanciaEbc < $arrayMdf['distancia']) { //COMPARO SI LA DISTANCIA DEL MDF ES MAS CORTA QUe LA DEL EBC
							$arrayEbc  = $CI->m_utils->getDistanciaEbcCodigo($latitud, $longitud);
							$codigo    = $arrayEbc['codigo'];
							$distancia = $arrayEbc['distancia'];
							$lat_ctoResEbcMdf = $arrayEbc['latitud'];
							$lon_ctoResEbcMdf = $arrayEbc['longitud'];
							$tipo      = 'EBC';
						} else {
							$codigo    = $arrayMdf['codigo'];
							$distancia = $arrayMdf['distancia'];
							$lat_ctoResEbcMdf = $arrayMdf['latitud'];
							$lon_ctoResEbcMdf = $arrayMdf['longitud'];
							$tipo      = 'MDF';
						}
						
						
					} else {
						$arrayMdf  = $CI->m_utils->getMdfCoord($latitud, $longitud);
						$distanciaEbc = $arrayEbc['distancia'];
						
						if($distanciaEbc <= $arrayMdf['distancia']) { //COMPARO SI LA DISTANCIA DEL MDF ES MAS CORTA QUe LA DEL EBC
							$arrayEbc  = $CI->m_utils->getDistanciaEbcCodigo($latitud, $longitud);
							$codigo    = $arrayEbc['codigo'];
							$distancia = $arrayEbc['distancia'];
							$lat_ctoResEbcMdf = $arrayEbc['latitud'];
							$lon_ctoResEbcMdf = $arrayEbc['longitud'];
							$tipo      = 'EBC';
							
						} else {
							$codigo    = $arrayMdf['codigo'];
							$distancia = $arrayMdf['distancia'];
							$lat_ctoResEbcMdf = $arrayMdf['latitud'];
							$lon_ctoResEbcMdf = $arrayMdf['longitud'];
							$tipo      = 'MDF';
							// _log("MDF ENTRO");
						}
					}
				} else {
					if($arrayReserva['distancia'] >  $arrayEbc['distancia']) { // SI LA RESERVA ESTA MAS LEJOS 
						$arrayEbc  = $CI->m_utils->getDistanciaEbcCodigo($latitud, $longitud);
						$codigo    = $arrayEbc['codigo'];
						$distancia = $arrayEbc['distancia'];
						$lat_ctoResEbcMdf = $arrayEbc['latitud'];
						$lon_ctoResEbcMdf = $arrayEbc['longitud'];
						$tipo      = 'EBC';
					} else {
						$codigo    = $arrayReserva['codigo'];
						$distancia = $arrayReserva['distancia'];
						$hilo_disp = $arrayReserva['hilos_disponibles'];
						$lat_ctoResEbcMdf = $arrayReserva['latitud'];
						$lon_ctoResEbcMdf = $arrayReserva['longitud'];
						$id_terminal      = $arrayReserva['id_terminal'];
						$tipo      = 'RESERVA';
					}
				}
			} else {
				$codigo    = $arrayCto['codigo'];
				$distancia = $arrayCto['distancia'];
				$hilo_disp = $arrayCto['disponible_hilos'];
				$lat_ctoResEbcMdf = $arrayCto['latitud'];
				$lon_ctoResEbcMdf = $arrayCto['longitud'];
				$id_terminal      = $arrayCto['id_terminal'];
				$tipo      = 'CTO';
			}
			
			$arrayData = array(
								'codigo'    => $codigo,
								'distancia' => $distancia,
								'tipo'      => $tipo,
								'hilo_disp' => $hilo_disp,
								'lat_ctoResEbcMdf' => $lat_ctoResEbcMdf,
								'lon_ctoResEbcMdf' => $lon_ctoResEbcMdf,
								'id_terminal'      => $id_terminal
							  );
			return $arrayData;		  
		}
	}
	
	if(!function_exists('_getDataKmzArqueologicoFull')) {
    function _getDataKmzArqueologicoFull() {
        $CI =& get_instance();
        $CI->load->library('map_utils/coordenadas_utils');
        
		$arrayData = $CI->coordenadas_utils->getCoordSitiosArqueoFull();
		
        return $arrayData;
    }
	
	if(!function_exists('_getCostosRobot')) {
		function _getCostosRobot($dataCostos, $flgSimu, $flg_tipo_diseno = null, $tecnologia = null, $tipo_habil_tras= null) {
			$CI =& get_instance();

			$clasificacion = $dataCostos['clasificacion'];
			$tipo_cliente  = $dataCostos['tipo_cliente'];
			$distancia     = $dataCostos['distancia'];
			$longitud      = $dataCostos['longitud'];
			$latitud       = $dataCostos['latitud'];
			$hilo_disp     = $dataCostos['hilo_disp'];
			$idCentral     = $dataCostos['idCentral'];
			$idSubProyecto = $dataCostos['idSubProyecto'];
			$tipo          = $dataCostos['tipo'];
			$inc_necesita  = $dataCostos['req_inc'];
			
			$arrayDataCentral = $CI->m_utils->getCentralPqt($idCentral);
			if($inc_necesita == null) {
				$inc_necesita = _getDataKmzArqueologico($longitud, $latitud);
			}
			
			$metroTendidoAe   = round($distancia + ($distancia * 0.30));
			$clasificacion = strtoupper(trim($clasificacion));
			$arrayData = $CI->m_utils->getDiasMatriz($metroTendidoAe, NULL, NULL, $inc_necesita, $arrayDataCentral['flg_tipo_zona'], $arrayDataCentral['jefatura']);
			
			$data['seia'] = $arrayData['seia'];
			$data['mtc']  = $arrayData['mtc'];
			$data['inc']  = $inc_necesita; 
			$data['eecc'] = $arrayDataCentral['empresaColabDesc'];
			$data['hilo_disp'] = $hilo_disp;
			$data['costo_mo_edif']  = 0;
			$data['costo_mat_edif'] = 0;
			$data['costo_oc_edif']  = 0;
			$data['flg_um']         = 0;
			
			$rowCtOTipoDiseno = $CI->m_utils->getCTOCotizacion_v2($clasificacion, $tipo_cliente);

			if($rowCtOTipoDiseno['costo_total'] == null && ($tipo_habil_tras == null || $tipo_habil_tras == 0)) {
				$rowMatrizCosto = $CI->m_utils->getCostosMatrizCotizacionV2($metroTendidoAe, $data['seia'], $data['inc'], 2, $rowCtOTipoDiseno['id_tipo_diseno']);

				if($rowCtOTipoDiseno['id_tipo_diseno'] == 7) { //CLUSTER EN EDIFICIO
					if($data['hilo_disp'] == 0 || $data['hilo_disp'] == '' || $data['hilo_disp'] == null || $tipo == 'RESERVA') {// SI ESTA SATURADO, SE DEBE CONSTRUIR CTO
						$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
						$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
						$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total']+$rowMatrizCosto['costo_edif'];
						
						$data['total_mo_pqt']           = $rowCostoTotalPqt['total_mo_pqt'];
						$data['costo_mo_edif']  = $rowMatrizCosto['costo_mo_edif'];
						$data['costo_mat_edif'] = $rowMatrizCosto['costo_mat_edif'];
						$data['costo_oc_edif']  = $rowMatrizCosto['costo_oc_edif'];
						
						
						$data['costo_mat_envio_sisego'] = $data['costo_mat_edif']+$data['costo_oc_edif']+$rowMatrizCosto['mat_total'];
						$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
						
						$data['tipo_diseno'] = $rowCtOTipoDiseno['tipo_diseno'];
						$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
						if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
							$data['duracion'] = $arrayData['dias'];
						} else {
							$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
						}
						$data['cant_cto']    = 5;
					} else {// SI HAY CTO ENTRA COMO UM
						$data['flg_um'] = 1;
						if($tecnologia == 'GPON') {
							$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
							$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
							
							$data['total_mo_pqt']       = $rowCostoTotalPqt['total_mo_pqt'];
							$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total']+$rowMatrizCosto['costo_edif'];
							
							$data['costo_mo_edif']  = $rowMatrizCosto['costo_mo_edif'];
							$data['costo_mat_edif'] = $rowMatrizCosto['costo_mat_edif'];
							$data['costo_oc_edif']  = $rowMatrizCosto['costo_oc_edif'];
							
							
							$data['costo_mat_envio_sisego'] = $data['costo_mat_edif']+$data['costo_oc_edif']+$rowMatrizCosto['mat_total'];
							$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
							
							$data['tipo_diseno'] = $rowCtOTipoDiseno['tipo_diseno'];
							$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
							if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
								$data['duracion'] = $arrayData['dias'];
							} else {
								$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
							}
							$data['cant_cto']    = 5;
						} else {
							$rowMatrizCosto['mo_total']  = $rowMatrizCosto['costo_total_um']*0.60;
							$rowMatrizCosto['mat_total'] = $rowMatrizCosto['costo_total_um']*0.40;
							
							$data['tipo_diseno'] = 'UM EDIFICIO';
							$data['id_tipo_diseno'] = $CI->m_utils->getIdTipoDisenoByDescDiseno($data['tipo_diseno']);
							$data['duracion']    = 7;
							$data['cant_cto']    = 0;
							
							$rowMatrizCosto['inc_total'] = 0;
							$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total'];
						
							$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
							$data['costo_mo_envio_sisego'] = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total'];
						}
					}
				} else {
					if($metroTendidoAe <= 200 && $tipo == 'CTO') {//SI LOS METROS ES MENOR O IGUAL A 200 ES UM.
						$data['flg_um'] = 1;
						if($flgSimu == 1) {//POR AHORA SOLO SIMULADOR TOMA UM
							if($flg_tipo_diseno == 1) {
								$data['tipo_diseno'] = $rowCtOTipoDiseno['tipo_diseno'];
								$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
								if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
									$data['duracion'] = $arrayData['dias'];
								} else {
									$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
								}
								
								$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
								$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
								$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
								$data['cant_cto']           = $rowCtOTipoDiseno['cto'];
								
								$data['total_mo_pqt']           = $rowCostoTotalPqt['total_mo_pqt'];
								$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
								$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
							} else {
								
								$rowMatrizCosto['mo_total']  = $rowMatrizCosto['costo_total_um']*0.60;
								$rowMatrizCosto['mat_total'] = $rowMatrizCosto['costo_total_um']*0.40;
								$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total'];
								$rowMatrizCosto['inc_total']  = 0;
								$data['tipo_diseno'] = 'UM';
								$data['id_tipo_diseno'] = $CI->m_utils->getIdTipoDisenoByDescDiseno($data['tipo_diseno']);
								$data['duracion']    = 7;
								$data['cant_cto']    = 0;
								
								$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
								$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total'];
							}
						} else {
							$data['tipo_diseno'] = $rowCtOTipoDiseno['tipo_diseno'];
							$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
							if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
								$data['duracion'] = $arrayData['dias'];
							} else {
								$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
							}
							
							$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
							$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
							$data['total_mo_pqt']       = $rowCostoTotalPqt['total_mo_pqt'];
							$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
							$data['cant_cto']           = $rowCtOTipoDiseno['cto'];
							
							$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
						    $data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
						}

						
					} else {
						$data['flg_um'] = 0;
						
						if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
							$data['duracion'] = $arrayData['dias'];
						} else {
							$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
						}
						$data['tipo_diseno']    = $rowCtOTipoDiseno['tipo_diseno'];
						$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
						
						if($rowMatrizCosto['mat_total']) {
							$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
							$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
							
							$data['total_mo_pqt']        = $rowCostoTotalPqt['total_mo_pqt'];
							$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
							$data['cant_cto']           = $rowCtOTipoDiseno['cto'];
							
							$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
							$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
						} else {
							$rowMatrizCosto['mo_total'] = null;
							$rowMatrizCosto['total']    = null;
							$data['cant_cto']           = null;
							$data['costo_mat_envio_sisego'] = null;
							$data['costo_mo_envio_sisego']  = null;
						}
					}
				}

            } else {
				if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
					$data['duracion'] = $arrayData['dias'];
				} else {
					$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
				}
				
				if($tipo_habil_tras == null || $tipo_habil_tras == 0) {
					if($clasificacion == 'ESTUDIO DE CAMPO') {
						$costoMo  = $rowCtOTipoDiseno['costo_total'];
						$costoMat = 0;
					} else {
						$costoMat = $rowCtOTipoDiseno['costo_total']*0.60;
						$costoMo  = $rowCtOTipoDiseno['costo_total']*0.40;
					}
				} else {
					$infoDataHab = $CI->m_utils->getCostoHabilitacionHilo($tipo_habil_tras);
					$costoMo  = $infoDataHab['costo_mo'];
					$costoMat = $infoDataHab['costo_mat'];
					$rowCtOTipoDiseno['costo_total'] = $costoMo+$costoMat;
				}
				
                
				$rowMatrizCosto = array('total' 	=> $rowCtOTipoDiseno['costo_total'],
				                        'mo_total'  => $costoMo,
										'mat_total' => $costoMat,
										'diseno_total' => 0,
										'inc_total'  => 0,
                                        'eia_total'  => 0,
										'metro_oc'   => '',
										'crxa'       => '',
										'crxc' 		 => '',
										'postes'     => '');
				
				$data['costo_mat_envio_sisego'] = $costoMat;
				$data['costo_mo_envio_sisego']  = $costoMo;
				
                $data['tipo_diseno']    = $rowCtOTipoDiseno['tipo_diseno'];
				$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
				$data['cant_cto']       = $rowCtOTipoDiseno['cto'];
            }
			
			$data['distancia']      = $distancia;
			$data['metrosTendidos'] = $metroTendidoAe;
            $data['arrayCostos']    = $rowMatrizCosto;
			return $data;
		}
	}
	
	if(!function_exists('_getCoordSitiosHistoricosFull')) {
		function _getCoordSitiosHistoricosFull() {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			
			$arrayData = $CI->coordenadas_utils->getCoordSitiosHistoricosFull();
			
			return $arrayData;
		}
    }
	
	if(!function_exists('_getCoordViasMetroFull')) {
		function _getCoordViasMetroFull() {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			
			$arrayData = $CI->coordenadas_utils->getCoordSViasMetroFull();
			
			return $arrayData;
		}
    }
	
	//CRECIMIENTO VETICAL ROBOT
	if(!function_exists('__data_robot_coti_cv')) {
		function __data_robot_coti_cv($clasificacion, $tipo_cliente, $longitud, $latitud, $idCentral=null, $idSubProyecto=null, $req_inc=null) {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			$tipo      = NULL;
			$codigo    = NULL;
			$distancia = NULL;
			$hilo_disp = NULL;
			$id_terminal = NULL;
			
			$clasificacion = strtoupper(trim($clasificacion));

			$rowCtOTipoDiseno = $CI->m_utils->getCTOCotizacion_v2($clasificacion, $tipo_cliente);
			if(($clasificacion == 'NAP EXPRESS VERDE' || $clasificacion == 'ESTUDIO ESPECIAL GRIS' || $clasificacion == 'ESTUDIO DE CAMPO' || $clasificacion == 'NAP EXPRESS AMARILLO') && $tipo_cliente != 'Centro Comercial') {
				$dataRobot = _trama_distancia_robot_cv($latitud, $longitud, 1);//EDIFICIOS
				
				$fac_red = $dataRobot['codigo'];
				
            }
			
			$dataRobot['fac_red'] = $fac_red;
			
			// LOGICA DE LOS COSTOS
			$dataCostosFin = array();
			if($idCentral != null) {
				$dataCostos['clasificacion'] = $clasificacion;
				$dataCostos['tipo_cliente'] = $tipo_cliente;
				$dataCostos['distancia'] = $dataRobot['distancia'];
				$dataCostos['longitud'] = $longitud;
				$dataCostos['latitud'] = $latitud;
				$dataCostos['hilo_disp'] = $dataRobot['hilo_disp'];
				$dataCostos['idCentral'] = $idCentral;
				$dataCostos['idSubProyecto'] = $idSubProyecto;
				$dataCostos['tipo'] = $dataRobot['tipo'];
				$dataCostos['req_inc'] = $req_inc;
				
				$dataCostosFin = _getCostosRobotCvManual($dataCostos, null);
				///////////	
			}

			$arrayData = $dataRobot;
			
			return array($arrayData, $dataCostosFin);
		}
	}
	
	if(!function_exists('_trama_distancia_robot_cv')) {
		function _trama_distancia_robot_cv($latitud, $longitud, $flg_edif) {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			
			$arrayCto = $CI->m_utils->getCtoByCoordEdif($latitud, $longitud);
			
			$hilo_disp = null;
			$lat_ctoResEbcMdf = null;
			$lon_ctoResEbcMdf = null;
			$id_terminal 	  = null;
			
			$arrayReserva = $CI->m_utils->getReservasByCoordV2($latitud, $longitud, $flg_edif);

			if($arrayCto['codigo'] == null || $arrayCto['codigo'] == '') {
				if($arrayReserva['codigo'] == null || $arrayReserva['codigo'] == '') {
						$arrayMdf  = $CI->m_utils->getMdfCoord($latitud, $longitud);
						
						$codigo    = $arrayMdf['codigo'];
						$distancia = $arrayMdf['distancia'];
						$lat_ctoResEbcMdf = $arrayMdf['latitud'];
						$lon_ctoResEbcMdf = $arrayMdf['longitud'];
						$tipo      = 'MDF';					
				} else {
					$codigo    = $arrayReserva['codigo'];
					$distancia = $arrayReserva['distancia'];
					$hilo_disp = $arrayReserva['hilos_disponibles'];
					$lat_ctoResEbcMdf = $arrayReserva['latitud'];
					$lon_ctoResEbcMdf = $arrayReserva['longitud'];
					$id_terminal      = $arrayReserva['id_terminal'];
					$tipo      = 'RESERVA';
				}
			} else {
				$codigo    = $arrayCto['codigo'];
				$distancia = $arrayCto['distancia'];
				$hilo_disp = $arrayCto['disponible_hilos'];
				$lat_ctoResEbcMdf = $arrayCto['latitud'];
				$lon_ctoResEbcMdf = $arrayCto['longitud'];
				$id_terminal      = $arrayCto['id_terminal'];
				$tipo      = 'CTO';
			}
			
			$arrayData = array(
								'codigo'    => $codigo,
								'distancia' => $distancia,
								'tipo'      => $tipo,
								'hilo_disp' => $hilo_disp,
								'lat_ctoResEbcMdf' => $lat_ctoResEbcMdf,
								'lon_ctoResEbcMdf' => $lon_ctoResEbcMdf,
								'id_terminal'      => $id_terminal
							  );
			return $arrayData;		  
		}
	}
	
	if(!function_exists('__data_robot_coti_luis_pedido')) {
		function __data_robot_coti_luis_pedido($clasificacion, $tipo_cliente, $longitud, $latitud, $idCentral=null, $idSubProyecto=null, $req_inc=null) {
			$CI =& get_instance();
			$CI->load->library('map_utils/coordenadas_utils');
			$tipo      = NULL;
			$codigo    = NULL;
			$distancia = NULL;
			$hilo_disp = NULL;
			$id_terminal = NULL;
			
			$clasificacion = strtoupper(trim($clasificacion));

			$rowCtOTipoDiseno = $CI->m_utils->getCTOCotizacion_v2($clasificacion, $tipo_cliente);
			if(($clasificacion == 'NAP EXPRESS VERDE' || $clasificacion == 'ESTUDIO ESPECIAL GRIS' || $clasificacion == 'ESTUDIO DE CAMPO' || $clasificacion == 'NAP EXPRESS AMARILLO') && $tipo_cliente != 'Centro Comercial') {
				if($rowCtOTipoDiseno['id_tipo_diseno'] == 6 || $rowCtOTipoDiseno['id_tipo_diseno'] == 7) {//EDIFICIOS
					$dataRobot = _trama_distancia_robot($latitud, $longitud, 1);
				} else {
					$dataRobot = _trama_distancia_robot($latitud, $longitud, 2);
				}
				
				$fac_red = $dataRobot['codigo'];
				
            } else {//LOGICA ANTERIOR
				list($arrayCen, $distancia) = _getDataKmz($longitud, $latitud);//TAIGO LA DISTANCIA DEL CLIENTE A LA CTO 
				$codigo = $arrayCen['codigo'];
				$fac_red = _getCtoInfo($longitud, $latitud);
				
				if($fac_red == null || $fac_red == '') {
					$fac_red = $codigo;
					$dataRobot['lat_ctoResEbcMdf'] = $arrayCen['latitud'];
					$dataRobot['lon_ctoResEbcMdf'] = $arrayCen['longitud'];
				} else {
					$dataRobot['lat_ctoResEbcMdf'] = null;
					$dataRobot['lon_ctoResEbcMdf'] = null;
				}

				$dataRobot['codigo']    = $codigo;
				$dataRobot['hilo_disp'] = null;
				$dataRobot['tipo']      = null;
				$dataRobot['distancia'] = $distancia;
				$dataRobot['id_terminal'] = null;
            }
			
			$dataRobot['fac_red'] = $fac_red;
			
			// LOGICA DE LOS COSTOS
			$dataCostosFin = array();
			if($idCentral != null) {
				$dataCostos['clasificacion'] = $clasificacion;
				$dataCostos['tipo_cliente'] = $tipo_cliente;
				$dataCostos['distancia'] = $dataRobot['distancia'];
				$dataCostos['longitud'] = $longitud;
				$dataCostos['latitud'] = $latitud;
				$dataCostos['hilo_disp'] = $dataRobot['hilo_disp'];
				$dataCostos['idCentral'] = $idCentral;
				$dataCostos['idSubProyecto'] = $idSubProyecto;
				$dataCostos['tipo'] = $dataRobot['tipo'];
				$dataCostos['req_inc'] = $req_inc;
				
				$dataCostosFin = _getCostosRobotCvManual($dataCostos, null);
				///////////	
			}
			$arrayData = $dataRobot;
			
			return array($arrayData, $dataCostosFin);
		}
	}
	
	if(!function_exists('_getCostosRobotCvManual')) {
		function _getCostosRobotCvManual($dataCostos, $flgSimu, $flg_tipo_diseno = null, $tecnologia = null) {
			$CI =& get_instance();

			$clasificacion = $dataCostos['clasificacion'];
			$tipo_cliente  = $dataCostos['tipo_cliente'];
			$distancia     = $dataCostos['distancia'];
			$longitud      = $dataCostos['longitud'];
			$latitud       = $dataCostos['latitud'];
			$hilo_disp     = $dataCostos['hilo_disp'];
			$idCentral     = $dataCostos['idCentral'];
			$idSubProyecto = $dataCostos['idSubProyecto'];
			$tipo          = $dataCostos['tipo'];
			$inc_necesita  = $dataCostos['req_inc'];
			
			$arrayDataCentral = $CI->m_utils->getCentralPqt($idCentral);
			if($inc_necesita == null) {
				$inc_necesita = _getDataKmzArqueologico($longitud, $latitud);
			}
			
			$metroTendidoAe   = $distancia + ($distancia * 0.30);
			$clasificacion = strtoupper(trim($clasificacion));
			$arrayData = $CI->m_utils->getDiasMatriz($metroTendidoAe, NULL, NULL, $inc_necesita, $arrayDataCentral['flg_tipo_zona'], $arrayDataCentral['jefatura']);
			
			$data['seia'] = $arrayData['seia'];
			$data['mtc']  = $arrayData['mtc'];
			$data['inc']  = $inc_necesita; 
			$data['eecc'] = $arrayDataCentral['empresaColabDesc'];
			$data['hilo_disp'] = $hilo_disp;
			$data['costo_mo_edif']  = 0;
			$data['costo_mat_edif'] = 0;
			$data['costo_oc_edif']  = 0;
			
			$rowCtOTipoDiseno = $CI->m_utils->getCTOCotizacion_v2($clasificacion, $tipo_cliente);
			
			
			if($rowCtOTipoDiseno['costo_total'] == null) {
				$rowMatrizCosto = $CI->m_utils->getCostosMatrizCotizacionV2($metroTendidoAe, $data['seia'], $data['inc'], 2, $rowCtOTipoDiseno['id_tipo_diseno']);

				if($rowCtOTipoDiseno['id_tipo_diseno'] == 7) { //CLUSTER EN EDIFICIO
					if($data['hilo_disp'] == 0 || $data['hilo_disp'] == '' || $data['hilo_disp'] == null || $tipo == 'RESERVA') {// SI ESTA SATURADO, SE DEBE CONSTRUIR CTO
						$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
						$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
						$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total']+$rowMatrizCosto['costo_edif'];
						$data['total_mo_pqt']  = $rowCostoTotalPqt['total_mo_pqt'];
						$data['costo_mo_edif']  = $rowMatrizCosto['costo_mo_edif'];
						$data['costo_mat_edif'] = $rowMatrizCosto['costo_mat_edif'];
						$data['costo_oc_edif']  = $rowMatrizCosto['costo_oc_edif'];
						
						$data['costo_oc']       = $rowMatrizCosto['costo_oc'];
						
						
						$data['costo_mat_envio_sisego'] = $data['costo_mat_edif']+$data['costo_oc_edif']+$rowMatrizCosto['mat_total'];
						$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
						
						$data['tipo_diseno'] = $rowCtOTipoDiseno['tipo_diseno'];
						$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
						if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
							$data['duracion'] = $arrayData['dias'];
						} else {
							$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
						}
						$data['cant_cto']    = 5;
					} else {// SI HAY CTO ENTRA COMO UM
						if($tecnologia == 'GPON') {
							$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
							$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
							$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total']+$rowMatrizCosto['costo_edif'];
							$data['total_mo_pqt']  = $rowCostoTotalPqt['total_mo_pqt'];
							$data['costo_mo_edif']  = $rowMatrizCosto['costo_mo_edif'];
							$data['costo_mat_edif'] = $rowMatrizCosto['costo_mat_edif'];
							$data['costo_oc_edif']  = $rowMatrizCosto['costo_oc_edif'];
							
							$data['costo_oc']       = $rowMatrizCosto['costo_oc'];
							$data['costo_mat_envio_sisego'] = $data['costo_mat_edif']+$data['costo_oc_edif']+$rowMatrizCosto['mat_total'];
							$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
							
							$data['tipo_diseno'] = $rowCtOTipoDiseno['tipo_diseno'];
							$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
							if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
								$data['duracion'] = $arrayData['dias'];
							} else {
								$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
							}
							$data['cant_cto']    = 5;
						} else {
							$rowMatrizCosto['mo_total']  = $rowMatrizCosto['costo_total_um']*0.60;
							$rowMatrizCosto['mat_total'] = $rowMatrizCosto['costo_total_um']*0.40;
							
							$data['tipo_diseno'] = 'UM EDIFICIO';
							$data['id_tipo_diseno'] = $CI->m_utils->getIdTipoDisenoByDescDiseno($data['tipo_diseno']);
							$data['duracion']    = 7;
							$data['cant_cto']    = 0;
							
							$rowMatrizCosto['inc_total'] = 0;
							$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total'];
						
							$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
							$data['costo_mo_envio_sisego'] = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total'];
						}
					}
				} else {
					if($metroTendidoAe <= 200 && $tipo == 'CTO') {//SI LOS METROS ES MENOR O IGUAL A 200 ES UM.
						if($flgSimu == 1) {//POR AHORA SOLO SIMULADOR TOMA UM
							if($flg_tipo_diseno == 1) {
								$data['tipo_diseno'] = $rowCtOTipoDiseno['tipo_diseno'];
								$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
								if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
									$data['duracion'] = $arrayData['dias'];
								} else {
									$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
								}
								
								$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
								$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
								$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
								$data['total_mo_pqt']  = $rowCostoTotalPqt['total_mo_pqt'];
								$data['cant_cto']           = $rowCtOTipoDiseno['cto'];
								$data['costo_oc']       = $rowMatrizCosto['costo_oc'];
								$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
								$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
							} else {
								$rowMatrizCosto['mo_total']  = $rowMatrizCosto['costo_total_um']*0.60;
								$rowMatrizCosto['mat_total'] = $rowMatrizCosto['costo_total_um']*0.40;
								$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total'];
								$rowMatrizCosto['inc_total']  = 0;
								$data['tipo_diseno'] = 'UM';
								$data['id_tipo_diseno'] = $CI->m_utils->getIdTipoDisenoByDescDiseno($data['tipo_diseno']);
								$data['duracion']    = 7;
								$data['cant_cto']    = 0;
								
								$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
								$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total'];
							}
						} else {
							$data['tipo_diseno'] = $rowCtOTipoDiseno['tipo_diseno'];
							$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
							if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
								$data['duracion'] = $arrayData['dias'];
							} else {
								$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
							}
							
							$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
							$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
							
							$data['total_mo_pqt']  = $rowCostoTotalPqt['total_mo_pqt'];
							$data['costo_oc']       = $rowMatrizCosto['costo_oc'];
							$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
							$data['cant_cto']           = $rowCtOTipoDiseno['cto'];
							
							$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
						    $data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
						}

						
					} else {
						if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
							$data['duracion'] = $arrayData['dias'];
						} else {
							$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
						}
						$data['tipo_diseno']    = $rowCtOTipoDiseno['tipo_diseno'];
						$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
						
						if($rowMatrizCosto['mat_total']) {
							$rowCostoTotalPqt = $CI->m_cotizacion_alcance_robot->getCostoTotalPaquetizado($idSubProyecto, $arrayDataCentral['idEmpresaColab'], 5, $arrayDataCentral['jefatura']);
							$rowMatrizCosto['mo_total'] = $rowCostoTotalPqt['total_mo_pqt']+$rowMatrizCosto['costo_oc'];
							
							$data['costo_oc']       = $rowMatrizCosto['costo_oc'];
							
							$data['total_mo_pqt']  = $rowCostoTotalPqt['total_mo_pqt'];
							$rowMatrizCosto['total']    = $rowMatrizCosto['mo_total']+$rowMatrizCosto['mat_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
							$data['cant_cto']           = $rowCtOTipoDiseno['cto'];
							
							$data['costo_mat_envio_sisego'] = $rowMatrizCosto['mat_total'];
							$data['costo_mo_envio_sisego']  = $rowMatrizCosto['mo_total']+$rowMatrizCosto['eia_total']+$rowMatrizCosto['inc_total'];
						} else {
							$rowMatrizCosto['mat_total'] = null;
							$rowMatrizCosto['mo_total'] = null;
							$rowMatrizCosto['total']    = null;
							$rowMatrizCosto['metro_oc'] = null;
							$data['cant_cto']           = null;
							$data['costo_mat_envio_sisego'] = null;
							$data['costo_mo_envio_sisego']  = null;
							$data['total_mo_pqt']           = null;
							$rowMatrizCosto['costo_oc']               = null;
							$rowMatrizCosto['eia_total']              = null;
							$rowMatrizCosto['inc_total']              = null;
						}
					}
				}

            } else {
				if($rowCtOTipoDiseno['tiempo'] == null) { // SI EL TIEMPO EN LA MATRIZ DE CTO ES NULL, MUESTRO LOS DIAS DE LA MATRIZ DIAS (ACTUAL).
					$data['duracion'] = $arrayData['dias'];
				} else {
					$data['duracion'] = $rowCtOTipoDiseno['tiempo'];
				}
				
				if($clasificacion == 'ESTUDIO DE CAMPO') {
					$costoMo  = $rowCtOTipoDiseno['costo_total'];
					$costoMat = 0;
				} else {
					$costoMat = $rowCtOTipoDiseno['costo_total']*0.60;
					$costoMo  = $rowCtOTipoDiseno['costo_total']*0.40;
				}
                
				$rowMatrizCosto = array('total' 	=> $rowCtOTipoDiseno['costo_total'],
				                        'mo_total'  => $costoMo,
										'mat_total' => $costoMat,
										'diseno_total' => 0,
										'inc_total'  => 0,
                                        'eia_total'  => 0,
										'metro_oc'   => '',
										'crxa'       => '',
										'crxc' 		 => '',
										'postes'     => '');
				
				$data['costo_mat_envio_sisego'] = $costoMat;
				$data['costo_mo_envio_sisego']  = $costoMo;
				
                $data['tipo_diseno']    = $rowCtOTipoDiseno['tipo_diseno'];
				$data['id_tipo_diseno'] = $rowCtOTipoDiseno['id_tipo_diseno'];
				$data['cant_cto']       = $rowCtOTipoDiseno['cto'];
            }
			
			$data['distancia']      = $distancia;
			$data['metrosTendidos'] = $metroTendidoAe;
            $data['arrayCostos']    = $rowMatrizCosto;
			
			return $data;
		}
	}

	if(!function_exists('_getArrayIDSubProyNuevoFlujoCV')) {
		function _getArrayIDSubProyNuevoFlujoCV($flg_tipo = null) {
			$CI =& get_instance();
			
			$dataArray = $CI->m_utils->getSubProyectoNuevoFLujoCV($flg_tipo);
			$arrayIds = array();
			foreach($dataArray as $row) {
				$arrayIds[] = $row['idSubProyecto'];
			}
			return $arrayIds;
		}
	}

	if(!function_exists('_getArrayIDContrataFlujoCVByFlgCV')) {
		function _getArrayIDContrataFlujoCVByFlgCV($flg_cv) {
			$CI =& get_instance();
			
			$dataArray = $CI->m_utils->getEECCNuevoFLujoCV($flg_cv);
			$arrayIds = array();
			foreach($dataArray as $row) {
				$arrayIds[] = $row['idEmpresaColab'];
			}
			return $arrayIds;
		}
	}

	if(!function_exists('_convertirFechaDatePicker')) {
		function _convertirFechaDatePicker($fecha,$separador = "-") {
			$CI =& get_instance();
			$array = explode(" ", $fecha);
        	$array2 = explode($separador, $array[0]);
        	return $array2[2] . '-' . $array2[1] . '-' . $array2[0] . ' ' . (isset($array[1]) ? $array[1] : '00:00:00');
		}
	}

	if(!function_exists('_getFormatoFechaDatePicker')) {
		function _getFormatoFechaDatePicker($fecha, $s = "-", $conHms = null) {
			$CI =& get_instance();
			$date = new DateTime($fecha);
	 		return $date->format('d'.$s.'m'.$s.'Y'.($conHms == null ? ' h:i:s' : null));
		}
	}
	
	if(!function_exists('_getModulosFlotante')) {
		function _getModulosFlotante() {
			$CI =& get_instance();
			
			$dataArray = $CI->m_utils->getModulos();
			$htmlData = null;
			foreach($dataArray as $row) {
				$htmlData .= '	<li>
									<a href="'.$row['routeBienvenido'].'" class="app-list-item hover-white">
										<span class="icon-stack">
											<i class="base-2 icon-stack-3x color-primary-600"></i>
											<i class="base-3 icon-stack-2x color-primary-700"></i>
											<i class="ni ni-settings icon-stack-1x text-white fs-lg"></i>
										</span>
										<span class="app-list-name">
											'.$row['abrv'].'
										</span>
									</a>
								</li>';
			}
			return $htmlData;
		}
	}
}

if(!function_exists('_fecha_actual')) {
    function _fecha_actual() {
        $zonahoraria = date_default_timezone_get();
        ini_set('date.timezone', 'America/Lima');
        setlocale(LC_TIME, "es_ES", "esp");
        $hoy = strftime("%Y-%m-%d %H:%M:%S");
        return $hoy;
    }
}

if (!function_exists('_removeEnterYTabs')) {
	function _removeEnterYTabs($texto) {
		return str_replace(PHP_EOL, ' ', trim(preg_replace('/[ ]{2,}|[\t]/', ' ', $texto)));
	}
}


function convert_object_to_array($data) {

    if (is_object($data)) {
        $data = get_object_vars($data);
    }

    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    }
    else {
        return $data;
    }
}