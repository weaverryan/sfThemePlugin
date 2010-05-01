<?php

/**
 * Acts as an extension of sfUser
 * 
 * @package     sfThemePlugin
 * @subpackage  user
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */
class sfThemeUser
{
  protected $_user;
  
  /**
   * Listens to the component.method_not_found event to effectively
   * extend the actions class
   */
  public function listenComponentMethodNotFound(sfEvent $event)
  {
    $this->_user = $event->getSubject();
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
   * Set the current theme for the users session
   *
   * @param string $theme
   * @return void
   */
  public function setCurrentTheme($theme)
  {
    $this->setAttribute('current_theme', $theme);
  }

  /**
   * Get the current theme for the users session
   *
   * @return string $theme
   */
  public function getCurrentTheme()
  {
    return $this->getAttribute('current_theme');
  }
}