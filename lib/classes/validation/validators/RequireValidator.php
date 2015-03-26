<?php
namespace Markzero\Validation\Validator;

class RequireValidator extends AbstractValidator {
  private $field;
  const DEFAULT_MESSAGE = "This field is required";
  
  /**
   * @param string $email to be validated
   */
  public function __construct($field) {
    $this->field = $field;
    $this->set_message(self::DEFAULT_MESSAGE);
  }

  /**
   * return boolean
   */
  public function validate() {
    return !empty($this->field);
  }

}
