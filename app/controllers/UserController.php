<?php
class UserController extends AppController {
  function index() {
    $users = User::find_all();
    
    $this->render();
  }

  function show($id) {
    $this->render(['id' => $id], "", "sublayout/test"); 
  }

  function login() {
    echo "Login please";
  }
}
