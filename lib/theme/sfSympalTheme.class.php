<?php

/**
 * Represents a theme
 * 
 * @package     sfSympalThemePlugin
 * @subpackage  theme
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @since       2010-03-27
 * @version     svn:$Id$ $Author$
 */
class sfSympalTheme
{
  /**
   * @var string The name of the plugin
   * @var array  The configuration array
   * @var string The absolute path to the layout for this theme
   */
  protected
    $_name,
    $_configuration,
    $_layoutPath;

  /**
   * @param array $configuration The array of configuration for this theme
   */
  public function __construct($name, $configuration)
  {
    $this->_name = $name;
    $this->_configuration = $configuration;
  }

  public function getName()
  {
    return $this->_name;
  }

  public function getLayout()
  {
    return isset($this->_configuration['layout']) ? $this->_configuration['layout'] : $this->getName();
  }

  /**
   * Returns the absolute path to the layout for this theme
   */
  public function getLayoutPath()
  {
    if ($this->_layoutPath === null)
    {
      $this->_layoutPath = $this->getLayoutPath();
    }

    return $this->_layoutPath;
  }

  public function getStylesheets()
  {
    return isset($this->_configuration['stylesheets']) ? $this->_configuration['stylesheets'] : array($this->_findStylesheetPath());
  }

  public function getJavascripts()
  {
    return isset($this->_configuration['javascripts']) ? $this->_configuration['javascripts'] : array();    
  }

  public function getCallables()
  {
    return isset($this->_configuration['callables']) ? $this->_configuration['callables'] : array();
  }

  /**
   * Returns the name of this plugin
   * 
   * @return string
   */
  public function __toString()
  {
    return $this->_configuration->getName();
  }
  
  /**
   * Calculates the location of the layout, which could live in several locations.
   * 
   * Specifically, the layout file for a theme could live in any "templates"
   * file found in the application dir or any enabled plugins
   */
  protected function _findLayoutPath()
  {
    $layout = $this->getLayout();
    $sympalConfiguration = sfSympalContext::getInstance()->getSympalConfiguration();

    $layouts = $sympalConfiguration->getLayouts();
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
   * If no stylesheets are defined, this returns some default css to
   * load automatically.
   * 
   * The css will be THEME_NAME.css, and can live in a variety of dirs
   * 
   * @TODO Consider removing this idea or part of it - this feels magical.
   */
  protected function _findStylesheetPath()
  {
    $name = $this->getName();
    if (strpos($this->getLayoutPath(), 'sfSympalPlugin/templates') !== false)
    {
      return '/sfSympalPlugin/css/' . $name . '.css';
    }
    else
    {
      if (is_readable(sfConfig::get('sf_web_dir').'/css/'.$name.'.css'))
      {
        return $name;
      }
      else
      {
        $configuration = sfContext::getInstance()->getConfiguration();
        $pluginPaths = $configuration->getAllPluginPaths();

        foreach ($pluginPaths as $plugin => $path)
        {
          if (file_exists($path.'/web/css/'.$name.'.css'))
          {
            return '/'.$plugin.'/css/'.$name.'.css';
          }
        }
      }
    }
  }
}