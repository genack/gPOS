<?php

include("tool.php");


SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Compras")); 

define("TALLAJE_DEFECTO",5);
$TallajeDefecto = "Varios";
$op = new Producto();
$op->Crea(); 

$modo                = CleanText($_GET["modo"]);
$Referencia          = $op->get("Referencia");
//$Nombre              = $op->get("Nombre");
$Marca 	             = _("Generico");
$FamDefecto          = _("Varias");
$primerCB            = $op->get("CodigoBarras");
$IdFamiliaDefecto    = getFirstNotNull("ges_familias","IdFamilia");
$IdSubFamiliaDefecto = getSubFamiliaAleatoria($IdFamiliaDefecto );
$FamDefecto          = getIdFamilia2Texto($IdFamiliaDefecto ) . " - " .getIdSubFamilia2Texto( $IdFamiliaDefecto,$IdSubFamiliaDefecto );
$Nombre      = '';
$txtMoDet    = getModeloDetalle2txt();
$esBTCA      = ( $txtMoDet[0] == "BTCA" );
$txtModelo   = $txtMoDet[1];
$txtDetalle  = $txtMoDet[2];
$txtalias    = $txtMoDet[3];
$txtref      = $txtMoDet[4];
$btca        = ( $esBTCA )?'false':'true';

$esInvent    = ( $modo=='altainventario')? true:false;
$btnAlta     = ($esInvent)? "AltaProductoInventario()":"AltaProducto()";
$lbtnAlta    = ($esInvent)? " Registrar...":" Comprar...";
$ibtnAlta    = ($esInvent)? "img/gpos_registrarinventarioalta.png":"img/gpos_compras.png";
$btnVaciar   = ($esInvent)? "parent.volverStock()":"CancelarTallasYColores()";
$lbtnVaciar  = ($esInvent)? " Volver Stock":" Vaciar";
$ibtnVaciar  = ($esInvent)? "img/gpos_volver.png":"img/gpos_vaciarcompras.png";
?>

<!--  no-visuales -->
<?php include("partes-compras/altarapida.php"); ?>
<!--  no-visuales -->

<spacer style="height:2px"/>
<hbox pack="center">
  <caption style="font-size: 14px;font-weight: bold;">
    <?php echo _("Alta Rapida") ?>
  </caption>
</hbox>
<spacer style="height:1px"/>

<hbox>

