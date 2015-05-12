<?php
  require_once 'auth.class.php';
  require_once 'config.inc.php';

  $auth = new Auth($db, $dbconfig);

  if ($auth->checkSession()) {
    $cs = "you're logged in";
  } else {
    $cs = "you're logged out";
  }

  $user = $auth->currentUser();
  if ($user) {
    $cu = "Welcome, {$user['email']}";
  } else {
    $cu = "User not found because you are NOT logged in!";
  }

  echo $cs . "<br />" . $cu;  
?>
