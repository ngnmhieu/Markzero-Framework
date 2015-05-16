<?php
namespace Markzero\Mvc;

use Markzero\App;
use Markzero\Mvc\View;
use Markzero\Http;

class AppController {
  /**
   * @var Markzero\Http\Request
   */
  private $request;
  /**
   * @var Markzero\Http\Response
   */
  private $response;
  /**
   * @var Markzero\Http\session
   */
  private $session;

  /*
   * Every child class should call this constructor
   * if it has its own constructor
   * @param Markzero\Http\Request 
   * @param Markzero\Http\Response  
   */
  function __construct(Http\Request $request = null, Http\Response $response = null, Http\Session $session = null) {
    $this->request  = $request;
    $this->response = $response;
    $this->session  = $session;
  }

  /**
   * @return Markzero\Http\Request Application Request object
   * @deprecated
   **/
  protected function request() { return $this->request; }
  /**
   * @return Markzero\Http\Response Application Response object
   * @deprecated
   **/
  protected function response() { return $this->response; }

  /**
   * @return Markzero\Http\Request Application Request object
   **/
  protected function getRequest() { return $this->request; }

  /**
   * @return Markzero\Http\Response Application Response object
   **/
  protected function getResponse() { return $this->response; }

  /**
   * @return Markzero\Http\Session Application Session object
   **/
  protected function getSession() { return $this->session; }

  /**
   * Shorthand for $this->response->respondTo(...);
   * See Markzero\Http\Response#respondTo
   */
  protected function respondTo() {
    return call_user_func_array(array($this->response, 'respondTo'), func_get_args());
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
