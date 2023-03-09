<?php
namespace Pioc\Http;

use Pioc\Base\Model;

class Request extends Model {

    protected function setHandler($value) {
        $this->_m['handler'] = $value;
    }

    protected function getHandler() {
        $name = 'handler';

        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;
        }

        return $this->_m[$name];
    }

    protected function setVariables($vars) {
        $this->_m['variables'] = $vars;
    }

    protected function getVariables() {
        $name = 'variables';

        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;
        }

        return $this->_m[$name];
    }

    protected function getMethod() {
        $name = 'method';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = empty($_SERVER['REQUEST_METHOD']) ? 'get' : strtolower($_SERVER['REQUEST_METHOD']);
        }

        return $this->_m[$name];
    }

    protected function getQuery() {
        $name = 'query';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = empty($_SERVER['QUERY_STRING'] ) ? '' : $_SERVER['QUERY_STRING'];
        }

        return $this->_m[$name];
    }

}
