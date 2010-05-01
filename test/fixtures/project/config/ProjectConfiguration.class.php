<?php

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->setPlugins(array('sfSympalThemePlugin'));
    $this->setPluginPath('sfSympalThemePlugin', dirname(__FILE__).'/../../../..');
    
    $this->enablePlugins(array('sfSympalThemeTestPlugin'));
  }
}