<groupbox>
<caption label="Caracteristicas"/>
<!-- alta de prod -->
<grid> 
  <rows> 
    <row>
      <caption class="media" label="Referencia"/>
      <textbox class="media" id="Referencia" value="<?php echo $Referencia ?>"
               onkeypress="return soloAlfaNumericoCodigo(event);"  style="text-transform:uppercase;" 
	       onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </row>

    <row>
      <caption class="media" label="Código Barras"/>
      <textbox style="min-width: 7em" class="media" id="CB" value="<?php echo $primerCB ?>"
               onkeypress="return soloNumeros(event,this.value);"/>
    </row>

    <row>
      <caption class="media" label="<?php echo $txtref ?>"/>
      <textbox class="media" id="RefProv" onkeypress="return soloAlfaNumerico(event);"
	       style="text-transform:uppercase;" 
	       onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </row>

     <row>
      <caption class="media" label="Prov. hab"/>
      <box>
	<toolbarbutton id="lProvHab" style="width: 32px !important" 
		       oncommand="CogeProvHab()" label="+"/>
	<textbox class="media" id="ProvHab" readonly="true" flex="1"/>
      </box>
    </row>

    <row  collapsed="<?php echo $btca;?>">
      <caption class="media" label="Lab. hab"/>
      <box>
	<toolbarbutton id="lLabHab" style="width: 32px !important" 
		       oncommand="CogeLabHab()" label="+"/>
	<textbox class="media" id="LabHab" readonly="true" flex="1"
                 onkeypress="return soloAlfaNumerico(event);"/>
      </box>
    </row>

    <row>
      <caption class="media" label="Fam/Subfam"/>
      <box>
	<toolbarbutton  id="lFamSub" style="width: 32px !important" 
		       oncommand="CogeFamilia()" label="+"/>
	<textbox value="<?php echo $FamDefecto; ?>" flex="1" id="FamSub"
                 onkeypress="return soloAlfaNumerico(event);"/>
      </box>
    </row>

    <row>
      <caption class="media" label="Nombre"/>
      <textbox class="media" multiline="true"  style="text-transform:uppercase;" 
	       onkeyup="javascript:this.value=this.value.toUpperCase();" 
	       onfocus="this.select()" id="Descripcion" value="<?php echo $Nombre ?>"
               onkeypress="return soloAlfaNumerico(event);"/>
    </row>

    <row>
      <caption class="media" label="Marca"/>
      <box>
	<toolbarbutton id="lMarca" style="width: 32px !important" 
		       oncommand="CogerMarca()" label="+"/>
	<textbox class="media" id="Marca" value="<?php echo $Marca ?>"  flex="1" readonly="true"/>
      </box>
    </row>

    <row>
      <caption class="media" label="<?php echo $txtDetalle; ?>"/>
      <box>
	<toolbarbutton id="lTallaje" style="width: 32px !important" 
		       oncommand="CogeTallaje()" label="+"/>
	<textbox value="<?php echo $TallajeDefecto; ?>" readonly="true" 
	class="media" id="Tallaje"  flex="1"/>
      </box>
    </row>

    <row>
      <caption class="media" label="Unidad de Medida"/>
      <hbox>
	<menulist flex="2"  style="min-width: 4em" class="media" id="UnidadMedida">
	  <menupopup class="media" id="comboUnidadMedida">
	    <menuitem label="Und." selected="true" oncommand="changeUnidMedida('und')"/>
	    <menuitem label="Mts." oncommand="changeUnidMedida('mts')"/>
	    <menuitem label="Lts." oncommand="changeUnidMedida('lts')"/>
	    <menuitem label="Kls." oncommand="changeUnidMedida('kls')"/>
	  </menupopup>
	</menulist>
      </hbox>
    </row>

    <row collapsed="<?php echo $btca;?>">
      <caption class="media" label="Condición de venta"/>
      <hbox>
	<menulist flex="2"  style="min-width: 4em" class="media" id="CondicionVenta">
	  <menupopup class="media" >
	    <menuitem label="Sin Receta Médica" value="0" />
	    <menuitem label="Con Receta Médica" value="CRM"/>
	    <menuitem label="Con Receta Médica Retenida" value="CRMR"/>
	  </menupopup>
	</menulist>
      </hbox>
    </row>

    <row>
<spacer style="height:5px"/>
    </row>
    
    <row>
      <caption class="media" label="Numeros de Serie"/>
      <box> 
	<checkbox id="NS" dir="reverse" oncommand="verDatosExtra('ns',this.checked)" /> 
      </box>
    </row>
    <row>
      <caption class="media" label="Fecha de Venc."/>
      <box>  
	<checkbox id="FechaVencimiento" dir="reverse" oncommand="verDatosExtra('fv',this.checked)" />
      </box>
    </row>
    <row>
      <caption class="media" label="Lote de Producción"/>
      <box> 
	<checkbox id="Lote" dir="reverse" oncommand="verDatosExtra('lt',this.checked)" />
      </box>
    </row>
    <row>
      <caption class="media" label="Venta al Menudeo"/>
      <box> 
	<checkbox id="Menudeo" dir="reverse" oncommand="verDatosExtra('ct',this.checked)" />
      </box>
    </row>

    <row>
      <box>
	<spacer flex="1" style="height: 8px"/>
      </box>
      <box/>
    </row>
    
  </rows>
</grid>

<!-- alta de prod -->
</groupbox>


