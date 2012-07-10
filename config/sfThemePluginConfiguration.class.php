<?php

/**
 * Plugin configuration for the theme plugin
 *
 * @package     sfThemePlugin
 * @subpackage  config
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @since       2010-03-27
 * @version     svn:$Id$ $Author$
 */
class sfThemePluginConfiguration extends sfPluginConfiguration
{
  /**
   * @var sfContext Stored here so we can access it in the controller.change_action
   *                event because the controller, frustratingly, doesn't
   *                have an accessor for its context property.
   */
  protected
    $_context;

  /**
   * @var sfThemeManager    The manager instance for the application
   * @var sfThemeController The controller instance for the application
   * @var sfThemeToolkit    The toolkit instance for the application
   */
  protected
    $_themeManager,
    $_themeController,
    $_themeToolkit;

  /**
   * Initializes the plugin
   */
  public function initialize()
  {
    // Only bootstrap if theming is enabled
    if (sfConfig::get('app_theme_enabled', false))
    {
      $this->dispatcher->connect('context.load_factories', array($this, 'bootstrap'));

      // Add a listener on controller.change_action - key to setting correct theme
      $this->dispatcher->connect('controller.change_action', array($this, 'listenControllerChangeAction'));

      // extend the user class
      $themeUser = new sfThemeUser();
      $this->dispatcher->connect('user.method_not_found', array($themeUser, 'listenUserMethodNotFound'));

      // Add listener on template.filter_parameters to add sf_theme var to view
      $this->dispatcher->connect('template.filter_parameters', array($themeUser, 'filterTemplateParameters'));

      // extend the actions class
      $actionObject = new sfThemeActions($this->getThemeController());
      $this->dispatcher->connect('component.method_not_found', array($actionObject, 'listenComponentMethodNotFound'));

      // register the web debug panel
      $this->dispatcher->connect('debug.web.load_panels', array($this, 'listenDebugWebLoadPanels'));

      // loading helpers
      $this->configuration->loadHelpers(array('Theme', 'I18N'));

    }
  }

  /**
   * Listens to context.load_factories. Bootstraps the plugin
   */
  public function bootstrap(sfEvent $event)
  {
    // store the context so we can use it for other listeners
    $this->_context = $event->getSubject();

    // create the theme manager instance
    $this->_themeManager = sfThemeManager::createInstance($this->_context);

    // Refresh the theme from the context
    $this->_refreshTheme();

    // throw a post-bootstrap event. 
    $event = new sfEvent($this, 'sf_theme.bootstrap');
    $this->dispatcher->notify($event);
  }

  /**
   * Listens to the controller.change_action event to set the correct theme
   * when the action changes
   *
   * @param sfEvent $event The controller.change_action event object
   */
  public function listenControllerChangeAction(sfEvent $event)
  {
    // Refresh the theme from the context
    $this->_refreshTheme();
  }

  /**
   * Re-determines the correct theme that should be used based off of a
   * variety of information on the context and then sets the theme if
   * one is found
   */
  protected function _refreshTheme()
  {
    $theme = $this->getThemeController()->getThemeForRequest(
      $this->_context,
      array_keys($this->getThemeManager()->getAvailableThemes())
    );

    // If we found a theme we should use, set it on the theme manager
    if ($theme)
    {
      $this->getThemeManager()->setCurrentTheme($theme);
    }
  }

  /**
   * Returns the current sfThemeManager instance for the application
   *
   * The theme manager is loaded automatically on context.load_factories
   *
   * @return sfThemeManager
   */
  public function getThemeManager()
  {
    if ($this->_themeManager === null)
    {
      throw new sfException('No theme manager instance found');
    }

    return $this->_themeManager;
  }

  /**
   * Returns the theme controller object, which is responsible for figuring
   * out what theme should be used based on a variety of variables
   *
   * @return sfThemeController
   */
  public function getThemeController()
  {
    if ($this->_themeController === null)
    {
      $this->_themeController = sfThemeController::createInstance();
    }

    return $this->_themeController;
  }

  /**
   * Returns the theme toolkit, which is a general-purpose worker class
   */
  public function getThemeToolkit()
  {
    if ($this->_themeToolkit === null)
    {
      $this->_themeToolkit = sfThemeToolkit::createInstance($this->configuration);
    }

    return $this->_themeToolkit;
  }

  // Listens on the debug.web.load_panels event, adds the web debug panel
  public function listenDebugWebLoadPanels(sfEvent $event)
  {
    if (sfConfig::get('app_theme_web_debug', true))
    {
      $event->getSubject()->setPanel('themes', new sfThemeWebDebugPanel($event->getSubject()));
    }
  }
}
