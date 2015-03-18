<?php
/**
 * Produce a Json-format View
 */
class JsonView extends AppView {

  public function __construct($data) {
    $this->setContent(json_encode($data));
  }

}
