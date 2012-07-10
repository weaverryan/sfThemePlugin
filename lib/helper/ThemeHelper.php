<?php

/**
 * Evaluates and returns a partial.
 * The syntax is similar to the one of include_partial
 *
 * <b>Example:</b>
 * <code>
 *  echo get_partial('mypartial', array('myvar' => 12345));
 * </code>
 *
 * @param  string $templateName  partial name
 * @param  array  $vars          variables to be made accessible to the partial
 *
 * @return string result of the partial execution
 * @see    include_partial
 */
function get_theme_partial($templateName, $vars = array())
{
  $context = sfContext::getInstance();

  $actionName = '_'.$templateName;

  $class = 'sfThemePartialView';
  $current_theme =  $context->getConfiguration()
                            ->getPluginConfiguration('sfThemePlugin')
                            ->getThemeManager()
                            ->getCurrentTheme();
  $view = new $class($context, $current_theme, $actionName, '');
  $view->setPartialVars(true === sfConfig::get('sf_escaping_strategy') ? sfOutputEscaper::unescape($vars) : $vars);

  return $view->render();
}

/**
 * Evaluates and echoes a partial from current theme.
 * The partial name is composed as follows: 'mypartial'.
 * The partial file name is _mypartial.php and is looked for in sfTheme??Plugin/templates/.
 * For a variable to be accessible to the partial, it has to be passed in the second argument.
 *
 * <b>Example:</b>
 * <code>
 *  include_theme_partial('mypartial', array('myvar' => 12345));
 * </code>
 *
 * @param  string $templateName  partial name
 * @param  array  $vars          variables to be made accessible to the partial
 *
 * @see    get_partial, include_component
 */
function include_theme_partial($templateName, $vars = array())
{
  echo get_theme_partial($templateName, $vars);
}

?>
