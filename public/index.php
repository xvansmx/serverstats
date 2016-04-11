<?php

require_once __DIR__ . '/../bootstrap/app.php';

use ServerStats\Os\Factory;;

$namespaces = [
	'ServerStats\\Os\\Darwin\\Darwin'
	];

$factory = new Factory;
$factory->load($namespaces);
$factory->excecute();
