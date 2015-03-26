<?php
namespace Markzero\Mvc\View;

/**
 * Produce a Json-format View
 */
class JsonView extends AbstractView {

  public function __construct($data) {
    $this->setContent(json_encode($data));
  }

}
