<?php
use Doctrine\Common\Collections\ArrayCollection;
/** 
 * @Entity @Table(name="categories") 
 **/
class Category extends AppModel {
  protected static $attr_reader = ['id', 'transactions'];
  protected static $attr_accessor = ['name', 'description'];

  /** @Id @Column(type="integer") @GeneratedValue **/
  protected $id;
  /** @Column(type="string") **/
  protected $name;
  /** @Column(type="string")**/
  protected $description;
  /** @OneToMany(targetEntity="Transaction", mappedBy="categories") **/
  protected $transactions;

  function __construct() {
    $this->time = new \DateTime();
  }

  static function find($id) {
    $em = App::$entity_manager->getRepository(__CLASS__);
    return $em->find($id);
  }
}
