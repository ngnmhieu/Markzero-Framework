<?php
/**
 * Central class of the system
 */
class App {
  static $APP_PATH;
  static $PUBLIC_DIR;
  static $CONTROLLERS_DIR;
  static $MODELS_DIR;
  static $VIEWS_DIR;
  static $db;
  static $session;

  static function bootstrap() {
    self::initialize();
    self::load_classes();
    self::$db = new Database();
    self::$session = new Session();

    Router::route();
  }

  private static function initialize() {
    # self::$APP_PATH = "/Users/hieusun/Work/programming/webserver/favopic/";
    self::$APP_PATH = realpath("../").'/';
    self::$CONTROLLERS_DIR = self::$APP_PATH."app/controllers/";
    self::$MODELS_DIR = self::$APP_PATH . "app/models/";
    self::$VIEWS_DIR = self::$APP_PATH . "app/views/";
    self::$PUBLIC_DIR = 'public/';
  }

  private static function load_classes() { 
    # Router finds and call the right controller and action for a specific uri
    require_once(self::$APP_PATH. "includes/router.php");
    # database class handle database connection
    require_once(self::$APP_PATH. "includes/database.php");
    # session class manage user session
    require_once(self::$APP_PATH. "includes/session.php");

    # Load base controller and model
    require_once(self::$APP_PATH. "app/base/AppController.php");
    require_once(self::$APP_PATH. "app/base/AppModel.php");
    
    # Load other models
    $model_dir = self::$MODELS_DIR;
    foreach (scandir($model_dir) as $file) {
      if (preg_match('/^[A-Z][a-z]*\.php$/', $file)) {
        require_once($model_dir . $file);
      }
    }

  }
}

?>
