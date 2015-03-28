<?php
namespace Markzero\Validation\Exception;

/**
 * Thrown when a validation error occurs
 */
class ValidationException extends \Exception {
  private $errors;

  public function __construct(array $errors = array(), $message = null, $code = 0) {
    parent::__construct($message, $code);

    $this->errors = $errors;
  }

  public function getErrors() {
    return $this->errors;
  }
}
