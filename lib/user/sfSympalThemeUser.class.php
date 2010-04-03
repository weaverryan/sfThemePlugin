<?php

/**
 * Acts as an extension of sfUser
 * 
 * @package     sfSympalThemePlugin
 * @subpackage  user
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */
class sfSympalThemeUser extends sfSympalExtendClass
{
  /**
   * Set the current theme for the users session
   *
   * @param string $theme
   * @return void
   */
  public function setCurrentTheme($theme)
  {
    $this->setAttribute('sympal_current_theme', $theme);
  }

  /**
   * Get the current theme for the users session
   *
   * @return string $theme
   */
  public function getCurrentTheme()
  {
    return $this->getAttribute('sympal_current_theme');
  }
}