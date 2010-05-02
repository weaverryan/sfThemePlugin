<?php

/**
 * Web debug panel that shows the current theme and allows switching of themes
 * 
 * @package     sfthemePlugin
 * @subpackage  debug
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */
class sfThemeWebDebugPanel extends sfWebDebugPanel
{
  public function getTitle()
  {
    return 'img-theme';
  }

  public function getPanelTitle()
  {
    return 'Themes';
  }

  public function getPanelContent()
  {
    return 'content';
  }
}