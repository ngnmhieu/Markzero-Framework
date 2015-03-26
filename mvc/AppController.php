<?php
namespace Markzero\Mvc;

use Markzero\App;
use Markzero\Mvc\View;

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
  }

  /**
   * @return Request | Application Request object
   **/
  protected function request() { return $this->request; }

  /**
   * @return Response | Application Request object
   **/
  protected function response() { return $this->response; }

  /**
   * Shorthand for $this->response->respond_to(...);
   */
  protected function respond_to() {
    return call_user_func_array(array($this->response, 'respond_to'), func_get_args());
  }

  /**
   * Render a view using a concrete AbstractView object (HtmlView, JsonView, ...)
   * @param Markzero\Mvc\View\AbstractView
   */
  protected function render(View\AbstractView $view) {
    $this->response->setContent($view->getContent());
  }

  /**
   * @return string | name of current controller
   */
  protected function name() {
    preg_match("/([a-zA-Z]+)Controller/", get_class($this), $matches);
    $controller = strtolower($matches[1]);
    return $controller;
  } 
}
