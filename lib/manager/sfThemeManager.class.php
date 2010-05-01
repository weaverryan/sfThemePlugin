<?php
/**
 * Keeps track of the current theme and manages the changing of themes
 * 
 * There should be one theme manager per application configuration
 * 
 * @package     sfThemePlugin
 * @subpackage  theme
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @since       2010-03-27
 * @version     svn:$Id$ $Author$
 */
class sfThemeManager
{
  /**
   * Dependencies
   */
  protected
    $_context;

  /**
   * @var array   An array of all of the available themes and their configurations
   * @var string  The class name to use for theme objects
   */
  protected
    $_themes,
    $_themeClass;

  /*
   * @var string The name of the current theme
   * @var array An array of theme names that are available to be switched to
   * @var array  $_themeObjects  Array of the instantiated sfTheme objects
   */
  protected
    $_currentTheme,
    $_availableThemes,
    $_themeObjects;
    
  
  /**
   * @var boolean Whether or not the current theme has been loaded
   */
  protected $_isLoaded = false;

  /**
   * Class constructor
   * 
   * @param sfContext $context
   * @param array $themes   An array of theme configurations to be used as
   *                        the available themes to switch to
   */
  public function __construct(sfContext $context, $themes = array(), $themeClass = 'sfTheme')
  {
    $this->_context = $context;
    $this->_themes = $themes;
    $this->_themeClass = $themeClass;
  }

  /**
   * Sets the given theme as the current theme and loads it up
   * 
   * This does everything from setting the template to adding stylesheets
   * and javascripts
   * 
   * @param string $theme The name of the theme to load
   */
  public function setCurrentTheme($theme)
  {
    // don't load the theme if it's already the current theme
    if ($theme == $this->getCurrentTheme())
    {
      return;
    }
    
    // unload the current theme
    $this->_unloadCurrentTheme();
    
    // set the current theme and load it
    $this->_currentThemeName = $theme;
    $this->_loadCurrentTheme();
  }

  /**
   * Set/add a theme to the theme manager
   * 
   * The theme can be an sfTheme instance of just theme configuration
   * 
   * @param string $name The name of the theme
   * @param mixed $theme  Either an sfTheme object or an array of configuration
   *                      that can be used to create a theme object
   */
  public function setTheme($name, $theme)
  {
    if ($theme instanceof sfTheme)
    {
      $this->_themeObjects[$name] = $theme;
      $this->_themes[$name] = $theme->getConfig();
    }
    else
    {
      /*
       * Unset the theme object if it was already instantiated then add
       * the theme configuration to the array
       */
      unset($this->_themeObjects[$name]);
      $this->_themes[$name] = $theme;
    }
  }

  /**
   * Loads the current theme if not already loaded
   */
  protected function _loadCurrentTheme()
  {
    // don't load if we're already loaded or don't have a current theme
    if ($this->isLoaded() || !$theme = $this->getCurrentThemeObject())
    {
      return;
    }

    // Change the layout
    $this->_changeLayout($this->_getLayoutPath($theme->getLayout()));

    // Add theme stylesheets to response
    $this->_addStylesheets($theme->getStylesheets());

    // Add theme javascripts to response
    $this->_addJavascripts($theme->getJavascripts());

    // Invoke any callables
    $this->_invokeCallables($theme->getCallables());

    // Set loaded flag
    $this->_isLoaded = true;
  }
  
  /**
   * Unloads the current theme
   */
  protected function _unloadCurrentTheme()
  {
    if (!$theme = $this->getCurrentThemeObject())
    {
      return;
    }

    // Remove theme stylesheets
    $this->_removeStylesheets($theme->getStylesheets());

    // Remove theme javascripts
    $this->_removeJavascripts($theme->getJavascripts());

    $this->_isLoaded = false;
  }

  /**
   * Changes the current layout to the given layout path
   */
  protected function _changeLayout($layoutPath)
  {
    $info = pathinfo($layoutPath);
    $path = $info['dirname'].'/'.$info['filename'];
    
    $actionEntry = $this->_context->getController()->getActionStack()->getLastEntry();
    $module = $actionEntry ? $actionEntry->getModuleName() : $this->_context->getRequest()->getParameter('module');
    $action = $actionEntry ? $actionEntry->getActionName() : $this->_context->getRequest()->getParameter('action');

    // Set the layout for the given module & action
    sfConfig::set('symfony.view.'.$module.'_'.$action.'_layout', $path);

    // Set the layout on the 404 module & action
    $error404Action = sfConfig::get('sf_error_404_action');
    $error404Module = sfConfig::get('sf_error_404_module');
    sfConfig::set('symfony.view.'.$error404Module.'_'.$error404Action.'_layout', $path);

    // Set the layout on the secure module & action
    $secureAction = sfConfig::get('sf_secure_action');
    $secureModule = sfConfig::get('sf_secure_module');
    sfConfig::set('symfony.view.'.$secureModule.'_'.$secureAction.'_layout', $path);
  }

