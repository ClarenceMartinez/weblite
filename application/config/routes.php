<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_metho
*/
$route['default_controller'] = 'C_login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['logear'] = 'c_login/logear';
$route['logOut'] = 'c_login/logOut';
$route['updPass'] = 'c_login/updatePassword';
$route['login'] = 'c_login';
$route['getPanel'] = 'C_panel';

$route['welcome'] = 'cf_bienvenido/c_bienvenida';

$route['bienvenidoAdministrador'] = 'cf_bienvenido/c_bienvenido_administracion';
$route['bienvenidoPlanta']        = 'cf_bienvenido/c_bienvenido_planta';

$route['agendaCV'] = 'cf_crecimiento_vertical/c_agenda_edificios';
$route['getInfoIPForCita'] = 'cf_crecimiento_vertical/c_agenda_edificios/getBasicInfoItemplan';
$route['agendarCitaCV'] = 'cf_crecimiento_vertical/c_agenda_edificios/createCitaCV';
$route['getDetalleCitasByIP'] = 'cf_crecimiento_vertical/c_agenda_edificios/geDetalleCitas';
$route['liquidarCitaCV'] = 'cf_crecimiento_vertical/c_agenda_edificios/liquidarCitaCV';
$route['cancelarCitaCV'] = 'cf_crecimiento_vertical/c_agenda_edificios/cancelarCitaCV';
$route['reagendarCitaCV'] = 'cf_crecimiento_vertical/c_agenda_edificios/reagendarCitaCV';
$route['regSinContactoCV'] = 'cf_crecimiento_vertical/c_agenda_edificios/regSinContacto';



$route['banDevCv'] = 'cf_crecimiento_vertical/c_bandeja_devolucion_cv';
$route['cerrarObraCV'] = 'cf_crecimiento_vertical/c_bandeja_devolucion_cv/cerrarObra';
$route['agendarCitaDev'] = 'cf_crecimiento_vertical/c_bandeja_devolucion_cv/agendarCita';

$route['getRegistrarItemplan']      = 'cf_utils/c_registrar_itemplan';
$route['getSubProyectoByProyecto']  = 'cf_utils/c_registrar_itemplan/getSubProyectoByProyecto';
$route['getItemplanMadreFactorMed'] = 'cf_utils/c_registrar_itemplan/getItemplanMadreFactorMed';
$route['getCodInvToCho']            = 'cf_utils/c_registrar_itemplan/getCodInverByEECCToChoice';
$route['getItemplanMadreRefo']      = 'cf_utils/c_registrar_itemplan/getItemplanMadreRefo';
$route['getInfoItemMadreToHijoRefo']      = 'cf_utils/c_registrar_itemplan/getInfoItemMadreToHijoRefo';


//C_UTILS
$route['getDataCentralByCoordenadas'] = 'cf_utils/c_utils/getDataCentralByCoordenadas';
$route['getDataCentralByIdCentral'] = 'cf_utils/c_utils/getDataCentralByIdCentral';
$route['registrarItemplan']           = 'cf_utils/c_utils/registrarItemplan';
$route['liquidarObra']                = 'cf_utils/c_utils/liquidarObra';
$route['getTabEstacionPoUtils']       = 'cf_utils/c_utils/getTabEstacionPoUtils';
$route['validarObraUtils']            = 'cf_utils/c_utils/validarObraUtils';
$route['rechazarLiquiPin']            = 'cf_utils/c_utils/rechazarLiquidacionPIN';
$route['getBandejaExceso']            = 'cf_utils/c_bandeja_exceso';
$route['cargarArchivoCertiPdt']       = 'cf_utils/c_utils/cargarArchivoCertiPdt';
$route['createManualEdiCert']         = 'cf_utils/c_utils/createManualOcEdiCertiByItemplan';
$route['testDelete']                  = 'cf_utils/c_utils/testDelete';

$route['getCotizacionPin']           = 'cf_planta_interna/c_cotizacion_pin';

$route['getRegCvMasivo']            = 'cf_crecimiento_vertical/C_registro_itemplan_masivo';
$route['getFormatoCargaMasivaCV']   = 'cf_crecimiento_vertical/C_registro_itemplan_masivo/getExcelCargaMasiva';
$route['regIPMasivoCV']             = 'cf_crecimiento_vertical/C_registro_itemplan_masivo/regItemplanCvMasivo';

$route['getRegCvMasivoAll']             = 'cf_consulta/C_registro_itemplan_masivo_all';
$route['getFormatoCargaMasivaCVAll']    = 'cf_consulta/C_registro_itemplan_masivo_all/getExcelCargaMasiva';
$route['regIPMasivoCVAll']              = 'cf_consulta/C_registro_itemplan_masivo_all/regItemplanCvMasivo';

//HIJO CV
$route['regCVSon']          = 'cf_crecimiento_vertical/C_registro_hijo_masivo';
$route['getFormregHijo']    = 'cf_crecimiento_vertical/C_registro_hijo_masivo/getExcelCargaMasiva';
$route['regIPMasivoHijoCV'] = 'cf_crecimiento_vertical/C_registro_hijo_masivo/regItemplanCvMasivo';

//UPDATE SITUACION
$route['upSiSon']          = 'cf_crecimiento_vertical/C_actualiza_situacion_hijo';
$route['getUpSituaSon']    = 'cf_crecimiento_vertical/C_actualiza_situacion_hijo/getExcelCargaMasiva';
$route['upSituaHijoCv']    = 'cf_crecimiento_vertical/C_actualiza_situacion_hijo/regItemplanCvMasivo';

