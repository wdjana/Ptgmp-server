<?php
namespace Pioc\Base;

class Model {
    protected $_m = array();

    public function __get($name)
    {
        $getter = 'get'.$name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } else {
            if (array_key_exists($name, $this->_m)){
                return $this->_m[$name];
            }
        }
    }

    public function __set($name, $value)
    {
        $setter = 'set'.$name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->_m);
    }
}
