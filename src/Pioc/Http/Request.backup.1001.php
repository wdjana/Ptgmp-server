<?php
namespace Pioc\Http;

use Pioc\Base\Model;

class Request extends Model {

    protected function getHeaders() {
        $name = 'headers';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = getallheaders();
        }
        return $this->_m[$name];
    }

    protected function getIsAjax() {
        $name = 'isAjax';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m['isAjax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
        }

        return $this->_m[$name];
    }

    protected function getMethod() {
        $name = 'method';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = empty($_SERVER['REQUEST_METHOD']) ? 'GET' : strtoupper($_SERVER['REQUEST_METHOD']);
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

    protected function getIpv4() {
        $name = 'ipv4';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = !empty($_SERVER['HTTP_CLIENT_IP'])
    			? $_SERVER['HTTP_CLIENT_IP']
    			: !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
    				? $_SERVER['HTTP_X_FORWARDED_FOR']
    				: $_SERVER['REMOTE_ADDR'];
        }

        return $this->_m[$name];
    }

    protected function getAgent() {
        $name = 'agent';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = empty($_SERVER['HTTP_USER_AGENT'] ) ? '' : $_SERVER['HTTP_USER_AGENT'];
        }

        return $this->_m[$name];
    }

    protected function getStartAt() {
        $name = 'startAt';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = empty($_SERVER['REQUEST_TIME_FLOAT']) ? null :
                $_SERVER['REQUEST_TIME_FLOAT'] * 1000;
        }

        return $this->_m[$name];
    }

}
