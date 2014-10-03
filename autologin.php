<?php

include("config/configuration.php");


ini_set("session.gc_maxlifetime",    "86400");
ini_alter("session.cookie_lifetime", "86400" );
ini_alter("session.entropy_file","/dev/urandom" );
ini_alter("session.entropy_length", "512" );

$location = $_BasePath . "xulentrar.php";

header("Location: $location");	

?>
