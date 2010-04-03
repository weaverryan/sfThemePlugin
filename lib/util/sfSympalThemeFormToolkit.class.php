<?php
/**
 * Form toolkit class for the theme plugin
 * 
 * @package     sfSympalThemePlugin
 * @subpackage  util
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class sfSympalThemeFormToolkit
{
  
  /**
   * Get the theme and widget validator
   *
   * @return array $widgetAndValidator
   */
  public function getThemeWidgetAndValidator()
  {
    $themes = sfSympalContext::getInstance()->getService('theme_manager')->getAvailableThemes();
    $options = array('' => '');
    foreach ($themes as $name => $theme)
    {
      $options[$name] = sfInflector::humanize($name);
    }
    $widget = new sfWidgetFormChoice(array(
      'choices'   => $options
    ));
    $validator = new sfValidatorChoice(array(
      'choices'   => array_keys($options),
      'required' => false
    ));

    return array('widget' => $widget, 'validator' => $validator);
  }
}