<?php
/*
 * Manages the organizing and loading of layouts and templates (or views).
 * AppView works in conjunction with Controller, in order to render the appropriate views.
 */
class AppViewOld {
  private $view_dir;
  private $layout_dir;
  private $layout;

  /*
   * @param string $views_dir directory contains all views and layout
   */
  function __construct($view_dir) {
    $this->view_dir = $view_dir;
    $this->layout_dir = "$this->view_dir/layouts";
  }

  /**
   * @param string $layout | default layout used to render views
   */
  public function setLayout($layout) {
    $this->layout = $layout;
  }

  /*
   * Render the specified template
   * @param string $format
   * @param array  $data variable used in layout and template file
   * @param string $name name of template
   * @param string $layout name of layout
   */
  public function render($format, $data, $name = null, $layout="") {
    // output raw data when no name is given
    if ($name === null) {
      echo $data;
      return;
    }

    $view_dir = $this->view_dir;
    $template_file = "$view_dir/$name.$format.php";

    // include template file in anonymous function
    // so that the populated variables doesn't cause conflicts
    $template_call = function () use ($template_file, $data) {
        extract($data);
        include($template_file);
    };

    if ($layout != "") { // specific layout
      $this->renderLayout($format, $layout, $template_call);
    } else if ($this->layout != "") { // default layout
      $this->renderLayout($format, $this->layout, $template_call);
    } else { // no layout
      $template_call();
    }
  }

  /*
   * Load the layout, layout will execute `$main()` to output main template
   * @param string $format
   * @param string $name name of layout [ex: homepage.layout.php, default.layout.php, ...]
   * @param callable $main ouput of main content
   */
  protected function renderLayout($format, $name, $main) {
      $layout_file = "$this->layout_dir/$name.$format.php";
      include($layout_file);
  }
}
