<?php

if (!isset($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

$projectPath = dirname(__FILE__).'/../fixtures/project';
require_once($projectPath.'/config/ProjectConfiguration.class.php');



//require_once(dirname(__FILE__).'/cleanup.php');

if (!isset($app))
{
  $configuration = new ProjectConfiguration($projectPath);
} else {
  $configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
  sfContext::createInstance($configuration);
}

require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

//require_once dirname(__FILE__).'/../../config/sfSympalPluginConfiguration.class.php';
//$plugin_configuration = new sfSympalPluginConfiguration($configuration, dirname(__FILE__).'/../..');