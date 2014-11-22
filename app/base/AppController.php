<?php
class AppController {
  private $appview;
  private $default_layout;
  private $view_subdir;
  private $request;

  /*
   * Every child class should call this constructor
   * if it has its own constructor
   */
  function __construct() {
    $this->request = new Request();
    $this->appview = new AppView(App::$VIEW_DIR);
    $this->set_view_subdir("");
    $this->set_default_layout("default");
  }

  /**
   * @return object contains request information
   **/
  public function request() {
    return $this->request;
  }

  /*
   * in which subdirectory the views are stored
   * used for controllers lies in subdirectory like admin, ...
   * @param string $dir 
   */
  public function set_view_subdir($dir) {
    $this->view_subdir = $dir;
  }

  /*
   * default layout for current controller
   * @param string $layout name of layout (without file extension)
   */
  protected function set_default_layout($layout) {
    if (is_string($layout) && $layout != "") {
      $this->default_layout = $layout;
    }
  }

  /**
   * render template with a layout (if given)
   * @param array $data variables to be populated into template
   * @param string $template default to {action_name}
   * @param string $layout default to "layouts/default"
   **/   
  protected function render(array $data = array(), $template = "", $layout = null) {
    // template default to action name
    if ($template == "")  {
      list(,$caller) = debug_backtrace(false); // `false` for performance php5.3
      $method = $caller['function'];
      $template = $method;
    }
    
    $controller = $this->current_controller();

    // set default layout if not provided
    if (empty($layout)) {
      $layout = $this->default_layout;
    }

    $this->appview->render($data, $this->view_subdir.$controller.'/'.$template, $layout);
  }

  /**
   * @return string name of current controller
   */
  private function current_controller() {
    preg_match("/([a-zA-Z]+)Controller/", get_class($this), $matches);
    $controller = strtolower($matches[1]);
    return $controller;
  } 

  /**
   * redirect to a specific controller action with (optional) parameters
   */
  protected function redirect(array $to = array(), array $params = array()) {
    if (!isset($to['controller']) && !isset($to['action'])) {
      die("No controller specified. Don't know where to redirect.");
    }

    $controller = isset($to['controller']) ? strtolower($to['controller']) : $this->current_controller(); 
    $action = isset($to['action']) ? strtolower($to['action']) : "index";
    $router = Router::getInstance();
    $path_name = "{$controller}_{$action}";
    $location = $router->get_web_path($path_name, $params);
    header('Location: '.$location);  
  }

  /**
   * set a flash message for next request (and only the next request)
   * @param $key     | key for future retrieval 
   * @param $message | message to be flashed
   */
  protected function flash($key, $message) {
    $flasher = \Session\Flash::getInstance();
    $flasher->set($key, $message);
  } 

}
