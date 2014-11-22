<?php
/**
 * Router class handles the job of determining
 * where to go with a given URI.
 */
class Router {
  
  /*
   * $routes have the following form:
   * array(
   *  ['GET' => array(array(:controller => 'user', :action => 'index'), [...])]
   *  ['POST' => array(array(:controller => 'user', :action => 'insert'), [...])]
   *  ['PUT' => array(array(:controller => 'user', :action => 'update'), [...])]
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

  /**
   * Return the Singleton instance Router of the class
   */
  public static function getInstance() {
    static $instance = null;

    if ($instance === null) {
      $instance = new static();
    }

    return $instance;
  }

  protected function __construct() {
    $this->routes = array(
      'GET' => array(),
      'PUT' => array(),
      'POST' => array(),
      'DELETE' => array()
    );
  }

  public function dispatch() {
    $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
    $http_method = $_SERVER['REQUEST_METHOD']; 

    // load mappings in configuration file, according to HTTP method
    $mappings = $this->routes[$http_method]; 
    $destination = array();
    $args = array(); // will contain URI arguments
    foreach ($mappings as $pattern => $dest) {
      $args = $this->match_path($pattern, $path);
      if($args !== false) {
        $destination = $dest;
        break;
      }
    }

    if (empty($destination)) {
      // TODO: handles no mapping found
      die("no mapping found"); 
    }

    $this->route($destination, $args);
  }

  /*
   * Check if the given path matched with
   * @return array containing information of controller and action if they match
   *         false if they don't
   */
  private function match_path($pattern, $path) {
    // TODO: extract arguments in a better way
    $matches = array();
    $result = preg_match($pattern, $path, $matches);
    return $result ? array_slice($matches, 1) : false;
  }

  /*
   * actually call the appropriate method in appropriate controller
   * @param array $destination array(':controller' => "controller", ':action' => "action"]
   * @param array $args Arguments that are passed to controller method
   */
  private function route(array $destination, array $args = array()) {
    // extract controller name and its filename
    $dest_ctrl = $destination[':controller'];
    $dir_separator_pos = strpos($dest_ctrl, '/');
    $subdir = "";
    if ($dir_separator_pos !== false) { // check if the controller lies in subdirectory
      $subdir = substr($dest_ctrl, 0, $dir_separator_pos + 1);
      $controller = substr($dest_ctrl, $dir_separator_pos + 1);
      $controller = ucfirst(strtolower($controller)).'Controller';
      $controller_filename = $subdir.$controller.'.php';
    } else {
      $controller = ucfirst(strtolower($dest_ctrl)).'Controller';
      $controller_filename = $controller.'.php';
    }

    $action = strtolower($destination[':action']);

    // call the appropriate controller
    $controller_file = App::$CONTROLLER_DIR.'/'.$controller_filename;
    if (file_exists($controller_file)) {
      require_once($controller_file);
    } else {
      // TODO: Error handling
      die("Cannot find $controller in $controller_file");
    }

    // setup controller object
    $controller_obj = new $controller();
    $controller_obj->set_view_subdir($subdir);

    // call action on controller
    if (is_callable(array($controller_obj, $action))) {
      // call method `$action` on object $controller` with arguments `$args`
      call_user_func_array(array($controller_obj, $action), $args);
    } else {
      // TODO: display error;
    }
  }

  /**
   * Matches the URI with the controller and action
   * @param string $method must be among these methods POST, GET, PUT, DELETE
   * @param string $route_string specify the uri that will be match
   * @param string $dest in form {controller}#{action}
   * @param string $path_name | will be used to get web path [optional] 
   **/
  public function map($method, $route_string, $dest) {
    if (preg_match("~([a-zA-Z0-9_\-/]+)#([a-zA-Z0-9_\-]+)~", $dest, $matches)) {
      $controller = $matches[1];
      $action     = $matches[2];
    } else {
      // TODO: illegal destination
      die('illegal destination');
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
    $path_name = "{$controller}_{$action}";
    $this->web_paths[$path_name] = function(array $params) use ($route_string) {
      return preg_replace_inorder('~\(.*?\)~', $params, $route_string);
    };
  }

  /**
   * return a web path with all the parameters replaced
   * this usually be called by helper function path() and by controller
   * @param string $path_name | eg: 'transaction_edit'
   * @param array  $params 
   */
  public function get_web_path($path_name, array $params = array()) {
    if (empty($this->web_paths[$path_name])) {
      die("No such path_name in web_paths: ". $path_name);
    }
    return $this->web_paths[$path_name]($params);
  }

  /*
   * helper method that maps root of webapp to $dest 
   * @param string $destination in form of "controller#action"
   */
  public function root($dest) {
    $this->map("GET", "/", $dest);
    $this->map("POST", "/", $dest);
  }
  
  /* TODO:
   * resources() method defines default RESTful routes
   * for the application
   */  
  public function resources() {
  }

  /* 
   * draw() method serves as DSL (Domain Specific Language) 
   * @param callable $func "draws" routes for application, 
   *        $func takes a Router object
   */  
  public function draw(callable $func) {
    $func($this);
  }
}
