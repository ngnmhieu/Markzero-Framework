<?php
namespace Markzero\Http\Routing\RouteMatcher;

/**
 * Match Routes using Regular Expression
 */
class RegexRouteMatcher extends AbstractRouteMatcher {
  /**
   * @var string
   */
  private $pattern;

  /**
   * @param string Regex pattern
   */
  public function __construct($pattern) {
    $this->pattern = $pattern;
    $this->arguments = array();
  }

  /**
   * @param string Path to be matched against the given pattern
   * @return bool
   */
  public function match($path) {
    $matches = array();
    $result = !!preg_match($this->pattern, $path, $matches);

    if ($result)
      $this->arguments = array_slice($matches, 1);

    return $result;
  }

}
