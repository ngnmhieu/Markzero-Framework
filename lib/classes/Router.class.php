<?php
/**
 * Router class handles the job of determining
 * where to go with a given URI.
 */
class Router {
  
  /*
   * $routes have the following form:
   * array(
   *  ['GET'    => array(array(:controller => 'user', :action => 'index' ), [...])]
   *  ['POST'   => array(array(:controller => 'user', :action => 'insert'), [...])]
   *  ['PUT'    => array(array(:controller => 'user', :action => 'update'), [...])]
   *  ['DELETE' => array(array(:controller => 'user', :action => 'delete'), [...])]
   * );
   * add new routes to $routes with map() method. (config/routes.php)
   */
  private $routes = array();
  /**
   * $web_paths is an array that map a string to a function
   * the string is the name of the path (eg: 'transaction_edit') 
   * the function will be used to replace user-provided parameters to web path
   * and returns a web path (eg: '/transaction/{PARAM1}/edit/{PARAM2}) 
   * with PARAM1, PARAM2,... are user-provided arguments
   */
  private $web_paths = array();

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

    // load mappings in configuration file, according to HTTP method
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
    // _TODO: extract arguments in a better way
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

    // call the appropriate controller
    $controller_file = App::$CONTROLLER_PATH.$controller_filename;
    if (file_exists($controller_file)) {
      require_once($controller_file);
    } else {
      $this->response->setStatusCode(
        Response::HTTP_BAD_REQUEST,
        "Bad Request (controller `$controller` not found)"
      );
      $this->response->send();
      return;
    }

    // setup controller object
    $controller_obj = new $controller();

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
   * @param string $path_name (optional) Name of path path , default to {controller}#{action} 
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

    // add mapping for web paths
    if ($path_name === null) {
      $path_name = "{$controller}#{$action}";
    }
    $this->web_paths[$path_name] = function(array $params) use ($route_string) {
      return preg_replace_inorder('~\(.*?\)~', $params, $route_string);
    };
  }

  /**
   * Return a web path with all the parameters replaced
   * this usually be called by helper function webpath() and by controller
   * @param string $path_name | eg: 'transaction#edit'
   * @param array  $params 
   */
  public function getWebPath($path_name, array $params = array()) {
    // _TODO: get web path by providing controller and action
    
    if (empty($this->web_paths[$path_name])) {
      die("No such path_name in web_paths: ". $path_name);
    }
    return $this->web_paths[$path_name]($params);
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
