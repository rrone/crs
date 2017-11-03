<?php

namespace Tests;

use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;
use There4\Slim\Test\WebTestClient;

class AppWebTestClient extends WebTestClient
{
    private $cookies = array();

    private $followRedirect;

    private $returnAsObject;

    public function __construct(App $slim)
    {
        parent::__construct($slim);

//        $this->followRedirects(false);
        $this->returnAsResponseObject(false);
    }

    public function __call($method, $arguments)
    {
        throw new \BadMethodCallException(strtoupper($method) . ' is not supported');
    }

    public function get($path, $data = array(), $optionalHeaders = array())
    {
        return $this->request('get', $path, $data, $optionalHeaders);
    }

    public function post($path, $data = array(), $optionalHeaders = array(), $uploadedFiles = array())
    {
        return $this->request('post', $path, $data, $optionalHeaders, $uploadedFiles);
    }

    public function patch($path, $data = array(), $optionalHeaders = array())
    {
        return $this->request('patch', $path, $data, $optionalHeaders);
    }

    public function put($path, $data = array(), $optionalHeaders = array())
    {
        return $this->request('put', $path, $data, $optionalHeaders);
    }

    public function delete($path, $data = array(), $optionalHeaders = array())
    {
        return $this->request('delete', $path, $data, $optionalHeaders);
    }

    public function head($path, $data = array(), $optionalHeaders = array())
    {
        return $this->request('head', $path, $data, $optionalHeaders);
    }

    public function options($path, $data = array(), $optionalHeaders = array())
    {
        return $this->request('options', $path, $data, $optionalHeaders);
    }

    // Abstract way to make a request to SlimPHP, this allows us to mock the
    // slim environment
    private function request($method, $path, $data = array(), $optionalHeaders = array(), $uploadedFiles = array())
    {
        //Make method uppercase
        $method = strtoupper($method);
        $options = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $path
        );

        if ($method === 'GET') {
            $options['QUERY_STRING'] = http_build_query($data);
        } else {
            $params  = json_encode($data);
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

        $this->request = new Request($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);

        $response = new Response();

        // Invoke request
        $app = $this->app;
        $this->response = $app($this->request, $response);

//        if($this->followRedirect){
//            while(empty((string)$this->response->getBody()))
//            {
//                $this->returnAsResponseObject(true);
//                $url = implode($this->response->getHeader('Location'));
//
//                $this->response = $this->request('get', $url);
//                var_dump((string)$this->response->getBody());
//
//            }
//        }
//
        // Return the application output
        if($this->returnAsObject){
            $return = $this->response;
        }
        else {
            $return = (string) $this->response->getBody();
        }

        return $return;
    }

    public function setCookie($name, $value)
    {
        $this->cookies[$name] = $value;
    }

//    public function followRedirects($value)
//    {
//        $this->followRedirect = $value;
//    }
//
    public function returnAsResponseObject($value)
    {
        $this->returnAsObject = $value;
    }

}
