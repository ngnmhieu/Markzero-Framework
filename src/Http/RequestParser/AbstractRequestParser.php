<?php
namespace Markzero\Http\RequestParser;

/**
 * 
 */
class AbstractRequestParser {
  /**
   * @param  string Request Data
   * @return array
   */
  public function parse($data);
}
