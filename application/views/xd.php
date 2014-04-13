<?
// ------------------------------------------------------------------------
// *** GRABA ESTE FICHERO CAMBIANDO LA EXTENSION .txt POR .PHP  y LISTO
// ------------------------------------------------------------------------
//
// mapas_marker.php: CALCULO/POSICIONAMIENTO DE UN MARCADOR EN UN MAPA DE GOOGLE
// USA LA V3 DEL API DE GOOGLE 
//
// EL MARCADOR SE PUEDE CAMBIAR DE POSICION DE ALGUNA DE ESTAS FORMAS
//
// 	- Arrastrando el marcador que hay en el mapa
//	- Haciendo click en cualquier parte del mapa
//	- Introduciendo LON/LAT y pulsando al bot¢n IR/GO 
//	- Introduciendo una direcci¢n y pulsando el bot¢n IR/GO
//  
// EL BOTON PROCESAR LLAMA A LA RUTINA mapas_marker_procesa
//
// PARAMETROS DE ENTRADA (E_GET)/
//
// lon:  	LONGITUD DONDE POSICIONAR EL MARCADOR INICALMENTE
// lat:   	LATITUD DONDE POSICIONAR EL MARCADOR INICALMENTE
// zoom:      	ZOOM ( Por defecto 9)
// tipo:        TIPO de mapa: ROADMAP, SATELLITE, HYBRID o TERRAIN  (Defecto HYBRID)
// dir: 	DIRECCION DEL MARCADOR INICiAL CON URL ENCODE(si esta dir entonces lat/ no se tiene en cuenta)
//
// SALIDA ($_POST) AL PULSAR EL BOTON  PROCESA se llama a mapas_maracador_procesa
//  
// POR DEFECTO EL MARCADOR SE POSICIONA EN QUINTO (longitud="-0.49647219999997105", latitud= "41.4235091", Zoom= 17)
//
// AUTOR:      MPS MULT Modif Sept. 2012
// 	   	 
// ------------------------------------------------------------------------

// INICIALIZO LAS VARIABLES 
$latitud= "23.2361111";
$longitud="-106.41527780000001";
$zoom= "15";
$tipo_mapa = "ROADMAP";
$direccion = "";

if (isset($_GET["direccion"])) $direccion=  urldecode ($_GET["direccion"]);
else $direccion="";

// LONGITUD Y LATITUD SI ESTAN COMO PARAMETROS LOS COJO
if (isset($_GET["dir"])) $direccion = $_GET["dir"];
if (strlen ($direccion) <= 8) $direccion =""; // SI LA DIRECCION ES MENOR QUE 8 NO LA PROCESO

// LONGITUD Y LATITUD SI ESTAN COMO PARAMETROS LOS COJO
if (isset($_GET["lon"])) $longitud= $_GET["lon"];
if (isset($_GET["lat"])) $latitud= $_GET["lat"];

// ZOOM ENTRE 0 y 19
if (isset($_GET["zoom"])) $zoom= $_GET["zoom"];
if (($zoom<=0) || ($zoom>=20)){ $zoom= "17";}


// TIPO DE MAPA
if (isset($_GET["tipo"])) $tipo_mapa= strtoupper($_GET["tipo"]);

// COMPRUEBO QUE EL TIPO ES UNO DE LOS QUE ACEPTA GOOGLE
if ($tipo_mapa == "SATELLITE") $error=0;
else
  if ($tipo_mapa == "ROADMAP") $error=0;
  else 	
    if ($tipo_mapa == "TERRAIN")$error=0;
    else $tipo_mapa = "HYBRID";



?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script src="<?=base_url()?>assets/js/jquery-2.1.0.min.js"></script> 
	<script src="<?=base_url()?>assets/js/semantic.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/alertify.core.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/alertify.default.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/resetter.css">
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/semantic.min.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/styles.css">
<script src="<?=base_url()?>assets/js/alertify.min.js"></script>
<script src="<?=base_url()?>assets/js/alertify.js"></script>
		
<script type="text/javascript">


// VARIABLES GLOBALES JAVASCRIPT
var geocoder;
var marker;
var latLng;
var latLng2;
var map;

