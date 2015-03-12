<?php
use \Curl\Curl;

class Currency extends AppModel {
  protected static $CURRENCIES = array('USD', 'EUR', 'VND', 'GBP');

  /**
   * Return exchange rates base on USD
   * @throw Exception
   */
  static function exchange_rates() {
    $curl = new Curl();

    $pairs = array_map(function($currency) {
      return "'USD,$currency'";
    }, self::$CURRENCIES);

    $api_endpoint = 'http://query.yahooapis.com/v1/public/yql?q='.urlencode('select * from yahoo.finance.xchange where pair in ('.implode(',', $pairs).')')
      .'&format=json'
      .'&env='.urlencode('store://datatables.org/alltableswithkeys');

    $curl->error(function() use ($curl) {
      throw new Exception("Cannot request Exchange Rates.");
    });
    $curl->get($api_endpoint);

    $data = $curl->response->query->results->rate;
    if (!$data) {
      throw new Exception("Cannot request Exchange Rates.");
    }

    $exchange_rates = array(); 
    foreach ($data as $rate) {
      $key = substr($rate->id,0,3);
      $exchange_rates[$key] = $rate->Rate;
    }

    return $exchange_rates;
  }

  static function get_supported_currencies() {
    return self::$CURRENCIES;
  }
}
