<?php
namespace Pioc\Base;

use Pioc\Base\Model;
use Pioc\Utils\Config;
use Pioc\Utils\TokenValidator;

use Pioc\Database\Mysqli\Database;
use Pioc\Database\Mysqli\Device\ClientApp;
use Pioc\Database\Mysqli\Device\Device;
use Pioc\Database\Mysqli\Device\Agent;

use Pioc\Database\Mysqli\Web\ClientAppValidator;
use Pioc\Database\Mysqli\Web\Routes;

use Pioc\Http\Request;
use Pioc\Http\Response;
use Pioc\Http\Route;

class App extends Model {

    public static $app;

    protected $_configFile;

    public $errors = array();

    public function __construct($configFile) {
        $this->_configFile = $configFile;
        static::$app = $this;
    }

    public function run() {
        if ($this->beforeRun()) {
            $this->executeRun();
        } else {
            // $this->response->json = array('invalid a');
        }

        $this->afterRun();

        // $this->response->json = array('time' => time());

        // if ($this->beforeRun()) {
        //
        // }
        //
        // $this->afterRun();
    }

    protected function beforeRun() {
        $retval = false;

        if ($this->validateRoute() &&
            $this->validateClientApp()) {
            $retval = true;
        }

        return $retval;
    }

    protected function executeRun() {
        // $this->response->json = array('execute run');

        $route = $this->route;
        $handler = $route->handler;

        if (!empty($handler['name'])) {
            // fixname
            $name = implode('', explode('/', $handler['name']));
            $action = 'action'.$name;

            if (method_exists($this, $action)) {
                $this->$action($route->variables);
            } else {
                $this->response->code = 404;
            }

            //
            // if (method_exists($this, $action)) {
            //     $this->$action($route->variables);
            // } else {
            //     $this->response->json = array('not found');
            //     // $this->response->code = 404;
            // }
        }

    }

    protected function afterRun(){
        //for debug
        if (!empty($this->errors)) {
            $this->response->json = $this->errors;
        }

        // echo "end of response";
        $this->response->send();
        $this->terminate();
    }

    public function terminate($code=0) {
        clearstatcache();
        exit($code);
    }

    public function getTables() {

    }

    public function getRoutes(){
        $name = 'routes';

        if (!array_key_exists($name, $this->_m)) {
            $db = $this->db;
            $this->_m[$name] = new Routes($db);
        }

        return $this->_m[$name];
    }

    protected function getConfig() {
        $name = 'config';

        // echo "get config\n";

        if (!array_key_exists($name, $this->_m)) {

            // echo "configFile", $this->_configFile, "\n";
            $econfig = new Config($this->_configFile);
            $this->_m[$name] = $econfig->toArray();
        }

        return $this->_m[$name];
    }