// INICiALIZACION DE MAPA
function initialize() {
  geocoder = new google.maps.Geocoder();	
  latLng = new google.maps.LatLng(<?echo $latitud;?> ,<?echo $longitud;?>);
  map = new google.maps.Map(document.getElementById('mapCanvas'), {
    zoom:<?echo $zoom;?>,
    center: latLng,
    mapTypeId: google.maps.MapTypeId.<?echo $tipo_mapa;?>
  });


// CREACION DEL MARCADOR  
    marker = new google.maps.Marker({
    position: latLng,
    title: 'Arrastra el marcador si quieres moverlo',
    map: map,
    draggable: true
  });
 
 

 
// Escucho el CLICK sobre el mama y si se produce actualizo la posicion del marcador 
     google.maps.event.addListener(map, 'click', function(event) {
     updateMarker(event.latLng);
   });
  
  // Inicializo los datos del marcador
  //    updateMarkerPosition(latLng);
     
      geocodePosition(latLng);
 
  // Permito los eventos drag/drop sobre el marcador
  google.maps.event.addListener(marker, 'dragstart', function() {
    updateMarkerAddress('Arrastrando...');
  });
 
  google.maps.event.addListener(marker, 'drag', function() {
    updateMarkerStatus('Arrastrando...');
    updateMarkerPosition(marker.getPosition());
  });
 
  google.maps.event.addListener(marker, 'dragend', function() {
    updateMarkerStatus('Arrastre finalizado');
    geocodePosition(marker.getPosition());
  });
  

 
}


// Permito la gesti¢n de los eventos DOM
google.maps.event.addDomListener(window, 'load', initialize);

// ESTA FUNCION OBTIENE LA DIRECCION A PARTIR DE LAS COORDENADAS POS
function geocodePosition(pos) {
  geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
      updateMarkerAddress(responses[0].formatted_address);
    } else {
      updateMarkerAddress('No puedo encontrar esta direccion.');
    }
  });
}

// OBTIENE LA DIRECCION A PARTIR DEL LAT y LON DEL FORMULARIO
function codeLatLon() { 
      str= document.form_mapa.longitud.value+" , "+document.form_mapa.latitud.value;
      latLng2 = new google.maps.LatLng(document.form_mapa.latitud.value ,document.form_mapa.longitud.value);
      marker.setPosition(latLng2);
      map.setCenter(latLng2);
      geocodePosition (latLng2);
      // document.form_mapa.direccion.value = str+" OK";
}

// OBTIENE LAS COORDENADAS DESDE lA DIRECCION EN LA CAJA DEL FORMULARIO
function codeAddress() {
        var address = document.form_mapa.direccion.value;
          geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
             updateMarkerPosition(results[0].geometry.location);
             marker.setPosition(results[0].geometry.location);
             map.setCenter(results[0].geometry.location);
           } else {
            alert('ERROR : ' + status);
          }
        });
      }
	  function codeAddress1() {
        var address = document.form_mapa1.direccion.value;
          geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
             updateMarkerPosition(results[0].geometry.location);
             marker.setPosition(results[0].geometry.location);
             map.setCenter(results[0].geometry.location);
           } else {
            alert('ERROR : ' + status);
          }
        });
      }


// OBTIENE LAS COORDENADAS DESDE lA DIRECCION EN LA CAJA DEL FORMULARIO
function codeAddress2 (address) {
          
          geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
             updateMarkerPosition(results[0].geometry.location);
             marker.setPosition(results[0].geometry.location);
             map.setCenter(results[0].geometry.location);
             document.form_mapa.direccion.value = address;
           } else {
            alert('ERROR : ' + status);
          }
        });
      }

function updateMarkerStatus(str) {
  document.form_mapa.direccion.value = str;
}

// RECUPERO LOS DATOS LON LAT Y DIRECCION Y LOS PONGO EN EL FORMULARIO
function updateMarkerPosition (latLng) {
  document.form_mapa.longitud.value =latLng.lng();
  document.form_mapa.latitud.value = latLng.lat();
   document.form_mapa1.longitud.value =latLng.lng();
  document.form_mapa1.latitud.value = latLng.lat();
}

function updateMarkerAddress(str) {
  document.form_mapa.direccion.value = str;
    document.form_mapa1.direccion.value = str;
	  
}

// ACTUALIZO LA POSICION DEL MARCADOR
function updateMarker(location) {
        marker.setPosition(location);
        updateMarkerPosition(location);
        geocodePosition(location);
      }


function enterpressalert(e){
var code = (e.keyCode ? e.keyCode : e.which);
if(code == 13) { //Enter keycode
 
}
}


</script>

</script>
</head>
<body  <? if ($direccion != "") { ?> onload=" codeAddress2('<? echo $direccion; ?>')" <? } ?> >
 
 

<style type="text/css">
  <!--html { height: 80% }-->
  <!--body { height: 80%; margin-left: 10px; margin-right : 0px; padding: 0px }-->
  <!--#mapCanvas { height: 100% }-->
</style> 
  

<div id="mapCanvas"></div>

<div  class="contenedor">

<div class="ui grid"> 
<div class="two wide column computer only"></div>

