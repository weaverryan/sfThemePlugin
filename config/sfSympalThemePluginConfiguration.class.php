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
  protected
    $_sympalContext;

  public function initialize()
  {
    // Only bootstrap if theming is disabled
    if (sfSympalConfig::get('theme', 'enabled'))
    {
      $this->dispatcher->connect('sympal.load', array($this, 'bootstrap'));
      $this->dispatcher->connect('controller.change_action', array($this, 'listenControllerChangeAction'));
    }
    
    $themeUser = new sfSympalThemeUser();
    $this->dispatcher->connect('user.method_not_found', array($themeUser, 'extend'));

    // Connect to the sympal.load_admin_menu event
    $this->dispatcher->connect('sympal.load_admin_menu', array($this, 'setupAdminMenu'));

    // Connect to the sympal.load_config_form evnet
    $this->dispatcher->connect('sympal.load_config_form', array($this, 'loadConfigForm'));
  }
  
  /**
   * Listens to sympal.load. Bootstraps the plugin
   */
  public function bootstrap(sfEvent $event)
  {
    $this->_sympalContext = $event->getSubject();
    
    // extend the actions class to sfSympalThemeActions
    $actionObject = new sfSympalThemeActions();
    $this->dispatcher->connect('component.method_not_found', array($actionObject, 'extend'));
    
    $themeDispatcher = $this->_sympalContext->getService('theme_dispatcher');
    $theme = $themeDispatcher->getThemeForRequest($this->_sympalContext->getSymfonyContext());
    
    if ($theme)
    {
      $this->_sympalContext->getService('theme_manager')->setCurrentTheme($theme);
    }
  }

  /**
   * Listens to the controller.change_action event to set the correct theme
   * when the action changes
   * 
   * @param sfEvent $event The controller.change_action event object
   */
  public function listenControllerChangeAction(sfEvent $event)
  {
    $sympalContext = sfSympalContext::getInstance();
    $themeDispatcher = $sympalContext->getService('theme_dispatcher');
    $theme = $themeDispatcher->getThemeForRequest($sympalContext->getSymfonyContext());
  }

  /**
   * Listens to the sympal.load_admin_menu to configure the admin menu
   */
  public function setupAdminMenu(sfEvent $event)
  {
    $menu = $event->getSubject();
    
    $administration = $menu->getChild('administration');
    
    $administration->addChild('Themes', '@sympal_themes')
      ->setCredentials(array('ManageThemes'));
  }

  /**
   * Listens to the sympal.load_config_form and allows for customization
   * of the config form
   */
  public function loadConfigForm(sfEvent $event)
  {
    $form = $event->getSubject();

    $array = $this->_sympalContext->getService('theme_form_toolkit')->getThemeWidgetAndValidator();
    $form->addSetting('theme', 'default_theme', 'Default Theme', $array['widget'], $array['validator']);
  }
}