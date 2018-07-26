#!/usr/bin/env php
<?php

function Usage() {
	global $argv;
	
	print( "Usage: " . $argv[0] . " app_dir phar_path [main_script]\n" );
}

if ( count( $argv ) < 3 )
{
	Usage();
	exit( 1 );
}

$appDir 		= $argv[1];
$pharPath 		= $argv[2];
$pharPathInfo 	= pathinfo( $pharPath );
$mainScript 	= isset( $argv[3] ) ? $argv[3] : "index.php";

// Validate Input Params
switch( false )
{
	case is_dir( $argv[1] ):
		die( 'Invalid Application Directory.' );
	case isset( $pharPathInfo['extension'] ) && $pharPathInfo['extension'] == 'phar':
		die( 'Invalid Phar Extension' );
	case is_dir( $pharPathInfo['dirname'] ):
		die( 'Phar Directory Not Exixts.' );
	case is_writable($pharPathInfo['dirname']):
		die( 'Phar Directory Not Writable.' );
	case file_exists( $appDir.DIRECTORY_SEPARATOR.$mainScript ):
		die( 'Main Script Not Exists' );
}

$phar	= new Phar(
	$pharPath, 
	FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, 
	$pharPathInfo['basename']
);
$phar->buildFromDirectory( $appDir );
$phar->setStub( $phar->createDefaultStub( $mainScript ) );

//$phar->extractTo('/tmp');
