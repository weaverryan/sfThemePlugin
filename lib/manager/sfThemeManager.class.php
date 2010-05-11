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
   * Array keeping track of the current js and css set on the response
   * for the current theme
   * 
   * This is important because the stylesheets and javascripts arrays on
   * a theme might have the alternate formatting (arrays), and we don't
   * want to have to process that again just to unset the assets when
   * switching themes
   */
  protected
    $_currentJavascripts = array(),
    $_currentStylesheets = array();
    
  
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
    // Make sure the theme object exists, this will trigger an exception if it does not
    $this->getThemeObject($theme);
    
    /*
     * Don't load the theme if it's already the current theme
     * 
     */
    if ($theme == $this->getCurrentTheme())
    {
      /*
       * We don't want to unload and reload the theme, if the theme hasn't
       * changed, but we do have to cover for the case where the action
       * is forwarded from one action to another with the same theme. In
       * that case, we still need to set the right layout for the new action
       */
      $this->_changeLayout($this->_getLayoutPath($this->getCurrentThemeObject()->getLayout()));
      
      return;
    }

    // unload the current theme
    $this->_unloadCurrentTheme();
    
    // set the current theme and load it
    $this->_currentTheme = $theme;
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
  public function addTheme($name, $theme)
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
    if ($this->_isLoaded || !$theme = $this->getCurrentThemeObject())
    {
      return;
    }

    // Change the layout
    $this->_changeLayout($this->_getLayoutPath($theme->getLayout()));

    // Add theme stylesheets to response
    $this->_currentStylesheets = $this->_addStylesheets($theme->getStylesheets());

    // Add theme javascripts to response
    $this->_currentJavascripts = $this->_addJavascripts($theme->getJavascripts());

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
    $this->_removeStylesheets($this->_currentStylesheets);
    $this->_currentStylesheets = array();

    // Remove theme javascripts
    $this->_removeJavascripts($this->_currentJavascripts);
    $this->_currentJavascripts = array();

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
   * @return an array of the stylesheets files just added
   */
  protected function _addStylesheets($stylesheets)
  {
    return $this->_addAssets('Stylesheet', $stylesheets);
  }

  /**
   * Adds the given javascripts to the response object
   * 
   * @param array $javascripts The javascripts to add to the response
   * @return an array of the javascripts files just added
   */
  protected function _addJavascripts($javascripts)
  {
    return $this->_addAssets('Javascript', $javascripts);
  }

  /**
   * Runs a series of add$Type statements by parsing the array of assets
   * and figuring out the correct options.
   * 
   * The assets array comes straight from app.yml, which has the same
   * format available for view.yml assets
   * 
   * The majority of this function taken from sfViewConfigHandler::addAssets()
   * 
   * @param string $type Either Stylesheet or Javascript
   */
  protected function _addAssets($type, $assets)
  {
    $method = 'add'.$type;
    $response = $this->_context->getResponse();

    $processedAssets = array();
    foreach ((array) $assets as $asset)
    {
      $position = 'last'; // default position to last
      if (is_array($asset))
      {
        $key = key($asset);
        $options = $asset[$key];
        if (isset($options['position']))
        {
          $position = $options['position'];
          unset($options['position']);
        }
      }
      else
      {
        $key = $asset;
        $options = array();
      }

      // Keep a full array of the assets and their options
      $processedAssets[] = array('file' => $key, 'position' => $position, 'options' => $options);
      
      // Keep a simple array of just the assets
      $assetFiles[] = $key;
      // Add the asset to the response
    }

    // Throw an event to allow for paths to be filtered at a low level
    $processedAssets = $this->_context->getEventDispatcher()->filter(
      new sfEvent($this, 'theme.filter_asset_paths'),
      $processedAssets
    )->getReturnValue();

    // Add the assets to the response
    $assetFiles = array();
    foreach ($processedAssets as $asset)
    {
      // Add the asset to the response
      $response->$method($asset['file'], $asset['position'], $asset['options']);
      
      // Record a simple array of the filenames, for returning
      $assetFiles[] = $asset['file'];
    }
    
    return $assetFiles;
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
   * Returns the name of the currently loaded theme
   * 
   * @return string
   */
  public function getCurrentTheme()
  {
    return $this->_currentTheme;
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

  /**
   * Creates a new instance of this class based on the available application configuration.
   * 
   * This should not be called directly - use sfThemePluginConfiguration::getThemeManager() instead
   * 
   * @return sfThemeController
   */
  public static function createInstance(sfContext $context)
  {
    $class = sfConfig::get('app_theme_manager_class', 'sfThemeManager');
    $themes = sfConfig::get('app_theme_themes', array());
    $themeClass = sfConfig::get('app_theme_theme_class', 'sfTheme');

    // Don't load themes that aren't enabled
    foreach ($themes as $key => $themeConfig)
    {
      if (isset($themeConfig['enabled']) && !$themeConfig['enabled'])
      {
        unset($themes[$key]);
      }
    }
    
    return new $class($context, $themes, $themeClass);
  }
}
