<?php
namespace Pioc\Database\Mysqli\Device;

use Pioc\Base\Model;
use Pioc\Utils\Uuid;
use Pioc\Database\Mysqli\Tables;

class Device extends Model {

    public $db;

    public $key;

    public function __construct($db, $key) {
        $this->db = $db;
        $this->key = $key;

        $this->init();
    }

    public function init() {
        if (PHP_SAPI == 'cli') return;

        $token = $this->token;

        if (!empty($token)) {
            $id = $this->find($token);

            if ($id > 0) {
                $this->update($id);
            }
        }
    }

    public function find($token) {
        $id = 0;
        if (Uuid::validate($token)) {
            $sql = 'SELECT * FROM '.Tables::CLIENT_DEVICE.
                ' WHERE token = ?';

            $st = $this->db->prepare($sql);

            if (!empty($st)) {
                $st->bind_param('s', $token);
                $st->execute();

                if ($st->errno == 0) {
                    $result = $this->db->parseResult($st->get_result());

                    if (!empty($result)) {
                        $id = $result[0]['id'];
                        $this->_m = $result[0];
                    }
                }


                $st->close();
            }
        }

        return $id;
    }

    public function update($id) {
        $sql = 'UPDATE '.Tables::CLIENT_DEVICE.' SET '.
            'access_count = access_count +1, access_last = UNIX_TIMESTAMP() '.
            'WHERE id = ?';

        $st = $this->db->prepare($sql);

        if (!empty($st)) {
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
        }
    }

    public function generateToken($agentId) {
        $token = Uuid::v4();

        $sql = 'INSERT INTO '.Tables::CLIENT_DEVICE.
            ' (token, agent_id, access_count, access_last, created_at) '.
            ' VALUES (?, ?, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';

        $st = $this->db->prepare($sql);

        if (!empty($st)) {
            $st->bind_param('si', $token, $agentId);
            $st->execute();

            if ($st->errno == 0) {
                $this->_m['id'] = $this->db->insert_id;
                $this->_m['agent_id'] = $agentId;
            }

            $st->close();
        }


        return $token;
    }



    protected function getToken() {
        $name = 'token';
        if (!array_key_exists($name, $this->_m)) {
            $this->_m[$name] = null;
            $headers = getallheaders();

            if (!empty($headers[$this->key]) &&
                Uuid::validate($headers[$this->key])) {
                $this->_m[$name] = $headers[$this->key];
            }
        }
        return $this->_m[$name];
    }


}
