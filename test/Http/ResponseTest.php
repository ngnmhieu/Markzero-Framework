<?php
use Markzero\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase {

  public function getNewResponse()
  {

    $router = \Mockery::mock('Markzero\Http\Routing\Router');
    $request = \Mockery::mock('Markzero\Http\Request');

    return new Response($request, $router);
  }

  public function getResponseForRedirection()
  {
    $path = '/book/';

    $router = \Mockery::mock('Markzero\Http\Routing\Router', array(
      'getWebpaths' => array($path)
    ));

    $request = \Mockery::mock('Markzero\Http\Request');

    return new Response($request, $router);
  }

  public function test_redirect_setCorrectRedirectUrl()
  {
    $response = $this->getResponseForRedirection();

    $response->redirect('BookController', 'index');

    $this->assertEquals('/book/', $response->headers->get('Location'));
  }

  public function test_redirect_setCorrectStatusCode()
  {
    $response = $this->getResponseForRedirection();

    $response->redirect('BookController', 'index', [], [], Response::HTTP_TEMPORARY_REDIRECT);

    $this->assertEquals(Response::HTTP_TEMPORARY_REDIRECT, $response->getStatusCode());
  }

  public function test_redirect_setDefaultStatusCode()
  {
    $response = $this->getResponseForRedirection();

    $response->redirect('BookController', 'index');

    $this->assertEquals(Response::DEFAULT_REDIRECT_STATUS_CODE, $response->getStatusCode());
  }

  public function test_redirect_noChangeToAlreadySetRedirectionStatus() 
  {
    $response = $this->getResponseForRedirection();

    $response->setStatusCode(Response::HTTP_MOVED_PERMANENTLY);

    $response->redirect('BookController', 'index');

    $this->assertNotEquals( Response::HTTP_FOUND,$response->getStatusCode());
  }

  public function test_respond_noResponder()
  {
    $response = $this->getNewResponse(); 

    $response->respond();

    $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
  }

  public function test_respond_mediatypeNotSupported()
  {
    $router = \Mockery::mock('Markzero\Http\Routing\Router');
    $request = \Mockery::mock('Markzero\Http\Request', array(
      'getAcceptableContentTypes' => array('application/xml'),
      'getFormat' => 'xml'
    ));
    $response = new Response($request, $router);
    $response->respondTo('html', function(){});

    $response->respond();
    $this->assertEquals(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $response->getStatusCode());
  }

}