  /**
   * Adds the given stylesheets to the response object
   * 
   * @param array $stylesheets The stylesheets to add to the response
   */
  protected function _addStylesheets($stylesheets)
  {
    $response = $this->_context->getResponse();
    foreach ($stylesheets as $stylesheet)
    {
      $response->addStylesheet($stylesheet, 'last');
    }
  }

  /**
   * Adds the given javascripts to the response object
   * 
   * @param array $javascripts The javascripts to add to the response
   */
  protected function _addJavascripts($javascripts)
  {
    $response = $this->_context->getResponse();
    foreach ($javascripts as $javascript)
    {
      $response->addJavascript($javascript);
    }
  }

  /**
   * Calls the given array of callables
   * 
   * @param array $callables The array of callables to call
   */
  protected function _invokeCallables($callables)
  {
    foreach ($callables as $callable)
    {
      if (count($callable) > 1)
      {
        call_user_func($callable);
      }
      else
      {
        call_user_func($callable[0]);
      }
    }
  }

  /**
   * Removes the array of stylesheets from the response
   */
  protected function _removeStylesheets($stylesheets)
  {
    $response = $this->_context->getResponse();
    foreach ($stylesheets as $stylesheet)
    {
      $response->removeStylesheet($stylesheet);
    }
  }

  /**
   * Removes the array of javascripts from the response
   */
  protected function _removeJavascripts($javascripts)
  {
    $response = $this->_context->getResponse();
    foreach ($javascripts as $javascript)
    {
      $response->removeJavascript($javascript);
    }
  }

  /**
   * Returns whether or not the current theme has been loaded
   * 
   * @return boolean
   */
  public function isLoaded()
  {
    return $this->_isLoaded;
  }

  /**
   * Returns the name of the currently loaded theme
   * 
   * @return string
   */
  public function getCurrentTheme()
  {
    return $this->_currentThemeName;
  }

  /**
   * Returns the current theme object, if there is one
   * 
   * @return sfTheme or false if there is not current theme
   */
  public function getCurrentThemeObject()
  {
    return $this->getCurrentTheme() ? $this->getThemeObject($this->getCurrentTheme()) : false;
  }

  /**
   * Get the theme object for a given theme name
   *
   * @param string $name 
   * @return sfTheme $theme
   */
  public function getThemeObject($theme)
  {
    if (!isset($this->_themeObjects[$theme]))
    {
      if (!isset($this->_themes[$theme]))
      {
        throw new sfException(sprintf('Cannot find configuration for theme "%s"', $theme));
      }

      $themeClass = $this->_themeClass;
      $this->_themeObjects[$theme] = new $themeClass($this->_themes[$theme], $this->_getThemeToolkit());
    }

    return $this->_themeObjects[$theme];
  }

  /**
   * Get array of all themes and their configurations
   *
   * @return array $themes
   */
  public function getThemes()
  {
    return $this->_themes;
  }

  /**
   * Get array of all themes that are not disabled and available for selection
   *
   * @return array $availableThemes
   */
  public function getAvailableThemes()
  {
    if ($this->_availableThemes === null)
    {
      $themes = $this->getThemes();
      foreach ($themes as $name => $theme)
      {
        if (isset($theme['available']) && !$theme['available'])
        {
          continue;
        }
        $this->_availableThemes[$name] = $theme;
      }
    }

    return $this->_availableThemes;
  }

  /**
   * Returns the absolute path to the layout for a given layout name
   */
  protected function _getLayoutPath($layout)
  {
    if (!isset($this->_layoutPaths[$layout]))
    {
      $this->_layoutPaths[$layout] = $this->_findLayoutPath($layout);
    }

    return $this->_layoutPaths[$layout];
  }
  
  /**
   * Calculates the location of a layout, which could live in several locations.
   * 
   * Specifically, the layout file for a theme could live in any "templates"
   * file found in the application dir or any enabled plugins
   */
  protected function _findLayoutPath($layout)
  {
    $layouts = $this->_getThemeToolkit()->getLayouts();
    $path = array_search($layout, $layouts);

    if (!$path)
    {
      throw new InvalidArgumentException(sprintf(
        'Could not find layout "%s" in any "templates" directories. You may need to clear your cache.',
        $layout
      ));
    }

    if (!sfToolkit::isPathAbsolute($path))
    {
      $path = sfConfig::get('sf_root_dir').'/'.$path;
    }

    return $path;
  }

  /**
   * Returns the current sfThemeToolkit
   * 
   * @return sfThemeToolkit
   */
  protected function _getThemeToolkit()
  {
    return $this->_context->getConfiguration()
      ->getPluginConfiguration('sfThemePlugin')
      ->getThemeToolkit();
  }
}