<?php
/**
 * Produce a plain text View
 */
class TextView extends AppView {

  /**
   * @throw InvalidArgumentException
   */
  public function __construct($str) {
    if (!is_string($str)) {
      throw new InvalidArgumentException();
    }
    $this->setContent($str);
  }

}
