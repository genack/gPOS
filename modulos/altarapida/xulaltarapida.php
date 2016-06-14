<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

define("TALLAJE_DEFECTO",5);
$TallajeDefecto = "Varios";
$op = new Producto();
$op->Crea();

if( !isset($_GET["modo"]) ) return;

// ---clonar ---
$esClonar            = ( isset($_GET["clonidbase"]) );
$cAccion             = ($esClonar)? 'clon':'alta';
$idproducto          = ($esClonar)? CleanID($_GET["clonidbase"]):0;
$DetProducto         = ($esClonar)? getDatosProductosExtra($idproducto,'clon'):Array();
$IdEmpaque           = ($esClonar)? $DetProducto["IdContenedor"]:false;
$UnidadMedida        = ($esClonar)? $DetProducto["Und"]:'Uni';
$UnidxEmp            = ($esClonar)? $DetProducto["UndxEmp"]:2;
$idcolor             = ($esClonar)? $DetProducto["IdColor"]:false;
$idtalla             = ($esClonar)? $DetProducto["IdTalla"]:32;
$laboratorio         = ($esClonar)? $DetProducto["Lab"]:"";
$txtModeloDetalle    = ($esClonar)? $DetProducto["Nombre"]." ".$DetProducto["Marca"]." ".$DetProducto["Lab"]:"";

$txtNuevo            = ($esClonar)? "":"Nuevo ";
$txtAltaRapida       = ($esClonar)? "Clonar Producto ":"Alta Rápida";
$txtAlias0           = ($esClonar)? $DetProducto["Alias0"]:"";
$txtAlias1           = ($esClonar)? $DetProducto["Alias1"]:"";
//---clonar

$modo                = CleanText($_GET["modo"]);
$Referencia          = ($esClonar)? $DetProducto["Referencia"]:$op->get("Referencia");
$Marca 	             = ($esClonar)? $DetProducto["Marca"]:_("Generico");
$primerCB            = $op->get("CodigoBarras");
$IdFamiliaDefecto    = ($esClonar)? $DetProducto["IdFamilia"]:getFirstNotNull("ges_familias","IdFamilia");
$IdSubFamiliaDefecto = ($esClonar)? $DetProducto["IdSubFamilia"]:getSubFamiliaAleatoria($IdFamiliaDefecto );
$FamDefecto          = getIdFamilia2Texto($IdFamiliaDefecto ) . " - " .getIdSubFamilia2Texto( $IdFamiliaDefecto,$IdSubFamiliaDefecto );
$Nombre      = ($esClonar)? $DetProducto["Nombre"]:'';

$txtMoDet    = getGiroNegocio2txt();
$esBTCA      = ( $txtMoDet[0] == "BTCA" );
$esWESL      = ( $txtMoDet[0] == "WESL" );
$txtModelo   = $txtMoDet[1];
$txtDetalle  = $txtMoDet[2];
$txtalias    = $txtMoDet[3];
$txtref      = $txtMoDet[4];
$btca        = ( $esBTCA )?'false':'true';
$wesl        = ( $esWESL )?'false':'true';

$esInvent    = ( $modo=='altainventario');
$btnAlta     = ($esInvent)? "AltaProductoInventario()":"AltaProducto()";
$lbtnAlta    = ($esInvent)? " Registrar...":" Comprar...";
$ibtnAlta    = ($esInvent)? "$_BasePath"."img/gpos_registrarinventarioalta.png":"$_BasePath"."img/gpos_compras.png";
$btnVaciar   = ($esInvent)? "parent.volverStock()":"volverPresupuestos()";
$lbtnVaciar  = ($esInvent)? " Volver Stock":" Volver Presupuestos";
$tituloAlta  = ($esInvent)? "true":"false";
$ibtnVaciar  = ($esInvent)? "$_BasePath"."img/gpos_volver.png":"$_BasePath"."img/gpos_volver.png";

