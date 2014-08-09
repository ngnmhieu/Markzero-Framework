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
  private $options = array(
    PDO::ATTR_PERSISTENT => true, 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );
  private $connection;

  public function __construct() {
    $this->connect();
  }

  private function connect() {
    $dsn = "mysql:dbname={$this->database};host={$this->host}";
    try {
      $this->connection = new PDO($dsn, $this->user, $this->password, $this->options);
    } catch(PDOException $e) {
      // TODO: Need better way to handle database exception, we need a system
      echo 'Connection Failed:' . $e->getMessage();
    }
  } 

  // the connection is closed automatically
  // when database object is set to null
  function __destruct() {
    $this->connection = null;
  }
}
