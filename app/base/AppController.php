<?php
class AppController {
  private $_appview;
  /*
   * Every child class should call this constructor
   * if it has its own constructor
   */
  function __construct() {
    $this->_appview = new AppView();
  }

  /*
   * render template with a layout (if given)
   * @param array $data variables to be populated into template
   * @param string $template default to {action_name}
   * @param strign $layout default to "layouts/default"
   * TODO: forced default layout or let user decide default layout?
   */   
  protected function render($data = array(), $template = "", $layout = "layouts/default") {
    // template default to action name
    if ($template == "")  {
      list(,$caller) = debug_backtrace(false); // `false` for performance php5.3
      $method = $caller['function'];
      $template = $method;
    }
    
    $controller = $this->current_controller();

    $this->_appview->render($data, $controller.'/'.$template, $layout);
  }

  private function current_controller() {
    preg_match("/([a-zA-Z]+)Controller/", get_class($this), $matches);
    $controller = strtolower($matches[1]);
    return $controller;
  } 

  protected function redirect($to = array()) {
    if (!isset($to['controller']) && !isset($to['action'])) {
      die("No controller specified. Don't know where to redirect.");
    }

    $controller = isset($to['controller']) ? strtolower($to['controller']) : $this->current_controller(); 
    $action = isset($to['action']) ? strtolower($to['action']) : "index";
    $location = "/$controller/$action";
    header('Location: '.$location);  
  }

}
