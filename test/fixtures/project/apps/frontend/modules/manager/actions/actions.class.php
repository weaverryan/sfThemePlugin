<?php

class managerActions extends sfActions
{

  // set to test_theme via app.yml modules
  public function executeForwardToSameTheme(sfWebRequest $request)
  {
    $this->forward('manager', 'otherAction');
  }

  // set to test_theme via app.yml modules
  public function executeOtherAction(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }
}
