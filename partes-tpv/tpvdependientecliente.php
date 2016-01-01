<hbox class="box" align="center" id="boxtpvheader">
  <hbox  align="center">    
    <spacer style="width: 10px"/>
    <toolbarbutton image="img/gpos_ventas.png" label="<?php echo $NombreEmpresa.', está atendiendo';?>" class="media" oncommand="loadListHotKey()"/>
    <toolbarbutton style="background-color: transparent" id="depLista" type="menu" 
                   label="<?php echo $NombreDependienteDefecto." "; ?>" class="media" 
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

<hbox  class="box" align="center" pack="center" flex="1"  >
   <caption class="mensajetpv"  id="txt-productoprogress" label="Cargando ..." collapsed="false" />
   <!-- progressmeter id="bar-productoprogress" mode="undetermined"  value="0"  collapsed="false"/ -->
</hbox>
<toolbarbutton type="menu" id="depTipoVenta"  label="<?php echo "TPV".$TipoVentaText." ".$NombreLocalActivo." "; ?>" class="media" >
<menupopup>
  <menuitem type='radio' name='radio' label='<?php echo "TPV PERSONAL ".$NombreLocalActivo." "; ?>' 
  value='VD' oncommand ='cambiarTipoVenta(this)' <?php echo $esCheckVD; ?> id="depTipoVentaVD"/>

  <menuitem type='radio' name='radio' label='<?php echo "TPV CORPORATIVO ".$NombreLocalActivo." "; ?>' 
  value='VC' oncommand ='cambiarTipoVenta(this)' <?php echo $esCheckVC; ?> id="depTipoVentaVC"  <?php gulAdmite("B2B") ?>  />
 
</menupopup>     
</toolbarbutton>              

<!-- toolbarbutton id="depLocalLista" type="menu" label=" echo $NombreLocalActivo; " class="media" -->
<!-- menupopup -->
<!-- ?php  echo $generadorLocalDependientes;  ? -->
<!-- /menupopup -->     
<!-- /toolbarbutton -->              

<!-- spacer style="width: 22px"/-->
<toolbarbutton id="botonsalirtpv" image="img/gpos_tpv_salir.png" oncommand="SalirNice()" class="salir"/>
<!-- spacer style="width: 10px"/ -->
</hbox>
