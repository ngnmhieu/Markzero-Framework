<?php
namespace Markzero\Http;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Manage web application Session
 */
class Session extends HttpFoundation\Session\Session {

  /**
   * Return ParameterBag contains (flashed) old inputs from last (error) form
   * @return Symfony\Component\HttpFoundation\ParameterBag
   */
  public function getOldInputBag() {

    $flash = $this->getFlashBag();

    $inputBag = $flash->has('inputs') ? new ParameterBag($flash->get('inputs')) : new ParameterBag();
    
    return $inputBag;
  }

  /**
   * Return ParameterBag contains (flashed) errors
   * @return Symfony\Component\HttpFoundation\ParameterBag
   */
  public function getErrorBag() {

    $flash = $this->getFlashBag();

    $errorBag = $flash->has('errors') ? new ParameterBag($flash->get('errors')) : new ParameterBag();
    
    return $errorBag; 
  }


}
