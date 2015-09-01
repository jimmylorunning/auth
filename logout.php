<?php
  require_once 'auth.class.php';
  require_once 'config.inc.php';
  
  $user = new User();
  $auth = new Auth($user);
  $auth->logout();
?>
OK.
