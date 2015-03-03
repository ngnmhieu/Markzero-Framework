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

  protected function validate() {
    $this->errors = array();

    if (empty($this->name)) {
      $this->errors['name'] = "Name cannot be empty";
    }

    return empty($this->errors); 
  }
  static function update($id, $params) {
    $obj = static::find($id);
    $obj->name        = $params->get('name');
    $obj->description = $params->get('description');

    try {
      App::$em->persist($obj);
      App::$em->flush();
    } catch(ValidationException $e) {
    }

    return $obj;
  }

  /**
   * create and save an Category entity
   * @var $params
   **/
  static function create($params) {
    $obj = new static();
    $obj->name        = $params->get('name', '');
    $obj->description = $params->get('description', '');

    try {
      App::$em->persist($obj);
      App::$em->flush();
    } catch(ValidationException $e) {
    }

    return $obj;
  }

  // TODO: cascade?
  static function delete($id) {
    $cat = static::find($id);
    App::$em->remove($cat); 
    App::$em->flush();
    return true;
  }
}
