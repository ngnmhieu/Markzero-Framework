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
    $this->setMessage(self::DEFAULT_MESSAGE);
  }

  /**
   * return boolean
   */
  public function validate() {
    return is_int($this->field) 
      || is_float($this->field) 
      || (is_string($this->field) && strlen($this->field) !== 0)
      || !!$this->field;
  }

}
