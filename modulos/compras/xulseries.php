<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Productos Series',$predata="",$css='');
?>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/compras/numerosdeserie.js?v=3.1" />
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
$item        = 0;
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
  <menuitem class="menuitem-iconic" image="<?php echo $_BasePath; ?>img/gpos_barcode.png" 
            label="Editar"  oncommand="quitareditarns()"/>
  <menuitem class="menuitem-iconic" image="<?php echo $_BasePath; ?>img/gpos_cancelar.png" 
            label="Quitar"  oncommand="quitarns()"/>
</popup>

<vbox  align="center"  pack="center" collapsed="<?php echo $vtitulo; ?>" class="box">
  <spacer style="height:20px"/>
  <caption class="box" label="<?php echo $tituloCart; ?>" />
  <spacer style="height:10px"/>
  <caption class="box" label="<?php echo $producto; ?>" />
  <spacer style="height:10px"/>
</vbox>

<vbox  align="center"  pack="top" class="box" flex="1">

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
	    <textbox id="numerodeserie"  onfocus="select()"
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
	    <caption  class="xtotal"  id="totalNS" label="<?php echo $item; ?>"/>
	  </row>

	  <row collapsed="<?php echo $escKBoxtx;?>">
	    <description value="NS Seleccionados:"/>
	    <caption class="xtotal" id="totalSelNS" label="0"/>
	  </row>

	  <row>
	    <vbox></vbox>

	    <vbox flex="1" >

	      <hbox flex="1" pack="center" >

		<?php if($valor=="true"){ ?>

		<button id="btncancelar"  
                        image="<?php echo $_BasePath; ?>img/gpos_volver.png" 
                        flex="1" class="media btn" 
			label="<?php echo $btnexittxt; ?>"  
			oncommand="<?php echo $btnexitcmd; ?>" />

		<?php } else {?>

		<button id="btnaceptar" image="<?php echo $_BasePath; ?>img/gpos_compras.png"
                        label=" <?php echo $btnComprar;?>" class="btn"
			oncommand="actualizar_carrito(<?php echo $id.",'".$unidades."',
                                                      '".$modo."','".$idpedidodet."','".$fila."',
			                              '".$trasAlta."',".$esPopup;?>)"/>

		<button id="btncancelar" class="btn"
                        image="<?php echo $_BasePath; ?>img/gpos_vaciarcompras.png" 
                        label =" Cancelar" 
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
