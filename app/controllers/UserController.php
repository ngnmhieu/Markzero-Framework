<?php
class UserController extends AppController {
  function index() {
    $users = UserModel::query("SELECT * FROM user");
    foreach ($users as $user) {
      echo $user->name . "<br>";
    }
  }
}
