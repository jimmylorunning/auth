<?php
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

  public function __construct($user, $session, $user_gw) {
    $this->_siteKey = "UTCu7Nt?C4#rK97()4zZkVzwJqVkJ&4&4{)k7vJLF,cQGo)4g4";
    $this->_session = $session;
    $this->_user = $user;
    $this->_user_gw = $user_gw;
  }

  public function createUser($email, $password, $is_admin = 0) {
    if (!$this->_user) {
      return self::NO_USER;
    }
    if (!$this->validEmailAndPassword($email, $password)) {
      return self::ERROR_OCCURRED;
    }
    if ($this->_user_gw->findBy('email', $email)) {
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
    if ($user_row = $this->_user_gw->findBy('email', $email)) {
      $this->_user->import($user_row);
      $password = $this->saltAndHash($this->_user->getUserSalt(), $password);

      if ($this->_user->getPassword() === $password) {
        if (!$this->_user->getIsActive()) {
          return self::NOT_ACTIVE;
        }
        $rows = $this->_session->create($this->_user->id, $this->createToken()); 
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
      if ($this->_user->id == $user_id) {
        return $this->_user;
      } else {
        $this->_user->import($this->_user_gw->findById($user_id));
        return $this->_user;
      }
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
