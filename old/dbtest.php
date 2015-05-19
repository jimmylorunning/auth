<?php
   require_once('../../../../configuration.php'); 
   openDB();

   function openDB()
   {
      //global database connection
      global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db;
      mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password) or  die("Could not connect: " . mysql_error());
      mysql_select_db($mosConfig_db);
   }

   $query = "SELECT * FROM `teenvrp` LIMIT 10";
   $result = mysql_query($query);
   $num_rows = mysql_num_rows($result);
   echo '<h3>Using mysql_ functions</h3>';
   while($row = mysql_fetch_assoc($result)) {
      echo $row['firstname'] . '<br />';
   }

   echo '<h3>Using PDO functions</h3>';

   $db = new PDO("mysql:host=$mosConfighost;dbname=$mosConfig_db;charset=utf8", 
     $mosConfig_user, $mosConfig_password, 
     array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
   $pdo_statement = $db->query($query);
   $pdo_results = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
   foreach($pdo_results as $row) {
      echo $row['firstname'] . '<br />';
   }
?>

