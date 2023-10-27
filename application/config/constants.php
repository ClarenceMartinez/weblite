<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this settingn
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
// CONSTANTES DE CONFIGURACION
define('ANIO_CREATE_ITEMPLAN' , '21'); // SE UTILIZA TAMBIEN PARA AVERIA , ITEMFAULT.
define('ANIO_CREATE_PO'       , '2021');
define('ID_FASE_ANIO_CREATE_ITEMPLAN' , '7'); 



defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS_SISEGO') OR define('EXIT_SUCCESS_SISEGO', "0"); // no errors
defined('EXIT_ERROR_SISEGO')   OR define('EXIT_ERROR_SISEGO', "1"); // generic error
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

//CONSTRANTES PROPIOS

defined('TITULO_FAVICON')        OR define('TITULO_FAVICON', "PangeaCo"); // highest automatically-assigned error code
defined('TITULO_PLANTA_INTERNA') OR define('TITULO_PLANTA_INTERNA', "PangeaCo");
defined('TITULO_CONSULTA')       OR define('TITULO_CONSULTA', "PangeaCo");
defined('TITULO_WEB_PO')         OR define('TITULO_WEB_PO', "PangeaCo");

defined('ESTADO_CONFIG') OR define('ESTADO_CONFIG', 1);


//RUTAS
//defined('RUTA_OBRA2') OR define('RUTA_OBRA2', 'https://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null).'/obra2.0');
defined('RUTA_OBRA2') OR define('RUTA_OBRA2', 'login');

