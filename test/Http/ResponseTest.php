<?php
use Mockery\Http\Response;

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

    $this->assertEquals($response->headers->get('Location'), $path);
  }

  public function test_redirect_setCorrectStatusCode() {
    $router = \Mockery::mock('Markzero\Http\Routing\Router', array(
      'getWebpaths' => array('/book/')
    ));
    $request = \Mockery::mock('Markzero\Http\Request');
    $response = new Markzero\Http\Response($request, $router);

    $response->redirect('BookController', 'index');

    $this->assertEquals($response->getStatusCode(), Markzero\Http\Response::HTTP_FOUND);
  }

  public function test_respond_noResponder() {
    $response = $this->getNewResponse(); 

    $response->respond();

    $this->assertEquals($response->getStatusCode(), Response::HTTP_NOT_FOUND);
  }

  public function test_respond_mediatypeNotSupported() {

    $router = \Mockery::mock('Markzero\Http\Routing\Router');
    $request = \Mockery::mock('Markzero\Http\Request', array(
      'getAcceptableContentTypes' => array()
    ));
    $response = new Response($request, $router);

    $response->respond();
    $this->assertEquals($response->getStatusCode(), Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
  }

}
