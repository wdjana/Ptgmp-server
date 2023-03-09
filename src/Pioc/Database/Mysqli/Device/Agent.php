<?php
namespace Pioc\Database\Mysqli\Device;

use Pioc\Base\Model;
use Pioc\Database\Mysqli\Tables;

class Agent extends Model {

    public $db;

    public function __construct($db) {
        $this->db = $db;
        $this->init();
    }

    public function init() {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $name = $_SERVER['HTTP_USER_AGENT'];
            $size = strlen($name);
            $hash = hash('sha256', $name);

            $id = $this->find($hash, $size);

            // echo "agent id init => $id\n";

            if ($id > 0) {
                $this->update($id);
            } else {
                $this->insert($name, $hash, $size);
            }
        }
    }

    public function find($hash, $size) {
        $id = 0;

        $sql = "SELECT * FROM ".Tables::CLIENT_AGENT." WHERE name_hash = ? AND name_size = ?";

        // echo "hash => ", $hash, ' size => ', $size, "\n";

        $st = $this->db->prepare($sql);
        if (!empty($st)) {
            $st->bind_param('si', $hash, $size);
            $st->execute();

            if ($st->errno == 0) {
                $result = $this->db->parseResult($st->get_result());
                // print_r($result);

                if (!empty($result)) {
                    $id = $result[0]['id'];
                    $this->_m = $result[0];

                    // echo "agent id =  $id\n";
                }
            } else {
                // echo "st error => ". $st->error;
            }

            $st->close();
        } else {
            // echo "db->error => ". $this->db->error;
        }

        return $id;
    }

    public function update($id) {
        $sql = 'UPDATE '. Tables::CLIENT_AGENT .
            ' SET access_count = access_count +1, access_last = UNIX_TIMESTAMP() ' .
            'WHERE id = ? ';

        $st = $this->db->prepare($sql);
        if (!empty($st)) {
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
        }
    }

    public function insert($name, $hash, $size) {
        if ($this->validateAgentName($name)) {
            $sql = 'INSERT INTO '.Tables::CLIENT_AGENT.
                ' (name, name_hash, name_size, access_count, access_last, created_at) '.
                ' VALUES (?, ?, ?, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';

            $st = $this->db->prepare($sql);
            if (!empty($st)) {
                $st->bind_param('ssi', $name, $hash, $size);
                $st->execute();

                if ($st->errno == 0) {
                    $this->_m['id'] = $this->db->insert_id;
                    $this->_m['name'] = $name;
                    $this->_m['name_hash'] = $hash;
                    $this->_m['name_size'] = $size;
                }


                $st->close();
            }
        }
    }

    public function validateAgentName($name){
        $retval = true;
        $words = array('insert', 'update', 'delete',
            'create', 'drop', 'alter', 'truncate');

        $cname = strtolower($name);
        foreach($words as $needle) {

            if (strpos($name, $needle) > -1) {
                $retval = false;
                break;
            }
        }

        return $retval;
    }

}
