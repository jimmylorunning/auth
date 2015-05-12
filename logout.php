<?php
  require_once 'auth.class.php';
  require_once 'config.inc.php';
  
  $auth = new Auth($db, $dbconfig);
  $auth->logout();
?>
OK.
