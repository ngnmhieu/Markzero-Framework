<?php
namespace Markzero\Validation\Validator;

abstract class AbstractValidator {
  /**
   * Error message
   */
  private $message;

  /**
   * Concrete Validator override this method
   * to implement actual validation
   */
  abstract function validate();

  public function setMessage($message) {
    $this->message = $message;
  }

  public function getMessage() {
    return $this->message;
  }

}
