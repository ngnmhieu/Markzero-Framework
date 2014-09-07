<?php
/**
 * Router class handles the job of determining
 * where to go with a given URI.
 */
class Router {
  
  private $routes = array();

  function __construct() {
    $routes = array(
      'GET' => array(),
      'PUT' => array(),
      'POST' => array(),
      'DELETE' => array()
    );
  }

  function dispatch_old() {
    $path = $_SERVER['PATH_INFO'];

    // extract controller and action from uri
    // default action index()
    $matches = array();
    if (preg_match('/\/([a-zA-Z]+)\/([a-zA-Z]+)\/?/', $path, $matches)) {
      $controller_name = ucfirst(strtolower($matches[1])) . 'Controller';
      $action = strtolower($matches[2]);
    } else if( preg_match('/\/([a-zA-Z]*)\/?/', $path, $matches)) {
      $controller_name = ucfirst(strtolower($matches[1])) . 'Controller';
      $action = "index";
    } else {
      // TODO: ROUTING ERROR
      die("Illegal URI");
    }

    // call the right controller
    $filename = ucfirst($controller_name).".php";
    $controllers_dir = App::$CONTROLLERS_DIR;
    if (file_exists($controllers_dir.$filename)) {
      require_once($controllers_dir . $filename);
    } else {
      // TODO: Error;
      die("Cannot find $controller_name in {$controllers_dir}{$filename}");
    }

    // call action on controller
    $controller = new $controller_name();
    if (is_callable(array($controller, $action))) {

      $controller->{$action}();
    } else {
      // TODO: display error;
    }
  }

  public function dispatch() {
    $path = $_SERVER['PATH_INFO'];
    $method = $_SERVER['REQUEST_METHOD']; 
    $mappings = $this->routes[$method];
    $destination = null;
    foreach ($mappings as $pattern => $dest) {
      if(preg_match($pattern, $path)) {
        $destination = $dest;
        break;
      }
    }

    // TODO: handles no mapping found
    if ($destination == null) {
      die("no mapping found"); 
    }

    $this->route($destination);
  }

  private function match_path($path, $pattern) {
  }

  // TODO: should be public or private?
  public function route($destination) {
    $controller_name = ucfirst(strtolower($destination[':controller'])) . 'Controller';
    $action = strtolower($destination[':action']);

    // call the right controller
    $filename = $controller_name.".php";
    $controllers_dir = App::$CONTROLLERS_DIR;
    if (file_exists($controllers_dir.$filename)) {
      require_once($controllers_dir . $filename);
    } else {
      // TODO: Error;
      die("Cannot find $controller_name in {$controllers_dir}{$filename}");
    }

    // call action on controller
    $controller = new $controller_name();
    if (is_callable(array($controller, $action))) {

      $controller->{$action}();
    } else {
      // TODO: display error;
    }
  }

  /*
   * Matches the URI with the controller and action
   * @param string $method must be POST GET PUT DELETE
   * @param string $route_string specify the uri that will be match
   * @param string $dest in form {controller}#{action}
   */
  public function map($method, $route_string, $dest) {
    // $matches = array();
    if (preg_match("/([a-zA-Z0-9_\-]+)#([a-zA-Z0-9_\-]+)/", $dest, $matches)) {
      $controller = $matches[1];
      $action     = $matches[2];
    } else {
      // TODO: illegal destination
      die('illegal destination');
    }

    // construct pattern, which will later be matched with user-input path 
    $pattern = '~^'.$route_string.'$~';

    $this->routes[strtoupper($method)][$pattern] = array(
      ':controller' => $controller, 
      ':action' => $action
    ); 
  }

  /*
   * @param string $destination in form of "controller#action"
   */
  public function root($dest) {
    $this->map("GET", "/", $dest);
    $this->map("POST", "/", $dest);
  }
  
  /*
   * resources() method defines default RESTful routes
   * for the application
   */  
  public function resources() {
  }

  /*
   * draw() method serves as DSL (Domain Specific Language)
   */  
  public function draw($func) {
    $func($this);
  }
}
