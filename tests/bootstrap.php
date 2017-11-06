<?php

namespace Tests;

// Settings to make all errors more obvious during testing
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_log', 'syslog');
date_default_timezone_set('UTC');

use Slim\App;
use App\Action\DataWarehouse;
use Slim\Container;
use There4\Slim\Test\WebTestCase;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Http\Headers;
use Slim\Http\RequestBody;
use Slim\Http\Request;

define('PROJECT_ROOT', realpath(__DIR__.'/..'));

ini_set('max_execution_time', 600);
ini_set('memory_limit','1G');

require_once PROJECT_ROOT.'/vendor/autoload.php';

// Initialize copy of the slim application for test
class AppTestCase extends WebTestCase
{
    protected $config;

    /**
     * @var DataWarehouse
     */
    protected $dw;

    /**
     * @var Container
     */
    protected $c;

    /* @var \Tests\AppWebTestClient */
    protected $client;

    private $cookies = array();

    public function getSlimInstance()
    {
        $this->config = include(PROJECT_ROOT.'/config/config.php');

// Instantiate the app
        $settings = require PROJECT_ROOT.'/app/settings.php';
        $settings['debug'] = true;

        $settings['settings']['db'] = $this->config['db_test'];
        $settings['test']['user'] = $this->config['user_test'];
        $settings['test']['admin'] = $this->config['admin_test'];
        $settings['test']['empty'] = $this->config['empty_test'];
        $settings['test']['dev'] = $this->config['dev_test'];

//Define where the log goes: syslog

        $app = new App($settings);

// Set up dependencies
        require PROJECT_ROOT.'/app/dependencies.php';

// Register middleware
        require PROJECT_ROOT.'/app/middleware.php';

// Register routes
        require PROJECT_ROOT.'/app/routes.php';

        $this->c = $app->getContainer();

        $this->dw = new DataWarehouse($this->c->get('db'));
        $app->getContainer()['settings.test'] = true;

        return $app;
    }

    protected function request($method, $path, $data = array(), $optionalHeaders = array())
    {
        //Make method uppercase
        $method = strtoupper($method);
        $options = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $path,
        );

        if ($method === 'GET') {
            $options['QUERY_STRING'] = http_build_query($data);
        } else {
            $params = json_encode($data);
        }

        // Prepare a mock environment
        $env = Environment::mock(array_merge($options, $optionalHeaders));
        $uri = Uri::createFromEnvironment($env);
        $headers = Headers::createFromEnvironment($env);
        $cookies = $this->cookies;
        $serverParams = $env->all();
        $body = new RequestBody();

        // Attach JSON request
        if (isset($params)) {
            $headers->set('Content-Type', 'application/json;charset=utf8');
            $body->write($params);
        }

        return new Request($method, $uri, $headers, $cookies, $serverParams, $body);
    }

    protected function setCookie($name, $value)
    {
        $this->cookies[$name] = $value;
    }
}

;

/* End of file bootstrap.php */