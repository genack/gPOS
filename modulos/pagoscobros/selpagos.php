<?php 
 require("../../tool.php");
 $modo = $_GET['modo'];

      switch ($modo) {
       case "add" :
	       $idcomprobante     = CleanID($_GET["id"]);
	       $idproveedor       = CleanID($_GET["idprov"]);
	       $rescomprobante    = CleanText($_GET["resumencbte"]);
	       $impcomprobante    = CleanText($_GET["importecbte"]);
	       $imppendiente      = CleanFloat($_GET["importepte"]);
	       $idmonedacbte      = CleanID($_GET["idm"]);
	       $cambiocomprobante = CleanFloat($_GET["cambiomda"]);
	       $cbtnAceptar       = "AltaPago()";
	       $esAgregar         = 'asociar';
	       $esPago            = "AgregarPagoProv('$esAgregar')";
	       $formapago         = CleanText($_GET["formapago"]);
	       $pendienteplan     = CleanFloat($_GET["pteplan"]);
	       $estadocbte        = CleanText($_GET["estadocbte"]);
	       $simbolomoneda     = CleanText($_GET["simbmoneda"]);
	       $codigo            = CleanText($_GET["codigo"]);
	       $idlocal           = CleanID($_GET["local"]);
	       $tipodoc           = CleanText($_GET["tipodoc"]);
	       $documento         = '';
	       $idmoneda          = 1;
	       $importepago       = 0;
	       $idpagodoc         = 0;
	       $obs               = '';
	       $fpago             = '';
	       $mora              = 0;
	       $exceso            = 0;
	       $estado            = '';
	       $idpagoprov        = 0;

	       break;

       case "edit" :
	       $idcomprobante     = CleanID($_GET["id"]);
	       $idproveedor       = CleanID($_GET["idprov"]);
	       $rescomprobante    = CleanText($_GET["resumencbte"]);
	       $impcomprobante    = CleanText($_GET["importecbte"]);
	       $imppendiente      = CleanFloat($_GET["importepte"]);
	       $idmonedacbte      = CleanID($_GET["monedacbte"]);
	       $cambiocomprobante = CleanFloat($_GET["cambiocbte"]);
	       $cbtnAceptar       = "Modificar()";
	       $esAgregar         = 'asociar';
	       $esPago            = "editardocumento('$esAgregar')";
	       $formapago         = '';
	       $pendienteplan     = CleanFloat($_GET["pteplan"]);
	       $estadocbte        = CleanText($_GET["estadocbte"]);
	       $simbolomoneda     = CleanText($_GET["simbmoneda"]);
	       $codigo            = CleanText($_GET["codigo"]);
	       $idlocal           = CleanID($_GET["local"]);
	       $tipodoc           = CleanText($_GET["tipodoc"]);
	       $documento         = CleanText($_GET["doc"]);
	       $idmoneda          = CleanID($_GET["idm"]);
	       $importepago       = CleanFloat($_GET["importep"]);
	       $idpagodoc         = CleanID($_GET["idpd"]);
	       $obs               = CleanText($_GET["obs"]);
	       $fpago             = CleanCadena($_GET["fpago"]);
	       $mora              = CleanFloat($_GET["mora"]);
	       $exceso            = CleanFloat($_GET["exceso"]);
	       $estado            = CleanText($_GET["estado"]);
	       $idpagoprov        = CleanText($_GET["idpp"]);

	       break;

      }

// $rescomprobante      = ($rescomprobante)?$rescomprobante:'';
      $cbtnSalir           = "SalirToComprobante()";
      $ImporteComprobante  = $impcomprobante; 
      $ImportePendiente    = $imppendiente;
      $SimboloComprobante  = $simbolomoneda;
      $tipoproveedor       = ($tipodoc == 'AlbaranInt')? 'Interno':'Externo';

      include("xulpagos.php");
?>