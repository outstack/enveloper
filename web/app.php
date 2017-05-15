<?php

use Symfony\Component\HttpFoundation\Request;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';

$env = getenv('SYMFONY_ENV');
if (!$env) {
    $env = 'dev';
}

$kernel = new AppKernel($env, in_array($env, ['dev', 'test']));

$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);