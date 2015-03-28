<?php
namespace Markzero\Session;

/**
 * Manage web application Session
 */
class Session {

  function __construct() {
    session_start();
  }

  /**
   * return the original $_SESSION array
   */
  public function all() {
    return $_SESSION;
  }

  /**
   * @return boolean check existence of $_SESSION
   */
  public function exist($key) {
    return !empty($_SESSION[$key]);
  }

  /**
   * get session value with $key, if not exist return NULL
   */
  public function get($key) {
    return $this->exist($key) ? $_SESSION[$key] : NULL;
  }

  /**
   * set value for $_SESSION[$key]
   */
  public function set($key, $val) {
    $_SESSION[$key] = $val;
  }

  /**
   * unset a key in session
   */
  public function remove($key) {
    unset($_SESSION[$key]);
  }
}
