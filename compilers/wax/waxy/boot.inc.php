<?
	if ( version_compare(PHP_VERSION,'5.4')<0 )
    	throw new \Exception( 'Waxy requires PHP 5.4 or above' );
	include_once __DIR__ . '/src/Compiler.class.php';
?>
