<?php

use Symfony\Component\Debug\Debug;

// To help the built-in PHP dev server, check if the request was actually for
// something which should probably be served as a static file
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', 'fe80::1', '::1']) || php_sapi_name() === 'cli-server')
    //VirtualHostX Local Domain
    && (!strpos($_SERVER['SERVER_NAME'],'.vhx.host') )
    //VirtualHostX Local Network Domain
    && (!strpos($_SERVER['SERVER_NAME'],'.xip.io') )  ) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require PROJECT_ROOT . '/vendor/autoload.php';

Debug::enable();

session_start();

// Instantiate the app
$settings = require PROJECT_ROOT . '/app/settings.php';

$settings['debug'] = true;
$settings['displayErrorDetails'] = $settings['debug'];

$server = $config['db'];
$settings['settings']['banner'] = "<h1 class=\"banner\">Development Server : $server</h1>";
$settings['settings']['db'] = $config[$server];

$settings['settings']['env_uri'] = 'http://';
if (isset($_SERVER['HTTPS'])) {
    $settings['settings']['env_uri'] = 'https://';
}

$settings['settings']['env_uri'] .= $_SERVER['SERVER_NAME'];

$app = new \Slim\App($settings);

// Set up dependencies
require PROJECT_ROOT . '/app/dependencies.php';

// Register middleware
require PROJECT_ROOT . '/app/middleware.php';

// Register routes
require PROJECT_ROOT . '/app/routes.php';

// Run!
$app->run();
