<?php
namespace Markzero\Http;

use Markzero\Http\Routing\Router;
use Symfony\Component\HttpFoundation;

/**
 * Encapsulate all informations about a response
 **/
class Response extends HttpFoundation\Response {
  /**
   * @var Markzero\Http\Request
   */
  private $request;       

  /**
   * @var Markzero\Http\Routing\Router
   */
  private $router;
  /**
   * array contain functions corresponding to a response format (html, json ...)
   * @var array<callable>
   */
  private $responders;

  /**
   * @const default Http status in a redirect response if not specified
   */
  const DEFAULT_REDIRECT_STATUS_CODE = Response::HTTP_FOUND;

  /**
   * @param Markzero\Http\Request
   * @param Markzero\Http\Routing\Router
   */
  function __construct(Request $request, Router $router) 
  {
    $this->request       = $request;
    $this->router        = $router;
    $this->responders    = array();

    parent::__construct();
  }

  /**
   * Setup a redirection
   * Modifies $headers by setting Location to the target location
   * Modifies $statusCode to the given
   *
   * @param string
   * @param string
   * @param array arguments to be replaced in the url
   * @param array query string parameters
   * @param int a valid Http status code
   * @param string Http status message
   * @return Markzero\Http\Response 
   */
  public function redirect($controller, $action, array $args = [], array $query_params = [], $status_code = null, $status_message = null)
  {

    $location = $this->router->getWebpaths($controller, $action, $args, $query_params)[0];

    if ($status_code != null) {

      $this->setStatusCode($status_code, $status_message);

    } else if (!$this->isRedirection()) { // if status code hasn't already set yet

      $this->setStatusCode(Response::DEFAULT_REDIRECT_STATUS_CODE, $status_message);

    }
      
    $this->headers->set('Location', $location);

    return $this;
  }

  /**
   * @param string $location
   */
  public function redirectUrl($location)
  {

    $status_code = $this->getStatusCode();

    if (!$this->isRedirection()) {
      $this->setStatusCode(Response::HTTP_FOUND);
    }

    $this->headers->set('Location', $location);

    return $this;
  }

  /**
   * Register a responder for a corresponding format
   * @param string The coresponding format
   * @param callable
   * @return Markzero\Http\Response current object
   */
  public function respondTo($format, callable $responder) 
  { 
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
   *
   * @param bool send response without any content
   * @return Markzero\Http\Response current object
   */
  public function respond($no_content = false)
  {
    $request       = $this->request;
    $responders    = $this->responders;

    if ($no_content) {
      $this->send();
      return $this;
    }

    if (empty($responders)) { // No responder found
      $this->setStatusCode(Response::HTTP_NOT_FOUND);
      $this->send();
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
        $this->headers->set('Content-Type', $mime);
        $this->send();
        return $this;
      }
    }

    // Check if request accept any kind of content-type `*/*`
    if (in_array('*/*', $accept_mimes)) {
      $first_responder = reset($responders);
      $first_responder();
      $this->send();
    } 
    // Check if user want to respond to any requested content-type
    else if (array_key_exists('all', $responders)) {
      $responders['all']();
      $this->send();
    }
    // Cannot find any corresponding responder
    else { 
      $this->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
      $this->send();
    }

    return $this;
  }

  /**
   * Set Access-Control-* Headers for Cross-Domain Request
   * @return Markzero\Http\Response current object
   */
  public function setAccessControlHeaders() 
  {
    $request = $this->request;

    if (!$request->isCrossDomain() || !$request->isCrossDomainAllowed()) {
      return $this;
    }

    $origin = $this->request->headers->get('Origin');
    $this->headers->set('Access-Control-Allow-Origin', $origin);
    $this->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept');
    $this->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE');
    $this->headers->set('Access-Control-Allow-Credentials', 'true');

    return $this;
  }
}
