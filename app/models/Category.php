<?php
use Doctrine\Common\Collections\ArrayCollection;
/** 
 * @Entity @Table(name="categories") 
 **/
class Category extends AppModel {
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
    $this->transactions = ArrayCollection();
  }

  public function getTransactions() {
    return $this->transactions;
  }

  public function getId() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getDesc() {
    return $this->desc;
  }

  public function setDesc($desc) {
    $this->desc = $desc;
  }

  static function find($id) {
    $em = App::$entity_manager->getRepository(__CLASS__);
    return $em->find($id);
  }
}
