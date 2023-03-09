<?php
namespace Pioc\Database\Mysqli\Web;

use Pioc\Base\Model;
use Pioc\Database\Mysqli\Tables;

class Routes extends Model {

    protected $_db;

    protected $_sql = 'SELECT * FROM ' . Tables::WEB_ROUTES;

    protected $_sql_update = 'UPDATE '. Tables::WEB_ROUTES .
        ' SET access_count = access_count +1, access_last = UNIX_TIMESTAMP() ' .
        'WHERE id = ? ';

    public function __construct($db) {
        $this->_db = $db;
        $this->init();
    }

    protected function init() {
        $name = 'routes';
        $this->_m[$name] = array();

        $res = $this->_db->equery($this->_sql);
        if (!empty($res)) {
            $this->_m[$name] = $res;
        }
    }

    public function update($id) {
        $st = $this->_db->prepare($this->_sql_update);
        if (!empty($st)) {
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
        }
    }

}
