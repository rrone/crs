<?php
$settings = [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,

        // View settings
        'view' => [
            'template_path' => [
                PROJECT_ROOT . '/templates',
                PROJECT_ROOT . '/src/Action/Logon',
                PROJECT_ROOT . '/src/Action/End',
                PROJECT_ROOT . '/src/Action/Reports',
                PROJECT_ROOT . '/src/Action/Admin',
            ],
            'twig' => [
                'cache' => PROJECT_ROOT . '/var/cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        'upload_path' => PROJECT_ROOT . '/var/uploads/',
        
        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => PROJECT_ROOT . '/var/logs/app.log',
        ],

        'version' => [
            'version' => 'dev.2017.11.03.0'
        ],

        'sra' => [
            'name' => 'Rick Roberts',
            'email' => 'ayso1sra@gmail.com'
        ],

        'issueTracker' => 'https://github.com/rrone/crs/issues'

    ],

    'settings.test' => false,

];

$config = include(PROJECT_ROOT . '/config/config.php');

return $settings;
