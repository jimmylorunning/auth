<?php
require_once 'connectionfactory.class.php';
require_once 'usergateway.class.php';
require_once 'session.class.php';

class Auth {
  const SUCCESSFUL = 1;
  const NOT_ACTIVE = 2;
  const ERROR_OCCURRED = 3;
  const REJECT = 4;
  const USER_EXISTS = 5;

  private $_siteKey;
  private $_user_gw;
  private $_session;

  public function __construct() {
    $this->_siteKey = "UTCu7Nt?C4#rK97()4zZkVzwJqVkJ&4&4{)k7vJLF,cQGo)4g4";
    $this->_user_gw = new UserGateway();
    $this->_session = new Session();
  }

  public function createUser($email, $password, $is_admin = 0) {
    if ($this->userExists($email)) {
      return self::USER_EXISTS;
    }
    $user_salt = $this->randomString();
    $password = $this->saltAndHash($user_salt, $password);
    $created = $this->_user_gw->create(array(
        ':email' => $email,
        ':password' => $password,
        ':user_salt' => $user_salt,
        ':is_admin' => $is_admin,
        ':is_active' => 1));
    if($created != 0) {
      return self::SUCCESSFUL;
    }
    return self::ERROR_OCCURRED;
  }
  
  public function login($email, $password) {
    $selection = $this->_user_gw->findBy('email', $email);
    if ($selection == null) {
      return self::REJECT;
    }
    $password = $this->saltAndHash($selection['user_salt'], $password);
    $is_active = (boolean) $selection['is_active'];

    if ($selection['password'] === $password) {
      if (!$is_active) {
        return self::NOT_ACTIVE;
      } 
      $rows = $this->_session->create($selection['id'], $this->createToken()); 
      return $rows ? self::SUCCESSFUL : self::ERROR_OCCURRED;
    }
    return self::REJECT;
  }

  public function checkSession() {
    return $this->_session->refreshIfValid($this->createToken());
  }

  // to do: instantiate User instance (first I'd have to write a User class though)
  public function currentUser() {
    $user_id = $this->_session->isValid();
    if ($user_id) {
      return $this->_user_gw->findById($user_id);
    }
    return false;
  }

  // note: this is a placeholder... I want to take this out and replace with $user->isAdmin()
  public function isAdmin() {
    // $selection is array of the row returned from database
    if($selection['is_admin'] == 1) {
      return true;
    }
    return false;
  }

  public function logout() {
    $this->_session->destroy();
  }

  private function userExists($email) {
    $selection = $this->_user_gw->findBy('email', $email);
    if ($selection) {
      return true;
    }
    return false; 
  }

  private function saltAndHash($salt, $password) {
    $password = $this->saltPassword($salt, $password);
    return $this->hashData($password);
  }

  private function saltPassword($salt, $password) {
    return($salt . $password);
  }

  private function hashData($data) {
    return hash_hmac('sha512', $data, $this->_siteKey);
  }

  private function randomString($length = 50) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*(){}[];:<>?,. ';
    $string = '';

    for ($p = 0; $p < $length; $p++) {
      $string .= $characters[mt_rand(0, strlen($characters) -1)];
    }
    return $string;
  }

  private function createToken() {
    $random = $this->randomString();
    $token = $_SERVER['HTTP_USER_AGENT'] . $random;
    $token = $this->hashData($token);
    return $token;
  }
}
