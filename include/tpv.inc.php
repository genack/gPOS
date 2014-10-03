<?php

	//Funciones para reducir cadenas: "332","33CASA","3" => 332,"33CASA",3 
	/**
	function is_intval($a) {
   		return ((string)$a === (string)(int)$a);
	}

	function qminimal($a){
		if (is_intval($a)){
			return (string)$a;			
		}	
		return qq($a);
	}
	**/
	/* - ------------------------------ */

	$NombreClienteContado = _("Cliente Contado");
        //$IdLocalActivo      = getSesionDato("IdTienda");
        $IdLocalActivo        = getSesionDato("IdTiendaDependiente");
        $localActivo          = new local;
	
	if ( $localActivo->Load($IdLocalActivo) ) 
	  {
	    $NombreLocalActivo	= CleanTo( $localActivo->get("NombreComercial")," " );
	    $MOTDActivo 	= CleanTo( $localActivo->get("MensajeMes")," " );
	    $PROMActivo 	= CleanTo( $localActivo->get("MensajePromocion")," " );
	  }

	//--------------------------------------------------
	// Indice de Ticket
	// $numSerieTicketLocalActual
	$miserie = "B" . $IdLocalActivo;
        //Nos aseguramos de coger el valor correcto preguntando tambien por 		
        // ..la serie. Esto ayudara cuando un mismo local tenga mas de una serie, como va a ser el 
        // ..caso luego. 
	
        $sql = 
	  "select Max(NComprobante) as NComprobanteMax ".
	  "from   ges_comprobantes ".
	  "where  (IdLocal = '$IdLocalActivo') ".
	  "and    (SerieComprobante='$miserie')";
	$row = queryrow($sql);
	
	if ($row)
	  {
	    $numSerieTicketLocalActual =  intval($row["NComprobanteMax"]) + 1; 
	  }	

	//--------------------------------------------------
        // LISTADO DE DEPENDIENTES
        // con identificador
        //Apuntamos todos los perfiles que pueden actuar en la TPV.
        $perfilesdependiente =  array();
        $dependientes        = array();

        $sql = 
	  "select IdPerfil ".
	  "from   ges_perfiles_usuario ".
	  "where  TPV=1 ";
        $res = query($sql);

        if ($res) 
	  {	
	    while($row = Row($res))
	      {
		$perfilesdependiente[$row["IdPerfil"]]=1;		
		//error(0,"Info: perfiles activos " .var_export($row,true) );
	      }		
	  }

        $numDependientes = 0;
        error(__LINE__ , "Info: salio  $sql");

        $NombreDependienteDefecto = false;
        $IdDependienteDefecto     = false;

        $sql = 
	  "select IdUsuario, ".
	  "       Nombre, ".
	  "       IdPerfil ".
	  "from   ges_usuarios ".
	  "where  Eliminado=0 AND IdLocal IN (".$IdLocalActivo.",0) ";			

        $res = query($sql);

        if($res) 
	  {	
	    $t = 0;
	    while($row = Row($res))
	      {
		$IdPerfil  = $row["IdPerfil"];
		$IdUsuario = $row["IdUsuario"];
		$nombre    = $row["Nombre"];			
		
		//error(0,"Info: usuarios activos " .var_export($row,true) );
		
		if ( isset($perfilesdependiente[$IdPerfil]) ) {
		  $t++;
		  
		  /*
		    if (!$IdDependienteDefecto) {
		    $IdDependienteDefecto = $IdUsuario;
		    $NombreDependienteDefecto  = $nombre;
		    }*/
		  
		  //Hace que si quien ha logueado es dependiente, se lo ponga por defecto
		  $sesname = getSesionDato("NombreUsuario") ;

		  if ( ($sesname == $nombre) and $nombre)
		    {
		      $IdDependienteDefecto      = $IdUsuario;
		      $NombreDependienteDefecto  = $nombre;	
		      //error(__FILE__ . __LINE__ ,"Info: OK '$sesname'=='$nombre'");
		      
		    } 
		  else
		    {
		      //error(__FILE__ . __LINE__ ,"Info: '$sesname'!='$nombre'");
		    }
		  
		  $dependientes[$nombre] = $IdUsuario;
		  //error(__LINE__,"Info: entro id '$IdUsuario', nombre '$nombre'");	
		  $numDependientes       = $numDependientes + 1;
		}		
	      }	
	  }
	
        if ((getSesionDato("NombreUsuario")  != $NombreDependienteDefecto) or !$NombreDependienteDefecto)
	  {
	    $usuarioActivoNoEsDependiente = 1;
	    error(__FILE__ . __LINE__ ,"Info: '$sesname'!='$nombre'");
	  } 
	else 
	  {
	    $usuarioActivoNoEsDependiente   = '';
	}

        $out = "";
        $t   = 0;	 	

        foreach ( $dependientes as $nombre => $IdUsuario)
	  {
	    $check = ($sesname == $nombre)? 'true':'false';

	    //error(__LINE__ , "Info: salio n $nombre, id $IdUsuario");
	    $out .= "<menuitem id='dep_". $t ."' image='chrome://mozapps/skin/profile/Zprofileicon.gif' type='radio' name='radio' label='$nombre' value='$IdUsuario' checked='$check' />\n"; 
	    $t++;		
	  }

        $generadorDeDependientes = $out;

	//--------------------------------------------------
        // LISTADO DE LOCALDEPENDIENTES
        // con identificador
        //Apuntamos todos los perfiles que pueden actuar en la TPV.
        $localdependientes = Array();
        $sql =
	  "select IdLocal,NombreComercial ".
	  "from   ges_locales ".
	  "where  Eliminado=0";			
        $res = query($sql);

        if($res) 
	  {	
	    $t = 0;
	    while($row = Row($res))
	      {
	      if(getSesionDato("esAlmacenCentral"))
		$localdependientes[$row["NombreComercial"]] = $row["IdLocal"];
	      else
		if(getSesionDato("IdTienda")==$row["IdLocal"])
		  $localdependientes[$row["NombreComercial"]] = $row["IdLocal"];
	      }	
	  }

        $out = "";
        $t   = 0;	 	

        foreach ( $localdependientes as $nombrelocal => $idlocaldep)
	  {
	    $out .= "<menuitem id='localdep_". $idlocaldep ."' type='radio' name='radio' label='$nombrelocal' value='$idlocaldep' oncommand ='cambiaLocalDependiente($idlocaldep)'/>\n"; 
	    $t++;		
	  }

          $generadorLocalDependientes = $out;

	  //--------------------------------------------------
          // LISTADO DE PRODUCTOS
          // con sus caracteristicas

          //$generadorJSDeProductos = getProductosSyncAlmacen(array(),$IdLocalActivo,false,false);
   
	  //--------------------------------------------------
          // LISTADO DE PROMOCIONES
          // con sus caracteristicas

          $generadorJSDePromociones = getPromocionesSyncAlmacen($IdLocalActivo);
   
          //--------------------------------------------------
          //LISTADO DE CLIENTES
          // con su identificador
          //$generadorJsDeClientes = getClientesTPV();

          //--------------------------------------------------
          //LISTADO SUBSIDIARIOS
          //proveedores de servicios 
          $i   = 0;
  	  $out = "";
	  $sql = 
	    "select IdSubsidiario, NombreComercial ".
	    "from   ges_subsidiarios ".
	    "where  Eliminado=0";
          $res = query($sql);

	  if ($res)
	    {
	      while( $row = Row($res))
		{
		  $i     = $i+1;
		  $value = $row["IdSubsidiario"];
		  $label = $row["NombreComercial"];
		  $out  .= "<menuitem class='media' id='subsidiario_".$i."' value='".$value."' label='".$label."'/>\n";
		}		
	    }

          $genSubsidiarios = $out;

          //--------------------------------------------------
          //LISTADO DE SERVICIOS
          $i   = 0;
	  $out = "";
	  $sql = 
	    "select IdServicio,Servicio ".
	    "from   ges_subsidiariosserv ";
	  $res = query($sql);
	  if ($res)
	    {
	      while( $row = Row($res))
		{
		  $i     = $i+1;
		  $value = $row["IdServicio"];
		  $label = $row["Servicio"];
		  $out  .= "<menuitem class='media' id='servicio_".$i."' value='".$value."' label='".$label."'/>\n";
		}		
	  }

          $genServicios = $out;

?>
