<?php
namespace Pioc\Http;

use Pioc\Base\Model;

use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector as RouteCollector;

class Route {

    public $handler = null;

    public $variables = null;

    public $code = 404;

    public function dispatch($routes, $method, $query) {
        $this->code = 404;

        if (!empty($routes)) {
            $routeCollector = new RouteCollector(new RouteParser, new DataGenerator);

            foreach($routes as $k=>$v) {
                $routeCollector->addRoute($v['method'], $v['pattern'], $v['id']);
            }

            $dispatcher = new Dispatcher($routeCollector->getData());
            $info = $dispatcher->dispatch($method, $query);

            switch ($info[0]) {
                case \FastRoute\Dispatcher::FOUND:
                    $this->code = 200;

                    foreach($routes as $k=>$v) {
                        if ($v['id'] == $info[1]) {
                            $this->handler = $v;
                        }
                    }

                    $this->variables = $info[2];

                    break;

                case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    $this->code = 405;
                    break;
            }

        }

        return $this->code;
    }


}
