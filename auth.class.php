<?php
require_once 'connectionfactory.class.php';

class Auth {
  const SUCCESSFUL = 1;
  const NOT_ACTIVE = 2;
  const ERROR_OCCURRED = 3;
  const REJECT = 4;
  const USER_EXISTS = 5;

  private $_siteKey;
  private $_pdo;
  private $_userGW;

/*  
  public function __construct(Gateway $userGateway = null, Gateway $userSessionGateway = null) {
    $this->_siteKey = "UTCu7Nt?C4#rK97()4zZkVzwJqVkJ&4&4{)k7vJLF,cQGo)4g4";
    $this->_userGW = $userGateway ? new userGateway();
    $this->_userSessionGW = $userSessionGateway ? new userSessionGateway();
  }
*/

  public function __construct() {
    $this->_siteKey = "UTCu7Nt?C4#rK97()4zZkVzwJqVkJ&4&4{)k7vJLF,cQGo)4g4";
    $this->_pdo = ConnectionFactory::getFactory()->getConnection();
  }

  public function createUser($email, $password, $is_admin = 0) {
    if ($this->userExists($email)) {
      return self::USER_EXISTS;
    }
    $user_salt = $this->randomString();
    $password = $this->saltAndHash($user_salt, $password);
    $created = $this->createUserPdo(array(
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
    $selection = $this->findUserByEmailPdo($email);
    if ($selection == null) {
      return self::REJECT;
    }
    $password = $this->saltAndHash($selection['user_salt'], $password);
    $is_active = (boolean) $selection['is_active'];

    if ($selection['password'] === $password) {
      if (!$is_active) {
        return self::NOT_ACTIVE;
      } 
      return $this->createSession($selection['id']); 
    }
    return self::REJECT;
  }

  public function checkSession() {
    session_start();
    $row = $this->retrieveSession();    
    if ($row) {
      if (session_id() == $row['session_id'] && 
        $_SESSION['token'] == $row['token']) {
          $this->refreshSession();
          return $row['user_id'];
      }
    }
    return false;
  }

  // to do: instantiate User instance (first I'd have to write a User class though)
  public function currentUser() {
    $user_id = $this->checkSession();
    if ($user_id) {
      return $this->findUserByIdPdo($user_id);
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
    session_start();
    // use all 3 strategies, just to make sure
    $this->removeSessionByUserIdPdo($_SESSION['user_id']);
    $this->removeSessionByTokenPdo($_SESSION['token']);
    $this->removeSessionByIdPdo(session_id());
    session_destroy();
  }
    

  private function userExists($email) {
    $selection = $this->findUserByEmailPdo($email);
    if ($selection) {
      return true;
    }
    return false; 
  }

  private function refreshSession() {
    session_start(); // in case it hasn't already been called
    session_regenerate_id();
    $token = $this->createToken();
    $_SESSION['token'] = $token;
    return $this->removeAndCreateSession($_SESSION['user_id'], $token);
  }

  private function createSession($user_id) {
    $token = $this->createToken();
    session_start();
    $_SESSION['token'] = $token;
    $_SESSION['user_id'] = $user_id;
    return $this->removeAndCreateSession($user_id, $token);
  }

  private function removeAndCreateSession($user_id, $token) {
    if ($this->removeSessionByUserIdPdo($user_id) &&
      $this->createSessionPdo($user_id, $token)) {
        return self::SUCCESSFUL;
    }
    return self::ERROR_OCCURRED;
  }

  private function retrieveSession() {
    $user_id = $_SESSION['user_id'];
    $row = $this->retrieveSessionPdo($user_id);
    return $row;
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

  // ******** PDO Functions ******* (move into a separate class later) ********* //

  private function createUserPdo($user) {
    $sql = "INSERT INTO `users` (email,password,user_salt,is_admin,is_active) " .
      "VALUES (:email,:password,:user_salt,:is_admin,:is_active)";
    $q = $this->_pdo->prepare($sql);
    $q->execute($user);
    return $q->rowCount();
  }

  private function findUserByEmailPdo($email) {
    $sql = "SELECT * FROM `users` WHERE `email` = :email";
    $q = $this->_pdo->prepare($sql);
    $q->execute(array(':email' => $email));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  private function findUserByIdPdo($user_id) {
    $sql = "SELECT * FROM `users` WHERE `id` = :user_id";
    $q = $this->_pdo->prepare($sql);
    $q->execute(array(':user_id' => $user_id));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row;
  }

  private function removeSessionByUserIdPdo($user_id) {
    $sql = "DELETE FROM `user_sessions` WHERE `user_id` = :user_id";
    $q = $this->_pdo->prepare($sql);
    return $q->execute(array(":user_id" => $user_id));
  }

  private function removeSessionByTokenPdo($token) {
    $sql = "DELETE FROM `user_sessions` WHERE `token` = :token";
    $q = $this->_pdo->prepare($sql);
    return $q->execute(array(":token" => $token));
  }

  private function removeSessionByIdPdo($session_id) {
    $sql = "DELETE FROM `user_sessions` WHERE `id` = :id";
    $q = $this->_pdo->prepare($sql);
    return $q->execute(array(":id" => $session_id));
  }

  private function createSessionPdo($user_id, $token) {
    $session_id = session_id();
    if ($session_id === "") {
      return false;
    }
    $sql = "INSERT INTO  `user_sessions` (user_id,session_id,token) "
      . "VALUES (:user_id,:session_id,:token)";
    $q = $this->_pdo->prepare($sql);
    return $q->execute(array(":user_id" => $user_id,
      ":session_id" => $session_id,
      ":token" => $token));
  }

  private function retrieveSessionPdo($user_id) {
    $sql = "SELECT * FROM `user_sessions` WHERE `user_id` = :user_id";
    $q = $this->_pdo->prepare($sql);
    $q->execute(array(':user_id' => $user_id));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row;
  } 
}
?>
