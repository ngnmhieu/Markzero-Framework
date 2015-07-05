<?php
use Markzero\Http\Routing\Route;
use Markzero\Http\Routing\RouteMatcher\AbstractRouteMatcher;

class RouteTest extends PHPUnit_Framework_TestCase {

  public function getPositiveMatcher()
  {
    $matcher = Mockery::mock('Markzero\Http\Routing\RouteMatcher\AbstractRouteMatcher', array(
      'match' => true,
      'getArguments' => array()
    ));
    return $matcher;
  }

  public function getNegativeMatcher()
  {
    $matcher = Mockery::mock('Markzero\Http\Routing\RouteMatcher\AbstractRouteMatcher', array(
      'match' => false,
      'getArguments' => array()
    ));
    return $matcher;
  }

  public function test_matchPath_succeeds()
  {
    $route = new Route('/book/([0-9]+)/', 'book', 'show', $this->getPositiveMatcher());

    $this->assertTrue($route->matchPath('/book/1'));
    $this->assertTrue($route->matchPath('/book/20/'));
  }

  public function test_matchPath_fails() 
  {
    $route = new Route('/book/([0-9]+)/', 'book', 'show', $this->getNegativeMatcher());

    $this->assertFalse($route->matchPath('/book/'));
    $this->assertFalse($route->matchPath('/book/-1'));
    $this->assertFalse($route->matchPath('/movie/'));
    $this->assertFalse($route->matchPath('/books/20/'));
  }

  public function test_go_ControllerNotFound() 
  {
    $this->setExpectedException('\RuntimeException');

    $route = new Route('/book/', 'SomeController', 'show', $this->getPositiveMatcher());

    $route->go();
  }

  public function test_getWebpath_withoutArgs() 
  {
    $route1 = new Route('/book/', 'SomeController', 'show', $this->getPositiveMatcher());
    $route2 = new Route('/book', 'SomeController', 'show', $this->getPositiveMatcher());

    $this->assertEquals($route1->getWebpath(), '/book/');
    $this->assertEquals($route2->getWebpath(), '/book');
  }

  public function test_getWebpath_withArgs()
  {
    $route = new Route('/user/([0-9]+)/book/([0-9]+)/like/', 'book', 'like', $this->getPositiveMatcher());

    $this->assertEquals('/user/1/book/20/like/', $route->getWebpath(array(1,20)));
  }

  public function test_getWebpath_withQueryParams() 
  {
    $route = new Route('/book/search/', 'BookController', 'Search', $this->getPositiveMatcher());

    $this->assertEquals('/book/search/?keyword=testphp', $route->getWebpath([], ['keyword' => 'testphp']));
    $this->assertEquals('/book/search/?keyword=test+php', $route->getWebpath([], ['keyword' => 'test php']));
    $this->assertEquals('/book/search/?keyword=test%26%26php%2B', $route->getWebpath([], ['keyword' => 'test&&php+']));
  }

}
