<?php
use Markzero\Validation\Validator;
use Markzero\Validation\ValidationManager;

class ValidationManagerTest extends PHPUnit_Framework_TestCase {

  public function getSampleValidators() {
    return array(
      new Validator\FunctionValidator(function(){}),
      new Validator\EmailValidator('email'),
      new Validator\FunctionValidator(function(){}),
      new Validator\RequireValidator('someattribute'),
      new Validator\EmailValidator('email'),
      new Validator\RequireValidator('someattribute')
    );
  }
  
  public function test_validate_Register_Validators_Same_Attribute() {
    $vm = new ValidationManager();

    $this->assertEquals(0, count($vm->getValidators()));

    $validators = $this->getSampleValidators();
    for ($i = 0; $i < count($validators); $i++) {
      $vm->validate("attribute_$i", $validators[$i]);
    }
    
    // should registered all validators
    $this->assertEquals(count($validators), count($vm->getValidators()));
  }

  public function test_validate_Register_Validators_Same_Attribute() {
    $vm = new ValidationManager();

    // no validators are registered yet
    $this->assertEquals(0, count($vm->getValidators()));

    $validators = $this->getSampleValidators();
    foreach ($validators as $validator) {
      $vm->validate("attribute", $validator);
    }

    // should registered all validators
    $num_registered_validators = array_reduce(
      $vm->getValidators(), function($total, $validators) {
        return $total + count($validators);
    }, 0);

    $this->assertEquals(count($validators), $num_registered_validators);
  }

  // public function test_validate_Set_Error_Message() {

  // }

  /**
   * @expectedException Markzero\Validation\Exception\ValidationException
   */
  public function test_doValidate_Throw_ValidationException() {
    $vm = new ValidationManager();

    $vm->validate('attribute', new Validator\FunctionValidator(function(){
      return false;
    }));
    
    $vm->doValidate();
  }

}
