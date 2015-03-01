<?php
class TransactionController extends AppController {
  function index() {
    $data['transactions'] = Transaction::find_all();

    $this->response()->respond_to('html', function() use ($data) {
      App::$view->render('html', $data, $this->name().'/'.'index', 'default');
    });

    $this->response()->respond_to('json', function() use ($data) {
      $data['transactions'] = array_map(function($transaction) {
        return $transaction->to_array();
      }, $data['transactions']);

      App::$view->render('json', $data, $this->name().'/'.'index');
    });
  }

  function show($id) {
    $data['transaction'] = Transaction::find($id);

    $this->response()->respond_to('json', function() use ($data) {
      $data['transaction'] = $data['transaction']->to_array();
      App::$view->render('json', $data, $this->name().'/'.'show');
    });
  }

  function add() {
    $data['categories'] = Category::find_all();
    $tran = new Transaction();

    $this->response()->respond_to('html', function() use ($data) {
      App::$view->render('html', $data, $this->name().'/'.'add', 'default');
    });

  }

  function edit($id) {
    $data['transaction'] = Transaction::find($id);
    $data['categories'] = Category::find_all();

    $this->response()->respond_to('html', function() use ($data) {
      App::$view->render('html', $data, $this->name().'/'.'edit', 'default');
    });
  }

  function create() {
    $tran = Transaction::create($this->request()->request);

    $this->response()->respond_to('html', function() use($tran) {
      if(empty($tran->errors)) {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      } else {
        set_flash('error', implode("<br />",$tran->errors));
        $this->response()->redirect(array("controller" => $this->name(), 'action' => 'add'));
      }
    });

    $this->response()->respond_to('json', function() use($tran) {
       if (empty($tran->errors)) {
         $this->response()->setStatusCode(Response::HTTP_CREATED);
       } else {
         $this->response()->setStatusCode(Response::HTTP_BAD_REQUEST, 'Bad Request (Validation Error)');
         $data = array('validation_errors' => $tran->errors);
         App::$view->render('json', $data, 'errors/validation');
       }
    });
  }

  function delete($id) {
    if (Transaction::delete($id)) {
      $this->response()->respond_to('html', function() {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      });

      $this->response()->respond_to('json', function() {
      });
    }
  }

  function update($id) {
    $tran = Transaction::update($id, $this->request()->request);

    $this->response()->respond_to('html', function() use($tran) {
      if (empty($tran->errors)) {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      } else {
        set_flash('error', implode("<br />",$tran->errors));
        $this->response()->redirect(array("controller" => $this->name(), 'action' => 'edit'), array($id));
      }
    });

    $this->response()->respond_to('json', function() use($tran) {
      if (empty($tran->errors)) {
        $this->response()->setStatusCode(Response::HTTP_OK, 'Transaction Updated');
      } else {
        $this->response()->setStatusCode(Response::HTTP_BAD_REQUEST, 'Bad Request (Validation Error)');
        $data = array('validation_errors' => $tran->errors);
        App::$view->render('json', $data, 'errors/validation');
      }
    });
  }
}
