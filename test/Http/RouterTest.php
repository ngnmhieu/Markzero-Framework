<?php
use Markzero\Http\Router;
use \Mockery;

class RouterTest extends \PHPUnit_Framework_TestCase {

  public function getRouter() {
    $response = Mockery::mock('Markzero\Http\Response');
    $request = Mockery::mock('Markzero\Http\Request');

    return new Router($request, $response); 
  }

  public function test_map_addNewRoute() {

    $router = $this->getRouter();
    $routes = array_flatten($router->getRoutes());

    $this->assertCount(0, $routes);

    $router->map('get','/book/([0-9]+)/', 'book','show');

    $routes = array_flatten($router->getRoutes());
    $this->assertCount(1, $routes);
  }

  public function test_map_emptyController() {
    $this->setExpectedException('\InvalidArgumentException');

    $router = $this->getRouter();

    $router->map('get', '/', '', 'action');
  }

  public function test_map_invalidController() {
    $this->setExpectedException('\InvalidArgumentException');

    $router = $this->getRouter();

    $router->map('get', '/', '#abc$', 'action');
  }

  public function test_map_invalidAction() {
    $this->setExpectedException('\InvalidArgumentException');

    $router = $this->getRouter();

    $router->map('get', '/', 'BookController', 'act io n');
  }

  // _TODO: test dispatch
  // _TODO: test getWebpaths
}

