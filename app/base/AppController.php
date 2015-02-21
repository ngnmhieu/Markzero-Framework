<?php
use Symfony\Component\HttpFoundation;

class AppController {
  private $request;
  private $response;
  private $responders;

  /*
   * Every child class should call this constructor
   * if it has its own constructor
   */
  function __construct() {
    $this->request = new HttpFoundation\Request(
      $_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER
    );

    $this->response = new Response();

    $this->responders = array();
  }

  /**
   * @return Request | Application Request object
   **/
  public function request() { return $this->request; }

  /**
   * @return Response | Application Request object
   **/
  public function response() { return $this->response; }

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
  public function send() {
    $request = $this->request;

    $accept_mimes = $request->getAcceptableContentTypes();
    // Response formats of the acceptable content types
    $formats = array_unique(array_map(function($mime) use ($request) {
      return $request->getFormat($mime); 
    }, $accept_mimes));
    $formats = array_filter($formats); // remove null values

    if (empty($this->responders)) {
      echo "No Responder Registered.\n";
      // _TODO: Raise exception or not? some request does not need response
      return;
    }

    // Call the repsonder function
    // which corresponds to the preferred format
    foreach ($formats as $format) {
      $responder = $this->responders[$format];
      if (is_callable($responder)) {
        $responder();
        return;
      }
    }

    // Cannot find any corresponding responder
    // check if request accept any kind of content-type `*/*`
    if (in_array('*/*', $accept_mimes)) {
      $first_responder = reset($this->responders);
      $first_responder();
    } else {
      echo "No Suitable Reponder.\n";
      // _TODO: Raise Exception
      return;
    }

  }

  /**
   * @return string | name of current controller
   */
  protected function name() {
    preg_match("/([a-zA-Z]+)Controller/", get_class($this), $matches);
    $controller = strtolower($matches[1]);
    return $controller;
  } 
}
