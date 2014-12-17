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

// should be flash for array

/**
 * @param string $path_name
 * @param array $params
 */
function path($path_name, array $params = array()) {
  return App::$router->get_web_path($path_name, $params);
}