$DSTOGR      = getSesionDato("DescuentoTienda");
$MUGR        = getSesionDato("MargenUtilidad");
$COPImpuesto = getSesionDato("COPImpuesto");
$MetodoRedondeo = getSesionDato("MetodoRedondeo");
$detadoc      = getSesionDato("detadoc");
$Moneda       = getSesionDato("Moneda");

$incimpuesto  = ($esInvent)? "false":getSesionDato("incImpuestoDet");
$impuesto     = ( $incimpuesto=="true" )? 0:getSesionDato("IGV");
$txtcostocore = ( $incimpuesto=="true" )? "Precio Unitario":"Costo Unitario";

$tipomoneda   = $detadoc[5];
$tipocambio   = ( $detadoc[6] != '')? $detadoc[6]:1;
$tipocambio   = (!$esInvent && $tipomoneda == 2)? $tipocambio:1;
$txtcosto     = (!$esInvent )? $Moneda[ $tipomoneda ]['S']:$Moneda[1]['S'];
$txtprecio    = $Moneda[1]['S'];

StartXul('Alta Rapida',$predata="",$css='');
StartJs($js='modulos/altarapida/altarapida.js?v=4.4.2');
?>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?a=4"/>

<!--  no-visuales -->
<?php include("altarapidamenu.php"); ?>
<!--  no-visuales -->
      <hbox class="box">	
	<html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
	<html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
	</html:div>
      </hbox>	

<hbox pack="center" class="box" collapsed="<?php echo $tituloAlta; ?>">
  <caption class="h1">
    <?php echo $txtAltaRapida ?>
  </caption>
</hbox>

<hbox pack="center" align="center" class="box">
  <caption id="msjAltaRapida" style="margin-top:.6em;font-size: 12px;font-style: italic;font-weight: bold;" 
	   label="" collapsed="true"/>
</hbox>

