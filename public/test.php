<?php
function preg_replace_inorder($pattern, array $replacements, $subject) {
  return preg_replace_callback($pattern, 
    function($matches) use (&$replacements) {
      return array_shift($replacements);
    }, $subject);
}
$route_pattern = '~/transaction/([0-9])/edit/(24)~';
$dest = 'transaction#edit';

$func_name = str_replace('#', '_', $dest).'_path';

// remove first and last character
$url = substr($route_pattern, 1, strlen($route_pattern) - 2);

$params = ["sdf", 2];

echo preg_replace_inorder('~\(.*?\)~', $params, $url);

?>
