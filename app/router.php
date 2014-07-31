<?php
/**
 * 
 */
class router {
  static $request = array();

  /**
   * 
   */
  public function __construct() {
  }

  static function route() {
    $request_uri = self::extract_uri();

    // extract controller and action name from uri
    $matches = array();
    // TODO: Need lots of care to this, with and without index.php
    //       Default action to index
    //       In this version, index.php controller and action must be given in url
    preg_match('/index.php\/([a-zA-Z]*)\/*([a-zA-Z]*)/', $request_uri, $matches);
    $controller_name = ucfirst($matches[1]) . 'Controller';
    $action = $matches[2];

    // call action on controller
    $controller = new $controller_name();
    $controller->{$action}();

    // echo "<pre>";
    // echo $controller . " \n";
    // echo $action . " \n ";
  }

  static function extract_uri() {
    // $app_relative_path = /folder/that/lead/to/application/root
    $app_relative_path = substr(Application::$APP_PATH, strlen($_SERVER['DOCUMENT_ROOT']));
    $request_uri = substr($_SERVER['REQUEST_URI'], strlen($app_relative_path . '/' . Application::$PUB_DIR));

    return $request_uri;
  }
}
