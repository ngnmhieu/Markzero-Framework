<?php
namespace Markzero\Mvc;

use Markzero\App;
use Markzero\Mvc\View;
use Markzero\Http;

class AppController {
  private $request;
  private $response;

  /*
   * Every child class should call this constructor
   * if it has its own constructor
   * @param Markzero\Http\Request 
   * @param Markzero\Http\Response  
   */
  function __construct(Http\Request $request = null, Http\Response $response = null) {
    $this->request  = $request;
    $this->response = $response;
  }

  /**
   * @return Markzero\Http\Request Application Request object
   **/
  protected function request() { return $this->request; }

  /**
   * @return Markzero\Http\Response Application Response object
   **/
  protected function response() { return $this->response; }

  /**
   * Shorthand for $this->response->respond_to(...);
   * See Markzero\Http\Response#respond_to
   */
  protected function respond_to() {
    return call_user_func_array(array($this->response, 'respond_to'), func_get_args());
  }

  /**
   * Render a view using a concrete AbstractView object (HtmlView, JsonView, ...)
   * @param Markzero\Mvc\View\AbstractView
   * @return none
   */
  protected function render(View\AbstractView $view) {
    $this->response->setContent($view->getContent());
  }

  /**
   * @return string Name of current controller
   */
  protected function name() {
    preg_match("/([a-zA-Z]+)Controller/", get_class($this), $matches);
    $controller = strtolower($matches[1]);
    return $controller;
  } 
}
