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
        <link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>public/css/page-login-alt.css">

        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>public/img/favicon/favicon-32x32.png">

        <style>
            body { 
                background-repeat: no-repeat !important;
                background-attachment: fixed !important;
                background-position: center !important;
            }
        </style>
    </head>
  
    <body>
        <!-- DOC: script to save and load page settings -->
        <div class="blankpage-form-field">
            <div style="background-color: #78b7e3;" class="page-logo m-0 w-100 align-items-center justify-content-center rounded border-bottom-left-radius-0 border-bottom-right-radius-0 px-4">
                <a href="javascript:void(0)" class="page-logo-link press-scale-down d-flex align-items-center">
                    <!--<img src="<?php echo base_url(); ?>public/img/logotipo.jpg" alt="SmartAdmin WebApp" aria-roledescription="logo">-->
                    <span class="page-logo-text mr-1">Iniciar Sesion</span>
                </a>
            </div>
            <div class="card p-4 border-top-left-radius-0 border-top-right-radius-0">
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <input type="email" id="username" class="form-control" placeholder="your id or email" value="">
                        <span class="help-block">
                            Your unique username to app
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" class="form-control" placeholder="password" value="">
                        <span class="help-block">
                            Your password
                        </span>
                    </div>
                    
                    <button onclick="logear()" type="submit" style="width: 100%;" class="btn btn-default float-right">Ingresar</button>
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
