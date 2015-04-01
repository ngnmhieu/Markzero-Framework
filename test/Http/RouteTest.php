<?php
use Markzero\Http\Route;
use Markzero\Http\RouteMatcher\AbstractRouteMatcher;

class RouteTest extends PHPUnit_Framework_TestCase {

  public function getPositiveMatcher() {
    $matcher = Mockery::mock('Markzero\Http\RouteMatcher\AbstractRouteMatcher', array(
      'match' => true,
      'getArguments' => array()
    ));
    return $matcher;
  }

  public function getNegativeMatcher() {
    $matcher = Mockery::mock('Markzero\Http\RouteMatcher\AbstractRouteMatcher', array(
      'match' => false,
      'getArguments' => array()
    ));
    return $matcher;
  }

  public function test_matchPath_succeeds() {
    $route = new Route('/book/([0-9]+)/', 'book', 'show', $this->getPositiveMatcher());

    $this->assertTrue($route->matchPath('/book/1'));
    $this->assertTrue($route->matchPath('/book/20/'));
  }

  public function test_matchPath_fails() {
    $route = new Route('/book/([0-9]+)/', 'book', 'show', $this->getNegativeMatcher());

    $this->assertFalse($route->matchPath('/book/'));
    $this->assertFalse($route->matchPath('/book/-1'));
    $this->assertFalse($route->matchPath('/movie/'));
    $this->assertFalse($route->matchPath('/books/20/'));
  }

  public function test_go_ControllerNotFound() {

    $this->setExpectedException('\RuntimeException');
  
    $route = new Route('/book/', 'SomeController', 'show', $this->getPositiveMatcher());

    $response = Mockery::mock('Markzero\Http\Response');
    $request = Mockery::mock('Markzero\Http\Request');

    $route->go($request, $response);
  }

  public function test_getWebpath_withoutArguments() {
    $route1 = new Route('/book/', 'SomeController', 'show', $this->getPositiveMatcher());
    $route2 = new Route('/book', 'SomeController', 'show', $this->getPositiveMatcher());

    $this->assertEquals($route1->getWebpath(array()), '/book/');
    $this->assertEquals($route2->getWebpath(array()), '/book');
  }

  public function test_getWebpath_withArguments() {
    $route = new Route('/user/([0-9]+)/book/([0-9]+)/like/', 'book', 'like', $this->getPositiveMatcher());

    $this->assertEquals($route->getWebpath(array(1,20)), '/user/1/book/20/like/');
  }

  // _TODO: How to test controller gets called?
  //        How to test fake that controller exists?

}
