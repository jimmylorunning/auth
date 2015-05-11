<?php
  require_once 'auth.class.php';
  require_once 'config.inc.php';

  $auth = new Auth($db, $dbconfig);
  if ($auth->checkSession()) {
    echo "you're logged in";
  } else {
    echo "you're logged out";
  }
?>
