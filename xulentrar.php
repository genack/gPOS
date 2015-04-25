<?php
include("tool.php");

//SimpleAutentificacionAutomatica("visual-xul");

//Valida Seccion
$User        = getSesionDato("IdUsuario");
$Local       = getSesionDato("IdTienda");

if ( $Local && $User )
  header("Location: xulgpos.php");

$_motivoFallo  = "";
$NombreEmpresa = $_SESSION["GlobalNombreNegocio"];  

//$modo = $_REQUEST["modo"];
$modo = isset($_REQUEST["modo"]) ? $_REQUEST["modo"] : NULL;//Modificado 2012
$_log = "";
$keypressnombre = "document.getElementById('passlocal').focus()";
$xtextrow = "";

function AddLog($text){
	global $_log;
	$_log = $_log . $text . "\n";
}
 
AddLog("Empieza modo es '$modo'");
 

switch($modo){
        case "tiendaDesconocida":
	case "login-local":

	  $ckAccess       = getSesionDato("LocalAccess");
	  $valckAccess    = ( isset($ckAccess['js']) )? $ckAccess['js']:"";
	  $login          = CleanLogin( isset($_POST["login"]) ? $_POST["login"] : NULL );
	  $pass           = CleanPass( isset($_POST["pass"]) ? $_POST["pass"] : NULL );
	  $keypressnombre = "ckLocalAccess()";
	  $xtextrow       = "nopasswd_local";
 	  $user           = false;

	  //Un solo local?
	  if( count($ckAccess) == 2 )
	    foreach ($ckAccess as $ckkey => $ckvalue) { if( $ckkey != 'js') $login = $ckkey; }

	  //Valida login
	  if ( !isset( $ckAccess[ $login ] ) ) break;

	  $keyAccess = explode(":", $ckAccess[ $login ]);//idlocal:admitepasswd
	  $pass      = ( $keyAccess[1] == '0')? '~':$pass;

	  if ($login and $pass){
	    $id = ( $keyAccess[1]=='1')? CleanID(identificacionLocalValidaMd5($login,md5($pass))):$keyAccess[0];
	    if ($id and $id != 0){	
	      RegistrarTiendaLogueada($id);
	      RegistrarIGVTienda($id);
	      RegistrarGarantiaComercial($id);
	      RegistrarVigenciaPresupuesto($id);
	      RegistrarAlmacenCentral($id);
	      RegistrarMUTienda($id);
	      RegistrarValuacionPrecioTPV($id);
	      RegistrarMoneda();
	      RegistrarKeySyncTPV();	
	      session_write_close();
	      header("Location: xulgpos.php");
	      exit();	
	    } else {
	      $fail = "Nombre ('$login') o password ('$pass') incorrectas: $_motivoFallo";	
	    }
	  }
	  break;
        case "avisoUsuarioIncorrecto":
	case "login-usuario":
	case "login-user"://desde la TPV
	case "login-tpv":
	case "login-admin":
	default:
		$login    = CleanLogin( isset($_POST["login"]) ? $_POST["login"] : NULL );
		$pass     = CleanPass( isset($_POST["pass"]) ? $_POST["pass"] : NULL );
		$valckAccess = "";

		AddLog("Cargando login/pass '$login/$pass'");

		$user = true;
		if ($login and $pass){

		  $id = identificacionUsuarioValidoMd5($login,md5($pass));
		  if ($id){		
		    RegistrarUsuarioLogueado($id);
		    
		    AddLog("Se loguea id'$id'");			
		    
		    AddLog("Se redirigie a xulmenu...");
		    session_write_close();
		    
		    header("Location: xulentrar.php?modo=login-local");
		    exit;

		  } else {
		    $fail = "Nombre ('$login') o password ('$pass') incorrectas: $_motivoFallo";
		    AddLog("Falla identificacion.");	
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
	
	<description style="font-weight: bold;color: #fff;margin-left: 20px; margin-top: -7px;font-size:13px;">
	  <html:br/>
	  <?php echo $NombreEmpresa;?>
	</description>
      </vbox>
      <spacer style="height:0.4em"/>
      <grid>
	<columns>
	  <column style="width: 120px"/>
	  <column/>
	  <column flex="1"/>
	</columns>
	<rows>
            <row id="xtextrow" class="<?php echo $xtextrow;?>">
	    <hbox><spacer flex="1" style="width: 40px"/>
	    </hbox>
	    <textbox id='nombrelocal' type="normal" 
	    placeholder="<?php if ($user) echo _("Tu usuario gPOS"); else echo _("Tu local gPOS");?> "
	    onkeypress="if (event.which == 13) <?php echo $keypressnombre;?>" onblur="<?php echo $keypressnombre;?>"/>
	  </row>
      <spacer style="height:0.1em"/>
	  <row style="height:.1em">
	    <hbox><spacer flex="1"/>
	    </hbox>
            <?php if($user){?>
	    <textbox id='passlocal' onkeypress="if (event.which == 13) SaltaLogin('login-usuario')" 
		     type='password' placeholder="Tu contraseña gPOS"/> 
            <?php } else { ?>
	    <textbox id='passlocal' onkeypress="if (event.which == 13) SaltaLogin('login-local')" 
		     type='password' placeholder="Tu contraseña gPOS" collapsed="true"/> 
            <?php } ?>
	  </row>
           <spacer style="height:0.4em"/>
	  <row  align="start">
	    <description>
	      <?php
		if ($user)
		  echo '<image style="width: 118px; height: 80px;margin-top:-1.2em;" src="img/gpos_user.png" />';
		else
		  echo '<image style="width: 118px; height: 80px;margin-top:-1.2em;" src="img/gpos_store.png" />';
	      ?>
	    </description>

	    <hbox flex='1' >					
	      <?php
		if ($user) {
		  echo "<button  flex='1' style='color: #505050; font-size:14px; border:0px solid;
border-radius:2px;'  label=\"". _("Inicia sesión") ."\" oncommand=\"SaltaLogin('login-usuario')\"/>";
		} else {
		echo "<button flex='1' style='color: #505050; font-size:14px; border:0px solid;
border-radius:2px;'  label=\"". _("Accede") ."\" oncommand=\"ckLocalAccess()\" />";
		}                          						
	      ?>
	    </hbox>
	  </row>					
	</rows>												
      </grid>			
    </vbox>
    <hbox>
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

var findex      = 1;
function SaltaLogin(pasoActual){
  var local = document.getElementById("nombrelocal").value;
  var pass = document.getElementById("passlocal").value;
  id("form-empresa").value = local;
  id("form-pass").value = pass;  
  id("form-modo").value = pasoActual;  
  id("form-enviar").submit();       
}
var aLocalAccess;

function ckLocalAccess2Array(){
   var txtLocalAccess = '<?php echo $valckAccess; ?>';
   aLocalAccess = txtLocalAccess.split('~');
}

function ckLocalAccess(){

   var aKey,xdisplay,xclass;

   for (var y=0; y<aLocalAccess.length; y++) {
      aKey = aLocalAccess[y].split(':');

      if( aKey[0] == id('nombrelocal').value ){

	  id("xtextrow").setAttribute("class",( aKey[1] == 1)? "":"nopasswd_local");//
	  id("passlocal").setAttribute("collapsed",( aKey[1] == 0));
	  if ( aKey[1] != 0){ 
	      id('passlocal').focus();
	      if( id("passlocal").value == "" )
	         return;
	      }
	  return SaltaLogin('login-local');
      }
   }
   id('nombrelocal').value = '';
   id("xtextrow").setAttribute("class","nopasswd_local");//
   id("passlocal").setAttribute("collapsed",true);
}

function VisitarLoginEmpresa(){
	document.location = "login.php";
}


// Corregimos el foco para situarse en el primer input box
var ventanamaestra = document.getElementById("login-gpos");
ventanamaestra.setAttribute("onload","FixFocus()");

function FixFocus(){
        ckLocalAccess2Array();
	document.getElementById("nombrelocal").focus();
}

]]></script>
<?php

EndXul();

?>
