<?php

/**
 * Responsible for determining the correct theme for a given context
 * based on request, module, routing and other variables
 * 
 * @package     sfhemePlugin
 * @subpackage  theme
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 */

class sfThemeController
{
  /**
   * @var array
   */
  protected $_options;

  /**
   * Class constructor. The available options include:
   *   * allow_changing_theme_by_url (default: true)
   *        Whether or not a theme can be changed via a request parameter
   * 
   *   * theme_request_parameter_name (default: sf_theme)
   *        The request parameter the triggers a theme change
   * 
   *   * default_theme (default: null)
   *        The theme to return if no theme match is found
   * 
   *   * modules
   *        A key-value array of module => theme name
   * 
   *   * routes
   *        A key-value array of route => theme name
   */
  public function __construct($options = array())
  {
    $this->_options = $options;
  }

  /**
   * Attempts to return the theme for the current request
   * 
   * This first throws a sympal.theme.set_theme_from_request event, giving
   * anyone the opportunity to determine the theme. If this event is not
   * handled, we continue with some default rules for setting themes.
   * 
   * @return string The theme (defaults to the default theme)
   */
  public function getThemeForRequest(sfContext $context)
  {
    $event = $context->getEventDispatcher()->notifyUntil(new sfEvent($this, 'theme.set_theme_from_request', array(
      'context' => $context,
    )));
    
    if ($event->isProcessed())
    {
      return $event->getReturnValue();
    }
    
    if (sfSympalConfig::get('theme', 'allow_changing_theme_by_url'))
    {
      $user = $context->getUser();
      $request = $context->getRequest();

      if ($theme = $request->getParameter(sfSympalConfig::get('theme', 'theme_request_parameter_name', 'sf_theme')))
      {
        $user->setCurrentTheme($theme);

        return $theme;
      }

      if ($theme = $user->getCurrentTheme())
      {
        return $theme;
      }
    }
    
    // Get the theme from module/route. False is a valid response (don't set theme)
    $module = $context->getModuleName();
    $route = $context->getRouting()->getCurrentRouteName();
    $theme = $this->getThemeFromConfig($module, $route);
    if ($theme || $theme === false)
    {
      return $theme;
    }
    
    return sfSympalConfig::get('theme', 'default_theme', false);
  }

  /**
   * Returns the theme based on the config and the module or route
   * 
   * @return string A matched theme name, or false
   */
  protected function getThemeFromConfig($module, $route)
  {
    $modules = sfSympalConfig::get('theme', 'modules', array());
    if (array_key_exists($module, $modules))
    {
      return $modules[$module];
    }
    
    $routes = sfSympalConfig::get('theme', 'routes', array());
    if (array_key_exists($module, $routes))
    {
      return $routes[$module];
    }
  }

  /**
   * Returns the array of options
   * 
   * @return array
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Returns a given option or default if the option doesn't exist
   * 
   * @param string $name    The name of the option
   * @param mixed $default  The default to return if the option doesn't exist
   * 
   * @return mixed
   */
  public function getOption($name, $default = null)
  {
    return isset($this->_options[$name]) ? $this->_options[$name] : $default;
  }

  /**
   * Creates a new instance of this class based on the available application configuration.
   * 
   * This should not be called directly - use sfThemePluginConfiguration::getThemeController() instead
   * 
   * @return sfThemeController
   */
  public function createInstance()
  {
    $class = sfConfig::get('app_theme_controller_class', 'sfThemeController');
    $options = sfConfig::get('app_theme_controller_options', array());
    
    return new $class($options);
  }
}