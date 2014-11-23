<?php
/**
 * flash the message with $key
 * @param string $key 
 */
function flash($key) {
  $flasher = \Session\Flash::getInstance();
  return $flasher->get($key);
}

/**
 * check if flash message exist
 * @param string $key 
 */
function flash_exist($key) {
  $flasher = \Session\Flash::getInstance();
  return !!$flasher->get($key);
}

/**
 * selected attribute in tag-option for select list html
 * @return string | "selected" if $a $b are equal
 */
function selected($a, $b) {
  return $a == $b ? "selected" : "";
}

// should be flash for array

/**
 * @param string $path_name
 * @param array $params
 */
function path($path_name, array $params = array()) {
  $router = Router::getInstance(); 
  return $router->get_web_path($path_name, $params);
}

function redirect(array $to = array(), array $params = array()) {
  if (!!$to['controller']) {
    die("Controller must be provided!");
  }

  $controller = strtolower($to['controller']);
  $action = isset($to['action']) ? strtolower($to['action']) : "index";
  $router = Router::getInstance();
  $path_name = "{$controller}_{$action}";
  $location = $router->get_web_path($path_name, $params);
  header('Location: '.$location);  
}


