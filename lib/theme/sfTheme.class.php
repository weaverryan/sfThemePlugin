<?php

/**
 * Represents a theme
 * 
 * @package     sfThemePlugin
 * @subpackage  theme
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @since       2010-03-27
 * @version     svn:$Id$ $Author$
 */
class sfTheme
{
  /**
   * @var array  The configuration array
   * @var string The absolute path to the layout for this theme
   */
  protected
    $_config,
    $_layoutPath;

  /**
   * @param array $config The array of configuration for this theme
   */
  public function __construct($config)
  {
    $this->_config = $config;
  }

  /**
   * Returns the absolute path to the layout for this theme
   */
  public function getLayoutPath()
  {
    if ($this->_layoutPath === null)
    {
      $this->_layoutPath = $this->_findLayoutPath();
    }

    return $this->_layoutPath;
  }
  
  /**
   * Calculates the location of the layout, which could live in several locations.
   * 
   * Specifically, the layout file for a theme could live in any "templates"
   * file found in the application dir or any enabled plugins
   */
  protected function _findLayoutPath()
  {
    $layout = $this->getConfig('layout');
    $sympalConfiguration = sfSympalConfiguration::getActive();

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
   * Returns a given config value or default if the config doesn't exist
   * 
   * You can also return all of the config by not passing any arguments
   * 
   * @param string $name    The name of the config
   * @param mixed $default  The default to return if the config doesn't exist
   * 
   * @return mixed
   */
  public function getConfig($name = null, $default = null)
  {
    if ($name === null)
    {
      return $this->_config;
    }
    else
    {
      return isset($this->_config[$name]) ? $this->_config[$name] : $default;
    }
  }
}