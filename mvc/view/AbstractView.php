<?php
namespace Markzero\Mvc\View;

/**
 * Declear interface for all the View classes
 */
abstract class AbstractView {
  protected $content;
  
  public function getContent() {
    return $this->content;
  }

  public function setContent($content) {
    $this->content = $content;
  }
}