    protected function getRequest() {
        $name = 'request';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = new Request();
        }
        return $this->_m[$name];
    }

    protected function getResponse() {
        $name = 'response';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = new Response();
        }

        return $this->_m[$name];
    }

    protected function getDb() {
        $name = 'db';

        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;
            $config = $this->config;
            if (!empty($config['db'])) {
                $dbconfig = $config['db'];
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

                try {
                    $db = new Database($dbconfig['host'],
                        $dbconfig['username'],
                        $dbconfig['password'],
                        $dbconfig['database']);

                    $this->_m[$name] = $db;
                } catch (mysqli_sql_exception $ex) {
                    $this->errors[] = 'database connection failed';
                }
            }
        }

        return $this->_m[$name];

    }

    protected function getIpv4() {
        $name = 'ipv4';
        if (!array_key_exists($name, $this->_m)) {
            if (PHP_SAPI == 'cli') {
                $this->_m[$name] = '127.0.0.1';
            } else {
                $this->_m[$name] = !empty($_SERVER['HTTP_CLIENT_IP'])
                    ? $_SERVER['HTTP_CLIENT_IP']
                    : !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
                        ? $_SERVER['HTTP_X_FORWARDED_FOR']
                        : $_SERVER['REMOTE_ADDR'];
            }
        }

        return $this->_m[$name];
    }

    protected function getAgent() {
        $name = 'agent';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = new Agent($this->db);
        }

        return $this->_m[$name];
    }

    protected function getDevice() {
        $name = 'device';
        if (!array_key_exists($name, $this->_m)) {
            $db = $this->db;
            $key = $this->config['deviceKey'];
            $this->_m[$name] = new Device($db, $key);
        }
        return $this->_m[$name];
    }

    protected function getClientAppToken() {
        $name = 'clientAppToken';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;

            $config = $this->config;
            if (PHP_SAPI != 'cli' && !empty($config['clientAppKey'])) {
                $key = $config['clientAppKey'];
                $headers = getallheaders();

                if (!empty($headers[$key])) {
                    if (TokenValidator::validate($headers[$key])) {
                        $this->_m[$name] = $headers[$key];
                    } else {
                        $this->errors[] = 'clientAppToken INVALID, '.$headers[$key];
                    }
                } else {
                    $this->errors[] = 'clientAppToken EMPTY';
                }
            }


        }
        return $this->_m[$name];
    }

    protected function getClientApp(){
        $name = 'clientApp';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;
            $token = $this->clientAppToken;

            if (!empty($token)) {
                $this->_m[$name] = new ClientApp($this->db, $token);
            }

            // $this->_m[$name] = new ClientApp($this->db, $this->clientAppToken);

            // $this->_m[$name] = null;
            // $db = $this->db;
            // $token = $this->clientAppToken;
            //
            // if (!empty($db) && !empty($token)) {
            //     $clientApp = new ClientApp($db, $token);
            //     if ($clientApp->id > 0) {
            //         $this->_m[$name] = $clientApp;
            //     }
            // }
        }
        return $this->_m[$name];
    }

    protected function getRoute() {
        $name = 'route';

        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;



            $croutes = $this->routes;
            $routes = $croutes->routes;

            $request = $this->request;


            if (!empty($routes)) {
                $route = new Route();
                $code = $route->dispatch($routes, $request->method, $request->query);
                if ($code == 200) {
                    $croutes->update($route->handler['id']);
                }

                $this->_m[$name] = $route;
            }

        }
        return $this->_m[$name];
    }

    protected function getUser() {
        return null;
    }

    protected function validateRoute() {
        $route = $this->route;
        if ($route->code == 200) {
            return true;
        } else {
            $this->response->code = $route->code;
        }
    }

    protected function validateClientApp(){
        if (PHP_SAPI == 'cli') return true;

        $retval = 0;

        $clientApp = $this->clientApp;

        if (!empty($clientApp)) {
            $appId = $clientApp->id;
            if ($appId > 0) {
                $routeId = $this->route->handler['id'];
                $db = $this->db;

                $validator = new ClientAppValidator($db, $routeId, $appId);
                $retval = $validator->id > 0;

                if (!$retval) {
                    $this->errors[] = array(
                        'message' => 'clientApp Route invalid',
                        'context' => array(
                            'route' => $routeId,
                            'app' => $appId
                        ),
                    );
                }

            } else {
                $this->errors[] = 'clientApp Invalid';
            }
        } else {
            $this->errors[] = 'clientApp EMPTY';
        }


        // if (!empty($clientApp) && $clientApp->id > 0) {
        //     $routeId = $this->route->handler['id'];
        //     $db = $this->db;
        //     $appId = $clientApp->id;
        //
        //     $validator = new ClientAppValidator($db, $routeId, $appId);
        //     $retval = $validator->id;
        //
        //     if ($retval > 0) {
        //
        //     } else {
        //         $this->response->json = array(
        //             'route' => $routeId,
        //             'app' => $appId
        //         );
        //     }
        //
        //     //
        //     //
        //     // if (!empty($route)) {
        //     //     $routeId = $route->handler['id'];
        //     //     $validator = new ClientAppValidator($db, $routeId, $appId);
        //     //     $retval = $validator->id > 0;
        //     // }
        // }

        // if (empty($retval)) {
        //     // $this->response->code = 403;
        //     //
        //     $this->response->json = array(
        //
        //     );
        // }

        return $retval;
    }

}
