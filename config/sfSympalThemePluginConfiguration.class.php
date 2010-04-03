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
    if (sfSympalConfig::get('theme', 'enabled'))
    {
      $this->dispatcher->connect('sympal.load', array($this, 'bootstrap'));
    }
    
    $themeUser = new sfSympalThemeUser();
    $this->dispatcher->connect('user.method_not_found', array($themeUser, 'extend'));
  }
  
  /**
   * Bootstraps the plugin
   */
  public function bootstrap(sfEvent $event)
  {
    // extend the actions class to sfSympalThemeActions
    $actionObject = new sfSympalThemeActions();
    $this->dispatcher->connect('component.method_not_found', array($actionObject, 'extend'));
    
    $themeDispatcher = $event->getSubject()->getService('theme_dispatcher');
    $theme = $themeDispatcher->getThemeForRequest($event->getSubject()->getSymfonyContext());
    
    if ($theme)
    {
      $event->getSubject()->getService('theme_manager')->setCurrentTheme($theme);
    }
    
    /**
     * @TODO Need to reimplement sfSympalConfiguration::getThemeForRequest()
     * and then load that theme on context.load_factories.
     * 
     * This replaces some code removed from core
     */
    
    /**
     * @TODO Reimplement the listener on controller.change_action which
     * reloads the theme.
     * 
     * See sfSympalControllerChangeActionListener
     */
    
    /**
     * @TODO sfSympalConfiguration::getThemes() and getAvailableThemes()
     * need to get put back
     */
  }
}