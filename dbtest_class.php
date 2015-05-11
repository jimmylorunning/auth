<?php



   require_once('../../../../configuration.php'); 



   class Teen {

     private $firstname;

     private $lastname;



     public function name() {

       return $this->firstname . ' ' . $this->lastname;

     }

   }



   $db = new PDO("mysql:host=$mosConfighost;dbname=$mosConfig_db;charset=utf8", 

         $mosConfig_user, $mosConfig_password, 

         array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

   $query = "SELECT * FROM `teenvrp` LIMIT 10";



/***** using fetch() to get them one at a time ****

 *****  works, but commenting this out for now ****

 *****  so we can try fetchAll() instead       ****



   $pdo_statement = $db->query($query);

 

   $pdo_statement->setFetchMode(PDO::FETCH_CLASS, 'Teen');

   $pdo_statement->execute();



   $pdo_results = $pdo_statement->fetch(PDO::FETCH_CLASS);

   print_r($pdo_results);

   echo '<br />' . $pdo_results->name() . '<br />';



   $pdo_results = $pdo_statement->fetch(PDO::FETCH_CLASS);

   print_r($pdo_results);

   echo '<br />' . $pdo_results->name() . '<br />';

 **************************************************

 ****** end fetch() *****/



/***** using fetchAll() with FETCH_CLASS parameter *****/



   $pdo_statement = $db->prepare($query);

   $pdo_statement->execute();



   $pdo_results = $pdo_statement->fetchAll(PDO::FETCH_CLASS, 'Teen');

// var_dump($pdo_results);

   foreach($pdo_results as $teen) {

      echo $teen->name() . '<br />';

   }

	

?>

