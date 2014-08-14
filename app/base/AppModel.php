<?php
class AppModel {

  // private $data = array();

  // function __construct($data) {
  //   $this->data = $data;
  // }

  /* 
   * Make a normal query with PDO
   * Values can be binded via an associative array
   * Return an array of model object which represent the result
  **/
  // static function query($query, $bindings = array()) { 
  //   $stmt = App::$db->prepare($query);
  //   foreach($bindings as $field => $value) {
  //     self::bind($stmt, $field, $value);
  //   }

  //   $stmt->execute();

  //   return array_map(function($row) {
  //     // create new object of the same class
  //     // with all the properties populated to $this->data
  //     return new static($row);
  //   },$stmt->fetchAll(PDO::FETCH_ASSOC));

  // }

  // access object property $obj->property
  // is actually access $obj->data[property]
  // public function __get($name) {
  //   return array_key_exists($name, $this->data) ? $this->data[$name] : null;
  // }

  // private static function bind($stmt, $field, $value) {
  //   $type = '';
  //   switch (true) {
  //     case is_int($value):
  //       $type = PDO::PARAM_INT;
  //       break;
  //     case is_bool($value):
  //       $type = PDO::PARAM_BOOL;
  //       break;
  //     case is_null($value):
  //       $type = PDO::PARAM_NULL;
  //       break;
  //     default:
  //       $type = PDO::PARAM_STR;
  //   }
  //   $stmt->bindValue($field, $value, $type);
  // }
}
