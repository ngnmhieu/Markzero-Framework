<?php
class AppController {
  private $request;
  private $response;

  /*
   * Every child class should call this constructor
   * if it has its own constructor
   */
  function __construct() {
    $this->request  = App::$request;
    $this->response = App::$response;
    $this->view     = App::$view;  
  }

  /**
   * @return Request | Application Request object
   **/
  public function request() { return $this->request; }

  /**
   * @return Response | Application Request object
   **/
  public function response() { return $this->response; }

  /**
   * @return string | name of current controller
   */
  protected function name() {
    preg_match("/([a-zA-Z]+)Controller/", get_class($this), $matches);
    $controller = strtolower($matches[1]);
    return $controller;
  } 
}
