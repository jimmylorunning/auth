<?php
class myDb {
  private $_db;

  public function __construct($pdo_db) {
    $this->_db = $pdo_db;
  }

  public function insert($table, $assoc_array) {
    $query = "INSERT INTO $table SET ";
    $key_values = array();
    foreach($assoc_array as $key=>$val) {
      $key_values[] = "`$key`='$val'"; 
      // note: this won't work with dates, to do: rethink
    }
    $query .= implode(',', $key_values); 
    return $this->_db->exec($query);
  }

  public function selectOne($table, $where) {
    $query = "SELECT * FROM $table";
    
    if(!empty($where)) {
      $where = implode(" AND ", $where);
      $query .= " WHERE {$where}";
    }

    $this->_db->prepare($query);
    
  }
}
?>
