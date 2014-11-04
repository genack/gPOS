<?php
include("tool.php");

//SimpleAutentificacionAutomatica("visual-xul");

//Valida Seccion
$User  = getSesionDato("IdUsuario");
$Local = getSesionDato("IdTienda");
if ( $Local && $User )
  header("Location: xulgpos.php");

$_motivoFallo  = "";
$NombreEmpresa = $_SESSION["GlobalNombreNegocio"];  

//$modo = $_REQUEST["modo"];
$modo = isset($_REQUEST["modo"]) ? $_REQUEST["modo"] : NULL;//Modificado 2012

$_log = "";

function AddLog($text){
	global $_log;
	$_log = $_log . $text . "\n";
}
 
AddLog("Empieza modo es '$modo'");
 

switch($modo){
    case "avisoUsuarioIncorrecto":
	case "login-usuario":
	case "login-user"://desde la TPV
	case "login-tpv":
	case "login-admin":

		$login = CleanLogin( isset($_POST["login"]) ? $_POST["login"] : NULL );//Modificado 2012
		$pass =  CleanPass( isset($_POST["pass"]) ? $_POST["pass"] : NULL );//Modificado 2012

		AddLog("Cargando login/pass '$login/$pass'");

		$user = true;
		if ($login and $pass){
		  $id = identificacionUsuarioValidoMd5($login,md5($pass));
		  if ($id){		
		    RegistrarUsuarioLogueado($id);
		    AddLog("Se loguea id'$id'");			
		    
		    if($modo == "login-admin") {
		      AddLog("Se redirigie a xulmenu...");
		      session_write_close();
 		      header("Location: xulgpos.php");
		      exit;

		    } else {
		      session_write_close();

		      if (Admite("TPV") )
			header("Location: xulgpos.php?t=on&r=" . rand(900000,999999));
		      else 
			header("Location: xulgpos.php");

		      exit;
		    }
		    exit();	
		  } else {
		    $fail = "Nombre ('$login') o password ('$pass') incorrectas: $_motivoFallo";
		    AddLog("Falla identificacion.");	
		  }
		}		
		break;	
	case "tiendaDesconocida":
	case "login-local":

	default:
	  $login = CleanLogin( isset($_POST["login"]) ? $_POST["login"] : NULL );//Modificado 2012
	  $pass =  CleanPass( isset($_POST["pass"]) ? $_POST["pass"] : NULL );//Modificado 2012

	  $user = false;
	  if ($login and $pass){
	    $id = CleanID(identificacionLocalValidaMd5($login,md5($pass)));
	    if ($id and $id != 0){		
	      RegistrarTiendaLogueada($id);
	      RegistrarIGVTienda($id);
	      RegistrarGarantiaComercial($id);
	      RegistrarVigenciaPresupuesto($id);
	      RegistrarAlmacenCentral($id);
	      RegistrarMUTienda($id);
	      RegistrarMoneda();
	      session_write_close();
	      header("Location: xulentrar.php?modo=login-usuario");
	      exit();	
	    } else {
	      $fail = "Nombre ('$login') o password ('$pass') incorrectas: $_motivoFallo";	
	    }
	  }
	  break;
}

StartXul("Login gPOS");


