<?php

class FunctionValidator extends AbstractValidator {
  private $validate_func; // Function that performs validation
  private $validate_args; // Arguments passed to validation function
  const DEFAULT_MESSAGE = "Invalid Data";
  
  /**
   * @param callable $func
   * @param array    $args
   */
  public function __construct(callable $func, array $args) {
    $this->validate_func = $func;
    $this->validate_args = $args;
    $this->set_message(self::DEFAULT_MESSAGE);
  }

  /**
   * return boolean
   */
  public function validate() {
    if (is_callable($this->validate_func)) {
      return call_user_func_array($this->validate_func, $this->validate_args);
    } else {
      $this->set_message("Validation function is not callable");
      return false;
    }
  }

}
