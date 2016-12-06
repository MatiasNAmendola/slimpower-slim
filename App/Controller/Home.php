<?php

namespace SlimPower\Controller;

class Home {

    // Optional properties
    protected $app;
    protected $request;
    protected $response;

    public function error() {
        throw new \Exception("Something wrong with your request!");
    }

    public function index() {
        $this->app->render(200, array(
            'msg' => 'welcome to my API!',
        ));
    }

    public function hello($name = 'test') {
        echo "Hello, $name";
    }

    public function bye($name = 'test') {
        echo "Bye, $name";
    }

    public function ups() {
        $this->app->render(200, array(
            'msg' => 'ups!',
        ));
    }

    // Optional setters
    public function setApp($app) {
        $this->app = $app;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

    public function setResponse($response) {
        $this->response = $response;
    }

    // Init
    public function init() {
        //$this->app->view(new \SlimPower\Middleware\jsonApi\JsonApiView());
        //$this->app->add(new \SlimPower\Middleware\jsonApi\JsonApiMiddleware());
        // do things now that app, request and response are set.
    }

}
