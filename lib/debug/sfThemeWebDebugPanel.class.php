<?php

/**
 * Web debug panel that shows the current theme and allows switching of themes
 * 
 * @package     sfthemePlugin
 * @subpackage  debug
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */
class sfThemeWebDebugPanel extends sfWebDebugPanel
{
  public function getTitle()
  {
    return '<img src="/sfThemePlugin/images/theme.png" alt="Theme Management" height="16" width="16" /> themes';
  }

  public function getPanelTitle()
  {
    return 'Themes';
  }

  /**
   * Returns the actual content to be used when the theme drops down
   */
  public function getPanelContent()
  {
    $context = sfContext::getInstance();
    $manager = $context->getConfiguration()
      ->getPluginConfiguration('sfThemePlugin')
      ->getThemeManager();
    $controller = $this->_getController();

    $content = '';

    // Put up warning about the theme in the user session
    if ($context->getUser()->getCurrentTheme())
    {
      $this->setStatus(sfLogger::WARNING);
      
      $content .= sprintf(
        '<h2>
          --> Theme "%s" is currently being used for this session
          %s
        </h2>',
        $context->getUser()->getCurrentTheme(),
        $this->_getThemeSwitchLink('clear', 'Reset theme')
      );
    }

    $availableThemes = array_keys($manager->getAvailableThemes());

    // Create a table of the themes
    $panel = '<table class="sfWebDebugLogs" style="width: 300px"><tr><th>theme name</th><th>available</th><th>current theme?</th><th>Switch</th></tr>';
    foreach ($manager->getThemes() as $theme => $config)
    {
      $themeToggler = $this->getToggler('web_debug_details_'.$theme, $theme);
      $available = $this->_getBooleanSpan(in_array($theme, $availableThemes));
      $current = $this->_getBooleanSpan($theme == $manager->getCurrentTheme());
      
      if ($controller->getOption('allow_changing_theme_by_url'))
      {
        $switch = (in_array($theme, $availableThemes) && !($theme == $manager->getCurrentTheme())) ? $this->_getThemeSwitchLink($theme, 'switch') : '';
      }
      else
      {
        $switch = '<i>url theme changing disabled</i>';
      }
      
      $panel .= sprintf('<tr><td class="sfWebDebugLogType">%s %s</td><td style="text-align: center">%s</td><td style="text-align: center">%s</td><td style="text-align: center">%s</td></tr>', $themeToggler, $theme, $available, $current, $switch);

      $panel .= sprintf('<tr id="web_debug_details_%s" style="display: none;"><td colspan="4">%s</td></tr>', $theme, self::_arrayToList($config));
    }
    $content .= $panel;
    
    return $content;
  }

  /**
   * Returns the colored span that relates to either a "yes", "no" or "unknown"
   * 
   * @param boolean $status true, false or null for "yes", "no" or "unknown"
   */
  protected function _getBooleanSpan($bool)
  {
    if ($bool === true)
    {
      $txt = 'Y';
      $color = 'darkgreen';
    }
    elseif ($bool === false)
    {
      $txt = 'X';
      $color = 'darkred';
    }
    else
    {
      $txt = '?';
      $color = 'lightblue';
    }
    
    return sprintf('<span style="color: %s; font-weight: bold; font-size: 1.2em;">%s</span>', $color, $txt);
  }

  /**
   * Turns an array into an unordered key: val list. This call itself recursively
   * to create multi-dimensional lists
   */
  protected static function _arrayToList($arr, $createNewList = true)
  {
    if (!is_array($arr))
    {
      return $arr;
    }
    
    if (count($arr) == 0)
    {
      return '<i>empty array</i>';
    }

    $list = '';
    foreach ($arr as $key => $val)
    {
      /*
       * special case (affects css) where an entry in an array is just an
       * indexed entry to another array. It actually makes most sense
       * visibly if we collapse the next level array into this level
       */
      if (is_numeric($key) && is_array($val))
      {
        $list .= self::_arrayToList($val, !is_numeric($key));
      }
      else
      {
        $list .= '<li>';
          if (!is_numeric($key))
          {
            $list .= '<b>'.$key.'</b>: ';
          }
        
          $list .= self::_arrayToList($val, !is_numeric($key));
        $list .= '</li>';
      }
    }
    
    if ($createNewList)
    {
      $list = sprintf('<ul>%s</ul>', $list);
    }
    
    return $list;
  }

  /**
   * Creates a link to switch to the given theme
   */
  protected function _getThemeSwitchLink($theme, $text = null)
  {
    if ($text === null)
    {
      $text = $theme;
    }
    
    $param = $this->_getController()->getOption('theme_request_parameter_name');
    
    return sprintf('<a href="?%s=%s" title="%s">%s</a>', $param, $theme, $text, $text);
  }

  protected function _getController()
  {
    $context = sfContext::getInstance();
    
    return $context->getConfiguration()
      ->getPluginConfiguration('sfThemePlugin')
      ->getThemeController();
  }
}




