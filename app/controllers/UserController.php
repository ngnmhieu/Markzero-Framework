<?php
class UserController extends AppController {
  function index() {
  }

  function show($id) {
    $this->render(array('id' => $id)); 
  }

  function login() {
    echo "Login please";
  }
}
