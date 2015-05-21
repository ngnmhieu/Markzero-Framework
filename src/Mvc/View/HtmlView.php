<?php
namespace Markzero\Mvc\View;

class HtmlView extends AbstractView {
  private $view_path;
  private static $DEFAULT_VIEW_PATH = null; 
  private $data;

  /**
   * @param array  $data Data to be populated, array('variable_name' => `value`, ...)
   * @param string $name Name of the view e.g 'book/index'
   * @param string $views_path Directory contains all views and layout
   * @throw Exception If no view path is specified
   *          either through HtmlView::setDefaultViewPath
   *          or through the constructor
   */
  function __construct(array $data, $name = null, $view_path = null) {

    // save the data (variables) that would be populated in the templates
    $this->data = $data;

    if ($view_path === null) {
      $this->view_path = self::$DEFAULT_VIEW_PATH;
    } else {
      $this->view_path = $view_path;
    }

    if ($this->view_path === null) {
      throw new Exception("No view path is specified. Please specify it with either HtmlView::setDefaultViewPath or through HtmlView constructor.");
    }

    $this->setContent($this->render($name));
  }

  /**
   * Set default view path, where templates are located
   */
  static function setDefaultViewPath($path) {
    return self::$DEFAULT_VIEW_PATH = $path;
  }

  /*
   * Render the specified template
   * @param string $name name of template
   */
  protected function render($name = null) {

    // start capturing output in buffer
    ob_start();

    $view_path = $this->view_path;
    $template_file = $view_path."$name.html.php";

    // include template file in anonymous function
    // so that the populated variables doesn't cause conflicts
    $template_call = function () use ($template_file) {
        extract($this->data);
        include($template_file);
    };

    $template_call();

    // get all ouput content and clean the buffer
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  /**
   * Include another view into the current view
   * @param string $name e.g 'book/footer'
   * @param array 
   */
  protected function partial($name, array $data = array()) {

    $view_path = $this->view_path;
    $template_file = $view_path."$name.html.php";

    extract($this->data); 

    extract($data); 

    include($template_file);
  }
}
