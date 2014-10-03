<?php

       function VolcandoXML($codigoxml,$raiz){
	 header("Content-type: text/xml");
	 echo '<?xml version="1.0" encoding="UTF-8"?>';
	 echo "<$raiz>";
	 echo $codigoxml;
	 echo "</$raiz>";	
       }

       function Traducir3XML($datos){
	 
	 if (!is_array($datos)){
	   return $datos;
	 }
	 
	 $out = "";
	 foreach ( $datos as $key => $values ){
	   if ($key and !is_numeric($key)){
	     $out .= "<$key ";
	     //if (is_array($values)){
	     $out .= Traducir2atrib($values);	
	     //} else {
	     //	$out .= $values;	
	     //}
	     $out .= "/>";	
	   }					
	 }	
	 return $out;	
       }
	
	
       function Traducir2XML($datos){
	 
	 if (!is_array($datos)){
	   return $datos;
	 }
	 
	 $out = "";
	 foreach ( $datos as $key => $values ){
	   if ($key and !is_numeric($key)){
	     $out .= "<$key>";
	     if (is_array($values)){
	       $out .= Traducir2XML($values);	
	     } else {
	       $out .= $values;	
	     }
	     $out .= "</$key>";	
	   }					
	 }	
	 return $out;	
	 
	 /*
	  * Ejemplo de uso:
	  $prueba = array();
	  $prueba["mensaje"] = array("autor"=>"Pedro", "texto"=>"hola mundo");
	  echo VolcarDatosEnXML($prueba);
	 */
       }

       function Traducir2atrib($datos){	

	 if (!is_array($datos))	return "";
	 
	 $out = "";
	 foreach ( $datos as $key => $value )
	   {
	     if ($key and !is_numeric($key))
	       {
		 $value = addslashes($value);
		 $value = str_replace("&","&amp;",$value);
		 $value = str_replace("<","&lt;",$value);
		 $value = str_replace(">","&gt;",$value);
		 $out .= "$key='$value' " ;			
	       }					
	   }	
	 return $out;
       }	

       function CleanCadenaSearch( $str ){
	 $str= str_replace("'", "&#39;", $str);
	 $str= str_replace('"', "&#34;", $str);
	 $str= str_replace(";", "&#59;", $str);
	 $str= str_replace("<", "&#60;", $str);
	 $str= str_replace(">", "&#62;", $str);
	 $str= str_replace("drop", "&#100;&#114;&#111;&#112;", $str);
	 $str= str_replace("javascript", "&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;", $str);
	 $str= str_replace("script", "&#118;&#98;&#115;&#99;&#114;&#105;&#112;&#116;", $str);
	 $str= str_replace("vbscript", "&#115;&#99;&#114;&#105;&#112;&#116;", $str);
	 return $str;
       }


	function qq($val) {
	  $val = addslashes($val);
	  $val = str_replace("\n","\\n",$val);
	  //$val = JSenquote($val);
	  return "\"$val\"";
	}
	
	function is_intval($a) {
   		return ((string)$a === (string)(int)$a);
	}

	function qminimal($a){
		if (is_intval($a)){			
			return (string)$a;			
		}	
		return qq($a);
	}

        function JSenquote($var) {
	  $ascii = '';
	  $strlen_var = strlen($var);
	  
	  for($c = 0; $c < $strlen_var; $c++) {
	    
	    $ord_var_c = ord($var{$c});
	    
	    if($ord_var_c == 0x08) {
	      $ascii .= '\b';
	      
	    } elseif($ord_var_c == 0x09) {
	      $ascii .= '\t';
	      
	    } elseif($ord_var_c == 0x0A) {
	      $ascii .= '\n';
	      
	    } elseif($ord_var_c == 0x0C) {
	      $ascii .= '\f';
	      
	    } elseif($ord_var_c == 0x0D) {
	      $ascii .= '\r';
	      
	    } elseif(($ord_var_c == 0x22) || ($ord_var_c == 0x2F) || ($ord_var_c == 0x5C)) {
	      $ascii .= '\\'.$var{$c}; // double quote, slash, slosh
	      
	    } elseif(($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)) {
	      // characters U-00000000 - U-0000007F (same as ASCII)
	      $ascii .= $var{$c}; // most normal ASCII chars
	      
	    } elseif(($ord_var_c & 0xE0) == 0xC0) {
	      // characters U-00000080 - U-000007FF, mask 110XXXXX, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	      $char = pack('C*', $ord_var_c, ord($var{$c+1})); $c+=1;
	      $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
	      
	    } elseif(($ord_var_c & 0xF0) == 0xE0) {
	      // characters U-00000800 - U-0000FFFF, mask 1110XXXX, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	      $char = pack('C*', $ord_var_c, ord($var{$c+1}), ord($var{$c+2})); $c+=2;
	      $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
	      
	    } elseif(($ord_var_c & 0xF8) == 0xF0) {
	      // characters U-00010000 - U-001FFFFF, mask 11110XXX, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	      $char = pack('C*', $ord_var_c, ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3})); $c+=3;
	      $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
	      
	    } elseif(($ord_var_c & 0xFC) == 0xF8) {
	      // characters U-00200000 - U-03FFFFFF, mask 111110XX, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	      $char = pack('C*', $ord_var_c, ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3}), ord($var{$c+4})); $c+=4;
	      $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
	      
	    } elseif(($ord_var_c & 0xFE) == 0xFC) {
	      // characters U-04000000 - U-7FFFFFFF, mask 1111110X, see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
	      $char = pack('C*', $ord_var_c, ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3}), ord($var{$c+4}), ord($var{$c+5})); $c+=5;
	      $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
	      
	    }
	  }
	  
	  return sprintf('"%s"', $ascii);
	}                    
 


?>