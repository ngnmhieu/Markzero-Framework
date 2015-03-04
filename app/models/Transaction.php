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
    $this->errors = array();

    if (empty($this->amount)) {
      $this->errors['amount'] = "Amount must not be empty";
    } else if (!is_numeric($this->amount)) {
      $this->errors['amount'] = "Amount must be number";
    } 

    if (empty($this->currency)) {
      $this->errors['currency'] = "A currency is required";
    }

    if (empty($this->category)) {
      $this->errors['category'] = "Transaction must be in a category";
    }

    if (empty($this->time)) {
      $this->errors['time'] = "The time is invalid - either empty, or wrong format dd/mm/yyyy";
    }

    if (!empty($this->errors)) {
      throw new ValidationException($this->errors);
    }
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
    $tran = static::find($id);
    App::$em->remove($tran); 
    App::$em->flush();
    return true;
  }

  static function findByFilter($params) {
    $transactions = self::findAll();

    $date_from = \DateTime::createFromFormat("d/m/Y", $params->get('date_from'));
    $date_to   = \DateTime::createFromFormat("d/m/Y", $params->get('date_to'));

    $query = App::$em
      ->createQuery('SELECT t FROM Transaction t WHERE t.time >= :date_from AND t.time <= :date_to')
      ->setParameters(array('date_from' => $date_from, 'date_to' => $date_to));

    return $query->getResult();
  }

}
