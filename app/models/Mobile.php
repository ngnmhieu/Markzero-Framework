<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="mobiles")
 **/
class Mobile extends AppModel {
  protected static $attr_reader = array('id');
  protected static $attr_accessor = array('name','description', 'price', 'picture');

  /** @Id @Column(type="integer") @GeneratedValue **/
  protected $id;
  /** @Column(type="string") **/
  protected $name;
  /** @Column(type="float") **/
  protected $price;
  /** @Column(type="string", nullable=true) **/
  protected $description;
  /** @Column(type="string") **/
  protected $picture;

  /**
   * set up default values for entity's attributes
   */
  protected function setup_default() {
  }
  
  /**
   * entity's attributes validation
   */
  protected function validate() {
    return empty($this->errors); 
  }

  static function notify($id, $params) {
    $params->permit(array('name', 'email', 'phone'));
    $company = "BananaCorp";
    $name = $params->val('name');
    $email = $params->val('email');
    $phone = $params->val('phone');
    $body = <<<BODY
  Name: $name\n
  Email: $email\n
  Phone: $phone
BODY;
    

    $status = true;
    if (!self::sendNotificationTo(array('ngnmhieu@gmail.com', 'mmujadidi@outlook.de'), "Interested Customer", $body)) {
      $status = false;
    }

    $body_customer = "Dear $name, \nWe will contact you, as soon as is available!\n\nYour Sincerely,\n$company CEO";
    if (!self::sendNotificationTo(array($email), "Thank you $name!", $body_customer)) {
      $status = false;
    }

    return $status;
  }

  private static function sendNotificationTo(array $emails, $subject, $body) {
    $company = "BananaCorp";
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host       = "my.inbox.com";      // SMTP server example, use smtp.live.com for Hotmail
    $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->Port       = 587;                   // SMTP port 465 or 587
    $mail->Username   = "bananaphone@inbox.com";  // SMTP account username example
    $mail->Password   = "bananaphone8A";            // SMTP account password example
    $mail->setFrom('bananaphone@inbox.com', $company);
    foreach ($emails as $email) {
      $mail->addAddress($email);
    }
    $mail->Subject = $subject;
    $mail->Body = $body;
    return $mail->send() ? true : false;
  }


  /**
   * create and save an Transaction entity
   * @var $params
   **/
  static function create($params) {
    // $params->permit(array('amount', 'notice', 'category_id', 'time'));
    // $obj = new static();
    // $obj->amount = $params->val('amount');
    // $obj->notice = $params->val('notice');
    // $obj->time   = \DateTime::createFromFormat("d/m/Y", $params->val('time'));
    // $category_id = $params->val('category_id');

    // if ($category_id != null)
    //   $obj->category = Category::find($category_id);

    // try {
    //   App::$entity_manager->persist($obj);
    //   App::$entity_manager->flush();
    // } catch(ValidationException $e) {
    // }

    // return $obj;
  }

  /**
   * update and save an Mobile entity
   * @param $id | id of the mobile
   * @param $params | the new attributes of the mobile
   */
  static function update($id, $params) {
    // $params->permit(array('amount', 'notice', 'time', 'category_id'));
    // $obj = static::find($id);
    // $obj->amount = $params->val('amount');
    // $obj->notice = $params->val('notice');
    // $obj->time   = \DateTime::createFromFormat("d/m/Y", $params->val('time'));
    // $category_id = $params->val('category_id');

    // if ($category_id != null)
    //   $obj->category = Category::find($category_id);

    // try {
    //   App::$entity_manager->persist($obj);
    //   App::$entity_manager->flush();
    // } catch(ValidationException $e) {
    // }

    // return $obj;
  }

  /**
   * delete entity
   */
  static function delete($id) {
    $mobile = static::find($id);
    App::$entity_manager->remove($mobile); 
    App::$entity_manager->flush();
    return true;
  }
}
