<?php
  $session_gw = new SessionGateway($dbconfig);
  $user_gw = new UserGateway($dbconfig);
  $user = new User($user_gw);
  $session = new Session($session_gw);

  $auth = new Auth($user, $session, $user_gw);