<hbox class="box" flex="1" pack="center" align="" >
 <groupbox id="faceNameBox">
  <caption class="box" label="<?php echo $txtNuevo.'Producto '.$txtModeloDetalle; ?>" flex="1" style="border-bottom: 1px solid  #bbb"/>
  <!-- alta de prod -->
   <grid> 
    <rows>
 
     <row id="row_referencia">
      <caption class="media" label="Referencia"/>
      <textbox class="media" id="Referencia" value="<?php echo $Referencia ?>"
               onkeypress="return soloAlfaNumericoCodigo(event);"  style="text-transform:uppercase;" 
	       onkeyup="javascript:this.value=this.value.toUpperCase();"/>
     </row>

     <row id="row_refproveedor">
      <caption class="media" label="<?php echo $txtref ?>"/>
      <textbox class="media" id="RefProv" onkeypress="return soloAlfaNumerico(event);"
	       style="text-transform:uppercase;" 
	       onkeyup="javascript:this.value=this.value.toUpperCase();"/>
     </row>

     <row id="row_proveedorhabitual">
      <caption class="media" label="Prov. hab"/>
      <box>
	<toolbarbutton class="btn" id="lProvHab" oncommand="CogeProvHab()" label="+"/>
	<textbox class="media" id="ProvHab" readonly="true" flex="1" onkeypress="if (event.which == 13)CogeProvHab() "  />
      </box>
     </row>

     <row  collapsed="<?php echo $btca;?>">
      <caption class="media" label="Lab. hab"/>
      <box>
	<toolbarbutton  class="btn" id="lLabHab" oncommand="CogeLabHab()" label="+"/>
	<textbox class="media" id="LabHab" readonly="true" flex="1" value="<?php echo $laboratorio;?>"
                 onkeypress="if (event.which == 13) CogeLabHab() "/>
      </box>
     </row>

     <row>
      <caption class="media" label="Fam/Subfam"/>
      <box>
	<toolbarbutton   class="btn"   id="lFamSub"  oncommand="CogeFamilia()" label="+"/>
	<textbox value="<?php echo $FamDefecto; ?>" flex="1" id="FamSub"
                 readonly="true" onkeypress="if (event.which == 13) CogeFamilia() "/>
      </box>
     </row>

     <row>
      <caption class="media" label="Nombre"/>
      <textbox class="media" multiline="true"  style="text-transform:uppercase;" 
	       onfocus="this.select()" id="Descripcion" value="<?php echo $Nombre ?>"
               onkeypress="return soloAlfaNumerico(event);"/>
     </row>

     <row>
      <caption class="media" label="Marca"/>
      <box>
	<toolbarbutton   class="btn" id="lMarca" oncommand="CogerMarca()" label="+"/>
	<textbox class="media" id="Marca" value="<?php echo $Marca ?>"
                 onkeypress="if (event.which == 13) CogerMarca()" flex="1" readonly="true"/>
      </box>
     </row>
  
     <row id="row_tipodetalle">
      <caption class="media" label="<?php echo $txtDetalle; ?>"/>
      <box>
	<toolbarbutton class="btn" id="lTallaje" oncommand="CogeTallaje()" label="+"/>
	<textbox value="<?php echo $TallajeDefecto; ?>" readonly="true" 
	class="media" id="Tallaje"  flex="1"/>
      </box>
     </row>

     <row>
      <caption class="media" label="Unidad Medida"/>
      <hbox>
	<menulist flex="2"  style="min-width: 4em" class="media" id="UnidadMedida">
	  <menupopup class="media" id="comboUnidadMedida">
	    <menuitem label="Unidad" selected="true" oncommand="changeUnidMedida('und')"/>
  	    <menuitem label="Metro" oncommand="changeUnidMedida('mts')"/>
  	    <menuitem label="Litro" oncommand="changeUnidMedida('lts')"/>
	    <menuitem label="Kilo" oncommand="changeUnidMedida('kls')"/>
       	    <menuitem label="Bolsa" oncommand="changeUnidMedida('bls')"/>
            <menuitem label="Balde" oncommand="changeUnidMedida('blds')"/>
            <menuitem label="Blister" oncommand="changeUnidMedida('blist')"/>
      	    <menuitem label="Metros3" oncommand="changeUnidMedida('mts3')"/>
    	    <menuitem label="Metros2" oncommand="changeUnidMedida('mts2')"/>
  	    <menuitem label="Caja" oncommand="changeUnidMedida('cjas')"/>
            <menuitem label="Varilla" oncommand="changeUnidMedida('vllas')"/>
            <menuitem label="Pieza" oncommand="changeUnidMedida('pzas')"/>
            <menuitem label="Palcha" oncommand="changeUnidMedida('plchs')"/>
            <menuitem label="Pack" oncommand="changeUnidMedida('pack')"/>
            <menuitem label="Madeja" oncommand="changeUnidMedida('mdjs')"/>
            <menuitem label="Galon" oncommand="changeUnidMedida('gls')"/>
            <menuitem label="Display" oncommand="changeUnidMedida('displ')"/>
            <menuitem label="Tira" oncommand="changeUnidMedida('tira')"/>
            <menuitem label="Saco" oncommand="changeUnidMedida('saco')"/>
	  </menupopup>
	</menulist>
       </hbox>
     </row>

     <row>
      <caption class="media" label="Opciones Avanzadas"/>
      <box> 
	<checkbox id="OpcionesAvanzadas" dir="reverse" oncommand="checkOpcionesAvanzadas(this.checked)" /> 
      </box>
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
      <caption class="box" label="Atributos Stock" flex="1"/>
     </row>

     <row>
      <caption class="media" label="Número Serie"/>
      <box> 
	<checkbox id="NS" dir="reverse" oncommand="verDatosExtra('ns',this.checked)" /> 
      </box>
     </row>
     <row>
      <caption class="media" label="Fecha Vencimiento"/>
      <box>  
	<checkbox id="FechaVencimiento" dir="reverse" oncommand="verDatosExtra('fv',this.checked)" />
      </box>
     </row>
     <row>
      <caption class="media" label="Lote Producción"/>
      <box> 
	<checkbox id="Lote" dir="reverse" oncommand="verDatosExtra('lt',this.checked)" />
      </box>
     </row>
     <row>
      <caption class="media" label="Venta Menudeo"/>
      <box>
       <checkbox id="Menudeo" dir="reverse" oncommand="verDatosExtra('ct',this.checked)" />
      </box>
     </row>

   </rows>
 
 </grid>
 <spacer style="height: 1em"/>
 <hbox>

  <button image="<?php echo $ibtnVaciar?>" class="media btn" flex="1" 
	          label="<?php echo $lbtnVaciar?>"
		  oncommand="<?php echo $btnVaciar?>" 
		  style="font-size:11px;font-weight: bold;"/>  


  <button image="<?php echo $_BasePath.'img/gpos_nuevoproducto.png'?>" class="media btn" flex="1" 
                   oncommand="setfacesaltarapida('matris')" label=" Siguiente " id="btnFacesName"
                   style="font-size:11px;font-weight: bold;"/>
 </hbox>

 <!-- alta de prod -->
 </groupbox>

 <!-- listado compra tickets -->
    <vbox flex="1" class="listado box"  id="faceMatrisBox">
	<spacer style="height: 6px"/>
	<caption label="<?php echo 'Elija '.$txtModelo .' y '. $txtDetalle.' del Producto '.$txtModeloDetalle; ?>" class="media box" id="txtModeloDetalle" style="border-bottom: 1px solid  #bbb"/>	    
	<spacer style="height: 4px"/>
	 <hbox>	
	  <grid>
	    <rows>
  
              <row id="row_codigobarras">
                <caption class="media" label="Código Barras"/>
                <textbox class="media" id="CB" value="<?php echo $primerCB ?>" maxlength="13"
                         onkeypress="return soloNumeros(event,this.value);"/>
              </row>

              <row>
		<caption class="media" label="<?php echo $txtalias.' uno';?>"/>
		<box>
		  <toolbarbutton class="btn" oncommand="CogeAlias(0,'<?php echo $txtalias ?>')" label="+"/>
		  <textbox class="media" id="IdAlias0" readonly="true" flex="1" value="<?php echo $txtAlias0;?>"/>
		</box>
	      </row>

	      <row>
		<caption class="media" label="<?php echo $txtalias.' dos';?>"/>
		<box>
		  <toolbarbutton class="btn" oncommand="CogeAlias(1,'<?php echo $txtalias ?>')" label="+"/>
		  <textbox class="media" id="IdAlias1" readonly="true" flex="1" value="<?php echo $txtAlias1;?>"/>
		</box>
	      </row>

	      <row>
		<caption class="media" label="<?php echo $txtModelo; ?>"/>
		<hbox>
		  <toolbarbutton class="btn" oncommand="CogeColor()" label="+"/>
		  <menulist flex="2" style="min-width: 4em" class="media" id="Colores" value="0">
		    <menupopup class="media" id="elementosColores">
		      <?php echo  genXulComboColores($idcolor, "menuitem", $IdFamiliaDefecto,"def");?>
		    </menupopup>
		  </menulist>
		</hbox>
	      </row>

	      <row>
		<caption class="media" label="<?php echo $txtDetalle; ?>"/>
		<hbox>
		  <toolbarbutton class="btn"  oncommand="CogeTalla()" label="+"/>
		  <menulist flex="2" class="media" id="Tallas" style="min-width: 7em">
		    <menupopup class="media" id="elementosTallas">
		      <?php echo  genXulComboTallas($idtalla, "menuitem",TALLAJE_DEFECTO,"def",$IdFamiliaDefecto);?>  	
		    </menupopup>
		  </menulist>
		</hbox>
	      </row>


 	      <row id="rowContenedor" collapsed="true">
		<caption class="media" label="Unid - Empaque"/>
		<hbox>
		  <toolbarbutton class="btn" oncommand="CogeContenedor()" label="+"/>
		  <textbox id="UnidadesxContenedor" value="<?php echo $UnidxEmp ?>" onfocus="this.select()" 
			   style="width: 5em" onchange="validaDato(this,'nCont')" 
			   onkeypress="return soloNumerosEnteros(event,this.value)"/>		  
   	           <menulist flex="2" style="min-width: 8em" class="media" id="Contenedores" value="1">
    		   <menupopup class="media" id="elementosContenedores">
                    <?php  echo  genXulComboContenedores($IdEmpaque,"menuitem","def"); ?>
 		   </menupopup>
		  </menulist>
		</hbox>
	      </row>

	    </rows>
	  </grid>

	  <grid>
	    <rows> 

	      <row id="rowDatoContenedor" collapsed="true">
		<caption id="txtContenedor" class="media" label="Empaque - Unid"/>
		<hbox flex="1">
		<textbox class="media" id="Empaques" value="1" onfocus="this.select()" 
			 style="width: 8.5em" 
			 onkeypress="return soloNumerosEnteros(event,this.value)"/>
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
	      <row id="rowDatoLote" collapsed="true">
		<caption class="media" label="Lote de Prod."/>
		<textbox id="vLote" value="" onfocus="this.select()"
			 style="text-transform:uppercase;" 
			 onkeyup="javascript:this.value=this.value.toUpperCase();" 
                         onkeypress="return soloAlfaNumerico(event);"/> 
	      </row>
	      <row id="rowDatoFechaVencimiento" collapsed="true">
		<caption class="media" label="Fecha Venc."/>
                <datepicker id="vFechaVencimiento" type="popup"/>
	      </row>

	      <row>
 		<caption class="media" label="<?php echo $txtcostocore.' '.$txtcosto?>"/>
                <hbox flex="1">
		  <textbox  flex="1" style="text-align:right; width:6em;" class="media" id="Costo" 
			 onfocus="this.select()" value="0" 
			 onkeypress="return soloNumeros(event,this.value)"
			 onchange="setCostoPreciosAltaRapida('costo',this)"/>
                  <textbox  flex="1" id="costoxEmpaque" value="0" collapsed="true"/>
	          <button class="btn" id="xEmpaqueProductoAlta" oncommand="mostrarCostoTotalAlta()" collapsed="true"/>
                </hbox>
	      </row>

	      <row>
		<caption label="Costo Operativo <?php echo $txtprecio?>" />
		<textbox style="text-align:right; width:6em;" id="xCostoOP" 
			 onfocus="this.select()" 
			 onkeypress="return soloNumeros(event,this.value)" value="0"
			 onchange="setCostoPreciosAltaRapida('precio',this)" />
	    </row>

	    </rows>
	   </grid>

	   <grid>
	     <rows>
	       <row>
		 <caption label="PVP <?php echo $txtprecio?>" />
		 <textbox id="xPVD" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onchange="setCostoPreciosAltaRapida('pvd',this)"/>
	       </row>

	       <row>
		 <caption label="PVP/Dcto. <?php echo $txtprecio?>" />
		 <textbox id="xPVDD" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onchange="setCostoPreciosAltaRapida('pvdd',this)"/>
	       </row>

	       <row>
		 <caption label="PVC <?php echo $txtprecio?>" />
		 <textbox id="xPVC" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onchange="setCostoPreciosAltaRapida('pvc',this)"/>
	       </row>

	       <row>
		 <caption label="PVC/Dcto. <?php echo $txtprecio?>" />
		 <textbox id="xPVCD" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onchange="setCostoPreciosAltaRapida('pvcd',this)"/>
	       </row>
	     </rows>
	   </grid>

           <grid>
  	     <rows>
  	       <row id="rowPVEmpaque" collapsed="true">
		 <caption label="PV/Empaq.<?php echo $txtprecio?>" />
		 <textbox id="xPVDE" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onchange="setCostoPreciosAltaRapida('pvpe',this)"/>
	       </row>
	       <row id="rowPVDocena" collapsed="<?php echo $wesl;?>">
		 <caption label="PV/Docena<?php echo $txtprecio?>" />
		 <textbox id="xPVDED" onfocus="this.select()" style="text-align:right; width:6em;"
			  onkeypress="return soloNumeros(event,this.value)" value="0"
			  onchange="setCostoPreciosAltaRapida('pvped',this)"/>
	       </row>
	     </rows>
	   </grid>
  
         </hbox>

	<hbox>
	  <button image="<?php echo $_BasePath; ?>img/gpos_nuevoproducto.png" class="media btn" flex="1" 
		  label="  Agregar Producto" oncommand="xNuevaTallaColor()" 
		  style="font-size:11px;font-weight: bold;"/> 		
	</hbox>		
	<tree id="listadoTacolor" flex="1" hidecolumnpicker="false" enableColumnDrag="true" contextmenu="accionesTicket" onkeypress="if (event.keyCode==13)  RevisarProductoSeleccionado()"  onclick="RevisarProductoSeleccionado()" >
	  <treecols>
	    <treecol  label="Código"  flex="1" />
	    <splitter class="tree-splitter" />
	    <treecol label="<?php echo $txtModelo; ?>" flex="1" />
	    <splitter class="tree-splitter" />
	    <treecol label="<?php echo $txtDetalle; ?>" flex="1" />
	    <splitter class="tree-splitter" />
	    <treecol label="<?php echo $txtalias;?>" hidden="true" flex="1" />
 	    <splitter class="tree-splitter" />
	    <treecol label="Cantidad" flex="1" />
 	    <splitter class="tree-splitter" />
	    <treecol label="Costo" flex="1" />
	    <splitter class="tree-splitter" />
	    <treecol label="Costo OP" flex="1"  hidden="true" />
	    <splitter class="tree-splitter"  />
	    <treecol label="PVP"  flex="1" />
	    <splitter class="tree-splitter" />
	    <treecol label="PVPD" flex="1" />
	    <splitter class="tree-splitter" />
	    <treecol label="PVC" flex="1"  hidden="true" />
	    <splitter class="tree-splitter" />
	    <treecol label="PVCD" flex="1" hidden="true" />
	    <splitter  class="tree-splitter" />
	    <treecol label="PVEmpaq." flex="1" id="colPVDE"  hidden="true"/>
	    <splitter  class="tree-splitter" />
	    <treecol label="PVDocena" flex="1" id="colDocena"  hidden="true"/>
	    <splitter class="tree-splitter" />
	    <treecol label="Menudeo" flex="1" id="colMenudeo"  hidden="true" />
	    <splitter  class="tree-splitter" />
	    <treecol label="FV" flex="1" id="colFV"  hidden="true"/>
	    <splitter  class="tree-splitter" />
	    <treecol label="LT" flex="1" id="colLT"  hidden="true"/>
	  </treecols>
	  <treechildren id="my_tree_children">
	  </treechildren>
	</tree>

	<hbox id="btnAccionAltaRapida" collapsed="false">
          <button image="<?php echo $ibtnAlta?>" class="media btn" flex="1" 
                  oncommand="<?php echo $btnAlta?>" label="<?php echo $lbtnAlta?>" 
                  style="font-size:11px;font-weight: bold;"/>
	  <button image="<?php echo $ibtnVaciar?>" class="media btn" flex="1" 
	          label="<?php echo $lbtnVaciar?>"
		  oncommand="<?php echo $btnVaciar?>" 
		  style="font-size:11px;font-weight: bold;"/>  
	</hbox>
  	<hbox  id="btnFaceMatris" collapsed="false">
          <spacer style="height: 1em"/>
          <button image="<?php echo $ibtnVaciar?>"
                  class="media btn" flex="1" 
                  oncommand="setfacesaltarapida('name')" label=" Volver Atrás " 
                  style="font-size:11px;font-weight: bold;"/>
         </hbox>
      </vbox>	
      <!-- listado compra tickets -->	
