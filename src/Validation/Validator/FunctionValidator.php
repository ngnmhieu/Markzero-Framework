<?php
namespace Markzero\Validation\Validator;

class FunctionValidator extends AbstractValidator {
  private $validate_func; // Function that performs validation
  private $validate_args; // Arguments passed to validation function
  const DEFAULT_MESSAGE = "Invalid Data";
  
  /**
   * @param callable $func
   * @param array    $args
   */
  public function __construct(callable $func, array $args = array()) {
    $this->validate_func = $func;
    $this->validate_args = $args;
    $this->setMessage(self::DEFAULT_MESSAGE);
  }

  /**
   * return boolean
   */
  public function validate() {
    if (is_callable($this->validate_func)) {

      $result = call_user_func_array($this->validate_func, $this->validate_args);;
      return !!$result;

    } else {

      $this->setMessage("Validation function is not callable");
      return false;

    }
  }

}
