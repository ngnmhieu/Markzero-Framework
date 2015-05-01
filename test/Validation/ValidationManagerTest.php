<?php
use Markzero\Validation\Validator;
use Markzero\Validation\ValidationManager;
use \Mockery as m;

class ValidationManagerTest extends \PHPUnit_Framework_TestCase {

  public function getDoubleValidators() {
    $create_mock = function() { 
      return m::mock('Markzero\Validation\Validator\AbstractValidator', array(
        'validate'   => null,
        'setMessage' => null,
        'getMessage' => null
      ));
    };

    return array(
      $create_mock(),$create_mock(),$create_mock(),
      $create_mock(),$create_mock(),$create_mock()
    );
  }
  
  public function getNegativeValidator($id) {
    return m::mock('Markzero\Validation\Validator\AbstractValidator', array(
      'setMessage' => null,
      'getMessage' => 'error'.$id,
      'validate'   => false
    ));
  }
  
  public function test_register_Validators_Diff_Attributes() {
    $vm = new ValidationManager();

    $this->assertEquals(0, count($vm->getValidators()));

    $validators = $this->getDoubleValidators();
    for ($i = 0; $i < count($validators); $i++) {
      $vm->register("attribute_$i", $validators[$i]);
    }

    $num_registered_validators = array_reduce(
      $vm->getValidators(), function($total, $validators) {
        return $total + count($validators);
    }, 0);
    
    // should registered all validators
    $this->assertEquals(count($validators), $num_registered_validators);
  }

  public function test_register_Validators_Same_Attribute() {
    $vm = new ValidationManager();
    $validators = $this->getDoubleValidators(); 
    // no validators are registered yet
    $this->assertEquals(0, count($vm->getValidators()));

    foreach ($validators as $validator) {
      $vm->register("attribute", $validator);
    }

    $num_registered_validators = array_reduce(
      $vm->getValidators(), function($total, $validators) {
        return $total + count($validators);
    }, 0);

    // should registered all validators
    $this->assertEquals(count($validators), $num_registered_validators);
  }

  public function test_doValidate_Throw_ValidationException() {
    $this->setExpectedException('Markzero\Validation\Exception\ValidationException');

    $vm = new ValidationManager();

    $validator = m::mock('Markzero\Validation\Validator\AbstractValidator', array(
      'setMessage' => null,
      'getMessage' => 'error',
      'validate'   => false
    ));

    $vm->register('attribute', $validator);

    $vm->doValidate();
  }

  public function test_doValidate_strict() {

    $vm = new ValidationManager();

    $validator1 = $this->getNegativeValidator(1);
    $validator2 = $this->getNegativeValidator(2);
    $vm->register('attribute1', $validator1);
    $vm->register('attribute2', $validator2);

    try {
      $strict = true;
      $vm->doValidate($strict);
    } catch(Markzero\Validation\Exception\ValidationException $e) {
      $errors = $e->getErrors();
      $this->assertArrayNotHasKey('attribute2', $errors);
    }
  }

  public function test_doValidate_noStrict() {

    $vm = new ValidationManager();

    $validator1 = $this->getNegativeValidator(1);
    $validator2 = $this->getNegativeValidator(2);
    $vm->register('attribute1', $validator1);
    $vm->register('attribute2', $validator2);

    try {
      $strict = false;
      $vm->doValidate($strict);
    } catch(Markzero\Validation\Exception\ValidationException $e) {
      $errors = $e->getErrors();
      $this->assertArrayHasKey('attribute2', $errors);
    }
  }
}
