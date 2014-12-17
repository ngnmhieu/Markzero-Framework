<?php
class CategoryController extends AppController {
  function index() {
    $data['categories'] = Category::find_all();
    App::$view->render($data, $this->name().'/'.'index', 'default');
  }

  function edit($id) {
    $data['category'] = Category::find($id);

    App::$view->render($data, $this->name().'/'.'edit', 'default');
  }

  function update($id) {
    $cat = Category::update($id, $this->request()->post());
    if (empty($cat->errors)) {
      $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
    } else {
      print_r($cat->errors);
    }
  }

  function delete($id) {
    if (Category::delete($id)) {
      $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
    }
  }

  function create() {
    $cat = Category::create($this->request()->post());
    if(empty($cat->errors)) {
      $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
    } else {
      print_r($cat->errors);
    }
  }

  function add() {
    App::$view->render(array(), $this->name().'/'.'add', 'default');
  }
}
