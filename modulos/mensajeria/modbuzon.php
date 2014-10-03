<?php

include("../../tool.php");

//SimpleAutentificacionAutomatica("visual-xulframe");

$IdUltimo = 0;

function EnviarMensaje($IdAutor,$titulo,$texto,$modo = "Normal",$toLocal=0,$toUser=0,$diasCaduca=1){
	$titulo = CleanText($titulo);
	$texto	= CleanText($texto);

	//Hora
	$titulo .= " - ".date('M d H:i');

	$sql = "INSERT INTO ges_mensajes (Titulo, Texto, IdAutor, IdLocalRestriccion, IdUsuarioRestriccion, Status, Fecha, DiasCaduca) 
		VALUES 
		('$titulo', '$texto', '$IdAutor', '$toLocal', '$toUser', '$modo', NOW(),'$diasCaduca')";
		
	return query($sql);		
}

function MensajesHeadersHoy($IdUltimo=0){
	//SELECT CURRENT_DATE
	//$IdLocal = getSesionDato("IdTienda");
        $IdLocal = getSesionDato("IdTiendaDependiente");
	$IdUsuario = getSesionDato("IdUsuario");
	
	$esParaNosotros = "IdLocalRestriccion=0 OR (IdLocalRestriccion = '$IdLocal')";
	$esParaMi = "IdUsuarioRestriccion=0 OR (IdUsuarioRestriccion = '$IdUsuario')";
	
	
	if ($IdUltimo>0) {
		$ultimoCondicion = " AND IdMensaje > '$IdUltimo' ";
	} else 
		$ultimoCondicion = "";
	//Fecha > CURRENT_DATE 
	
	$esFecha = "UNIX_TIMESTAMP(Fecha ) > UNIX_TIMESTAMP() - 86400*DiasCaduca";
	
	$sql = "SELECT IdMensaje, Titulo, Status FROM ges_mensajes WHERE ( $esFecha )
		AND ($esParaNosotros) 
		AND ($esParaMi) $ultimoCondicion ORDER BY Fecha ASC";
		
	//Usando el tipo CONVERSOR, que evita que aparezca en el log demasiadas entradas molestas de este.	
	$res = query($sql,"CONVERSOR");
	if ($res){
		$text = "";
		while ($row = Row($res) ){
			$IdMensaje 	= $row["IdMensaje"];
			$Titulo		= $row["Titulo"];
			$Status		= $row["Status"];
			$text .= $IdMensaje."'". $Titulo ."'". $Status. "\n";		
		}
	} else {
		return "error";
	}
	return $text;
}


function MensajeID($IdMensaje){
	$IdMensaje = CleanID($IdMensaje);

	$sql = "SELECT Titulo,Texto, Status FROM ges_mensajes WHERE IdMensaje = '$IdMensaje'"; 

	$row = queryrow($sql,"cargando mensaje");
	if ($row){
		$out = "";
		$Titulo		= $row["Titulo"];
		$Status		= $row["Status"];
		$Texto		= $row["Texto"];
		$out = $IdMensaje."'". $Titulo ."'". $Status."'".$Texto ."\n";				
	} else {
		return "error";
	}
	return $out;
}


