<?php
class MobileController extends AppController {
  function index() {
    $data['mobiles'] = Mobile::find_all();
    $img_path = '/images/phones/';
    $img_dir = App::$PUBLIC.'images/phones/';
    foreach($data['mobiles'] as $m) {
      $m->picture = $img_path.$m->picture;
    }
    $this->render($data, '', 'mobile');
  }

  function notify($id) {
    if (Mobile::notify($id, $this->request()->post())) {
      $status = 'success';
    } else {
      $status = 'error';
    }

    echo json_encode(array(
      'status' => $status
    ));
  }

  function add() {
  }

  function edit($id) {
  }

  function create() {
  }

  function delete($id) {
  }

  function update($id) {
  }
}
