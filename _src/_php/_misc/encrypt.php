<?php

function concat($length)
{
	//$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	
	$chars = "0123456789";	
	$str = "";
	$size = strlen( $chars );
	for( $i = 0; $i < $length; $i++ ) {
		$str .= $chars[ rand( 0, $size - 1 ) ];
	}

	return $str;
}

function encrypt($str)
{
	return concat(6).$str.concat(6);
}
/*
$temp = encrypt("123");

echo "ENCRYPTED1= ".$temp."<br><br>";

include("decrypt.php");

echo "DECRYPTED1= ".decrypt($temp);
*/
?>