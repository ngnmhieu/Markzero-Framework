<?php
namespace Markzero\Session;

/**
 * Flash keep messages between 2 request
 * usually used for error, notice, success messages
 */
class Flash {
  private $keep; // [bool] Keep old flash messages to next request or not

  /**
   * Return the Singleton instance Flash of the class
   */
  public static function getInstance() {
    static $instance = null;

    if ($instance === null) {
      $instance = new static();
    }

    return $instance;
  }

  /**
   * constructor could only be called by getInstance()
   */
  protected function __construct() {
    $this->keep = false;
  }

  /**
   * keep old flash messages to next request
   */
  public function keep() {
    $this->keep = true;
  }

  /**
   * get flash message of a particular type
   * @param string $key | type of flash (error, warning, success ...)
   * @return string | message to be flashed
   */
  public function get($key) {
    $flash_key = 'Flash.old.'.$key;

    return !empty($_SESSION[$flash_key]) ? $_SESSION[$flash_key] : '';
  }

  public function set($key, $value) {
    $flash_key = 'Flash.new.'.$key;
    $_SESSION[$flash_key] = $value;
  }

  function __destruct() {
    // remove all old flash messages from last request
    if (!$this->keep) {
      foreach ($_SESSION as $key => $value)
        if (preg_match("~^Flash\.old\..*$~",$key))
          unset($_SESSION[$key]);
    }

    // prepare flash messages for next request
    foreach ($_SESSION as $key => $value)
      if (preg_match("~^Flash\.new\.(.*)$~",$key, $matches)) {
        $_SESSION['Flash.old.'.$matches[1]] = $value;
        unset($_SESSION['Flash.new.'.$matches[1]]);
      }
  }
}
