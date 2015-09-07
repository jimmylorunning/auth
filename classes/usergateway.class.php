<?php
require_once 'classes/gateway.php';

class UserGateway implements Gateway {
  private $_dbhandle;

  function __construct($dbconfig) {
    $this->_dbhandle = ConnectionFactory::getFactory()->getConnection($dbconfig);
  }

  public function create($user) {
    $sql = "INSERT INTO `users` (email,password,user_salt,is_admin,is_active) " .
      "VALUES (:email,:password,:user_salt,:is_admin,:is_active)";
    $q = $this->_dbhandle->prepare($sql);
    if ($q->execute($user)) {
      return $this->_dbhandle->lastInsertId(); // this may not be threadsafe?
    }
    return false;
  }

  public function findById($id) {
    $sql = "SELECT * FROM `users` WHERE `id` = :id";
    $q = $this->_dbhandle->prepare($sql);
    $q->execute(array(':id' => $id));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  public function findBy($key, $value) {
    $sql = "SELECT * FROM `users` WHERE `$key` = :$key";
    $q = $this->_dbhandle->prepare($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);
    $q->execute(array(":$key" => $value));
    $user_row = $q->fetch();
    return $user_row;
  }
}
