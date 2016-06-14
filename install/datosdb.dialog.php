
<p>Para realizar la instalación, rellene los siguientes datos.</p>


<form method="post" action="?modo=EntradaDatosDB"> 

	<fieldset>
	<legend>Datos del servidor</legend>

<br>Base de datos</br>	
<label for="hostname">Hostname</label><br />
<input id="hostname" value="localhost" style="width: 40em" class="textinput" type="text" name="hostname"><br />

<br/>	
<label for="usuario">Usuario</label><br />
<input id="usuario" value="root" style="width: 20em" class="textinput"  type="text" name="usuario"><br />

<br/>	
<label for="password">Contraseña</label><br />
<input id="password" value="" style="width: 20em" class="textinput" type="password" name="password"><br />

<br/>	
<label for="password">Nueva base de datos (intentara crearla, o utilizar una existente)</label><br />
<input id="password" value="gpos_test" style="width: 20em" class="textinput" type="text" name="database"><br />
</fieldset>
<br/>	
<fieldset>
	<legend>Datos de la aplicación</legend>

<br>Configuración basica</br>

<label for="baseUrl">Dirección web de la aplicación</label><br />
<input id="baseurl" value="http://localhost/gPOS/" style="width: 40em" class="textinput" 
       onfocus="var liga = document.URL; this.value = liga.replace('install/instalar.php','');"
       type="text" name="baseurl" ><br/>

<br>Denominacion del Negocio</br>

<label for="nombreNegocio">Nombre comercial del negocio</label><br />
<input id="nombreNegocio" value="gPOS" style="width: 20em" class="textinput" type="text" name="nombreNegocio"><br />

<br>Giro del negocio</br>

<label for="giroNegocio">Tipo de negocio de la aplicación</label><br />
<select name="gironegocio" id="gironegocio">
  <option value="PINF" selected>Tecnología</option>
  <option value="BTCA">Medicamentos</option>
  <option value="BTQE">Moda/Ropa</option>
  <option value="WESL">Minorista/Mayorista</option>
</select>
<br/>

<label><input id='dbinicio' name='dbinicio' type='checkbox' >Cargar base de datos inicial</label></br>

<br/>
<label for="adminemail">Email del contacto administrador</label><br />
<input id="adminemail" value="admin@localhost" style="width: 20em" class="textinput" type="text" name="adminemail"><br />


<!-- <br>Otros</br> -->

<!-- <label for="passmodulos">Contraseña para modulos auxiliares (en blanco para desactivar)</label><br /> -->
<input id="passmodulos" value="" style="display:none;width: 20em" class="textinput" type="text" name="passmodulos">
<!-- <br/> -->


<input type="submit"  class="buttonSubmit" value="Instalar">

	</fieldset>

</form>

<script>
var liga = document.URL; 
document.getElementById("baseurl").value = liga.replace('install/instalar.php','');
</script>