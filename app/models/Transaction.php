<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="transactions")
 **/
class Transaction extends AppModel {
  protected static $attr_reader = ['id'];
  protected static $attr_accessor = ['amount','notice', 'time', 'category'];

  /** @Id @Column(type="integer") @GeneratedValue **/
  protected $id;
  /** @Column(type="float") **/
  protected $amount;
  /** @Column(type="string") **/
  protected $notice;
  /** @Column(type="datetime") **/
  protected $time;

  /** @ManyToOne(targetEntity="Category", inversedBy="transactions") **/
  protected $category;

  public function getCategory() {
    return $this->category;
  }

  static function create($amount, $notice = "", $category_ids, $time = null) {
    $em = App::$entity_manager;
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

    $em->persist($trans);
    $em->flush();
  }

  static function getTodayTransaction() {
    $em = App::$entity_manager;
    $repo = $em->getRepository($table);
    $query = $em->createQuery("select t from $table as t where t.time >= CURRENT_DATE()");
    return $query->getResult();
  }
}
