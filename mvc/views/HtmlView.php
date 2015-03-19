<?php
class HtmlView extends AppView {
  private $view_dir;

  /**
   * @param array $data Data to be populated, array('variable_name' => `value`, ...)
   * @param string $name Name of the view e.g 'book/index'
   * @param string $views_dir Directory contains all views and layout
   */
  function __construct(array $data, $name = null, $view_dir) {
    $this->view_dir = $view_dir;
    $this->setContent($this->render($data, $name));
  }

  /*
   * Render the specified template
   * @param array  $data variable used in layout and template file
   * @param string $name name of template
   */
  public function render($data, $name = null) {
    ob_start();

    // output raw data when no name is given
    if ($name === null) {
      return $data;
    }

    $view_dir = $this->view_dir;
    $template_file = "$view_dir/$name.html.php";

    // include template file in anonymous function
    // so that the populated variables doesn't cause conflicts
    $template_call = function () use ($template_file, $data) {
        extract($data);
        include($template_file);
    };

    $template_call();

    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  /**
   * Include another view into the current view
   * @param string $name e.g 'book/footer'
   */
  protected function partial($name) {
    $view_dir = $this->view_dir;
    $template_file = $view_dir."$name.html.php";
    include($template_file);
  }
}
