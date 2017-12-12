<?php

// To help the built-in PHP dev server, check if the request was actually for
// something which should probably be served as a static file
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

ini_set("display_errors", 0);
//Define where the log goes: syslog
ini_set("error_log", "syslog");
ini_set("log_errors", 1);

ini_set('max_execution_time', 300);
ini_set('memory_limit','1G');

require PROJECT_ROOT . '/vendor/autoload.php';

// Instantiate the app
$settings = require PROJECT_ROOT . '/app/settings.php';

$settings['debug'] = false;

$settings['settings']['banner'] = null;
$settings['settings']['dbConfig'] = $config['wpe'];

$settings['settings']['env_uri'] = 'http://';
if (isset($_SERVER['HTTPS'])) {
    $settings['settings']['env_uri'] = 'https://';
}

$settings['settings']['env_uri'] .= $_SERVER['SERVER_NAME'] . '/certrs/public';

$app = new \Slim\App($settings);

// Set up dependencies
require PROJECT_ROOT . '/app/dependencies.php';

// Register middleware
require PROJECT_ROOT . '/app/middleware.php';

// Register routes
require PROJECT_ROOT . '/app/routes.php';

$container = $app->getContainer();
session_set_save_handler($container[SessionHandler::class], true);

session_name('CRSID');

session_start();

// Run!
$app->run();
