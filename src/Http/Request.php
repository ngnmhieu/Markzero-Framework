<?php
namespace Markzero\Http;

use Symfony\Component\HttpFoundation;

/**
 * Represent a HTTP Request
 **/
class Request extends HttpFoundation\Request {

  function __construct() {

    parent::__construct(
      $_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER
    );

    $this->prepareRequestData();
  }

  /**
   * Applies to Request with Content-Type other than application/x-www-form-urlencoded
   * Replace POST request parameters with the content in the body of the request if possible
   */
  private function prepareRequestData() {
    $content_type = $this->headers->get('Content-Type');

    // _TODO: make it some how more extensible
    if (0 === strpos($content_type, 'application/json')) {
        $data = json_decode($this->getContent(), true);
        $this->request->replace(is_array($data) ? $data : array());
    } else if (0 === strpos($content_type, 'application/xml')) {
      // pending
    }
  } 

  /**
   * Check if current request is a Cross-Domain Request
   * @return boolean
   */
  public function isCrossDomain() {
    $origin = $this->headers->get('Origin');

    // A cross-domain request must have an 'Origin' header
    if (is_null($origin))
      return false;

    // Request with HTTP Method 'OPTIONS' is always Cross-Domain
    if ($this->getMethod() == 'OPTIONS')
      return true;

    // Finally, compare Origin and Host (protocol, hostname, port must exactly the same)
    // if they're different then it's a Cross-Domain Request
    $server_host = $this->getSchemeAndHttpHost().':'.$this->getPort();
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
   * Check if CORS Request from a specific host is allowed 
   *
   * Configure allowed hosts: (maybe in config/config.php)
   * $requestObject->setTrustedHosts(array('host1','host2');
   *
   * @return boolean
   */
  public function isCrossDomainAllowed() {
    
    // Host that make the request
    $origin = $this->headers->get('Origin', '');

    // List of trusted hosts
    $trusted_origins = $this->getTrustedHosts();

    foreach ($trusted_origins as $host_pattern) {
      if (preg_match($host_pattern, $origin)) {
        return true;
      }
    }

    return false;
  }
}
