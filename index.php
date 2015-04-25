<?php

if (!file_exists('config/configuration.php'))
    header("Location: install/instalar.php");

include('config/baseurl.php');
include('include/browser.inc.php');
$broswer = new browser();
$htmlOut = '<html>
<style type="text/css">
       div#xBox {
            background-image: url("img/gpos_nofirefox.png");
            background-repeat: no-repeat;
            background-position: center top; 
            width: calc(100%);
            height: 80%;
            margin-top: 10%;
            margin-left: auto;
            margin-right: auto;
            }
       div#xPOS {
            width: calc(50%);
            height: 7em;
            margin-top: 0%;
            border: solid 0px red;
            margin-left: auto;
            margin-right: auto;
            cursor:pointer;
            }
       div#xFox {
            width: calc(50%);
            height: 6em;
            margin-top: 5em;
            border: solid 0px blue;
            margin-left: auto;
            margin-right: auto;
            cursor:pointer;
            }
       body {
            background-image:  url("img/gpos_nofirefox_bg.png");
            background-repeat: repeat-x;
            background-color:  #EDEBE4;
       }
</style>
<body>
  <div id="xBox" >
    <div id="xPOS" onclick="javascript:location.href=\'http://genack.net/gpos\'"></div>
    <div id="xFox" onclick="javascript:location.href=\'https://www.mozilla.org/es-ES/firefox/desktop/\'"></div>
  </div>
</body>
</html>';

if ($broswer->isFirefox()){

  //return header("Location: xulentrar.php");
  echo "<html>
       <head> 
       <meta charset='utf-8'>﻿
        <style type='text/css'>
          body {
                background-image:  url('img/gpos_bg_login.jpg');
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

       <iframe id='checkxuldominio' src='xuldominio.php' style='display:none'></iframe>
       <input id='esDominio' value='0' style='display:none'/>

       <script>//<![CDATA[
        function checkDominio(){ 
          if( xdominio.value == 0 && xcheck ){
               xcheck = false;
               return setTimeout('checkDominio()', 2000);
          }
          location = ( xdominio.value == 1)? '".$_BasePath."xulentrar.php':'".$_BasePath."xulremoto/'; 
        }

        var xdominio   = document.getElementById('esDominio');
        xdominio.value = 0;
        var xcheck     = true;
        setTimeout('checkDominio()', 600);
        //]]></script>
       </body>";
} else 
  echo $htmlOut;

?>
