<?php
/**
 * Represent a HTTP Request
 **/
class Request {
  private $get;
  private $post;

  function __construct() {
    $this->get  = $_GET;
    $this->post = $_POST;
  }

  /**
   * @param $permit | whitelist of permitted keys
   * @return array
   **/
  public function getParams(array $permit = array()) {
    return $this->get;
  }

  /**
   * @param $permit | whitelist of permitted keys
   * @return array
   **/
  public function postParams(array $permit = array()) {
    return $this->post;
  }

}
