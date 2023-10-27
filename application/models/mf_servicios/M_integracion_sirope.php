<?php
class M_integracion_sirope extends CI_Model{
	function __construct(){
		parent::__construct();
		
	}
	
	function execWs($itemplan, $codigoOT, $fechaInicio, $fechaFin, $caracteristica){
	    $rpta['error'] = EXIT_ERROR;
	    $rpta['msj']   = null;
	    try{
			
    	    $webservice_url = "https://www.sirope.movistar.com.pe/cpqd/ws/meta/WorkOrderService/create";
    	    $userid = 'webpo';
    	    //$password = 'Integra#1';
    	    $password = 'Plan2022$';
    	    $xmlToSend = $this->makeXMLToSendSirope($userid, $password, $codigoOT, $fechaInicio, $fechaFin, $caracteristica);
   
    	    $datosInsert = array(  
				'itemplan' => $itemplan,
    	        'codigo_ot'     => $codigoOT,
    	        'fecha_envio'   => $this->fechaActual(),
    	        'xml_envio'     => $xmlToSend,
    	        'id_usuario'    => $this->session->userdata('idPersonaSessionPan')
    	    );
    	    
    	    $ch = curl_init($webservice_url);
    	    curl_setopt ($ch, CURLOPT_POST, 1);
    	    curl_setopt ($ch, CURLOPT_POSTFIELDS, $xmlToSend);
    	    //curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
    	    // time allowed to connect to server
    	    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
    	    // time allowed to process curl call
    	    curl_setopt($ch,CURLOPT_TIMEOUT,120);
    	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	    curl_setopt($ch, CURLOPT_VERBOSE, true);
    	    $verbose = fopen('php://temp', 'w+');
    	    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    	    //curl_setopt($ch, CURLOPT_USERPWD, "<username>:<password>");
    	
    	    $headers = array(
    	        //'Content-Type:application/xml',
    	        'Content-Type: text/xml;charset=UTF-8',
    	        'Authorization: Basic '. base64_encode('"'.$userid.'":"'.$password.'"'),
    	        "Content-length: ".strlen($xmlToSend)
    	    );
    	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
    	    curl_setopt($ch, CURLOPT_SSLVERSION,'all');	
    	    $result = curl_exec ($ch);
    	    
    	    if ($result === FALSE) {//error conexion...
    	        $datosInsert['estado'] = 3;//ERROR CONEXION
    	        //log_message("error", '-->'.curl_errno($ch).'-'.htmlspecialchars(curl_error($ch)));
    	    }else{//si conecto!!	       
			    $xml_response = str_ireplace("ns1:", "", str_ireplace("soap:", "", $result));
				//log_message('error', 'REENVIO SIROPE:'.$xml_response);#fecha de inicio el dia de hoy y prev 7 dias despues..
    	        $objeto_xml = simplexml_load_string($xml_response);
    	        if($objeto_xml->Body->Fault){//error
    	          //  log_message('error', "Fault : ".print_r($objeto_xml->Body->Fault,true));
    	            $datosInsert['estado'] = 2;//NO OK
    	            $datosInsert['xml_recibido'] = $result;
    	            $datosInsert['mensaje_recibido'] = $objeto_xml->Body->Fault->faultstring;
    	        }
    	         
    	        if($objeto_xml->Body->createResponse){//creo bien la OT
    	            //log_message('error', "createResponse : ".print_r($objeto_xml->Body->createResponse,true));
    	            $datosInsert['estado'] = 1;//OK
    	            $datosInsert['xml_recibido'] = $result;
    	            $datosInsert['mensaje_recibido'] = 'SE CREO LA OT';
    	        }    	        
    	    }
    	
    	    curl_close ($ch);
    	    $rpta = $this->saveLogSigoplusFromSIOM($datosInsert);
	    }catch(Exception $e){
	        $rpta['msj'] = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $rpta;
	    
	}
	
    function makeXMLToSendSirope($userid, $password, $codigoOT, $fechaInicio, $fechaFin, $caracteristica){//fecha YYYY-mm-dd
        $soap_request  = '<soapenv:Envelope xmlns:con="http://context.ws.cpqd.com.br" xmlns:meta="http://meta.webservices.cpqd.com.br" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                            <soapenv:Header>';
        $soap_request .= $this->getHeaderWS($userid, $password);
        $soap_request .= '</soapenv:Header>
                            <soapenv:Body>
                              <meta:create>
                                 <!--Optional:-->
                                 <meta:workOrder>
                                    <characteristic>'.$caracteristica.'</characteristic>
                                    <code>'.$codigoOT.'</code>
                                    <estimatedEndDate>'.$fechaFin.'</estimatedEndDate>
                                    <startDate>'.$fechaInicio.'</startDate>
                                 </meta:workOrder>
                              </meta:create>
                            </soapenv:Body>
                          </soapenv:Envelope>';
        return $soap_request;
    }
	
	function getHeaderWS($userid, $password){
	    $nonce_t = mt_rand();
	    #$timeStam = gmdate('Y-m-d\TH:i:s\Z', time() - 30);//2019.10.29 se resto 30 segundos al envio por motivos de hora entre servidores.
	    $timeStam = gmdate('Y-m-d\TH:i:s\Z', time() - 240);
		$passwordDigest = $this->generatePasswordDigest($password, $nonce_t, $timeStam);
	    $xml = '<wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                    <wsse:UsernameToken>
                        <wsse:Username>' . $userid . '</wsse:Username>
                        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">' . $passwordDigest . '</wsse:Password>
                        <wsse:Nonce>' . base64_encode(pack('H*', $nonce_t)) . '</wsse:Nonce>
                        <wsu:Created xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $timeStam . '</wsu:Created>
                    </wsse:UsernameToken>
                </wsse:Security>';
	    return $xml;
	}
	
	function generatePasswordDigest($password, $nonce_t, $timeStam)
	{
	    // Can use rand() to repeat the word if the server is under high load
	    $packedNonce = pack('H*', $nonce_t);
	    $packedTimestamp = pack('a*', $timeStam);
	    $packedPassword = pack('a*', $password);
	    $hash = sha1($packedNonce . $packedTimestamp . $packedPassword);
	    $packedHash = pack('H*', $hash);
	    return base64_encode($packedHash);
	}
	
	
	function saveLogSigoplusFromSIOM($datosInsert){
	    $rpta['error'] = EXIT_ERROR;
	    $rpta['msj']   = null;
	    try{
	        $this->db->trans_begin();
	        $this->db->insert('log_tramas_sirope',$datosInsert);
	        if($this->db->affected_rows() != 1) {
	            $this->db->trans_rollback();
	            throw new Exception('Error al insertar en log_tramas_sirope');
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
	
	public function fechaActual()
	{
	    $zonahoraria = date_default_timezone_get();
	    ini_set('date.timezone', 'America/Lima');
	    setlocale(LC_TIME, "es_ES", "esp");
	    $hoy = strftime("%Y-%m-%d %H:%M:%S");
	    return $hoy;
	}
	
	
	/**solo xml to test*/
	
	function getXMLexecWs($itemplan, $codigoOT, $fechaInicio, $fechaFin){
	  
	$userid = 'webpo';
	$password = 'Integra#1';
		
	$xmlToSend = $this->makeXMLToSendSirope($userid, $password, $codigoOT, $fechaInicio, $fechaFin);
	log_message('error', $xmlToSend);
	}
	
	function getXMLexecWsFilter($codigoOT){
	     
	    $userid = 'webpo';
	    $password = 'Plan2022$';
	
	    $xmlToSend = $this->makeXMLToSendSiropeFilter($userid, $password, $codigoOT);
	    log_message('error', $xmlToSend);
	}
	
	
	function execWsFilter($itemplan, $codigoOT){
	    $rpta['error'] = EXIT_ERROR;
	    $rpta['msj']   = null;
	    try{
	        	
	        $webservice_url = "https://www.sirope.movistar.com.pe/cpqd/ws/meta/WorkOrderService/filter";
	        $userid = 'webpo';
	        //$password = 'Integra#1';
	        $password = 'Plan2022$';
	        $xmlToSend = $this->makeXMLToSendSiropeFilter($userid, $password, $codigoOT);
	         
	        $datosInsert = array(  'itemplan'      => $itemplan,
                    	            'codigo_ot'     => $codigoOT,
                    	            'fecha_envio'   => $this->fechaActual(),
                    	            'xml_envio'     => $xmlToSend,
                    	            'id_usuario'    => $this->session->userdata('idPersonaSessionPan')
                    	        );
	        $estado_actual_ot =    null;
	        $ch = curl_init($webservice_url);
	        curl_setopt ($ch, CURLOPT_POST, 1);
	        curl_setopt ($ch, CURLOPT_POSTFIELDS, $xmlToSend);
	        //curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
	        // time allowed to connect to server
	        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
	        // time allowed to process curl call
	        curl_setopt($ch,CURLOPT_TIMEOUT,120);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_VERBOSE, true);
	        $verbose = fopen('php://temp', 'w+');
	        curl_setopt($ch, CURLOPT_STDERR, $verbose);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	        //curl_setopt($ch, CURLOPT_USERPWD, "<username>:<password>");
	         
	        $headers = array(
	            //'Content-Type:application/xml',
	            'Content-Type: text/xml;charset=UTF-8',
	            'Authorization: Basic '. base64_encode('"'.$userid.'":"'.$password.'"'),
	            "Content-length: ".strlen($xmlToSend)
	        );
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_SSLVERSION,'all');
	        $result = curl_exec ($ch);
			
	        if ($result === FALSE) {//error conexion...
	            $datosInsert['estado'] = 3;//ERROR CONEXION
	            //log_message("error", '-->'.curl_errno($ch).'-'.htmlspecialchars(curl_error($ch)));
	        }else{//si conecto!!
	            $xml_response = str_ireplace("ns1:", "", str_ireplace("soap:", "", $result));
	            $objeto_xml = simplexml_load_string($xml_response);
	            if($objeto_xml->Body->Fault){//error
	                //log_message('error', "Fault : ".print_r($objeto_xml->Body->Fault,true));
	                $datosInsert['estado'] = 2;//NO OK
	                $datosInsert['xml_recibido'] = $result;
	                $datosInsert['mensaje_recibido'] = $objeto_xml->Body->Fault->faultstring;
	            }
	
	            if($objeto_xml->Body->filterResponse){//creo bien la OT
	                //log_message('error', "createResponse : ".print_r($objeto_xml->Body->createResponse,true));
	                $datosInsert['estado'] = 1;//OK
	                $datosInsert['xml_recibido'] = $result;
	                $datosInsert['mensaje_recibido'] = 'SE ACTUALIZO LA OT';
	                $datosInsert['estado_ot']  =   $objeto_xml->Body->filterResponse->workOrder->currentState;
	                $datosInsert['id_ot']  =   $objeto_xml->Body->filterResponse->workOrder->id;
	            }
	        }
	         
	        curl_close ($ch);
	        $rpta = $this->saveLogConsultaEstadoOTSirope($datosInsert);
	    }catch(Exception $e){
	        $rpta['msj']   = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $rpta;
	     
	}
	
	function makeXMLToSendSiropeFilter($userid, $password, $codigoOT){//fecha YYYY-mm-dd
	    $soap_request  = '<soapenv:Envelope xmlns:con="http://context.ws.cpqd.com.br" xmlns:meta="http://meta.webservices.cpqd.com.br" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                            <soapenv:Header>';
	    $soap_request .= $this->getHeaderWS($userid, $password);
	    $soap_request .= '</soapenv:Header>
                            <soapenv:Body>
                              <meta:filter>
                                 <!--Optional:-->
                                 <meta:workOrder>                                    
                                    <code>'.$codigoOT.'</code>                                    
                                 </meta:workOrder>
                              </meta:filter>
                            </soapenv:Body>
                          </soapenv:Envelope>';
	    return $soap_request;
	}
	
	function saveLogConsultaEstadoOTSirope($datosInsert){
	    $rpta['error'] = EXIT_ERROR;
	    $rpta['msj']   = null;
	    try{
	        $this->db->trans_begin();
	        $this->db->insert('log_tramas_sirope_filter',$datosInsert);
	        if($this->db->affected_rows() != 1) {
	            $this->db->trans_rollback();
	            throw new Exception('Error al insertar en log_tramas_sirope_filter');
	        }else{
	            $this->db->trans_commit();
	            $rpta['error'] = EXIT_SUCCESS;
	            $rpta['msj'] = 'Se agrego correctamente!';
	        }
	    }catch(Exception $e){
	        $rpta['msj'] = $e->getMessage();
	        $this->db->trans_rollback();
	    }
	    return $rpta;
	}
}