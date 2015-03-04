<?php
class TransactionController extends AppController {
  function index() {
    $data['transactions'] = Transaction::findAll();

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

  function get_support_currencies() {
    $data['currencies'] = Transaction::get_support_currencies();

    $this->response()->respond_to('json', function() use ($data) {
      App::$view->render('json', $data, $this->name().'/'.'currencies');
    });
  }

  function filtered_transactions() {
    $data['transactions'] = Transaction::findByFilter($this->request()->query);

    $this->response()->respond_to('json', function() use ($data) {
      if ($data['transactions']) {
        $data['transactions'] = array_map(function($transaction) {
          return $transaction->to_array();
        }, $data['transactions']);
      }

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
    $data['categories'] = Category::findAll();
    $tran = new Transaction();

    $this->response()->respond_to('html', function() use ($data) {
      App::$view->render('html', $data, $this->name().'/'.'add', 'default');
    });

  }

  function edit($id) {
    $data['transaction'] = Transaction::find($id);
    $data['categories'] = Category::findAll();

    $this->response()->respond_to('html', function() use ($data) {
      App::$view->render('html', $data, $this->name().'/'.'edit', 'default');
    });
  }

  function create() {
    try {
      $tran = Transaction::create($this->request()->request);

      $this->response()->respond_to('html', function() {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      });

      $this->response()->respond_to('json', function() {
        $this->response()->setStatusCode(Response::HTTP_CREATED);
      });
    } catch(ValidationException $e) {
      $this->response()->respond_to('html', function() use($e) {
        set_flash('error', implode("<br />",$e->get_errors()));
        $this->response()->redirect(array("controller" => $this->name(), 'action' => 'add'));
      });

      $this->response()->respond_to('json', function() use($e) {
        $this->response()->setStatusCode(Response::HTTP_BAD_REQUEST, 'Bad Request (Validation Error)');
        $data = array('validation_errors' => $e->get_errors());
        App::$view->render('json', $data, 'errors/validation');
      });
    }
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
    try {
      $tran = Transaction::update($id, $this->request()->request);

      $this->response()->respond_to('html', function() {
          $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      });

      $this->response()->respond_to('json', function() {
        $this->response()->setStatusCode(Response::HTTP_OK, 'Transaction Updated');
      });

    } catch(ValidationException $e) {

      $this->response()->respond_to('html', function() use($e, $id) {
        set_flash('error', implode("<br />",$e->get_errors()));
        $this->response()->redirect(array("controller" => $this->name(), 'action' => 'edit'), array($id));
      });

      $this->response()->respond_to('json', function() use($e) {
        $this->response()->setStatusCode(Response::HTTP_BAD_REQUEST, 'Bad Request (Validation Error)');
        $data = array('validation_errors' => $e->get_errors());
        App::$view->render('json', $data, 'errors/validation');
      });

    }
  }

}
