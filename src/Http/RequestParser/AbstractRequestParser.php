<?php
namespace Markzero\Http\RequestParser;

abstract class AbstractRequestParser {

  /**
   * @param  string Request Data
   * @return array
   */
  public abstract  function parse($data);
}
