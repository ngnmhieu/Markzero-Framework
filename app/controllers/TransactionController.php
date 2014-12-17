<?php
class TransactionController extends AppController {
  function index() {
    $data['transactions'] = Transaction::find_all();

    App::$view->render($data, $this->name().'/'.'index', 'default');
  }

  function add() {
    $data['categories'] = Category::find_all();
    $tran = new Transaction();

    App::$view->render($data, $this->name().'/'.'add', 'default');
  }

  function edit($id) {
    $data['transaction'] = Transaction::find($id);
    $data['categories'] = Category::find_all();

    App::$view->render($data, $this->name().'/'.'edit', 'default');
  }

  function create() {
    $tran = Transaction::create($this->request()->post());
    if(empty($tran->errors)) {
      $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
    } else {
      set_flash('error', implode("<br />",$tran->errors));
      $this->response()->redirect(array("controller" => $this->name(), 'action' => 'add'));
    }
  }

  function delete($id) {
    if (Transaction::delete($id)) {
      $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
    }
  }

  function update($id) {
    $tran = Transaction::update($id, $this->request()->post());
    if (empty($tran->errors)) {
      $this->response()->redirect(array("controller" => $this->name(), "action" => "index"));
    } else {
      set_flash('error', implode("<br />",$tran->errors));
      $this->response()->redirect(array("controller" => $this->name(), 'action' => 'edit'), array($id));
    }
  }
}
