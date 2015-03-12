<?php

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

  public function set_message($message) {
    $this->message = $message;
  }

  public function get_message() {
    return $this->message;
  }

}
