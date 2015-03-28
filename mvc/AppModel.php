<?php
namespace Markzero\Mvc;

use Markzero\App;
use Markzero\Validation;
use Markzero\Validation\Exception\ValidationException;

/**
 * @MappedSuperClass
 * @HasLifecycleCallbacks
 */
class AppModel {
  /**
   * Names of callbacks, which are invoked on the event PrePersist and Update
   */
  private $prePersistUpdateCallbacks = array('_validate');
  private $prePersistCallbacks = array('_default'); // _TODO: consider constructor as the function for setting default value
  private $preUpdateCallbacks  = array();


  /**
   * Run all the registered PrePersist and PreUpdate callback in order
   *
   * @PrePersist
   * @PreUpdate
   */
  public final function _prePersistAndUpdate() {
    foreach ($this->prePersistUpdateCallbacks as $callback) {
      if (method_exists($this, $callback)) {
        $this->{$callback}();
      }
    }
  }

  /**
   * Run all the PrePersist callback in order
   * @PrePersist
   */
  public final function _prePersist() {
    foreach ($this->prePersistCallbacks as $callback) {
      if (method_exists($this, $callback)) {
        $this->{$callback}();
      }
    }
  }

  /**
   * Run all the PreUpdate callback in order
   * @PreUpdate
   */
  public final function _preUpdate() {
    foreach ($this->preUpdateCallbacks as $callback) {
      if (method_exists($this, $callback)) {
        $this->{$callback}();
      }
    }
  }

  /**
   * _TODO: should it be abstract
   * Set up default values for attributes
   * Concrete models override this method to assign default values to attributes
   */
  protected function _default() {
  }

  /**
   * _TODO: should it be abstract?
   * Perform validation 
   * Concrete models override this method to perform specific validations
   * @throw Markzero\Validation\Exception\ValidationException ($array_errors)
   */
  protected function _validate() {
  }

  /**
   * @return boolean | is this entity valid
   */
  public function is_valid() {
    try {
      $this->_validate();
    } catch (ValidationException $e) {
      return false;
    }
    return true;
  }

  /**
   * Get a new ValidationManager object
   */
  static function createValidationManager() {
    return new Validation\ValidationManager();
  }

  /**
   * Return the application EntityManager
   * @return Doctrine\ORM\EntityManager
   */
  static function getEntityManager() {
    return App::$em;
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#find
   **/
  static function find($id, $lock_mode = \Doctrine\DBAL\LockMode::NONE, $lock_version = null) {
    $result = self::getRepo()->find($id, $lock_mode, $lock_version);
    if ($result === null)
      throw new ResourceNotFoundException();
    return $result;
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#findAll
   * find all entities
   **/
  static function findAll() {
    return self::getRepo()->findAll();
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#createQueryBuilder
   */
  static function createQueryBuilder($alias) {
    return self::getRepo()->createQueryBuilder($alias);
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#createNamedQuery
   */
  static function createNamedQuery($queryName) {
    return self::getRepo()->createNamedQuery($queryName);
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#clear
   */
  static function clear() {
    return self::getRepo()->clear();
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#findBy
   */
  static function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
    return self::getRepo()->findBy($criteria, $orderBy, $limit, $offset);
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#findOneBy
   */
  static function findOneBy(array $criteria) {
    return self::getRepo()->findOneBy($criteria);
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#getEntityName
   */
  static function getEntityName() {
    return self::getRepo()->getEntityName();
  }

  /**
   * Get the repository with the name of the current model
   **/
  static function getRepo() {
    $model = get_called_class();
    return self::getEntityMananger()->getRepository($model);
  }

  /**
   * Call getter for attributes if defined
   * otherwise return the attribute
   **/
  function __get($attr) {

    $getter = 'get'.ucfirst($attr); // getter name

    if (method_exists($this, $getter)) {
      return $this->{$getter}();
    } else if (property_exists(get_called_class(), $attr) 
               && ((property_exists(get_called_class(), 'attr_reader') 
                   && is_array(static::$attr_reader) 
                   && in_array($attr, static::$attr_reader))
                 || 
                   (property_exists(get_called_class(), 'attr_accessor') 
                   && is_array(static::$attr_accessor) 
                   && in_array($attr, static::$attr_accessor)))) {
      return $this->{$attr};
    }

    $trace = debug_backtrace();
    throw new AttributeNotFoundException(
       "Undefined attribute `$attr`:"
      .' in ' . $trace[0]['file']
      .' Line ' . $trace[0]['line']
    );
  }

  /**
   * Call setter for attributes if defined
   * otherwise set the attribute to the given value
   **/
  function __set($attr, $value) {

    $setter = 'set'.ucfirst($attr); // setter name

    if (method_exists($this, $setter)) {
      return call_user_func_array(array($this, $setter), func_get_args());
    } else if (property_exists(new static, $attr) 
               && ((property_exists(get_called_class(), 'attr_writer') 
                   && is_array(static::$attr_writer) 
                   && in_array($attr, static::$attr_writer))
                 || 
                   (property_exists(get_called_class(), 'attr_accessor') 
                   && is_array(static::$attr_accessor) 
                   && in_array($attr, static::$attr_accessor)))) {
      $this->{$attr} = $value;
      return $value;
    }

    $trace = debug_backtrace();
    throw new AttributeNotFoundException(
       "Undefined attribute `$attr`:"
      .' in ' . $trace[0]['file']
      .' Line ' . $trace[0]['line']
    );
  }

  /**
   * return attributes on call to  get{Atrribute}()
   * and set attributes value on call to  set{Atrribute}({val})
   * although we already have mechanism for accessing attribute,
   * these methods are required by doctrine
   **/
  function __call($name, $args) {
    if (preg_match('~get([A-Z].*)~', $name, $matches)) {
      $attr = lcfirst($matches[1]);
      return $this->{$attr};
    } else if (preg_match('~set([A-Z].*)~', $name, $matches)) {
      $attr = lcfirst($matches[1]);
      $this->{$attr} = $args[0];
      return $this->{$attr};
    }
    
    $trace = debug_backtrace();
    throw new BadMethodCallException(
      "Undefined method `$name()` in class `".get_class(new static)."`: ".
      ' in ' . $trace[0]['file'] .
      ' Line ' . $trace[0]['line'],
      E_USER_NOTICE
    );
  }

  /**
   * Return array containing attributes of the model
   * @return array
   */
  public function to_array() {
    $attributes = array_merge(static::$attr_reader, static::$attr_accessor);

    $array = array();
    foreach ($attributes as $attr) {
      if (method_exists($this->{$attr}, 'to_array')) {
        $array[$attr] = $this->{$attr}->to_array();
      } else {
        $array[$attr] = $this->{$attr};
      }
    }

    return $array;
  }

}
