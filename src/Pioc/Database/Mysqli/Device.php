<?php
namespace Pioc\Database\Mysqli;

use Pioc\Base\Model;

class Device extends Model {

    protected $_db;

    public function __construct($db) {
        $this->_db = $db;
    }


}
