<?php

// El objetivo de este modulo es cachear informacion
// en la sesion sin que sea necesario repetir busquedas

function getParametro($nombre) {
	$params = getSesionDato("Parametros");
	if (!is_array($params)){
		error(__FILE__ . __LINE__ , "E: lectura fallida de parametros");
		return false;	
	}
	return $params[$nombre];	
}

function setParametroSuscripcion() {

	query("update ges_parametros set Suscripcion = NOW()");
	$row = queryrow("select * from ges_parametros","Cargando parametros");
	$_SESSION["Parametros"] = $row;
}

function getSesionDato($nombre){
	global $debug_mode;
	
	switch($nombre){
		case "series":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
                case "seriesbuy":
		        if (!isset($_SESSION[$nombre]))
			  $_SESSION[$nombre]=array();
			return $_SESSION[$nombre];

                case "fechagarantia":
		        if (!isset($_SESSION[$nombre]))
			  $_SESSION[$nombre]=array();
			return $_SESSION[$nombre];

                case "postCompraListado":
		        if ($_SESSION[$nombre])
			  $_SESSION[$nombre]=true;
			return $_SESSION[$nombre];

                case "xdtCarritoCompras":
		        if (!isset($_SESSION[$nombre]))
			  $_SESSION[$nombre]=array();
			return $_SESSION[$nombre];

                case "seriescart":
		       if (!isset($_SESSION[$nombre]))
			 $_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
		case "idprodserie":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
	        case "idprodseriebuy":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
	        case "idprodseriecart":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];

		case "cantserie":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
		case "modoserie":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
		case "fechavencimiento":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
                case "codigolote":
 			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
		case "garantia":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
		case "CarritoProd":
		case "CarritoTrans":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];

		case "CarritoTransSeries":
			if (!isset($_SESSION[$nombre]))
				$_SESSION[$nombre]=array();
			return $_SESSION[$nombre];
	
		case "CarritoMover":
		case "PerfilActivo":
		case "CarroCostesCompra":
		case "CarritoCompras":
			//Esta mal pero funciona (?) y si lo arreglas deja de funcionar (?!)		
		        if ( !isset( $_SESSION[$nombre] ))
			  return $_SESSION[$nombre]=array();

		        if (  is_array( $_SESSION[$nombre]) )
		  	  return $_SESSION[$nombre]=array();
				
			return unserialize($_SESSION[$nombre]);		
		
		case "Parametros":
			if (isset($_SESSION[$nombre])){
				return $_SESSION[$nombre];
			}		
		
			$row = queryrow("SELECT * FROM ges_parametros","Cargando parametros");
			$_SESSION[$nombre] = $row;
			
			return $row;						

		case "ModoCarritoAlmacen":
		  if (!isset($_SESSION[$nombre]))
			  $_SESSION[$nombre]='g';

		  return $_SESSION[$nombre];


		case "TipoVentaTPV":
		        if (isset($_SESSION[$nombre]))
			  return $_SESSION[$nombre];
			//return $_SESSION[$nombre];
		
		case "IdLenguajeDefecto": //Idioma para productos en altas, bajas, etc...
		
			if (isset($_SESSION[$nombre])){
				return $_SESSION[$nombre];
			}		
			
			$lang = getIdFromLang("es");
			$_SESSION[$nombre] = $lang;
			return $lang;

		case "IdTienda":
		case "IdUsuario":
			if (isset($_SESSION[$nombre]))
				return $_SESSION[$nombre];
			return false;
	
		case "IdLenguajeInterface": //Idioma del usuario
			//TODO:
			// leer del usuario 	
				
			return getSesionDato("IdLenguajeDefecto");

		case "ComboAlmacenes":	
		         
		        if (isset($_SESSION[$nombre])){
				return $_SESSION[$nombre];
			}
		
			$out = genComboAlmacenes();
			$_SESSION[$nombre] = $out;
			return $out;						
		case "Almacen":		
			return new almacenes; //obsoleto
		case "Articulos":		
			return new articulo;	
		case "AlmacenCentral":
			$local = new local;
			if ($local->LoadCentral()){
				return $local;	
			}
			error(__FILE__ . __LINE__ , "E: no pudo cargar el almacén central");
			return false;				
		case "ArrayTiendas":
		        if ( isset($_SESSION["ArrayTiendas"]) ){
				return $_SESSION["ArrayTiendas"];
			}
			
			$alm = new almacenes;
			$arrayTodos = array_keys($alm->listaTodosConNombre());

			$_SESSION["ArrayTiendas"] = $arrayTodos;
			return $arrayTodos;
	
		case "hayCarritoCompras":
			if (!isset($_SESSION["CarritoCompras"])){
				return false;
			}
			$val = $_SESSION["CarritoCompras"];
			if(!is_array($val) and count($val) ){
				return false;
			}
			return true;	
		case "hayCarritoTrans":
			if (!isset($_SESSION["CarritoTrans"])){
				return false;
			}
			$val = $_SESSION["CarritoTrans"];
			if(!is_array($val) and count($val)){
				return false;
			}
			
			if ($val==0 or $val == array())
				return false;
				
			if (count($val)==0)
				return false;
			
			return true;
	
		case "hayCarritoProd":
			if (!isset($_SESSION["CarritoProd"])){
				return false;
			}
			$val = $_SESSION["CarritoProd"];
			if(!is_array($val) and count($val)){
				return false;
			}
			return true;
			
		case "hayCarritoFam":
			if (!isset($_SESSION["CarritoFam"])){
				return false;
			}
			$val = $_SESSION["CarritoFam"];
			if(!is_array($val) and count($val)){
				return false;
			}
			return true;
                case "detadoc":
			if (!isset($_SESSION[$nombre])){
				$detadoc=array();
				$detadoc[0]='SD';
				$detadoc[1]='1';
				$detadoc[2]='CASAS VARIAS';
				$detadoc[3]=false;
				$detadoc[4]=false;
				$detadoc[5]=1;
				$detadoc[6]=1;
				$detadoc[7]=false;
				$detadoc[8]=false;
				$detadoc[9]=false;
				$detadoc[10]=false;
				$detadoc[11]=false;
				$detadoc[12]=false;
				$detadoc[13]=0;
				$detadoc[14]=0;
				$detadoc[15]='';
				$_SESSION[$nombre]=$detadoc;
			}
			return $_SESSION[$nombre];
			
		case "PaginadorCliente":	
		case "PaginadorSeleccionCompras2":	
		case "PaginadorSeleccionCompras":
		case "PaginadorCompras":
		case "PaginadorProv":
		case "PaginadorListaProv":				
		case "PaginadorLab":
		case "PaginadorListaLab":				
		case "PaginadorAlmacen":
		case "PaginadorListaProd":		
		case "PaginadorSeleccionAlmacen":
		case "PaginadorListaFam":
		case "PaginadorListaSubFam":
		    if (!isset($_SESSION[$nombre])) 
		      return false;
		     return intval($_SESSION[$nombre]);
		     break;
	        case "incImpuestoDet":
	        case "descuentos":
	        case "incPercepcionDet":
	        case "aCredito":
		case "FiltraLab":
		case "FiltraAlias":
		case "FiltraMarca":
		case "FiltraColor":
		case "FiltraTalla":
		case "FiltraBase":
		case "FiltraBase":
		case "FiltraProv":
		     if (!isset($_SESSION[$nombre]))
                        return false;
		     return $_SESSION[$nombre];
		     break;
		default:	
		  return ( isset($_SESSION[$nombre]) )? $_SESSION[$nombre]:false;	
	}		
	
}

