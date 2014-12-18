<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="transactions")
 **/
class Transaction extends AppModel {
  protected static $attr_reader = array('id');
  protected static $attr_accessor = array('amount','notice', 'time', 'category');

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

  /**
   * set up default values for entity's attributes
   */
  protected function setup_default() {
    if (empty($this->time))
      $this->time = new \DateTime("now");
  }
  
  /**
   * entity's attributes validation
   */
  protected function validate() {
    if (empty($this->amount)) {
      $this->errors['amount'] = "Amount must not be empty";
    } else if (!is_numeric($this->amount)) {
      $this->errors['amount'] = "Amount must be number";
    }
    
    return empty($this->errors); 
  }



  /**
   * create and save an Transaction entity
   * @var $params
   **/
  static function create($params) {
    $obj = new static();
    $obj->amount = $params['amount'];
    $obj->notice = $params['notice'];
    $obj->time   = \DateTime::createFromFormat("d/m/Y", $params['time']);
    $category_id = $params['category_id'];

    if ($category_id != null)
      $obj->category = Category::find($category_id);

    try {
      App::$entity_manager->persist($obj);
      App::$entity_manager->flush();
    } catch(ValidationException $e) {
    }

    return $obj;
  }

  /**
   * update and save an Transaction entity
   * @param $id | id of the transaction
   * @param $params | the new attributes of the transaction
   */
  static function update($id, $params) {
    $obj = static::find($id);
    $obj->amount = $params['amount'];
    $obj->notice = $params['notice'];
    $obj->time   = \DateTime::createFromFormat("d/m/Y", $params['time']);
    $category_id = $params['category_id'];

    if ($category_id != null)
      $obj->category = Category::find($category_id);

    try {
      App::$entity_manager->persist($obj);
      App::$entity_manager->flush();
    } catch(ValidationException $e) {
    }

    return $obj;
  }

  static function delete($id) {
    $tran = static::find($id);
    App::$entity_manager->remove($tran); 
    App::$entity_manager->flush();
    return true;
  }
}
