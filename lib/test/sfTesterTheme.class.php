<?php

/**
 * Tester class to help testing themes in functional tests
 * 
 * @package     sfThemePlugin
 * @subpackage  test
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class sfTesterTheme extends sfTester
{
  protected $themeManager;

  /**
   * Prepares the tester.
   */
  public function prepare()
  {
  }

  /**
   * Initializes the tester.
   */
  public function initialize()
  {
    $this->themeManager = $this->browser
      ->getContext()
      ->getConfiguration()
      ->getPluginConfiguration('sfThemePlugin')
      ->getThemeManager();
  }

  public function isCurrentTheme($theme)
  {
    $this->tester->is($this->themeManager->getCurrentTheme(), $theme, sprintf('"%s" is the currently set theme', $theme));
    
    return $this->getObjectToReturn();
  }
}