<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_login extends CI_Controller {

    var $login;

    function __construct() {
        parent::__construct();
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->model('mf_login/m_login');
        $this->load->model('mf_utils/m_utils');
        $this->load->library('lib_utils');
        $this->load->helper('url');
    }

    public function index() {
        $data = array();
        $logedUser = $this->session->userdata('idPersonaSessionPan');
        if ($logedUser != null) {  
            //redirect('getPanel', 'refresh');         
            redirect('welcome', 'refresh');      
            //$this->load->view('v_login', $data);
        } else {
            $this->load->view('v_login', $data);
        }
    }


    public function logear() {
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try {
            $user = $this->input->post('user');
            $pasw = $this->input->post('passwrd');
        
            $resultado = $this->m_login->getUserInfo($user);
    
            if ($resultado != null) {

                    if (password_verify($pasw, $resultado['clave'])) {
                        $perfilesInfo = $this->m_login->getPerfilesDescByIdUsuario($resultado['id_usuario']);
                        $this->session->set_userdata(
                                array(  
                                        'idPersonaSessionPan'      => $resultado['id_usuario'],
                                        'usernameSessionPan'       => $resultado['nombre_completo']. ' ' . $resultado['ape_paterno'],
                                        'idPerfilSessionPan'       => $perfilesInfo['id_perfiles'],
                                        'descPerfilSessionPan'     => $perfilesInfo['perfiles'],
                                        'permisosArbolPan'         => $this->makeListaPermisos($resultado['id_usuario'], null),
                                        'idEmpresaColabSesion'     => $resultado['idEmpresaColab'],
                                        'empresaColabDesc'         => $resultado['empresaColabDesc']
                                    )
                        );
                        $data['error']  =   EXIT_SUCCESS;
                    } else {
                       // log_message('error',password_hash('123',PASSWORD_DEFAULT));
                        throw new Exception('Clave Incorrecta!');
                    }
                
            } else {
                throw new Exception('El Usuario no existe.');
            }
        }catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        } 
        echo json_encode(array_map('utf8_encode', $data));
    }

    function logOut() {
        $logedUser = $this->session->userdata('idPersonaSessionPan');
        if ($logedUser != null) {
            $this->session->sess_destroy();
            //redirect(RUTA_OBRA2, 'refresh');
            redirect('login', 'refresh');
        }
    }

    function makeListaPermisos($idUsuario) {
        $salida = array();
        $cabezaPermisoList = array();
        $listCabeza = array();
        $permisosPadres = $this->m_login->getPermisosRoute($idUsuario);//Obtenemos nivel 4            
        foreach ($permisosPadres as $row) {
            $permiso = $this->m_login->getPermisoByIdPermiso($row['id_padre']);
            if($permiso!=null){
                if($permiso['id_padre'] == null){ 
                    if($permiso['nivel'] == 2){
                         //cabeza de permisos
                         $cabeza        = array();
                         $cabeza['id_permiso']  =  $permiso['id_permiso'];
                         $cabeza['nivel']       =  $permiso['nivel'];
                         $cabeza['icono']       =  $permiso['icono'];         
                         $cabeza['descripcion'] =  $permiso['descripcion'];    
                         $cabeza['route']       =  $permiso['route'];    
                         $cabeza['id_padre']    =  $permiso['id_padre'];
                         $cabeza['idModulo']    =  $permiso['idModulo'];      
                         if(!in_array($permiso['id_permiso'], $listCabeza))  {                
                            array_push($cabezaPermisoList, $cabeza);
                            array_push($listCabeza, $permiso['id_permiso']);
                         }
                    }  
                    
                }else{ 
                    if($permiso['nivel'] == 3){//el siguiente permiso es de nivel 3 siempre tiene un padre
                        $permiso2 = $this->m_login->getPermisoByIdPermiso($permiso['id_padre']);//datos del permiso nivel 2
                        if($permiso2['id_padre']    ==  null){
                            //cabeza de permisos
                            $cabeza        = array();
                            $cabeza['id_permiso']  =  $permiso2['id_permiso'];
                            $cabeza['nivel']       =  $permiso2['nivel'];
                            $cabeza['icono']       =  $permiso2['icono'];         
                            $cabeza['descripcion'] =  $permiso2['descripcion']; 
                            $cabeza['route']       =  $permiso2['route'];    
                            $cabeza['id_padre']    =  $permiso2['id_padre'];
                            $cabeza['idModulo']    =  $permiso2['idModulo'];                       
                            if(!in_array($permiso2['id_permiso'],$listCabeza ))  {                
                                array_push($cabezaPermisoList, $cabeza);
                                array_push($listCabeza, $permiso2['id_permiso']);
                             }
                            
                        }else{
                            $permiso3 =  $this->m_login->getPermisoByIdPermiso($permiso2['id_padre']);//datos del permiso nivel 1  
                            //cabeza de permisos
                            $cabeza        = array();
                            $cabeza['id_permiso']  =  $permiso3['id_permiso'];
                            $cabeza['nivel']       =  $permiso3['nivel'];
                            $cabeza['icono']       =  $permiso3['icono'];         
                            $cabeza['descripcion'] =  $permiso3['descripcion'];
                            $cabeza['route']       =  $permiso3['route'];    
                            $cabeza['id_padre']    =  $permiso3['id_padre'];
                            $cabeza['idModulo']    =  $permiso2['idModulo'];
                            if(!in_array($permiso3['id_permiso'],$listCabeza ))  {                
                                array_push($cabezaPermisoList, $cabeza);
                                array_push($listCabeza, $permiso3['id_permiso']);
                             }
                           
                        }
                        
                    }else if($permiso['nivel'] == 2){//el siguiente permiso es de nivel 2 y tiene padre
                        $permiso2 = $this->m_login->getPermisoByIdPermiso($permiso['id_padre']);//datos del permiso nivel 1
                        //cabeza de permisos
                        $cabeza        = array();
                        $cabeza['id_permiso']  =  $permiso2['id_permiso'];
                        $cabeza['nivel']       =  $permiso2['nivel'];
                        $cabeza['icono']       =  $permiso2['icono'];         
                        $cabeza['descripcion'] =  $permiso2['descripcion'];
                        $cabeza['route']       =  $permiso2['route'];    
                        $cabeza['id_padre']    =  $permiso2['id_padre'];
                        $cabeza['idModulo']    =  $permiso2['idModulo']; 
                        if(!in_array($permiso2['id_permiso'],$listCabeza ))  {                
                            array_push($cabezaPermisoList, $cabeza);
                            array_push($listCabeza, $permiso2['id_permiso']);
                         }
                      
                    }
                }

            }
        }
      //  log_message('error', 'cabezas..'.print_r($cabezaPermisoList, true));
            foreach ($cabezaPermisoList as $ItemCabeza) {//iteramos las cabezas o es nivel  1 o 2
                $array_hijos_nivel_2 = array();
                
                if($ItemCabeza['nivel'] ==  1){//INICIAMOS LA BUSQUEDA DE LOS PERMISOS DEL NIVEL 2
                    $hijos_nivel_2 = $this->m_login->getPermisosByIdPadre($ItemCabeza['id_permiso']);
                    foreach($hijos_nivel_2 as $hijo_n2){
                        $array_hijos_n3_n4 = array();
                        $hijos_nivel_3_4 = $this->m_login->getPermisosByIdPadre($hijo_n2['id_permiso']);
                        //log_message('error', 'hijos_nivel_3_4:'.print_r($hijos_nivel_3_4, true));
                        foreach($hijos_nivel_3_4 as $hijo_3_4){
                            $array_hijos_4_3 = array();
                            if($hijo_3_4['nivel']   ==  3){
                                $hijos_nivel_4_de_3 = $this->m_login->getPermisosByIdPadre($hijo_3_4['id_permiso']);
                                foreach($hijos_nivel_4_de_3 as $hijo_4_3){
                                    $is_hijo_4_3 = $this->m_login->getPermisoByIdPadreAndUsuarioN4($idUsuario, $hijo_4_3['id_permiso']);
                                    if($is_hijo_4_3  != null){
                                        array_push($array_hijos_4_3, $hijo_4_3);
                                    }
                                }                               
                                if(count($array_hijos_4_3)  > 0){//si hay 1 hijo agregar
                                    $hijo_3_4['hijos']  =   $array_hijos_4_3;                                
                                    array_push($array_hijos_n3_n4, $hijo_3_4);
                                }
                            }else if($hijo_3_4['nivel']   ==  4){
                                $is_hijo_n4 = $this->m_login->getPermisoByIdPadreAndUsuarioN4($idUsuario, $hijo_3_4['id_permiso']);
                                if($is_hijo_n4  != null){
                                    array_push($array_hijos_n3_n4, $hijo_3_4);
                                }
                            }
                        }
                        //log_message('error', 'faqq:'.print_r($hijo_3_4, true));         
                        if(count($array_hijos_n3_n4)    >   0){               
                            $hijo_n2['hijos'] = $array_hijos_n3_n4; 
                            array_push($array_hijos_nivel_2, $hijo_n2);
                        }
                    }

                    $ItemCabeza['hijos']    =   $array_hijos_nivel_2;//ASOCIAMOS LOS HIJOS ENCONTRADOS AL ITEMCABEZA
                    array_push($salida, $ItemCabeza);
                } else if($ItemCabeza['nivel'] ==  2){//INICIAMOS LA BUSQUEDA DE LOS PERMISOS DEL NIVEL 3 O 4
                        $array_hijos_n3_n4 = array();
                        $hijos_nivel_3_4 = $this->m_login->getPermisosByIdPadre($ItemCabeza['id_permiso']);
                        //log_message('error', 'hijos_nivel_3_4:'.print_r($hijos_nivel_3_4, true)); 
                        foreach($hijos_nivel_3_4 as $hijo_3_4){
                           // log_message('error', 'hijo_3_4:'.print_r($hijo_3_4, true));
                            $array_hijos_4_3 = array();
                            if($hijo_3_4['nivel']   ==  3){
                                $hijos_nivel_4_de_3 = $this->m_login->getPermisosByIdPadre($hijo_3_4['id_permiso']);
                                foreach($hijos_nivel_4_de_3 as $hijo_4_3){
                                    $is_hijo_4_3 = $this->m_login->getPermisoByIdPadreAndUsuarioN4($idUsuario, $hijo_4_3['id_permiso']);
                                    if($is_hijo_4_3  != null){
                                        array_push($array_hijos_4_3, $hijo_4_3);
                                    }
                                }                               
                                if(count($array_hijos_4_3)  > 0){//si hay 1 hijo agregar
                                    $hijo_3_4['hijos']  =   $array_hijos_4_3;                                
                                    array_push($array_hijos_n3_n4, $hijo_3_4);
                                }
                            }else if($hijo_3_4['nivel']   ==  4){
                                $is_hijo_n4 = $this->m_login->getPermisoByIdPadreAndUsuarioN4($idUsuario, $hijo_3_4['id_permiso']);
                                if($is_hijo_n4  != null){
                                    array_push($array_hijos_n3_n4, $hijo_3_4);
                                }
                            }
                        }
                    //log_message('error', 'array_hijos_n3_n4:'.print_r($array_hijos_n3_n4, true));    
                    if(count($array_hijos_n3_n4) > 0){                    
                        $ItemCabeza['hijos'] = $array_hijos_n3_n4; 
                        array_push($salida, $ItemCabeza);
                    }
                }
                
            }
        return $salida;
    }

    public function updatePassword(){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
        try {
            $idUsuario = $this->session->userdata('idPersonaSessionPan');
            if($idUsuario == null || $idUsuario == ''){
                throw new Exception("La sesion caduco, cargar nuevamente la pagina.");
            }
           
            $newPasw = $this->input->post('newPass');
            $password   = password_hash($newPasw, PASSWORD_DEFAULT);
            $updateUser = array( 'id_usuario'   =>  $idUsuario,
                                'clave'        =>  $password);

            $data   =   $this->m_utils->updateUsuario($idUsuario, $updateUser);
                    
        }catch(Exception $e) {
            $data['msj'] = $e->getMessage();
        } 
        echo json_encode($data);
    }
    
}
