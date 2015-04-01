<?php
namespace Markzero\Http\Routing;

use Markzero\Http\Request;
use Markzero\Http\Response;
use Markzero\Http\Routing\RouteMatcher\AbstractRouteMatcher;

class Route {

  /**
   * @var Markzero\Http\Routing\RouteMatcher\AbstractRouteMatcher 
   */
  private $matcher;
  /**
   * @var string
   */
  private $route_string;
  /**
   * @var string Controller name
   */
  private $controller;
  /**
   * @var string Action/Method name
   */
  private $action;
  
  /**
   * @param string Route string
   * @param string Destination controller
   * @param string Action to be executed
   * @param Markzero\Http\Routing\RouteMatcher\AbstractRouteMatcher 
   */
  public function __construct($route_string, $controller, $action, AbstractRouteMatcher $matcher) {
    $this->route_string  = $route_string;
    $this->controller    = $controller;
    $this->action        = $action;
    $this->matcher       = $matcher;
  }

  /**
   * 
   * @param string Path to be matched against the Route's pattern
   * @return bool
   */
  public function matchPath($path) {
    return $this->matcher->match($path);
  }

  /**
   * Execute controller's action
   *
   * @param Markzero\Http\Request
   * @param Markzero\Http\Response
   * @throw \RuntimeException
   */
  public function go(Request $request, Response $response) {

    if (!class_exists($this->controller)) {
      throw new \RuntimeException("Controller '".$this->controller."' cannot be instantiated");
    }

    $controller_obj = new $this->controller($request, $response);
    
    if (!is_callable(array($controller_obj, $this->action))) {
      throw new \RuntimeException("Action '".$this->action."' in controller '".$this->controller."' not found");
    }

    return call_user_func_array(array($controller_obj, $this->action), $this->matcher->getArguments());
  }

  /**
   * Return the web path with populated arguments 
   * @param array Arguments to be populated
   */
  public function getWebpath(array $arguments = array()) {
    if (count($arguments) === 0) {
      return $this->route_string;
    }

    $patterns = array_fill(0, count($arguments), '~\(.*?\)~');

    return preg_replace($patterns, $arguments, $this->route_string, 1);
  }

}
