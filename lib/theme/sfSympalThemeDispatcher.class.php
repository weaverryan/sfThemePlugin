<?php

/**
 * Responsible for determining the correct theme for a request, module, route, etc
 * 
 * @package     sfSympalThemePlugin
 * @subpackage  theme
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 */

class sfSympalThemeDispatcher
{

  /**
   * Attempts to return the theme for the current request
   * 
   * This first throws a sympal.theme.set_theme_from_request event, giving
   * anyone the opportunity to determine the theme. If this event is not
   * handled, we continue with some default rules for setting themes.
   * 
   * @return string The theme (defaults to the default theme)
   */
  protected function getThemeForRequest(sfContext $context)
  {
    $event = $context->getEventDispatcher()->notifyUntil(new sfEvent($this, 'sympal.theme.set_theme_from_request', array(
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

      if ($theme = $request->getParameter(sfSympalConfig::get('theme', 'theme_request_parameter_name', 'sf_sympal_theme')))
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
    
    return sfSympalConfig::get('theme', 'default_theme');
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
}