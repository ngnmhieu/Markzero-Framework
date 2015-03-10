<?php

class CurrencyController extends AppController {
  
  public function get_exchange_rates() {
    try {
      $exchange_rates = Currency::exchange_rates();

      $this->respond_to('json', function() use($exchange_rates) {
        App::$view->render('json', json_encode($exchange_rates));
      });
    } catch (Exception $e) {
      $this->respond_to('json', function() use ($e) {
        $error = array('error' => $e->getMessage());
        App::$view->render('json', json_encode($error));
      });
    }

  }

  function get_supported_currencies() {
    $currencies = Currency::get_supported_currencies();

    $this->response()->respond_to('json', function() use ($currencies) {
      App::$view->render('json', json_encode($currencies));
    });
  }
}
