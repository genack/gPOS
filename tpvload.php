<?php
$t       = $_GET["t"];
$espopup = $_GET["espopup"];
?>
<script>
   var xurl = "tpvmodular.php?modo=tpv&t=<?php echo $t;?>&espopup=<?php echo $espopup;?>&r=" + Math.random();
</script>

<html>
<meta charset="utf-8">
<style type="text/css">
       div#xBox {
            /*background-image: url("../img/gpos_cargandoTPV.png");*/
            background-repeat: no-repeat;
            background-position: center top; 
	    background-size: 339px 342px;
            width: calc(100%);
            height: 80%;
            margin-top: 6%;
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
            margin-top: -6em;
            border: solid 0px blue;
            margin-left: auto;
            margin-right: auto;
            cursor:pointer;
            text-align:center;
            font-size:12px;
            font-family: sans-serif;

            }
       body {
            /*background-image:  url("../img/gpos_bg_login.jpg")*/;
            background-repeat: repeat-x;
            background-color:  #D7D7D7;
       }


.spinner {
       margin: 100px auto;
       width: 40px;
       height: 40px;
       position: relative;
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
	 background-color: #333;
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
<body>
  <div id="xBox" >
    <div id="xPOS"></div>
    <div class="spinner">
      <div class="dot1"></div>
      <div class="dot2"></div>
    </div>
    <div id="xFox">un momento...</div>
  </div>
</body>

       <script>//<![CDATA[
        setTimeout(function(){ loadDominio() }, 400);
        function loadDominio(){ 
	location = xurl; 
        }
       //]]></script>

</html>

