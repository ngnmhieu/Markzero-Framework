<?php
function flash($key) {
  $flasher = \Session\Flash::getInstance();
  return $flasher->get($key);
}
