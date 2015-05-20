<?php
namespace Markzero\Validation\Exception;

/**
 * Thrown when a validation error occurs
 */
class ValidationException extends \Exception {
  private $errors;

  public function __construct(array $errors = array(), $message = null, $code = 0) {
    parent::__construct($message, $code);

    $this->errors = $this->normalizeArray($errors);
  }

  /**
   * Convert 1 dimensional array into multidimensional array
   *    [
   *     'user[name]' => "a message",
   *     'phones[10][num]' => "a message"
   *    ]
   * into 
   *    [
   *     'user' => ['name' => 'a message'],
   *     'phones' => [10 => ['num' => 'a message']
   *    ]
   * @param array
   * @return array 
   */
  private function normalizeArray($array) {
    $result_array = array();

    // convert errors array into url query string
    $strings = array(); 
    foreach ($array as $k => $v) {
      $strings[] = "$k=$v";
    }

    // convert back to multidimensional array
    parse_str(implode('&', $strings), $result_array);

    return $result_array;
  }

  public function getErrors() {
    return $this->errors;
  }
}
