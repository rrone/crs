<?php

// To help the built-in PHP dev server, check if the request was actually for
// something which should probably be served as a static file
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require PROJECT_ROOT . '/vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require PROJECT_ROOT . '/app/settings.php';

$settings['debug'] = false;

$settings['settings']['banner'] = null;
$settings['settings']['db'] = $config['server'];

$settings['settings']['env_uri'] = 'http://';
if (isset($_SERVER['HTTPS'])) {
    $settings['settings']['env_uri'] = 'https://';
}

$settings['settings']['env_uri'] .= $_SERVER['SERVER_NAME'] . '/refsched/public';

ini_set("display_errors", 0);
ini_set("log_errors", 1);

//Define where the log goes: syslog
ini_set("error_log", "syslog");

$app = new \Slim\App($settings);

// Set up dependencies
require PROJECT_ROOT . '/app/dependencies.php';

// Register middleware
require PROJECT_ROOT . '/app/middleware.php';

// Register routes
require PROJECT_ROOT . '/app/routes.php';

// Run!
$app->run();
