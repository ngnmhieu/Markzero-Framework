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

  public function test_getParams_mixPostAndGetParamsPOST() {
    $_GET = array(
      'getparam1' => 'getvalue1',
      'getparam2' => 'getvalue2',
      'commonkey' => 'getvalue3'
    );
    $_POST = array(
      'postparam1' => 'postvalue1',
      'postparam2' => 'postvalue2',
      'commonkey'  => 'postvalue3'
    );
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $request = new Request() ;  
    $parameter_bag = $request->getParams();

    $params = $parameter_bag->all();
    $expected_array = array_merge($_GET,$_POST);

    $this->assertEmpty(array_diff_assoc($expected_array, $params), 'Two array are not the same');
  }

  public function test_getParams_mixPostAndGetParamsGET() {
    $_GET = array(
      'getparam1' => 'getvalue1',
      'getparam2' => 'getvalue2',
      'commonkey' => 'getvalue3'
    );
    $_POST = array(
      'postparam1' => 'postvalue1',
      'postparam2' => 'postvalue2',
      'commonkey'  => 'postvalue3'
    );
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $request = new Request() ;  
    $parameter_bag = $request->getParams();

    $params = $parameter_bag->all();
    $expected_array = array_merge($_POST, $_GET);

    $this->assertEmpty(array_diff_assoc($expected_array, $params), 'Two array are not the same');
  }

}
