<?php

class frontendConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
    $this->dispatcher->connect('theme.set_theme_from_request', array($this, 'listenSetThemeFromRequest'));
  }

  // If the module/action is controller/eventListener, we hijack and set to test_theme
  public function listenSetThemeFromRequest(sfEvent $event)
  {
    $context = $event['context'];
    
    if ($context->getModuleName() == 'controller' && $context->getActionName() == 'eventListener')
    {
      $event->setReturnValue('test_theme');
      
      return true;
    }
    
    return false;
  }
}
