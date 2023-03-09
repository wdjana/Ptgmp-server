<?php
namespace Pioc\Utils;

class Config {

    private $_filepath;

    private $_config;

    public function __construct($filepath) {
        $this->_filepath = $filepath;
        $this->read();
    }

    protected function read() {
        $this->_config = null;

        if (is_file($this->_filepath)) {
            $text  = file_get_contents($this->_filepath);
            if (!empty($text)) {
              try {
                $json = json_decode($text, true);
                $this->_config = $json;
              } catch (Exception $ex) {

              }
            }
         }
    }

    protected function clear() {
        $this->_config = null;
    }

    public function refresh() {
        $this->clear();
        $this->read();
    }

    public function toArray() {
        return !empty($this->_config) ? $this->_config : null;
    }




}
