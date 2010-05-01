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
   * @var string
   * @var string
   */
  protected
    $_defaultTheme,
    $_adminTheme;

  /**
   * Class constructor
   * 
   * @param string $defaultTheme  The theme name that should be used as the default theme
   * @param string $adminTheme    The theme name that should be used as the admin theme
   */
  public function __construct($defaultTheme, $adminTheme = null)
  {
    $this->_defaultTheme = $defaultTheme;
    $this->_adminTheme = ($adminTheme !== null) ? $adminTheme : $defaultTheme;
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
    $this->loadTheme($this->_defaultTheme);
  }

  /**
   * Load the admin theme from your actions
   *
   * @return void
   */
  public function loadAdminTheme()
  {
    $this->loadTheme($this->_adminTheme);
  }
}