<?php
/**
 * 
 */
class Router {
  static $request = array();

  public function __construct() {
  }

  static function route() {
    $path = $_SERVER['PATH_INFO'];

    // TODO: Need lots of care to this, with and without index.php
    //       Default action to index
    //       In this version, index.php controller and action must be given in url

    // TODO:consider extract to a new function
    // extract controller and action name from uri
    // action default to "index" if not provided
    $matches = array();
    if(preg_match('/\/([a-zA-Z]*)\/([a-zA-Z]*)\/?/', $path, $matches)) {
      $controller_name = ucfirst(strtolower($matches[1])) . 'Controller';
      $action = strtolower($matches[2]);
    } else if( preg_match('/\/([a-zA-Z]*)\/?/', $path, $matches)) {
      $controller_name = ucfirst(strtolower($matches[1])) . 'Controller';
      $action = "index";
    } else {
      // TODO: ROUTING ERROR
      die("Illegal URI");
    }


    $filename = ucfirst($controller_name).".php";
    $controllers_dir = Application::$CONTROLLERS_DIR;
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
}
