<?php
/**
 * Encapsulate all informations about a request
 **/
class Request {
  private $get;
  private $post;

  function __construct() {
    $this->get = new Parameter($_GET);
    $this->post = new Parameter($_POST);
  }

  /**
   * @return Parameter $get
   **/
  public function get() {
    return $this->get;
  }

  /**
   * @return Parameter $post
   **/
  public function post() {
    return $this->post;
  }

  /**
   * more methods to come when needed
   **/
}

/**
 * 
 */
class Parameter {
  /**
   * array contains key-value pairs
   */
  private $params;

  public function __construct($params) {
    if (is_array($params)) {
      $this->params = $params; 
    }
  }

  /**
   * whitelist given parameters
   * @param array permitted keys
   */
  public function permit($keys) {
  }

  /**
   * @return $value if $key exists in (permitted) parameters
   *         null if $key does not exist or not permitted
   */
  public function val($key) {
    return isset($this->params[$key]) ? $this->params[$key] : null;
  }
}
