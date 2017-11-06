<?php
// Routes

$container = $app->getContainer();

//Request::setTrustedProxies(array('127.0.0.1'));

$app->map(['GET','POST'],'/', App\Action\Logon\LogonController::class)
    ->setName('logon');
$app->map(['GET','POST'],'/logon', App\Action\Logon\LogonController::class);
$container['logon'] = $container->get('router')->pathFor('logon');

$app->get('/end', App\Action\End\EndController::class)
    ->setName('end');
$container['end'] = $container->get('router')->pathFor('end');

$app->get('/reports', App\Action\Reports\ReportsController::class)
    ->setName('reports');
$container['reports'] = $container->get('router')->pathFor('reports');

$app->group('/adm', function() {
    $this->map(['GET', 'POST'], '', App\Action\Admin\AdminController::class)
        ->setName('admin');

    $this->map(['GET', 'POST'], '/log', App\Action\Admin\LogExportController::class)
        ->setName('log_export');
    });
$container['admin'] = $container->get('router')->pathFor('admin');
$container['logExport'] = $container->get('router')->pathFor('log_export');

$app->group('/export', function() {
    $this->get('/hrc', App\Action\Export\ExportController::class)
        ->setName('hrc');
    $this->get('/ra', App\Action\Export\ExportController::class)
        ->setName('ra');
    $this->get('/ri', App\Action\Export\ExportController::class)
        ->setName('ri');
    $this->get('/rie', App\Action\Export\ExportController::class)
        ->setName('rie');
    });

$container['hrc'] = $container->get('router')->pathFor('hrc');
$container['ra'] = $container->get('router')->pathFor('ra');
$container['ri'] = $container->get('router')->pathFor('ri');
$container['rie'] = $container->get('router')->pathFor('rie');