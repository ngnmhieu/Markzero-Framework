<?php
use Doctrine\Common\Collections\ArrayCollection;
/** 
 * @Entity @Table(name="categories") 
 **/
class Category extends AppModel {
  protected static $attr_reader = array('id');
  protected static $attr_accessor = array('name', 'description');

  /** @Id @Column(type="integer") @GeneratedValue **/
  protected $id;
  /** @Column(type="string") **/
  protected $name;
  /** @Column(type="string")**/
  protected $description;
  /** @OneToMany(targetEntity="Transaction", mappedBy="categories") **/
  protected $transactions;

  protected function _validate() {
    $vm = self::createValidationManager();

    $vm->validate('name', new FunctionValidator(function($name) {
      return !empty($name);
    }, array($this->name)), "Name cannot be empty");
    
    $vm->do_validate();
  }

  /**
   * @throw ValidationException
   */
  static function update($id, $params) {
    $obj = static::find($id);
    $obj->name        = $params->get('name');
    $obj->description = $params->get('description');

    App::$em->persist($obj);
    App::$em->flush();

    return $obj;
  }

  /**
   * create and save an Category entity
   * @var $params
   * @throw ValidationException
   **/
  static function create($params) {
    $obj = new static();
    $obj->name        = $params->get('name', '');
    $obj->description = $params->get('description', '');

    App::$em->persist($obj);
    App::$em->flush();

    return $obj;
  }

  /**
   * @throw Exception
   */
  static function delete($id) {
    App::$em->getConnection()->beginTransaction();
    try {
      $cat = static::find($id);
      App::$em->remove($cat); 

      $query = App::$em->createQuery('DELETE FROM Transaction t WHERE IDENTITY(t.category) = :id');
      $query->setParameter('id', $id);
      $query->execute();

      App::$em->flush();
      App::$em->getConnection()->commit();
    } catch(Exception $e) {
      App::$em->getConnection()->rollback();
      throw $e;
    }
  }
}
