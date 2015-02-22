<?php
class CategoryController extends AppController {
  function index() {
    $data['categories'] = Category::find_all();

    $this->response()->respond_to('html', function() use($data) {
      App::$view->render('html', $data, $this->name().'/'.'index', 'default');
    });

    $this->response()->respond_to('json', function() use($data) {
      App::$view->render('json', $data, $this->name().'/'.'index');
    });

  }

  function edit($id) {
    $data['category'] = Category::find($id);

    $this->response()->respond_to('html', function() use($data) {
      App::$view->render('html', $data, $this->name().'/'.'edit', 'default');
    });

  }

  function update($id) {
    $cat = Category::update($id, $this->request()->request);

    $this->response()->respond_to('html', function() use($cat) {
      if (empty($cat->errors)) {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      } else {
        print_r($cat->errors);
      }
    });

  }

  function delete($id) {
    if (Category::delete($id)) {
      $this->response()->respond_to('html', function() {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      });
    }

  }

  function create() {
    $cat = Category::create($this->request()->request);

    if(empty($cat->errors)) {
      $this->response()->respond_to('html', function() {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      });
    } else {
      $this->response()->respond_to('html', function() {
        print_r($cat->errors);
      });
    }

  }

  function add() {
    $this->response()->respond_to('html', function() {
      App::$view->render('html', array(), $this->name().'/'.'add', 'default');
    });

  }
}
