function initDataTable (id_tabla,nro_column_fix = null) {
	var jsonTB = {
					//responsive: true, // not compatible
					lengthChange: false,
					scrollY: 400,
					scrollX: true,
					scrollCollapse: true,
					order: [],
					// paging: false,
					fixedColumns: (nro_column_fix != null ? true : false),
					// fixedColumns:
					// {
					//     leftColumns: 2
					// },
					language: {
						searchPlaceholder: 'Buscar registros...',
						sProcessing: 'Procesando...',
						sZeroRecords: 'No se encontraron resultados',
						sEmptyTable: 'Ning\u00fan dato disponible en esta tabla',
						sInfoEmpty: 'Mostrando 0 de 0 de un total de 0 registros',
						sLoadingRecords: 'Cargando...',
						sInfo: 'Mostrando _START_ de _END_ de un total de _TOTAL_ registros'
					},
					dom:
						"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
							buttons: [
								{
									extend: 'colvis',
									text: 'Visibilidad de Columna',
									titleAttr: 'Visualizar Columna',
									className: 'btn-outline-default btn-sm'
								}, 
								{
									extend: 'excelHtml5',
									text: 'Excel',
									titleAttr: 'Generar Excel',
									className: 'btn-outline-success btn-sm mr-1',
									charset: 'UTF-8',
									bom: true
								}
							]
				};
	if(nro_column_fix != null && nro_column_fix != '' && nro_column_fix != undefined){
		jsonTB['fixedColumns'] = {
			leftColumns: nro_column_fix
		};
	}

	var tabla = $('#'+id_tabla).dataTable(jsonTB);
	return tabla;
}

function modal(idModal) {
    $('#' + idModal).modal('toggle');
}

