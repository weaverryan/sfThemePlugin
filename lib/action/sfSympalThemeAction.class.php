<?php

/**
 * This class acts as an extension of sfComponent
 * 
 * @package     sfSympalThemePlugin
 * @subpackage  action
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @since       2010-03-27
 * @version     svn:$Id$ $Author$
 */
class sfSympalThemeActions extends sfSympalExtendClass
{
  /**
   * Load the given Sympal theme
   *
   * @param string $name 
   * @return void
   */
  public function loadTheme($name)
  {
    $this->getSympalContext()->loadTheme($name);
  }

  /**
   * Load a theme and if none given load the default theme
   *
   * @param string $name
   * @return void
   */
  public function loadThemeOrDefault($name)
  {
    if ($name)
    {
      $this->getSympalContext()->loadTheme($name);
    }
    else
    {
      $this->getSympalContext()->loadTheme(sfSympalConfig::get('default_theme'));
    }
  }

  /**
   * Load the default theme from your actions
   *
   * @return void
   */
  public function loadDefaultTheme()
  {
    $this->loadTheme(sfSympalConfig::get('default_theme'));
  }

  /**
   * Load the admin theme from your actions
   *
   * @return void
   */
  public function loadAdminTheme()
  {
    $this->loadTheme(sfSympalConfig::get('admin_theme', null, 'admin'));
  }

  /**
   * Load the theme for the current site
   *
   * @return void
   */
  public function loadSiteTheme()
  {
    $this->loadThemeOrDefault($this->getSympalContext()->getSite()->getTheme());
  }
}