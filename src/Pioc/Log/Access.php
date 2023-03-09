<?php
namespace Pioc\Log;

use Pioc\Base\Model;

class Access extends Model {

    public $keyorders = array(
        'timestart',
        'http_method',
        'http_response_code',
        'http_query',
        'timespan',
        'client_app_id',
        'client_app_name',
        'ipv4',
        'agent',
    );

    protected function setApp($app) {
        $this->_m['app'] = $app;
    }

    protected function getStartAt() {
        $name = 'startat';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = $_SERVER['REQUEST_TIME_FLOAT'];
        }
        return $this->_m[$name];
    }

    protected function getTimeStart(){
        $name = 'timestart';
        if (!array_key_exists($name, $this->_m)) {
            $startAt = $this->startAt;
            $this->_m[$name] = (int) $startAt;
        }
        return $this->_m[$name];
    }

    protected function getMicrotime(){
        $name = 'microtime';
        if (!array_key_exists($name, $this->_m)) {
            $mtime = microtime();
            $comp = explode(' ', $mtime);
            $this->_m[$name] = sprintf('%d%03d', $comp[1], $comp[0]*1000) / 1000;
        }
        return $this->_m[$name];
    }

    protected function getTimeSpan() {
        $name = 'timespan';
        if (!array_key_exists($name, $this->_m)) {
            $startAt = $this->startAt;
            $microTime = $this->microTime;
            $this->_m[$name] = (int)(($microTime * 1000) - ($startAt * 1000));
        }
        return $this->_m[$name];
    }

    protected function getCData() {
        $name = 'cdata';

        if (!array_key_exists($name, $this->_m)) {
            $cdata = array();
            $cdata['timestart'] = $this->timestart;
            $cdata['timespan'] = $this->timeSpan;

            $app = $this->app;
            if (!empty($app)) {

                $clientApp = $app->clientApp;

                if (!empty($clientApp) && $clientApp->id > 0) {
                    $cdata['client_app_id'] = $clientApp->id;
                    $cdata['client_app_name'] = $clientApp->name;
                }

                $cdata['http_method'] = $app->request->method;
                $cdata['http_response_code'] = $app->response->code;

                // $cdata['http_handler'] = $app->request->handler;
                $cdata['http_query'] = $app->request->query;

                $cdata['ipv4'] = $app->ipv4;
                $cdata['agent'] = $app->agent->name;
            }



            $this->_m[$name] = $cdata;
        }
        return $this->_m[$name];
    }

    protected function getLine() {
        $name = 'line';
        if (!array_key_exists($name, $this->_m)) {
            $cdata = $this->cdata;
            $cdata['timestart'] = "[".date('H:i:s',$cdata['timestart'])."]";
            $cdata = $this->resort($cdata);

            $this->_m[$name] = implode("\t", $cdata);
        }
        return $this->_m[$name];
    }

    protected function resort($cdata) {
        $retval = array();

        foreach($this->keyorders as $key) {
            if (array_key_exists($key, $cdata)) {
                $retval[$key] = $cdata[$key];
            }
        }

        return $retval;
    }

}