<!-- listado compra tickets -->
    <vbox flex="1" class="listado">
	<spacer style="height: 16px"/>
	<caption label="<?php echo $txtModelo .' y '. $txtDetalle; ?>" class="media" 
	style="border-bottom: 1px black solid"/>	    
	<spacer style="height: 16px"/>
	 <hbox>	
	  <grid>
	    <rows> 
	      <row>
		<caption class="media" label="<?php echo $txtalias.' uno';?>"/>
		<box>
		  <toolbarbutton style="width: 32px !important" 
				 oncommand="CogeAlias(0,'<?php echo $txtalias ?>')" label="+"/>
		  <textbox class="media" id="IdAlias0" readonly="true" flex="1" value=""/>
		</box>
	      </row>

	      <row>
		<caption class="media" label="<?php echo $txtalias.' dos';?>"/>
		<box>
		  <toolbarbutton style="width: 32px !important" 
				 oncommand="CogeAlias(1,'<?php echo $txtalias ?>')" label="+"/>
		  <textbox class="media" id="IdAlias1" readonly="true" flex="1" value=""/>
		</box>
	      </row>

	      <row>
		<caption class="media" label="<?php echo $txtModelo; ?>"/>
		<hbox>
		  <toolbarbutton style="width: 32px !important" oncommand="CogeColor()" label="+"/>
		  <menulist flex="2" style="min-width: 4em" class="media" id="Colores" value="0">
		    <menupopup class="media" id="elementosColores">
		      <?php echo  genXulComboColores(false, "menuitem", $IdFamiliaDefecto,"def");?>
		    </menupopup>
		  </menulist>
		</hbox>
	      </row>

	      <row>
		<caption class="media" label="<?php echo $txtDetalle; ?>"/>
		<hbox>
		  <toolbarbutton style="width: 32px !important" oncommand="CogeTalla()" label="+"/>
		  <menulist flex="2" class="media" id="Tallas" style="min-width: 7em">
		    <menupopup class="media" id="elementosTallas">
		      <?php echo  genXulComboTallas("32", "menuitem",TALLAJE_DEFECTO,"def",$IdFamiliaDefecto);?>  	
		    </menupopup>
		  </menulist>
		</hbox>
	      </row>

	    </rows>
	  </grid>

	  <grid>
	    <rows> 

	      <row id="rowDatoFechaVencimiento" collapsed="true">
		<caption class="media" label="Fecha Venc."/>
                <datepicker id="vFechaVencimiento" type="popup"/>
	      </row>
	      <row id="rowDatoLote" collapsed="true">
		<caption class="media" label="Lote de Prod."/>
		<textbox id="vLote" value="" onfocus="this.select()"
			 style="text-transform:uppercase;" 
			 onkeyup="javascript:this.value=this.value.toUpperCase();" 
                         onkeypress="return soloAlfaNumerico(event);"/> 
	      </row>

 	      <row id="rowContenedor" collapsed="true">
		<caption class="media" label="Unid/Empaque"/>
		<hbox>
		  <toolbarbutton style="width: 32px !important" oncommand="CogeContenedor()" label="+"/>
		  <textbox id="UnidadesxContenedor" value="2" onfocus="this.select()" 
			   style="width: 5em"
			   onchange="validaDato(this,'nCont')" 
			   onkeypress="return soloNumerosEnteros(event,this.value)"/>/		  
   	           <menulist flex="2" style="min-width: 8em" class="media" id="Contenedores" value="1">
    		   <menupopup class="media" id="elementosContenedores">
                    <?php  echo  genXulComboContenedores("1","menuitem","def"); ?>
 		   </menupopup>
		  </menulist>
		</hbox>
	      </row>

	      <row id="rowDatoContenedor" collapsed="true">
		<caption id="txtContenedor" class="media" label="Empaque - Unid"/>
		<hbox flex="1">
		<textbox class="media" id="Empaques" value="1" onfocus="this.select()" 
			 style="width: 8.5em" 
			 onkeypress="return soloNumerosEnteros(event,this.value)"/> -
		<textbox class="media" id="Unidades" value="0" onfocus="this.select()"
			 style="width: 8em" 
			 onchange="validaDato(this,'nUnidEmp')" 
			 onkeypress="return soloNumerosEnteros(event,this.value)"/>
		</hbox>
	      </row>
	      <row id="rowCantidad">
		<caption class="media" label="Cantidad"/>
		<textbox class="media" style="text-align:right; width:6em;" id="Cantidad" value="1" 
			 onchange="validaDato(this,'nCant')" onfocus="this.select()"
			 onkeypress="return soloNumerosEnteros(event,this.value)"/>
	      </row>
	      <row>
 		<caption class="media" label="Costo Unitario"/>
		<textbox style="text-align:right; width:6em;" class="media" id="Costo" 
			 onfocus="this.select()" value="0" 
			 onkeypress="return soloNumeros(event,this.value)"
			 onblur="setCostoPreciosAltaRapida('costo',this)"/>
	      </row>

	      <row>
		<caption label="Precio Compra" />
		<textbox style="text-align:right; width:6em;" id="xPrecioCompra" 
			 onfocus="this.select()" 
			 onkeypress="return soloNumeros(event,this.value)" value="0"
			 onblur="setCostoPreciosAltaRapida('precio',this)" />
	    </row>


	    </rows>
	   </grid>

	   <grid>
	     <rows>
	       <row>
		 <caption label="PVD" />
		 <textbox id="xPVD" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onblur="setCostoPreciosAltaRapida('pvd',this)"/>
	       </row>

	       <row>
		 <caption label="PVD/Dcto." />
		 <textbox id="xPVDD" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onblur="setCostoPreciosAltaRapida('pvdd',this)"/>
	       </row>

	       <row>
		 <caption label="PVC" />
		 <textbox id="xPVC" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onblur="setCostoPreciosAltaRapida('pvc',this)"/>
	       </row>

	       <row>
		 <caption label="PVC/Dcto." />
		 <textbox id="xPVCD" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onblur="setCostoPreciosAltaRapida('pvcd',this)"/>
	       </row>
	     </rows>
	   </grid>

         </hbox>

	<hbox>
	  <button image="img/gpos_nuevoproducto.png" class="media" flex="1" 
		  label="  Agregar Producto" oncommand="xNuevaTallaColor()" 
		  style="font-size:11px;font-weight: bold;"/> 		
	</hbox>		
	<listbox id="listadoTacolor" rows="4" flex="1" contextmenu="accionesTicket" onkeypress="if (event.keyCode==13)  RevisarProductoSeleccionado()"  onclick="RevisarProductoSeleccionado()" >
	  <listcols flex="1">
	    <listcol  />
	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
 	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
	    <splitter class="tree-splitter"  />
	    <listcol flex="1" />
	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
	    <splitter class="tree-splitter" />
	    <listcol flex="1" />
	    <splitter  class="tree-splitter" />
	    <listcol flex="1" />
	    <listcol/>
	  </listcols>
	  <listhead>
	    <listheader label="Codigo" />
	    <listheader label="<?php echo $txtModelo; ?>" />
	    <listheader label="<?php echo $txtDetalle; ?>"/>
	    <listheader label="<?php echo $txtalias;?>" />
	    <listheader label="Cantidad" />
	    <listheader label="Costo" />
	    <listheader label="PVD" />
	    <listheader label="PVDD" />
	    <listheader label="PVC" />
	    <listheader label="PVCD" />
	    <listheader label="" id="colMenudeo"/>
	    <listheader label="" id="colFV"/>
	    <listheader label="" id="colLT"/>
	    <listheader label=""/>
	  </listhead>
	  
	</listbox>
	<hbox>
          <button image="<?php echo $ibtnAlta?>" class="media" flex="1" 
                  oncommand="<?php echo $btnAlta?>" label="<?php echo $lbtnAlta?>" 
                  style="font-size:11px;font-weight: bold;"/>
	  <button image="<?php echo $ibtnVaciar?>" class="media" flex="1" 
	          label="<?php echo $lbtnVaciar?>"
		  oncommand="<?php echo $btnVaciar?>" 
		  style="font-size:11px;font-weight: bold;"/>  
	</hbox>
      </vbox>	
      <!-- listado compra tickets -->	

