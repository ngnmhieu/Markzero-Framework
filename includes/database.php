<?php
/**
 * Database class initiates connection with MySQL servers
 * TODO: should database information be mixed with application static data?
 * if it's isolated, it's better
 * should it be initialized with data in app.php?
 */
class Database {
  private $user;
  private $host; // TODO: 'localhost' cause error, why?
  private $database;
  private $password;
  private $options = array(
    PDO::ATTR_PERSISTENT => true, 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );
  private $connection;

  public function __construct($user, $host, $pass, $dbname) {
    $this->user = $user;
    $this->host = $host;
    $this->password = $pass;
    $this->database = $dbname;

    $this->connect();
  }

  public function prepare($query) {
    return $this->connection->prepare($query);
  }


  /*
   * Connect to the database with given credentials
   * In this version, connect() only works with mysql
   */
  private function connect() {
    $dsn = "mysql:dbname={$this->database};host={$this->host}";
    try {
      $this->connection = new PDO($dsn, $this->user, $this->password, $this->options);
    } catch(PDOException $e) {
      // TODO: Need better way to handle database exception, we need a system
      echo 'Connection Failed:' . $e->getMessage();
    }
  } 


  /*
   * The connection is closed automatically
   * when database object is set to null
   */
  function __destruct() {
    $this->connection = null;
  }
}
