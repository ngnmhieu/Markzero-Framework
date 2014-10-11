<?php
class TransactionController extends AppController {
  function index() {
    $data['transactions'] = Transaction::find_all();
    $this->render($data);
  }

  function add() {
    $this->render();
  }

  function create() {
    $tran = Transaction::create($this->request()->post());
    if(empty($tran->errors)) {
      $this->redirect(["action" => "index"]);
    } else {
      print_r($tran->errors);
      die();
    }
  }

  function delete($id) {
    if (Transaction::delete($id)) {
      $this->redirect(["action" => "index"]);
    }
  }
}
