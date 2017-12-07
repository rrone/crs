<?php
// Routes
use App\Action\Logon\LogonController;
use App\Action\Admin\AdminController;
use App\Action\Reports\ReportsController;
use App\Action\Export\ExportController;
use App\Action\Admin\LogExportController;
use App\Action\End\EndController;

$container = $app->getContainer();

//Request::setTrustedProxies(array('127.0.0.1'));

$app->map(['GET', 'POST'], '/', LogonController::class)
    ->setName('logon');
$app->map(['GET', 'POST'], '/logon', LogonController::class);
$container['logon'] = $container->get('router')->pathFor('logon');

$app->get('/end', EndController::class)
    ->setName('end');
$container['end'] = $container->get('router')->pathFor('end');

$app->get('/reports', ReportsController::class)
    ->setName('reports');
$container['reports'] = $container->get('router')->pathFor('reports');

$app->map(['GET', 'POST'], '/adm', AdminController::class)
    ->setName('admin');
$app->map(['GET', 'POST'], '/log', LogExportController::class)
    ->setName('log_export');
$container['admin'] = $container->get('router')->pathFor('admin');
$container['logExport'] = $container->get('router')->pathFor('log_export');

$app->get('/hrc', ExportController::class)
    ->setName('hrc');
$app->get('/ra', ExportController::class)
    ->setName('ra');
$app->get('/ri', ExportController::class)
    ->setName('ri');
$app->get('/rie', ExportController::class)
    ->setName('rie');
$app->get('/ruc', ExportController::class)
    ->setName('ruc');
$app->get('/urr', ExportController::class)
    ->setName('urr');
$app->get('/nra', ExportController::class)
    ->setName('nra');
$app->get('/nocerts', ExportController::class)
    ->setName('nocerts');
$app->get('/rcdc', ExportController::class)
    ->setName('rcdc');
$app->get('/rsh', ExportController::class)
    ->setName('rsh');
$container['hrc'] = $container->get('router')->pathFor('hrc');
$container['ra'] = $container->get('router')->pathFor('ra');
$container['ri'] = $container->get('router')->pathFor('ri');
$container['rie'] = $container->get('router')->pathFor('rie');
$container['ruc'] = $container->get('router')->pathFor('ruc');
$container['urr'] = $container->get('router')->pathFor('urr');
$container['nra'] = $container->get('router')->pathFor('nra');
$container['nocerts'] = $container->get('router')->pathFor('nocerts');
$container['rcdc'] = $container->get('router')->pathFor('rcdc');
$container['rsh'] = $container->get('router')->pathFor('rsh');