<script>//<![CDATA[
var enviar = new Array();

  enviar["IdSubFamilia"] = <?php echo CleanID($IdSubFamiliaDefecto) ?>;
  enviar["IdFamilia"]    = <?php echo CleanID($IdFamiliaDefecto) ?>;
  enviar["IdTallaje"]    = '<?php echo TALLAJE_DEFECTO ?>';//Precargado con un tallaje por defecto para autogeneracion
  enviar["UnidadMedida"] = 'und';
  enviar["IdLabHab"] = 1;
  enviar["IdMarca"] = 1;
  enviar["IdProvHab"] = 1;
  enviar["IdAlias0"] = 0;
  enviar["IdAlias1"] = 0;

  var MITALLAJEDEFECTO = '<?php echo TALLAJE_DEFECTO ?>';//Si no se especifica tallaje, prepopula para un tallaje concreto x defecto
  var cImpuesto    = <?php echo getSesionDato("IGV"); ?>;
  var cIdLocal     = <?php echo getSesionDato("IdTienda"); ?>;
  var cUtilidad    = <?php echo getSesionDato("MargenUtilidad"); ?>;
  var cModo        = <?php echo "'".$modo."'"; ?>;
  var esInventario = (cModo=='altainventario')? true:false;
  var aProducto    = Array();
//]]></script>
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?a=4"/>
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js"/>
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/altarapida.js?a=4"/>

</hbox>

<?php

EndXul();

?>
