<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * @Entity @Table(name="transactions")
 **/
class Transaction extends AppModel {
  protected static $attr_reader = array('id');
  protected static $attr_accessor = array('amount','notice', 'time', 'category', 'currency');

  /** @Id @Column(type="integer") @GeneratedValue **/
  protected $id;
  /** @Column(type="float") **/
  protected $amount;
  /** @Column(type="string", nullable=true) **/
  protected $notice;
  /** @Column(type="datetime") **/
  protected $time;
  /** @Column(type="string") **/
  protected $currency;
  /** @ManyToOne(targetEntity="Category", inversedBy="transactions") **/
  protected $category;

  protected static $CURRENCIES = array('USD', 'EUR', 'VND');

  /**
   * set up default values for entity's attributes
   */
  public function _default() {
    if (empty($this->time))
      $this->time = new \DateTime("now");
    if (empty($this->currency))
      $this->currency = 'USD';
  }

  /**
   * entity's attributes validation
   */
  protected function _validate() {
    $errors = array();

    $vm = self::createValidationManager();

    $vm->validate("amount", new FunctionValidator(function($amount) {
      return !empty($amount);
    }, array($this->amount)) ,"Amount must not be empty");

    $vm->validate("amount", new FunctionValidator(function($amount) {
      return is_numeric($amount); 
    }, array($this->amount)) ,"Amount must be a number");

    $vm->validate("currency", new FunctionValidator(function($currency) {
      return !empty($currency); 
    }, array($this->currency)) ,"A currency is required");

    $vm->validate("category", new FunctionValidator(function($category) {
      return !empty($category); 
    }, array($this->category)) ,"Transaction must be in a category");

    $vm->validate("time", new FunctionValidator(function($time) {
      return !empty($time); 
    }, array($this->time)) ,"The time is invalid - either empty, or wrong format dd/mm/yyyy");

    $vm->validate("time", new FunctionValidator(function($time) {
      return !empty($time); 
    }, array($this->time)) ,"The time is invalid - either empty, or wrong format dd/mm/yyyy");

    $vm->do_validate();
  }

  static function get_support_currencies() {
    return self::$CURRENCIES;
  }

  /**
   * create and save an Transaction entity
   * @var $params
   * @throw ValidationException
   **/
  static function create($params) {
    $obj = new static();
    $obj->amount   = $params->get('amount');
    $obj->notice   = $params->get('notice');
    $obj->currency = $params->get('currency');
    if ($time = $params->get('time')) {
      $obj->time   = \DateTime::createFromFormat("d/m/Y", $time);
    }
    $category_id = $params->get('category[id]', null, true);

    if ($category_id != null)
      $obj->category = Category::find($category_id);

    App::$em->persist($obj);
    App::$em->flush();

    return $obj;
  }

  /**
   * update and save an Transaction entity
   * @param $id | id of the transaction
   * @param $params | the new attributes of the transaction
   * @throw ValidationException
   */
  static function update($id, $params) {
    $obj = static::find($id);
    $obj->amount   = $params->get('amount');
    $obj->notice   = $params->get('notice');
    $obj->currency = $params->get('currency');
    $obj->time     = \DateTime::createFromFormat("d/m/Y", $params->get('time'));
    $category_id   = $params->get('category[id]', null, true);

    if ($category_id != null)
      $obj->category = Category::find($category_id);

    App::$em->persist($obj);
    App::$em->flush();

    return $obj;
  }

  static function delete($id) {
    App::$em->getConnection()->beginTransaction();
    try {
      $tran = static::find($id);

      App::$em->remove($tran); 
      App::$em->flush();

      App::$em->getConnection()->commit();
    } catch(Exception $e) {
      App::$em->getConnection()->rollback();
      throw $e;
    }
  }

  /**
   * @throw ValidationException
   */
  static function findByFilter($params) {
    $transactions = self::findAll();

    // App::$validation_manager->validate("date_from", 
    //   new DateTimeValidator($params->get('date_from'), "Invalid date"));

    // App::$validation_manager->validate("date_to", 
    //   new DateTimeValidator($params->get('date_to'), "Invalid date"));

    // App::$validation_manager->do_validate();

    $date_from = \DateTime::createFromFormat("d/m/Y", $params->get('date_from'));
    $date_to   = \DateTime::createFromFormat("d/m/Y", $params->get('date_to'));

    $query = App::$em
      ->createQuery('SELECT t FROM Transaction t WHERE t.time >= :date_from AND t.time <= :date_to')
      ->setParameters(array('date_from' => $date_from, 'date_to' => $date_to));

    return $query->getResult();
  }

}
