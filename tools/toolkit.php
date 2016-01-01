<?php

//MERGE SORT 
//$array = array( 3, 1, 4, 1);
//$array = search_merge_sort( $array ); 

function search_merge_sort( $array ) {

    //if array is but one element, array is sorted, so return as is
    if ( sizeof ( $array ) <= 1 )
    	return $array;
    	
    //bifurcate unsorted array
    $array2 = array_splice( $array, ( sizeof( $array ) / 2 ) );
    
    //recursively merge-sort and return
    return search_merge( search_merge_sort( $array ), search_merge_sort( $array2 ) );
    
}

function search_merge( $array1, $array2 ) {
    
    //init an empty output array
    $output = array();
    
    //loop through the arrays while at least one still has elements left in it
    while( !empty( $array1 ) || !empty( $array2 ) ) 
    	
    	//one of the arrays is empty, so the last man standing wins...
    	if ( empty( $array1 ) || empty( $array2 ) )
    		$output[] = ( empty( $array2 ) ) ? array_shift( $array1 ) : array_shift( $array2 );
    		
    	//both arrays still have elements, looks like we have a showdown...
    	else 
    		$output[] = ( $array1[ 0 ] <= $array2[ 0 ] ) ? array_shift( $array1 ) : array_shift( $array2 );
    
    //pass back the output array	
    return $output;
}

function exportMysqlToCsv($query,$filename,$xtitulo=false) {
  $csv_terminated = "\n"; 
  $csv_separator  = ","; 
  $csv_enclosed   = '"'; 
  $csv_escaped    = "\\"; 
  $sql            = $query; // Gets the data from the database
  //$result         = mysql_query($sql);
  $result         = query($sql);
  $fields_cnt     = mysql_num_fields($result); 
  $schema_insert  = ''; 
  
  $xout = '"gPOS"'.$csv_terminated; 
  $xout .= $xtitulo.$csv_terminated; 
  $xout .= '""' .$csv_terminated; 
  $xout .= '""'.$csv_terminated ; 
  $xout = ($xtitulo)? $xout:'';

  for ($i = 0; $i < $fields_cnt; $i++) { 
    $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, stripslashes(mysql_field_name($result, $i))) . $csv_enclosed; 
    $schema_insert .= $l; $schema_insert .= $csv_separator; 
  } // end for 
  $out  = trim(substr($schema_insert, 0, -1)); 
  $out .= $csv_terminated; // Format the data 

  while ($row = mysql_fetch_array($result)) {
    $schema_insert = ''; 
    for ($j = 0; $j < $fields_cnt; $j++) { 
      $row[$j] = str_replace('&#038;','&',$row[$j]);
      if ($row[$j] == '0' || $row[$j] != '') { 
	if ($csv_enclosed == '') { 
	  $schema_insert .= $row[$j]; 
	} else { 
	  $schema_insert .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed; 
	} 
      } else { 
	$schema_insert .= ' '; 
      } 
      if ($j < $fields_cnt - 1) { 
	$schema_insert .= $csv_separator; 
      } 
    } // end for 
    $out .= $schema_insert; 
    $out .= $csv_terminated; 
  } // end while 
  $out = $xout.$out;

  header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
  header("Content-Length: " . strlen($out)); 
  // Output to browser with appropriate mime type, you choose 
  header("Content-type: text/x-csv"); 
  header("Content-type: text/csv"); 
  header("Content-type: application/csv");
  header("Content-Disposition: attachment; filename=$filename");
  header("Pragma: no-cache");
  header("Expires: 0");
  echo $out; 
}

function generarclaveAlfaNumeria($baselong){ 
       $basecadena="[^A-Z0-9]"; 
       return substr(preg_replace($basecadena, "", md5(rand())) . 
       preg_replace($basecadena, "", md5(rand())) . 
       preg_replace($basecadena, "", md5(rand())), 
       0, $baselong); 
} 

?>
