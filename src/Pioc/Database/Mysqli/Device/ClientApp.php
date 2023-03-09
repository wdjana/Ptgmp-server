<?php
namespace Pioc\Database\Mysqli\Device;

use Pioc\Base\Model;
use Pioc\Database\Mysqli\Tables;

class ClientApp extends Model {

    protected $_db;

    protected $_token;

    protected $_sql = "SELECT * FROM ". Tables::CLIENT_APP." WHERE token = ?";

    protected $_sql_update = 'UPDATE '. Tables::CLIENT_APP .
        ' SET access_count = access_count +1, access_last = UNIX_TIMESTAMP() ' .
        'WHERE id = ? ';

    public $errors = array();

    public function __construct($db, $token) {
        $this->_db = $db;
        $this->_token = $token;

        $this->init();
    }

    protected function init() {
        $this->_m['id'] = 0;

        $st = $this->_db->prepare($this->_sql);

        if (!empty($st)) {
            $st->bind_param('s', $this->_token);
            $st->execute();
            if ($st->errno > 0) {
                $this->errors[] = $st->error;
            } else {
                $result = $this->_db->parseResult($st->get_result());

                if (!empty($result)) {
                    $this->_m['cdata'] = array();
                    foreach($result[0] as $key => $value) {
                        $this->_m[$key] = $value;
                        $this->_m['cdata'][$key] = $value;
                    }

                    $this->update($result[0]['id']);
                }
            }

            $st->close();
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
