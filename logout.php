<?php
  require_once 'loadclasses.php';

  $session_gw = new SessionGateway(ConnectionFactory::getFactory()->getConnection());
  $user = new User();
  $user_gw = new UserGateway();
  $session = new Session($session_gw);

  $auth = new Auth($user, $session, $user_gw);
  $auth->logout();
?>
OK.
