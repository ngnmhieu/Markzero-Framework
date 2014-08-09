<?php
require_once(Application::$APP_PATH. "/config/db.php");

/**
 * Database class initiates connection with MySQL servers
 */
class Database {
  private $user = DB_USER;
  private $host = DB_HOST; // 'localhost' cause error
  private $database = DB_NAME;
  private $password = DB_PASS;
  private $cnx;

  public function __construct() {
    $this->connect();
  }

  public function connect() {
    // TODO: This should be moved to somewhere else, may in config file
    $options = array(
      PDO::ATTR_PERSISTENT => true, 
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

    $dsn = "mysql:dbname={$this->database};host={$this->host}";
    try {
      $this->cnx = new PDO($dsn, $this->user, $this->password, $options);
    } catch(PDOException $e) {
      // TODO: Need better way to handle database exception, we need a system
      echo 'Connection Failed:' . $e->getMessage();
    }
  } 
}
