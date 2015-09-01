<?php
require_once 'user.class.php';
require_once 'session.class.php';
require_once 'usergateway.class.php';

class Auth {
  const SUCCESSFUL = 1;
  const NOT_ACTIVE = 2;
  const ERROR_OCCURRED = 3;
  const REJECT = 4;
  const USER_EXISTS = 5;
  const NO_USER = 6;

  private $_siteKey;
  private $_session;
  private $_user;
  private $_user_gw;

  public function __construct($user, $user_gw=null) {
    $this->_siteKey = "UTCu7Nt?C4#rK97()4zZkVzwJqVkJ&4&4{)k7vJLF,cQGo)4g4";
    $this->_session = new Session();
    $this->_user = $user;
    if (!$user_gw) {
      $this->_user_gw = new UserGateway();
    } else {
      $this->_user_gw = $user_gw;
    }
  }

  public function createUser($email, $password, $is_admin = 0) {
    if (!$this->_user) {
      return self::NO_USER;
    }
    if (!$this->validEmailAndPassword($email, $password)) {
      return self::ERROR_OCCURRED;
    }
    if ($this->_user_gw->existsBy('email', $email)) {
      return self::USER_EXISTS;
    } 
    $user_salt = $this->randomString();
    $password = $this->saltAndHash($user_salt, $password);
    $this->_user->import(array(
        ':email' => $email,
        ':password' => $password,
        ':user_salt' => $user_salt,
        ':is_admin' => $is_admin,
        ':is_active' => 1), ':');
    $created = $this->_user->create();
    if($created) {
      return self::SUCCESSFUL;
    }
    return self::ERROR_OCCURRED;
  }
  
  public function login($email, $password) {
    if ($user = $this->_user_gw->findBy('email', $email, true)) {
      $password = $this->saltAndHash($user->getUserSalt(), $password);
      $is_active = (boolean) $user->getIsActive();

      if ($user->getPassword() === $password) {
        if (!$is_active) {
          return self::NOT_ACTIVE;
        } 
        $rows = $this->_session->create($user->id, $this->createToken()); 
        return $rows ? self::SUCCESSFUL : self::ERROR_OCCURRED;
      }
    }
    return self::REJECT;
  }

  public function checkSession() {
    return $this->_session->refreshIfValid($this->createToken());
  }

  public function currentUser() {
    $user_id = $this->_session->isValid();
    if ($user_id) {
      return User::getUserBy('id', $user_id);
    }
    return false;
  }

  public function logout() {
    $this->_session->destroy();
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

  private function validEmailAndPassword($email, $password) {
    if ($this->validEmail($email) &&
      $this->validPassword($password)) {
      return true;
    }
    return false;
  }

  private function validEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    return true;
  }

  private function validPassword($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);

    if(!$uppercase || !$lowercase || strlen($password) < 8) {
      return false;
    }
    return true;
  }
}
