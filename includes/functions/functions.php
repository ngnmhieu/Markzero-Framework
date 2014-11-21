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
} // currently not being used

/**
 * @param string $pattern      | Regex pattern
 * @param array $replacements | array of replacements (in order)
 * @param string $subject      | string to replace
 */
function preg_replace_inorder($pattern, array $replacements, $subject) {
  return preg_replace_callback($pattern, 
    function($matches) use ($replacements) {
      return array_shift($replacements);
    }, $subject);
}

?>
