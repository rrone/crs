<?php
// Routes

$container = $app->getContainer();

//Request::setTrustedProxies(array('127.0.0.1'));

$app->map(['GET', 'POST'], '/', App\Action\Logon\LogonController::class)
    ->setName('logon');
$container['logonPath'] = $container->get('router')->pathFor('logon');

$app->map(['GET', 'POST'], '/logon', App\Action\Logon\LogonController::class);

$app->map(['GET', 'POST'], '/end', App\Action\End\EndController::class)
    ->setName('end');
$container['endPath'] = $container->get('router')->pathFor('end');

$app->map(['GET', 'POST'], '/reports', App\Action\Reports\ReportsController::class)
    ->setName('reports');
$container['reportsPath'] = $container->get('router')->pathFor('reports');

$app->map(['GET', 'POST'], '/adm', App\Action\Admin\AdminController::class)
    ->setName('admin');
$container['adminPath'] = $container->get('router')->pathFor('admin');

$app->map(['GET', 'POST'], '/export', App\Action\Export\ExportController::class)
    ->setName('export');
$container['exportPath'] = $container->get('router')->pathFor('export');