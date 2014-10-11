<?php
/**
 * Central class of the system
 */
class App {
  static $APP_PATH;
  static $PUBLIC;
  static $CONTROLLER_DIR;
  static $MODEL_DIR;
  static $VIEW_DIR;

  static $config; // application configurations
  static $session; // manage user sessions
  static $router; // handling
  static $data; // store static data of the application
  static $entity_manager; // Doctrine EntityManager

  /*
   * This method has these responsibilities:
   * - initializing constants
   * - loading classes
   * - loading configurations
   * - and dispatch the request from client
   */
  static function bootstrap() {
    self::init_path();
    self::load_classes();
    self::load_config();
    self::load_static_data();
    self::init_classes();
    self::load_routes();
  }

  /*
   * initializes the paths in the application
   */
  private static function init_path() {
    $parent_dir = dirname(dirname(__FILE__));
    self::$APP_PATH       = $parent_dir.'/';
    self::$CONTROLLER_DIR = self::$APP_PATH."app/controllers";
    self::$MODEL_DIR      = self::$APP_PATH."app/models";
    self::$VIEW_DIR       = self::$APP_PATH."app/views";
    self::$PUBLIC         = self::$APP_PATH."public/";
  }

  /*
   * loads important classes for the application
   * like Session, Router, Database,...
   */
  private static function init_classes() {
    self::$session = new Session();
    self::$router = new Router();
  }

  /*
   * loads application and environment specific configuration.
   * configurations are located in config/ directory.
   * important configurations are among others: application wide config, database,...
   */
  private static function load_config() {
    // global application configurations
    $config_dir = self::$APP_PATH."config";
    self::$config = new StaticData($config_dir);
    // database configurations
    require_once(self::$APP_PATH. "includes/database.php");
  }

  /*
   * routes.php defines routes for the Router
   */
  private static function load_routes() {
    require_once(self::$APP_PATH. "config/routes.php");
  }

  /*
   * loads files that contain important classes
   */
  private static function load_classes() { 
    // autoload third-party libraries
    require_once(self::$APP_PATH. "vendor/autoload.php");
    // request class encapsulate all the information about the current request
    require_once(self::$APP_PATH. "includes/Request.class.php");
    // router finds and call the right controller and action for a specific uri
    require_once(self::$APP_PATH. "includes/Router.class.php");
    // session class manage user session
    require_once(self::$APP_PATH. "includes/Session.class.php");
    // static data class keep all static data in one places
    require_once(self::$APP_PATH. "includes/StaticData.class.php");

    // load base controller, model and view
    require_once(self::$APP_PATH. "app/base/AppController.php");
    require_once(self::$APP_PATH. "app/base/AppModel.php");
    require_once(self::$APP_PATH. "app/base/AppView.php");
    
    // load other models
    $model_dir = self::$MODEL_DIR;
    foreach (scandir($model_dir) as $file) {
      if (preg_match('/^[A-Z][a-z]*\.php$/', $file)) {
        require_once($model_dir.'/'.$file);
      }
    }
  }

  /* 
   * load application specific static data 
   * data is in json formats
   */
  private static function load_static_data() {
    $data_dir = self::$APP_PATH."data";
    self::$data = new StaticData($data_dir);
  }   

}

?>
