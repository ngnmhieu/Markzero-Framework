<?php
/**
 * Central class of the system
 */
class Application {
  // TODO: should we hardcode application path like this, 
  //       or initialize in bootstrap static method?
  static $APP_PATH;
  static $PUBLIC_DIR;
  static $CONTROLLERS_DIR;
  static $db;

  static function bootstrap() {
    self::initialize();
    self::load_classes();
    self::$db = new Database();

    Router::route();
  }

  private static function initialize() {
    // self::$APP_PATH = realpath("../");
    self::$APP_PATH = "/Users/hieusun/Work/programming/webserver/favopic/";
    self::$CONTROLLERS_DIR = self::$APP_PATH."app/controllers/";
    self::$PUBLIC_DIR = 'public';
  }

  private static function load_classes() { 
    // Router finds and call the right controller and action for a specific uri
    require_once(self::$APP_PATH. "app/router.php");
    // database class handle database connection
    require_once(self::$APP_PATH. "includes/database.php");

    // Load base controller and model
    require_once(self::$APP_PATH. "app/base/AppController.php");
    require_once(self::$APP_PATH. "app/base/AppModel.php");
    
    // Load other models
    $model_dir = self::$APP_PATH . "app/models/";
    foreach (scandir($model_dir) as $file) {
      if (preg_match('/^[A-Z][a-z]*\.php$/', $file)) {
        require_once($model_dir . $file);
      }
    }

    // Load other controllers
    // TODO: should we load the controllers all at once?
    //       or should we load at dispatch
    // $controller_dir = self::$APP_PATH . "app/controllers/";
    // foreach (scandir($controller_dir) as $file) {
    //   if (preg_match('/^[A-Z][a-z]*Controller\.php$/', $file)) {
    //     require_once($controller_dir . $file);
    //   }
    // }

  }
}

?>
