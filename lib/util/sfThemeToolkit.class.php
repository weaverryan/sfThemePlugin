<?php
/**
 * General toolkit class for the theme plugin
 * 
 * @package     sfThemePlugin
 * @subpackage  util
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class sfThemeToolkit
{
  protected
    $_cacheDriver;
  
  protected
    $_layouts;

  /**
   * Get array of all layouts in the current project
   *
   * @return array $layouts
   */
  public function getLayouts()
  {
    if ($this->_layouts === null)
    {
      // check if it's in the cache
      if ($layouts = $this->getCache('configuration.layouts'))
      {
        $this->_layouts = unserialize($layouts);
      }
      else
      {
        $this->_layouts = $this->_generateLayoutsArray();
        $this->setCache('configuration.layouts', serialize($this->_layouts));
      }
    }

    return $this->_layouts;
  }

  /**
   * Builds and returns an array with the widget and validator to use with
   * any choice field for a theme
   *
   * @return array $widgetAndValidator
   */
  public function getThemeWidgetAndValidator()
  {
    $themes = sfApplicationConfiguration::getActive()
      ->getPluginConfiguration('sfThemePlugin')
      ->getThemeManager()
      ->getAvailableThemes();

    $options = array('' => '');
    foreach ($themes as $name => $theme)
    {
      $options[$name] = sfInflector::humanize($name);
    }
    $widget = new sfWidgetFormChoice(array(
      'choices'   => $options
    ));
    $validator = new sfValidatorChoice(array(
      'choices'   => array_keys($options),
      'required' => false
    ));

    return array('widget' => $widget, 'validator' => $validator);
  }

  /**
   * Set the cache driver on this toolkit to enable caching
   */
  public function setCacheDriver(sfCache $cacheDriver)
  {
    $this->_cacheDriver = $cacheDriver;
  }

  /**
   * Returns the cache object represented by the given key
   * 
   * @return mixed
   */
  public function getCache($key)
  {
    if ($this->_cacheDriver)
    {
      return $this->_cacheDriver->get($key);
    }
  }

  /**
   * Sets the given data to cache with the given key
   */
  public function setCache($key, $data)
  {
    if ($this->_cacheDriver)
    {
      $this->_cacheDriver->set($key, $data);
    }
  }

  /**
   * Find all the layouts that exist for this project and application
   *
   * @return array
   */
  protected function _generateLayoutsArray()
  {
    $layouts = array();
    foreach ($this->getPluginPaths() as $plugin => $path)
    {
      $path = $path.'/templates';
      $find = glob($path.'/*.php');
      if (is_array($find))
      {
        $layouts = array_merge($layouts, $find);
      }
    }

    $find = glob(sfConfig::get('sf_app_dir').'/templates/*.php');
    if (is_array($find))
    {
      $layouts = array_merge($layouts, $find);
    }

    $layoutsCache = array();
    foreach ($layouts as $path)
    {
      $info = pathinfo($path);
      $name = $info['filename'];
      // skip partial/component templates
      if ($name[0] == '_')
      {
        continue;
      }
      $path = str_replace(sfConfig::get('sf_root_dir').'/', '', $path);
      $layoutsCache[$path] = $name;
    }

    return $layoutsCache;
  }

  /**
   * Creates an instance of the toolkit class using the global configuration
   * variables.
   * 
   * This method should not normally be used directly. Instead, go through
   * sfThemeConfiguration::getThemeToolkit()
   * 
   * @return sfThemeToolkit
   */
  public static createInstance()
  {
    $class = sfConfig::get('app_theme_toolkit_class', 'sfThemeToolkit');
    
    $toolkit = new $class();
    
    // Set the cache driver if caching is enabled
    $cacheConfig = sfConfig::get('app_theme_cache');
    if ($cacheConfig['enabled'])
    {
      $class = $cacheConfig['class'];
      $options = isset($cacheConfig['options']) ? $cacheConfig['options'] : array();
      
      $cacheDriver = new $class($options);
      $toolkit->setCacheDriver($cacheDriver);
    }
    
    return $toolkit;
  }
}