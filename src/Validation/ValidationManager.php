<?php
namespace Markzero\Validation;

use Markzero\Validation\Exception\ValidationException;
use Markzero\Validation\Validator\AbstractValidator;

/**
 * Manage registration and performing validation 
 * using registered validators
 */
class ValidationManager {
  /**
   * array | Contain registered validators
   */
  private $validators;
  
  public function __construct() {
    $this->validators = array();
  }

  /**
   * Mostly used for testing
   * @return array Registered Validators
   */
  public function getValidators() {
    return $this->validators;
  }

  /**
   * Execute a validation transaction, and clear all validators at the end
   * @param callable
   * @throw Markzero\Validation\Exception\ValidationException 
   */
  public function validate(callable $closure) {

    // validators are be registered in the closure
    $closure($this);

    // run the validators
    $this->doValidate();

    // if no Exception was thrown, clear all validators
    $this->clear();
  }

  /**
   * Register a validator, which will be 
   * executed (with other validators) by calling #do_validate
   * @param string $field_name
   * @param Markzero\Validation\Validator\AbstractValidator $validator
   * @param string  $error_message custom error message
   * @return $this return itself enables chaining method calls
   */
  public function register($field_name, AbstractValidator $validator, $error_message = "") {
    if ($error_message)
      $validator->setMessage($error_message);

    if (!array_key_exists($field_name, $this->validators))
      $this->validators[$field_name] = array();

    $this->validators[$field_name][] = $validator;

    return $this;
  }

  /**
   * Iterate over all registered validators
   * and execute the validations
   * @throw Markzero\Validation\Exception\ValidationException
   */
  public function doValidate() {
    $errors = array(); // contains error messages

    foreach ($this->validators as $field_name => $validators) {

      foreach ($validators as $validator) {

        if (!$validator->validate())
          $errors[$field_name] = $validator->getMessage();

        if (!empty($errors)) {
          throw new ValidationException($errors);
        }

      }

    }

  }

  /**
   * Clear all registered validators
   */
  public function clear() {
    $this->validators = array();
  }
}
