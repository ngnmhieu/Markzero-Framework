<?php
use Markzero\Validation\Validator\FunctionValidator; 

class FunctionValidatorTest extends \PHPUnit_Framework_TestCase {
  public function test_validate_success() {
    $validator = new FunctionValidator(function() { return true; });
    
    $this->assertTrue($validator->validate());
  }

  public function test_validate_failure() {
    $validator1 = new FunctionValidator(function() { return false; });
    $validator2 = new FunctionValidator(function() {  });

    $this->assertFalse($validator1->validate());
    $this->assertFalse($validator2->validate());
  }
}
