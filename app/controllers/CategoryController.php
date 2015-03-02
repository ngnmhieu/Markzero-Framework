<?php
class CategoryController extends AppController {
  function index() {
    $data['categories'] = Category::findAll();

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

    $this->response()->respond_to('json', function() use($cat) {
      if (empty($cat->errors)) {
        $this->response()->setStatusCode(Response::HTTP_OK, 'Category Updated');
      } else {
        $this->response()->setStatusCode(Response::HTTP_BAD_REQUEST, 'Bad Request (Validation Error)');
        $data = array('validation_errors' => $cat->errors);
        App::$view->render('json', $data, 'errors/validation');
      }
    });

  }

  function delete($id) {
    if (Category::delete($id)) {
      $this->response()->respond_to('html', function() {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      });

      $this->response()->respond_to('json', function() {
        $this->response()->setStatusCode(Response::HTTP_OK, 'Category Deleted');
      });

    } else {
      $this->response()->respond_to('html', function() {
        echo "Error";
      });

      $this->response()->respond_to('json', function() {
        $this->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR, 'Category could not be deleted, error occurred');
      });
    }

  }

  function create() {
    $cat = Category::create($this->request()->request);

      $this->response()->respond_to('html', function() use($cat) {
        if(empty($cat->errors)) {
          $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
        } else {
          $this->response()->respond_to('html', function() { print_r($cat->errors); });
        }
      });

      $this->response()->respond_to('json', function() use($cat) {
        if(empty($cat->errors)) {
          $this->response()->setStatusCode(Response::HTTP_OK, 'Category Created');
        } else {
          $this->response()->setStatusCode(Response::HTTP_BAD_REQUEST, 'Bad Request (Validation Error)');
          $data = array('validation_errors' => $cat->errors);
          App::$view->render('json', $data, 'errors/validation');
        }
      });
  }

  function add() {
    $this->response()->respond_to('html', function() {
      App::$view->render('html', array(), $this->name().'/'.'add', 'default');
    });

  }
}
