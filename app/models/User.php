<?php
class User extends AppModel {
  private static $table = 'user';

  public $id;
  public $name;
  public $password;
  public $gender;
  

  /*
   * @return if user found, return an User object. If not, return null
   */   
  public static function find_by_id($id) {
    $collection = self::query("SELECT * FROM ".self::$table." WHERE id = :id LIMIT 1", array(':id' => $id));
    return empty($collection) ? null : array_shift($collection);
  }

  /*
   * @return return all users from database
   */   
  public static function find_all() {
    return self::query("SELECT * FROM ".self::$table);
  } 

  /* 
   * Make a normal query with PDO
   * Values can be binded via an associative array
   * Return an array of model object which represent the result
   **/
  public static function query($query, $bindings = array()) { 
    $stmt = Application::$db->prepare($query);
    foreach($bindings as $field => $value) {
      self::bind($stmt, $field, $value);
    }

    $stmt->execute();

    return self::instantiate($stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  private static function instantiate($rows) {
    if (!is_array($rows))
      return null; 

    // turn all records into objects
    $obj_collection = array_map(function($row) {
      $obj = new self;
      foreach ($row as $attr => $value)
        if (property_exists($obj, $attr))
          $obj->$attr = $value;
      return $obj;
    }, $rows);

    return $obj_collection;
  }

  // bind parameters to PDO statement
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
