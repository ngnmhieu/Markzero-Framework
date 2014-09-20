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

  static $db; // database connection
  static $session; // manage user sessions
  static $router; // handling
  static $data; // store static data of the application

  /*
   * This method has these responsibilities:
   * - initializing constants
   * - loading classes
   * - loading configurations
   * - and dispatch the request from client
   */
  static function bootstrap() {
    self::initialize();
    self::load_classes();
    self::$session = new Session();
    self::$router = new Router();
    // TODO: input developement or production in app.php
    $db = App::$data->database->development;
    self::$db = new Database($db->user, $db->host, $db->pass, $db->dbname);
    
    self::load_config();

    self::$router->dispatch();
  }

  private static function initialize() {
    self::$APP_PATH = realpath("../").'/';
    self::$CONTROLLER_DIR = self::$APP_PATH."app/controllers";
    self::$MODEL_DIR = self::$APP_PATH."app/models";
    self::$VIEW_DIR = self::$APP_PATH."app/views";
    self::$PUBLIC = self::$APP_PATH."public/";
  }

  private static function load_config() {
    // base.php defines neccessary constants
    require_once(self::$APP_PATH. "config/base.php");
    // routes.php defines routes for the router
    require_once(self::$APP_PATH. "config/routes.php");
  }

  private static function load_classes() { 
    // Router finds and call the right controller and action for a specific uri
    require_once(self::$APP_PATH. "includes/router.php");
    // database class handle database connection
    require_once(self::$APP_PATH. "includes/database.php");
    // session class manage user session
    require_once(self::$APP_PATH. "includes/session.php");
    // static data class keep all static data in one places
    require_once(self::$APP_PATH. "includes/static_data.php");

    // Load base controller, model and view
    require_once(self::$APP_PATH. "app/base/AppController.php");
    require_once(self::$APP_PATH. "app/base/AppModel.php");
    require_once(self::$APP_PATH. "app/base/AppView.php");
    
    // Load other models
    $model_dir = self::$MODEL_DIR;
    foreach (scandir($model_dir) as $file) {
      if (preg_match('/^[A-Z][a-z]*\.php$/', $file)) {
        require_once($model_dir.'/'.$file);
      }
    }

    // autoload third-party libraries
    require_once(self::$APP_PATH. "vendor/autoload.php");

    // load required data
    $data_dir = self::$APP_PATH."data";
    foreach (scandir($data_dir) as $file) {
      if (preg_match('/^.*\.php$/', $file)) {
        require_once($data_dir.'/'.$file);
      }
    }

    self::load_static_data();

  }

  /* 
   * Load static data like database connections, api keys,...
   * Data are in yaml formats
   * TODO: should there be nested data?
   */
  private static function load_static_data() {
    $data_dir = self::$APP_PATH."data";
    self::$data = new StaticData($data_dir);
  }   

}

?>
