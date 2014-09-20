<?php
class UserController extends AppController {
  function index() {
    var_dump(App::$data);
    foreach (Tumblr::getShuffledPhotos() as $photo) {
      echo "<img src='$photo->url' /> "; 
    }
  }

  function show($id) {
    $this->render(['id' => $id]); 
  }

  function login() {
    echo "Login please";
  }
}
