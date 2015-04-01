<?php
namespace Markzero\Http;

use Markzero\App;

/**
 * Router class handles the job of determining
 * where to go with a given URI.
 */
class Router {
  
  /*
   * Contains application's routes 
   * add new routes with #map method. (config/routes.php)
   *
   * It has the following form:
   *  array(
   *   'GET'    => Route array,
   *   'POST'   => Route array,
   *   'PUT'    => Route array,
   *   'DELETE' => Route array,
   *   ...
   *  );
   *
   * @var array 
   *
   */
  private $routes = array();

  /**
   * $webpaths is an array that map a route identification (i.e 'controller#action') 
   * to a function. This function will be used to replace user-provided parameters 
   * to webpath and returns a webpath (eg: '/controller/{PARAM1}/edit/{PARAM2}) 
   * with PARAM1, PARAM2,... are user-provided arguments
   */
  private $webpaths = array();

  /**
   * @var Markzero\Http\Response 
   */
  private $response;

  /**
   * @var Markzero\Http\Request
   */
  private $request;

  /**
   * @param Markzero\Http\Request $request
   * @param Markzero\Http\Response $response
   */
  function __construct(Request $request = null, Response $response = null) {
    $this->routes = array();
    $this->request  = $request;
    $this->response = $response;
  }

  function setRequest(Request $request) {
    $this->request = $request;
  }

  function setResponse(Response $response) {
    $this->response = $response;
  }

  /**
   * @return array Markzero\Http\Route
   */
  public function getRoutes() {
    return $this->routes;
  }

  /**
   * Find a route, execute it and send response to client
   */
  public function dispatch() {
    $http_method = $this->request->getMethod();

    // Detect Cross-Domain Request
    if ($this->request->isCrossDomain()) {
      if ($this->request->isCrossDomainAllowed()) {
        $this->response->setAccessControlHeaders();
        $this->response->setStatusCode(Response::HTTP_OK);
      } else {
        $this->response->setStatusCode(Response::HTTP_BAD_REQUEST,
          'Bad Request (Cross-Domain Request not allowed)');
      }

      // Detect preflight request (for cross domain request)
      if ($http_method === 'OPTIONS') {
        $this->response->send();
        return $this;
      }
    }

    // Match requested path against registered routes
    $routes = $this->routes[$http_method]; 
    foreach ($routes as $route) {
      if ($route->matchPath($this->request->getPathInfo())) {

        try {
          $route->go($this->request, $this->response);
          $this->response->respond();

        } catch(\RuntimeException $e) {

          $this->response->setStatusCode(
            Response::HTTP_BAD_REQUEST,
            "Bad Request (".$e->getMessage().")"
          );
          $this->response->respond(true);
        }

        return $this;
      }
    }

    // No Route match
    $this->response->setStatusCode(
      Response::HTTP_BAD_REQUEST, 'Bad Request (No Route Found)'
    );
    $this->response->respond(true);

    return $this;
  }

  /**
   * Create a Route that map a URI to a controller's action
   *
   * @param string HTTP Method: POST, GET, PUT, DELETE,...
   * @param string Specify the uri that will be match against
   * @param string Controller
   * @param string Action
   **/
  public function map($method, $route_string, $controller, $action) {

    if (!preg_match("~^[a-zA-Z_][a-zA-Z0-9_]*$~", $controller)) {
      throw new \InvalidArgumentException("Invalid Controller Name: `$controller`");
    }
    if (!preg_match("~^[a-zA-Z_][a-zA-Z0-9_]*$~", $action)) {
      throw new \InvalidArgumentException("Invalid Action Name: `$action`");
    }

    $route = new Route($route_string, $controller, $action);

    // Save route for request routing 
    $method_normalized = strtoupper($method);
    if (!array_key_exists($method_normalized, $this->routes))
      $this->routes[$method_normalized] = array();

    $this->routes[$method_normalized][] = $route;

    // Save route for web path generation 
    $route_id = $controller.'#'.$action;
    if (!array_key_exists($route_id, $this->webpaths))
      $this->webpaths[$route_id] = array();

    $this->webpaths[$route_id][] = $route;
  }

  /**
   * Return webpaths with all the parameters replaced
   *
   * @param string Controller
   * @param string Action
   * @param array  Arguments for the route
   * @return array List of all webpaths associated with the provided controller and action
   * @throw InvalidArgumentException when $controller or $action is not string
   *        RuntimeException if no path is found
   */
  public function getWebpaths($controller, $action, array $args = array()) {
    if(!is_string($controller)) {
      throw new \InvalidArgumentException('Argument $controller must be a string: Router#getWebpath.');
    } 

    if(!is_string($action)) {
      throw new \InvalidArgumentException('Argument $action must be a string: Router#getWebpath.');
    }

    $route_id = "{$controller}#{$action}";

    if (!array_key_exists($route_id, $this->webpaths) || empty($this->webpaths[$route_id]))
      throw new \RuntimeException("No web path is found for path name: ". $route_id);
    
    return array_map(function($route) use ($args) {
      return $route->getWebpath($args);
    }, $this->webpaths[$route_id]);
  }

  /*
   * Helper method that maps root of webapp to $dest 
   * @param string Controller
   * @param string Action
   */
  public function root($controller, $action) {
    $this->map("get", "/", $controller, $action);
    $this->map("post", "/", $controller, $action);
    $this->map("delete", "/", $controller, $action);
    $this->map("put", "/", $controller, $action);
  }

  /**
   * Shorthand for $this->map('get',...);
   */
  public function get($route_string, $controller, $action) {
    return $this->map('get', $route_string, $controller, $action);
  }
  /**
   * Shorthand for $this->map('post',...);
   */
  public function post($route_string, $controller, $action) {
    return $this->map('post', $route_string, $controller, $action);
  }
  /**
   * Shorthand for $this->map('put',...);
   */
  public function put($route_string, $controller, $action) {
    return $this->map('put', $route_string, $controller, $action);
  }
  /**
   * Shorthand for $this->map('delete',...);
   */
  public function delete($route_string, $controller, $action) {
    return $this->map('delete', $route_string, $controller, $action);
  }
  
  /* 
   * draw() method serves as DSL (Domain Specific Language) 
   * @param callable $func | "draws" routes for application, 
   *                       |  $func takes a Router object
   * @return Router the current Router object
   */  
  public function draw($func) {
    $func($this);
    return $this;
  }
}