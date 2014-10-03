<?php

include("../tool.php");

//page-break-before: always;

$text=<<<HEREDOC
<table width="200" CellPad="0" CellSpace="0" style="font-family:monospace,courier,sans;">
<tr><td style="font-size: 12px" colspan='2'><nobr>Precio: <big><b>%precio%</b></big></nobr></tr>
<tr><td><img src="<?php echo $_BasePath; ?>modulos/barcode/%urlbarcode%"></td><td style="font-size: 12px"><nobr>M: <big><b>%color%</b></big><br>D: %talla%</nobr></td></tr>
</table>
HEREDOC;

$codigo = base64_encode($text);

$sql = "UPDATE ges_templates SET Codigo='$codigo' WHERE Nombre = 'Etiqueta'";

query($sql);

echo $sql;

//<!--<tr><td style="font-size: 11px">Referencia:</td><td style="font-size: 11px">%referencia%</tr>-->

echo "<hr>";

echo $text;


?>
