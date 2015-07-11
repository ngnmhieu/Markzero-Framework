<?php
namespace Markzero\Mvc;

use Markzero\App;
use Markzero\Validation;
use Markzero\Exception\AttributeNotFoundException;
use Markzero\Validation\Exception\ValidationException;

/**
 * @MappedSuperClass
 * @HasLifecycleCallbacks
 */
abstract class AppModel 
{
  /**
   * Names of callbacks, which are invoked on the event PrePersist and Update
   */
  private $prePersistUpdateCallbacks = array('_validate');
  private $prePersistCallbacks  = array();
  private $preUpdateCallbacks  = array();


  /**
   * Run all the registered PrePersist and PreUpdate callback in order
   *
   * @PrePersist
   * @PreUpdate
   */
  public final function _prePersistAndUpdate()
  {
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
  public final function _prePersist()
  {
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
  public final function _preUpdate()
  {
    foreach ($this->preUpdateCallbacks as $callback) {
      if (method_exists($this, $callback)) {
        $this->{$callback}();
      }
    }
  }

  /**
   * Perform validation 
   * Concrete models override this method to perform specific validations
   *
   * @throw Markzero\Validation\Exception\ValidationException
   */
  abstract protected function _validate();

  /**
   * If _validate doesn't throw ValidationException then the entity is valid
   *
   * @return boolean is this entity valid
   */
  public function isValid()
  {
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
  static function createValidationManager()
  {
    return new Validation\ValidationManager();
  }

  /**
   * Return the application EntityManager
   * @return Doctrine\ORM\EntityManager
   */
  static function getEntityManager()
  {
    return App::$em;
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#find
   *
   * @return object|null Null is returned if the entity cannot be found or $id is null
   **/
  static function find($id, $lock_mode = \Doctrine\DBAL\LockMode::NONE, $lock_version = null)
  {
    if ($id == null)
      return null;

    return self::getRepo()->find($id, $lock_mode, $lock_version);;
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#findAll
   * find all entities
   *
   * @return array The entities.
   **/
  static function findAll()
  {
    return self::getRepo()->findAll();
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#createQueryBuilder
   */
  static function createQueryBuilder($alias)
  {
    return self::getRepo()->createQueryBuilder($alias);
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#createNamedQuery
   */
  static function createNamedQuery($queryName)
  {
    return self::getRepo()->createNamedQuery($queryName);
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#clear
   */
  static function clear()
  {
    return self::getRepo()->clear();
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#findBy
   */
  static function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
  {
    return self::getRepo()->findBy($criteria, $orderBy, $limit, $offset);
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#findOneBy
   */
  static function findOneBy(array $criteria, array $orderBy = null)
  {
    return self::getRepo()->findOneBy($criteria, $orderBy);
  }

  /**
   * Proxy for Doctrine\ORM\EntityRepository#getEntityName
   */
  static function getEntityName()
  {
    return self::getRepo()->getEntityName();
  }

  /**
   * Get the repository with the name of the current model
   **/
  static function getRepo() 
  {
    $model = get_called_class();

    return self::getEntityManager()->getRepository($model);
  }

  /**
   * Delete an entity with the given $id
   *
   * @param int $id
   * @throws Markzero\Http\Exception\ResourceNotFoundException
   */
  public static function delete($id)
  {
    $object = self::find($id);

    if ($object === null) {
      throw new ResourceNotFoundException();
    }

    $object->destroy();
  }

  /**
   * Delete current entity
   * @throw Exception
   */
  public function destroy()
  {
    $em = self::getEntityManager();

    $conn = $em->getConnection();

    $conn->beginTransaction();

    try {
      $em->remove($this); 

      $em->flush();
      $conn->commit();

    } catch(Exception $e) {

      $conn->rollback();
      throw $e;
    }
  }



  /**
   * Call getter for attributes if defined
   * otherwise return the attribute
   **/
  function __get($attr)
  {

    $getter = 'get'.ucfirst($attr); // getter name

    if (method_exists($this, $getter)) {
      return $this->{$getter}();
    } else if (property_exists(get_called_class(), $attr) 
      && ((property_exists(get_called_class(), 'readable') 
      && is_array(static::$readable) 
      && in_array($attr, static::$readable))
      || 
      (property_exists(get_called_class(), 'accessible') 
      && is_array(static::$accessible) 
      && in_array($attr, static::$accessible)))) {
        return $this->{$attr};
      }

    throw new AttributeNotFoundException("Undefined attribute `$attr` accessed in object of class `".get_called_class()."`");
  }

  /**
   * Call setter for attributes if defined
   * otherwise set the attribute to the given value
   **/
  function __set($attr, $value)
  {

    $setter = 'set'.ucfirst($attr); // setter name
    $klass = get_called_class();

    if (method_exists($this, $setter)) {
      return call_user_func_array(array($this, $setter), func_get_args());
    } else if (property_exists($klass, $attr) 
      && ((property_exists($klass, 'writable') 
      && is_array(static::$writable) 
      && in_array($attr, static::$writable))
      || 
      (property_exists($klass, 'accessible') 
      && is_array(static::$accessible) 
      && in_array($attr, static::$accessible)))) {
        $this->{$attr} = $value;
        return $value;
      }

    throw new AttributeNotFoundException("Undefined attribute `$attr` accessed in object of class `".get_called_class()."`");
  }

  /**
   * return attributes on call to  get{Atrribute}()
   * and set attributes value on call to  set{Atrribute}({val})
   * although we already have mechanism for accessing attribute,
   * these methods are required by doctrine
   **/
  function __call($name, $args)
  {

    if (preg_match('~get([A-Z].*)~', $name, $matches)) {
      $attr = lcfirst($matches[1]);
      return $this->{$attr};
    } else if (preg_match('~set([A-Z].*)~', $name, $matches)) {
      $attr = lcfirst($matches[1]);
      $this->{$attr} = $args[0];
      return $this->{$attr};
    } 

    throw new \BadMethodCallException("Undefined method `$name()` accessed in object of class `".get_called_class()."`");
  }

  /**
   * For dynamic attributes to work with isset()
   */
  function __isset($name)
  {
    $is_defined = property_exists($this, $name);
    $is_readable = property_exists(get_called_class(), 'readable')
      && in_array($name, static::$readable);
    $is_accessible = property_exists(get_called_class(), 'accessible')
      && in_array($name, static::$accessible);

    return $is_defined && ($is_readable || $is_accessible);
  }

  /**
   * Return array containing attributes of the model
   * @return array
   */
  public function toArray()
  {
    $attributes = array_merge(static::$readable, static::$accessible);

    $array = array();
    foreach ($attributes as $attr) {
      if ($this->{$attr} instanceof \Traversable) {
        $items = iterator_to_array($this->{$attr});

        $array[$attr] = array_map(function($item) {
          return $item->toArray();
        }, $items);
      } else if (method_exists($this->{$attr}, 'toArray')) {
        $array[$attr] = $this->{$attr}->toArray();
      } else {
        $array[$attr] = $this->{$attr};
      }
    }

    return $array;
  }

}
