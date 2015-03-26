<?php
namespace Markzero;

use Markzero\Session;
use Markzero\Http;

/**
 * Central class of the system
 */
class App {
  static $APP_PATH;
  static $CORE_PATH;
  static $CONTROLLER_PATH;
  static $MODEL_PATH;
  static $VIEW_PATH;
  static $PUBLIC_PATH;

  static $request;
  static $response;
  static $config;   // application configurations
  static $session;  // manage user sessions
  static $router;   // handling
  static $data;     // store static data of the application
  static $em;       // Doctrine EntityManager

  /**
   * This method has these responsibilities:
   * - initializing constants
   * - loading classes
   * - loading configurations
   * - and dispatch the request from client
   */
  static function bootstrap() {
    self::initPath();
    self::loadClasses();
    self::initClasses();
    self::loadConfig();
    self::loadFunctions();
    self::loadRoutes();
    self::loadStaticData();
  }

  /**
   * Initializes the paths in the application
   */
  private static function initPath() {
    $parent_dir = dirname(dirname(__FILE__));
    self::$APP_PATH       = $parent_dir.'/';
    self::$CORE_PATH       = self::$APP_PATH."core/";
    self::$CONTROLLER_PATH = self::$APP_PATH."app/controllers/";
    self::$MODEL_PATH      = self::$APP_PATH."app/models/";
    self::$VIEW_PATH       = self::$APP_PATH."app/views/";
    self::$PUBLIC_PATH     = self::$APP_PATH."public/";
  }

  /**
   * Load system functions, helper functions ...
   */
  private static function loadFunctions() {
    require_once(self::$CORE_PATH."lib/functions/functions.php");
    require_once(self::$CORE_PATH."lib/functions/helpers.php");
  }

  /**
   * Loads important classes for the application
   * like Session, Router, Database,...
   */
  private static function initClasses() {
    self::$session  = new Session\Session();
    self::$router   = new Http\Router();
    self::$request  = new Http\Request();
    self::$response = new Http\Response(self::$request, self::$router);

    self::$router->setRequest(self::$request);
    self::$router->setResponse(self::$response);
  }

  /**
   * Loads application and environment specific configuration.
   * configurations are located in config/ directory.
   * important configurations are among others: application wide config, database,...
   */
  private static function loadConfig() {
    // Global application configurations
    self::$config = new StaticData(self::$APP_PATH."config/");

    // Configurations
    require_once(self::$APP_PATH."config/config.php");

    // Initializers, load all files in initializers directory
    $initializers_path = self::$APP_PATH."config/initializers/"; 
    foreach (scandir($initializers_path) as $file) {
      if (preg_match('/^[A-Za-z_\-.]+\.php$/', $file)) {
        require_once($initializers_path.$file);
      }
    }

    // Database configurations
    require_once(self::$CORE_PATH."database.php");
  }

  /**
   * routes.php defines routes for the Router
   */
  private static function loadRoutes() {
    require_once(self::$APP_PATH. "config/routes.php");
  }

  /**
   * Loads files that contain important classes
   */
  private static function loadClasses() { 
    // autoload third-party libraries for the framework
    require_once(self::$CORE_PATH. "vendor/autoload.php");
    // request class encapsulate all the information about the current request
    require_once(self::$CORE_PATH. "lib/classes/http/Request.php");
    // request class encapsulate all the information about the current request
    require_once(self::$CORE_PATH. "lib/classes/http/HasHttpStatusCode.interface.php");
    // request class encapsulate all the information about the current request
    require_once(self::$CORE_PATH. "lib/classes/http/Response.php");
    // router finds and call the right controller and action for a specific uri
    require_once(self::$CORE_PATH. "lib/classes/http/Router.php");
    // static data class keep all static data in one places
    require_once(self::$CORE_PATH. "lib/classes/StaticData.class.php");
    // session class manage user session
    require_once(self::$CORE_PATH. "lib/classes/session/Session.class.php");
    // manages flash messages
    require_once(self::$CORE_PATH. "lib/classes/session/Flash.class.php");
    // autoload third-party libraries for the application
    require_once(self::$APP_PATH. "vendor/autoload.php");

    // load base controller, model and view
    require_once(self::$CORE_PATH. "mvc/AppController.php");
    require_once(self::$CORE_PATH. "mvc/AppModel.php");

    // Load AbstractView and it's concrete implementation
    $views_dir = self::$CORE_PATH. "mvc/view/"; 
    foreach (scandir($views_dir) as $file) {
      if (preg_match('/^[A-Z][A-Za-z_\-.]*\.php$/', $file)) {
        require_once($views_dir.$file);
      }
    }

    // Validation facilities
    require_once(self::$CORE_PATH. "lib/classes/validation/ValidationManger.class.php");
    // Load all validators
    $validators_dir = self::$CORE_PATH. "lib/classes/validation/validators/"; 
    foreach (scandir($validators_dir) as $file) {
      if (preg_match('/^[A-Z][A-Za-z_\-.]*\.php$/', $file)) {
        require_once($validators_dir.$file);
      }
    }

    // Load all Exception classes
    $exceptions_dirs = array();
    $exceptions_dirs[] = self::$CORE_PATH. "lib/classes/exception/"; 
    $exceptions_dirs[] = self::$CORE_PATH. "lib/classes/auth/exception/"; 
    $exceptions_dirs[] = self::$CORE_PATH. "lib/classes/validation/exception/"; 
    $exceptions_dirs[] = self::$CORE_PATH. "lib/classes/http/exception/"; 
    foreach ($exceptions_dirs as $dir) {
      foreach (scandir($dir) as $file) {
        if (preg_match('/^[A-Z][A-Za-z_\-.]*\.php$/', $file)) {
          require_once($dir.$file);
        }
      }
    }

    // load other models
    $model_dir = self::$MODEL_PATH;
    foreach (scandir($model_dir) as $file) {
      if (preg_match('/^[A-Z][A-Za-z_\-.]*\.php$/', $file)) {
        require_once($model_dir.'/'.$file);
      }
    }

  }

  /* 
   * Load application-specific static data 
   * data is in json formats
   */
  private static function loadStaticData() {
    $data_dir = self::$APP_PATH."data";
    self::$data = new StaticData($data_dir);
  }   

}

?>
