<?php
use Symfony\Component\HttpFoundation;

/**
 * Represent a HTTP Request
 **/
class Request {
  private $http_request;

  function __construct() {
    $this->http_request = new HttpFoundation\Request(
      $_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER
    );
  }

  /**
   * Delegate undefined methods to HttpFoundation\Request object
   */
  function __call($method, $args) {
    return call_user_func_array(array($this->http_request, $method), $args);
  }

  /**
   * Delegate undefined attributes to HttpFoundation\Request object
   */
  function __get($attribute) {
    return $this->http_request->$attribute;
  }
}
