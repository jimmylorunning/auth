<?php
  require_once 'auth.class.php';
  require_once 'config.inc.php';
  
  $pdo = Auth::newPdo($db, $dbconfig);
  $auth = new Auth($pdo);
  $auth->logout();
?>
OK.
