<?php
class AppModel {

  function __construct() {
  }

  /**
   * find entities by ID
   * @var int $id
   **/
  static function find($id) {
    $repo = self::getRepo();
    return $repo->find($id);
  }

  /**
   * get the repository with the name of the current model
   **/
  static function getRepo() {
    $model = get_called_class();
    return App::$entity_manager->getRepository($model);
  }

  /**
   * call getter for attributes if defined
   * otherwise return the attribute
   **/
  function __get($attr) {
    $getter = 'get'.ucfirst($attr); // getter name
    if (method_exists($this, $getter)) {
      return $this->{$getter}();
    } else if (property_exists(new static, $attr) 
               && ((property_exists(get_called_class(), 'attr_reader') 
                   && is_array(static::$attr_reader) 
                   && in_array($attr, static::$attr_reader))
                 || 
                   (property_exists(get_called_class(), 'attr_accessor') 
                   && is_array(static::$attr_accessor) 
                   && in_array($attr, static::$attr_accessor)))) {
      return $this->{$attr};
    }

    // Attribute not found, generate error
    $trace = debug_backtrace();
    trigger_error(
      'Undefinierte Eigenschaft for __get(): ' . $attr .
      ' in ' . $trace[0]['file'] .
      ' Zeile ' . $trace[0]['line'],
      E_USER_NOTICE);
    return null;
  }

  /**
   * call setter for attributes if defined
   * otherwise set the attribute to the given value
   **/
  function __set($attr, $value) {
    $setter = 'set'.ucfirst($attr); // setter name
    if (method_exists($this, $setter)) {
      call_user_func_array([$this, $setter], func_get_args());
      return;
    } else if (property_exists(new static, $attr) 
               && ((property_exists(get_called_class(), 'attr_writer') 
                   && is_array(static::$attr_writer) 
                   && in_array($attr, static::$attr_writer))
                 || 
                   (property_exists(get_called_class(), 'attr_accessor') 
                   && is_array(static::$attr_accessor) 
                   && in_array($attr, static::$attr_accessor)))) {
      $this->{$attr} = $value;
      return;
    }

    // Attribute not found, generate error
    $trace = debug_backtrace();
    trigger_error(
      'Undefinierte Eigenschaft for __set(): ' . $attr .
      ' in ' . $trace[0]['file'] .
      ' Zeile ' . $trace[0]['line'],
      E_USER_NOTICE);
    return null;

  }
}
