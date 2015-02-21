<?php
class TransactionController extends AppController {
  function index() {
    $data['transactions'] = Transaction::find_all();

    $this->response()->respond_to('html', function() use ($data) {
      App::$view->render('html', $data, $this->name().'/'.'index', 'default');
    });

    $this->response()->respond_to('json', function() use ($data) {
      App::$view->render('json', $data, $this->name().'/'.'index');
    });

    $this->response()->respond();
  }

  function add() {
    $data['categories'] = Category::find_all();
    $tran = new Transaction();

    $this->response()->respond_to('html', function() use ($data) {
      App::$view->render('html', $data, $this->name().'/'.'add', 'default');
    });

    $this->response()->respond();
  }

  function edit($id) {
    $data['transaction'] = Transaction::find($id);
    $data['categories'] = Category::find_all();

    $this->response()->respond_to('html', function() use ($data) {
      App::$view->render('html', $data, $this->name().'/'.'edit', 'default');
    });

    $this->response()->respond();
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

    $this->response()->respond();
  }

  function delete($id) {
    if (Transaction::delete($id)) {
      $this->response()->respond_to('html', function() use($tran) {
        $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
      });
    }

    $this->response()->respond();
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

    $this->response()->respond();
  }
}
