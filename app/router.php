<?php
/**
 * 
 */
class Router {
  static $request = array();

  public function __construct() {
  }

  static function route() {
    $path = $_SERVER['PATH_INFO']; #self::extract_uri();

    // TODO: Need lots of care to this, with and without index.php
    //       Default action to index
    //       In this version, index.php controller and action must be given in url

    // extract controller and action name from uri
    $matches = array();
    preg_match('/\/([a-zA-Z]*)\/([a-zA-Z]*)/', $path, $matches);
    $controller_name = ucfirst(strtolower($matches[1])) . 'Controller';
    $action = strtolower($matches[2]);

    // call action on controller
    $controller = new $controller_name();
    if (is_callable(array($controller, $action))) {
      $controller->{$action}();
    } else {
      // TODO: display error;
    }
  }

  static function extract_uri() {
    // $app_relative_path = /folder/that/lead/to/application/root
    $app_relative_path = substr(Application::$APP_PATH, strlen($_SERVER['DOCUMENT_ROOT']));
    $request_uri = substr($_SERVER['REQUEST_URI'], strlen($app_relative_path . '/' . Application::$PUB_DIR));

    return $request_uri;
  }
}
