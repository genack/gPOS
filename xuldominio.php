<?php 
include("tool.php"); 
StartXul("Xul remoto"); 
echo "<script>parent.document.getElementById('esDominio').value = parseInt(1);</script>";
EndXul();
?>
