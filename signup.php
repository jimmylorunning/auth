<?php
if ($_POST) {
  require_once 'auth.class.php';
  require_once 'config.inc.php';
  
  $pdo = Auth::newPdo($dbconfig);
  $auth = new Auth($pdo);
  echo $auth->createUser($_POST['email'], $_POST['password']);
  
} else { ?>
  <form action="signup.php" method="post">
  Email: <input type="text" name="email" value=""><br />
  Password: <input type="password" name="password" value=""><br />
  <input type="submit">
  </form>
<?php } ?>
