<?php
namespace Pioc\Base;

use Model;

class Component extends Model
{
    public function getClassInfo()
    {
        if (empty($this->_m['classinfo'])) {
            $this->_m['classinfo'] = new \ReflectionClass($this);
        }

        return $this->_m['classinfo'];
    }

    public function getClassFile()
    {
        if (empty($this->_m['classfile'])) {
            $this->_m['classfile'] = $this->getClassInfo()->getFilename();
        }

        return $this->_m['classfile'];
    }

    public function getClassDir()
    {
        if (empty($this->_m['classdir'])) {
            $this->_m['classdir'] = DIRNAME($this->getClassFile());
        }

        return $this->_m['classdir'];
    }

    public function getClassName()
    {
        if (empty($this->_m['classname'])) {
            $this->_m['classname'] = $this->getClassInfo()->getName();
        }

        return $this->_m['classname'];
    }
}
