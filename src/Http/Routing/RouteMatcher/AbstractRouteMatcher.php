<?php
namespace Markzero\Http\Routing\RouteMatcher;

abstract class AbstractRouteMatcher {
  /**
   * @var array
   */
  protected $arguments;

  /**
   * Return extracted arguments
   * @return array
   */
  public function getArguments() {
    return $this->arguments;
  }

  /**
   * Match the given $path
   * @param string
   * @return bool
   */
  abstract function match($path);
}

