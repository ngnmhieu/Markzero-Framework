<?php
class PhotoController extends AppController {
  function index() {
    $users = User::find_by_sql("select * from users where id % 2 <> 0");
    var_dump($users);
    $this->render();
  }  

  function tag($tag) {
    // TODO: validate $tag
    $data['photos'] = Tumblr::searchTaggedPhoto($tag);
    $this->render($data);
  }
}