//UPDATE FEC COMERCIAL
$route['upFeCom']           = 'cf_crecimiento_vertical/C_actualiza_fec_comercial';
$route['getUpFeCom']        = 'cf_crecimiento_vertical/C_actualiza_fec_comercial/getExcelCargaMasiva';
$route['upFeComhijoCv']     = 'cf_crecimiento_vertical/C_actualiza_fec_comercial/regItemplanCvMasivo';

$route['getCotizacionPin']       = 'cf_planta_interna/c_cotizacion_pin';
$route['getDataKitPartida']      = 'cf_planta_interna/c_cotizacion_pin/getDataKitPartida';

$route['getDataKitPartidadetalle']      = 'cf_planta_interna/c_cotizacion_pin/getDataKitPartidadetalle';
$route['registrarKitPartidaPin'] = 'cf_planta_interna/c_cotizacion_pin/registrarKitPartidaPin';

$route['getBandejaAprobacion'] = 'cf_planta_interna/c_bandeja_aprobacion';
$route['aprobarObraPin']       = 'cf_planta_interna/c_bandeja_aprobacion/aprobarObraPin';

$route['getEnObraPin'] = 'cf_planta_interna/C_bandeja_en_obra_pin';


$route['getMatrizSeg'] 							= 'cf_matriz_seguimiento/C_matriz_seguimiento';
$route['getMatrizSeg2'] 							= 'cf_matriz_seguimiento/C_matriz_seguimiento/index2';
$route['getExcelFmtAtenMatrizSeguimiento'] 		= 'cf_matriz_seguimiento/C_matriz_seguimiento/getFormatoExcelCarga';
$route['getExcelFmtAtenMatrizSeguimientoMin'] 	= 'cf_matriz_seguimiento/C_matriz_seguimiento/getFormatoExcelCargaMin';
$route['procesarFileMatSeg']  					= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimiento';
$route['procesarFileMatSegMin']  					= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoMin';

$route['procesarFileMatSegDiseno']  					= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoDiseno';
$route['procesarFileMatSegEconomico']  					= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoEconomico';
$route['procesarFileMatSegLicencia']  					= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoLicencia';
$route['procesarFileMatSegLogistica']  					= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoLogistica';
$route['procesarFileMatSegPin']  						= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoPin';
$route['procesarFileMatSegCensado']  					= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoCensado';
$route['procesarFileMatSegDespliegue']  				= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoDespliegue';
$route['procesarFileMatSegHGU']  						= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoHGU';
$route['procesarFileMatSegStatus']  					= 'cf_matriz_seguimiento/C_matriz_seguimiento/procesarFileMatrizSeguimientoStatus';


$route['cargaAtenMasivaMatrizSeguimiento']            = 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimiento';
$route['cargaAtenMasivaMatrizSeguimientoMin']         = 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoMin';

$route['getLoadMatSeg'] 						= 'cf_matriz_seguimiento/C_matriz_seguimiento/showUploadMatSeg';
$route['getLoadMatSegm'] 						= 'cf_matriz_seguimiento/C_matriz_seguimiento/showUploadMatSegm';
$route['getMatrizSegByItemPlan'] 				= 'cf_matriz_seguimiento/C_matriz_seguimiento/getByItemPlan';
$route['getInfoMatrizSegByItemPlan']			= 'cf_matriz_seguimiento/C_matriz_seguimiento/getInfoMatrizSegByItemPlan';
$route['getInfoMatSegByItempanAndDivicau'] 		= 'cf_matriz_seguimiento/C_matriz_seguimiento/getInfoMatrizSeguimientoByItempanAndDivicau';
$route['getInfoMatSegByItemplan'] 				= 'cf_matriz_seguimiento/C_matriz_seguimiento/getInfoMatrizSegByItemPlan2';
$route['postUpdateMatrizSeguimientoDiseno']		= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoDiseno';
$route['postUpdateMatrizSeguimientoEconomico']	= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoEconomico';
$route['postUpdateMatrizSeguimientoLicencia']	= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoLicencia';
$route['postUpdateMatrizSeguimientoLogistica']	= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoLogistica';
$route['postUpdateMatrizSeguimientoPIN']		= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoPIN';
$route['postUpdateMatrizSeguimientoCensado']	= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoCensado';
$route['postUpdateMatrizSeguimientoDespliegue']	= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoDespliegue';
$route['postUpdateMatrizSeguimientoHGU']		= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoHGU';
$route['postUpdateMatrizSeguimientoStatus']		= 'cf_matriz_seguimiento/C_matriz_seguimiento/postUpdateMatrizSeguimientoStatus';
$route['getCableByNodo']						= 'cf_matriz_seguimiento/C_matriz_seguimiento/getCableByNodo';
$route['getInfoCableNodo']						= 'cf_matriz_seguimiento/C_matriz_seguimiento/getInfoCableNodo';
$route['saveMatSegInfoPIN']						= 'cf_matriz_seguimiento/C_matriz_seguimiento/saveMatSegInfoPIN';


$route['cargaAtenMasivMatSegDis']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoDiseno';
$route['cargaAtenMasivMatSegEco']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoEconomico';
$route['cargaAtenMasivMatSegLic']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoLicenciada';
$route['cargaAtenMasivMatSegLog']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoLogistica';
$route['cargaAtenMasivMatSegPIN']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoPIN';
$route['cargaAtenMasivMatSegCen']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoCensado';
$route['cargaAtenMasivMatSegDesp']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoDespliegue';
$route['cargaAtenMasivMatSegHGU']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoHGU';
$route['cargaAtenMasivMatSegST']         	= 'cf_matriz_seguimiento/C_matriz_seguimiento/cargarMasivoMatrizSeguimientoStatus';



