<?php
use Markzero\App;

/**
 * selected attribute in tag-option for select list html
 * @return string | "selected" if $a $b are equal
 */
function selected($a, $b) {
  return $a == $b ? "selected" : "";
}

/**
 * @param string $route_id
 * @param array $params
 */
function webpath($route_id, array $params = array()) {
  $route_comp  = explode('#',$route_id);
  $controller = $route_comp[0];
  $action     = $route_comp[1];

  // return the first webpath found
  return App::$router->getWebpaths($controller, $action, $params)[0];
}
