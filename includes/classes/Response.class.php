<?php
/**
 * Encapsulate all informations about a response
 **/
class Response {

  /**
   */
  public function redirect(array $to = array(), array $params = array()) {
    if (!$to['controller']) {
      die("Controller must be provided!");
    }

    $controller = strtolower($to['controller']);
    $action = isset($to['action']) ? strtolower($to['action']) : "index";

    $path_name = "{$controller}_{$action}";
    $location = App::$router->get_web_path($path_name, $params);
    header('Location: '.$location);  
  }
}
