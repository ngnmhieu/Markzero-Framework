<?php
class User extends AppModel {
  private static $table = 'user';
  /* 
   * Make a normal query with PDO
   * Values can be binded via an associative array
   * Return an array of model object which represent the result
  **/

  public static function all() {
    return self::query("SELECT * FROM ".self::$table);
  } 

  public static function query($query, $bindings = array()) { 
    $stmt = Application::$db->prepare($query);
    foreach($bindings as $field => $value) {
      self::bind($stmt, $field, $value);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  private static function bind($stmt, $field, $value) {
    $type = '';
    switch (true) {
      case is_int($value):
        $type = PDO::PARAM_INT;
        break;
      case is_bool($value):
        $type = PDO::PARAM_BOOL;
        break;
      case is_null($value):
        $type = PDO::PARAM_NULL;
        break;
      default:
        $type = PDO::PARAM_STR;
    }
    $stmt->bindValue($field, $value, $type);
  }
}
