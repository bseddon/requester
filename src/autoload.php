<?php

function requestor_autoload( $classname )
{
	$prefix = 'lyquidity\\';
	if ( strpos( $classname, $prefix ) !== 0 ) return false;
	$filename = substr( $classname, strlen( $prefix ) ) . '.php';
	if ( ! file_exists(  __DIR__ . "/$filename" ) ) return false;
	// $filename = "$classname.php";
	require_once  __DIR__ . "/$filename" ;
}

spl_autoload_register( 'requestor_autoload' );
