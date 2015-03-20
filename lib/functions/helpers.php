<?php
/**
 * flash the message with $key
 * @param string $key 
 * @param string $value
 */
function set_flash($key, $value) {
  $flasher = \Session\Flash::getInstance();
  return $flasher->set($key, $value);
}
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

/**
 * @param string $path_name
 * @param array $params
 */
function webpath($path_name, array $params = array()) {
  $path_comp  = explode('#',$path_name);
  $controller = $path_comp[0];
  $action     = $path_comp[1];

  // return the first webpath found
  return App::$router->getWebPath($controller, $action, $params)[0];
}
