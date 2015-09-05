<?php
namespace Markzero\Validation;

use Markzero\Validation\Exception\ValidationException;
use Markzero\Validation\Validator\AbstractValidator;

/**
 * Manage registration and performing validation 
 * using registered validators
 */
class ValidationManager 
{
  /**
   * @var array Contain registered validators
   */
  private $validators;
  
  public function __construct() 
  {
    $this->validators = array();
  }

  /**
   * Mostly used for testing
   * @return array Registered Validators
   */
  public function getValidators() 
  {
    return $this->validators;
  }

  /**
   * _TODO: consider make it static
   * Execute a validation transaction, and clear all validators at the end
   * @param callable
   * @param bool 
   * @throw Markzero\Validation\Exception\ValidationException 
   */
  public static function validate(callable $closure, $strict = false) 
  {
    $vm = new static();
    
    // validators are be registered in the closure
    $closure($vm);

    // run the validators
    $vm->doValidate($strict);

    // if no Exception was thrown, clear all validators
    $vm->clear();
  }

  /**
   * Register a validator, which will be 
   * executed (with other validators) by calling #do_validate
   * @param string $field_name
   * @param Markzero\Validation\Validator\AbstractValidator $validator
   * @param string  $error_message custom error message
   * @return $this return itself enables chaining method calls
   */
  public function register($field_name, AbstractValidator $validator, $error_message = "") 
  {
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
   * @param boolean Strict Mode 
   *                if set to true, ValidationException is thrown immediately after a validation error
   *                otherwise ValidationException is only thrown after all validators are run.
   * @throw Markzero\Validation\Exception\ValidationException
   */
  public function doValidate($strict = false) 
  {
    $errors = array(); // contains error messages

    foreach ($this->validators as $field_name => $validators) {

      foreach ($validators as $validator) {
        if (!$validator->validate()) {
          $errors[$field_name] = $validator->getMessage();

          if ($strict) {
            throw new ValidationException($errors);
          } else {
            break;
          }
        }
      }

    }

    if (!empty($errors)) {
      throw new ValidationException($errors);
    }
  }

  /**
   * Clear all registered validators
   */
  public function clear()
  {
    $this->validators = array();
  }
}
