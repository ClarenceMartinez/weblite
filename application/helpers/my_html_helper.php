<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if(!function_exists('__buildArbolPermisosPanel')) {
    function __buildArbolPermisosPanel($idUsuario, $idPadre) {
        $CI =& get_instance();
        $CI->load->model('mf_utils/m_utils');
        $arbol = null;
        $hijos = $CI->m_utils->getDataArbolNav($idUsuario, $idPadre);
        $var = 0;
        foreach ($hijos as $hijo) {
            $arbol .= '<li>
                           <a class="mdl-button mdl-js-button mdl-js-ripple-effect" href="'.base_url().$hijo['route'].'">
                               <i class="mdi mdi-'.$hijo['icono'].'"></i> '.$hijo['descripcion'].'</a></li>';
            $var++;
        }
        $pie = '</ul>
            </li>';
        return $arbol;
    }
}

?>