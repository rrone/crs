<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/config/bootstrap.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
ini_set("display_errors", 1);

if ($_SERVER['APP_ENV'] === 'dev') {
    umask(0000);

    ini_set('xdebug.var_display_max_depth', -1);
    ini_set('xdebug.var_display_max_children', -1);
    ini_set('xdebug.var_display_max_data', -1);

    ini_set('max_execution_time', 600);
    ini_set('max_input_time', 600);

    Debug::enable();

}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
try {
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}
