<?php
use Markzero\App;

/**
 * selected attribute in tag-option for select list html
 * @return string | "selected" if $a $b are equal
 */
function selected($a, $b) 
{
  return $a == $b ? "selected" : "";
}

/**
 * Get Webpath for the given controller and action
 *
 * $ctrl_str is given in form:
 *   [Module::Submodule::]Ctrlname
 * and will be converted to
 *   [Module\Submodule\]Controllers\Ctrlname
 * 
 * @param string specifies target controller in aforementioned form
 * @param string action name
 * @param array  optional arguments to the action method
 */
function webpath($ctrl_str, $action, array $args = array()) 
{
  $parts = explode('::', $ctrl_str);

  // insert 'Controllers' between namespace and controller name
  array_splice( $parts, count($parts) - 1, 0, 'Controllers' ); 

  $ctrl_parts = array_merge(['App'], $parts);

  $ctrl_fullname = implode('\\', $ctrl_parts);

  // return the first webpath found
  return App::$router->getWebpaths($ctrl_fullname, $action, $args)[0];
}
