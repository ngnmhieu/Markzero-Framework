<?php
use Markzero\Http\Routing\Router;
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

    $router->map('get','/book/([0-9]+)/', 'BookController','show');

    $routes = array_flatten($router->getRoutes());
    $this->assertCount(1, $routes);
  }

  public function test_getNamedWebpath() {

    $router = $this->getRouter();

    $router->map('post','/login/', 'SessionController','create', 'login');

    $this->assertEquals('/login/',$router->getNamedWebpath('login'));
  }

  public function test_getNamedWebpathWithArgs() {

    $router = $this->getRouter();

    $router->map('post','/user/([0-9]+)/book/favorite', 'BookController','favorite', 'favorite_book');

    $this->assertEquals('/user/12/book/favorite',$router->getNamedWebpath('favorite_book', array(12)));
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

  public function test_getWebpaths_withoutArgs() {
    $router = $this->getRouter();

    $router->map('get', '/book/index', 'BookController', 'index');
    $webpaths = $router->getWebpaths('BookController', 'index');  

    $this->assertContains('/book/index', $webpaths);
  }

  public function test_getWebpaths_withArgs() {
    $router = $this->getRouter();

    $router->map('get', '/user/([0-9]+)/book/show/([0-9]+)', 'BookController', 'show');
    $webpaths = $router->getWebpaths('BookController', 'show', array(9,12));  

    $this->assertContains('/user/9/book/show/12', $webpaths);
  }

  public function test_getWebpaths_multiplePaths() {
    $router = $this->getRouter();

    $router->map('get', '/user/([0-9]+)/book/show/([0-9]+)', 'BookController', 'show');
    $router->map('get', '/user/([0-9]+)/book/display/([0-9]+)', 'BookController', 'show');
    $webpaths = $router->getWebpaths('BookController', 'show', array(9,12));  

    $this->assertContains('/user/9/book/show/12', $webpaths);
    $this->assertContains('/user/9/book/display/12', $webpaths);
  }

  public function test_getRouteId() {
    $router = $this->getRouter();

    $this->assertEquals('BookController#update',$router->getRouteId('BookController', 'update'));
  }

  // _TODO: test dispatch
}
