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

  /** _TODO: wrap validations in a function which is passed a validationmanager and don't have to call do_validate  **/

  /**
   * Register a validator, which will be 
   * executed (with other validators) by calling #do_validate
   * @param string $field_name
   * @param Markzero\Validation\Validator\AbstractValidator $validator
   * @param string  $error_message custom error message
   * @return $this return itself enables chaining method calls
   */
  public function validate($field_name, AbstractValidator $validator, $error_message = "") {
    if ($error_message)
      $validator->set_message($error_message);

    $this->validators[$field_name] = $validator;

    return $this;
  }

  /**
   * Iterate over all registered validators
   * and execute the validations
   * @throw Markzero\Validation\Exception\ValidationException
   */
  public function do_validate() {
    $errors = array(); // contains error messages

    foreach ($this->validators as $field_name => $validator) {
      if (!$validator->validate())
        $errors[$field_name] = $validator->get_message();
    }

    if (!empty($errors)) {
      throw new ValidationException($errors);
    }
  }

  /**
   * Clear all registered validators
   */
  public function clear() {
    $this->validators = array();
  }
}
