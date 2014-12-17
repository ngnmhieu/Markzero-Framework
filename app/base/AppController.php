<?php
class AppController {
  private $default_layout;
  private $request;
  private $response;

  /*
   * Every child class should call this constructor
   * if it has its own constructor
   */
  function __construct() {
    $this->request = new Request();
    $this->response= new Response();
  }

  /**
   * @return Request
   **/
  public function request() { return $this->request; }

  /**
   * @return Response
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

  /**
   * render template with a layout (if given)
   * @param array $data variables to be populated into template
   * @param string $template default to {action_name}
   * @param string $layout default to "layouts/default"
   **/   
  // protected function render(array $data = array(), $template = "", $layout = null) {
  //   // template default to action name
  //   if ($template == "")  {
  //     list(,$caller) = debug_backtrace(false); // `false` for performance php5.3
  //     $method = $caller['function'];
  //     $template = $method;
  //   }
  //   
  //   $controller = $this->name();

  //   // set default layout if not provided
  //   if (empty($layout)) {
  //     $layout = $this->default_layout;
  //   }

  //   $this->view->render($data, $controller.'/'.$template, $layout);
  // }


  /*
   * default layout for current controller
   * @param string $layout name of layout (without file extension)
   */
  // protected function set_default_layout($layout) {
  //   if (is_string($layout) && $layout != "") {
  //     $this->default_layout = $layout;
  //   }
  // }

}
