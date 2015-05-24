<?php

/**
 * @param $pattern Regular Expression string
 * @param $input 
 * @param boolean flags if PREG_GREP_INVERT is given, 
 *              return entries with keys not match the pattern
 * @return array | array contains all key that match a given pattern
 */
function preg_grep_keys(string $pattern, array $input, $flags = 0) {
    return array_intersect_key($input, array_flip(preg_grep($pattern, array_keys($input), $flags)));
}

/**
 * Make Multidimensional array One-Dimensional array
 * @param array Array to be flatten
 */
function array_flatten($array) {
  if (!is_array($array)) {
    return array($array);
  }

  $result = array();
  foreach ($array as $item) {
    $result = array_merge($result, array_flatten($item));
  }

  return $result;
}

?>
