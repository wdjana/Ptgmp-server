<?php
namespace Pioc\Database\Mysqli\Web;

use Pioc\Base\Model;
use Pioc\Database\Mysqli\Tables;


class ClientAppValidator extends Model {

    protected $_db;
    protected $_routeId;
    protected $_clientAppId;

    private $_sql = "SELECT * FROM " . Tables::APP_ROUTES .
        " WHERE route_id = ? AND (app_id = 1001 OR app_id = ?)";

    protected $_sql_update = 'UPDATE '. Tables::APP_ROUTES .
        ' SET access_count = access_count +1, access_last = UNIX_TIMESTAMP() ' .
        'WHERE id = ? ';


    public function __construct($db, $routeId, $clientAppId) {
        $this->_db = $db;
        $this->_routeId = $routeId;
        $this->_clientAppId = $clientAppId;
        $this->init();
    }

    protected function init() {
        $this->_m['id'] = 0;

        $st = $this->_db->prepare($this->_sql);

        if (!empty($st)) {
            $st->bind_param('ii', $this->_routeId, $this->_clientAppId);
            $st->execute();
            if ($st->errno > 0) {
                $this->errors[] = $st->error;
            } else {
                $result = $this->_db->parseResult($st->get_result());

                if (!empty($result)) {
                    foreach($result[0] as $key => $value) {
                        $this->_m[$key] = $value;
                    }
                }
            }

            $st->close();
        }

        $this->update();
    }

    protected function update() {
        $id = $this->id;

        if ($id > 0) {
            $st = $this->_db->prepare($this->_sql_update);
            if (!empty($st)) {
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();
            }
        }
    }

}
