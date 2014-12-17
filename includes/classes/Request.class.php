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
   * @param @permitted_keys array permitted keys
   */
  public function permit($permitted_keys) {
    $whitelist = array();
    foreach ($this->params as $key => $value) {
      if (in_array($key, $permitted_keys)) 
        $whitelist[$key] = $value;
    }
    $this->params = $whitelist;
  
    return $this;
  }

  /**
   * @return $value if $key exists in (permitted) parameters
   *         null if $key does not exist or not permitted
   */
  public function val($key) {
    if (isset($this->params[$key])) {
      $parameter = $this->params[$key];
      return is_array($parameter) ? new Parameter($parameter) : $parameter;
    }

    return null;
  }
}
