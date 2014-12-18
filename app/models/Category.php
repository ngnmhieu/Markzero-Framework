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

  function __construct() {
  }

  static function update($id, $params) {
    $params->permit(array('name', 'description'));
    $obj = static::find($id);
    $obj->name        = $params['name'];
    $obj->description = $params['description'];

    try {
      App::$entity_manager->persist($obj);
      App::$entity_manager->flush();
    } catch(ValidationException $e) {
    }

    return $obj;
  }

  /**
   * create and save an Category entity
   * @var $params
   **/
  static function create($params) {
    $params->permit(array('name', 'description'));
    $obj = new static();
    $obj->name        = $params['name'];
    $obj->description = $params['description'];

    try {
      App::$entity_manager->persist($obj);
      App::$entity_manager->flush();
    } catch(ValidationException $e) {
      print_r($e);
    }

    return $obj;
  }

  static function delete($id) {
    $cat = static::find($id);
    App::$entity_manager->remove($cat); 
    App::$entity_manager->flush();
    return true;
  }
}
