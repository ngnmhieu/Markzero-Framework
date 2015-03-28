<?php
namespace Markzero\Validation\Validator;

class EmailValidator extends AbstractValidator {
  private $email;
  const DEFAULT_MESSAGE = "Invalid Email Address";
  
  /**
   * @param string $email to be validated
   */
  public function __construct($email) {
    $this->email = $email;
    $this->set_message(self::DEFAULT_MESSAGE);
  }

  /**
   * return boolean
   */
  public function validate() {
    return $this->email && preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-zA-Z]{2,4}$/", $this->email);
  }

}
