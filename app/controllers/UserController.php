<?php
class UserController extends AppController {
  function index() {
    $users = User::all();
    var_dump($users);
  }
}
