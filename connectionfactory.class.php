<?php
require_once 'config.inc.php';

class ConnectionFactory 
{
  private static $_factory; 
  private $_connection;

  public static function getFactory() {
    if (!self::$_factory)
      self::$_factory = new ConnectionFactory();
    return self::$_factory;
  }

  public function getConnection() {
    global $dbconfig;
    if( is_null($_connection) ) {
      $_connection = new PDO("mysql:host={$dbconfig['host']};" .
        "dbname={$dbconfig['dbname']};" .
        "charset={$dbconfig['charset']}",
        $dbconfig['user'],
        $dbconfig['password'],
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    return $_connection;
  }
}