function invalidarSesion($clase) {
	
	switch($clase){
		case "ListaTiendas":
			$_SESSION["ArrayTiendas"] = false;
			$_SESSION["ComboAlmacenes"] = false;
			break;
		default:
			$_SESSION[$clase] =false;	
	} 
		
	
}


function setSesionDato($dato,$valor) {	
	global $_SESSION;
	
	if (is_object($valor)){
	 	$_SESSION[$dato] = serialize($valor);
	 	return;		
	}
	
	switch($dato){
		case "PerfilActivo":
		case "CarritoMover":
		case "CarroCostesCompra":
		case "CarritoCompras":
		$_SESSION[$dato] = serialize($valor);
		return;		
	}
	
	$_SESSION[$dato] = $valor;
}

function getModeloDetalle2txt(){

   $atxt = array();

   switch( getSesionDato("GlobalGiroNegocio") ){
   case "BTCA": 
     array_push($atxt, 'BTCA');
     array_push($atxt, 'Presentación ó Modelo');
     array_push($atxt, 'Concentración ó Detalle');
     array_push($atxt, 'Principio activo');
     array_push($atxt, 'Registro Sanitario');
     break;

   case "BTQE": 
     array_push($atxt, 'BTQE');
     array_push($atxt, 'Color ó Modelo');
     array_push($atxt, 'Talla ó Detalle');
     array_push($atxt, 'Etiqueta');
     array_push($atxt, 'Referencia Fabr.');
     break;

   default:
     array_push($atxt, 'PINF');
     array_push($atxt, 'Modelo');
     array_push($atxt, 'Detalle');
     array_push($atxt, 'Etiqueta');
     array_push($atxt, 'Referencia Fabr.');
     break;
   }
   return $atxt;
}

?>
