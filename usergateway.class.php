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

  public function findBy($key, $value, $fetchclass=false) {
    $sql = "SELECT * FROM `users` WHERE `$key` = :$key";
    $q = $this->_dbhandle->prepare($sql);
    if ($fetchclass) {
      $q->setFetchMode(PDO::FETCH_CLASS, 'User');
    } else {
      $q->setFetchMode(PDO::FETCH_ASSOC);
    }
    $q->execute(array(":$key" => $value));
    $rv = $q->fetch();
    return $rv;
  }

  public function existsBy($key, $value, $fetchclass=false) {
    $selection = $this->findBy($key, $value, $fetchclass);
    if ($selection) {
      return true;
    }  
    return false;
  }

}
