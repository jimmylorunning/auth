<?php
class User {
  private $_user_gw;
  public $email;
  public $id;
  private $password;
  private $user_salt;
  private $is_admin;
  private $is_active;

  public function __construct($user_gw=null) {
    $this->init($user_gw);
  }

  public function init($user_gw) {
    $this->_user_gw = $user_gw;
  }

  public function import($user_array) {
    if (array_key_exists($pre . 'id', $user_array)) {
      $this->id = $user_array['id'];
    }
    $this->email = $user_array['email'];
    $this->setPassword($user_array['password']);
    $this->setUserSalt($user_array['user_salt']);
    $this->setIsAdmin($user_array['is_admin']);
    $this->setIsActive($user_array['is_active']);
  }

  public function export($pre='') {
    return array(
      $pre . 'email' => $this->email,
      $pre . 'password' => $this->getPassword(),
      $pre . 'user_salt' => $this->getUserSalt(),
      $pre . 'is_admin' => $this->getIsAdmin(),
      $pre . 'is_active' => $this->getIsActive());
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
    if (!$this->_user_gw->findBy('email', $this->email)) {
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
  
