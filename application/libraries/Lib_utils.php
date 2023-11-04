<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Lib_utils {

  
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    function simple_encrypt($text, $clave){
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $clave, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
    
    function simple_decrypt($text, $clave){
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $clave, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }
    
    
    function validFecha($fecha){
        $test_arr  = explode('/', $fecha);
        if (count($test_arr) == 3) {
            if (checkdate($test_arr[1], $test_arr[0], $test_arr[2])) {//MES / DIA / YEAR
                return null;
            } else {
                return 'Fecha inv�lida';
            }
        } else {
            return 'Fecha inv�lida';
        }
    }
    
    function array_equal($a, $b) {
        return (is_array($a) && is_array($b) && array_diff($a, $b) === array_diff($b, $a));
    }
    
    //VALIDACIONES
    //MIN Y MAX
    function validLength($data,$min,$max){
        $lenght = strlen($data);
        $bool = false;
        if($min != null && $max != null){
            if($lenght >= $min && $lenght <= $max){
                $bool = true;
            }
        }else if($min != null){
            if($lenght >= $min){
                $bool = true;
            }
        }else if($max != null){
            if($lenght <= $max){
                $bool = true;
            }
        }
        return $bool;
    }
    
    // DATA :: DD/MM/YYYY
    function validateDate($date, $format = 'd/m/Y'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    function enviarEmail($correoDestino,$asunto,$body){
        $data['error'] = EXIT_ERROR;
        $data['msj']   = null;
    	try{
    		$CI =& get_instance();
    		$CI->load->library('email');    		
    		$configGmail = array('protocol'  => PROTOCOL,
                				 'smtp_host' => SMTP_HOST,
                				 'smtp_port' => SMTP_PORT,
                				 'smtp_user' => CORREO_BASE,
                				 'smtp_pass' => PASSWORD_BASE,
                				 'mailtype'  => MAILTYPE,
                				 'charset'   => 'utf-8',
                				 'newline'   => "\r\n",
                				 'starttls'  => TRUE);
    		$CI->email->initialize($configGmail);
    		$CI->email->from(CORREO_BASE);
    		$CI->email->to($correoDestino);
    		$CI->email->subject($asunto);
    		$CI->email->message($body);
    		if ($CI->email->send()) {
    			$data['error'] = EXIT_SUCCESS;
    		}else {
    		    $err = print_r($CI->email->print_debugger(), TRUE);
    		    log_message('error','err: '.$err);
    		    throw new Exception($err);
    		}
    	}catch(Exception $e){
    		$data['msj'] = $e->getMessage();
    	}
    	return $data;
    }
    
    function bodyMensajeResetearClave($nomComple,$usuario,$password){
    	$body	=	'<h2>Tu contrase�a ha cambiado!</h2>
						<p>Hola '.$nomComple.'!</p>
						<p>Se prosegui� a cambiar su clave para iniciar sesion en Plataforma Comunidades de Aprendizajes: </p>
						<ul>
						<li><h4>Usuario: '.$usuario.'</h4> </li>
						<li><h4>Clave: '.$password.'</h4></li>
						</ul>
						<p><h4>Ingresa a <a href="'.IP_URL_NATURA.'">Plataforma Comunidades de Aprendizajes</a> para iniciar sesi�n.</p>
						<img src="'.IP_URL_NATURA.FOTO_COMUNIDADES.'" height="60" width="300">';
    
    	return $body;
    }
    function getNombreUsuario($user){
        $nombres = preg_split("/[\s,]+/", $user['nombres']);
        $nombre  = $nombres[0];
        return $nombre;
    }
    public function makeHTMLToEMail($nombre){
        $html = '<p><img src="http://rockstartemplate.com/design/Blue_Simple_background.jpg" alt=""style="width:100%; height: 100px" /></p>
                <p style="text-align: center;">&nbsp;<span style="color: #3366ff;">Estimado'.$nombre.'bienvenido</span> a <strong><span style="color: #008000;">ELEARNING BMT.</span></strong></p>
                <p style="text-align: center;">Este correo electr&oacute;nico es utilizado para verificar que la direcci&oacute;n de correo que proporcion&oacute; es real.</p>
                <p style="text-align: center;">Para acceder al sistema usted debe utilizar los siguientes datos:</p>
                <div style="text-align: center;">Usuario:</div>
                <div><hr />
                <div style="text-align: center;">usuarioEBF2C7F29A0F</div>
                <hr /></div>
    
                <div>&nbsp;</div>
                <div>
                <div style="text-align: center;">Password:</div>
                <div><hr />
                <div style="text-align: center;">passwordEBF2C7F29A0F</div>
                <hr /></div>
                <div style="text-align: center;">Para acceder al Sistema de cllic en el siguiente enlace</div>
                <div style="text-align: center;">&nbsp;</div>
                <div style="text-align: center;"><a style="text-decoration: none; background: #1ab394; color: #fff; padding: 5px;" title="Elearning" href="localhost/elearning" target="_blank">Ir a Sistema ELEARNING</a></div>
                <div style="text-align: center;">&nbsp;</div>
                </div>
                <p style="text-align: center;"><span style="color: #999999; background-color: #ffffff;">&nbsp;BMT ELEARNING&nbsp;&copy; 2017 Lima, Per&uacute;.</span></p>
                <blockquote>
                <p><span style="color: #999999;"><em>Si recibi&oacute; este correo electr&oacute;nico pero no se registr&oacute; en NeoBux significa que alguien se registr&oacute; utilizando esta direcci&oacute;n de correo electr&oacute;nico. Si no se registr&oacute;, simplemente ignore este correo.</em></span></p>
                </blockquote>';
        return $html;
    }
    
    function getDecimalNumber($numero){// xxx,xxx,xxx.xx
        return number_format($numero,3,'.',",");
    }
    
    function getDecimalNumber2($numero){// xxx,xxx,xxx.xx
        return number_format($numero,2,'.',",");
    }
    /**
     * primer nivel:
     */
  
    function getHTMLPermisos($array, $id_permiso_nivel_2, $id_permiso_nivel_3, $id_permiso_nivel_4, $idModulo){
        $data = array();
        $html = '<ul id="js-nav-menu" class="nav-menu">';
        $active = 'class="active"';
        $active_open = 'class="active open"';
        foreach ($array as $row){
            if($row['idModulo'] == $idModulo) {
                if($row['nivel']    ==  1){

                    $html .= '<li class="nav-title">'.$row['descripcion'].'</li>';
                    foreach($row['hijos'] as $row2){
                        if($row2['nivel']   ==  2){
                            $html .= ' <li '.(($row2['id_permiso']==$id_permiso_nivel_2) ? $active_open   :   '').'>
                                            <a href="#" title="Pages" data-filter-tags="pages">
                                                <i class="fal '.$row2['icono'].'"></i>
                                                <span class="nav-link-text" data-i18n="nav.pages">'.$row2['descripcion'].'</span><!--nivel 2-->
                                            </a>
                                            <ul>';
                            foreach($row2['hijos'] as $row3){
                                if($row3['nivel']    ==  3){
                                    $html .= '<li '.(($row3['id_permiso']==$id_permiso_nivel_3) ? $active_open   :   '').'>
                                                    <a href="javascript:void(0);" title="Forum" data-filter-tags="pages forum">
                                                        <span class="nav-link-text" data-i18n="nav.pages_forum">'.$row3['descripcion'].'</span><!--nivel 3-->
                                                    </a>';
                                    if(count($row3['hijos'])> 0){
                                        $html   .=  '<ul>';
                                        foreach($row3['hijos']  as $row4){
                                            if($row4['nivel']    ==  4){
                                                $html .= '<li '.(($row4['id_permiso']==$id_permiso_nivel_4) ? $active   :   '').'>
                                                            <a href=#'.$row4['route'].'" title="List" data-filter-tags="pages forum list">
                                                                <span class="nav-link-text" data-i18n="nav.pages_forum_list">'.$row4['descripcion'].'</span><!--nivel 4-->
                                                            </a>
                                                        </li>';
                                            }                                           
                                        }
                                        $html   .=  '</ul>';
                                    }
                                    $html .= '</li>';
                                }else if($row3['nivel']    ==  4){
                                    $html .= '<li '.(($row3['id_permiso']==$id_permiso_nivel_4) ? $active   :   '').'>
                                                    <a href="'.$row3['route'].'" title="Contacts" data-filter-tags="pages contacts">
                                                        <span class="nav-link-text" data-i18n="nav.pages_contacts">'.$row3['descripcion'].'</span><!--nivel 4-->
                                                    </a>
                                                </li>';
                                }
                                 
                            }        
                            $html .= '</ul>';                
                        }
                       
                    }
                    $html .= '</li>';

                }else if($row['nivel']    ==  2){
                    $html .= ' <li '.(($row['id_permiso']   ==  $id_permiso_nivel_2) ? $active_open   :   '').'>
                    <a href="#" title="Pages" data-filter-tags="pages">
                        <i class="fal '.$row['icono'].'"></i>
                        <span class="nav-link-text" data-i18n="nav.pages">'.$row['descripcion'].'</span><!--nivel 2-->
                    </a>
                <ul>';
                        foreach($row['hijos'] as $row3){
                        if($row3['nivel']    ==  3){
                            $html .= '<li '.(($row3['id_permiso']   ==  $id_permiso_nivel_3) ? $active_open   :   '').'>
                                            <a href="javascript:void(0);" title="Forum" data-filter-tags="pages forum">
                                                <span class="nav-link-text" data-i18n="nav.pages_forum">'.$row3['descripcion'].'</span><!--nivel 3-->
                                            </a>';
                            if(count($row3['hijos'])> 0){
                                $html   .=  '<ul>';
                                foreach($row3['hijos']  as $row4){                                  
                                    if($row4['nivel']    ==  4){
                                        $html .= '<li '.(($row4['id_permiso']   ==  $id_permiso_nivel_4) ? $active   :   '').'>
                                                    <a href="'.$row4['route'].'" title="List" data-filter-tags="pages forum list">
                                                        <span class="nav-link-text" data-i18n="nav.pages_forum_list">'.$row4['descripcion'].'</span><!--nivel 4-->
                                                    </a>
                                                </li>';
                                    }                                  
                                }
                                $html   .=  '</ul>';
                             }
                            $html .= '</li>';
                        }else if($row3['nivel']    ==  4){
                            $html .= '<li '.(($row3['id_permiso']   ==  $id_permiso_nivel_4) ? $active   :   '').'>
                                            <a href="'.$row3['route'].'" title="Contacts" data-filter-tags="pages contacts">
                                                <span class="nav-link-text" data-i18n="nav.pages_contacts">'.$row3['descripcion'].'</span><!--nivel 4-->
                                            </a>
                                        </li>';
                        }
                        
                    }        
                    $html .= '</ul>';  
                }
            }
    
        }
        $html .= '</ul>';
			
        //log_message('error', $html);
        $data['html'] = $html;
        return $data;
    }
	
    public function removeEnterYTabs($texto){
        return str_replace(PHP_EOL,' ',trim(preg_replace('/[ ]{2,}|[\t]/',' ',$texto)));
    }

    public function getHeader(){
        $CI =& get_instance();
        $html = '<header class="page-header" role="banner" style="justify-content: center">
                    <!-- we need this logo when user switches to nav-function-top -->
                    <div class="page-logo" style="position: absolute;">
                        <a href="welcome"><h1 style="font-weight: 900;color: var(--theme-light);">PangeaCo</h1></a>
                    </div>
                    <!-- DOC: nav menu layout change shortcut -->
                    <div class="hidden-md-down dropdown-icon-menu position-relative">
                        <a href="#" class="header-btn btn js-waves-off" data-action="toggle" data-class="nav-function-hidden" title="Hide Navigation">
                            <i class="ni ni-menu"></i>
                        </a>
                        <ul>
                            <li>
                                <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-minify" title="Minify Navigation">
                                    <i class="ni ni-minify-nav"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-fixed" title="Lock Navigation">
                                    <i class="ni ni-lock-nav"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- DOC: mobile button appears during mobile width -->
                    <div class="hidden-lg-up">
                        <a href="#" class="header-btn btn press-scale-down" data-action="toggle" data-class="mobile-nav-on">
                            <i class="ni ni-menu"></i>
                        </a>
                    </div>
                
                    <div class="ml-auto d-flex">
                        <!-- activate app search icon (mobile) 
                        <div class="hidden-sm-up">
                            <a href="#" class="header-icon" data-action="toggle" data-class="mobile-search-on" data-focus="search-field" title="Search">
                                <i class="fal fa-search"></i>
                            </a>
                        </div>-->
                        <!-- app settings
                        <div class="hidden-md-down">
                            <a href="#" class="header-icon" data-toggle="modal" data-target=".js-modal-settings">
                                <i class="fal fa-cog"></i>
                            </a>
                        </div>                        
                        -->
                        <!-- app user menu -->
                        <div>
                            <a href="#" data-toggle="dropdown" title="Administrar Cuenta" class="header-icon d-flex align-items-center justify-content-center ml-2">
                                <img src="'.base_url().'public/img/demo/avatars/avatar-m.png" class="rounded-circle profile-image">
                                <!-- you can also add username next to the avatar with the codes below:
                                <span class="ml-1 mr-1 text-truncate text-truncate-header hidden-xs-down">Me</span>
                                <i class="ni ni-chevron-down hidden-xs-down"></i> -->
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
                                <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
                                    <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                                        <!--<span class="mr-2">
                                            <img src="'.base_url().'public/img/demo/avatars/avatar-m.png" class="rounded-circle profile-image">
                                        </span>-->
                                        <div class="info-card-text">                                                
                                            <span class="text-truncate text-truncate-md opacity-80">'.$CI->session->userdata('descPerfilSessionPan').'</span>
                                            <div class="fs-lg text-truncate text-truncate-lg">'.$CI->session->userdata('usernameSessionPan').'</div>
                                            <div class="fs-lg text-truncate text-truncate-lg text-dark">'.$CI->session->userdata('empresaColabDesc').'</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>         
                                <a  class="dropdown-item fw-500 pt-3 pb-3" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
									Cambiar Clave
								</a>
                                <div class="collapse" id="collapseExample">
                                    <div class="card card-body">
                                        
                                            <div class="panel-content">
                                                <div class="form-row">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label" for="validationCustom01">Nueva Clave <span class="text-danger">*</span> </label>
                                                        <input type="text" class="form-control" id="txtNewPass" placeholder="Ingrese Nueva Clave" required>
                                                        <div class="valid-feedback">
                                                            Looks good!
                                                        </div>
                                                    </div>			
                                                </div>			
                                            </div>
                                            <div style="text-align: center;">		 
                                                <button style="color:white;" onclick="updatePass();" class="btn btn-primary ml-auto" type="submit">Guardar</button>
                                            </div>
                                          
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <a class="dropdown-item fw-500 pt-3 pb-3" href="logOut">
                                    <span data-i18n="drpdwn.page-logout">Cerrar Sesion</span> 
                                </a>
                            </div>
                        </div>
                    </div>
                </header>';
               
        return $html;
    }
}