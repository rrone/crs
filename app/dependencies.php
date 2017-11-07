<?php
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

    return $view;
};

// Flash messages
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};

unset($container['errorHandler']);

//Override the default Not Found Handler
$container['notFoundHandler'] = function (\Slim\Container $c) {
    return function ($request, $response) use ($c) {
        return $response->withRedirect('/', 301);
    };
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function (\Slim\Container $c) {
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

$container['db'] = function (\Slim\Container $c) {
    $capsule = new Illuminate\Database\Capsule\Manager;

    $capsule->addConnection($c['settings']['dbConfig']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container['dw'] = function (\Slim\Container $c) {
    $db = $c->get('db');
    $dataWarehouse = new \App\Action\DataWarehouse($db);

    return $dataWarehouse;
};

// -----------------------------------------------------------------------------
// Action dependency Injection
// -----------------------------------------------------------------------------
$db = $container->get('db');
$dw = $container->get('dw');
$view = $container->get('view');
$uploadPath = $container->get('settings')['upload_path'];

$container[App\Action\DataWarehouse::class] = function ($db) {

    return new \App\Action\DataWarehouse($db);
};

// -----------------------------------------------------------------------------
// Admin class
// -----------------------------------------------------------------------------
$container[App\Action\Admin\AdminView::class] = function ($c) use ($dw) {

    return new \App\Action\Admin\AdminView($c, $dw);
};

$container[App\Action\Admin\AdminController::class] = function ($c) use ($dw) {
    $v = new \App\Action\Admin\AdminView($c, $dw);

    return new \App\Action\Admin\AdminController($c, $v);
};

// -----------------------------------------------------------------------------
// LogExport class
// -----------------------------------------------------------------------------
$container[App\Action\Admin\LogExport::class] = function ($c) use ($dw) {

    return new \App\Action\Admin\LogExport($c, $dw);
};

$container[App\Action\Admin\LogExportController::class] = function ($c) use ($dw) {
    $v = new \App\Action\Admin\LogExport($c, $dw);

    return new \App\Action\Admin\LogExportController($c, $v);
};

// -----------------------------------------------------------------------------
// Logon class
// -----------------------------------------------------------------------------
$container[App\Action\Logon\LogonView::class] = function ($c) use ($dw) {

    return new \App\Action\Logon\LogonView($c, $dw);
};

$container[App\Action\Logon\LogonController::class] = function ($c) use ($dw) {
    $v = new \App\Action\Logon\LogonView($c, $dw);

    return new \App\Action\Logon\LogonController($c, $v);
};

// -----------------------------------------------------------------------------
// Reports class
// -----------------------------------------------------------------------------
$container[App\Action\Reports\ReportsView::class] = function ($c) use ($dw) {

    return new \App\Action\Reports\ReportsView($c, $dw);
};

$container[App\Action\Reports\ReportsController::class] = function ($c) use ($dw) {
    $v = new \App\Action\Reports\ReportsView($c, $dw);

    return new \App\Action\Reports\ReportsController($c, $v);
};

// -----------------------------------------------------------------------------
// Export class
// -----------------------------------------------------------------------------
$container[App\Action\Export\ExportXl::class] = function () use ($dw) {

    return new \App\Action\Export\ExportXl($dw);
};

$container[App\Action\Export\ExportController::class] = function ($c) use ($dw) {
    $v = new \App\Action\Export\ExportXl($dw);

    return new \App\Action\Export\ExportController($c, $v);
};

