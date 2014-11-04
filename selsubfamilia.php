<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

switch($modo){
	case "salvafamilia":
		$nombre = CleanText($_GET["familia"]);		
		if (strlen($nombre)>1)	CrearFamilia($nombre);							
		break;	
		
	case "salvasubfamilia":			
		$padre = CleanID($_GET["IdFamilia"]);
		$nombre = CleanText($_GET["Nombre"]);					
		CrearSubFamilia($nombre,$padre);	
		break;				
	case "getsubfamilia":
		$idfamilia = CleanID($_GET["IdFamilia"]);

		$subfamilias = genArraySubFamilias($idfamilia);

		foreach ($subfamilias as $key=>$value){
			echo "$value=$key\n";			
		}		
		
		exit();
		break;
}



StartXul(_("Elige familia"));

?>

<groupbox flex='1'> 
	<caption label='<?php echo _("Familia"); ?>'/>
<script>//<![CDATA[

		function id(valor){
			return document.getElementById(valor);
		}

		var sub = new Object();
		var fam = new Object();

<?php
		$familias = genArrayFamilias();				
		foreach ($familias as $key=>$value){
			echo "fam[$key] = '$value';\n";			
		}
?>
function BuscarFamilia(){
    var filtro = document.getElementById('buscafamilia').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('Familia');
    for(var i=0; i<theList.itemCount; i++){
        var texto2  = document.getElementsByTagName('listitem');
        var cadena = new String(texto2[i].attributes.getNamedItem('label').nodeValue);
        cadena = cadena.toUpperCase();
        if(cadena.indexOf(ns) != -1){
            theList.ensureIndexIsVisible(i);
            theList.selectedIndex=i;
            theList.onclick();
            return;
        }
    }
    theList.clearSelection();    
    var theList2=document.getElementById('Subfamilia');
    var n = theList2.itemCount;
    for(var i=0; i<n; i++){
        theList2.removeItemAt(0);
    }
}
function BuscarSubFamilia(){
    var filtro = document.getElementById('buscasubfamilia').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('Subfamilia');
    var contador = 0;
    var posicion = 0;
    if( theList.itemCount==0) return;
    for(var i=0; i<theList.itemCount; i++){
        var cadena = new String(theList.getItemAtIndex(i).label);
        cadena = cadena.toUpperCase();
        if(cadena.indexOf(ns) != -1){
            contador++;
            posicion = i;
            theList.ensureIndexIsVisible(i);
        }
    }
    if(contador==1){
            theList.selectedIndex=posicion;
            theList.onclick();
    }
}

function UsarNuevoFam() {              
    var url;
    var familia = document.getElementById('nueva').value;			
    
    if (!familia || familia == '') return;
        
        url = 'selsubfamilia.php';
        url = url +'?modo=salvafamilia';
        url = url + '&familia=' + familia;
        document.location.href = url			
}

		function UsarNuevoSub() {              
			var url;
			var famvalue = id("Familia").value;	
			var subfamilia = document.getElementById('nueva').value;			

            if (!subfamilia || subfamilia == '') return;
            if (!famvalue ) return;
            
			url = 'selsubfamilia.php';
			url = url +'?modo=salvasubfamilia';
            url = url + '&Nombre=' + subfamilia;
            url = url + '&IdFamilia=' + famvalue;
			document.location.href = url			
		}
				
		function VaciarListaSubFamilias(){
			var lista = document.getElementById("Subfamilia");
	
			
			while( lista.hasChildNodes()) {
			    lista.removeChild(lista.childNodes[0]);
		    }
		    
		    /*
		    var xsub= 	document.createElement("listitem");
			xsub.setAttribute("label","Cargando...");
			xsub.setAttribute("id","cargando");
			lista.appendChild(xsub);*/
					
		}
								
		
		function ProcesarSubfamilias(ordenes){		
			if (!ordenes)
				return;

			var xroot = document.getElementById("Subfamilia");
			var datos = ordenes.split("\n");
			var valores;
							


			for(var t=0;t<datos.length;t++){				
				valores = datos[t].split("=");		
				
				if (valores && valores[0] && valores[1] ) {
					var xsub= 	document.createElement("listitem");
					xsub.setAttribute("label",valores[0]);
					xsub.setAttribute("value",valores[1]);												
					xroot.appendChild( xsub );
					sub[valores[1]] = valores[0];
				}
			}
			
		}
		
		
		function RecalculaSubfamilia(valor){								
			var xrequest = new XMLHttpRequest();	
			var url;										
					

			id("familiatxt").setAttribute("label",fam[valor]);		
			id("subfamiliatxt").setAttribute("label"," ");		
					
			VaciarListaSubFamilias();	
			
			
			
	
			url = "selsubfamilia.php?modo=getsubfamilia&IdFamilia="+valor;
			xrequest.open("GET",url,false);
			xrequest.send(null);
  
			ProcesarSubfamilias(xrequest.responseText);					
		}
		
		function DevolverFamySubFam( subfamilia ){
            var index=document.getElementById('Subfamilia').selectedIndex;
            if (index== -1) return;
			var subtxt = sub[subfamilia];
			id("subfamiliatxt").setAttribute("label",subtxt);
			var famvalue = id("Familia").value;		
			
			//alert("Envia sub:"+subfamilia+",fam:"+famvalue+",sub["+sub[subfamilia]+",fam["+fam[famvalue]);
					
			opener.changeFamYSub(subfamilia,famvalue,sub[subfamilia],fam[famvalue]);
			window.close();
			return true;
		}

function soloAlfaNumerico(e){
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz0123456789";
    especiales = [8, 13];
    tecla_especial = false
    for(var i in especiales){
        if(key == especiales[i]){
            tecla_especial = true;
            break;
        }
    }
    
    if(letras.indexOf(tecla)==-1 && !tecla_especial){
        return false;
    }
}

//]]></script>
<hbox>
    <textbox flex='1' id='buscafamilia' onkeyup='BuscarFamilia()' 
             onkeypress="return soloAlfaNumerico(event)"/>
    <textbox flex='1' id='buscasubfamilia' onkeyup='BuscarSubFamilia()' 
             onkeypress="return soloAlfaNumerico(event)"/>
</hbox>
<hbox flex='1'>
	<listbox id='Familia'   onclick='RecalculaSubfamilia(this.value)' flex='1'>
		<?php  echo genXulComboFamilias();	?>
	</listbox>
	<listbox id='Subfamilia'  onclick='DevolverFamySubFam(this.value)' flex='1'>
	</listbox>
</hbox>		
</groupbox>

<hbox>
<caption label="Seleccion:"/><spacer style="width: 8px"/><caption id="familiatxt" label=" "/><caption label="-"/><caption id="subfamiliatxt"  label=" "/>
<?php 


?>
</hbox>

<hbox>
<groupbox flex='1'>
	<caption label='<?php echo _("Crear nueva") ?>'/>		
	<textbox id='nueva'  style="text-transform:uppercase;" 
                 onkeyup="javascript:this.value=this.value.toUpperCase();"
                 onkeypress="return soloAlfaNumerico(event)"/>
	<hbox flex="1">
	<button flex='1' label='<?php echo _("Familia") ?>' onkeypress='if (event.which == 13) UsarNuevoFam()' oncommand='UsarNuevoFam()'/>
	<button flex='1' label='<?php echo _("Subfamilia") ?>' onkeypress='if (event.which == 13) UsarNuevoSub()' oncommand='UsarNuevoSub()'/>
	</hbox>
</groupbox>
</hbox>

<?php

EndXul();

?>