<script>//<![CDATA[
  var enviar = new Array();
  var cAccion = "<?php echo $cAccion; ?>";
  var MITALLAJEDEFECTO = '<?php echo TALLAJE_DEFECTO ?>';//Si no se especifica tallaje, prepopula para un tallaje concreto x defecto
  var cImpuesto    = <?php echo $impuesto; ?>;
  var cIdLocal     = <?php echo getSesionDato("IdTienda"); ?>;
  var cUtilidad    = <?php echo getSesionDato("MargenUtilidad"); ?>;
  var cCambio      = <?php echo $tipocambio; ?>;
  var cModeloDetCore = "<?php echo 'Elija '.$txtModelo .' y '. $txtDetalle.' del Producto '?>";
  var cModeloDetalle = '<?php echo $txtModeloDetalle;?>';
  var cDescripcion = '';
  var cMarca       = '';
  var cClonIdBase  = 0;
  var cLaboratorio = '';

  var cModo        = <?php echo "'".$modo."'"; ?>;
  var esInventario = (cModo=='altainventario')? true:false;
  var aProducto    = Array();
  var MUGR         = <?php echo $MUGR;?>;
  var DSTOGR       = <?php echo $DSTOGR;?>;
  var COPImpuesto  = <?php echo $COPImpuesto;?>;
  var cMetodoRedondeo = "<?php echo $MetodoRedondeo;?>";
  var cOpcionesAvanzadas = true;
  var cFaceAltaRapida    = ( cAccion=='clon' )? 'matris':'name';
  var aSubFamilia  = Array();

