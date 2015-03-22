<?php
use Symfony\Component\HttpFoundation;

/**
 * Encapsulate all informations about a response
 **/
class Response implements HasHttpStatusCode {
  private $http_response; // HttpFoundation\Response object
  private $request;       // Request object
  private $router;        // Router object
  private $responders;    // array contain functions corresponding to a response format (html, json ...)

  /**
   * @param Request  $request
   * @param Response $response
   */
  function __construct(Request $request, Router $router) {
    $this->http_response = new HttpFoundation\Response();
    $this->request       = $request;
    $this->router        = $router;
    $this->responders    = array();
  }

  /**
   * Setup a redirection
   * _TODO:  to Root? to a URL?
   * @param string $controller
   * @param string $action
   * @param array $params
   * @return Response $this
   */
  public function redirect($controller, $action, array $params = array()) {

    $location = $this->router->getWebpaths($controller, $action, $params)[0];

    // if non of the 3xx HTTP Status Code has been set, HTTP_FOUND 302 status code is set
    $status_code = $this->http_response->getStatusCode();
    if (!in_array($status_code, array(
      Response::HTTP_MULTIPLE_CHOICES,
      Response::HTTP_MOVED_PERMANENTLY,
      Response::HTTP_FOUND,
      Response::HTTP_SEE_OTHER,
      Response::HTTP_NOT_MODIFIED,
      Response::HTTP_USE_PROXY,
      Response::HTTP_RESERVED,
      Response::HTTP_TEMPORARY_REDIRECT,
      Response::HTTP_PERMANENTLY_REDIRECT
    ))) {
      $this->http_response->setStatusCode(Response::HTTP_FOUND, 'Redirecting');
    }
      
    $this->http_response->headers->set('Location', $location);

    return $this;
  }

  /**
   * Register a responder for a corresponding format
   * @param string $format
   * @param callable $responder
   * @return Response $this
   */
  public function respond_to($format, callable $responder) {
    if ($this->responders === null) {
      $this->responders = array();
    }

    // Register the responder
    $this->responders[$format] = $responder;

    return $this;
  }

  /**
   * Send the response to client by calling one of the repsonders.
   * If no corresponding responder is found ###Exception is raised.
   * @return Response $this
   */
  public function respond() {
    $request       = $this->request;
    $http_response = $this->http_response;
    $responders    = $this->responders;

    if (empty($responders)) { // No responder found
      $http_response->setStatusCode(Response::HTTP_NOT_FOUND);
      $http_response->send();
      return $this;
    }

    // Respond with the corresponding Content-type
    $accept_mimes = $request->getAcceptableContentTypes();
    foreach ($accept_mimes as $mime) {
      $format = $request->getFormat($mime);
      if ($format === null)
        continue;

      if (array_key_exists($format,$responders)) {
        $responders[$format]();
        $http_response->headers->set('Content-Type', $mime);
        $http_response->send();
        return $this;
      }
    }


    // Check if request accept any kind of content-type `*/*`
    if (in_array('*/*', $accept_mimes)) {
      $first_responder = reset($responders);
      $first_responder();
      $http_response->send();
    } 
    // Check if user want to respond to any requested content-type
    else if (array_key_exists('all', $responders)) {
      $responders['all']();
      $http_response->send();
    } 
    // Cannot find any corresponding responder
    else { 
      $http_response->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
      $http_response->send();
    }

    return $this;
  }

  /**
   * Set Access-Control-* Headers for Cross-Domain Request
   */
  public function setAccessControlHeaders() {
    $request = $this->request;

    if (!$request->isCrossDomain() || !$request->isCrossDomainAllowed()) {
      return $this;
    }

    $origin = $this->request->headers->get('Origin');
    $this->http_response->headers->set('Access-Control-Allow-Origin', $origin);
    $this->http_response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept');
    $this->http_response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE');
    $this->http_response->headers->set('Access-Control-Allow-Credentials', 'true');

    return $this;
  }

  /**
   * Delegate undefined methods to HttpFoundation\Response object
   */
  function __call($method, $args) {
    call_user_func_array(array($this->http_response, $method), $args);
  }

  /**
   * Delegate undefined attributes to HttpFoundation\Response object
   */
  function __get($attribute) {
    return $this->http_response->$attribute;
  }

}
