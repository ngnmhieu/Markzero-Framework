<?php
class CategoryController extends AppController {
  function index() {
    $data['categories'] = Category::find_all();
    $this->render($data);
  }

  function edit($id) {
    $data['category'] = Category::find($id);
    $this->render($data);
  }

  function update($id) {
    $cat = Category::update($id, $this->request()->post());
    if (empty($cat->errors)) {
      $this->redirect(array("action" => "index"));
    } else {
      print_r($cat->errors);
    }
  }

  function delete($id) {
    if (Category::delete($id)) {
      $this->redirect(array("action" => "index"));
    }
  }

  function create() {
    $cat = Category::create($this->request()->post());
    if(empty($cat->errors)) {
      $this->redirect(array("action" => "index"));
    } else {
      print_r($cat->errors);
    }
  }

  function add() {
    $this->render();
  }
}