switch(cAccion){
case 'alta':

  enviar["IdSubFamilia"] = <?php echo CleanID($IdSubFamiliaDefecto) ?>;
  enviar["IdFamilia"]    = <?php echo CleanID($IdFamiliaDefecto) ?>;
  enviar["IdTallaje"]    = '<?php echo TALLAJE_DEFECTO ?>';//Precargado con un tallaje por defecto para autogeneracion
  enviar["UnidadMedida"] = 'und';
  enviar["IdLabHab"] = 1;
  enviar["IdMarca"] = 1;
  enviar["IdProvHab"] = 1;
  enviar["IdAlias0"] = 0;
  enviar["IdAlias1"] = 0;
  break;
case 'clon':
  <?php if($esClonar) {?>
  var esSerie         = (<?php echo $DetProducto["Serie"] ?> == 1);
  var esLote          = (<?php echo $DetProducto["Lote"] ?> == 1);
  var esVence         = (<?php echo $DetProducto["Vence"] ?> == 1);
  var esMenudeo       = (<?php echo $DetProducto["Menudeo"] ?> == 1);

  enviar["IdSubFamilia"] = <?php echo $DetProducto["IdSubFamilia"] ?>;
  enviar["IdFamilia"]    = <?php echo $DetProducto["IdFamilia"] ?>;
  enviar["IdTallaje"]    = <?php echo $DetProducto["IdTallaje"] ?>;
  enviar["UnidadMedida"] = "<?php echo $DetProducto['Und'] ?>";
  enviar["IdLabHab"]     = <?php echo $DetProducto["IdLabHab"] ?>;
  enviar["IdMarca"]      = <?php echo $DetProducto["IdMarca"] ?>;
  enviar["IdProvHab"]    = <?php echo $DetProducto["IdProvHab"] ?>;
  enviar["IdAlias0"]     = <?php echo $DetProducto["IdAlias0"] ?>;
  enviar["IdAlias1"]     = <?php echo $DetProducto["IdAlias1"] ?>;

  id("Marca").value = '<?php echo $DetProducto["Marca"] ?>';
  id("UnidadMedida").setAttribute("label",'<?php echo $DetProducto["Und"] ?>');
  id("Descripcion").setAttribute("value", '<?php echo $DetProducto["Nombre"] ?>');
  cDescripcion = '<?php echo $DetProducto["Nombre"] ?>';
  cMarca       = '<?php echo $DetProducto["Marca"] ?>';

  if(esVence) verDatosExtra('fv',true);
  if(esLote) verDatosExtra('lt',true);
  if(esMenudeo) verDatosExtra('ct',true);
  if(esSerie) verDatosExtra('ns',true);
  changeEditHeadDatos(true);
  blockfacesaltarapida(false);
  <?php }?>
  break;
}
  facesaltarapida();
  opcionesavanzadas();
  setTimeout("CargarDataSubFamilias()", 200);
//]]></script>


</hbox>

<?php

EndXul();

?>
