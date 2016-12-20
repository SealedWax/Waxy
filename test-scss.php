<?
	// Define the folder path
	$path = str_replace('\\', '/',dirname(__FILE__)).'/';
	define( 'PATH', $path );
	
	// Leafo Sassy CSS (SCSS) compiler for PHP
	require_once 'compilers/leafo/scss.inc.php';
	// Waxy compiler for PHP
	require_once 'compilers/wax/waxy/boot.inc.php';
	
	// Setup compiler(s)
	$scss	= new Leafo\ScssPhp\Compiler();
	$waxy	= new Wax\Scss\Waxy\Compiler();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="eng">
	<head>
		<title>Sealed Wax® - Waxy Expansion</title>
		<meta name="description" content="This is the new CSS Rebel Selector with Pseudo-Events & Attribute Property." />
		
		<script type="text/javascript" src="js/jquery/3.1.1/jquery-3.1.1.min.js"></script>
		<style media="screen" type="text/css">
			<?
				// Use Leafo SCSS PHP compiler to invoke file
				ob_start();
				include_once __DIR__.'/css/style.scss';
				$css = ob_get_clean();
				$css = $scss->compile( $css );
				// Compile waxy
				echo $waxy->compile( $css );
			?>
		</style>
		<script type="text/javascript" language="javascript">
			<?
				echo $waxy->js['include'];
				echo $waxy->js['script'];
			?>
		</script>
	</head>

	<body>
		<div class="father parent">
			<span class="son child">
				Hover to see a Rebel Attack class change.
				Click to see a Rebel Contaminate permanent color change & a Rebel Zigzag.
			</span>
		</div>
		<div class="uncle parent">
			<span class="cousin child">
				Cousin.
			</span>
		</div>
	</body>
</html>
