<?php
require_once 'gateway.php';

class SessionGateway implements Gateway {
  private $_dbhandle;

  function __construct($dbhandle = null) {
    $this->_dbhandle = ConnectionFactory::getFactory()->getConnection();
  }

  public function create($session) {
    $sql = "INSERT INTO `user_sessions` (user_id,session_id,token) " .
      "VALUES (:user_id,:session_id,:token)";
    $q = $this->_dbhandle->prepare($sql);
    $q->execute($session);
    return $q->rowCount();
  }

  public function findById($id) {
    $sql = "SELECT * FROM `users` WHERE `id` = :id";
    $q = $this->_dbhandle->prepare($sql);
    $q->execute(array(':id' => $id));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  public function findBy($key, $value) {
    $sql = "SELECT * FROM `user_sessions` WHERE `$key` = :$key";
    $q = $this->_dbhandle->prepare($sql);
    $q->execute(array(":$key" => $value));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  public function deleteBy($key, $value) {
    $sql = "DELETE FROM `user_sessions` WHERE `$key` = :$key";
    $q = $this->_dbhandle->prepare($sql);
    return $q->execute(array(":$key" => $value));
  }
}
