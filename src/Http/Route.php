<?php
namespace Markzero\Http;

use Markzero\Http\RouteMatcher;

class Route {

  /**
   * @var AbstractRouteMatcher
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
   * @var array
   */
  private $arguments;
  
  /**
   * @param string Route string
   * @param string Destination controller
   * @param string Action to be executed
   */
  public function __construct($route_string, $controller, $action) {
    $this->route_string  = $route_string;
    $this->controller    = $controller;
    $this->action        = $action;
    $this->arguments     = array();

    $pattern = $route_string;
    // add '/?' to the end of $pattern so that both URIs '/user/' and '/user' works
    $pattern .= !preg_match("~.*/$~", $pattern) ? '/?' : '?';
    $this->matcher = new RouteMatcher\RegexRouteMatcher('~^'.$pattern.'$~');
  }

  /**
   * 
   * @param string Path to be matched against the Route's pattern
   * @return bool
   */
  public function matchPath($path) {
    return $this->matcher->match($path);
  }

  public function getArguments() {
    return $this->matcher->getArguments();
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

    return call_user_func_array(array($controller_obj, $this->action), $this->getArguments());
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
