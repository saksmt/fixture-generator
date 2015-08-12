<?php

use Smt\FixtureGenerator\Application\FixtureGeneratorApp;

require_once __DIR__ . '/vendor/autoload.php';

$app = new FixtureGeneratorApp();
$app->run();
