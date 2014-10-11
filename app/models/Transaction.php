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
  /** @Column(type="string", nullable=true) **/
  protected $notice;
  /** @Column(type="datetime") **/
  protected $time;

  /** @ManyToOne(targetEntity="Category", inversedBy="transactions") **/
  protected $category;

  function __construct() {
    $this->time = new \DateTime("now");
  }
  
  public function validate() {
    if (empty($this->amount)) {
      $this->errors['amount'] = "Amount must not be empty";
    } else if (is_numeric($this->amount)) {
      $this->errors['amount'] = "Amount must be number.";
    }

    
    return empty($this->errors); 
  }

  /**
   * @var $params
   **/
  static function create($params) {
    $obj = new static();
    $obj->amount = $params->val('amount');
    $obj->notice = $params->val('notice');
    $obj->time = $params->val('time');
    // $obj->category = $params->val('category');

    try {
      App::$entity_manager->persist($obj);
      App::$entity_manager->flush();
    } catch(ValidationException $e) {

    }

    return $obj;
  }

}
