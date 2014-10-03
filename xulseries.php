<?php
SimpleAutentificacionAutomatica("visual-xulframe");
header("Content-type: application/vnd.mozilla.xul+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';
?>

<window id="NumSerie" title="<?php echo "gPOS // NS - ".$producto; ?>" 
xmlns:html="http://www.w3.org/1999/xhtml"
xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/numerosdeserie.js" />
<?php
 $unid       = ($validarSeries==1)? $cantidadNS:$unidades; 
 $unid       = ($Comprar)? 0:$unid; 
 $btnexittxt = ($btnexittxt)? $btnexittxt:" Volver Comprobantes ";
 $btnexitcmd = ($btnexitcmd)? $btnexitcmd:" SalirNStoComprobante()";
 $vtitulo    = ($vtitulo)? "true":"false";
 $prodbase   = ($prodbase)? "true":"false";
 $fecharead  = ($fecharead)? "true":"false";
 $eslTBox    = ($escKBox)? false:true;
 $eslTBoxtx  = ($eslTBox)? 'false':'true';
 $escKBoxtx  = ($escKBox)? 'false':'true';
?> 

<script>//<![CDATA[
var cIdPedidoDet = "<?php echo $idpedidodet;?>";
var n            = "<?php echo $unid;?>";
var cIdLocal     = "<?php echo $idlocal;?>";
var cOpEntrada   = "<?php echo $opentrada;?>";
<?php if($Agregar) echo "nsAdd=true;";?>
//]]></script>

<popup  id="oe-date-picker-popup" position="after_start" oncommand="RecibeCalendario( this )" value=""/>
<popup id="accionesNS" class="media">
  <menuitem class="menuitem-iconic" image="img/remove16.gif" label="Quitar"  oncommand="quitarns()"/>
  <!-- <menuitem class="menuitem-iconic" image="img/remove16.gif" label="Modificar Fecha Garantia"  oncommand="modifecha()"  /> -->
</popup>

<vbox  align="center"  pack="center" collapsed="<?php echo $vtitulo; ?>">
  <spacer style="height:20px"/>
  <caption label="<?php echo $tituloCart; ?>" style="font-size: 14px;font-weight: bold;"/>
  <spacer style="height:10px"/>
  <caption label="<?php echo $producto; ?>" style="font-size: 12px;font-weight: bold;"/>
</vbox>
<spacer style="height:10px"/>

<vbox  align="center"  pack="center">
  <spacer flex="1"></spacer>
  <groupbox>
    <hbox>
      <grid>
	<rows>
	  <row collapsed="<?php echo $esGarantia;?>">
	    <caption label="Garantía "/>
	    <hbox>
              <datepicker id="fec_garantia" type="popup" 
			  value="<?php echo $Garantia;?>" readonly="<?php echo $fecharead;?>"/>
	    </hbox>
	  </row>
	  <row>
	    <caption label="Acciones"/>
	    <hbox>
		<radiogroup id="radio_group" oncommand="limpiar_caja()" > 
		  <hbox>
		    <radio label="Agregar" disabled="<?php echo $valor; ?>" 
		    selected="<?php echo $selAgregar; ?>" />
		    <radio label="Quitar" disabled="<?php echo $valor; ?>" />
		    <radio label="Buscar" selected="<?php echo $selBuscar; ?>" />
		  </hbox>
		</radiogroup>
	      </hbox>
	  </row>
          <row collapsed="<?php echo $eslTBoxtx; ?>">
	    <caption label="NS"/>
	    <textbox id="lasseries" value=" " hidden="true"/>
	    <textbox id="numerodeserie"  
		     onkeypress="if (event.which == 13) 
		     acciones('<?php echo $unidades; ?>',<?php echo $id; ?>,
		              '<?php echo $validarSeries; ?>',event);
                     return soloAlfaNumericoSerie(event);"/>
	  </row>

          <row collapsed="<?php echo $escKBoxtx; ?>">
	    <caption label="Select NS"/>
	    <textbox id="ckserie" onkeypress="if (event.which == 13) selcKBoxSerie()"/>
	  </row>
	</rows>
      </grid>
    </hbox>
    <listbox id="lista" contextmenu="accionesNS" >
      <listhead>
	<listheader label="#" style="font-style:italic;" />
	<listheader label="NS"/>
      </listhead>
      <listcols>
	<listcol/>
	<listcol flex="1"/>
      </listcols>
      <?php 
	//List  
	if($eslTBox)
	  if (count($series)>0) 
	    { 
	      $series_producto = explode(";",$series);
	      for($i=0;$i<count($series_producto);$i++)
		{
		  if($series_producto[$i]!="")
		    {
		      $item = $i+1;
		      $detalle_serie = explode(",",$series_producto[$i]);
		      echo '<listitem>';
		      echo ' <listcell label="'.$item.'" />';
		      echo ' <listcell label="'.$detalle_serie[0].'" />';
		      echo '</listitem>';
		    }
		}
	    }
	//Select check box   
	if($escKBox)
	  if (count($series)>0) 
	    { 
	      $series_producto = explode(";",$series);
	      for($i=0;$i<count($series_producto);$i++)
		{
		  if($series_producto[$i]!="")
		    {
		      $item = $i+1;
		      $detalle_serie = explode(",",$series_producto[$i]);
		      echo ' <listitem type="checkbox" checked="false" label="'.$detalle_serie[0].'"  oncommand="selSeriesKardexcarrito(this.label,this,false)"/>';
		    }
		}
	    }
      ?>
    </listbox>
    <hbox   flex="1">
      <grid>
	<rows >

	  <row  collapsed="<?php echo $eslTBoxtx;?>">
	    <description value="NS Listados:"/>
	    <caption id="totalNS" label="<?php echo $item; ?>"/>
	  </row>

	  <row collapsed="<?php echo $escKBoxtx;?>">
	    <description value="NS Seleccionados:"/>
	    <caption id="totalSelNS" label="0"/>
	  </row>

	  <row>
	    <vbox></vbox>

	    <vbox flex="1" >

	      <hbox flex="1" pack="center" style="width:19.5em;">

		<?php if($valor=="true"){ ?>

		<button id="btncancelar"  image="img/gpos_volver.png" flex="1" class="media" 
			label="<?php echo $btnexittxt; ?>"  style="font-size: 11px;font-weight: bold;"
			oncommand="<?php echo $btnexitcmd; ?>" />

		<?php } else {?>

		<button id="btnaceptar" image="img/gpos_compras.png" label=" <?php echo $btnComprar;?>" 
			oncommand="actualizar_carrito(<?php echo $id.",'".$unidades."',
                                                      '".$modo."','".$idpedidodet."','".$fila."',
                                                      '".$trasAlta."'";?>)"/>

		<button id="btncancelar" image="img/gpos_vaciarcompras.png" label =" Cancelar" 
			oncommand="<?php echo $btnCancelar; ?>" />
		<?php }?>
	      </hbox>
	    </vbox>
	  </row>
	</rows>


      </grid>
    </hbox>
  </groupbox>	

</vbox>

<script>//<![CDATA[
var aSeries  = new Array();
var alSeries = new Array();
var nSeries  = 0;

<?php echo $escKBoxinit;?>

//]]></script>

</window>
