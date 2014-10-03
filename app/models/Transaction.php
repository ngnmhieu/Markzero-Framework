<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="transactions")
 **/
class Transaction extends AppModel {
  /** @Id @Column(type="integer") @GeneratedValue **/
  protected $id;
  /** @Column(type="float") **/
  protected $amount;
  /** @Column(type="string") **/
  protected $notice;
  /** @Column(type="datetime") **/
  protected $time;

  /** @ManyToOne(targetEntity="Category", inversedBy="transactions") **/
  protected $categories;

  function __construct() {
    $this->categories = new ArrayCollection();
  }

  public function assignToCategory($category) {
    $this->categories[] = $category;
  }

  public function getCategories() {
    return $this->categories;
  }

  public function getId() {
    return $this->id;
  }

  public function getAmount() {
    return $this->amount;
  }

  public function getNotice() {
    return $this->notice;
  }

  public function getTime() {
    return $this->time;
  }

  public function setAmount($amount) {
    $this->amount = $amount;
  }

  public function setNotice($notice) {
    $this->notice = $notice;
  }

  public function setTime($time) {
    $this->time = $time;
  }

  static function find($id) {
    $em = App::$entity_manager->getRepository(__CLASS__);
    return $em->find($id);
  }

  static function findAll() {
    $repo = App::$entity_manager->getRepository(__CLASS__);        
    return $repo->findAll();
  }

  static function create($amount, $notice = "", $category_ids, $time = null) {
    $trans = new static();
    $trans->setAmount($amount);
    $trans->setNotice($notice);
    if ($time !== null) {
      $trans->setTime($time);
    }

    if (is_array($category_ids)) {
      foreach ($category_ids as $id) {
        $cat = Category::find($id);
        if ($cat !== null) {
          $trans->assignToCategory($cat);
        }
      }
    }

    $em = App::$entity_manager;
    $em->persist($trans);
    $em->flush();
  }

  static function getTodayTransaction() {
    $table = __CLASS__;
    $em = App::$entity_manager;
    $repo = $em->getRepository($table);
    $query = $em->createQuery("select t from $table as t where t.time >= CURRENT_DATE()");
    return $query->getResult();
  }
}
