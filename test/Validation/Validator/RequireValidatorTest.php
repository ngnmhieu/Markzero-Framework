<?php
use Markzero\Validation\Validator\RequireValidator; 

class RequireValidatorTest extends \PHPUnit_Framework_TestCase {
  public function test_validate_success() {
    $validators = array(
      new RequireValidator('OK'),
      new RequireValidator('0'),
      new RequireValidator('0.0'),
      new RequireValidator(0),
      new RequireValidator(0.0),
      new RequireValidator(new \stdClass()),
    );

    foreach ($validators as $validator) {
      $this->assertTrue($validator->validate());
    }
  }

  public function test_validate_failure() {
    $validators = array(
      new RequireValidator(false),
      new RequireValidator(''),
      new RequireValidator(null),
    );

    foreach ($validators as $validator) {
      $this->assertFalse($validator->validate());
    }
  }
}
