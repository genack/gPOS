<hbox align="center" style="zbackground-color: #0679D6;" id="boxtpvheader">
  <hbox  align="center">    
	<spacer style="width: 10px"/>
   <toolbarbutton image="img/gpos_ventas.png" label="<?php echo $NombreEmpresa.', está atendiendo';?>" class="media" oncommand="loadListHotKey()"/>
    <toolbarbutton style="background-color: transparent" id="depLista" type="menu" 
                   label="<?php echo $NombreDependienteDefecto; ?>" class="media" 
                   oncommand ="cambiaDependiente(this)" >
    <menupopup>
      <?php  echo $generadorDeDependientes; ?>
    </menupopup>     
  </toolbarbutton>
  <spacer style="width: 20px"/>
  <textbox id="cambioPassUsuario"  type="password" placeholder="Ingrese contraseña" 
           onkeypress="if (event.which == 13) cambiarUsuarioTPV()"
           collapsed="true" onblur="checkCambioPassUsuario(this.value)"/>
  <textbox id="DependienteSession" value="<?php echo $IdDependienteDefecto; ?>"
           collapsed="true"/>

</hbox>
<hbox align="center" pack="center" flex="1" style="font-size: 1.2em;" >
   <caption   id="txt-productoprogress" label="Cargando ..." collapsed="false"/>
   <progressmeter id="bar-productoprogress" mode="undetermined"  value="0"  collapsed="false"/>
</hbox>
<toolbarbutton id="NombreLocalActivo" label="<?php echo "TPV".$TipoVentaText; ?>" class="media" />
<toolbarbutton style="background-color: transparent" id="depLocalLista" type="menu" label="<?php echo $NombreLocalActivo; ?>" class="media" >
<menupopup>
  <?php  echo $generadorLocalDependientes; ?>
</menupopup>     
</toolbarbutton>              

<spacer style="width: 22px"/>
<toolbarbutton id="botonsalirtpv" image="img/gpos_tpv_salir.png" oncommand="SalirNice()" class="salir"/>
<spacer style="width: 10px"/>
</hbox>
