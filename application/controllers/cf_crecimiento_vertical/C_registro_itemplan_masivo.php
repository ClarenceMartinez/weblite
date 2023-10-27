<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_registro_itemplan_masivo extends CI_Controller {


    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_crecimiento_vertical/m_registro_itemplan_masivo');
		$this->load->model('mf_servicios/m_integracion_sirope');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        //$this->load->library('encrypt');
        $this->load->library('excel');
        $this->load->helper('url');
    }

    public function index() {
        $idUsuario  = $this->session->userdata('idPersonaSessionPan');
	    if($idUsuario != null){           
            $permisos =  $this->session->userdata('permisosArbolPan');         
            $result = $this->lib_utils->getHTMLPermisos($permisos, 7, null, 13, null);
            $data['opciones'] = $result['html'];
            $data['header'] = $this->lib_utils->getHeader();
            $this->load->view('vf_crecimiento_vertical/v_registro_itemplan_masivo',$data);        	  
    	 }else{
        	 redirect('login','refresh');
	    }     
    }


    function getExcelCargaMasiva()
    {
		$data['error'] = EXIT_ERROR;
		$data['msj'] = null;

		try {

			/*AQUI CREAMOS EL EXCEL*/
			$spreadsheet = $this->excel;

			$spreadsheet
				->getProperties()
				->setCreator('Fernando Paolo Luna Villalba')
				->setLastModifiedBy('Fernando Paolo Luna Villalba')
				->setTitle('Excel creado con PhpSpreadSheet')
				->setSubject('Excel de prueba')
				->setDescription('Excel generado como prueba')
				->setKeywords('PHPSpreadsheet')
				->setCategory('Categoría de prueba');

 			$hoja = $spreadsheet->getActiveSheet();
			$hoja->getSheetView()->setZoomScale(85); // zoom por defecto a la hoja
			$hoja->setTitle('FORMATO CARGA');

			// $col = 0;
			// $row = 1;
			$col = 0;
			$row = 1;
			$hoja->setCellValueByColumnAndRow($col, $row, 'SUBPROYECTO');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'LONGITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'LATITUD');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'EEECC');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NOM. PROYECTO');
			$col++;
//			$hoja->setCellValueByColumnAndRow($col, $row, 'NRO PISOS');
//			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'NRO DEPA');
			$col++;
/*   		$hoja->setCellValueByColumnAndRow($col, $row, 'LOTE');
			$col++;
			$hoja->setCellValueByColumnAndRow($col, $row, 'MANZANA');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'ESTADO EDIFICIO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TIPO PROYECTO');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'RUC');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NOMBRE CONTRUCTORA');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'CONTACTO_1');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TELEFONO_1');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'TELEFONO_1_2');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DIRECCIÓN');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'DISTRITO');
			$col++;*/
            $hoja->setCellValueByColumnAndRow($col, $row, 'CÓDIGO INVERSIÓN');
			$col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'COSTO MO (ACTIVACIONES)');
			$col++;


            $estiloTituloColumnas = array(
                'font' => array(
                    'name' => 'Calibri',
                    'bold' => true,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
            );

            $hoja->getStyle('A1:G1')->applyFromArray($estiloTituloColumnas);

			$col = 0;
			$row = 2;
            $hoja->setCellValueByColumnAndRow($col, $row, 'PANGEA C.E RESIDENCIAL - INTEGRAL');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '-77.02824');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '-12.04318');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'COBRA');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'EJEMPLO');
            $col++;
//          $hoja->setCellValueByColumnAndRow($col, $row, 1);
//          $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 2);
            $col++;
