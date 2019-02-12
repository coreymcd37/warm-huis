<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';
//if (PHP_VERSION_ID < 70000) {
    //include_once __DIR__.'/../app/bootstrap.php.cache';
//}

$env = getenv('SYMFONY__ENV') ?: 'prod';
$debug = getenv('SYMFONY__DEBUG') === '1';

if ($debug) {
    Debug::enable();
}

$kernel = new AppKernel($env, $debug);
//if (PHP_VERSION_ID < 70000) {
//    $kernel->loadClassCache();
//}
//$kernel = new AppCache($kernel);
// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);