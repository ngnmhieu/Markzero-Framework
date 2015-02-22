<?php
use Symfony\Component\HttpFoundation;

/**
 * Represent a HTTP Request
 **/
class Request {
  private $http_request;

  function __construct() {
    $this->http_request = new HttpFoundation\Request(
      $_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER
    );

    $this->prepareRequestData();
  }

  /**
   * Applies to Request with Content-Type other than application/x-www-form-urlencoded
   * Replace POST request parameters with the content in the body of the request
   * (if possible)
   */
  private function prepareRequestData() {
    $http_request = $this->http_request;
    $content_type = $http_request->headers->get('Content-Type');

    if (0 === strpos($content_type, 'application/json')) {
        $data = json_decode($http_request->getContent(), true);
        $http_request->request->replace(is_array($data) ? $data : array());
    } else if (0 === strpos($content_type, 'application/xml')) {
      // pending
    }

  } 

  /**
   * Check if current request is a Cross-Domain Request
   * @return boolean
   */
  public function isCrossDomain() {
    $http_request = $this->http_request;
    $origin = $http_request->headers->get('Origin');

    // A cross-domain request must have an 'Origin' header
    if (is_null($origin))
      return false;

    // Request with HTTP Method 'OPTIONS' is always Cross-Domain
    if ($http_request->getMethod() == 'OPTIONS')
      return true;

    // Finally, compare Origin and Host
    $server_host = $http_request->getSchemeAndHttpHost().':'.$http_request->getPort();
    preg_match('/^(https?):\/\/(.*?)(?::(\d+))?$/', $origin, $matches);
    $origin_protocol = $matches[1];
    $origin_hostname = $matches[2];
    $origin_port     = isset($matches[3]) ? $matches[3] : null;
    if (is_null($origin_port)) {
      $origin_port = $origin_protocol === 'http' ? '80' : '443';
    }
    $origin_host = "$origin_protocol://$origin_hostname:$origin_port";

    return $server_host !== $origin_host;
  }

  /**
   * _TODO: more thorough check
   * Check if CORS Request allowed
   * @return boolean
   */
  public function isCrossDomainAllowed() {
    $whitelist = array('http://localhost:8000', 'markzero.com');
    $this->http_request->setTrustedHosts($whitelist);

    $origin = $this->http_request->headers->get('Origin', '');
    $trusted_origins = $this->http_request->getTrustedHosts();

    foreach ($trusted_origins as $host_pattern) {
      if (preg_match($host_pattern, $origin)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Delegate undefined methods to HttpFoundation\Request object
   */
  function __call($method, $args) {
    return call_user_func_array(array($this->http_request, $method), $args);
  }

  /**
   * Delegate undefined attributes to HttpFoundation\Request object
   */
  function __get($attribute) {
    return $this->http_request->$attribute;
  }
}
