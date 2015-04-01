<?php

use Markzero\Http\RouteMatcher\RegexRouteMatcher;

class RegexRouteMatcherTest extends \PHPUnit_Framework_TestCase {

  public function test_match_2Arguments() {
    $matcher = new RegexRouteMatcher('~^/user/([0-9]+)/book/([0-9]+)/like/?$~');

    $this->assertCount(0, $matcher->getArguments());

    $matcher->match('/user/1/book/20/like/');

    $this->assertCount(2, $matcher->getArguments());
  }

  public function test_match_noArguments() {
    $matcher = new RegexRouteMatcher('~^/book/$~');

    $this->assertCount(0, $matcher->getArguments());

    $matcher->match('/book/');

    $this->assertCount(0, $matcher->getArguments());
  }

}
