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

    // directory containing views
    $views_dir = Application::$APP_PATH."app/views/";

    // get controller name
    $matches = array();
    preg_match("/([a-zA-Z]+)Controller/",get_called_class(), $matches);
    $controller = strtolower($matches[1]);

    $template_file = $views_dir.$controller.'/'.$template.".tpl.php";
    if (file_exists($template_file)) {
      include($template_file);
    } else {
      // TODO: Template not found error
      die("Template file not found: " . $template_file);
    }
  }
}
