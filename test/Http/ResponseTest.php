<?php

class ResponseTest extends \PHPUnit_Framework_TestCase {

  private $path = '/book/';

  public function getNewResponse() {

    $router = \Mockery::mock('Markzero\Http\Routing\Router', array(
      'getWebpaths' => array($this->path)
    ));
    $request = \Mockery::mock('Markzero\Http\Request');

    return new Markzero\Http\Response($request, $router);
  }

  public function test_redirect_setCorrectRedirectUrl() {
    $response = $this->getNewResponse();
    $response->redirect('BookController', 'index');

    $this->assertEquals($response->headers->get('Location'), $this->path);
  }  

  public function test_redirect_setCorrectStatusCode() {
    $response = $this->getNewResponse();

    $response->redirect('BookController', 'index');

    $this->assertEquals($response->getStatusCode(), Markzero\Http\Response::HTTP_FOUND);
  }
}
