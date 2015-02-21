<?php
use Symfony\Component\HttpFoundation;

/**
 * Encapsulate all informations about a response
 **/
class Response implements HasHttpStatusCode {
  private $http_response; // HttpFoundation\Response object
  private $request;       // Request object
  private $responders;    // array contain functions corresponding to a response format (html, json ...)

  function __construct(Request $request) {
    $this->http_response = new HttpFoundation\Response();
    $this->request       = $request;
    $this->responders    = array();
  }

  /**
   * Setup a redirection
   * @param array $to array('controller' => ..., 'action' => '..')
   * @param array $params
   */
  public function redirect(array $to = array(), array $params = array()) {
    if (!$to['controller']) {
      // _TODO: Raise exception
      die("Controller must be provided!");
    }

    $controller = strtolower($to['controller']);
    $action = isset($to['action']) ? strtolower($to['action']) : "index";

    $path_name = "{$controller}_{$action}";
    $location = App::$router->getWebPath($path_name, $params);

    $this->http_response->setStatusCode(Response::HTTP_FOUND);
    $this->http_response->headers->set('Location', $location);  
  }

  /**
   * Register a responder for a corresponding format
   * @param string $format
   * @param callable $responder
   */
  public function respond_to($format, callable $responder) {
    if ($this->responders === null) {
      $this->responders = array();
    }

    // Register the responder
    $this->responders[$format] = $responder;
  }

  /**
   * Send the response to client by calling one of the repsonders.
   * If no corresponding responder is found ###Exception is raised.
   */
  public function respond() {
    $request       = $this->request;
    $http_response = $this->http_response;

    $accept_mimes = $request->getAcceptableContentTypes();
    // Response formats of the acceptable content types
    $formats = array_unique(array_map(function($mime) use ($request) {
      return $request->getFormat($mime); 
    }, $accept_mimes));
    $formats = array_filter($formats); // remove null values

    if (empty($this->responders)) { // No responder found
      $http_response->setStatusCode(Response::HTTP_NOT_FOUND);
      $http_response->send();
      return;
    }

    // Call the repsonder function which corresponds to the preferred format
    foreach ($formats as $format) {
      if (array_key_exists($format,$this->responders)) {
        $this->responders[$format]();
        $http_response->send();
        return;
      }
    }

    // Check if request accept any kind of content-type `*/*`
    if (in_array('*/*', $accept_mimes)) {
      $first_responder = reset($this->responders);
      $first_responder();
      $http_response->send();

    } else { // Cannot find any corresponding responder
      $http_response->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
      $http_response->send();
      return;
    }

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
