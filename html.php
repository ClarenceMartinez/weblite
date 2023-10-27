
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from byrushan.com/projects/material-admin/app/2.0/jquery/bs4/hidden-sidebar.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 07 Jul 2017 17:16:44 GMT -->
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Vendor styles -->
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/bower_components/animate.css/animate.min.css">
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/bower_components/jquery.scrollbar/jquery.scrollbar.css">
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/bower_components/fullcalendar/dist/fullcalendar.min.css">
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/bower_components/dropzone/dist/dropzone.css">
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/bower_components/sweetalert2/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/bower_components/notify/pnotify.custom.min.css">
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/bower_components/select2/dist/css/select2.min.css">
        <!-- App styles -->
        <link rel="stylesheet" href="https://www.ing-fix.com/obra2.0/public/css/app.min.css">
    </head>

    <body data-ma-theme="entel">
        <main class="main">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>

            <header class="header">
                <div class="navigation-trigger" data-ma-action="aside-open" data-ma-target=".sidebar">
                    <div class="navigation-trigger__inner">
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                    </div>
                </div>

                <div class="header__logo hidden-sm-down" style="text-align: center;">
                   <a href="" title=""><img src="https://www.ing-fix.com/obra2.0/public/img/logo/logo-telefonica.jpg" alt=""></a>
                </div>
<!--
                <form class="search">
                    <div class="search__inner">
                        <input type="text" class="search__text" placeholder="Search for people, files, documents...">
                        <i class="zmdi zmdi-search search__helper" data-ma-action="search-close"></i>
                    </div>
                </form>
