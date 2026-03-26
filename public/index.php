<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require dirname(__DIR__).'/config/bootstrap.php';

$env = $_SERVER['APP_ENV'] ?? 'prod';

$debugRaw = $_SERVER['APP_DEBUG'] ?? null;
$debugParsed = null;

if (null !== $debugRaw) {
    $debugParsed = filter_var($debugRaw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
}

$debug = null !== $debugParsed ? $debugParsed : ('prod' !== $env);

if ($debug) {
    umask(0000);
    Debug::enable();
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();

$response = null;

try {
    $response = $kernel->handle($request);
    $response->send();
} catch (Throwable $e) {
    if (!$debug) {
        $response = new Response('Internal Server Error', 500);
        $response->send();
    } else {
        throw $e;
    }
} finally {
    if ($response instanceof Response) {
        $kernel->terminate($request, $response);
    }
}