switch($modo){

	case "CargarMensaje":
		$IdMensaje = CleanID($_GET["IdMensaje"]);
		$text = MensajeID($IdMensaje);
		echo $text;
		exit();	
		break;
	case "leernuevos":
	  $IdUltimo = (isset($_GET["IdUltimo"]))? CleanID($_GET["IdUltimo"]):0;
	case "hoy":
		if (!$IdUltimo)
			$IdUltimo = 0;
	
		$text = MensajesHeadersHoy($IdUltimo);
		echo $text;
		exit();
		break;
	case "notanormal":
		$titulo =  _("Nota a los locales - Normal");
		$explicacion = _("Este mensaje aparecerá en todos los TPV como un mensaje normal de aviso del administrador");
		$tTitulo = _("Titulo");
		$tCuerpo = _("Texto:");		
		$func = "EnviarNotaNormal";
		$prepopulado= _("Texto para enviar a todos los locales");
		break;
	case "notaimportante":
		$titulo =  _("Nota a los locales - IMPORTANTE");
		$explicacion = _("Este mensaje aparecerá en todos los TPV como un mensaje IMPORTANTE de aviso del administrador");
		$tTitulo = _("Titulo");
		$tCuerpo = _("Texto:");		
		$func = "EnviarNotaImportante";
		$prepopulado= _("Texto para enviar a todos los locales");
		break;		
	case "feature":
		$titulo =  _("Captura de Requisitos - Sugerencia");
		
		$tTitulo = _("Titulo");
		$tCuerpo = _("Sugerencia:");		
		$func = "EnviarSugerencia";
		$prepopulado= _("Escriba aquí su sugerencia");
		break;	
	case "bug":
		$titulo =  _("Captura de Requisitos - Defecto");
				
		$tTitulo = _("Titulo");
		$tCuerpo = _("Descripción del problema:");		
		$func = "EnviarBug";
		$prepopulado= _("Escriba aquí 1) como reproducir el error. 2) Que esperaba. 3) Que ocurrió");			
		break;	
	case "avisonotaprivada":	
		$titulo 	= CleanText($_POST["titulo"]);
		$vigencia 	= CleanText($_POST["vigencia"]);
		$texto 		= CleanText($_POST["cuerpo"]);
		$destino 	= CleanID($_POST["idestino"]);		
		$origen 	= getSesionDato("IdUsuario");
		if ( EnviarMensaje($origen,$titulo,$texto,"Normal",$destino,0,$vigencia) ) {
			echo "OK";
		} else {
			echo "ERROR";
		}		
		exit();
		break;		
	case "avisonotaimportante":
		$modoNota ="Urgente";
	case "avisonotanormal":
		if(!$modoNota)
			$modoNota = "Normal";
	
		$titulo = CleanText($_POST["titulo"]);
		$texto = CleanText($_POST["cuerpo"]);
		$QuienEnvia = getSesionDato("IdUsuario");
		if ( EnviarMensaje($QuienEnvia,$titulo,$texto,$modoNota) ) {
			echo "OK";
		} else {
			echo "ERROR";
		}		
		exit();
		break;
	case "avisobug":
	case "enviosugerencia":
		
		$res = mail(CORREO_ADMIN, "[9Gestion] $modo: ". $_POST["titulo"], "Cuerpo:\n".$_POST["cuerpo"]);
		
		if ($res)
		   echo "OK";
		else
		   echo "ERROR";
		
		exit();
		break;
}

StartXul("Buzon 9Gestion");
 

?>
<box flex="1" class="frameExtra">
<spacer flex="1" class="frameExtra"/>
   <vbox>
   <spacer flex="1" class="frameExtra"/>
	<groupbox style="width: 400px;height: 200px;background-color: #ECE8DE">
	 	<spacer flex="1"/>		
		<groupbox>
			<caption label="<?php	echo $titulo;	?>"/>
			<grid>
				<columns>
					<column flex="1"/>
					<column/>
				</columns>
				<rows>
					<row>
						<description>
						<?php  echo $tTitulo; ?>						
						</description>
						<textbox id='titulo' type="normal"
						 onkeypress="if (event.which == 13) document.getElementById('cuerpo').focus()"/>
					</row>
					<row>
						<description>
						<?php echo $tCuerpo; ?>													
						</description>
						<textbox rows="8" multiline="true" id='cuerpo' flex="1" style="width: 400px" 
							value="<?php echo $prepopulado ?>" />
					</row>
					<row>
						<description> 
						</description>
						<button label="<?php echo _("Enviar") ?>" oncommand="<?php echo $func ?>()"/>
					</row>		
					<row>
						<description>Ayuda:	</description>
						
						<textbox readonly='true' multiline="true" value="<?php
							echo $explicacion;												
						?>"/>
					</row>			
				</rows>												
			</grid>			
		</groupbox>
		<spacer flex="1"/>		
	</groupbox>
	<spacer flex="1"/>	 
	</vbox>
<spacer flex="1"/>	
</box>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>
<script><![CDATA[



function EnviarSugerencia() {
	EnviarMensaje( po_sugerenciarecibida ,"enviosugerencia");
}

function EnviarBug() {
	EnviarMensaje( po_incidenciaanotada ,"avisobug");
}

function EnviarNotaNormal() {
	EnviarMensaje(po_notaenviada ,"avisonotanormal");
}

function EnviarNotaImportante() {
	EnviarMensaje( po_notaenviada ,"avisonotaimportante");
}



function EnviarMensaje(mensaje,modo){
	var titulo  = document.getElementById("titulo").value;		
	var cuerpo  = document.getElementById("cuerpo").value;		
	
	var xrequest = new XMLHttpRequest();
	var data = "titulo="+escape(titulo)+"&cuerpo=" + encodeURIComponent(cuerpo);       	
	
	var url="modbuzon.php?modo="+modo;
		
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	var reply = xrequest.responseText;
	
	if (reply=="OK") {	
		document.getElementById("titulo").setAttribute("value","");
		document.getElementById("cuerpo").setAttribute("value","");
		document.getElementById("cuerpo").value = "";
		document.getElementById("titulo").value = "";
		alert(mensaje);
	} else {
		alert(po_servidorocupado);
	}
}



// Corregimos el foco para situarse en el primer input box

var mainwindow = document.getElementById("buzon-9gestion");

mainwindow.setAttribute("onload","FixFocus()");

function FixFocus(){
	document.getElementById("titulo").focus();
}


]]></script>

<?php


EndXul();

?>