<?php
namespace Pioc\Database\Mysqli;

use mysqli;

class Database extends mysqli {

  public function equery($sql, $fetchfields=false) {
    return $this->parseResult($this->query($sql), $fetchfields);
  }

  public function resultToFields($result) {
    $retval = array();

    $temps = $result->fetch_fields();

    foreach($temps as $obj) {
      $retval[] = $obj->name;
    }


    return $retval;
  }


  public function parseResult($result, $fetchfields=false) {

      if ($result){

          if (is_bool($result)) return $result;

          $retval = array();

          if ($fetchfields) {
            $retval[] = $this->resultToFields($result);

            while($row = $result->fetch_array()) {
              $retval[] = $row;
            }
          } else {
            while($row = $result->fetch_assoc()) {
              $retval[] = $row;
            }
          }

          $result->close();
          return $retval;
      }
  }

    public function clearResult($res) {
      while($this->more_results() && $this->next_result()){
  			if ($res = $this->store_result()) $res->free();
  		}
    }

}
