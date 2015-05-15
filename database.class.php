<?php
require_once 'config.inc.php';

class Database
{
  private static $_handle;

  public static function getHandle() {
    global $dbconfig;
    if( is_null(self::$_handle) ) {
      self::$_handle = new PDO("mysql:host={$dbconfig['host']};" .
        "dbname={$dbconfig['dbname']};" .
        "charset={$dbconfig['charset']}",
        $dbconfig['user'],
        $dbconfig['password'],
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    return self::$_handle;
  }
}