<div class="twelve wide column computer only">

<div class='ui small form segment'>
<div id="formulario">

   <form name="form_mapa" method="POST" enctype="multipart/form-data">
      
     
       <label>Direccion</label>
  	 <input type="text" name="direccion" id="direccion" value="<?echo $direccion;?>" style="width: 250px;font-size: 10px;font-family: verdana;font-weight: bold;" />
  	 <input type="button" class="ui blue button" value="Direccion" onclick="codeAddress()">
  
		<label>Lat</label>
		<input type="text" onchange="ejecuta()" readonly="readonly" name="latitud" id="lat"  value="<?echo $latitud;?>" style="width: 100px;font-size: 10px;font-family: verdana;font-weight: bold;" />	    
	
		<label>Lon</label>
	   <input type="text" onchange="ejecuta()" readonly="readonly" name="longitud" id="lon"   value="<?echo $longitud;?>" style="width: 100px;font-size: 10px;font-family: verdana;font-weight: bold;" />	
	 
	
		   

   
     </form>
 


       
</div> 

</div>

 

<br>
<div></div>
<button class="ui small blue submit button" type="submit" onclick="ejecuta()">Obtener Temperatura y humedad</button>
<div id="form">

</div>
<div id="info"></div>
</div>
<div class="two wide column computer only"></div>


<!--tablets-->
	<div class="column"></div>
			<div class="twelve wide column tablet only">
				<div class='ui small form segment'>
<div id="formulario">

   <form name="form_mapa1" method="POST" enctype="multipart/form-data">
      
     	<br>
			<br>
       <label>Direccion</label>
	   <br>
	   	<br>
  	 <input type="text" name="direccion" id="direccion" value="<?echo $direccion;?>" style="width: 250px;font-size: 10px;font-family: verdana;font-weight: bold;" />
  	<br>
	<br>
	<input type="button" class="ui blue button" value="Direccion" onclick="codeAddress1()">
  <br>
  <br>
		<label>Lat</label>
		<br>
			<br>
		<input type="text" onchange="ejecuta()" readonly="readonly" name="latitud" id="lat" value="<?echo $latitud;?>" style="width: 100px;font-size: 10px;font-family: verdana;font-weight: bold;" />	    
	<br>
		<br>
		<label>Lon</label>
		<br>
			<br>
	   <input type="text" onchange="ejecuta()" readonly="readonly" name="longitud"  id="lon" value="<?echo $longitud;?>" style="width: 100px;font-size: 10px;font-family: verdana;font-weight: bold;" />	
	
	
		   

   
     </form>
 


       
</div> 

</div>
<button class="ui small blue submit button" type="submit" onclick="ejecuta()">Obtener Temperatura y humedad</button>
<div id="form">

</div>
<div id="info"></div>
</div>

			</div>

			
		


		
		
		
		
</div>









<script>





function ejecuta(){
 	var lat=document.getElementById("lat").value;
var lon=document.getElementById("lon").value;
	var dataString = 'lat='+lat+'&lon='+lon;
	alert(dataString);
	//alert("xd");
	$.ajax({
            type: "POST",
           url: '<?=base_url()?>index.php/simula/compara',
           data: dataString,
            success: function(respuesta) {
		
				
					if(respuesta!=""){
	respuesta=$.parseJSON(respuesta);
	}
	
			var html = "";	
		
		if(respuesta.status==1){
			html+="<div class='ui small form segment'>";
			html+="<div class='two fields'>";
			
			html+="<div class='field'>";
			html+="<label>Temperatura:</label>";
		html+="<input id='temp' type='text' value=' "+respuesta.t+" °C'></input>";
		html+="</div>";
		html+="<div class='field'>";
				html+="<label>Humedad:</label>";
			html+="<input type='text' value=' "+respuesta.h+" %'></input>";
                 html+="</div>";
				html+="</div>";
					html+="</div>";
					$('#form').html(html);
			
				}
           
else{
			alertify.error("Actualmente no contamos con sensores en esta área"); 
			// $('#form').fadeIn(1000000).html("No se encontro un dispositivo en este punto");
			 html+="";
			// $('#form').html(alertify.error(No se encontro un disositivo));
			$('#form').html(html);
			}
			
		/*	else{
			  
			
			}
			*/
			}
      });
	

}






