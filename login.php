<?php
if ($_POST) {
  require_once 'loadclasses.php';

  $session_gw = new SessionGateway(ConnectionFactory::getFactory()->getConnection());
  $user = new User();
  $user_gw = new UserGateway();
  $session = new Session($session_gw);

  $auth = new Auth($user, $session, $user_gw);
  $authcode = $auth->login($_POST['email'], $_POST['password']);
  echo $authcode;

} else { ?>
  <form action="login.php" method="post">
  Email: <input type="text" name="email" value=""><br />
  Password: <input type="password" name="password" value=""><br />
  <input type="submit">
  </form>
<?php } ?>