defined('RUTA_PLUGINS') OR define('RUTA_PLUGINS', 'https://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null).'/pangea/public/plugins/');
defined('RUTA_FONTS')   OR define('RUTA_FONTS'  , 'https://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null).'/pangea/public/fonts/');
defined('RUTA_CSS')     OR define('RUTA_CSS'    , 'https://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null).'/pangea/public/css/');
defined('RUTA_JS')      OR define('RUTA_JS'     , 'https://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null).'/pangea/public/js/');
defined('RUTA_IMG')     OR define('RUTA_IMG'    , 'https://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null).'/pangea/public/img/');
defined('RUTA_IMG_INTERNAS')     OR define('RUTA_IMG_INTERNAS'    , 'https://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null).'/pangea/public/img/internas/');

//ESTADOS PLAN
defined('ID_ESTADO_PLAN_PRE_REGISTRO')  OR define('ID_ESTADO_PLAN_PRE_REGISTRO', 1);    
defined('ID_ESTADO_PLAN_DISENIO')       OR define('ID_ESTADO_PLAN_DISENIO', 2);
defined('ID_ESTADO_PLAN_EN_OBRA')       OR define('ID_ESTADO_PLAN_EN_OBRA', 3);
defined('ID_ESTADO_PLAN_TERMINADO')     OR define('ID_ESTADO_PLAN_TERMINADO', 4);
defined('ID_ESTADO_PLAN_PDT_OC')        OR define('ID_ESTADO_PLAN_PDT_OC', 24);
defined('ID_ESTADO_PLAN_SUSPENDIDO')    OR define('ID_ESTADO_PLAN_SUSPENDIDO', 18);
defined('ID_ESTADO_PLAN_PRE_LIQUIDADO') OR define('ID_ESTADO_PLAN_PRE_LIQUIDADO', 9);
defined('ID_ESTADO_PLAN_EN_CERTIFICACION') OR define('ID_ESTADO_PLAN_EN_CERTIFICACION', 22);
defined('ID_ESTADO_PLAN_CERTIFICADO')      OR define('ID_ESTADO_PLAN_CERTIFICADO', 23);
defined('ID_ESTADO_PLAN_EN_LICENCIA') OR define('ID_ESTADO_PLAN_EN_LICENCIA', 19);
defined('ID_ESTADO_PLAN_EN_APROBACION') OR define('ID_ESTADO_PLAN_EN_APROBACION', 20);
defined('ID_ESTADO_PLAN_CANCELADO') OR define('ID_ESTADO_PLAN_CANCELADO', 6);

//ESTADOS PO 
defined('ID_ESTADO_PO_REGISTRADO') OR define('ID_ESTADO_PO_REGISTRADO', 1);
defined('ID_ESTADO_PO_PRE_APROBADO') OR define('ID_ESTADO_PO_PRE_APROBADO', 2);
defined('ID_ESTADO_PO_APROBADO') OR define('ID_ESTADO_PO_APROBADO', 3);
defined('ID_ESTADO_PO_LIQUIDADO') OR define('ID_ESTADO_PO_LIQUIDADO', 4);
defined('ID_ESTADO_PO_VALIDADO') OR define('ID_ESTADO_PO_VALIDADO', 5);
defined('ID_ESTADO_PO_CERTIFICADO') OR define('ID_ESTADO_PO_CERTIFICADO', 6);
defined('ID_ESTADO_PO_PRE_CANCELADO') OR define('ID_ESTADO_PO_PRE_CANCELADO', 7);
defined('ID_ESTADO_PO_CANCELADO') OR define('ID_ESTADO_PO_CANCELADO', 8);

//ESTACIONES
defined('ID_ESTACION_DISENO')  OR define('ID_ESTACION_DISENO', 1);
defined('ID_ESTACION_COAX')    OR define('ID_ESTACION_COAX', 2);
defined('ID_ESTACION_OC_COAX') OR define('ID_ESTACION_OC_COAX', 3);
defined('ID_ESTACION_PIN')     OR define('ID_ESTACION_PIN', 11);
defined('ID_ESTACION_FO')      OR define('ID_ESTACION_FO', 5);
defined('ID_ESTACION_MANTENIMIENTO')      OR define('ID_ESTACION_MANTENIMIENTO', 22);
defined('ID_ESTACION_OC_FO')      OR define('ID_ESTACION_OC_FO', 6);
//ID SUBPROYECTOS
define('ID_SUBPROYECTO_CABLEADO_EDIFICIOS_RESIDENCIAL_INTEGRAL' , '663');
define('ID_SUBPROYECTO_CABLEADO_EDIFICIOS_RESIDENCIAL_OVERLAY_I' , '665');
define('ID_SUBPROYECTO_MEGAPROYECTOS_INTEGRAL' , '670');
define('ID_SUBPROYECTO_MEGAPROYECTOS_OVERLAY_INTEGRAL' , '671');
define('ID_SUBPROYECTO_CABLEADO_EDIFICIOS_CTO_ADICIONAL' , '707');

//ID TIPO PLANTA
define('ID_TIPO_PLANTA_EXTERNA' , '1');
define('ID_TIPO_PLANTA_INTERNA' , '2');

//ID PROYECTO
define('ID_PROYECTO_CABLEADO_DE_EDIFICIOS' , '21');

//ID PROYECTO
defined('TIPO_SOLICITUD_OC_CREA')  OR define('TIPO_SOLICITUD_OC_CREA', 1);
defined('TIPO_SOLICITUD_OC_EDIC')  OR define('TIPO_SOLICITUD_OC_EDIC', 2);
defined('TIPO_SOLICITUD_OC_CERTI') OR define('TIPO_SOLICITUD_OC_CERTI', 3);
defined('TIPO_SOLICITUD_OC_ANULA') OR define('TIPO_SOLICITUD_OC_ANULA', 4);

defined('ID_ESTADO_SOLICITUD_OC_PDT') OR define('ID_ESTADO_SOLICITUD_OC_PDT', 1);
defined('ID_ESTADO_SOLICITUD_OC_ATENDIDO') OR define('ID_ESTADO_SOLICITUD_OC_ATENDIDO', 2);
defined('ID_ESTADO_SOLICITUD_OC_PDT_ACTA') OR define('ID_ESTADO_SOLICITUD_OC_PDT_ACTA', 5);

//MODULO
defined('ID_MODULO_DESPLIEGUE_PLANTA') OR define('ID_MODULO_DESPLIEGUE_PLANTA', 1);
defined('ID_MODULO_ADMINISTRATIVO')    OR define('ID_MODULO_ADMINISTRATIVO', 2);


//PERMISOS PADRES
defined('ID_GESTION_OBRA_PADRE')    OR define('ID_GESTION_OBRA_PADRE', 10);
defined('ID_PLANTA_INTERNA_PADRE')  OR define('ID_PLANTA_INTERNA_PADRE', 6);
defined('ID_EXCESO_OBRA_PADRE')     OR define('ID_EXCESO_OBRA_PADRE', 18);
defined('ID_ORDEN_COMPRA_PADRE')    OR define('ID_ORDEN_COMPRA_PADRE', 20);
defined('ID_GESTION_VR_PADRE')      OR define('ID_GESTION_VR_PADRE', 26);
defined('ID_CAP_REQUERIMIENTO_PADRE')     OR define('ID_CAP_REQUERIMIENTO_PADRE', 30);

//PERMISOS HIJOS
defined('ID_REGISTRAR_CAP_HIJO')     OR define('ID_REGISTRAR_CAP_HIJO', 31);
defined('ID_CONSULTAR_CAP_HIJO')     OR define('ID_CONSULTAR_CAP_HIJO', 32);

defined('ID_REGISTRO_ITEMPLAN_HIJO') OR define('ID_REGISTRO_ITEMPLAN_HIJO', 11);
defined('ID_CONSULTA_HIJO')          OR define('ID_CONSULTA_HIJO', 12);
defined('ID_COTIZACION_PIN_HIJO')    OR define('ID_COTIZACION_PIN_HIJO', 14);
defined('ID_APROBACION_PIN_HIJO')    OR define('ID_APROBACION_PIN_HIJO', 15);
defined('ID_LIQUIDACION_PIN_HIJO')   OR define('ID_LIQUIDACION_PIN_HIJO', 16);
defined('ID_VALIDACION_PIN_HIJO')    OR define('ID_VALIDACION_PIN_HIJO', 17);

defined('ID_BANDEJA_EXCESO_HIJO')       OR define('ID_BANDEJA_EXCESO_HIJO', 19);
defined('ID_BANDEJA_SOLICITUD_OC_HIJO') OR define('ID_BANDEJA_SOLICITUD_OC_HIJO', 21);

defined('ID_ATEN_SOL_OC_CREA_HIJO') OR define('ID_ATEN_SOL_OC_CREA_HIJO', 29);

//TIPO PO
define('TIPO_PO_MATERIAL' , '1');
define('TIPO_PO_MANO_OBRA' , '2');

defined('ESTADO_CONFIG_ACTIVO')   OR define('ESTADO_CONFIG_ACTIVO', 1);
defined('ESTADO_CONFIG_INACTIVO') OR define('ESTADO_CONFIG_INACTIVO', 0);

defined('CAP_ESTADO_PENDIENTE') OR define('CAP_ESTADO_PENDIENTE', 1);
defined('CAP_ESTADO_ATENDIDO')  OR define('CAP_ESTADO_ATENDIDO', 2);
defined('CAP_ESTADO_RECHAZADO') OR define('CAP_ESTADO_RECHAZADO', 3);

//ESTILOS

defined('ESTILO_BODY') OR define('ESTILO_BODY', 'mod-bg-1 mod-skin-default nav-function-top mod-smaller-font');
defined('THEME_COLOR') OR define('THEME_COLOR', 'cust-theme-5.css');

defined('ID_USUARIO_VALIDADOR_EDIFICIOS_AUTOMATICO') OR define('ID_USUARIO_VALIDADOR_EDIFICIOS_AUTOMATICO', '22');
defined('COSTO_PARTIDA_FERRETERIA_FTTH') OR define('COSTO_PARTIDA_FERRETERIA_FTTH', 19.64);//old 18.88; cambio realizado el 05.07.2023