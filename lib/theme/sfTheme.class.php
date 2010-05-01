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

  public function getLayout()
  {
    return $this->getConfig('layout');
  }

  public function getStylesheets()
  {
    return $this->getConfig('stylesheets', array());
  }

  public function getJavascripts()
  {
    return $this->getConfig('javascripts', array());
  }

  public function getCallables()
  {
    return $this->getConfig('callables', array());
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