<?php

use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\app\AppKernel;
use Symfony\Component\Console\Input\ArrayInput;

require_once __DIR__.'/../vendor/autoload.php';
function bootstrap()
{
    $kernel = new AppKernel('test', true);
    $kernel->boot();
    $application = new Application($kernel);
    $application->setAutoExit(false);
    $application->add(new DropDatabaseDoctrineCommand());
    $application->add(new CreateDatabaseDoctrineCommand());
    $application->add(new RunSqlDoctrineCommand());
    $application->run(new ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => '1',
        '--force' => '1',
    ]));
    $application->run(new ArrayInput([
        'command' => 'doctrine:database:create',
    ]));
    $application->run(new ArrayInput([
        'command' => 'doctrine:query:sql',
        'sql' => 'CREATE TABLE test (test VARCHAR(10))',
    ]));
    $kernel->shutdown();
}
bootstrap();