?>
<box flex="1" style="background-image: url(img/gpos_bg_login.jpg); background-repeat:no-repeat; background-color:#2F6496;">
  <spacer flex="1" />
  <vbox  >
    <spacer flex="1"  />
    
    <spacer flex="1" />		
    
    <vbox style="width: 425px; height: 180px; background-image: url(img/gpos_login_back.png); background-repeat:no-repeat; ">
      <vbox >
	
	<description style="font-weight: bold;color: #fff;margin-left: 20px; margin-top: -5px;font-size:13px;">
	  <html:br/>
	  <?php echo $NombreEmpresa;?>
	</description>
      </vbox>
      <spacer style="height:0.4em"/>
      <grid>
	<columns>
	  <column style="width: 170px"/>
	  <column/>
	  <column flex="1"/>
	</columns>
	<rows>
	  <row>
	    <hbox><spacer flex="1" style="width: 40px"/>
	    <description style="color: #fff; font-size:12px;">
	      <?php if ($user)
		echo _("Usuario");
		else					
		echo _("Local");
	      ?>						
	    </description>
	    </hbox>
	    <textbox id='nombrelocal' type="normal" style="border:px solid; height:1.8em;"
		     onkeypress="if (event.which == 13) document.getElementById('passlocal').focus()"/>
	  </row>
      <spacer style="height:0.1em"/>
	  <row>
	    <hbox><spacer flex="1"/>
	    <description style="color: #fff; font-size:12px;">
	      <?php	echo _("Contraseña");?>													
	    </description>
	    </hbox>
            <?php if($user){?>
	    <textbox id='passlocal' onkeypress="if (event.which == 13) SaltaLogin('login-tpv')" 
		     type='password'  style="border:0px solid; height:1.8em;"/> 
            <?php } else { ?>
	    <textbox id='passlocal' onkeypress="if (event.which == 13) SaltaLogin('login-local')" 
		     type='password'  style="border:0px solid; height:1.8em;"/> 
            <?php } ?>
	  </row>
           <spacer style="height:0.4em"/>
	  <row  align="start">
	    <description>
	      <?php
		if ($user)
		echo '<image style="width: 118px; height: 80px" src="img/gpos_user.png" />';
		else
		echo '<image style="width: 118px; height: 80px" src="img/gpos_store.png" />';
	      ?>
	    </description>

	    <hbox flex='1' >					
	      <?php
		if ($user) {
		echo "<button style='color: #505050; font-size:12px; border:0px solid;
border-radius:2px;' label=\"". _("TPV") . "\" oncommand=\"SaltaLogin('login-tpv')\" />";
		echo "<button style='color: #505050; font-size:12px; border:0px solid;
border-radius:2px;'  label=\"". _("Admin") ."\" oncommand=\"SaltaLogin('login-admin')\"/>";
		} else {
		echo "<button flex='1' style='color: #505050; font-size:12px; border:0px solid;
border-radius:2px;'  label=\"". _("Entrar") ."\" oncommand=\"SaltaLogin('login-local')\" />";
		}                          						
	      ?>
	    </hbox>
	  </row>					
	</rows>												
      </grid>			
    </vbox>
    <hbox>
      <button class="borderless" label="Cambio empresa" onclick="VisitarLoginEmpresa()" collapsed="true"/>
      <spacer flex="1"/>
    </hbox>		
    <spacer flex="1"/>
    <!-- Es de buen nacido el ser agradecido -->
    <!-- /Es de buen nacido el ser agradecido -->
    <spacer flex="1"/>	 
  </vbox>
  <spacer flex="1"/>	
</box>

<box collapsed="true" hidden="true">
  <html:form  collapsed="true" hidden="true"  
	      id="form-enviar" action="xulentrar.php" method="post">
    <html:input collapsed="true" hidden="true" 
		id="form-empresa" name="login" type="hidden" value=""/>
    <html:input collapsed="true" hidden="true" 
		id="form-pass" name="pass"  type="hidden" value=""/>
    <html:input collapsed="true" hidden="true" 
		id="form-modo" name="modo" type="hidden" value=""/>
  </html:form>
</box>
<script><![CDATA[

function id(nombreEntidad){
return document.getElementById(nombreEntidad);
}


var findex = 1;
function SaltaLogin(pasoActual){
  var local = document.getElementById("nombrelocal").value;
  var pass = document.getElementById("passlocal").value;
  id("form-empresa").value = local;
  id("form-pass").value = pass;  
  id("form-modo").value = pasoActual;  
  id("form-enviar").submit();       
}


function VisitarLoginEmpresa(){
	document.location = "login.php";
}


// Corregimos el foco para situarse en el primer input box
var ventanamaestra = document.getElementById("login-gpos");
ventanamaestra.setAttribute("onload","FixFocus()");

function FixFocus(){
	document.getElementById("nombrelocal").focus();
}



]]></script>
<?php

EndXul();

?>
