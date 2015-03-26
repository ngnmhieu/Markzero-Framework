<?php
namespace Markzero\Mvc\View;

class HtmlView extends AbstractView {
  private $view_path;
  private static $DEFAULT_VIEW_PATH = null; 

  /**
   * @param array  $data Data to be populated, array('variable_name' => `value`, ...)
   * @param string $name Name of the view e.g 'book/index'
   * @param string $views_path Directory contains all views and layout
   * @throw Exception If no view path is specified
   *          either through HtmlView::setDefaultViewPath
   *          or through the constructor
   */
  function __construct(array $data, $name = null, $view_path = null) {
    if ($view_path === null) {
      $this->view_path = self::$DEFAULT_VIEW_PATH;
    } else {
      $this->view_path = $view_path;
    }

    if ($this->view_path === null) {
      throw new Exception("No view path is specified. Please specify it with either HtmlView::setDefaultViewPath or through HtmlView constructor.");
    }

    $this->setContent($this->render($data, $name));
  }

  /**
   * Set default view path, where templates are located
   */
  static function setDefaultViewPath($path) {
    return self::$DEFAULT_VIEW_PATH = $path;
  }

  /*
   * Render the specified template
   * @param array  $data variable used in layout and template file
   * @param string $name name of template
   */
  public function render(array $data, $name = null) {

    // start capturing output in buffer
    ob_start();

    $view_path = $this->view_path;
    $template_file = $view_path."$name.html.php";

    // include template file in anonymous function
    // so that the populated variables doesn't cause conflicts
    $template_call = function () use ($template_file, $data) {
        extract($data);
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
   */
  protected function partial($name) {
    $view_path = $this->view_path;
    $template_file = $view_path."$name.html.php";
    include($template_file);
  }
}
