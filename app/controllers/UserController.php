<?php
class UserController extends AppController {
  function index() {
  }

  function show($id) {
    $this->render(['id' => $id]); 
  }

  function login() {
    echo "Login please";
  }
}
