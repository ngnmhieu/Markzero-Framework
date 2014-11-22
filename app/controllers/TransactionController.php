<?php
class TransactionController extends AppController {
  function index() {
    $data['transactions'] = Transaction::find_all();
    $this->render($data);
  }

  function add() {
    $data['categories'] = Category::find_all();
    $tran = new Transaction();
    $this->render($data);
  }

  function edit($id) {
    $data['transaction'] = Transaction::find($id);
    $data['categories'] = Category::find_all();
    $this->render($data);
  }

  function create() {
    $tran = Transaction::create($this->request()->post());
    if(empty($tran->errors)) {
      $this->redirect(["action" => "index"]);
    } else {
      $this->flash('error', implode("<br />",$tran->errors));
      $this->redirect(['action' => 'add']);
    }
  }

  function delete($id) {
    if (Transaction::delete($id)) {
      $this->redirect(["action" => "index"]);
    }
  }

  function update($id) {
    $tran = Transaction::update($id, $this->request()->post());
    if (empty($tran->errors)) {
      $this->redirect(["action" => "index"]);
    } else {
      $this->flash('error', implode("<br />",$tran->errors));
      $this->redirect(['action' => 'edit'], [$id]);
    }
  }
}
