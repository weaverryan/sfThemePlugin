<?php

/**
 * Plugin configuration for the theme plugin
 * 
 * @package     sfSympalThemePlugin
 * @subpackage  config
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @since       2010-03-27
 * @version     svn:$Id$ $Author$
 */
class sfSympalThemePluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    // Only bootstrap if theming is disabled
    if (sfSympalConfig::get('theme', 'enable'))
    {
      $this->dispatcher->connect('context.load_factories', array($this, 'bootstrap'));
    }
  }
  
  /**
   * Bootstraps the plugin
   */
  public function bootstrap(sfEvent $event)
  {
    // extend the actions class to sfSympalThemeActions
    $actionObject = new sfSympalThemeActions();
    $this->dispatcher->connect('component.method_not_found', array($actionObject, 'extend'));
    
    $manager = new sfSympalThemeManager($event->getSubject());
    $event->getSubject()->set('theme_manager', $manager);
  }
}