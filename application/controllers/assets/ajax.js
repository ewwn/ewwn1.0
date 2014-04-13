
		//cambiar();
		
		function ajax(opciones){												
			opciones.url = setval(opciones.url,"string",false);
			if(opciones.url==false){
				alert("Defina la url");
				return false;
			}
			opciones.tipo = setval(opciones.tipo,"string","POST");
			opciones.async = setval(opciones.async,"boolean",true);					
			opciones.datos = setval(opciones.datos,"string","");
			opciones.exito = setval(opciones.exito,"function",function(){return false;});
			opciones.mientras = setval(opciones.mientras,"function",function(){return false;});
			var xmlhttp;			
			if (window.XMLHttpRequest)// IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			else// IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			
			xmlhttp.onreadystatechange=function(){				
				if (xmlhttp.readyState == 4){
					if(xmlhttp.status == 200)
						opciones.exito(xmlhttp.responseText);
					else if(xmlhttp.status == 404)
						alert("Página no encontrada");	
					else if(xmlhttp.status == 500)
						alert("Error en el servidor");
						
				}else
					opciones.mientras();
			}
			xmlhttp.open(opciones.tipo,opciones.url,opciones.async);			
			//ENVIA DATOS POR POST
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");		
			xmlhttp.send(opciones.datos);
		}		
		function setval(variable,tipo,defecto){
			var tipo_v = typeof(variable);
			var valor;
			if(tipo_v!="undefined"){
				if(tipo_v==tipo){					
					valor = variable;
				}else
					valor = defecto;
			}else
				valor = defecto;
			return valor;	
		}
		function byId(id){
			return document.getElementById(id);
		}
	