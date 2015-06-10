<?php

use Insphare\Base\Application\Setup;
use Insphare\Base\Client;
use Insphare\Base\ObjectContainer;

$ds = DIRECTORY_SEPARATOR;
include_once implode($ds, array(__DIR__, 'lib', 'insphare', 'Base', 'Autoloader.php'));

$autoloader = new \Insphare\Base\Autoloader();
$autoloader->addIncludePath(__DIR__ . $ds . 'lib' . $ds . 'insphare');
$autoloader->setNameSpace('Insphare');
$autoloader->register();

$container = new ObjectContainer();
$container->setSetup(new Setup());
$container->setClient(new Client(isset($_SERVER) ? $_SERVER : []));
