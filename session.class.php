<?php
require_once 'sessiongateway.class.php';

class Session {
  private $_session_gw;

  public function __construct() {
    session_start();
    $this->_session_gw = new SessionGateway();
  }

  public function findBy($key) {
    $val = $_SESSION[$key];
    $row = $this->_session_gw->findBy($key, $val);
    return $row;
  }

  public function find() {
    $row = $this->_session_gw->findBy("user_id", $_SESSION['user_id']);
    return $row;
  }

  public function create($user_id = null, $token = null) {
    if ($user_id) {
      $_SESSION['user_id'] = $user_id;
    }
    if ($token) {
      $_SESSION['token'] = $token;
    }
    $this->_session_gw->deleteBy('user_id', $user_id);
    return $this->_session_gw->create($this->asArray());
  }

  public function asArray() {
    return array(':user_id' => $_SESSION['user_id'],
      ':token' => $_SESSION['token'],
      ':session_id' => session_id());
  }

  public function isValid() {
    $row = $this->find();
/*    print_r($row);
    echo 'ss = ' . session_id();
    echo 'tk = ' . $_SESSION['token']; */
    if ($row) {
      if (session_id() == $row['session_id'] &&
        $_SESSION['token'] == $row['token']) {
          return $row['user_id'];
      }
    }
    return false;
  }

  public function refreshIfValid($token) {
    if ($user_id = $this->isValid()) {
      return $this->refresh($user_id, $token);
    }
    return false;
  }

  public function refresh($user_id, $token) {
    session_regenerate_id();
    $_SESSION['token'] = $token;
    return $this->create($user_id, $token);
  }
}
