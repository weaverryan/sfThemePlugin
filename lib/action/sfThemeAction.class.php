<?php

/**
 * This class acts as an extension of sfComponent
 * 
 * @package     sfThemePlugin
 * @subpackage  action
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @since       2010-03-27
 * @version     svn:$Id$ $Author$
 */
class sfThemeActions
{

  /**
   * The action instance that triggered the component.method_not_found event
   */
  protected $_action;

  /**
   * @var sfThemeController The theme controller instance to use
   */
  protected
    $_themeController;

  /**
   * Class constructor
   * 
   * @param sfThemeController The theme controller instance
   */
  public function __construct(sfThemeController $themeController)
  {
    $this->_themeController = $themeController;
  }

  /**
   * Listens to the component.method_not_found event to effectively
   * extend the actions class
   */
  public function listenComponentMethodNotFound(sfEvent $event)
  {
    $this->_action = $event->getSubject();
    $method = $event['method'];
    $arguments = $event['arguments'];

    if (method_exists($this, $method))
    {
      $result = call_user_func_array(array($this, $method), $arguments);

      $event->setReturnValue($result);

      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Load the given Sympal theme
   *
   * @param string $name 
   * @return void
   */
  public function loadTheme($name)
  {
    $this->_action
      ->getContext()
      ->getConfiguration()
      ->getPluginConfiguration('sfThemePlugin')
      ->getThemeManager()
      ->setCurrentTheme($name);
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
      $this->loadTheme($name);
    }
    else
    {
      $this->loadDefaultTheme();
    }
  }

  /**
   * Load the default theme from your actions
   *
   * @return void
   */
  public function loadDefaultTheme()
  {
    $defaultTheme = $this->_themeController->getOption('default_theme');
    if (!$defaultTheme)
    {
      throw new sfException('Cannot load default theme - no default theme set.');
    }
    
    $this->loadTheme($defaultTheme);
  }

  /**
   * Load the admin theme from your actions
   *
   * @return void
   */
  public function loadAdminTheme()
  {
    $adminTheme = $this->_themeController->getOption('admin_theme');
    if (!$adminTheme)
    {
      throw new sfException('Cannot load admin theme - no admin theme set.');
    }
    
    $this->loadTheme($adminTheme);
  }
}