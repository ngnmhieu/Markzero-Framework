<?php

class AdminController extends AppController {
  function index() {
    $this->redirect(array('controller' => 'user'));
    # $this->render();
  }

  function login() {
  }
}
