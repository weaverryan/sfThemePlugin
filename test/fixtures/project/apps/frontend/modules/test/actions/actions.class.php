<?php

class testActions extends sfActions
{
  // Helps test the extension of the actions class
  public function executeCustomTheme(sfWebRequest $request)
  {
    $theme = $request->getParameter('theme');
    $this->loadTheme($theme);
    
    $this->setTemplate('index');
  }

  // Helps test the extension of the actions class
  public function executeDefaultTheme(sfWebRequest $request)
  {
    // set the theme to something else, then back to the default
    $this->loadTheme('test_theme');
    $this->loadDefaultTheme();
    
    $this->setTemplate('index');
  }
}