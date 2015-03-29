<?php
use Markzero\Validation\Validator\AbstractValidator;

class TestValidator extends AbstractValidator {
  public function validate() { }
}

class AbstractValidatorTest extends \PHPUnit_Framework_TestCase {
  public function test_set_and_getMessage() {
    $validator = new TestValidator();
    $msg = 'An error message';

    $validator->setMessage($msg);

    $this->assertEquals($msg, $validator->getMessage());
  }
}
