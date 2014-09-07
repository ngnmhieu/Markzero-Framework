<?php
class AppController {
  protected function render($template = "") {
    // TODO: sanitize $template, need it?

    // default to method name as template name
    if ($template == "")  {
      list(,$caller) = debug_backtrace(false); // `false` for performance php5.3
      $method = $caller['function'];
      $template = $method;
    }

    // get controller name
    $matches = array();
    preg_match("/([a-zA-Z]+)Controller/",get_called_class(), $matches);
    $controller = strtolower($matches[1]);

    $template_file = App::$VIEWS_DIR.$controller.'/'.$template.".tpl.php";
    if (file_exists($template_file)) {
      include($template_file);
    } else {
      // TODO: Template not found error
      die("Template file not found: " . $template_file);
    }
  }

  protected function current_controller() {
    preg_match("/([a-zA-Z]+)Controller/", get_class($this), $matches);
    $controller = strtolower($matches[1]);
    return $controller;
  } 

  protected function redirect($to = array()) {
    if (!isset($to['controller']) && !isset($to['action'])) {
      die("No controller specified. Don't know where to redirect");
    }

    $controller = isset($to['controller']) ? strtolower($to['controller']) : $this->current_controller(); 
    $action = isset($to['action']) ? strtolower($to['action']) : "index";
    $location = "/$controller/$action";
    header('Location: '.$location);  
  }

}
