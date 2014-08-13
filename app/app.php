<?php
/**
 * Central class of the system
 */
class Application {
  static $APP_PATH;
  static $PUB_DIR = 'public';
  static $db;

  static function bootstrap() {
    self::$APP_PATH = realpath('../');

    self::load_classes();
    self::$db = new Database();

    Router::route();
  }

  private static function load_classes() { 
    // Router finds and call the right controller and action for a specific uri
    require_once(self::$APP_PATH. "/app/router.php");
    // database class handle database connection
    require_once(self::$APP_PATH. "/includes/database.php");

    // Load base controller and model
    require_once(self::$APP_PATH. "/app/base/AppController.php"); // hardcoding need to fix somehow
    require_once(self::$APP_PATH. "/app/base/AppModel.php");
    
    // Load other controllers
    $controller_dir = self::$APP_PATH . "/app/controllers/";
    foreach (scandir($controller_dir) as $file) {
      if (preg_match('/^[A-Z][a-z]*Controller\.php$/', $file)) {
        require_once($controller_dir . $file);
      }
    }

    // Load other models
    $model_dir = self::$APP_PATH . "/app/models/";
    foreach (scandir($model_dir) as $file) {
      if (preg_match('/^[A-Z][a-z]*\.php$/', $file)) {
        require_once($model_dir . $file);
      }
    }

  }
}

?>