-->
                <ul class="top-nav">


            <!-- 

                    <li class="dropdown top-nav__notifications">
                        <a href="#" data-toggle="dropdown" class="top-nav__notify">
                            <i class="zmdi zmdi-notifications"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu--block">
                            <div class="listview listview--hover">
                                <div class="listview__header">
                                    Notifications

                                    <div class="actions">
                                        <a href="#" class="actions__item zmdi zmdi-check-all" data-ma-action="notifications-clear"></a>
                                    </div>
                                </div>

                                <div class="listview__scroll scrollbar-inner">
                                    <a href="#" class="listview__item">
                                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/1.jpg" class="listview__img" alt="">

                                        <div class="listview__content">
                                            <div class="listview__heading">David Belle</div>
                                            <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                        </div>
                                    </a>

                                    <a href="#" class="listview__item">
                                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/2.jpg" class="listview__img" alt="">

                                        <div class="listview__content">
                                            <div class="listview__heading">Jonathan Morris</div>
                                            <p>Nunc quis diam diamurabitur at dolor elementum, dictum turpis vel</p>
                                        </div>
                                    </a>

                                    <a href="#" class="listview__item">
                                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/3.jpg" class="listview__img" alt="">

                                        <div class="listview__content">
                                            <div class="listview__heading">Fredric Mitchell Jr.</div>
                                            <p>Phasellus a ante et est ornare accumsan at vel magnauis blandit turpis at augue ultricies</p>
                                        </div>
                                    </a>

                                    <a href="#" class="listview__item">
                                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/4.jpg" class="listview__img" alt="">

                                        <div class="listview__content">
                                            <div class="listview__heading">Glenn Jecobs</div>
                                            <p>Ut vitae lacus sem ellentesque maximus, nunc sit amet varius dignissim, dui est consectetur neque</p>
                                        </div>
                                    </a>

                                    <a href="#" class="listview__item">
                                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/5.jpg" class="listview__img" alt="">

                                        <div class="listview__content">
                                            <div class="listview__heading">Bill Phillips</div>
                                            <p>Proin laoreet commodo eros id faucibus. Donec ligula quam, imperdiet vel ante placerat</p>
                                        </div>
                                    </a>

                                    <a href="#" class="listview__item">
                                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/1.jpg" class="listview__img" alt="">

                                        <div class="listview__content">
                                            <div class="listview__heading">David Belle</div>
                                            <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                        </div>
                                    </a>

                                    <a href="#" class="listview__item">
                                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/2.jpg" class="listview__img" alt="">

                                        <div class="listview__content">
                                            <div class="listview__heading">Jonathan Morris</div>
                                            <p>Nunc quis diam diamurabitur at dolor elementum, dictum turpis vel</p>
                                        </div>
                                    </a>

                                    <a href="#" class="listview__item">
                                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/3.jpg" class="listview__img" alt="">

                                        <div class="listview__content">
                                            <div class="listview__heading">Fredric Mitchell Jr.</div>
                                            <p>Phasellus a ante et est ornare accumsan at vel magnauis blandit turpis at augue ultricies</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="p-1"></div>
                            </div>
                        </div>
                    </li>-->



                    <li class="hidden-xs-down">
                        <a href="#" data-toggle="dropdown" aria-expanded="false">
                            <i class="zmdi zmdi-power"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">

						                            <a href="logOut" class="dropdown-item">Cerrar Sesi&oacute;n</a>
                        </div>
                    </li>
                </ul>
            </header>

            <aside class="sidebar sidebar--hidden">
                <div class="scrollbar-inner">
                    <div class="user">
                        <div class="user__info" data-toggle="dropdown">
                            <img class="user__img" src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/8.jpg" alt="">
                            <div>
                                <div class="user__name">Owen Saravia</div>
                                <div class="user__email">ADMINISTRADOR</div>
                            </div>
                        </div>

                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">View Profile</a>
                            <a class="dropdown-item" href="#">Settings</a>
                            <a class="dropdown-item" href="#">Logout</a>
                        </div>
                    </div>

                    <ul class="navigation">

						         <li class="navigation__sub navigation__sub--active navigation__sub--toggled">  <a>
                        <i class="zmdi zmdi-view-week"></i>Plan de Obra
                        </a>
                        <ul><li><a href="consulta">Consultas</a></li><li><a href="updatefech">Carga Masiva Fechas PO</a></li><li><a href="gestpo2">Gestionar PO</a></li><li><a href="pocar">Carga Masiva Plan de Obra</a></li><li><a href="dpcarga">Carga Masiva Detalle Plan</a></li><li><a href="changeEjec">Liquidador Masivo</a></li><li><a href="detalleObra">Detalle de Obra</a></li> </ul>
                       </li><li class="navigation__sub">  <a>
                        <i class="zmdi zmdi-view-week"></i>Reportes
                        </a>
                        <ul><li><a href="rebapro">Seguimiento BA</a></li><li><a href="segpdo3">Seguimiento PO</a></li><li><a href="segava">Seguimiento Avance Operativa</a></li><li><a href="itemptr">PO Pdte. Aprob MAT</a></li><li><a href="itemMO">PO Pdte. Aprob MO</a></li><li class="navigation__active"><a href="extrac">Extractor</a></li> </ul>
                       </li><li class="navigation__sub">  <a>
                        <i class="zmdi zmdi-view-week"></i>Bandejas
                        </a>
                        <ul><li><a href="preAproMo">Bandeja Pre - Certifica</a></li><li><a href="conPreCerti">Consulta Bandeja Pre - Certifica</a></li><li><a href="preAprob">Bandeja Pre Aprob</a></li><li><a href="liqui">Bandeja Aprob</a></li> </ul>
                       </li><li class="navigation__sub">  <a>
                        <i class="zmdi zmdi-view-week"></i>Tranferencias
                        </a>
                        <ul><li><a href="tranwu">Web Unificada</a></li><li><a href="sapco">Tranferencia Sap Coaxial</a></li><li><a href="sapfi">Tranferencia Sap Fija</a></li> </ul>
                       </li><li class="navigation__sub">  <a>
                        <i class="zmdi zmdi-view-week"></i>Mantenimiento
                        </a>
                        <ul><li><a href="mspg">Subproyecto, Pep, Grafo</a></li><li><a href="mUsuario">Usuarios</a></li><li><a href="mproyecto">Proyecto - Sub Proyecto</a></li><li><a href="mcentral">Central</a></li><li><a href="EstacionArea">Area & Estacion</a></li> </ul>
                       </li><li class="navigation__sub">  <a>
                        <i class="zmdi zmdi-view-week"></i>Diseño
                        </a>
                        <ul><li><a href="preAprobdi">Bandeja Pre Aprob. Diseño</a></li><li><a href="preCerDi">Bandeja Pre - Certificacion Diseño</a></li> </ul>
                       </li><li class="navigation__sub">  <a>
                        <i class="zmdi zmdi-view-week"></i>Planta Interna
                        </a>
                        <ul><li><a href="plantaInterna">Bandeja Registrio PTR Interna</a></li><li><a href="detallePI">Detalle PLanta Interna</a></li> </ul>
                       </li>                    </ul>
                </div>
            </aside>

            <aside class="chat">
                <div class="chat__header">
                    <h2 class="chat__title">Chat <small>Currently 20 contacts online</small></h2>

                    <div class="chat__search">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search...">
                            <i class="form-group__bar"></i>
                        </div>
                    </div>
                </div>

                <div class="listview listview--hover chat__buddies scrollbar-inner">
                    <a class="listview__item chat__available">
                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/7.jpg" class="listview__img" alt="">

                        <div class="listview__content">
                            <div class="listview__heading">Jeannette Lawson</div>
                            <p>hey, how are you doing.</p>
                        </div>
                    </a>

                    <a class="listview__item chat__available">
                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/5.jpg" class="listview__img" alt="">

                        <div class="listview__content">
                            <div class="listview__heading">Jeannette Lawson</div>
                            <p>hmm...</p>
                        </div>
                    </a>

                    <a class="listview__item chat__away">
                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/3.jpg" class="listview__img" alt="">

                        <div class="listview__content">
                            <div class="listview__heading">Jeannette Lawson</div>
                            <p>all good</p>
                        </div>
                    </a>

                    <a class="listview__item">
                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/8.jpg" class="listview__img" alt="">

                        <div class="listview__content">
                            <div class="listview__heading">Jeannette Lawson</div>
                            <p>morbi leo risus portaac consectetur vestibulum at eros.</p>
                        </div>
                    </a>

                    <a class="listview__item">
                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/6.jpg" class="listview__img" alt="">

                        <div class="listview__content">
                            <div class="listview__heading">Jeannette Lawson</div>
                            <p>fusce dapibus</p>
                        </div>
                    </a>

                    <a class="listview__item chat__busy">
                        <img src="https://www.ing-fix.com/obra2.0/public/demo/img/profile-pics/9.jpg" class="listview__img" alt="">

                        <div class="listview__content">
                            <div class="listview__heading">Jeannette Lawson</div>
                            <p>cras mattis consectetur purus sit amet fermentum.</p>
                        </div>
                    </a>
                </div>

                <a href="messages.html" class="btn btn--action btn--fixed btn-danger"><i class="zmdi zmdi-plus"></i></a>
            </aside>

            <section class="content content--full">
           
		                   <div class="content__inner">
                                    <h2 style="color: #333333d4;font-weight: 800;text-align: center;">EXTRACTOR</h2>
		   				                    <div class="card">
		   				                        
		   				                        
        <div class="container">
            <div class="table table-responsive">
                <table id="data-table" class="table table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th colspan="2"><h4 style="text-align:center;">Extractores</h4></th>
                        </tr>
                        <tr>
                            <th>PlanObra</th>
                            <th>DetallePlan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a href="excelplan" style="font-size:40px;" class="zmdi zmdi-file-text zmdi-hc-fw"></a>PlanObra</td>
                            <td><a href="download/detalleplan/DetallePlan.xls" style="font-size:40px;" class="zmdi zmdi-file-text zmdi-hc-fw"></a>DetallePlan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        		   				                       
		   				                    </div>
		   				                </div>
                                            
                                            
                                            
		   				                <footer class="footer hidden-xs-down">
		   				                    <p>Â© Material Admin Responsive. All rights reserved.</p>

		   				                    <ul class="nav footer__nav">
		   				                        <a class="nav-link" href="#">Homepage</a>

		   				                        <a class="nav-link" href="#">Company</a>

		   				                        <a class="nav-link" href="#">Support</a>

		   				                        <a class="nav-link" href="#">News</a>

		   				                        <a class="nav-link" href="#">Contacts</a>
		   				                    </ul>
		                   </footer>
            </section>
        </main>

       

        <!-- Javascript -->
        <!-- ..vendors -->
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/tether/dist/js/tether.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/Waves/dist/waves.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/jquery.scrollbar/jquery.scrollbar.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/jquery-scrollLock/jquery-scrollLock.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/Waves/dist/waves.min.js"></script>

        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/flot/jquery.flot.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/flot/jquery.flot.resize.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/flot.curvedlines/curvedLines.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/jqvmap/dist/jquery.vmap.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/jqvmap/dist/maps/jquery.vmap.world.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/salvattore/dist/salvattore.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/jquery.sparkline/jquery.sparkline.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/moment/min/moment.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>

   <!--  tables -->
		<script src="https://www.ing-fix.com/obra2.0/public/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
		<script src="https://www.ing-fix.com/obra2.0/public/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
		<script src="https://www.ing-fix.com/obra2.0/public/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
		<script src="https://www.ing-fix.com/obra2.0/public/bower_components/jszip/dist/jszip.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>

        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/dropzone/dist/min/dropzone.min.js"></script>
        <!-- Charts and maps-->
        <script src="https://www.ing-fix.com/obra2.0/public/demo/js/flot-charts/curved-line.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/demo/js/flot-charts/line.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/demo/js/flot-charts/chart-tooltips.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/demo/js/other-charts.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/demo/js/jqvmap.js"></script>
        
        <!-- App functions and actions -->
        <script src="https://www.ing-fix.com/obra2.0/public/js/app.min.js"></script>
        
        <!--  -->
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/notify/pnotify.custom.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/bower_components/select2/dist/js/select2.full.min.js"></script>
        <script src="https://www.ing-fix.com/obra2.0/public/js/Utils.js"></script>
        <script type="text/javascript">
        
        
                
        function asignarGrafo(component){


        	swal({
                title: 'EstÃ¡ seguro de actualizar el estado a 01?',
                text: 'Asegurese de validar la informaciÃ³n seleccionada!',
                type: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonClass: 'btn btn-primary',
                confirmButtonText: 'Si, actualizar estado!',
                cancelButtonClass: 'btn btn-secondary'
            }).then(function(){

            	var id_ptr = $(component).attr('data-ptr');
             	var grafo = $(component).attr('data-grafo');

             	var subProy = $.trim($('#selectSubProy').val()); 
             	var eecc = $.trim($('#selectEECC').val()); 
             	var zonal = $.trim($('#selectZonal').val()); 
             	var item = $.trim($('#selectHasItemPlan').val()); 
             	var mes = $.trim($('#selectMesEjec').val()); 
             	var area = $.trim($('#selectArea').val()); 
             	             	
         	    $.ajax({
         	    	type	:	'POST',
         	    	'url'	:	'updtTo01',
         	    	data	:	{id_ptr	:	id_ptr,
          	    	             grafo : grafo,
            	    	           subProy : subProy,
            	    	           eecc : eecc,
            	    	           zonal : zonal,
            	    	           item : item,
            	    	           mes : mes,
            	    	           area : area},
         	    	'async'	:	false
         	    })
         	    .done(function(data){             	    
         	    	var data	=	JSON.parse(data);
         	    	if(data.error == 0){
             	    	          	    	   
         	    		mostrarNotificacion('success','OperaciÃ³n Ã©xitosa.',data.msj);
         	    		$('#contTabla').html(data.tablaAsigGrafo)
           	    	    initDataTable('#data-table');
         			}else if(data.error == 1){
         				
         				mostrarNotificacion('error','Error el asociar Grafo',data.msj);
         			}
         		  })
         		  .fail(function(jqXHR, textStatus, errorThrown) {
         		     mostrarNotificacion('error','Error al insertar',errorThrown+ '. Estado: '+textStatus);
         		  })
         		  .always(function() {
         	  	 
         		});
         	   
            });            
          	 
        }

        function filtrarTabla(){
     	     var subProy = $.trim($('#selectSubProy').val()); 
           	 var eecc = $.trim($('#selectEECC').val()); 
           	 var zonal = $.trim($('#selectZonal').val()); 
            	var item = $.trim($('#selectHasItemPlan').val()); 
             	var mes = $.trim($('#selectMesEjec').val()); 
             	var area = $.trim($('#selectArea').val()); 
             	
       	    $.ajax({
       	    	type	:	'POST',
       	    	'url'	:	'getDataTablePre',
       	    	data	:	{subProy  :	subProy,
               	    		eecc      : eecc,
            	    	    zonal     : zonal,
         	    	           item : item,
        	    	           mes : mes,
        	    	           area : area},
       	    	'async'	:	false
       	    })
       	    .done(function(data){
       	    	var data	=	JSON.parse(data);
       	    	if(data.error == 0){           	    	          	    	   
       	    		$('#contTabla').html(data.tablaAsigGrafo)
       	    	    initDataTable('#data-table');
       	    		
       			}else if(data.error == 1){
       				
       				mostrarNotificacion('error','Hubo problemas al filtrar los datos!');
       			}
       		  });
        }
            
        function recogePep(){
            console.log('ok');
            var pep1 = $.trim($('#pep1').val());
            var pep2 = $.trim($('#pep2').val());
            
            console.log(pep1);
            console.log(pep2);
            
            $.ajax({
         	    	type	:	'POST',
         	    	'url'	:	'getPep',
         	    	data	:	{pep1	:	pep1,
          	    	             pep2 : pep2
            	    	           },
         	    	'async'	:	false
         	    })
            
            
        }
            function getPepEdit(component){
                var pep1Edit = $(component).attr('data-pep1');
                var pep2Edit = $(component).attr('data-pep2');
                var id_relacion = $(component).attr('data-id_relacion');
                
                $('#pep1Edit').attr('data-pep1Edit',pep1Edit);
                
                
            }

        </script>
        <script type="text/javascript">
        $(document).ready(function() {
        $('#refresh').click(function() {
            // Recargo la pÃ¡gina
            window.setTimeout('location.reload()', 700);
            alertify.success("Insertado Correctamente");
        });
        });
        </script>
        
        <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.11.0/build/alertify.min.js"></script>
        <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.0/build/css/alertify.min.css"/>
        <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.0/build/css/themes/bootstrap.min.css"/>
    </body>

<!-- Mirrored from byrushan.com/projects/material-admin/app/2.0/jquery/bs4/hidden-sidebar.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 07 Jul 2017 17:16:44 GMT -->
</html>