/*          $hoja->setCellValueByColumnAndRow($col, $row, 1);
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 2);
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'NUEVO');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'FTTH');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '12345123');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'XXXXXXXX');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, '');
            $col++;*/
            $hoja->setCellValueByColumnAndRow($col, $row, '3000000035');
            $col++;
            $hoja->setCellValueByColumnAndRow($col, $row, 'LLENAR SOLO PARA ACTIVACIONES');
            $col++;

			$estilo = [
                'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => [
							'rgb' => '000000',
						],
					)
				)
            ];
            $hoja->getStyle('A1' . ':' . ($hoja->getHighestColumn()) . ($hoja->getHighestRow()))->applyFromArray($estilo);
            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel5');

			ob_start();
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

			$data['error'] = EXIT_SUCCESS;
			$data['archivo'] = "data:application/vnd.ms-excel;base64," . base64_encode($xlsData);
			
		} catch (Exception $e) {
			$data['message'] = $e->getMessage();
		}

		echo json_encode($data);

	}

    public function regItemplanCvMasivo() 
    {
        $data['error']    = EXIT_ERROR;
        $data['msj']      = null; 

        try {
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
			
            if($idUsuario == null) {
                throw new Exception('La sesión a expirado, recargue la página');
            }
            if(count($_FILES) == 0){
                throw new Exception('Debe poner un archivo para registrar!! ');
            }
            $arrayRegistro = array();
            $cont = 0;
            $path   = $_FILES['file']['tmp_name'];
            $object = PHPExcel_IOFactory::load($path);
            foreach($object->getWorksheetIterator() as $worksheet) {
                $highestRow    = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $cont = 1;
                for($row=2; $row<=$highestRow; $row++) {
                    $subproyecto = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $longitud = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $latitud = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $eecc = $worksheet->getCellByColumnAndRow(3, $row)->getValue();                    
                    $nom_proyecto = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $nro_dep = $worksheet->getCellByColumnAndRow(5, $row)->getValue();         
                    $codigoInversion = $worksheet->getCellByColumnAndRow(6, $row)->getValue();   
                    $costo_mo   = $worksheet->getCellByColumnAndRow(7, $row)->getValue();   
                   // $itemplan = $worksheet->getCellByColumnAndRow(7, $row)->getValue();   //TEMPORAL                 
                    $idSubProyecto = $this->m_utils->getIdSubProyectoBySubProyectoDesc(trim($subproyecto));
                    $idEmpresaColab = $this->m_utils->getEmpresaColabByDesc(trim($eecc));
                    $dataCentral = _getCentralCercana($longitud, $latitud);
                    log_message('error', $costo_mo);
                    $dataRegistro = array(
              //          'itemplan'          =>  $itemplan,//TEMPORAL
                        'nro'              => $cont,
                        'idSubProyecto'    => $idSubProyecto,
                        'idZonal'          => $dataCentral['idZonal'],
                        'longitud'         => $longitud,           	 
                        'latitud'          => $latitud,
                        'nom_proyecto'     => $nom_proyecto,
                        'nro_dep'          => $nro_dep,
                        'nro_pisos'        => null,
                        'idEmpresaColab'   => $idEmpresaColab,
                        'idEmpresaColabCentral' => $dataCentral['idEmpresaColab'],
                        'direccion'        => null,
                        'idCentral'        => $dataCentral['idCentral'],
                        'tipo_proyecto'    => null,
                        'estado_edificio'  => null,
                        'manzana'          => null,
                        'lote'             => null,
                        'ruc'              => null,
                        'nomb_contructora' => null,
                        'contacto_1'       => null,
                        'telefono_1'       => null,
                        'telefono_1_2'     => null,
                        'distrito'         => null,
                        'subProyectoDesc'  => trim($subproyecto),
                        'codigoInversion'  => $codigoInversion,
                        'costo_unitario_mo' => (($idSubProyecto == 753) ? $costo_mo : null)
                    );
                    log_message('error', print_r($dataRegistro,true));
                    $arrayRegistro []= $dataRegistro;
                    $cont++;
                }
            }
            list($data, $dataTabla, $arrayIps) = $this->insertItemplan($arrayRegistro,$idUsuario);
            $data['tablaItem'] = $this->getTablaItems($dataTabla);
            if(count($arrayIps) > 0){
				//$this->createPOAutomatic($arrayIps, $idUsuario);
				$this->createPOMoAutomatic($arrayIps, $idUsuario);
				//$this->createSiropeMasivo($arrayIps); COMENTADO NO USAREMOS SIROPE 10.07.2022
			}

        }catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        echo json_encode($data);
    }

    public function insertItemplan($dataRegistro,$idUsuario) {
		$data['error'] = EXIT_ERROR;
        $data['msj'] = null; 

        try {
            $fechaActual = $this->m_utils->fechaActual();//date("Y-m-d h:i:s");
            $idFase =  ($this->m_utils->getFaseAll())[0]['idFase'];
            $dataArray = array();
            $dataArray['data_cargada'] = json_encode($dataRegistro);
            $arrayTablaItem = array();
            $arrayIps = array();
            foreach($dataRegistro as $row){
                $direccion      = $row['direccion'];
                $manzana        = $row['manzana'];
                $lote           = $row['lote'];
                $nombreProyecto = $this->lib_utils->removeEnterYTabs($row['nom_proyecto']);
                $num_pisos      = $row['nro_pisos'];
                $num_depa       = $row['nro_dep'];
                $avance         = 10;
                $ruc            = $row['ruc'];
                $nombre_constru = $row['nomb_contructora'];
                $contacto_1     = $row['contacto_1'];
                $telefono_1_1   = $row['telefono_1'];
                $telefono_1_2   = $row['telefono_1_2'];
                $coord_x        = $row['longitud'];
			    $coord_y        = $row['latitud'];
                $tipoSubProy    = $row['tipo_proyecto'];///HFC O FTTH

                $distrito       = $row['distrito'];
                $idCentral      = $row['idCentral'];
                $idSubProyecto  = $row['idSubProyecto'];
                $idEECC         = $row['idEmpresaColab'];
                $idZonal        = $row['idZonal'];
                $estado_edifi   = $row['estado_edificio'];
                $operador       =  '';

                $codigoInversion = $row['codigoInversion'];
                $costo_unitario_mo = $row['costo_unitario_mo'];

                $departamento   = null;
                $provincia      = null;
                $idTipoUrba     = null;
                $nombreUrba     = null;
                $idTipoVia      = null;
                $numero         = null;
                $blocke         = null;
                $num_depa_habi  = null;
                $fec_termino    = null;     
			    $observacion    = null;
                $email_1        = null;
                $contacto_2     = null;
                $competencia    = null;
                $prioridad      = null;

                $telefono_2_1   = NULL;
                $telefono_2_2   = NULL;
                $email_2   = NULL;


                $dataArray['nro'] = $row['nro'];
                $dataArray['itemplan']  = null;
                $dataArray['subproyecto'] = $row['subProyectoDesc'];
                $dataArray['fecha_registro'] = $fechaActual;
                $dataArray['id_usuario'] = $idUsuario;
			    $dataArray['observacion'] = '';
                $dataArray['codigoInversion'] = null;
				$dataArray['nom_plan'] = strtoupper($nombreProyecto);
                $indicador = '';
			    $uip = 0;
			    $cantidadTroba = 0;
                $flgInsert = true;

                if($idSubProyecto == null || $idSubProyecto == ''){
                    $dataArray['observacion'] .= 'VERIFICAR QUE ESTE BIEN ESCRITO EL SUBPROYECTO.'.'<br>';
                    $flgInsert = false;
                }else{
                    $arraySubProyNuevoFlujo = _getArrayIDSubProyNuevoFlujoCV();
                    if(!in_array($idSubProyecto,$arraySubProyNuevoFlujo)){
                        $dataArray['observacion'] .= 'EL SUBPROYECTO INGRESADO NO PERTENECE EL NUEVO FLUJO.'.'<br>';
                        $flgInsert = false;
                    }else{
                        if($idEECC == null || $idEECC == ''){
                            $dataArray['observacion'] .= 'VERIFICAR QUE ESTE BIEN ESCRITO LA CONTRATA, Y SE SE UBIQUE EN LA COLUMNA CORRECTA EN EL EXCEL SEGÚN EL MODELO.'.'<br>';
                            $flgInsert = false;
                        } else if($idEECC == 7) {
                            $dataArray['observacion'] .= 'NO SE PERMITE EL REGISTRO DE QUANTA.'.'<br>';
                            $flgInsert = false;
                        } else if($coord_y <= -68 && $coord_y >= -81) {// SI LA COORDENADA Y SE ENCUENTRA EN EL REANGO DE LA LONG.
                            $dataArray['observacion'] .= 'La coordenada "X" (longitud), que se esta mandando pertenece a "Y" (latitud), enviar de manera correcta.'.'<br>';
                            $flgInsert = false;
                        } else {
                            $arraySubProyIntegralException = _getArrayIDSubProyNuevoFlujoCV(2);//INTEGRAL
                            $arraySubProyBucleException = _getArrayIDSubProyNuevoFlujoCV(1);//BUCLE
                            if(in_array($idSubProyecto,$arraySubProyIntegralException)){
                                $arrayEECException = _getArrayIDContrataFlujoCVByFlgCV(2);
                                if(!in_array($idEECC,$arrayEECException)) {
                                    $dataArray['observacion'] .= 'LA CONTRATA INGRESADA, NO ES VÁLIDA A ESTE SUBPROYECTO.'.'<br>';
                                    $flgInsert = false;
                                }
                                
                            }else if(in_array($idSubProyecto,$arraySubProyBucleException)){
                                $arrayEECException = _getArrayIDContrataFlujoCVByFlgCV(1);
                                if(!in_array($idEECC,$arrayEECException)) {
                                    $dataArray['observacion'] .= 'LA CONTRATA INGRESADA, NO ES VÁLIDA PARA ESTE SUBPROYECTO.'.'<br>';
                                    $flgInsert = false;
                                }
                            }else{
                                if($idSubProyecto == ID_SUBPROYECTO_CABLEADO_EDIFICIOS_CTO_ADICIONAL){
                                    $num_depa = 1;//siempre 1
                                }
                            }
                        }
                    }
                }
                /*
                if($tipoSubProy == null || $tipoSubProy == '' || !in_array($tipoSubProy,array('FTTH','HFC','NUEVO'))){
                    $dataArray['observacion'] .= 'DEBE INDICAR EL TIPO DE PROYECTO(FTTH,HFC,NUEVO).'.'<br>';
                    $flgInsert = false;
                }*/

                if($idCentral == null){
                    $dataArray['observacion'] .= 'ERROR INTERNO, CENTRAL NO ENCONTRADO.'.'<br>';
                    $flgInsert = false;
                }
    
                if($idZonal == null) {
                    $dataArray['observacion'] .= 'VERIFICAR QUE ESTE BIEN ESCRITO LA ZONAL.'.'<br>';
                    $flgInsert = false;
                }
                
                if($num_depa == null) {
                    $dataArray['observacion'] .= 'DEBE INGRESAR EL NRO DE DEPARTAMENTOS.'.'<br>';
                    $flgInsert = false;
                }

                // if($ruc == null || $ruc == '') {
                //     $dataArray['observacion'] .= 'DEBE INGRESAR EL RUC.'.'<br>';
                //     $flgInsert = false;
                // }
                
                if($coord_x == null || $coord_x == '' || !is_numeric($coord_x)) {
                    $dataArray['observacion'] .= 'DEBE INGRESAR LA LONGITUD VALIDO.'.'<br>';
                    $flgInsert = false;
                }
                
                if($coord_y == null || $coord_y == '' || !is_numeric($coord_y)) {
                    $dataArray['observacion'] .= 'DEBE INGRESAR LA LATITUD VALIDO.'.'<br>';
                    $flgInsert = false;
                }

                if(strlen($codigoInversion) < 10 || $codigoInversion == null || $codigoInversion == ''){
					$dataArray['observacion'] .= 'INGRESE UN CODIGO DE INVERSIÓN VÁLIDO.'.'<br>';
					$flgInsert = false;
                }else{
					$existeCodigo = $this->m_registro_itemplan_masivo->getCodigoInversionxEECC($codigoInversion, $idEECC);
                    if($existeCodigo == 0){
                        $dataArray['observacion'] .= 'NO EXISTE EL CÓDIGO DE INVERSIÓN CON ESA EECC'.'<br>';
                        $flgInsert = false;
                    }else{
                        $dataArray['codigoInversion'] = $codigoInversion;
                    }
                }

                $dataSubProyecto = $this->m_utils->getDataSubProyectoById($idSubProyecto);
                if($dataSubProyecto == null || $dataSubProyecto == ''){
                    $dataArray['observacion'] .= 'NO EXISTE PROYECTO ASOCIADO AL SUBPROYECTO.'.'<br>';
                    $flgInsert = false;
                }

                $costo_mo = null;
                $arrayCosto = null;
				$precioObra = null;
                if($dataSubProyecto != null){
                    if($dataSubProyecto['flg_tipo'] == 2){
                        $arrayCosto = $this->m_registro_itemplan_masivo->getCostoxDptoByIdEECCAndSubProy($idSubProyecto, $idEECC, $num_depa);
                        if($arrayCosto == null){
                            $dataArray['observacion'] .= 'LA OBRA NO CUENTA CON UN PRECIO CONFIGURADO PARA EL SUBPROYECTO, CONTRATA Y DPTO INGRESADO.'.'<br>';
                            $flgInsert = false;
                        }else{
                            $precioObra = $arrayCosto['costo'];
                            $costo_mo = round($num_depa * $arrayCosto['costo'],2);
                        }
                    }/*else if($dataSubProyecto['flg_tipo'] == 1){
                        $costo_mo = $this->m_utils->getCostoTotalToOCMoPqt($idEstacionPartidas, $itemplan, $tipo_partidas)
                    }*/
                }
               
                if($dataSubProyecto['flg_tipo'] == 2 && ($costo_mo == null || $costo_mo == 0) && $idSubProyecto != 753){
                    $dataArray['observacion'] .= 'NO HAY COSTO DE MANO DE OBRA.'.'<br>';
                    $flgInsert = false;
                }
                
                // $costo_mo = 5000;


                if($flgInsert){
                    $costo_unitario_mat = 0;
                    
                    $paquetizado_fg = $dataSubProyecto['paquetizado_fg'];
                    if($paquetizado_fg == null || $paquetizado_fg == '') {
                        $paquetizado_fg = 1;
                    }
                    
                    $dataCTO = $this->m_registro_itemplan_masivo->getCtoByFlgTipoAndNroDepa($num_depa, $dataSubProyecto['flg_overlay']);
                    if($dataCTO == null){
                        $cant_cto = 0;
                    }else{
                        $cant_cto = $dataCTO['nro_cto_colocar'];
                    }
                    $new_itemplan = $this->m_utils->getCodigoItemplan($idZonal,$dataSubProyecto['idProyecto']); //TEMPORALMENTE COMENTADO
                //    $new_itemplan   =   $row['itemplan'];//TEMPORALMENTE
                    $dataArray['nom_plan'] = strtoupper($nombreProyecto);
                    $dataInsert = array(
                        "itemplan"         =>  $new_itemplan,
                        "nombrePlan"       =>  strtoupper($nombreProyecto),
                        "longitud"         =>  $coord_x,
                        "latitud"          =>  $coord_y,
                        "indicador"        =>  $indicador,
                        "cantidadTroba"    =>  intval($cantidadTroba),
                        "uip"              =>  intval($uip),
                        "fechaRegistro"    =>  $fechaActual,
                        "usua_crea_obra"   =>  $idUsuario, 
                        "fechaInicio"      =>  $fechaActual,
                        "idEstadoPlan"     =>  intval(ID_ESTADO_PLAN_PRE_REGISTRO),
                        "idFase"           =>  intval($idFase),
                        "idCentral"        =>  intval($idCentral),
                        "idSubProyecto"    =>  intval($idSubProyecto),
                        "idZonal"          =>  intval($idZonal),
                        "idEmpresaColab"   =>  intval($idEECC),
                        "has_cotizacion"   =>  '0',
                        "hasAdelanto"      =>  '0',
                        "paquetizado_fg"   => $paquetizado_fg,
                        "costo_unitario_mat" => $costo_unitario_mat,
                        "costo_unitario_mo"  => $costo_mo,
                        "cantFactorPlanificado" => strtoupper($num_depa),
                        "idPqtTipoFactorMedicion" => 5,
                        "idUsuarioLog"    => $idUsuario,
                        "fechaLog"  => $fechaActual,
                        "usu_upd"    => $idUsuario,
                        "fecha_upd"  => $fechaActual,
                        "cant_cto"   => $cant_cto,
                        "codigoInversion" => $codigoInversion,
						"precio_obra" => $precioObra,
                        "descripcion"   =>  'REGISTRO MASIVO CV'
                    );

                    $dataInsertDetalle = array(
                        "itemplan"              =>  $new_itemplan,	                   
                        "departamento"          =>  strtoupper($departamento),
                        "provincia"             =>  strtoupper($provincia),
                        "distrito"              =>  strtoupper($distrito),
                        "coordenada_x"          =>  $coord_x,
                        "coordenada_y"          =>  $coord_y,
                        "idSubProyecto"         =>  intval($idSubProyecto),
                        "tipo_urb_cchh"         =>  $idTipoUrba,
                        "nombre_urb_cchh"       =>  strtoupper($nombreUrba),
                        "tipo_via"              =>  $idTipoVia,
                        "direccion"             =>  strtoupper($direccion),
                        "numero"                =>  strtoupper($numero),
                        "manzana"               =>  strtoupper($manzana),
                        "lote"                  =>  strtoupper($lote),
                        "nombre_proyecto"       =>  strtoupper($nombreProyecto),
                        "blocks"                =>  strtoupper($blocke),
                        "pisos"                 =>  strtoupper($num_pisos),
                        "depa"                  =>  strtoupper($num_depa),
                        "depa_habitados"        =>  strtoupper($num_depa_habi),
                        "avance"                =>  strtoupper($avance),
                        "fec_termino_constru"   =>  $fec_termino,
                        "observaciones"         =>  strtoupper($observacion),
                        "ruc_constructora"      =>  strtoupper($ruc),
                        "nombre_constructora"   =>  strtoupper($nombre_constru),
                        "contacto_1"            =>  strtoupper($contacto_1),
                        "telefono_1_1"          =>  $telefono_1_1,
                        "telefono_1_2"          =>  $telefono_1_2,
                        "email_1"               =>  strtoupper($email_1),
                        "contacto_2"            =>  strtoupper($contacto_2),
                        "telefono_2_1"          =>  $telefono_2_1,
                        "telefeono_2_2"         =>  $telefono_2_2,
                        "email_2"               =>  strtoupper($email_2),
                        "usuario_edit"          =>  $idUsuario,
                        "fecha_edit"            =>  $fechaActual,
                        "estado_edificio"       =>  $estado_edifi,
                        "competencia"           =>  $competencia,
                        "prioridad"             =>  $prioridad,
                        "tipo_subpro"           =>  $tipoSubProy,
                        "estado_aprob"          =>  '0',
                        "operador"              =>  $operador
                    );

                    $dataInsertSeguimiento = array();
                    /*
                    $dataInsertSeguimiento = array(  
                        'itemplan'              =>  $new_itemplan,
                        'idEstadoPlan'          =>  ID_ESTADO_PLAN_PRE_REGISTRO,
                        'usuario_registro'      =>  $idUsuario,
                        'fecha_registro'        =>  $fechaActual,
                        'id_motivo_seguimiento' =>  31,
                        'comentario_incidencia' =>  'AUTOMÁTICO'
                    );
                    */
                    $data = $this->m_registro_itemplan_masivo->registrarItemplanMasivoCV($dataInsert,$dataInsertDetalle,$dataInsertSeguimiento);
                    if($data['error'] == EXIT_ERROR){
                        $dataArray['observacion'] .= $data['msj'].'<br>';
                    }else{
                        if($dataSubProyecto['flg_tipo'] == 1){
                            $monto_pqt   = $this->m_utils->getCostoTotalToOCMoPqt(5, $new_itemplan, 3);//3: bucle, 5:FO
                            $monto_ferre = 0;
                            $materialInfo = $this->m_utils->getMaterialFerreteriaPoPqtCvBucle($new_itemplan);
                            if($materialInfo !== null){                              
                                $monto_ferre = $materialInfo['baremo_final'];
                            }
                            $costo_mo   =   $monto_pqt+$monto_ferre;                            
                        }
                        $costo_unitario_mat = 0;
                        if($idSubProyecto   ==  753){//ACTIVACIONES VERTICAL
                                $costo_mo = $costo_unitario_mo;
                                $costo_unitario_mat = $costo_unitario_mo;
                        }

                        $dataArray['itemplan'] = $new_itemplan;
					    $dataArray['observacion'] .= 'REGISTRO CORRECTO.';
                        $arrayIps[]= $new_itemplan;
                        $codigo_solicitud = $this->m_utils->getNextCodSolicitud();//obtengo codigo unico de solicitud
						$dataPlanobra = array(  
							"itemplan"                   => $new_itemplan,
							"costo_unitario_mo"          => $costo_mo,
							"costo_unitario_mat"         => $costo_unitario_mat,
							"solicitud_oc"               => $codigo_solicitud,
							"estado_sol_oc"              => 'PENDIENTE',
							"costo_unitario_mo_crea_oc"  => $costo_mo,
							"costo_unitario_mat_crea_oc" => $costo_unitario_mat
						);
						$solicitud_oc_creacion = array(
							'codigo_solicitud'  =>  $codigo_solicitud,
							'idEmpresaColab'    =>  $idEECC,
							'estado'            =>  1,//pendiente
							'fecha_creacion'    =>  date("Y-m-d H:i:s"),
							'idSubProyecto'     =>  $idSubProyecto,
							'plan'              =>  'COTIZACION',
							'codigoInversion'   => 	$codigoInversion,
							'estatus_solicitud' => 'NUEVO',
							'tipo_solicitud'    =>  1,// 1= CREACION, 2 = EDICION, 3 = CERTIFICACION,
                            'usuario_creacion'  =>  $idUsuario,
                            'fecha_creacion'    =>  date("Y-m-d H:i:s")
						);
						$item_x_sol = array(
							'itemplan'            =>  $new_itemplan,
							'codigo_solicitud_oc' =>  $codigo_solicitud,
							'costo_unitario_mo'   =>  $costo_mo
						);
                        $data = $this->m_registro_itemplan_masivo->crearSolCreacionForItemplan($dataPlanobra, $solicitud_oc_creacion, $item_x_sol);
                    }

                }else{
                    $data['error'] = EXIT_SUCCESS;
                    $data['msj'] = 'Se cargó correctamente el archivo';
                }
                $arrayTablaItem []= $dataArray;
            }

            $this->m_registro_itemplan_masivo->insertLogItemplanCvMasivo($arrayTablaItem);

        }catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        }
        return array($data, $arrayTablaItem, $arrayIps);
    }

    function getTablaItems($dataTabla) {
        $html = '<table id="tb_carga" class="table table-bordered table-hover table-striped w-100">
                    <thead class="bg-primary-600">
                        <tr>
							<th>NRO</th>
							<th>ITEMPLAN</th>
                            <th>SUBPROYECTO</th>
                            <th>NOMBRE PLAN</th>
							<th>CÓDIGO INVERSIÓN</th>
                            <th>FECHA REGISTRO</th>
                            <th>STATUS</th>
                            <th>OBSERVACIÓN</th>
                        </tr>
                    </thead>                    
                    <tbody>';                                                                            
        foreach($dataTabla as $row){
            $html .=' <tr>
                        <td>'.$row['nro'].'</td>
                        <td>'.$row['itemplan'].'</td>
                        <td>'.$row['subproyecto'].'</td>
                        <td>'.$row['nom_plan'].'</td>
                        <td>'.$row['codigoInversion'].'</td>
                        <td>'.$row['fecha_registro'].'</td>
                        <td>'.(isset($row['itemplan']) ? '<span class="badge badge-success badge-pill">REGISTRADO</span>' : '<span class="badge badge-danger badge-pill">FALLIDO</span>').'</td>
                        <td>'.$row['observacion'].'</td>
                    </tr>';
        }
        $html .='
                </tbody>
                <tfoot class="thead-themed">
                    <tr>
                        <th>NRO</th>
                        <th>ITEMPLAN</th>
                        <th>SUBPROYECTO</th>
                        <th>NOMBRE PLAN</th>
                        <th>CÓDIGO INVERSIÓN</th>
                        <th>FECHA REGISTRO</th>
                        <th>STATUS</th>
                        <th>OBSERVACIÓN</th>
                    </tr>
                </tfoot>
            </table>';
                    
        return $html;
    }

    public function createPOAutomatic($itemPlanList, $idUsuario)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try { 
            $this->db->trans_begin();
            $idEstacion =  5;//todo es FO INTEGRAL
            $fechaActual = $this->m_utils->fechaActual();

            foreach($itemPlanList as $item){
                $arrayInfo = $this->m_utils->getPlanObraByItemplan($item);
                if($arrayInfo == null){
                    throw new Exception('Hubo un error al traer la información del itemplan.');
                }
                $arrayMat = $this->m_utils->getListaMaterialesToEdificios($arrayInfo['tipo_edificio'],$arrayInfo['cant_cto']);
                $codigoPO = $this->m_utils->getCodigoPO($item);
                if($codigoPO == null || $codigoPO == '') {
                    throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
                }
                $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($item, 'MAT', $idEstacion);
                if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                    throw new Exception("No tiene configurado un area.");
                }

                $costoTotal = null;
                $dataPo = array (
                    'codigo_po'      => $codigoPO,
                    'itemplan'       => $item,
                    'estado_po'      => ID_ESTADO_PO_PRE_APROBADO,
                    'idEstacion'     => $idEstacion,
                    'costo_total'    => &$costoTotal,
                    'idUsuario'      => $idUsuario,
                    'fechaRegistro'  => $fechaActual,
                    'flg_tipo_area'  => 1,
                    'idEmpresaColab' => $arrayInfo['idEmpresaColab'],
                    'idArea'         => $dataSubEstacionArea['idArea'],
                    'idSubProyecto'  => $arrayInfo['idSubProyecto']
                );

                $arrayDetalleInsert = array();
                foreach($arrayMat as $row) {
                    $exitMatKit = $this->m_utils->countMatxKit($row['codigo_material'], $arrayInfo['idSubProyecto'], $idEstacion);
                    if($row['flg_tipo'] == 0){ //NO BUCLE
                        if ($exitMatKit > 0) {
                            if ($row['codigo_material'] != null && $row['codigo_material'] != '' && $row['cantidad'] != 0 && $row['motivo'] == '' && $row['cantidad_kit'] != '-' && $row['factor_porcentual'] != '-' && $row['costo_material'] != '-') {
                                $detallePo = array (
                                    'codigo_po'        => $codigoPO,
                                    'codigo_material'  => $row['codigo_material'],
                                    'cantidadInicial'  => $row['cantidad'],
                                    'cantidadFinal'    => $row['cantidad'],
                                    'costoMat'         => $row['costo_material'],
                                    'montoFinal'       => $row['total']
                                );
								$arrayDetalleInsert[] = $detallePo;
								$costoTotal = $costoTotal + $row['total'];
                            }
                        }
                    }else if($row['flg_tipo'] == 1){ // BUCLE
                        if ($row['codigo_material'] != null && $row['codigo_material'] != '' && $row['cantidad'] != 0 && $row['motivo'] == '' && $row['cantidad_kit'] != '-' && $row['factor_porcentual'] != '-' && $row['costo_material'] != '-') {
                            $detallePo = array (
                                'codigo_po'        => $codigoPO,
                                'codigo_material'  => $row['codigo_material'],
                                'cantidadInicial'  => $row['cantidad'],
                                'cantidadFinal'    => $row['cantidad'],
                                'costoMat'         => $row['costo_material'],
                                'montoFinal'       => $row['total']
                            );
							$arrayDetalleInsert[] = $detallePo;
							$costoTotal = $costoTotal + $row['total'];
                        }
                    }
                }
                if(count($arrayDetalleInsert) == 0){
                    throw new Exception("No hay materiales válidos para el registro de la PO");
                }

                $dataLogPO =    array(
                    'codigo_po'        =>  $codigoPO,
                    'itemplan'         =>  $item,
                    'idUsuario'        =>  $idUsuario,
                    'fecha_registro'   =>  $fechaActual,
                    'idPoestado'       =>  ID_ESTADO_PO_PRE_APROBADO
                );

                $data = $this->m_utils->registrarPoMat($dataPo, $arrayDetalleInsert, $dataLogPO);
                if($data['error'] == EXIT_ERROR) {
                    throw new Exception($data['msj']);
                }
                $this->db->trans_commit();

            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
       return $data;
    }

    public function createPoMatInteManual(){
		 
		 
		$itemPlanList = array('22-2111100025');
	  
		// $this->createPOAutomatic($itemPlanList, 1);
        $this->createPOMoAutomatic($itemPlanList, 1);
	}

    public function createPOMoAutomatic($itemPlanList, $idUsuario)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try { 
            $this->db->trans_begin();
            // $idEstacion =  5;//todo es FO INTEGRAL
            $fechaActual = $this->m_utils->fechaActual();
            //log_message('error', '.......1');
            foreach($itemPlanList as $item){
                $estacionesAnclas = $this->m_utils->getEstacionesAnclasByItemplan($item);
                foreach ($estacionesAnclas as $row){
                    $hasPoPqtACtive = $this->m_utils->hasPoPqtActive($item, $row['idEstacion']);
                    //log_message('error', '.......2');
                    if($hasPoPqtACtive == 0){
                        $idEstacion = $row['idEstacion'];
                        $arrayInfo = $this->m_utils->getPlanObraByItemplan($item);
                        if($arrayInfo == null){
                            throw new Exception('Hubo un error al traer la información del itemplan.');
                        }
                        //log_message('error', '.......3');
                        if($arrayInfo['flg_tipo'] == 1){//BUCLE
                            $data = $this->createPoPqt($row['idEstacion'], $row['idEstacion'], $item, 3);//3 = EDIFICIOS
                            if($data['error'] == EXIT_ERROR) {
                                throw new Exception($data['msj']);
                            }
                            $this->db->trans_commit();
                            //log_message('error', print_r($data, true));
                        }else if($arrayInfo['flg_tipo'] == 2){//INTEGRAL
                            //log_message('error', '.......5');
                            $arrayCosto = $this->m_registro_itemplan_masivo->getCostoxDptoByIdEECCAndSubProy($arrayInfo['idSubProyecto'], $arrayInfo['idEmpresaColab'], $arrayInfo['cantFactorPlanificado']);
                                if($arrayCosto == null){
                                    throw new Exception('LA OBRA NO CUENTA CON UN PRECIO CONFIGURADO PARA EL SUBPROYECTO, CONTRATA Y DPTO.');
                                }
                                $codigoPO = $this->m_utils->getCodigoPO($item);
                                if($codigoPO == null || $codigoPO == '') {
                                    throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
                                }
                                $dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($item, 'MO', $idEstacion);
                                if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
                                    throw new Exception("No tiene configurado un area.");
                                }

                                $costoTotal = round($arrayCosto['costo']*$arrayInfo['cantFactorPlanificado'],2);
                                $dataPo = array (
                                    'codigo_po'      => $codigoPO,
                                    'itemplan'       => $item,
                                    'estado_po'      => ID_ESTADO_PO_REGISTRADO,
                                    'idEstacion'     => $idEstacion,
                                    'costo_total'    => &$costoTotal,
                                    'idUsuario'      => $idUsuario,
                                    'fechaRegistro'  => $fechaActual,
                                    'flg_tipo_area'  => 2,
                                    'idEmpresaColab' => $arrayInfo['idEmpresaColab'],
                                    'idArea'         => $dataSubEstacionArea['idArea'],
                                    'idSubProyecto'  => $arrayInfo['idSubProyecto'],
                                    'isPoPqt'        => 1
                                );

                                $arrayDetalleInsert = array();
                                $detallePo = array (
                                    'codigo_po'        => $codigoPO,
                                    'codigoPartida'    => $arrayCosto['codigoPartida'], //ESTADO REGISTRADO
                                    'baremo'           => 1,
                                    'preciario'        => $arrayCosto['costo'],
                                    'cantidadInicial'  => $arrayInfo['cantFactorPlanificado'],
                                    'montoInicial'     => $costoTotal,
                                    'cantidadFinal'    => $arrayInfo['cantFactorPlanificado'],
                                    'montoFinal'       => $costoTotal,
                                    'costoMo'          => $costoTotal
                                );
                                $arrayDetalleInsert[] = $detallePo;
                                if(count($arrayDetalleInsert) == 0){
                                    throw new Exception("No hay partidas válidas para el registro de la PO");
                                }

                                $dataLogPO =    array(
                                    'codigo_po'        =>  $codigoPO,
                                    'itemplan'         =>  $item,
                                    'idUsuario'        =>  $idUsuario,
                                    'fecha_registro'   =>  $fechaActual,
                                    'idPoestado'       =>  ID_ESTADO_PO_REGISTRADO
                                );

                                $data = $this->m_utils->registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO);
                                if($data['error'] == EXIT_ERROR) {
                                    throw new Exception($data['msj']);
                                }
                                $this->db->trans_commit();
                            }
                        }                        
                }

            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
       return $data;
    }
	
	public function createSiropeMasivo($itemPlanList){
        foreach($itemPlanList as $item){
            $this->m_integracion_sirope->execWs($item, $item.'FO',date('Y-m-d'),date('Y-m-d',strtotime('+' . 7 . ' day', strtotime(date('Y-m-d')))),'PROJECT');
        }
	}

    /**
 * 	idEstacionPlace	= ESTACION EN LA QUE SE CREARA LA PO PQT
 * idEstacionPartidas	=	PARTIDAS CONFIGURADAS A LA ESTACION 
 * tipo_partidas	=	1	SOLO PARITDA DE DISENO, 2 TODO MENOS DISENO, 3 TODAS LAS PARTIDAS DE ESTA ESTACION
	 */
	public function createPoPqt($idEstacionPlace, $idEstacionPartidas, $itemplan, $tipo_partidas)
    {
        $data['error'] = EXIT_ERROR;
        $data['msj'] = null;
        try { 
            $this->db->trans_begin();
         //   $idEstacionPartidas =  ID_ESTACION_FO;//todo es FO INTEGRAL
		    $idUsuario      = $this->session->userdata('idPersonaSessionPan');
            $fechaActual 		= $this->m_utils->fechaActual();              
			$hasPoPqtACtive = $this->m_utils->hasPoPqtActive($itemplan, $idEstacionPlace);
			//log_message('error', '.......6');
				$arrayInfo = $this->m_utils->getPlanObraByItemplan($itemplan);
				if($arrayInfo == null){
					throw new Exception('Hubo un error al traer la información del itemplan.');
				}
			 			 
				$codigoPO = $this->m_utils->getCodigoPO($itemplan);
				if($codigoPO == null || $codigoPO == '') {
					throw new Exception("Hubo un error al crear el código de po, comunicarse con el programador a cargo.");
				}
				$dataSubEstacionArea = $this->m_utils->getAreaByItemplanTipoArea($itemplan, 'MO', $idEstacionPlace);
				if($dataSubEstacionArea['idArea'] == null || $dataSubEstacionArea['idArea'] == ''){
					throw new Exception("No tiene configurado un area.");
				}

				$partidasTOPoPqt = $this->m_utils->getPartidasToPoPqtByTipo($idEstacionPartidas, $itemplan, $tipo_partidas);
				if($partidasTOPoPqt == null){
					throw new Exception('No se encontraron Partidas Configuradas para la obra.');
				}
                //log_message('error', '.......7');

				$arrayDetalleInsert = array();
				$costo_total_po = 0;
				foreach($partidasTOPoPqt as $dataPartidaPqt){//log_message('error', '.......8');

					$costo_partida = $dataPartidaPqt['baremo']*$dataPartidaPqt['costo']*$dataPartidaPqt['cantFactorPlanificado'];

					$detallePo = array (
						'codigo_po'        => $codigoPO,
						'codigoPartida'    => $dataPartidaPqt['codigoPartida'], //ESTADO REGISTRADO
						'baremo'           => $dataPartidaPqt['baremo'],
						'preciario'        => $dataPartidaPqt['costo'],
						'cantidadInicial'  => $dataPartidaPqt['cantFactorPlanificado'],
						'montoInicial'     => $costo_partida,
						'cantidadFinal'    => $dataPartidaPqt['cantFactorPlanificado'],
						'montoFinal'       => $costo_partida,
						'costoMo'          => $costo_partida
					);

					$costo_total_po	=	$costo_total_po	+	$costo_partida;
					array_push($arrayDetalleInsert, $detallePo);
				}

				if($arrayInfo['idProyecto']	==	52){//FTTHA ADICIONAMOS PARTIDA FERRETERIA
					if($idEstacionPlace	==	5){//SI ES LA FO
						$baremoFerreteria = 1;
						$costo_partida = $baremoFerreteria*COSTO_PARTIDA_FERRETERIA_FTTH*$arrayInfo['cantFactorPlanificado'];
						$detallePo = array (
							'codigo_po'        => $codigoPO,
							'codigoPartida'    => '69901-2', //PARTIDA UNIDAD MATERIAL FERRETERIA
							'baremo'           => $baremoFerreteria,
							'preciario'        => COSTO_PARTIDA_FERRETERIA_FTTH,
							'cantidadInicial'  => $arrayInfo['cantFactorPlanificado'],
							'montoInicial'     => $costo_partida,
							'cantidadFinal'    => $arrayInfo['cantFactorPlanificado'],
							'montoFinal'       => $costo_partida,
							'costoMo'          => $costo_partida
						);

						$costo_total_po	=	$costo_total_po	+	$costo_partida;
						array_push($arrayDetalleInsert, $detallePo);
					}
				}else if($arrayInfo['idProyecto']	==	21){//SI YA ENTRO ES BUCLE
                    $materialInfo = $this->m_utils->getMaterialFerreteriaPoPqtCvBucle($itemplan);//log_message('error', '.......9');
                    if($materialInfo !== null){
                        $baremoFerreteria = 1;
                        $precioPartida = $materialInfo['baremo_final'];
						$costo_partida = $materialInfo['baremo_final'];
						$detallePo = array (
							'codigo_po'        => $codigoPO,
							'codigoPartida'    => '69901-2', //PARTIDA UNIDAD MATERIAL FERRETERIA
							'baremo'           => $baremoFerreteria,
							'preciario'        => $precioPartida,
							'cantidadInicial'  => 1,
							'montoInicial'     => $costo_partida,
							'cantidadFinal'    => 1,
							'montoFinal'       => $costo_partida,
							'costoMo'          => $costo_partida
						);

						$costo_total_po	=	$costo_total_po	+	$costo_partida;
						array_push($arrayDetalleInsert, $detallePo);
                    }
                }
				 
				if(count($arrayDetalleInsert) == 0){
					throw new Exception("No hay partidas válidas para el registro de la PO");
				}

				$dataPo = array (
					'codigo_po'      => $codigoPO,
					'itemplan'       => $itemplan,
					'estado_po'      => ID_ESTADO_PO_REGISTRADO,
					'idEstacion'     => $idEstacionPlace,
					'costo_total'    => $costo_total_po,
					'idUsuario'      => $idUsuario,
					'fechaRegistro'  => $fechaActual,
					'flg_tipo_area'  => 2,
					'idEmpresaColab' => $arrayInfo['idEmpresaColab'],
					'idArea'         => $dataSubEstacionArea['idArea'],
					'idSubProyecto'  => $arrayInfo['idSubProyecto'],
					'isPoPqt'        => 1
				);

				
				$dataLogPO =    array(
					'codigo_po'        =>  $codigoPO,
					'itemplan'         =>  $itemplan,
					'idUsuario'        =>  $idUsuario,
					'fecha_registro'   =>  $fechaActual,
					'idPoestado'       =>  ID_ESTADO_PO_REGISTRADO
				);
                //log_message('error', '.......10');
                //log_message('error', print_r($arrayDetalleInsert,true));
				$data = $this->m_utils->registrarPo($dataPo, $arrayDetalleInsert, $dataLogPO);
                //log_message('error', '.......14');
				if($data['error'] == EXIT_ERROR) {
					throw new Exception($data['msj']);
				}
				$this->db->trans_commit();
		
                //log_message('error', '.......15');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $data['msj'] = $e->getMessage();
        }
       return $data;
    }

}