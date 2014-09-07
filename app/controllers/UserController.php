<?php
class UserController extends AppController {
  function index() {
    $users = User::find_all();
    foreach ($users as $user) {
      var_dump($user);
    }
    
    $this->render();
  }

  function show($id) {
  }

  function login() {
    echo "Login please";
  }
}
