<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

include("bug.class.php");



		$tituloSUG =  _("Captura de Requisitos - Sugerencia");
		
		$tTituloSUG = _("Titulo");
		$tCuerpoSUG = _("Sugerencia:");		
		$funcSUG = "EnviarSugerencia";
		$prepopuladoSUG = _("Escriba aquí su sugerencia");

		$tituloBUG =  _("Captura de Requisitos - Fallo");
				
		$tTituloBUG = _("Titulo");
		$tCuerpoBUG = _("Descripción del problema:");		
		$funcBUG = "EnviarBug";
		$prepopuladoBUG= _("Escriba aquí 1) como reproducir el error. 2) Que esperaba. 3) Que ocurrió");			


switch($modo){
	case "enviosugerencia":
		$titulo = CleanText($_POST["titulo"]);
		$cuerpo = CleanText($_POST["cuerpo"]);
	
		$bug = new bug();
	
		$bug->Sugerencia($titulo,$cuerpo);
		if ($bug->Alta()){
			$bug->Reportar();
			echo "OK";
		} else	
			echo "ERROR";
			
		exit();
		break;	
	case "avisobug":
		$titulo = CleanText($_POST["titulo"]);
		$quedonde = CleanText($_POST["quedonde"]);
		$queesperaba = CleanText($_POST["queesperaba"]);
		$categoria = CleanText($_POST["categoria"]);
		$queocurrio = CleanText($_POST["queocurrio"]);
		$urgencia = CleanID($_POST["urgencia"]);

		$bug = new bug();

		$bug->Fallo($titulo, $quedonde, $queesperaba, $categoria, $queocurrio, $urgencia	);			
		if($bug->Alta()){	
			$bug->Reportar();
			echo "OK";			
		} else	
			echo "ERROR";
		exit();

		break;	
}

StartXul("Reporte");
 

?>
<box flex="1" class="frameExtra" style="overflow: auto">
<spacer flex="1" class="frameExtra"/>
   <vbox>
   <spacer flex="1"/>	 		 	
	 	<tabbox style="width: 600px">
    	<tabs>
			<tab label="Sugerencia"/>    
			<tab label="Fallo"/>
		</tabs>
 	   
       <tabpanels flex="1" id="paneles">
       <tabpanel>	
       <vbox flex="1"> 	
		<groupbox flex="1">
			<caption label="<?php	echo $tituloSUG;	?>"/>
			<vbox>
			<hbox>
			<spacer flex="1"/>
			<image style="width: 48px; height: 48px" src="img/sugerencia1.png" />											
			</hbox>
			</vbox>
			<description><?php  echo $tTituloSUG; ?></description>
			<textbox id='tituloSUG' type="normal" onkeypress="if (event.which == 13) document.getElementById('cuerpo').focus()"/>
			
			<description><?php echo $tCuerpoSUG; ?></description>
			<textbox rows="8" multiline="true" id='cuerpoSUG' flex="1"	value="<?php echo $prepopuladoSUG ?>" />
			<description><?php echo $imagenSUG; ?></description>
			<button label="<?php echo _("Enviar") ?>" oncommand="<?php echo $funcSUG ?>()"/>
		</groupbox>		
		<spacer flex="1"/>
		</vbox>
		</tabpanel>

       <tabpanel>	 	
		<groupbox flex="1">
			<caption label="<?php	echo $tituloBUG;	?>"/>
			<vbox>
			<hbox>
			<spacer flex="1"/>
			<image style="width: 39px; height: 40px" src="img/defecto1.png" />											
			</hbox>
			</vbox>			
			<description><?php  echo $tTituloBUG; ?></description>
			<textbox id='tituloBUG'/>
			
			<description><?php  echo _("Urgencia"); ?></description>
			<menulist id="urgenciaBUG" >
			<menupopup>
			<menuitem value='0' label='0 - maxima'/>
			<menuitem value='1' label='1'/>
			<menuitem value='2' label='2'/>
			<menuitem value='3' label='3'/>
			<menuitem value='4' label='4 - minima'/>
			</menupopup>
			</menulist>
			<description><?php  echo _("Categoría"); ?></description>
			<menulist id="categoriaBUG" editable='true'>
			<menupopup>			
			<menuitem value='otros' label='Otros'/>
			<menuitem value='diseño' label='Diseño'/>
			<menuitem value='datos' label='Datos'/>
			<menuitem value='logica de negocio' label='Logica de negocio'/>
			<menuitem value='hardware' label='Hardware'/>
			</menupopup>
			</menulist>
					
			<description><?php echo _("Donde se produjo el error"); ?></description>
			<textbox rows="2" multiline="true" id='quedonde' flex="1"	value="" />

			<description><?php echo _("Que esperaba"); ?></description>
			<textbox rows="3" multiline="true" id='queesperaba' flex="1"	value="" />

			<description><?php echo _("Que ocurrió"); ?></description>
			<textbox rows="3" multiline="true" id='queocurrio' flex="1"	value="" />


			<button label="<?php echo _("Enviar") ?>" oncommand="<?php echo $funcBUG ?>()"/>
		</groupbox>
		</tabpanel>


		</tabpanels>
		</tabbox>
		
	<spacer flex="1"/>	 
	</vbox>
<spacer flex="1"/>	
</box>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>
<script><![CDATA[


function EnviarBug(){
	var mensaje = po_incidenciaanotada;
	var modo = "avisobug";
	var titulo  = document.getElementById("tituloBUG").value;		
	var quedonde  = document.getElementById("quedonde").value;		
	var queesperaba  = document.getElementById("queesperaba").value;
	var queocurrio  = document.getElementById("queocurrio").value;
	var categoria = document.getElementById("categoriaBUG").label;
	var urgencia = document.getElementById("urgenciaBUG").label;
	
	var xrequest = new XMLHttpRequest();
	var data = "titulo="+encodeURIComponent(titulo)+
		"&quedonde=" + encodeURIComponent(quedonde)+       	
		"&queesperaba=" + encodeURIComponent(queesperaba)+
		"&categoria=" + encodeURIComponent(categoria)+
		"&urgencia=" + encodeURIComponent(urgencia)+
		"&queocurrio=" + encodeURIComponent(queocurrio);
		
	
	var url="reporte.php?modo="+modo;
		
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	var reply = xrequest.responseText;
	
	if (reply=="OK") {	
		document.getElementById("quedonde").value = "";
		document.getElementById("queesperaba").value = "";
		document.getElementById("queocurrio").value = "";
		document.getElementById("tituloBUG").value = "";
		alert(mensaje);
	} else {
		alert(po_servidorocupado);
	}
}


function EnviarSugerencia(){
	var mensaje = po_sugerenciarecibida;
	var modo = "enviosugerencia";
	var titulo  = document.getElementById("tituloSUG").value;		
	var cuerpo  = document.getElementById("cuerpoSUG").value;		
	
	var xrequest = new XMLHttpRequest();
	var data = "titulo="+escape(titulo)+"&cuerpo=" + encodeURIComponent(cuerpo);       	
	
	var url="reporte.php?modo="+modo;
		
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	var reply = xrequest.responseText;
	
	if (reply=="OK") {	
		document.getElementById("cuerpoSUG").value = "";
		document.getElementById("tituloSUG").value = "";
		alert(mensaje);
	} else {
		alert(po_servidorocupado);
	}
}



// Corregimos el foco para situarse en el primer input box

/*
var mainwindow = document.getElementById("buzon-9gestion");

mainwindow.setAttribute("onload","FixFocus()");
*/

function FixFocus(){
	document.getElementById("tituloSUG").focus();
}


]]></script>

<?php


EndXul();

?>
