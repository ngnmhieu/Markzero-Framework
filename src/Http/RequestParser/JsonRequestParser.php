<?php
namespace Markzero\Http\RequestParser;

class JsonRequestParser extends AbstractRequestParser {

  public function parse($data) {
    return json_decode($data, true);
  }
  
}
