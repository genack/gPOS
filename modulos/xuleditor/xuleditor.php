<?php

include("../../tool.php");

SimpleAuth("xuleditor",$module_password);


$ot = new template;

StartXul("Editor");


switch($modo) {
	case "load":
		$Nombre = $_POST["Nombre"];
		

		$id = getIdTemplateFromNombre($Nombre);
		if ($id) {

			if ($ot->Load($id)){
			$code = $ot->getCodigo();
			$Nombre = $ot->getNombre();	
			$idTemplate = $id;
			}
		}
		break;
	case "xulsave":
		$id = CleanID($_POST["id"]);
		$Nombre = $_POST["Nombre"];
		$Codigo = trim($_POST["Codigo"]);
		
		/*
		//$Codigo = str_replace('\\"', '"',$Codigo);		
		//$Codigo = str_replace("\\'", "'",$Codigo );
		//$Codigo = str_replace("\\\\n", "\\n",$Codigo );
		//$Codigo = str_replace("\\\\t", "\\t",$Codigo );
		$Codigo = str_replace("\\\\", "\\",$Codigo );
		$Codigo = str_replace("\\", "",$Codigo );		
		
		//str_replace("\\\'", "\\'",$Codigo )*/
		$Codigo = stripslashes($Codigo);
		
		error(0,"Info: llega del navegador '$Codigo'");
		
		if ($id and $Codigo) {
			if ($ot->Load($id)){	
			
				error(0, "Info: text:'". $Codigo. "'");
			
				$oldnombre = $ot->get("Nombre");
				$ot->set("Nombre",$Nombre,FORCE);
				$ot->setCodigo($Codigo);
				$idTemplate = $id;
				$code = $Codigo;				
				$ot->Save();
				$_SESSION["Template_$oldnombre"] = false;		
															
			}
		}	
		break;
	default:
	$id = $_GET["id"];

	if ($id) {

		if ($ot->Load($id)){
			$code = $ot->getCodigo();
			$Nombre = $ot->getNombre();	
			$idTemplate = $id;
		}
	}
	break;		
}

?>

<description id="contenidos" collapsed="true">
<html:div id="htmlcode"><![CDATA[
<?php
 echo trim($code);
?>
]]></html:div>
</description>

<hbox>
<?php

$menuWebmaster = array(
	_("Base CSS") =>  "editarCSS",
	_("Base xul CSS") =>  "editarxulCSS",	
	_("Base JS") =>  "editarJS"
	);  
	
echo xulMakeMenuCommands("Rapido",$menuWebmaster);
 
?>
<menulist label="Template">
 <menupopup>
<?php

	$res = Seleccion( "Template","","Nombre ASC, IdTemplate DESC","");
	while ($oTemplate = TemplateFactory($res) ){
		$n = $oTemplate->getNombre();
		echo "<menuitem label='$n' oncommand='load(\"$n\")'/>";	
	}
	
?>
 </menupopup>
</menulist>
<button label="Guardar" oncommand="salvar()"/>
<textbox id="Nombre" value="<?php echo $Nombre ?>"/>
<textbox id="IdTemplate" value="<?php echo $idTemplate ?>"/>


<command id="editarJS"   oncommand="load('BaseJS')"  label="Editar JS"/>  
<command id="editarCSS"   oncommand="load('BaseCSS')"  label="Editar CSS"/>  
<command id="editarxulCSS"  oncommand="load('xulCSS')"  label="Editar xul CSS"/>      
  
</hbox>

<html:textarea id="texteditor" flex="1" name="Codigo" onkeypress="return tabfriendly(event,this);">
<![CDATA[
<?php
 echo $code;
?>
]]></html:textarea>


<html:form id="forma" action="xuleditor.php?modo=xulsave" method="post" collapsed="true">
 <html:input type="hidden" id="htmlId" 	   name="id" value="" collapsed="true"/>
 <html:input type="hidden" id="htmlNombre" name="Nombre" value="" collapsed="true"/>
 <html:input type="hidden" id="htmlCodigo" name="Codigo" value="" collapsed="true"/> 
</html:form> 

<html:form id="formaLoad" action="xuleditor.php?modo=load" method="post" collapsed="true">
 <html:input type="hidden" id="htmlNombreLoad" name="Nombre" value="hidden" collapsed="true"/>
</html:form> 

<script>
<![CDATA[

function insertAtCursor(myField, myValue) {
	if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var cursorPos = startPos + myValue.length;

		myField.value = myField.value.substring(0, startPos)
		+ myValue
		+ myField.value.substring(endPos, myField.value.length);

		myField.selectionStart = cursorPos;
		myField.selectionEnd = cursorPos;
	}else{
		myField.value += myValue;
	}
} 

function AddText(element, aText) {
        var command = "cmd_insertText";
        var controller = element.controllers.getControllerForCommand(command);
        if (controller && controller.isCommandEnabled(command)) {
            controller = controller.QueryInterface(Components.interfaces.nsICommandController);
            var params = Components.classes["@mozilla.org/embedcomp/command-params;1"];
            params = params.createInstance(Components.interfaces.nsICommandParams);
            params.setStringValue("state_data", aText);
            controller.doCommandWithParams(command, params);
        }
}
    
function tabfriendly(event,me){
        if (event.keyCode == 9  && event.charCode==0) {
            //tabinta.insertTab(event.originalTarget);
            //insertAtCursor(me, "\t");
            //insertAtCursor(me, "   ");
            event.preventDefault();
        }
       
}

function load(nombre){
	var forma = document.getElementById("formaLoad");
	var hnl = document.getElementById("htmlNombreLoad");
	
	hnl.value = nombre;
	
	forma.submit();	
}




function salvar(){
	var forma = document.getElementById("forma");
	
	var editedcodigo = document.getElementById("texteditor");
	var hcodigo = document.getElementById("htmlCodigo");

	var nombre = document.getElementById("Nombre");
	var hnombre = document.getElementById("htmlNombre");
	
	var hid = document.getElementById("htmlId");
	var id = document.getElementById("IdTemplate");
	

	hid.value = id.getAttribute("value");

	hnombre.setAttribute("value", nombre.getAttribute("value"));
	hcodigo.setAttribute("value", unescape(editedcodigo.value) );  

    
    hnombre.value = nombre.value;
    hcodigo.value = editedcodigo.value
    
    
	forma.submit();

}

function id(nombre) {
	return document.getElementById(nombre);
}


function salvar2(){
	var xrequest = new XMLHttpRequest();
	var url = "xuleditor.php?modo=xulsave";
	var data = "";
	
	var IdTemplate 	= id("IdTemplate").value;
	var Nombre 		= id("Nombre").value;
	var Codigo 		= id("texteditor").value;
	

	data = data + "&id="+ escape(IdTemplate);
	data = data + "&Nombre="+ escape(Nombre);
	data = data + "&Codigo="+ escape(Codigo);			
	
	alert(data);
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	
	var resultado = xrequest.responseText;	
}



function clone() {

}



]]></script>  

<?php

EndXul();

?>
