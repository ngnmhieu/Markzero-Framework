<?php

/*
 * Manages the organizing and loading of layouts and templates (or views).
 * AppView works in conjunction with Controller, in order to render the appropriate views.
 */
class AppView {
  /*
   * Load the layout, layout will execute `$main()` to output main template
   * @param string $name name of layout [ex: homepage.layout.php, default.layout.php, ...]
   * @param callable $main ouput of main content
   */
  protected function layout($name, callable $main) {
      $views_dir = App::$VIEWS_DIR;
      $layout_file = "$views_dir/$name.layout.php";
      include($layout_file);
  }

  /*
   * Render the specified template
   * @param array $data variable used in layout and template file
   * @param string $name name of template
   * @param string $layout name of layout
   */
  public function render($data, $name, $layout) {
    $views_dir = App::$VIEWS_DIR;
    $template_file = "$views_dir/$name.tpl.php";

    // include template file in anonymous function
    // so that the populated variables doesn't cause conflicts
    $template_call = function () use ($template_file, $data) {
        // TODO: check template file exists
        extract($data);
        include($template_file);
    };

    if ($layout != "") {
      // wrap template in an layout
      $this->layout($layout, $template_call);
    } else {
      $template_call();
    }
  }
}
