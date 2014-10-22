<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Letharion\Apple\CliValidatorCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new CliValidatorCommand);
$application->run();
