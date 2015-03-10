<?php
use Doctrine\Common\Collections\ArrayCollection;

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

    $vm->validate("currency", new FunctionValidator(function($currency) {
      $currs = Currency::get_supported_currencies();
      return preg_match('/^('.implode('|',$currs).')$/',$currency); 
    }, array($this->currency)) ,"This currency is not supported at the moment");

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

    $vm = self::createValidationManager();

    $filtertype = $params->get('type');

    $vm->validate("type", new FunctionValidator(function($type) {
      return !empty($type);
    }, array($filtertype)), "Require a filter type");

    $vm->do_validate();
    $vm->clear();
    
    if ($filtertype === 'period_from_to') {

      $vm->validate("date_from", new FunctionValidator(function($date) {
        return preg_match('~^\d{1,2}/\d{1,2}/\d{4}$~', $date);
      }, array($params->get('date_from'))), "Invalid date");

      $vm->validate("date_to", new FunctionValidator(function($date) {
        return preg_match('~^\d{1,2}/\d{1,2}/\d{4}$~', $date);
      }, array($params->get('date_to'))), "Invalid date");

      $vm->do_validate();

      $date_from = \DateTime::createFromFormat("d/m/Y", $params->get('date_from'));
      $date_to   = \DateTime::createFromFormat("d/m/Y", $params->get('date_to'));

      $query = App::$em->createQuery('SELECT t FROM Transaction t WHERE t.time >= :date_from AND t.time <= :date_to');
      $query->setParameters(array('date_from' => $date_from, 'date_to' => $date_to));

    } else if ($filtertype === 'period_lastdays') {
      $lastdays = $params->get('lastdays');
      $vm->validate("lastdays", new FunctionValidator(function($lastdays) {
        return is_numeric($lastdays) && ((int) $lastdays) > 0;
      }, array($lastdays)), "Days period must be a number greater than 0");

      $vm->do_validate();

      $query = App::$em->createQuery('SELECT t FROM TRANSACTION t WHERE t.time >= :past_day');
      $today = new \DateTime("now");
      $query->setParameter('past_day',$today->sub(new DateInterval('P'.$lastdays.'D')));
    }

    return $query->getResult();
  }

}
