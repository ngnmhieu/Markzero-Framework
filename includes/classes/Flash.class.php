<?php

/**
 * Flash keep messages between 2 request
 * usually used for error, notice, success messages
 */
class Flash {
  // @type Session | Session object
  private $session;
  // @type boolean | keep old flash messages to next request or not
  private $keep;

  /**
   * @param Session | session object (in this framework App::$session is used)
   */
  function __construct(Session $session) {
    $this->session = $session;
    $this->keep = false;
  }

  /**
   * keep old flash messages to next request
   */
  public function keep() {
    $this->keep = true;
  }

  public function get($type) {
    $key = 'Flash.old.'.$type;
    if ($this->session->exist($key))
      return $this->session->get($key);

    return '';
  }

  public function set($key, $value) {
    $key = 'Flash.new.'.$type;
    $this->session->set($key, $value);
  }

  function __destruct() {
    $session_arr = $this->session->all(); 

    // remove all old flash messages from last request
    if (!$this->keep) {
      foreach ($session_arr as $key => $value) {
        if (preg_match("~^Flash\.old\..*$~",$key)) {
          $this->session->remove($key);
        }
      }
    }

    // prepare flash messages for next request
    foreach ($session_arr as $key => $value) {
      if (preg_match("~^Flash\.new\.(.*)$~",$key, $matches)) {
        $this->session->set('Flash.old.'.$matches[1], $value);
      }
    }

  }
}
