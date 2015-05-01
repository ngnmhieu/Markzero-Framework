<?php
use Markzero\Http\Request;
use \Mockery;

class RequestTest extends \PHPUnit_Framework_TestCase {

  public function test_isCrossDomain_noOrigin() {
    $_SERVER['HTTP_Origin'] = null;
    
    $request = new Request();

    $this->assertFalse($request->isCrossDomain());
  }

  public function test_isCrossDomain_HTTPMethodOptions() {
    $_SERVER['HTTP_Origin'] = 'www.example.org';
    
    $request = Mockery::mock('Markzero\Http\Request[getMethod,getSchemeAndHttpHost,getPort]');
    $request->shouldReceive(array(
      'getMethod' => 'OPTIONS'
    ));

    $this->assertTrue($request->isCrossDomain());
  }

  public function test_isCrossDomain_SameURL() {
    $_SERVER['HTTP_Origin'] = 'https://www.example.org';
    
    $request = Mockery::mock('Markzero\Http\Request[getMethod,getSchemeAndHttpHost,getPort]');
    $request->shouldReceive(array(
      'getMethod'            => 'GET',
      'getSchemeAndHttpHost' => 'https://www.example.org',
      'getPort'              => 443,
    ));

    $this->assertFalse($request->isCrossDomain());
  }

  public function test_isCrossDomain_DifferentScheme() {

    $_SERVER['HTTP_Origin'] = 'http://www.example.org';
    
    $request = Mockery::mock('Markzero\Http\Request[getMethod,getSchemeAndHttpHost,getPort]');
    $request->shouldReceive(array(
      'getMethod'            => 'GET',
      'getSchemeAndHttpHost' => 'https://www.example.org',
      'getPort'              => 443,
    ));

    $this->assertTrue($request->isCrossDomain());
  }

  public function test_isCrossDomain_DifferentHost() {

    $_SERVER['HTTP_Origin'] = 'http://www.apple.com';
    
    $request = Mockery::mock('Markzero\Http\Request[getMethod,getSchemeAndHttpHost,getPort]');
    $request->shouldReceive(array(
      'getMethod'            => 'GET',
      'getSchemeAndHttpHost' => 'http://www.example.org',
      'getPort'              => 80,
    ));

    $this->assertTrue($request->isCrossDomain());
  }

  public function test_isCrossDomain_DifferentPort() {

    $_SERVER['HTTP_Origin'] = 'http://www.example.org:333';
    
    $request = Mockery::mock('Markzero\Http\Request[getMethod,getSchemeAndHttpHost,getPort]');
    $request->shouldReceive(array(
      'getMethod'            => 'GET',
      'getSchemeAndHttpHost' => 'http://www.example.org',
      'getPort'              => 80,
    ));

    $this->assertTrue($request->isCrossDomain());
  }

  public function test_isCrossDomainAllowed_positive() {
    
    $_SERVER['HTTP_Origin'] = 'http://www.example.org';
    $request = new Request();
    $request->setTrustedHosts(array('example'));
  
    $this->assertTrue($request->isCrossDomainAllowed());
  }

  public function test_isCrossDomainAllowed_negative() {
    
    $_SERVER['HTTP_Origin'] = 'http://www.example.org';
    $request = new Request();
    $request->setTrustedHosts(array('www.linux.org'));

    $this->assertFalse($request->isCrossDomainAllowed());
  }
}
