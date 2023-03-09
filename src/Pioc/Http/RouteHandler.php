<?php
namespace Pioc\Http;


class RouteHandler extends Model {

    public $routes;

    public $request;

    public function __construct($routes, $request) {
        $this->routes = $routes;
        $this->request = $route;
        $this->run();
    }

    public function run(){
        
    }

}
