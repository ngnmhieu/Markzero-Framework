<?php
namespace Markzero\Http;

use Markzero\App;

/**
 * Router class handles the job of determining
 * where to go with a given URI.
 */
class Router {
  
  /*
   * $routes have the following form:
   * array(
   *  'GET'    => array('path_pattern' => array(:controller => 'user', :action => 'index' ), array(),...),
   *  'POST'   => array('path_pattern' => array(:controller => 'user', :action => 'insert'), array(),...),
   *  'PUT'    => array('path_pattern' => array(:controller => 'user', :action => 'update'), array(),...),
   *  'DELETE' => array('path_pattern' => array(:controller => 'user', :action => 'delete'), array(),...),
   * );
   * add new routes to $routes with map() method. (config/routes.php)
   */
  private $routes = array();
  /**
   * $webpaths is an array that map a route identification (i.e 'controller#action') 
   * to a function. This function will be used to replace user-provided parameters 
   * to webpath and returns a webpath (eg: '/controller/{PARAM1}/edit/{PARAM2}) 
   * with PARAM1, PARAM2,... are user-provided arguments
   */
  private $webpaths = array();

  private $response; // Response object
  private $request;  // Request object

  /**
   * @param Request  $request
   * @param Response $response
   */
  function __construct(Request $request = null, Response $response = null) {
    $this->routes = array(
      'GET' => array(),
      'PUT' => array(),
      'POST' => array(),
      'DELETE' => array()
    );

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
   * Call the right action of the right controller
   */
  public function dispatch() {
    $request = $this->request;
    $http_method = $request->getMethod();


    // Detect Cross-Domain Request
    if ($request->isCrossDomain()) {
      if ($request->isCrossDomainAllowed()) {
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

    // get mappings in routes.php file, according to HTTP method
    $mappings = $this->routes[$http_method]; 
    $destination = array();
    $args = array(); // will contain URI arguments
    foreach ($mappings as $pattern => $dest) {
      $args = $this->matchPath($pattern, $request->getPathInfo());
      if($args !== false) {
        $destination = $dest;
        break;
      }
    }

    // No mapping found
    if (empty($destination)) {
      $this->response->setStatusCode(Response::HTTP_BAD_REQUEST,
        'Bad Request (No Route Found)');
      $this->response->send();

      return $this;
    }

    $this->route($destination, $args);

    return $this;
  }

  /*
   * Check if the given path matched with
   * @return array containing information of controller and action if they match
   *         false if they don't
   */
  private function matchPath($pattern, $path) {
    $matches = array();
    $result = preg_match($pattern, $path, $matches);
    return $result ? array_slice($matches, 1) : false;
  }

  /*
   * Actually call the appropriate method in appropriate controller
   * @param array $destination array(':controller' => "controller", ':action' => "action"]
   * @param array $args Arguments that are passed to controller method
   */
  private function route(array $destination, array $args = array()) {
    // extract controller name and its filename
    $dest_ctrl = $destination[':controller'];
    $controller = ucfirst(strtolower($dest_ctrl)).'Controller';
    $controller_filename = $controller.'.php';

    $action = strtolower($destination[':action']);

    // Controller not found
    if (!class_exists($controller)) {
      $this->response->setStatusCode(
        Response::HTTP_BAD_REQUEST,
        "Bad Request (controller `$controller` not found)"
      );
      $this->response->send();
      return;
    }

    // setup controller object
    $controller_obj = new $controller(App::$request, App::$response);

    // call action on controller
    if (is_callable(array($controller_obj, $action))) {
      call_user_func_array(array($controller_obj, $action), $args);
      // prepare the reponse and send to the client
      $this->response->respond();
    } else {
      $this->response->setStatusCode(
        Response::HTTP_BAD_REQUEST,
        "Bad Request (action `$action` not found)"
      );
      $this->response->send();
    }
  }

  /**
   * Matches the URI with the controller and action
   * @param string $method HTTP Method, must be among these methods POST, GET, PUT, DELETE,...
   * @param string $route_string Specify the uri that will be match against
   * @param string $dest Destination, should have the form of {controller}#{action}
   * @param string $path_name (optional, not implemented)
   **/
  public function map($method, $route_string, $dest, $path_name = null) {
    if (preg_match("~([a-zA-Z0-9_\-/]+)#([a-zA-Z0-9_\-]+)~", $dest, $matches)) {
      $controller = $matches[1];
      $action     = $matches[2];
    } else {
      throw new InvalidArgumentException("Illegal destination: `$dest`");
    }

    $pattern = $route_string;

    // add '/?' to the end of $pattern
    // so that both uri '/user/' and '/user' works
    $pattern .= !preg_match("~.*/$~", $pattern) ? '/?' : '?';

    // construct pattern, which will later be matched with user-input path 
    $pattern = '~^'.$pattern.'$~';

    // save the route information
    $this->routes[strtoupper($method)][$pattern] = array(
      ':controller' => $controller, 
      ':action' => $action
    ); 

    // _TODO: namespaced ???
    // route identification
    $route_id = "{$controller}#{$action}";
    if (!array_key_exists($route_id, $this->webpaths)) {
      $this->webpaths[$route_id] = array();
    }

    $this->webpaths[$route_id][] = function(array $params) use ($route_string) {
      if (count($params) === 0) {
        return $route_string;
      }
      $patterns = array_fill(0, count($params), '~\(.*?\)~');
      return preg_replace($patterns, $params, $route_string, 1);
    };
  }

  /**
   * Return webpaths with all the parameters replaced
   * this usually be called by helper function webpath() and by controller
   * @param string $controller
   * @param string $action
   * @param array  $params 
   * @return array List of all webpaths associated with the provided controller and action
   * @throw InvalidArgumentException when $controller or $action is not string
   *        Exception if no path is found
   */
  public function getWebpaths($controller, $action, array $params = array()) {
    if(!is_string($controller)) {
      throw new InvalidArgumentException('Argument $controller must be a string: Router#getWebpath.');
    } 

    if(!is_string($action)) {
      throw new InvalidArgumentException('Argument $action must be a string: Router#getWebpath.');
    }

    $route_id = "{$controller}#{$action}";
    if (empty($this->webpaths[$route_id])) {
      throw new Exception("No webpath is found for path name: ". $route_id);
    }

    // return all populated webpath associated with the specified controller and action
    return array_map(function($webpath_callback) use ($params) {
      return $webpath_callback($params);
    }, $this->webpaths[$route_id]);
  }

  /*
   * Helper method that maps root of webapp to $dest 
   * @param string $destination in form of "controller#action"
   */
  public function root($dest) {
    $this->map("get", "/", $dest);
    $this->map("post", "/", $dest);
    $this->map("delete", "/", $dest);
    $this->map("put", "/", $dest);
  }

  /**
   * Shorthand for $this->map('get',...);
   */
  public function get($route_string, $dest) {
    return $this->map('get', $route_string, $dest);
  }
  /**
   * Shorthand for $this->map('post',...);
   */
  public function post($route_string, $dest) {
    return $this->map('post', $route_string, $dest);
  }
  /**
   * Shorthand for $this->map('put',...);
   */
  public function put($route_string, $dest) {
    return $this->map('put', $route_string, $dest);
  }
  /**
   * Shorthand for $this->map('delete',...);
   */
  public function delete($route_string, $dest) {
    return $this->map('delete', $route_string, $dest);
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
