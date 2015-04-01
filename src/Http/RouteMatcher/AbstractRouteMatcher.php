<?php
namespace Markzero\Http\RouteMatcher;

abstract class AbstractRouteMatcher {
  /**
   * @var array
   */
  protected $arguments;

  /**
   * Return extracted arguments
   * @return @array
   */
  public function getArguments() {
    return $this->arguments;
  }
}

