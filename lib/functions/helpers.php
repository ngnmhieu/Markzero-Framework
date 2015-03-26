<?php
use Markzero\App;
use Markzero\Session\Flash;

/**
 * flash the message with $key
 * @param string $key 
 * @param string $value
 */
function set_flash($key, $value) {
  $flasher = Flash::getInstance();
  return $flasher->set($key, $value);
}
/**
 * flash the message with $key
 * @param string $key 
 */
function flash($key) {
  $flasher = Flash::getInstance();
  return $flasher->get($key);
}

/**
 * check if flash message exist
 * @param string $key 
 */
function flash_exist($key) {
  $flasher = Flash::getInstance();
  return !!$flasher->get($key);
}

/**
 * selected attribute in tag-option for select list html
 * @return string | "selected" if $a $b are equal
 */
function selected($a, $b) {
  return $a == $b ? "selected" : "";
}

/**
 * @param string $route_id
 * @param array $params
 */
function webpath($route_id, array $params = array()) {
  $route_comp  = explode('#',$route_id);
  $controller = $route_comp[0];
  $action     = $route_comp[1];

  // return the first webpath found
  return App::$router->getWebpaths($controller, $action, $params)[0];
}
