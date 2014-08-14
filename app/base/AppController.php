<?php
class AppController {
  protected function render($template = "") {
    // TODO: sanitize $template, need it?

    // default to method name as template name
    if ($template == "")  {
      list(,$caller) = debug_backtrace(false); // false for performance php5.3
      $method = $caller['function'];
      $template = $method;
    }

    // get controller name
    $matches = array();
    preg_match("/([a-zA-Z]+)Controller/",get_called_class(), $matches);
    $controller = strtolower($matches[1]);

    $template_file = Application::$VIEWS_DIR.$controller.'/'.$template.".tpl.php";
    if (file_exists($template_file)) {
      include($template_file);
    } else {
      // TODO: Template not found error
      die("Template file not found: " . $template_file);
    }
  }
  
  protected function redirect($to = array()) {
    if (isset($to['controller'])) {
      $action = isset($to['action']) ? $to['action'] : "index";
      $location = '/'.strtolower($to['controller']).'/'.$action;
      header('Location: '.$location);  
    } else {
      die("No controller specified. Don't know where to redirect");
    }
  }

}
