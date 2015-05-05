<?php
/**
 * Collection of array-related helper-functions
 */
class ArrayHelper {

  /**
   * Make Multidimensional array One-Dimensional array
   * @param array Array to be flatten
   */
  static function array_flatten($array) {
    if (!is_array($array)) {
      return array($array);
    }

    $result = array();
    foreach ($array as $item) {
      $result = array_merge($result, self::array_flatten($item));
    }

    return $result;
  }

}

