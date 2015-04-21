<?php

use Insphare\Base\Application\Setup;
use Insphare\Base\ObjectContainer;

$ds = DIRECTORY_SEPARATOR;
include_once implode($ds, array(__DIR__, 'lib', 'insphare', 'Base', 'Autoloader.php'));

$arrIncludePath = array(
	'Insphare' => __DIR__ . $ds . 'lib' . $ds . 'insphare',
);

foreach ($arrIncludePath as $namespace => $includePath) {
	$autoloader = new \Insphare\Base\Autoloader();
	$autoloader->addIncludePath($includePath);
	$autoloader->setNameSpace($namespace);
	$autoloader->register();
}

$container = new ObjectContainer();
$container->setSetup(new Setup());