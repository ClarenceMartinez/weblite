<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <title>
            <?php echo TITULO_FAVICON?>
        </title>        
        <meta name="description" content="Login">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
        <!-- Call App Mode on ios devices -->
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <!-- Remove Tap Highlight on Windows Phone IE -->
        <meta name="msapplication-tap-highlight" content="no">
        <!-- base css -->
        <link id="vendorsbundle" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/vendors.bundle.css">
        <link id="appbundle" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/app.bundle.css"> 
        <link id="mytheme" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/themes/<?php echo THEME_COLOR ?>">

		<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/page-login-alt.css">

        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>public/img/favicon/favicon-32x32.png">

        <style>
            body { 
                background-repeat: no-repeat !important;
                background-attachment: fixed !important;
                background-position: center !important;
            }

            .tittle1_login{
                font-weight: bold;
                    display: block;
                    font-family: Poppins-Bold;
                    font-size: 30px;
                    color: #333333;
                    line-height: 1.2;
                    text-align: center;
                    padding-bottom: 26px;
            }

            .tittle2_login{
                font-weight: bold;
                    display: block;
                    font-family: Poppins-Bold;
                    font-size: 20px;
                    color: #333333;
                    line-height: 1.2;
                    text-align: center;
                    padding-bottom: 26px;
            }
        </style>
    </head>
  
    <body>
        <!-- DOC: script to save and load page settings -->
        <div class="blankpage-form-field">
            
     
            <div class="card p-4 border-top-left-radius-0 border-top-right-radius-0">
            
            <span class="tittle1_login">Iniciar Sesion</span>
                    <div class="form-group">
                        <label class="form-label" for="username">Usuario</label>
                        <input type="email" id="username" class="form-control" placeholder="Ingrese Usuario" value="">
                        <span class="help-block">
                            Nombre de Usuario
                        </span>
                    </div>
                    <div class="form-group" style="padding-bottom: 20px;">
                        <label class="form-label" for="password">Contraseña</label>
                        <input type="password" id="password" class="form-control" placeholder="Ingrese Contraseña" value="">
                        <span class="help-block">
                            Clave
                        </span>
                    </div>
                    
                    <button onclick="logear()" type="submit" style="width: 100%;color: white;" class="btn btn-primary float-right">Ingresar</button>
                    <label style="text-align: center;color:red" id="msgError"></label>
                    <!--</form>-->
            </div>
            <!--
            <div class="blankpage-footer text-center">
                <a href="#"><strong>Recover Password</strong></a> | <a href="#"><strong>Register Account</strong></a>
            </div>
                -->
        </div>
        <div class="login-footer p-2">
            <div class="row">
                <div class="col col-sm-12 text-center">
                    <!--<i><strong>System Message:</strong> AQUI PUEDE HABER UN MENSAJE PARA EL USUARIO ANTES DE LOGEAR</i>-->
                </div>
            </div>
        </div>
        
        <script src="<?php echo base_url(); ?>public/js/vendors.bundle.js"></script>
        <script src="<?php echo base_url(); ?>public/js/app.bundle.js"></script>
        <script>

            function logear(){
                var user  = ($('#username').val()).trim();
                var passw = ($('#password').val()).trim();          
           
                if(user != '' && user != null && passw != '' && passw != null){ 
                    $.ajax({
                                type: "POST",
                                'url' : 'logear',
                                data: { 
                                        user    : user ,
                                        passwrd : passw
                                      },
                                'async' : false
                            })
                            .done(function(data) {
                                var data = JSON.parse(data);
                                if(data.error ==  1){
                                    $('#msgError').html(data.msj);
                                }else if(data.error == 0){                                    
                                    location.reload();
                                }
                            })
                }else{
                    $('#msgError').html('Ingrese Usuario y Clave');
                }    		
            }
            
            $("#password").keypress(function(e) {
                if(e.which == 13) {
                logear();
                }
             });
        </script>
        <!-- Page related scripts -->
    </body>
    <!-- END Body -->
</html>