</script>
<div class="footer">
	<div class="ui left aligned grid">
		<div class="fondosfooter row">
			<div class="sixteen wide column">
				
			</div>
		</div>
		<div style="padding-top:0px;margin-top:0px;" class="row">
		<!-- Computer -->
			<div class="two wide column computer only"></div>
			<div class="four wide column computer only">
				 <div class="ui small header"><span class="nombrecolor">Más</span>Reciente</div>
				<a class="twitter-timeline" href="https://twitter.com/coolersearth" data-widget-id="455366953629007873">Tweets por @coolersearth</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>


				 <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
			<div class="four wide column computer only">
				 <div  class="ui small header">Contáctanos</div>
				 <div class="chico cursiva">
					NASA contrátenos por favor. (:, somos otro pedo :3
				 </div>
				 <div class="nombrecolor"> 
				Heat island solver
				 </div>
				 <div class="chico listado">
					<span>Lennin Sánchez</span>
					<span>Gilberto Sánchez</span>
					<span>Jorge Zatarain</span>
					
					<br>
					<span>Lomas de Mazatlán</span>
					<span>Mazatlan, Sinaloa, México</span>
					<br>
					<span class="nombrecolor">(669) 158-54-59</span>
					<span class="nombrecolor">(669) 165-00-58</span>
				 </div>
			</div>
			<div class="four wide column computer only">
				 <div class="ui small header">Redes Sociales</div>
				 <i class="facebook big icon"></i>
				 <i class="twitter big icon"></i>
				 <i class="google big plus icon"></i>

			</div>
			<div class="two wide column computer only"></div>
		</div>
		<!-- Tablet -->
			<div class="column"></div>
			<div class="five wide column tablet only">
				 <div  class="ui small header">Contáctanos</div>
				 <div class="chico cursiva">
					Nos gustaría saber de ti, ya se un proyecto para el cual necesites ayuda, o si te gustaría hablarnos para mejorar
				 </div>
				 <div class="nombrecolor"> 
					We cooler Watchers
				 </div>
				 <div class="chico listado">
				<span>Lennin Sánchez</span>
					<span>Gilberto Sánchez</span>
					<span>Jorge Zatarain</span>
					<span>Jose Villaseñor</span>
					<span>Carlos Ortega</span>
					<span>Angel Roca</span>
					<br>
					<span>Lomas de Mazatlán</span>
					<span>Mazatlan, Sinaloa, México</span>
					<br>
					<span class="nombrecolor">(669) 158-54-59</span>
					<span class="nombrecolor">(669) 165-00-58</span>
				 </div>
			</div>
			<div class="five wide column tablet only">
				 <div class="ui small header">Servicios</div>
				 <div class="ui link list">
				  <a class="item">Home</a>
				  <a class="item">About</a>
				  <a class="item">Jobs</a>
				  <a class="item">Team</a>
				</div>
			</div>
			<div class="five wide column tablet only">
				 <div class="ui small header">Redes Sociales</div>
				 <i class="facebook big icon"></i> 
				 <i class="twitter big icon"></i>
				 <i class="google big plus icon"></i>

			</div>
			<!-- Movil -->
			<div class="center aligned sixteen wide column mobile only">
				 <div  class="ui small header">Contáctanos</div>
				 <div class="chico cursiva">
					Nos gustaría saber de ti, ya se un proyecto para el cual necesites ayuda, o si te gustaría hablarnos para mejorar
				 </div>
				 <div class="nombrecolor"> 
					We cooler watchers
				 </div>
				 <div class="chico listado">
					<span>Lennin Sánchez</span>
					<span>Gilberto Sánchez</span>
					<span>Jorge Zatarain</span>
					<span>Jose Villaseñor</span>
					<span>Carlos Ortega</span>
					<span>Angel Roca</span> 
					<br>
					<span>Lomas de Mazatlán</span>
					<span>Mazatlan, Sinaloa, México</span>
					<br>
					<span class="nombrecolor">(669) 158-54-59</span>
					<span class="nombrecolor">(669) 165-00-58</span>
				 </div>
			</div>
			<div class="center aligned sixteen wide column mobile only">
				 <div class="ui small header">Servicios</div>
				 <div class="chico listado">
					<span>Publicidad</span>
					<span>Diseño Web</span>
					<span>asdfsdf</span>
				 </div>
			</div>
			<div class="center aligned sixteen wide column mobile only">
				 <div class="ui small header">Redes Sociales</div>
				 <i class="facebook big icon"></i>
				 <i class="twitter big icon"></i>
				 <i class="google big plus icon"></i>

			</div>
		<div class="sixteen wide column"></div>
		<div class="sixteen wide column"></div>
	</div>

</div>

<script>					
	$('.ui.dropdown').dropdown();
	$('.ui.accordion').accordion();
	$('.ui.popup').popup();		
$('.ui.rating')
  .rating();	

  $('#login')
  .popup({
    on: 'click',
	modal: 'true'
  })
;


	
</script>
			
	</body>
</html>