<?php
include './vendor/autoload.php';

//var_dump($_SERVER['argv']);

\Robo\Robo::createContainer(null, null, null);
$statusCode = \Robo\Robo::run(['', 'start'],RoboFile::class);

//shell_exec('php robo.phar start');
