<?php
use Markzero\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase {

  public function getNewResponse() {

    $router = \Mockery::mock('Markzero\Http\Routing\Router');
    $request = \Mockery::mock('Markzero\Http\Request');

    return new Response($request, $router);
  }

  public function test_redirect_setCorrectRedirectUrl() {

    $path = '/book/';
    $router = \Mockery::mock('Markzero\Http\Routing\Router', array(
      'getWebpaths' => array($path)
    ));
    $request = \Mockery::mock('Markzero\Http\Request');
    $response = new Markzero\Http\Response($request, $router);

    $response->redirect('BookController', 'index');

    $this->assertEquals($path, $response->headers->get('Location'));
  }

  public function test_redirect_setCorrectStatusCode() {

    $router = \Mockery::mock('Markzero\Http\Routing\Router', array(
      'getWebpaths' => array('/book/')
    ));
    $request = \Mockery::mock('Markzero\Http\Request');
    $response = new Markzero\Http\Response($request, $router);

    $response->redirect('BookController', 'index');

    $this->assertEquals(Markzero\Http\Response::HTTP_FOUND, $response->getStatusCode());
  }

  public function test_respond_noResponder() {

    $response = $this->getNewResponse(); 

    $response->respond();

    $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
  }

  public function test_respond_mediatypeNotSupported() {

    $router = \Mockery::mock('Markzero\Http\Routing\Router');
    $request = \Mockery::mock('Markzero\Http\Request', array(
      'getAcceptableContentTypes' => array('application/xml'),
      'getFormat' => 'xml'
    ));
    $response = new Response($request, $router);
    $response->respond_to('html', function(){});

    $response->respond();
    $this->assertEquals(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $response->getStatusCode());
  }

}
