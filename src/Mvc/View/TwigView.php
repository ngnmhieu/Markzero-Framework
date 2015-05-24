<?php

namespace Markzero\Mvc\View;

/**
 * Adapter for Twig Templating Engine
 *
 * In order to use, $TEMPLATE_PATHS must be configured
 * (through static methods TwigView::setTemplatePath() and (optional) TwigView::configEnvironment())
 */
class TwigView extends AbstractView 
{

  protected static $TEMPLATE_PATHS = null; 

  protected static $ENV_CONFIGURATOR = null;

  /**
   * @var string
   */
  protected $view_path;
  /**
   * @var array
   */
  protected $data;
  
  /**
   * @param string
   * @param array
   * @param string|array
   */
  public function __construct($name, array $data = array(), $cust_tpl_path = null) 
  {
    $tpl_path = $cust_tpl_path === null ? static::$TEMPLATE_PATHS : $cust_tpl_path;

    if ($tpl_path === null || empty($tpl_path)) {
      throw new Exception("No view path is specified. Please specify it with either TwigView::setTemplatePath or through TwigView constructor.");
    }
    
    $loader = new \Twig_Loader_Filesystem($tpl_path);

    $twig = new \Twig_Environment($loader);

    static::defaultConfigure($twig);

    if (is_callable(static::$ENV_CONFIGURATOR)) {
      call_user_func(static::$ENV_CONFIGURATOR, $twig);
    }

    $this->setContent($twig->render($name, $data));
  }

  /**
   * Apply default configurations to \Twig_Environment object
   * like add functions, filters, tags, ...
   *
   * @param \Twig_Environment
   */
  protected static function defaultConfigure(\Twig_Environment $twig) 
  {
    $functions = array(
      'webpath' => 'webpath'
    );

    foreach ($functions as $name => $callable) {
      $twig->addFunction(new \Twig_SimpleFunction($name, $callable));
    }
  }

  /**
   * Expose \Twig_Environment object, so that it could be configured from outside
   *
   * @param callable A closure that called after a \Twig_Environment object is initialized
   *                 and is passed that \Twig_Environment object
   */
  public static function configEnvironment(callable $callback) 
  {
    static::$ENV_CONFIGURATOR = $callback;
  }

  /**
   * @param array Path(s) where Twig templates can be found
   */
  public static function setTemplatePaths(array $paths) 
  {
    static::$TEMPLATE_PATHS = $paths;
  }

}
