<?php
if ($_POST) {
  require_once 'shared/config.inc.php';

  $session_gw = new SessionGateway(ConnectionFactory::getFactory()->getConnection());
  $user = new User();
  $user_gw = new UserGateway();
  $session = new Session($session_gw);
  $auth = new Auth($user, $session, $user_gw);
  echo $auth->createUser($_POST['email'], $_POST['password']);
  
} else { ?>
  <form action="signup.php" method="post">
  Email: <input type="text" name="email" value=""><br />
  Password: <input type="password" name="password" value=""><br />
  <input type="submit">
  </form>
<?php } ?>
