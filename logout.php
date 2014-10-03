<?php

session_start();
session_unset();
session_destroy();

$AUTOLOGIN      = isset($_SESSION["DireccionAutologin"]) ? $_SESSION["DireccionAutologin"] : NULL;
$redireccionweb = ($AUTOLOGIN)? $AUTOLOGIN : "xulentrar.php";

/**
setcookie("auth","el usuario ha salido",time()-1); 

foreach ($_SESSION as $key => $value) {
	$_SESSION[$key] = false;
	session_unregister($key);	
}

session_unset();

setcookie("auth","el usuario ha salido",time()-1); 

$cookiesSet = array_keys($_COOKIE);
for ($x = 0; $x < count($cookiesSet); $x++) {
   if (is_array($_COOKIE[$cookiesSet[$x]])) {
       $cookiesSetA = array_keys($_COOKIE[$cookiesSet[$x]]);
       for ($c = 0; $c < count($cookiesSetA); $c++) {
           $aCookie = $cookiesSet[$x].'['.$cookiesSetA[$c].']';
           setcookie($aCookie,"",time()-1);
       }
   }
   setcookie($cookiesSet[$x],"",time()-1);
}
**/
?>

<html>
<head> 
<meta charset="utf-8">﻿
<style type="text/css">
       body {
            background-image:  url("img/gpos_bg_login.jpg");
            background-repeat: no-repeat;
            background-color:  #EDEBE4;
       }
.spinner {

       position: absolute;
       top: 45%; 
       left: 49%; 
       width: 40px;
       height: 40px;
       text-align: center;
  
	 -webkit-animation: rotate 2.0s infinite linear;
       animation: rotate 2.0s infinite linear;
 }

.dot1, .dot2 {
       width: 60%;
       height: 60%;
       display: inline-block;
       position: absolute;
       top: 0;
	 background-color: #F7F7F7;
	 border-radius: 100%;
  
	 -webkit-animation: bounce 2.0s infinite ease-in-out;
       animation: bounce 2.0s infinite ease-in-out;
       }

.dot2 {
       top: auto;
       bottom: 0px;
	 -webkit-animation-delay: -1.0s;
	 animation-delay: -1.0s;
       }

@-webkit-keyframes rotate { 100% { -webkit-transform: rotate(360deg) }}
@keyframes rotate { 100% { transform: rotate(360deg); -webkit-transform: rotate(360deg) }}

@-webkit-keyframes bounce {
  0%, 100% { -webkit-transform: scale(0.0) }
  50% { -webkit-transform: scale(1.0) }
}

@keyframes bounce {
  0%, 100% { 
  transform: scale(0.0);
    -webkit-transform: scale(0.0);
  } 50% { 
  transform: scale(1.0);
    -webkit-transform: scale(1.0);
  }
}

</style>
</head> 
<body>
       <div class='spinner'>
        <div class='dot1'></div>
        <div class='dot2'></div>
       </div>
</body>
<script>
  setTimeout("document.location='<?php echo $redireccionweb ?>'",400);
</script>
</html>