$route['getSolJumEECCPEXT'] 				= 'cf_matriz_jumpeo/C_matriz_jumpeo/getSolJumEECCPEXT';
$route['getSolJumEECCPINT'] 				= 'cf_matriz_jumpeo/C_matriz_jumpeo/getSolJumEECCPINT';
$route['banProgJum'] 						= 'cf_matriz_jumpeo/C_matriz_jumpeo/banProgJum';
$route['bangestPINT'] 						= 'cf_matriz_jumpeo/C_matriz_jumpeo/bangestPINT';
$route['getInfoMatrizJumpeoById'] 			= 'cf_matriz_jumpeo/C_matriz_jumpeo/getInfoMatrizJumpeoById';
$route['saveMatrizJumpeoByIdRechazado'] 	= 'cf_matriz_jumpeo/C_matriz_jumpeo/jumpeoRechazado';
$route['saveMatrizJumpeoByIdObservado'] 	= 'cf_matriz_jumpeo/C_matriz_jumpeo/jumpeoObservado';
$route['saveMatrizJumpeoById'] 				= 'cf_matriz_jumpeo/C_matriz_jumpeo/saveMatrizJumpeo';
$route['saveEvidenciaMatrizJumpeo'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/saveEvidenciaMatrizJumpeo';
$route['saveMatrizJumpeo_Jum'] 				= 'cf_matriz_jumpeo/C_matriz_jumpeo/saveMatrizJumpeoJum';
$route['getEECCByItemPlan'] 				= 'cf_matriz_jumpeo/C_matriz_jumpeo/getEECCByItemPlan';
$route['getLoadMatJum'] 					= 'cf_matriz_jumpeo/C_matriz_jumpeo/getLoadMatJum';
$route['getLoadPinPex'] 					= 'cf_matriz_jumpeo/C_matriz_jumpeo/getLoadPinPex';
$route['depDupliMatPinPex'] 				= 'cf_matriz_jumpeo/C_matriz_jumpeo/depDupliMatPinPex';
$route['getExcelFmtAtenMatrizJumpeo'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/getFormatoExcelCarga';
$route['procesarFileMatJum'] 				= 'cf_matriz_jumpeo/C_matriz_jumpeo/procesarFileMatrizJumpeo';
$route['cargaAtenMasivaMatrizJumpeo'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/cargarMasivoMatrizJumpeo';
$route['procesarFileMatPinxPex'] 			= 'cf_matriz_jumpeo/C_matriz_jumpeo/procesarFileMatrizPinPex';
$route['searchMatrizJumpeoByFilters'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/searchMatrizJumpeoByFilters';
$route['searchMatrizJumpeoByFiltersbanPlIntJum'] = 'cf_matriz_jumpeo/C_matriz_jumpeo/searchMatrizJumpeoByFiltersbanPlIntJum';
$route['searchMatrizJumpeoByFiltersgetSolJumEECCPEXT'] = 'cf_matriz_jumpeo/C_matriz_jumpeo/searchMatrizJumpeoByFiltersgetSolJumEECCPEXT';
$route['searchMatrizJumpeoByFiltersgetSolJumEECCPIN'] = 'cf_matriz_jumpeo/C_matriz_jumpeo/searchMatrizJumpeoByFiltersgetSolJumEECCPIN';
$route['getLogByMatrizJumpeo'] 				= 'cf_matriz_jumpeo/C_matriz_jumpeo/getLogByMatrizJumpeo';
$route['cancelarSolicitudJumpeo'] 			= 'cf_matriz_jumpeo/C_matriz_jumpeo/cancelarSolicitudJumpeo';

$route['getExcelFmtAtenMatrizJumpeoDiseno'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/getExcelFmtAtenMatrizJumpeoDiseno';
$route['getExcelFmtAtenMatrizJumpeoEconomico'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/getExcelFmtAtenMatrizJumpeoEconomico';
$route['getExcelFmtAtenMatrizJumpeoLicencia'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/getExcelFmtAtenMatrizJumpeoLicencia';
$route['getExcelFmtAtenMatrizJumpeoLogistica'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/getExcelFmtAtenMatrizJumpeoLogistica';
$route['getExcelFmtAtenMatrizJumpeoCensado'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/getExcelFmtAtenMatrizJumpeoCensado';
$route['getExcelFmtAtenMatrizJumpeoDespliegue'] 	= 'cf_matriz_jumpeo/C_matriz_jumpeo/getExcelFmtAtenMatrizJumpeoDespliegue';
$route['getExcelFmtAtenMatrizJumpeoHGU'] 			= 'cf_matriz_jumpeo/C_matriz_jumpeo/getExcelFmtAtenMatrizJumpeoHGU';
$route['getExcelFmtAtenMatrizJumpeoStatus'] 		= 'cf_matriz_jumpeo/C_matriz_jumpeo/getExcelFmtAtenMatrizJumpeoStatus';

$route['getConsulta']   = 'cf_consulta/c_consulta';
$route['getLogPlanobra']   = 'cf_consulta/c_consulta/getLogPlanobra';
$route['getModalDetallePO'] = 'cf_consulta/c_consulta/getDetallePO';
$route['regEditPO'] = 'cf_consulta/c_consulta/registrarEdicionPO';
$route['getLogSeguimientoCV'] = 'cf_consulta/c_consulta/getLogSeguimientoCV';
$route['regLogSeguiCV'] = 'cf_consulta/c_consulta/registrarLogSeguimientoCV';

$route['getLogSeguimientoCVDet'] = 'cf_consulta/c_detalle_consulta/getLogSeguimientoCV';
$route['regLogSeguiCVDet'] = 'cf_consulta/c_detalle_consulta/registrarLogSeguimientoCV';

$route['getConsultaItemplanByFiltros'] = 'cf_consulta/c_consulta/filtrarTabla';
$route['getDataPartidaAdicIntegral'] = 'cf_consulta/c_consulta/getDataPartidaAdicIntegral';
$route['regEditPartidaAdicIntegral'] = 'cf_consulta/c_consulta/regEditPartidaAdicIntegral';

$route['getDetalleConsulta']        = 'cf_consulta/c_detalle_consulta';

$route['createPodiseb2bManual']        = 'cf_consulta/c_detalle_consulta/manualCreatePoDiseno';
$route['findObra']                  = 'cf_consulta/c_detalle_consulta/getInfoITemplanByCod';

$route['getBandejaSolicitudOC']     = 'cf_orden_compra/C_bandeja_solicitud_oc';
$route['getBandejaSolicitudOCRP']     = 'cf_orden_compra/C_bandeja_solicitud_oc/ftth';
$route['getDataDetalleSolicitud']   = 'cf_orden_compra/C_bandeja_solicitud_oc/getDataDetalleSolicitud';
$route['getDataAtenderSolicitudOc'] = 'cf_orden_compra/C_bandeja_solicitud_oc/getDataAtenderSolicitudOc';
$route['atenderSolicitudCreaOc']    = 'cf_orden_compra/C_bandeja_solicitud_oc/atenderSolicitudCreaOc';
$route['atenderSolicitudCertiOc']   = 'cf_orden_compra/C_bandeja_solicitud_oc/atenderSolicitudCertiOc';
$route['filtrarSolicitudOC']        = 'cf_orden_compra/C_bandeja_solicitud_oc/filtrarSolicitudOC';
$route['updateSolCertiToPdt']       = 'cf_orden_compra/C_bandeja_solicitud_oc/actualizarSolicitudCertToPdt';
$route['valSolEdiOC']               = 'cf_orden_compra/C_bandeja_solicitud_oc/validarSolicitudEdicionOC'; 
$route['valSolAnulOC']               = 'cf_orden_compra/C_bandeja_solicitud_oc/validarAnulacionDeOC';

$route['getLiquidacionPin'] = 'cf_planta_interna/c_liquidacion_obra_pin';
$route['openMdlLiquidarObraPin'] = 'cf_planta_interna/c_liquidacion_obra_pin/openMdlLiquidarObraPin';

$route['getValidacionPin']     = 'cf_planta_interna/c_validacion_pin';
$route['getTablaDetalleMo']    = 'cf_planta_interna/c_cotizacion_pin/getTablaDetalleMo';
$route['getTablaPoValidacion'] = 'cf_planta_interna/c_validacion_pin/getTablaPoValidacion';

$route['atenSolExceso'] = 'cf_utils/c_bandeja_exceso/atenderSolicitudExceso';
$route['getDetalleSolExceso'] = 'cf_utils/c_bandeja_exceso/getDetalleSolExceso';
$route['actualizarKitPartidaPin'] = 'cf_planta_interna/c_cotizacion_pin/actualizarKitPartidaPin';

$route['crearPOMatManual'] = 'cf_crecimiento_vertical/C_registro_itemplan_masivo/createPoMatInteManual';

$route['getBandejaAprobPoMat'] = 'cf_consulta/C_bandeja_aprobacion_po_mat';
$route['aprobarPOMat'] = 'cf_consulta/C_bandeja_aprobacion_po_mat/aprobarPOMat';
$route['getBandejaAprobPoMatByFiltros'] = 'cf_consulta/C_bandeja_aprobacion_po_mat/filtrarTabla';
$route['getMaterialesByPoBanAprob'] = 'cf_consulta/C_bandeja_aprobacion_po_mat/getReporteMateriales';

$route['getBaPreAproPoMat'] = 'cf_consulta/C_bandeja_pre_aprobacion_po_mat';
$route['preAprobarPOMat']   = 'cf_consulta/C_bandeja_pre_aprobacion_po_mat/preAprobarPOMat';

$route['extractor'] = 'cf_consulta/C_extractor';
$route['getReportePlanobraCv'] = 'cf_consulta/C_extractor/reportePlanobraCV';
$route['getReportePoMoCv'] = 'cf_consulta/C_extractor/reporteDetallePoMoCV';
$route['getReportePoMoAll'] = 'cf_consulta/C_extractor/reporteDetallePoMoAll';
$route['getReportePoMatCv'] = 'cf_consulta/C_extractor/reporteDetallePoMatCV';
$route['getReporteDetallePlanCv'] = 'cf_consulta/C_extractor/reporteDetallePlanCV';
$route['getReportHijosCv']  = 'cf_consulta/C_extractor/reporteDetHijosCV';
$route['getRepCotizaB2b']  = 'cf_consulta/C_extractor/reporteCotizacionesB2b';
$route['getDetLicenciasRep']  = 'cf_consulta/C_extractor/getReporteDetalleLicencias';
$route['getDetFormRefCto']  = 'cf_consulta/C_extractor/getReporteDetalleFormReforzamientoCto';
$route['getDetMatrizSeg']  = 'cf_consulta/C_extractor/getReporteDetalleMatrizSeguimiento';
$route['getDetMatrizJum']  = 'cf_consulta/C_extractor/getReporteDetalleMatrizJumpeo';
$route['getDetMatrizPinPex']  = 'cf_consulta/C_extractor/getReporteDetalleMatrizPinPex';

//ejecución diseño
$route['getEntidadesForEjecDiseno'] = 'cf_consulta/c_detalle_consulta/getEntidadesForEjecucion';
$route['ejecutarDiseno']  = 'cf_consulta/c_detalle_consulta/ejecutarDiseno';

//licencia
$route['getTablaEntidad']  = 'cf_consulta/c_detalle_consulta/getTablaEntidad';
$route['registrarEntidad'] = 'cf_consulta/c_detalle_consulta/registrarEntidad';
$route['registrarExpLicencia']   = 'cf_consulta/c_detalle_consulta/registrarExpLicencia';
$route['getTablaComprobanteLic'] = 'cf_consulta/c_detalle_consulta/getTablaComprobanteLic';
$route['registrarCompLicencia'] = 'cf_consulta/c_detalle_consulta/registrarCompLicencia';
$route['eliminarEntidadLicencia'] = 'cf_consulta/c_detalle_consulta/eliminarEntidadLicencia';
$route['registrarExpTerminoLicencia']   = 'cf_consulta/c_detalle_consulta/registrarExpTerminoLicencia';

//bandeja registro gráfico
$route['banRegGrafCV'] = 'cf_crecimiento_vertical/C_registro_grafico_cv';
$route['getBandejaRegGrafCVByFiltros'] = 'cf_crecimiento_vertical/C_registro_grafico_cv/filtrarTabla';
$route['saveIPxRegGraficoCV'] = 'cf_crecimiento_vertical/C_registro_grafico_cv/insertIPxRegGraficoCV';
//registro manual po mat
$route['regIndiPOMat'] = 'cf_consulta/C_registro_manual_po_mat';
$route['exportFormatoCargaPoMat'] = 'cf_consulta/C_registro_manual_po_mat/getFormatoExcelCarga';
$route['procesarExcelPoMat'] = 'cf_consulta/C_registro_manual_po_mat/procesarArchivoPoMat';
$route['registrarPoMat'] = 'cf_consulta/C_registro_manual_po_mat/registrarPoMat';
//preliquidacion
$route['getEstacionesForLiquidacion'] = 'cf_consulta/c_detalle_consulta/getEstacionesForLiquidacion';
$route['ingresarPorcentajeLiqui'] = 'cf_consulta/c_detalle_consulta/ingresarPorcentajeLiqui';
$route['ingresarEvidenciaLiqui'] = 'cf_consulta/c_detalle_consulta/ingresarEvidenciaLiqui';
//terminado
$route['sendValidatePartAdic'] = 'cf_consulta/c_detalle_consulta/sendValidatePartidasAdicionales';

$route['cancelarPO'] = 'cf_consulta/c_consulta/cancelarPO';
$route['getBandejaValObra'] = 'cf_consulta/C_bandeja_valida_obra';
$route['getContPartPndtVal'] = 'cf_consulta/C_bandeja_valida_obra/getPartidasPdtValidacion';
$route['validarNivel1'] = 'cf_consulta/C_bandeja_valida_obra/validarPartidasNivel1';
$route['rejectSolAdPqt'] = 'cf_consulta/C_bandeja_valida_obra/rechazarSolicitud';
$route['validarNivel2'] = 'cf_consulta/C_bandeja_valida_obra/validarPartidasNivel2';


$route['getReporteSolicitudOc'] = 'cf_consulta/C_extractor/getReporteSolicitudOc';

$route['testSiropeWS']    = 'cf_servicios/C_integracion_sirope';

$route['getSolicitudVR'] = 'cf_consulta/C_solicitud_Vr';
$route['getInfoComboPoByIP'] = 'cf_consulta/C_solicitud_Vr/getComboPO';
$route['getDetallePoMatForSolVR'] = 'cf_consulta/C_solicitud_Vr/getDetallePoMatForSolicitudVR';
$route['getKitMaterialForSolVr'] = 'cf_consulta/C_solicitud_Vr/getKitMaterialForSolVr';
$route['regSolicitudVr'] = 'cf_consulta/C_solicitud_Vr/registrarSolicitudVr';

$route['getBandejaSolicitudVR'] = 'cf_consulta/C_bandeja_solicitud_Vr';
$route['getDetalleSolVrByCod'] = 'cf_consulta/C_bandeja_solicitud_Vr/getDetalleSolicitudVr';
$route['actualizarDetalleSolVr'] = 'cf_consulta/C_bandeja_solicitud_Vr/actualizarDetalleSolVr';
$route['atenderDetalleSolVr'] = 'cf_consulta/C_bandeja_solicitud_Vr/atenderDetalleSolVr';
$route['getBandejaSolVrByFiltros'] = 'cf_consulta/C_bandeja_solicitud_Vr/filtrarTabla';

$route['getConSolVR'] = 'cf_consulta/C_con_bandeja_solicitud_vr';
$route['filBanConSolVR'] = 'cf_consulta/C_con_bandeja_solicitud_vr/filtrarTabla';
$route['getDetalleConSolVrByCod'] = 'cf_consulta/C_con_bandeja_solicitud_vr/getDetalleSolicitudVr';

$route['cancelarItemplan']  = 'cf_consulta/c_consulta/cancelarItemplan';
$route['saveQuiebreCVR']    = 'cf_consulta/c_consulta/saveQuiebreCVR';

$route['atenMasivoSolOcCrea'] = 'cf_orden_compra/C_aten_masivo_sol_crea_oc';
$route['getExcelFmtAtenMasivaSolOcCreaPan'] = 'cf_orden_compra/C_aten_masivo_sol_crea_oc/getFormatoExcelCarga';

$route['procesarFileMasivoAtenSolCreaOcPan'] = 'cf_orden_compra/C_aten_masivo_sol_crea_oc/procesarFileMasivoAtenSolCreaOc';
$route['cargaAtenMasivaSolCreaOcPan'] = 'cf_orden_compra/C_aten_masivo_sol_crea_oc/updateMasivoSolCreacionOC';

$route['reenviarTramaSiropeMN'] = 'cf_utils/c_utils/reenviarTramaSiropeMN';

$route['excuteSiropeFilterMasivo'] = 'cf_consulta/c_consulta/excuteSiropeFilterMasivo';

$route['getRegitrarItemplanMadre']           = 'cf_itemplan_madre/C_registrar_itemplan_madre';
$route['registrarItemplanMadre']             = 'cf_itemplan_madre/C_registrar_itemplan_madre/registrarItemplanMadre';
$route['getSolicitudOcItemMadre']            = 'cf_itemplan_madre/C_solicitud_oc_item_madre';
$route['filtrarSolicitudOCItemMadre']        = 'cf_itemplan_madre/C_solicitud_oc_item_madre/filtrarSolicitudOCItemMadre';
$route['getDetalleSolicitudOcItemMadre']     = 'cf_itemplan_madre/C_solicitud_oc_item_madre/getDetalleSolicitudOcItemMadre';
$route['getDataAtenderSolicitudOcItemMadre'] = 'cf_itemplan_madre/C_solicitud_oc_item_madre/getDataAtenderSolicitudOcItemMadre';
$route['atenderSolicitudCreaOcItemMadre']    = 'cf_itemplan_madre/C_solicitud_oc_item_madre/atenderSolicitudCreaOcItemMadre';
$route['getConsultaItemMadre']               = 'cf_itemplan_madre/C_consulta_item_madre';
$route['getDataHijosItemValida']             = 'cf_itemplan_madre/C_consulta_item_madre/getDataHijosItemValida';

$route['getValIPMadre']               = 'cf_itemplan_madre/C_validacion_item_madre';
$route['getHijosValIpMadre']          = 'cf_itemplan_madre/C_validacion_item_madre/getDataHijosItemValida';
$route['validarItemplanPan']          = 'cf_itemplan_madre/C_validacion_item_madre/validarItemplanPan';


$route['getCapRegistrar']         = 'cf_cap_requerimiento/C_registrar_cap';
$route['getMotivoResponsableCap'] = 'cf_cap_requerimiento/C_registrar_cap/getMotivoResponsableCap';
$route['getResponsableCap']       = 'cf_cap_requerimiento/C_registrar_cap/getResponsableCap';
$route['registrarRequerimiento']  = 'cf_cap_requerimiento/C_registrar_cap/registrarRequerimiento';
$route['getCapConsultar'] = 'cf_cap_requerimiento/C_consultar_cap';
$route['getFormRespuesta'] = 'cf_cap_requerimiento/C_consultar_cap/getFormRespuesta';
$route['actualizarRequerimiento'] = 'cf_cap_requerimiento/C_consultar_cap/actualizarRequerimiento';

$route['downloadLiquiEsta'] = 'cf_consulta/c_detalle_consulta/downloadLiquiEsta';
$route['liquidacion_download_by_esta'] = 'cf_consulta/c_detalle_consulta/liquidacion_download_estacion';

$route['masExpeLic']                = 'cf_consulta/C_consulta_masiva_expe_lic'; 
$route['findMasExpeLic']            = 'cf_consulta/C_consulta_masiva_expe_lic/filtrarTabla'; 
$route['expedientes_lic_download']  = 'cf_consulta/C_consulta_masiva_expe_lic/downloadExpedientesLicenciaByItemplanList';


$route['savePoMoTest'] = 'cf_utils/c_utils/createManualPoPqt';

/**REGISTRO PO MO */
$route['regPOMo']           = 'cf_consulta/c_registro_po_mo';
$route['exportFormRegPoMo'] = 'cf_consulta/c_registro_po_mo/getFormatoExcelCarga';
$route['procesarExcelPoMo'] = 'cf_consulta/c_registro_po_mo/procesarArchivoPoMo';
$route['registrarPoMo']     = 'cf_consulta/c_registro_po_mo/registrarPoMo';

//SOLICITUD COTIZACION B2B
$route['reMaSoCo']           = 'cf_b2b/C_registro_solicitud_cotizacion';
$route['formReSolCoti']      = 'cf_b2b/C_registro_solicitud_cotizacion/getExcelCargaMasiva';
$route['regSolCotB2b']       = 'cf_b2b/C_registro_solicitud_cotizacion/regItemplanCvMasivo';

//COTIZACION B2B 
$route['cotib2b']                           = 'cf_b2b/C_cotizacion_b2b';
$route['getDataDetalleCotizacionSisego']    = 'cf_b2b/C_cotizacion_b2b/getDataDetalleCotizacionSisego';
$route['zipArchivosForm']                   = 'cf_b2b/C_cotizacion_b2b/zipArchivosForm';
$route['aprobCanCoti']                      = 'cf_b2b/C_cotizacion_b2b/aprobarCancelarCotizacion';
$route['filtrarCoti']                       = 'cf_b2b/C_cotizacion_b2b/filtrarCotizacion';

//APROBACION
$route['aprob2b']                           = 'cf_b2b/C_aprobacion_b2b';
$route['filtAproCo']                        = 'cf_b2b/C_aprobacion_b2b/filtrarCotizacion';

//SOLICITUDES APROBADAS 
$route['solAprob2b']                        = 'cf_b2b/C_solicitudes_aprobadas_b2b';
$route['filtraSolApro']                     = 'cf_b2b/C_solicitudes_aprobadas_b2b/filtrarCotizacion';

//REGISTRO DE IP B2B
$route['geObraB2b']                        = 'cf_b2b/C_generar_ip_b2b';
$route['filGeObraB2b']                     = 'cf_b2b/C_generar_ip_b2b/filtrarCotizacion';
$route['getInversionComb']                 = 'cf_b2b/C_generar_ip_b2b/getCodInverByEECCToChoice';
$route['genIpFromB2b']                 = 'cf_b2b/C_generar_ip_b2b/createPlanObraFromSisego';

//FORMULARIO COTIZACION B2B
$route['formCotiB2b']           = 'cf_b2b/C_formulario_cotizacion';
$route['getCentralByTipoRed']   = 'cf_b2b/C_formulario_cotizacion/getCentralByTipoRed';
$route['getDataSeiaMtc']        = 'cf_b2b/C_formulario_cotizacion/getDataSeiaMtc';

$route['getDiasMatriz']                     = 'cf_b2b/C_formulario_cotizacion/getDiasMatriz';
$route['getEbcByDistritoByDistrito']        = 'cf_b2b/C_formulario_cotizacion/getEbcByDistritoByDistrito';
$route['sendCotizacionIndividual']          = 'cf_b2b/C_formulario_cotizacion/sendCotizacionIndividual';
$route['getFacByCent']                      = 'cf_b2b/C_formulario_cotizacion/getFacilidadesRedByIdCentral';

//DISENO B2B
$route['valDiseB2b']                      = 'cf_b2b/C_validacion_diseno_b2b';
$route['filValDiB2b']                     = 'cf_b2b/C_validacion_diseno_b2b/filtrarCotizacion';
$route['valRecB2b']                       = 'cf_b2b/C_validacion_diseno_b2b/validarItemplanPanDi';

//OPERACIONES B2B
$route['valOpeB2b']                      = 'cf_b2b/C_validacion_operaciones_b2b';
$route['filValOpeB2b']                   = 'cf_b2b/C_validacion_operaciones_b2b/filtrarCotizacion';

//LIQUIDACIONPO MO
$route['liquiMo']           = 'cf_consulta/C_liquidacion_po_mo';
$route['detPoLiquiMo']      = 'cf_consulta/C_liquidacion_po_mo/getFormatoExcelCarga';
$route['procesLiquiPoMo']   = 'cf_consulta/C_liquidacion_po_mo/procesarArchivoPoMo';
$route['saveLiquiPoMo']     = 'cf_consulta/C_liquidacion_po_mo/registrarPoMo';

$route['liquiPoMo']         = 'cf_consulta/C_liquidacion_po_mo/liquidarPOMO';
$route['sendValNoPqt']      = 'cf_consulta/c_detalle_consulta/sendValidatePartidasAdicionalesNoPqt';

$route['eviLiquidacion']        = 'cf_consultaZip/C_consultaZip/liquidacion';
$route['liquidacion_download']  = 'cf_consultaZip/C_consultaZip/liquidacion_download';

$route['getLogSeguimientoB2bDet']       = 'cf_consulta/c_detalle_consulta/getLogSeguimientoB2b';
$route['getLogSeguimientoReforzaDet']   = 'cf_consulta/c_detalle_consulta/getLogSeguimientoReforzamiento';
$route['saveInstaladoRefor']            = 'cf_consulta/c_detalle_consulta/saveInstaladoReforzamiento';

$route['upSitB2b']         = 'cf_b2b/C_actualiza_situacion_b2b';
$route['getUpSitub2b']     = 'cf_b2b/C_actualiza_situacion_b2b/getExcelCargaMasiva';
$route['upSituaHijob2b']   = 'cf_b2b/C_actualiza_situacion_b2b/regItemplanCvMasivo';
$route['getReportHijosB2b']  = 'cf_consulta/C_extractor/reporteDetHijosB2b';
$route['getDetallePoMat']   = 'cf_consulta/C_extractor/reporteDetallePoMat';

$route['valProNiv2NoPqt']  = 'cf_consulta/C_bandeja_valida_obra/validarPropuestaNivel2NoPqt';


$route['getCmbMotPreCancela'] = 'cf_consulta/c_detalle_consulta/getComboMotivoPreCancela'; 
$route['preCancelPO']         = 'cf_consulta/c_detalle_consulta/preCancelarPO';

$route['getBanCancePoMat'] = 'cf_consulta/C_bandeja_cancelacion_po_mat';
$route['conCancelPomat'] = 'cf_consulta/C_bandeja_cancelacion_po_mat/cancelarPoMatConfirm';

$route['upSiroFile']     = 'cf_transferencias/C_carga_estados_ot_sirope';
$route['proFiSiroEs']    = 'cf_transferencias/C_carga_estados_ot_sirope/procesarFileEstadoSirope';
$route['updSiropeEsta']  = 'cf_transferencias/C_carga_estados_ot_sirope/actualizarEstadosSirope';

$route['upRefSegCto']           = 'cf_transferencias/C_carga_seguimiento_refor_cto';
$route['proCarRefor']           = 'cf_transferencias/C_carga_seguimiento_refor_cto/procesarFileEstadoSirope';
$route['regSeguiFormuCto']      = 'cf_transferencias/C_carga_seguimiento_refor_cto/procesarFileToSegRefor';
$route['getExcelCargaRefoCto']  = 'cf_transferencias/C_carga_seguimiento_refor_cto/getFormatoExcelCarga';

$route['cotOcb2b']                  = 'cf_orden_compra/C_asociacion_oc_cotizacion_b2b';
$route['getFormAteCotiB2bOC']       = 'cf_orden_compra/C_asociacion_oc_cotizacion_b2b/getFormatoExcelCarga';
$route['procesarFileOCCotiB2bPan']  = 'cf_orden_compra/C_asociacion_oc_cotizacion_b2b/procesarFileMasivoAtenSolCotiOc';
$route['cargaAtenCotiOcB2bPan']     = 'cf_orden_compra/C_asociacion_oc_cotizacion_b2b/updateMasivoSolCotiB2bOC';

$route['getInfoRech2Bucles']        = 'cf_consulta/c_detalle_consulta/getRechazadoByidSolicitud2Bucles';
$route['getInfoRech2BuclesRe']      = 'cf_consulta/c_detalle_consulta/getRechazadoByidSolicitud2BuclesOnlyrechazo';

$route['ateMaEdiOc']                            = 'cf_orden_compra/C_aten_masivo_sol_edi_oc';
$route['getFormatEdiMasivoOc']                  = 'cf_orden_compra/C_aten_masivo_sol_edi_oc/getFormatoExcelCarga';
$route['procesarFileMasivoAtenSolEdiOcPan']     = 'cf_orden_compra/C_aten_masivo_sol_edi_oc/procesarFileMasivoAtenSolCreaOc';
$route['cargaAtenMasivaSolEdiOcPan']            = 'cf_orden_compra/C_aten_masivo_sol_edi_oc/updateMasivoSolCreacionOC';

$route['ateAcToPdtOc']                              = 'cf_orden_compra/C_aten_masivo_sol_acta_pdt_oc';
$route['getFormatAcToPdtMasivoOc']                  = 'cf_orden_compra/C_aten_masivo_sol_acta_pdt_oc/getFormatoExcelCarga';
$route['procesarFileMasivoAtenSolAcToPdtOcPan']     = 'cf_orden_compra/C_aten_masivo_sol_acta_pdt_oc/procesarFileMasivoAtenSolCreaOc';
$route['cargaAtenMasivaSolAcToPdtOcPan']            = 'cf_orden_compra/C_aten_masivo_sol_acta_pdt_oc/updateMasivoSolCreacionOC';
 
$route['ateMaCertOc']                            = 'cf_orden_compra/C_aten_masivo_sol_certi_oc';
$route['getFormatCertMasivoOc']                  = 'cf_orden_compra/C_aten_masivo_sol_certi_oc/getFormatoExcelCarga';
$route['procesarFileMasivoAtenSolCertOcPan']     = 'cf_orden_compra/C_aten_masivo_sol_certi_oc/procesarFileMasivoAtenSolCreaOc';
$route['cargaAtenMasivaSolCertOcPan']            = 'cf_orden_compra/C_aten_masivo_sol_certi_oc/updateMasivoSolCreacionOC';

$route['dashboard']     = 'cf_reportes/C_dashboard';
$route['drawLine']      = 'cf_reportes/C_dashboard/makeDataColum';

$route['getExpeLiqui']  = 'cf_consulta/c_bandeja_valida_obra/expediente_liquidacion';
$route['liquidacion']   = 'cf_consulta/c_bandeja_valida_obra/liquidacion';
$route['disenho']       = 'cf_consulta/c_bandeja_valida_obra/disenho';
$route['licencias']     = 'cf_consulta/c_bandeja_valida_obra/licencias';

$route['liquidacion_download']  = 'cf_consulta/c_bandeja_valida_obra/liquidacion_download';
$route['disenho_download']      = 'cf_consulta/c_bandeja_valida_obra/disenho_download';
$route['licencias_download']    = 'cf_consulta/c_bandeja_valida_obra/licencias_download';

$route['evidencias']     = 'cf_consulta/C_consulta_evidencias';
$route['findEviObra']     = 'cf_consulta/C_consulta_evidencias/filtrarTabla';
$route['getDetPartidasAll']     = 'cf_consulta/C_consulta_evidencias/getPartidasPaquetizadas';

$route['eviLicencia']     = 'cf_consulta/C_consulta_licencias';
$route['findEviObraLi']   = 'cf_consulta/C_consulta_licencias/filtrarTabla'; 

$route['rechMasDi']       = 'cf_b2b/C_validacion_diseno_b2b/manualRechazoDisenoMasivo';
$route['aprobMasDi']       = 'cf_b2b/C_validacion_diseno_b2b/manualAprobacionDisenoMasivo';

$route['regPoByCU']       = 'cf_utils/c_utils/validateRegPoByCostoUnitario';
$route['genSolExce']      = 'cf_utils/c_utils/generarSolicitud';

$route['banConPre']             = 'cf_control_presupuestal/C_control_presupuestal';
$route['validSolCP']            = 'cf_control_presupuestal/C_control_presupuestal/validarControlPresupuestal';
$route['openMdlDetalleExceso']  = 'cf_control_presupuestal/C_control_presupuestal/openMdlDetalleExceso';
$route['filBandejaExce']        = 'cf_control_presupuestal/C_control_presupuestal/filtrarBandejaExceso';

$route['banConPreCon']          = 'cf_control_presupuestal/C_consulta_control_presupuestal';
$route['filBandejaExceCon']     = 'cf_control_presupuestal/C_consulta_control_presupuestal/filtrarBandejaExceso';

$route['regMasHijoRefo']        = 'cf_reforzamiento_cto/C_registro_itemplan_hijo_masivo';
$route['getFormRefHijo']        = 'cf_reforzamiento_cto/C_registro_itemplan_hijo_masivo/getExcelCargaMasiva';
$route['reMasRefIP']            = 'cf_reforzamiento_cto/C_registro_itemplan_hijo_masivo/regItemplanRefMasivo';

$route['evaeecc']            = 'cf_reportes/C_evaluacion_eecc';
$route['filevaeecc']         = 'cf_reportes/C_evaluacion_eecc/filtrarReporteEvaEcc';

$route['boPep']                 = 'cf_control_presupuestal/C_bolsa_pep';
$route['filBoPep']              = 'cf_control_presupuestal/C_bolsa_pep/filtrarBolsaPep';
$route['newConfBoPep']          = 'cf_control_presupuestal/C_bolsa_pep/registrarNuevaConfig';
$route['updBoPepCon']           = 'cf_control_presupuestal/C_bolsa_pep/saveInactivarConfigBoPep';

$route['ipPep2']                 = 'cf_control_presupuestal/C_itemplan_pep2';
$route['filipPep2']              = 'cf_control_presupuestal/C_itemplan_pep2/filtrarBolsaPep';
$route['newConfipPep2']          = 'cf_control_presupuestal/C_itemplan_pep2/registrarNuevaConfig';
$route['updipPep2']              = 'cf_control_presupuestal/C_itemplan_pep2/saveInactivarConfigBoPep';
