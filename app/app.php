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

    // Load base controllers
    require_once(self::$APP_PATH. "/app/base/ApplicationController.php"); // hardcoding need to fix somehow
    
    // Load other controllers
    $controller_dir = self::$APP_PATH . "/app/controllers/";
    foreach (scandir($controller_dir) as $file) {
      if (preg_match('/^[A-Z][a-z]*Controller\.php$/', $file)) {
        require_once($controller_dir . $file);
      }
    }
  }
}

?>
