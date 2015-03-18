<?php

/**
 * Declear interface for all the View classes
 */
abstract class AppView {
  protected $content;
  
  public function getContent() {
    return $this->content;
  }

  public function setContent($content) {
    $this->content = $content;
  }
}
