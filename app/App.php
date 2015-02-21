<?php
/**
 * Central class of the system
 */
class App {
  static $APP_PATH;
  static $PUBLIC_DIR;
  static $CONTROLLER_DIR;
  static $MODEL_DIR;
  static $VIEW_DIR;

  static $view;
  static $request;
  static $response;
  static $config;  // application configurations
  static $session; // manage user sessions
  static $router;  // handling
  static $data;    // store static data of the application
  static $entity_manager; // Doctrine EntityManager

  /*
   * This method has these responsibilities:
   * - initializing constants
   * - loading classes
   * - loading configurations
   * - and dispatch the request from client
   */
  static function bootstrap() {
    self::initPath();
    self::loadClasses();
    self::loadConfig();
    self::initClasses();
    self::loadFunctions();
    self::loadRoutes();
    self::loadStaticData();
  }

  /**
   * end of a request, some clean up have to be done 
   * remove flash messages
   */
   /* 
     static function cleanup() {
     }
   */

  /*
   * initializes the paths in the application
   */
  private static function initPath() {
    $parent_dir = dirname(dirname(__FILE__));
    self::$APP_PATH       = $parent_dir.'/';
    self::$CONTROLLER_DIR = self::$APP_PATH."app/controllers";
    self::$MODEL_DIR      = self::$APP_PATH."app/models";
    self::$VIEW_DIR       = self::$APP_PATH."app/views";
    self::$PUBLIC_DIR     = self::$APP_PATH."public/";
  }

  /**
   * load system functions, helper functions ...
   */
  private static function loadFunctions() {
    require_once(self::$APP_PATH."includes/functions/functions.php");
    require_once(self::$APP_PATH."includes/functions/helpers.php");
  }
  /*
   * loads important classes for the application
   * like Session, Router, Database,...
   */
  private static function initClasses() {
    self::$view     = new AppView(self::$VIEW_DIR);
    self::$session  = new Session();
    self::$request  = new Request();
    self::$response = new Response(self::$request);
    self::$router   = new Router(self::$response);
  }

  /*
   * loads application and environment specific configuration.
   * configurations are located in config/ directory.
   * important configurations are among others: application wide config, database,...
   */
  private static function loadConfig() {
    // global application configurations
    self::$config = new StaticData(self::$APP_PATH."config/");
    // base configurations
    require_once(self::$APP_PATH."config/base.php");
    // database configurations
    require_once(self::$APP_PATH. "includes/database.php");
  }

  /*
   * routes.php defines routes for the Router
   */
  private static function loadRoutes() {
    require_once(self::$APP_PATH. "config/routes.php");
  }

  /*
   * loads files that contain important classes
   */
  private static function loadClasses() { 
    // autoload third-party libraries
    require_once(self::$APP_PATH. "vendor/autoload.php");
    // request class encapsulate all the information about the current request
    require_once(self::$APP_PATH. "includes/classes/http/Request.class.php");
    // request class encapsulate all the information about the current request
    require_once(self::$APP_PATH. "includes/classes/http/HasHttpStatusCode.interface.php");
    // request class encapsulate all the information about the current request
    require_once(self::$APP_PATH. "includes/classes/http/Response.class.php");
    // router finds and call the right controller and action for a specific uri
    require_once(self::$APP_PATH. "includes/classes/Router.class.php");
    // static data class keep all static data in one places
    require_once(self::$APP_PATH. "includes/classes/StaticData.class.php");
    // session class manage user session
    require_once(self::$APP_PATH. "includes/classes/session/Session.class.php");
    // manages flash messages
    require_once(self::$APP_PATH. "includes/classes/session/Flash.class.php");

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
   * load application-specific static data 
   * data is in json formats
   */
  private static function loadStaticData() {
    $data_dir = self::$APP_PATH."data";
    self::$data = new StaticData($data_dir);
  }   

}

?>
