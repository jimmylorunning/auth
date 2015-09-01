<?php
require_once 'usergateway.class.php';

class User {
  private $_user_gw;
  public $email;
  public $id;
  private $password;
  private $user_salt;
  private $is_admin;
  private $is_active;

  public function __construct() {
    $this->_user_gw = new UserGateway();
  }

  public function import($user_array, $pre='') {
    $this->email = $user_array[$pre . 'email'];
    $this->setPassword($user_array[$pre . 'password']);
    $this->setUserSalt($user_array[$pre . 'user_salt']);
    $this->setIsAdmin($user_array[$pre . 'is_admin']);
    $this->setIsActive($user_array[$pre . 'is_active']);
  }

  public function export($pre='') {
    return array(
      $pre . 'email' => $this->email,
      $pre . 'password' => $this->getPassword(),
      $pre . 'user_salt' => $this->getUserSalt(),
      $pre . 'is_admin' => $this->getIsAdmin(),
      $pre . 'is_active' => $this->getIsActive());
  }

  public static function getUserBy($key, $value) {
    $user_gw = new UserGateway();
    return $user_gw->findBy($key, $value, true);
  }

  public function create() {
    if ($this->validNewUser()) {
      if ($id = $this->_user_gw->create($this->export(), ':')) {
        $this->id = $id;
        return true;
      }
    }
    return false;
  }

  public function update() {
    if ($this->validUser()) { 
      // update statement here
    }
    return false;
  }

  public function validUser() {
    if ($this->validEmail() &&
      $this->validPassword() &&
      $this->validUserSalt()) {
        return true;
    }
    return false;
  }

  public function validNewUser() {
    if (!$this->_user_gw->existsBy('email', $this->email)) {
      return $this->validUser();
    }
    return false;
  }

  public function validEmail() {
    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    return true;
  }

  public function validPassword() {
    if ($this->getPassword() !== '') {
      return true;
    }
    return false;
  }

  public function validUserSalt() {
    if ($this->getUserSalt() !== '') {
      return true;
    }
    return false;
  }

  // getter and setter methods
  //
  public function getPassword() {
    return $this->password;
  } 

  public function getUserSalt() {
    return $this->user_salt;
  }

  public function getIsAdmin() {
    return $this->is_admin;
  }

  public function getIsActive() {
    return $this->is_active;
  }

  public function setPassword($password) {
    $this->password = $password;
  }

  public function setUserSalt($user_salt) {
    $this->user_salt = $user_salt;
  }

  public function setIsAdmin($is_admin) {
    $this->is_admin = $is_admin;
  }

  public function setIsActive($is_active) {
    $this->is_active = $is_active;
  }
}
  
