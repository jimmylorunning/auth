<?php
require_once 'gateway.php';

class UserGateway implements Gateway {
  private $_dbhandle;

  function __construct($dbhandle = null) {
    $this->_dbhandle = ConnectionFactory::getFactory()->getConnection();
  }

  public function create($user) {
    $sql = "INSERT INTO `users` (email,password,user_salt,is_admin,is_active) " .
      "VALUES (:email,:password,:user_salt,:is_admin,:is_active)";
    $q = $this->_dbhandle->prepare($sql);
    $q->execute($user);
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
    $sql = "SELECT * FROM `users` WHERE `$key` = :$key";
    $q = $this->_dbhandle->prepare($sql);
    $q->execute(array(":$key" => $value));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

}
