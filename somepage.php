<?php
  require_once 'loadclasses.php';

  $session_gw = new SessionGateway(ConnectionFactory::getFactory()->getConnection());
  $user = new User();
  $user_gw = new UserGateway();
  $session = new Session($session_gw);

  $auth = new Auth($user, $session, $user_gw);
  if ($auth->checkSession()) {
    $cs = "you're logged in";
  } else {
    $cs = "you're logged out";
  }

  $user = $auth->currentUser();
  if ($user) {
    $cu = "Welcome, {$user->email}";
  } else {
    $cu = "User not found because you are NOT logged in!";
  }

  echo $cs . "<br />" . $cu;  
?>
