<?php
class CategoryController extends AppController {
  function index() {
    $data['categories'] = Category::findAll();

    $this->response()->respond_to('html', function() use($data) {
      App::$view->render('html', $data, 'category/index', 'default');
    });

    $this->response()->respond_to('json', function() use($data) {
      App::$view->render('json', $data, 'category/index');
    });

  }

  function edit($id) {
    $data['category'] = Category::find($id);

    $this->response()->respond_to('html', function() use($data) {
      App::$view->render('html', $data, 'category/edit', 'default');
    });

  }

  function update($id) {
    try {
      $cat = Category::update($id, $this->request()->request);

      $this->response()->respond_to('html', function() {
        $this->response()->redirect(array("controller" => 'category', "action" => "index"));
      });

      $this->response()->respond_to('json', function() {
        $this->response()->setStatusCode(Response::HTTP_OK, 'Category Updated');
      });

    } catch(ValidationException $e) {
      $this->response()->respond_to('html', function() use($e) {
        print_r($e->get_errors());
      });

      $this->response()->respond_to('json', function() use($e) {
        $this->response()->setStatusCode(Response::HTTP_BAD_REQUEST, 'Bad Request (Validation Error)');
        $data = array('validation_errors' => $e->get_errors());
        App::$view->render('json', $data, 'errors/validation');
      });

    }
  }

  function delete($id) {
    try {
      Category::delete($id);

      $this->response()->respond_to('html', function() {
        $this->response()->redirect(array("controller" => 'category', "action" => "index"));
      });

      $this->response()->respond_to('json', function() {
        $this->response()->setStatusCode(Response::HTTP_OK, 'Category Deleted');
      });
    } catch (Exception $e) {
      $this->response()->respond_to('html', function() use($e) {
        echo "Error: ".$e->getMessage();
      });

      $this->response()->respond_to('json', function() use($e) {
        $this->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR, 'Category could not be deleted, error occurred: '.$e->getMessage());
      });
    }

  }

  function create() {
    try {
      $cat = Category::create($this->request()->request);

      $this->response()->respond_to('html', function() {
        $this->response()->redirect(array("controller" => 'category', "action" => "index"));
      });

      $this->response()->respond_to('json', function() {
        $this->response()->setStatusCode(Response::HTTP_OK, 'Category Created');
      });
    } catch(ValidationException $e) {

      $this->response()->respond_to('html', function() use($e) { 
        print_r($e->get_errors()); 
      });

      $this->response()->respond_to('json', function() use($e) {
        $this->response()->setStatusCode(Response::HTTP_BAD_REQUEST, 'Bad Request (Validation Error)');
        $data = array('validation_errors' => $e->get_errors());
        App::$view->render('json', $data, 'errors/validation');
      });

    }
  }

  function add() {
    $this->response()->respond_to('html', function() {
      App::$view->render('html', array(), 'category/add', 'default');
    });

  }
}
