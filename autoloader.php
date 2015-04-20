<?php

$ds = DIRECTORY_SEPARATOR;
include_once implode($ds, array(__DIR__, 'lib', 'insphare', 'Base', 'Autoloader.php'));

$arrIncludePath = array(
	'Insphare' => __DIR__ . $ds . 'lib' . $ds . 'insphare',
	'entity' => __DIR__ . $ds . 'base-entity' . $ds . 'entity',
);

foreach ($arrIncludePath as $namespace => $includePath) {
	$autoloader = new \Insphare\Base\Autoloader();
	$autoloader->addIncludePath($includePath);
	$autoloader->setNameSpace($namespace);
	$autoloader->register();
}
