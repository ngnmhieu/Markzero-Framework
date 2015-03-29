<?php
use Markzero\Validation\Validator\EmailValidator; 

class EmailValidatorTest extends \PHPUnit_Framework_TestCase {
  public function test_validate_success() {
    $emails = array(
      'somEPEople@gmail.com',
      '%_+.abc@gmail.cc',
      '%baby@gmail.cc',
    );

    foreach ($emails as $email) {
      $validator = new EmailValidator($email);
      // should all succeed
      $this->assertTrue($validator->validate());
    }
  }

  public function test_validate_failure() {
    $emails = array(
      '@gmail.com',
      'anybody@gmail.c',
      'anybody@gmail.commmmm',
      'abc@.com',
      'abc@gm@il.com',
      'abc@gm#il.com',
      '_%^abc@gmail.com'
    );

    foreach ($emails as $email) {
      $validator = new EmailValidator($email);
      // should all fail
      $this->assertFalse($validator->validate());
    }
  }
}
