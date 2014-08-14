<?php

class AdminController extends AppController {
  function index() {
    if (!App::$session->is_logged_in()) {
      $this->redirect(array('action' => 'login'));
      return;
    }
    $this->render();
  }

  function login() {
    $this->render();
  }
}
