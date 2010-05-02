<?php

class controllerActions extends sfActions
{
  // Do nothing, let the default theme be used
  public function executeDefaultTheme(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

  // This action explicitly requests the "test_theme" theme
  public function executeExplicitTestTheme(sfWebRequest $request)
  {
    $this->loadTheme('test_theme');
    $this->setTemplate('index');
  }

  // Sets the test theme then forwards to the default theme
  public function executeTestThemeForwardDefaultTheme(sfWebRequest $request)
  {
    $this->loadTheme('test_theme');
    $this->forward('controller', 'defaultTheme');
  }

  // Sets the default theme (do nothing) then forwards to the test theme
  public function executeDefaultThemeForwardTestTheme(sfWebRequest $request)
  {
    $this->forward('controller', 'explicitTestTheme');
  }

  // An event listener on frontendConfiguration looks for this module and
  // sets to be the test_theme
  public function executeEventListener(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }
}