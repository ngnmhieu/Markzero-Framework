<?php
use Markzero\Http\Route;

class RouteTest extends PHPUnit_Framework_TestCase {

  public function test_matchPath_succeeds() {
    $route = new Route('/book/([0-9]+)/', 'book', 'show');

    $this->assertTrue($route->matchPath('/book/1'));
    $this->assertTrue($route->matchPath('/book/20/'));
  }

  public function test_matchPath_fails() {
    $route = new Route('/book/([0-9]+)/', 'book', 'show');

    $this->assertFalse($route->matchPath('/book/'));
    $this->assertFalse($route->matchPath('/book/-1'));
    $this->assertFalse($route->matchPath('/movie/'));
    $this->assertFalse($route->matchPath('/books/20/'));
  }

  public function test_matchPath_set2Arguments() {
    $route = new Route('/user/([0-9]+)/book/([0-9]+)/like/', 'book', 'like');

    $this->assertCount(0, $route->getArguments());

    $route->matchPath('/user/1/book/20/like/');

    $this->assertCount(2, $route->getArguments());
  }

  public function test_matchPath_set0Arguments() {
    $route = new Route('/book/', 'book', 'show');

    $this->assertCount(0, $route->getArguments());

    $route->matchPath('/book/');

    $this->assertCount(0, $route->getArguments());
  }

  public function test_go_ControllerNotFound() {

    $this->setExpectedException('\RuntimeException');
  
    $route = new Route('/book/', 'SomeController', 'show');

    $response = Mockery::mock('Markzero\Http\Response');
    $request = Mockery::mock('Markzero\Http\Request');

    $route->go($request, $response);
  }

  public function test_getWebpath_withoutArguments() {
    $route1 = new Route('/book/', 'SomeController', 'show');
    $route2 = new Route('/book', 'SomeController', 'show');

    $this->assertEquals($route1->getWebpath(array()), '/book/');
    $this->assertEquals($route2->getWebpath(array()), '/book');
  }

  public function test_getWebpath_withArguments() {
    $route = new Route('/user/([0-9]+)/book/([0-9]+)/like/', 'book', 'like');

    $this->assertEquals($route->getWebpath(array(1,20)), '/user/1/book/20/like/');
  }

  // _TODO: How to test controller gets called?
  //        How to test fake that controller exists?

}
