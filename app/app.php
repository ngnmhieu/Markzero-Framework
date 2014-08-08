<?php
/**
 * Central class of the system
 */
class Application {
  static $APP_PATH;
  static $PUB_DIR = 'public';
  static function bootstrap() {
    self::load_classes();
    Router::route();
  }

  private static function load_classes() { 
    self::$APP_PATH = realpath('../');
    require_once(self::$APP_PATH. "/app/router.php");

    require_once(self::$APP_PATH. "/config/database.php");

    // Load base controller and model
    require_once(self::$APP_PATH. "/app/base/AppController.php"); // hardcoding need to fix somehow
    require_once(self::$APP_PATH. "/app/base/AppModel.php"); // hardcoding need to fix somehow
    
    // Load other controllers
    $controller_dir = self::$APP_PATH . "/app/controllers/";
    foreach (scandir($controller_dir) as $file) {
      if (preg_match('/^[A-Z][a-z]*Controller\.php$/', $file)) {
        require_once($controller_dir . $file);
      }
    }

    // Load other models
  }
}

?>
