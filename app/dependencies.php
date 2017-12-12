<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use Illuminate\Database\Capsule\Manager;
use App\Action\SessionHandler;
use App\Action\DataWarehouse;
use App\Action\Admin\AdminController;
use App\Action\Admin\AdminView;
use App\Action\Logon\LogonController;
use App\Action\Logon\LogonView;
use App\Action\Reports\ReportsController;
use App\Action\Reports\ReportsView;
use App\Action\Export\ExportController;
use App\Action\Export\ExportXl;
use App\Action\Admin\LogExportController;
use App\Action\Admin\LogExport;

// DIC configuration
$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function (\Slim\Container $c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    //Manage Twig base_url() returns port 80 when used over HTTPS connection
    $view['env_uri'] = $c->get('settings')['env_uri'];
    $view['sra_email'] = $c->get('settings')['sra']['email'];
    $view['subject'] = $c->get('settings')['sra']['subject'];
    $view['issueTracker'] = $c->get('settings')['issueTracker'];
    $view['banner'] = $c->get('settings')['banner'];

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    $Version = new Twig_SimpleFunction(
        'version', function () use ($settings) {
        $ver = 'Version '.$settings['version']['version'];

        return $ver;
        }
    );

    $view->getEnvironment()->addFunction($Version);

    $filter_cast_to_array = new Twig_SimpleFilter('cast_to_array', function ($stdClassObject) {
        $response = array();

        foreach ($stdClassObject as $key => $value) {
            $response[] = array($key, $value->text);
        }
//        var_dump($response); die();
        return $response;
    });

    $view->getEnvironment()->addFilter($filter_cast_to_array);

    $view->getEnvironment()->setCache(false);

    return $view;
};

// Flash messages
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};

unset($container['errorHandler']);

//Override the default Not Found Handler
$container['notFoundHandler'] = function (Container $c) {
    return function (Request $request, Response $response) use ($c) {
        return $response->withRedirect('/', 301);
    };
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function (Container $c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());

    //Added to remove empty brackets
    //Reference: http://stackoverflow.com/questions/13968967/how-not-to-show-last-bracket-in-a-monolog-log-line
    $handler = new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG);
    // the last "true" here tells it to remove empty []'s
    $formatter = new Monolog\Formatter\LineFormatter(null, null, false, true);
    $handler->setFormatter($formatter);
    //End of added

    $logger->pushHandler($handler);

    return $logger;
};

$container['db'] = function (Container $c) {
    $capsule = new Manager;

    $capsule->addConnection($c['settings']['dbConfig']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container['dw'] = function (Container $c) {
    $db = $c->get('db');
    $dataWarehouse = new DataWarehouse($db);

    return $dataWarehouse;
};

// -----------------------------------------------------------------------------
// Action dependency Injection
// -----------------------------------------------------------------------------
$db = $container->get('db');
$dw = $container->get('dw');
$view = $container->get('view');
$uploadPath = $container->get('settings')['upload_path'];

$container[SessionHandler::class] = function (Container $c) {
    $db = $c->get('db');

    return new SessionHandler($db);
};

$container[DataWarehouse::class] = function ($db) {

    return new DataWarehouse($db);
};

// -----------------------------------------------------------------------------
// Admin class
// -----------------------------------------------------------------------------
$container[AdminView::class] = function ($c) use ($dw) {

    return new AdminView($c, $dw);
};

$container[AdminController::class] = function ($c) use ($dw) {
    $v = new AdminView($c, $dw);

    return new AdminController($c, $v);
};

// -----------------------------------------------------------------------------
// LogExport class
// -----------------------------------------------------------------------------
$container[LogExport::class] = function ($c) use ($dw) {

    return new LogExport($c, $dw);
};

$container[LogExportController::class] = function ($c) use ($dw) {
    $v = new LogExport($c, $dw);

    return new LogExportController($c, $v);
};

// -----------------------------------------------------------------------------
// Logon class
// -----------------------------------------------------------------------------
$container[LogonView::class] = function ($c) use ($dw) {

    return new LogonView($c, $dw);
};

$container[LogonController::class] = function ($c) use ($dw) {
    $v = new LogonView($c, $dw);

    return new LogonController($c, $v);
};

// -----------------------------------------------------------------------------
// Reports class
// -----------------------------------------------------------------------------
$container[ReportsView::class] = function ($c) use ($dw) {

    return new ReportsView($c, $dw);
};

$container[ReportsController::class] = function ($c) use ($dw) {
    $v = new ReportsView($c, $dw);

    return new ReportsController($c, $v);
};

// -----------------------------------------------------------------------------
// Export class
// -----------------------------------------------------------------------------
$container[ExportXl::class] = function () use ($dw) {

    return new ExportXl($dw);
};

$container[ExportController::class] = function ($c) use ($dw) {
    $v = new ExportXl($dw);

    return new ExportController($c, $v);
};