function soloDigitos(clase) {
    //console.log('.......soloDigitos.......:'+"."+clase);
    $("." + clase).keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                        // Allow: home, end, left, right, down, up
                                (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

}

function seguridad_clave(clave) {
    var seguridad = 0;
    if (clave.length != 0) {
        if (tiene_numeros(clave) && tiene_letras(clave)) {
            seguridad += 30;
        }
        if (tiene_minusculas(clave) && tiene_mayusculas(clave)) {
            seguridad += 30;
        }
        if (clave.length >= 4 && clave.length <= 5) {
            seguridad += 10;
        } else {
            if (clave.length >= 6 && clave.length <= 8) {
                seguridad += 30;
            } else {
                if (clave.length > 8) {
                    seguridad += 50;
                }
            }
        }
    }
    return seguridad
}

function tiene_numeros(texto) {
    var numeros = "0123456789";

    for (i = 0; i < texto.length; i++) {
        if (numeros.indexOf(texto.charAt(i), 0) != -1) {
            return 1;
        }
    }
    return 0;
}

function tiene_letras(texto) {
    texto = texto.toLowerCase();
    var letras = "abcdefghyjklmnñopqrstuvwxyz";

    for (i = 0; i < texto.length; i++) {
        if (letras.indexOf(texto.charAt(i), 0) != -1) {
            return 1;
        }
    }
    return 0;
}

function tiene_minusculas(texto) {
    var letras = "abcdefghyjklmnñopqrstuvwxyz";

    for (i = 0; i < texto.length; i++) {
        if (letras.indexOf(texto.charAt(i), 0) != -1) {
            return 1;
        }
    }
    return 0;
}

function tiene_mayusculas(texto) {
    var letras_mayusculas = "ABCDEFGHYJKLMNÑOPQRSTUVWXYZ";
    for (i = 0; i < texto.length; i++) {
        if (letras_mayusculas.indexOf(texto.charAt(i), 0) != -1) {
            return 1;
        }
    }
    return 0;
}

function soloDecimal(idComponente) {

    $("input[id*='" + idComponente + "']").keydown(function (event) {


        if (event.shiftKey == true) {
            event.preventDefault();
        }

        if ((event.keyCode >= 48 && event.keyCode <= 57) ||
                (event.keyCode >= 96 && event.keyCode <= 105) ||
                event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
                event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

        } else {
            event.preventDefault();
        }

        if ($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
            event.preventDefault();
        //if a decimal has been added, disable the "."-button

    });
}

function openModalNoticias(btn) {
    // modal('modalPDF');
    var url_noticia = btn.data('direccion');
    console.log(url_noticia);
    var htmlDataNoticia = "<div class='pdf'><object id='objPDF' data='" + url_noticia + "'></object></div>";
    $('#doc_declaracion').html(htmlDataNoticia);
}

function canCreateEditPOByCostoUnitario(jsonCreateSol, callback){
		let dataFile = new FormData();
		if(jsonCreateSol.idEstacion == 1) {
			callback();
			return;
		}

		 $.ajax({
	            type: 'POST',
	            url: 'regPoByCU',
	            data: {
	            	origen		: jsonCreateSol.origen,//1= CREACION PO MAT, 2 = CREACION PO MO, 3 = GESTION VR MAT, 4 = LIQUIDACION MO
					tipo_po 	: jsonCreateSol.tipo_po_dato,//1 = MATERIAL; 2 = MO
	            	accion  	: jsonCreateSol.accion_dato,//1 = NUEVA PO; 2 = EDITAR PO
	            	codigo_po 	: jsonCreateSol.codigo_po_dato,//NUEVA PO = NULL, EDITAR PO = 'CODIGO_PO'
	                itemplan	: jsonCreateSol.itemplan_dato,//ITEMPLAN
	                costoTotalPo: jsonCreateSol.costoTotalPo_dato //COSTO TOTAL DE LA PO
	            }
	        }).done(function (data) {
	        	data = JSON.parse(data);
	        	if (data.error == 0) {
	        		callback();
	        	}else if(data.error == 1){
	        		if(data.canGenSoli == 0){
	        			var costo_actual = data.costo_actual;
	        			var excedente 	 = data.excedente;
	        			var costo_final  = data.costo_final;

						console.log('costo_actual:'+costo_actual);
						console.log('excedente:'+excedente);
						console.log('costo_final:'+costo_final);
						console.log('data_json:'+jsonCreateSol.data_json);
						
					swal.fire({
	            	        title: 'No se pudo procesar la Solicitud',
	            	        // text: data.msj,
							html : '<div class="form-group"><a>'+data.msj+'</a></div>'+
									'<div class="form-group">'+
										'<label style="color:red">SUBIR EVIDENCIA EXCESO</label>'+
										'<input type="file" name="archivo" id="archivoFile">'+
									'</div>'+
									'<div class="form-group">'+
										'<textarea class="col-md-12 form-control" placeholder="Ingresar Comentario..." style="height:80px;background:#F9F8CF" id="comentarioText"></textarea>'+
									'</div>',
	            	        type: 'warning',
	            	        showCancelButton: true,
	            	        buttonsStyling: false,
	            	        confirmButtonClass: 'btn btn-primary',
	            	        confirmButtonText: 'Si, generar Solicitud!',
	            	        cancelButtonClass: 'btn btn-secondary',
	            	        allowOutsideClick: false
	            	    }).then(function(){//falta codigo que genera la solicitud...
							var comentario = $('#comentarioText').val();

							var fileArchivo = $('#archivoFile')[0].files[0];

							dataFile.append('origen', jsonCreateSol.origen);
							dataFile.append('itemplan', jsonCreateSol.itemplan_dato);
							dataFile.append('tipo_po', jsonCreateSol.tipo_po_dato);
							dataFile.append('costo_inicial', costo_actual);
							dataFile.append('exceso_solicitado', excedente);
							dataFile.append('costo_final', costo_final);
							dataFile.append('codigo_po', jsonCreateSol.codigo_po_dato);
							dataFile.append('comentario', comentario);
							dataFile.append('idEstacion', jsonCreateSol.idEstacion);
							dataFile.append('file', fileArchivo);
							dataFile.append('data_json', JSON.stringify(jsonCreateSol.data_json));

							 

	            	        $.ajax({
								data: dataFile,
	            	            type: 'POST',
	            	            url: 'genSolExce',
								cache: false,
								contentType: false,
								processData: false
	            	        }).done(function (data) {
	            	        	data = JSON.parse(data);
	            	        	if (data.error == 0) {
	            	        		swal.fire({
	                                    title: 'Se realizo la Operacion!',
	                                    text: 'Asegurese de validar la informacion!',
	                                    type: 'success',
	                                    buttonsStyling: false,
	                                    confirmButtonClass: 'btn btn-primary',
	                                    confirmButtonText: 'OK!',
	                                    allowOutsideClick: false
	                                }).then(function(){
										console.log('cerrar ventana!!');
	                                	window.close(); 
	                                });	                                
	            	        	}else if(data.error == 1){
	            	        		mostrarNotificacion(1, 'warning', data.msj);
	            	        	}
	            	        	 
	            	        });
	            	    }, function(dismiss) {
	            	       console.log('cancelar.');
	            	    });
						
	        		}else{console.log('canCreateEditPOByCostoUnitario:0009');
	        			//swal(1,"Mensaje Informativo", data.msj , "warning");
						swal.fire("Mensaje Informativo", data.msj , "warning");
	        		}        		
	        	}
	        });
	}
	
	function formatearNumeroComas(numeroFormat){
    	var format_monto_final =  Number(numeroFormat).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return format_monto_final;
    }
	
	/*********nuevo CZAVALA	29.04.2020*******/
	function showMessageIsObraCerrada(id_estado_plan){
		var estadoplan = '';
		if(id_estado_plan	==	5){
			estadoplan = 'CERRADO';
		}else if(id_estado_plan	==	6){
			estadoplan = 'CANCELADO';
		}
		swal({
			title: 'ACCION BLOQUEADA!',
			text: 'La obra se encuentra en estado "'+estadoplan+'", las gestiones han sido finalizadas.',
			type: 'warning',
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			confirmButtonText: 'OK!',
			allowOutsideClick: false
		}).then(function(){
			window.close(); 
		}); 	
	}
	
	function deleteOtActualizacion(component){
     	var itemplan = $(component).attr('data-itemplan');
     	swal({
             title: 'Est&aacute; seguro de eliminar la OT de Actualizacion?',
             text: 'Asegurese de validar la Informacion.',
             type: 'warning',        		
             showCancelButton: true,
             buttonsStyling: false,
             confirmButtonClass: 'btn btn-primary',
             confirmButtonText: 'Si, eliminar OT de Actualizacion!',
             cancelButtonClass: 'btn btn-secondary',
             allowOutsideClick: false
         }).then(function(){
         	$.ajax({
                 type: 'POST',
                 url: 'delOTAC',
                 data: {
                 	itemplan: itemplan
                 }
             }).done(function (data) {
                 data = JSON.parse(data);
                 if(data.error == 0){
                 	swal({
							title: 'Se Elimino la OT ' + itemplan + 'AC',
							text: 'Asegurese de validar la informacion!',
							type: 'success',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'OK!'
						}).then(function () {
							filtrarTabla();
						});                        
                 }else if(data.error == 1){
                 	mostrarNotificacion('warning','No se pudo Elimnar la OT AC',data.msj);
                 }
             });
         })
     }
	
	function delOtPrincipal(component){
     	var itemplan = $(component).attr('data-itemplan');
     	swal({
             title: 'Est&aacute; seguro de eliminar la OT Principal?',
             text: 'Asegurese de validar la Informacion.',
             type: 'warning',        		
             showCancelButton: true,
             buttonsStyling: false,
             confirmButtonClass: 'btn btn-primary',
             confirmButtonText: 'Si, eliminar OT Principal!',
             cancelButtonClass: 'btn btn-secondary',
             allowOutsideClick: false
         }).then(function(){
         	$.ajax({
                 type: 'POST',
                 url: 'delOtPrincipal',
                 data: {
                 	itemplan: itemplan
                 }
             }).done(function (data) {
                 data = JSON.parse(data);
                 if(data.error == 0){
                 	swal({
							title: 'Se Elimino la OT ' + data.ot_principal,
							text: 'Asegurese de validar la informacion!',
							type: 'success',
							buttonsStyling: false,
							confirmButtonClass: 'btn btn-primary',
							confirmButtonText: 'OK!'
						}).then(function () {
							filtrarTabla();
						});                        
                 }else if(data.error == 1){
                 	mostrarNotificacion('warning','No se pudo Elimnar la OT Principal',data.msj);
                 }
             });
         })
	}
		
	 function mostrarNotificacionHTML(tipo, titulo, mensaje) {
			 
		    swal({
		        title: titulo,
		        html: mensaje,
		        type: tipo
		    });
		}
		
	function soloLetras(e) {
		var key = e.keyCode || e.which,
		tecla = String.fromCharCode(key).toLowerCase(),
		letras = " áéíóúabcdefghijklmnñopqrstuvwxyz,;",
		especiales = [8, 37, 39, 46],
		tecla_especial = false;

		for (var i in especiales) {
			if (key == especiales[i]) {
				tecla_especial = true;
				break;
			}
		}

		if (letras.indexOf(tecla) == -1 && !tecla_especial) {
			return false;
		}
	}
	
	function __getCodigoCercano(arrayData, latitud, longitud) {
		var distanciaMenor = null;
		var codigo = null;
		json_data_return = {};
		arrayData.forEach(function(data){
			var distancia = _ditanciaVicenty(latitud, longitud, data.latitud, data.longitud);
			
			if(distanciaMenor == null) {
				distanciaMenor = distancia;
			}
				
			if(distanciaMenor > distancia) {
				distanciaMenor = distancia;
				codigo         = data.codigo;
			}

			json_data_return.distancia = distanciaMenor;
			json_data_return.codigo    = codigo;
		});
		
		return json_data_return;
	}
	
	function _ditanciaVicenty(latitudeFrom, longitudeFrom, latitudeTo, longitudeTo, earthRadius = 6371000) {
		// Se convierte a radianes
		
        var latFrom = (parseFloat(latitudeFrom)) * Math.PI / 180;
        var lonFrom = (parseFloat(longitudeFrom))* Math.PI / 180;
        var latTo   = (parseFloat(latitudeTo)) * Math.PI / 180;
        var lonTo   = (parseFloat(longitudeTo))* Math.PI / 180;
      
        var lonDelta = lonTo - lonFrom;
        var a = Math.pow(Math.cos(latTo) * Math.sin(lonDelta), 2) +
             Math.pow(Math.cos(latFrom) * Math.sin(latTo) - Math.sin(latFrom) * Math.cos(latTo) * Math.cos(lonDelta), 2);
        var b = Math.sin(latFrom) * Math.sin(latTo) + Math.cos(latFrom) * Math.cos(latTo) * Math.cos(lonDelta);
      
        angle = Math.atan2(Math.sqrt(a), b);
        return angle * earthRadius;
    }
	
	function dataCentralByCoord(latitud, longitud) {
		$.ajax({
			type : 'POST',
			url  : "getCentralCoordPqt",
			data : { latitud  : latitud,
			         longitud : longitud }
			
		}).done(function (data) {
			$('body').loading('destroy')
			data = JSON.parse(data);
			console.log(data.dataCentral);
			return data.dataCentral;
		});
		console.log("ENRR");
	}
	
	function openModalNoticiasDiptico(btn) {
		// modal('modalPDF');
		var url_noticia = btn.data('direccion');
		console.log(url_noticia);
		var htmlDataNoticia = "<div class='pdf'><object id='objPDF' data='" + url_noticia + "'></object></div>";
		$('#doc_declaracion_diptico').html(htmlDataNoticia);
	}
	
	

	function getDistanciaArecta(lon1, lat1, lon2, lat2, lon3, lat3) {
		lat1 = toRad(lat1);
		lat2 = toRad(lat2);
		lat3 = toRad(lat3);
		lon1 = toRad(lon1);
		lon2 = toRad(lon2);
		lon3 = toRad(lon3);
	
		// Radio de la Tierra en metros
		var R = 6371000;
	
		// Requisitos previos para las fórmulas
		var bear12 = bear(lat1, lon1, lat2, lon2);
		var bear13 = bear(lat1, lon1, lat3, lon3);
		var dis13  = dis(lat1, lon1, lat3, lon3);
	
		// ¿Es obtuso el rumbo relativo?
		if (Math.abs(bear13 - bear12) > (Math.PI / 2))
			return dis13;
	
		// Encuentra la distancia entre vías.
		var dxt = Math.asin(Math.sin(dis13 / R) * Math.sin(bear13 - bear12)) * R;
	
		// ¿Está p4 más allá del arco?
		var dis12 = dis(lat1, lon1, lat2, lon2);
		var dis14 = Math.acos(Math.cos(dis13 / R) / Math.cos(dxt / R)) * R;
		if (dis14 > dis12)
			return dis(lat2, lon2, lat3, lon3);
		return Math.abs(dxt);
	}
	
	function dis(latA, lonA, latB, lonB) {
		var R = 6371000;
		return Math.acos(Math.sin(latA) * Math.sin(latB) + Math.cos(latA) * Math.cos(latB) * Math.cos(lonB - lonA)) * R;
	}
	
	function bear(latA, lonA, latB, lonB) {
		// Encuentra el rumbo de un punto de latitud / longitud a otro.
		return Math.atan2(Math.sin(lonB - lonA) * Math.cos(latB), Math.cos(latA) * Math.sin(latB) - Math.sin(latA) * Math.cos(latB) * Math.cos(lonB - lonA));
	}
	
	function toRad(Value) {
		/** Converts numeric degrees to radians */
		return Value * Math.PI / 180;
	}

	function dataCentralByCoord(latitud, longitud) {
		$.ajax({
			type : 'POST',
			url  : "getCentralCoordPqt",
			data : { latitud  : latitud,
			         longitud : longitud }
			
		}).done(function (data) {
			$('body').loading('destroy')
			data = JSON.parse(data);
			console.log(data.dataCentral);
			return data.dataCentral;
		});
	}
	
	
	function ordenarAscArray(arrayData) {
		var arrayPosteCamino = arrayData.sort(function(a, b) {
			return parseFloat(a.distancia) - parseFloat(b.distancia);
		});
		
		return arrayPosteCamino;
	}

	function getCmbSubProyectoByProyecto() {
		var idProyecto = $('#cmbProyecto option:selected').val();
		console.log("DIPOR: "+idProyecto);
		if(idProyecto == null || idProyecto == '') {
			return;
		}

		$.ajax({
			type : 'POST',
			url  : 'getSubProyectoByProyecto',
			data : { idProyecto : idProyecto }
		}).done(function(data){
			data = JSON.parse(data);

			if(data.error == 0) {
				$('#cmbSubProyecto').html(data.cmbSubProyecto);
			}
		});
	}

	/************METODOS GOOGLE MAP**************/
	var marker = null;
	var map = null;
	var center = null;
	
	function init() {
		 
		infoWindow = new google.maps.InfoWindow();
		var geocoder = new google.maps.Geocoder();
		//var map;
		var latitude = -12.0965634; // YOUR LATITUDE VALUE
		var longitude = -77.0276785; // YOUR LONGITUDE VALUE


		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				latitude = position.coords.latitude;
				longitude = position.coords.longitude;
			});
		}
		
		var myLatLng = {lat: latitude, lng: longitude};
		
		map = new google.maps.Map(document.getElementById('divMapCoordenadas'), {
		center: myLatLng,
		zoom: 14
		});

	 
		
		// Create the search box and link it to the UI element.
		var input = document.getElementById('pac-input');
		var searchBox = new google.maps.places.SearchBox(input);
		map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
		// Bias the SearchBox results towards current map's viewport.
		map.addListener('bounds_changed', function() {
		searchBox.setBounds(map.getBounds());
		});

		// Listen for the event fired when the user selects a prediction and retrieve
		// more details for that place.
		searchBox.addListener('places_changed', function() {
		var places = searchBox.getPlaces();

		if (places.length == 0) {
			return;
		}

		// For each place, get the icon, name and location.
		var bounds = new google.maps.LatLngBounds();
		places.forEach(function(place) {
			
			if (place.geometry.viewport) {
			// Only geocodes have viewport.
			bounds.union(place.geometry.viewport);
			} else {
			bounds.extend(place.geometry.location);
			}
		});
		map.fitBounds(bounds);
		});
		
		// Update lat/long value of div when anywhere in the map is clicked    
		google.maps.event.addListener(map,'click',function(event) {
			myLatLng = [event.latLng.lng(),event.latLng.lat()];

			$("#inputCoordX").val(event.latLng.lat());
			$("#inputCoordY").val(event.latLng.lng());

			$.ajax({
					type: 'POST',
					url : 'getDataCentralByCoordenadas',
					data: { 
							latitud : event.latLng.lat(),
							longitud : event.latLng.lng()  
						  }
					}).done(function(data) {  
							data = JSON.parse(data);
							if(data.error == 0){
								// $('#cmbCentral').val(data.idCentral).trigger('change');
								//$('#txtEmpresaColab').val(data.empresaColabDesc);
								$('#cmbEecc').val(data.idEmpresaColab);
								$('#cmbEecc').val(data.idEmpresaColab).trigger('change');
								$('#cmbCentral').val(data.idCentral);
								$('#cmbCentral').val(data.idCentral).trigger('change');
								$('#txtLongitud').val(event.latLng.lng());
								$('#txtLatitud').val(event.latLng.lat());
								$('#txtEmpresaColab').val(data.empresaColabDesc);//PARA IP MADRES
								$('#txtDepa').val(data.departamento);//PARA IP MADRES
								$('#txtProv').val(data.provincia);//PARA IP MADRES
								$('#txtDis').val(data.distrito);//PARA IP MADRES
								$('#txtCodigoCentral').val(data.codigoCentral);//PARA IP MADRES
								$('#cmbInversion').html(data.cmbInversion);

								objetoDataRegistro.idEmpresaColab = data.idEmpresaColab;
								objetoDataRegistro.idZonal        = data.idZonal;
								objetoDataRegistro.idCentral      = data.idCentral;
								console.log('1');
								//changueCentral();
							}else if(data.error == 1){
								mostrarNotificacion('error','Error','No se inserto el Plan de obra:'+data.msj);
							}
					})
			.fail(function(jqXHR, textStatus, errorThrown) {
				console.log("ERRORRRRR");
				// mostrarNotificacion('error','Error','Comuniquese con alguna persona a cargo :');
			})
			.always(function() {
				console.log("always");
			});
				
		});
		
		//var marker;
		
		// Create new marker on double click event on the map
		google.maps.event.addListener(map,'click',function(event) {
			console.log('2');
			if ( marker ) {
				marker.setPosition(event.latLng);
			}else{
				marker = new google.maps.Marker({
					position: event.latLng, 
					map: map, 
					title: event.latLng.lat()+', '+event.latLng.lng(),
					draggable: true,
					animation: google.maps.Animation.DROP
				});
			}
			
			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {                    			
							var pos = marker.getPosition();
							// llenarTextosByCoordenadas(results,pos)
							var address=results[0]['formatted_address'];
							openInfoWindowAddress(address,marker);
					}
				});	
				var pos = marker.getPosition();
				map.setCenter(new google.maps.LatLng(pos.lat(),pos.lng())); 
				
			google.maps.event.addListener(marker, 'dragend', function(){
				var pos = marker.getPosition();
				geocoder.geocode({'latLng': pos}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {                        			
							// llenarTextosByCoordenadas(results,pos)
							var address=results[0]['formatted_address'];
							openInfoWindowAddress(address,marker);				
						}
				});
				map.setCenter(new google.maps.LatLng(pos.lat(),pos.lng()));        	
			});
					
		});
	}


	function openInfoWindowAddress(Addres,marker) {
		infoWindow.setContent([
			 Addres
		 ].join(''));
		 infoWindow.open(map, marker);
	}


	var controls = {
		leftArrow: '<i class="fal fa-angle-left" style="font-size: 1.25rem"></i>',
		rightArrow: '<i class="fal fa-angle-right" style="font-size: 1.25rem"></i>'
	}

	$('.date_picker').datepicker(
	{
		orientation: "bottom right",
		todayHighlight: true,
		templates: controls,
		format: 'dd-mm-yyyy'
	});


	// function initDataTable(id_tabla) {
	// 	$(id_tabla).DataTable({
	// 		autoWidth: false,
	// 		responsive: false,
	// 		aaSorting: [],
	// 		lengthMenu: [[15, 30, 45, -1], ["15 Rows", "30 Rows", "45 Rows", "Everything"]],
	// 		language: {searchPlaceholder: "Search for records..."},
	// 		dom: "Blfrtip",
	// 		buttons: [{extend: "excelHtml5", title: "Export Data"},
	// 			{extend: "csvHtml5", title: "Export Data"},
	// 			{extend: "print", title: "Print"}],
	// 		initComplete: function (a, b) {
	// 			$(this).closest(".dataTables_wrapper").prepend('<div class="dataTables_buttons hidden-sm-down actions"><span class="actions__item zmdi zmdi-print" data-table-action="print" /><span class="actions__item zmdi zmdi-fullscreen" data-table-action="fullscreen" /><div class="dropdown actions__item"><i data-toggle="dropdown" class="zmdi zmdi-download" /><ul class="dropdown-menu dropdown-menu-right"><a href="" class="dropdown-item" data-table-action="excel">Excel (.xlsx)</a><a href="" class="dropdown-item" data-table-action="csv">CSV (.csv)</a></ul></div></div>')
	// 		}
	// 	});
	// }

	function initDataTableRow (id_tabla,nro_column_fix = null) {
		var jsonTB = {
						//responsive: true, // not compatible
						scrollY: 400,
						scrollX: true,
						scrollCollapse: true,
						lengthMenu: [[4, 30, 45, -1], ["15 Rows", "30 Rows", "45 Rows", "Everything"]],
						// paging: false,
						order: [],
						fixedColumns: (nro_column_fix != null ? true : false),
						
						// fixedColumns:
						// {
						//     leftColumns: 2
						// },
						language: {
							searchPlaceholder: 'Buscar registros...',
							sProcessing: 'Procesando...',
							sZeroRecords: 'No se encontraron resultados',
							sEmptyTable: 'Ning\u00fan dato disponible en esta tabla',
							sInfoEmpty: 'Mostrando 0 de 0 de un total de 0 registros',
							sLoadingRecords: 'Cargando...',
							sInfo: 'Mostrando _START_ de _END_ de un total de _TOTAL_ registros'
						},
						dom:
								"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
								"<'row'<'col-sm-12'tr>>" +
								"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [									
									{
										extend: 'excelHtml5',
										text: 'Excel',
										titleAttr: 'Generar Excel',
										className: 'btn-outline-success btn-sm mr-1',
										charset: 'UTF-8',
										bom: true
									}
								]
					};
		if(nro_column_fix != null && nro_column_fix != '' && nro_column_fix != undefined){
			jsonTB['fixedColumns'] = {
				leftColumns: nro_column_fix
			};
		}

		var tabla = $('#'+id_tabla).dataTable(jsonTB);
		return tabla;
	}


	function mostrarNotificacionRecargar(tipo, titulo, mensaje) {
		Swal.fire({
			icon: tipo,
			title: titulo,
			text : mensaje,
			showConfirmButton: true,
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				window.location.reload();
			}
		});
	}

	function initDataTableModal(idModal, idTabla) {
		$('#'+idModal).on('shown.bs.modal', function(){ 
			initDataTableRow(idTabla);
		});
	}
	
	function mostrarNotificacion(flg, tipo, titulo, mensaje) {
		if(flg == 1) {
			Swal.fire({
				icon : tipo,
				title: titulo,
				text : mensaje,
				showConfirmButton: true
			});
		} else if(flg == 2) {
			Swal.fire({
				icon: tipo,
				title: titulo,
				text : mensaje,
				showConfirmButton: true,
				allowOutsideClick: false
			}).then((result) => {
				if (result.value) {
					window.location.reload();
				}
			});
		} else if(flg == 3) {
			return Swal.fire({
						title: titulo,
						icon : 'question',
						text : mensaje,
						showDenyButton: true,
						showCancelButton: true,
						confirmButtonText: 'Si',
						cancelButtonText: 'NO',
						allowOutsideClick: false
					});
		} else if(flg == 4) {

			Swal.fire({
				icon: tipo,
				title: titulo,
				text : mensaje,
				showConfirmButton: true,
				allowOutsideClick: false
			}).then((result) => {
				if (result.value) {
					// window.location.reload();
				}
			});
		}
		
	}

	function mostrarNotificacionConfirmar(tipo, titulo, mensaje) {
		Swal.fire({
			title: 'Do you want to save the changes?',
			icon : 'question',
			text : mensaje,
			showDenyButton: true,
			showCancelButton: true,
			confirmButtonText: 'Si'
		  }).then((result) => {
			/* Read more about isConfirmed, isDenied below */
			if (result.isConfirmed) {
			  Swal.fire('Saved!', '', 'success')
			} else if (result.isDenied) {
			  Swal.fire('Changes are not saved', '', 'info')
			}
		  })
	}
	
	function initDataTableResponsive(id_tabla) {


		//initTableLight

		if ($.fn.DataTable.isDataTable('#'+id_tabla))
    	{
		  console.log("entroooo");
		  $('#'+id_tabla).DataTable().destroy();

		}

		var jsonTB = {
						responsive: true,
						// paging: false,
						fixedColumns: false,
						language: {
							searchPlaceholder: 'Buscar registros...',
							sProcessing: 'Procesando...',
							sZeroRecords: 'No se encontraron resultados',
							sEmptyTable: 'Ning\u00fan dato disponible en esta tabla',
							sInfoEmpty: 'Mostrando 0 de 0 de un total de 0 registros',
							sLoadingRecords: 'Cargando...',
							sInfo: 'Mostrando _START_ de _END_ de un total de _TOTAL_ registros'
						},
						dom:
								"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
								"<'row'<'col-sm-12'tr>>" +
								"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [
									{
										extend: 'pdfHtml5',
										text: 'PDF',
										titleAttr: 'Generar PDF',
										className: 'btn-outline-danger btn-sm mr-1'
									},
									{
										extend: 'excelHtml5',
										text: 'Excel',
										titleAttr: 'Generar Excel',
										className: 'btn-outline-success btn-sm mr-1',
										charset: 'UTF-8',
										bom: true
									},
									{
										extend: 'csvHtml5',
										text: 'CSV',
										titleAttr: 'Generar CSV',
										className: 'btn-outline-success btn-sm mr-1',
										charset: 'UTF-8',
										bom: true
									},
									{
										extend: 'copyHtml5',
										text: 'Copiar',
										titleAttr: 'Copiar al Portapapeles',
										className: 'btn-outline-primary btn-sm mr-1'
									},
									{
										extend: 'print',
										text: 'Imprimir',
										titleAttr: 'Imprimir Tabla',
										className: 'btn-outline-primary btn-sm mr-1'
									}
	
								]
					};
		var tabla = $('#'+id_tabla).dataTable(jsonTB);
		return tabla;
	}

	function initDataTableResponsiveParent(parent, id_tabla) {


		//initTableLight

		if ($.fn.DataTable.isDataTable('#'+id_tabla))
    	{
		  console.log("entroooo");
		  $('#'+id_tabla).DataTable().destroy();

		}

		var jsonTB = {
						responsive: true,
						// paging: false,
						fixedColumns: false,
						language: {
							searchPlaceholder: 'Buscar registros...',
							sProcessing: 'Procesando...',
							sZeroRecords: 'No se encontraron resultados',
							sEmptyTable: 'Ning\u00fan dato disponible en esta tabla',
							sInfoEmpty: 'Mostrando 0 de 0 de un total de 0 registros',
							sLoadingRecords: 'Cargando...',
							sInfo: 'Mostrando _START_ de _END_ de un total de _TOTAL_ registros'
						},
						dom:
								"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
								"<'row'<'col-sm-12'tr>>" +
								"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
								buttons: [
									{
										extend: 'pdfHtml5',
										text: 'PDF',
										titleAttr: 'Generar PDF',
										className: 'btn-outline-danger btn-sm mr-1'
									},
									{
										extend: 'excelHtml5',
										text: 'Excel',
										titleAttr: 'Generar Excel',
										className: 'btn-outline-success btn-sm mr-1',
										charset: 'UTF-8',
										bom: true
									},
									{
										extend: 'csvHtml5',
										text: 'CSV',
										titleAttr: 'Generar CSV',
										className: 'btn-outline-success btn-sm mr-1',
										charset: 'UTF-8',
										bom: true
									},
									{
										extend: 'copyHtml5',
										text: 'Copiar',
										titleAttr: 'Copiar al Portapapeles',
										className: 'btn-outline-primary btn-sm mr-1'
									},
									{
										extend: 'print',
										text: 'Imprimir',
										titleAttr: 'Imprimir Tabla',
										className: 'btn-outline-primary btn-sm mr-1'
									}
	
								]
					};
		var tabla = $('#'+id_tabla).dataTable(jsonTB);
		return tabla;
	}

	function initTableLight(ejemplo_json){

		//initTableLight

		if ($.fn.DataTable.isDataTable('#dt-basic-example'))
    	{
		  $('#dt-basic-example').DataTable().destroy();
		} 
                
		table   =  $('#dt-basic-example').DataTable(
			{   
				"data": ejemplo_json,
				"fnDrawCallback": function( oSettings ) {
					$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
				},
				responsive: true,
				dom:"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
					"<'row'<'col-sm-12'tr>>" +
					"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				buttons: [   
					{
						extend: 'colvis',
						text: 'Visibilidad Columnas',
						titleAttr: 'Visibilidad Columnas',
						className: 'btn-outline-default'
					},
					{
							extend: 'excelHtml5',
							text: 'Exportar Excel',
							titleAttr: 'Exportar Excel',
							className: 'btn-outline-success btn-sm mr-1',
							charset: 'UTF-8',
							bom: true
						}                       
					
				] 
			});
	}

	function reInitTableLight(ejemplo_json){
		table.destroy();
		table = $('#dt-basic-example').DataTable(
				{   
					"data": ejemplo_json,
					"fnDrawCallback": function( oSettings ) {
						$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
					},
					responsive: true,
					dom:"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
					buttons: [   
						{
							extend: 'colvis',
							text: 'Visibilidad Columnas',
							titleAttr: 'Visibilidad Columnas',
							className: 'btn-outline-default'
						},
						{
								extend: 'excelHtml5',
								text: 'Exportar Excel',
								titleAttr: 'Exportar Excel',
								className: 'btn-outline-success btn-sm mr-1',
								charset: 'UTF-8',
								bom: true
							}                       
						
					] 
				});
	}

	function initExistDataTableLight(idTable){	  
		table = $('#'+idTable).DataTable(
				{    
					"fnDrawCallback": function( oSettings ) {
						$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
					},
					responsive: true,
					dom:"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
						"<'row'<'col-sm-12'tr>>" +
						"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
					buttons: [   
						{
							extend: 'colvis',
							text: 'Visibilidad Columnas',
							titleAttr: 'Visibilidad Columnas',
							className: 'btn-outline-default'
						},
						{
								extend: 'excelHtml5',
								text: 'Exportar Excel',
								titleAttr: 'Exportar Excel',
								className: 'btn-outline-success btn-sm mr-1',
								charset: 'UTF-8',
								bom: true
							}                       
						
					] 
				});
	}

	function updatePass(){
		var begood = false;
		var newPass = $.trim($('#txtNewPass').val());
		var mensaje = 'Accion Invalida.';
		if(newPass.length == 0){
			mensaje	=	'Por favor ingrese nueva clave.';
		}else if(newPass.length < 6){
			mensaje	=	'Nueva clave debe ser mayor o igual a 6 caracteres.';
		}else if(newPass.length > 12){
			mensaje	=	'Nueva clave debe ser menor o igual a 12 caracteres.';
		}else if(newPass.length >= 6 && newPass.length <=12){//OK
			begood = true;

			$.ajax({
				type : 'POST',
				url  : 'updPass',
				data : { newPass : newPass}
			}).done(function(data) {				   
				data = JSON.parse(data);		
				if(data.error == 0) {					
					mostrarNotificacion(2, 'success', data.msj, 'Correcto');
					$('#txtNewPass').val('');
				} else {
					mostrarNotificacion(1, 'error', data.msj, 'Incorrecto');
				}
			});
		}		

		if(!begood){
			mostrarNotificacion(1, 'error', mensaje, 'Incorrecto');
		}
	}
 
	

	$('#v-tabs-tab .nav-link').click(function()
	{
		var _this 	= $(this);
		var section = _this.attr('data-section');
   		$('.nav-link').removeClass('active');   // it remove all the active links
   		$(this).addClass('active');    // it adds active class to the current link you have opened

   		$(".tab-pane").removeClass('show');
   		$(".tab-pane").removeClass('active');
   		$(".tab-pane").addClass('d-none');
   		

   		$(".tab-pane."+section).addClass("show");
   		$(".tab-pane."+section).addClass("active");
   		$(".tab-pane."+section).removeClass("d-none");


   		if (section == 'section-1')
   		{
   			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
   			$("#content-diseno-wrap").find('.tab-pane').removeClass('d-none');
   			$("#content-diseno-wrap").find('.tab-pane:first').addClass('fade');
   			$("#content-diseno-wrap").find('.tab-pane:first').addClass('show');
   			$("#content-diseno-wrap").find('.tab-pane:first').addClass('active');
   		}


   		if (section == 'section-2')
   		{
   			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
   			$("#content-economico-wrap").find('.tab-pane').removeClass('d-none');
   			$("#content-economico-wrap").find('.tab-pane:first').addClass('fade');
   			$("#content-economico-wrap").find('.tab-pane:first').addClass('show');
   			$("#content-economico-wrap").find('.tab-pane:first').addClass('active');
   		}


   		if (section == 'section-3')
		{
			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
			$("#content-licencia-wrap").find('.tab-pane').removeClass('d-none');
			$("#content-licencia-wrap").find('.tab-pane:first').addClass('fade');
			$("#content-licencia-wrap").find('.tab-pane:first').addClass('show');
			$("#content-licencia-wrap").find('.tab-pane:first').addClass('active');
		}


   		if (section == 'section-4')
		{
			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
			$("#content-logistica-wrap").find('.tab-pane').removeClass('d-none');
			$("#content-logistica-wrap").find('.tab-pane:first').addClass('fade');
			$("#content-logistica-wrap").find('.tab-pane:first').addClass('show');
			$("#content-logistica-wrap").find('.tab-pane:first').addClass('active');
		}

   		if (section == 'section-5')
		{
			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
			$("#content-pin-wrap").find('.tab-pane').removeClass('d-none');
			$("#content-pin-wrap").find('.tab-pane:first').addClass('fade');
			$("#content-pin-wrap").find('.tab-pane:first').addClass('show');
			$("#content-pin-wrap").find('.tab-pane:first').addClass('active');
		}


		if (section == 'section-6')
		{
			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
			$("#content-censado-wrap").find('.tab-pane').removeClass('d-none');
			$("#content-censado-wrap").find('.tab-pane:first').addClass('fade');
			$("#content-censado-wrap").find('.tab-pane:first').addClass('show');
			$("#content-censado-wrap").find('.tab-pane:first').addClass('active');
		}

		
		if (section == 'section-7')
		{
			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
			$("#content-despliegue-wrap").find('.tab-pane').removeClass('d-none');
			$("#content-despliegue-wrap").find('.tab-pane:first').addClass('fade');
			$("#content-despliegue-wrap").find('.tab-pane:first').addClass('show');
			$("#content-despliegue-wrap").find('.tab-pane:first').addClass('active');
		}

		if (section == 'section-8')
		{
			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
			$("#content-hgu-wrap").find('.tab-pane').removeClass('d-none');
			$("#content-hgu-wrap").find('.tab-pane:first').addClass('fade');
			$("#content-hgu-wrap").find('.tab-pane:first').addClass('show');
			$("#content-hgu-wrap").find('.tab-pane:first').addClass('active');
		}

		if (section == 'section-9')
		{
			$(".tab-pane."+section).find('.nav-link:first').addClass('active');
			$("#content-status-wrap").find('.tab-pane').removeClass('d-none');
			$("#content-status-wrap").find('.tab-pane:first').addClass('fade');
			$("#content-status-wrap").find('.tab-pane:first').addClass('show');
			$("#content-status-wrap").find('.tab-pane:first').addClass('active');
		}




   		//content-diseno-wrap
   		//section

   		$(".section-log").addClass('d-none');
    	console.log("Ocultar");
})