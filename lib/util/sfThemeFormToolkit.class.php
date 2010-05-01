<?php
/**
 * Form toolkit class for the theme plugin
 * 
 * @package     sfThemePlugin
 * @subpackage  util
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class sfThemeFormToolkit
{
  
  /**
   * Builds and returns an array with the widget and validator to use with
   * any choice field for a theme
   *
   * @return array $widgetAndValidator
   */
  public function getThemeWidgetAndValidator()
  {
    $themes = sfApplicationConfiguration::getActive()
      ->getPluginConfiguration('sfThemePlugin')
      ->getThemeManager()
      ->getAvailableThemes();

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