<?php

// Bringing in functional to have context for the createInstance() method
require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../bootstrap/functional.php');

$t = new lime_test(4);

$t->info('1 - Test the basics, like getOptions()');

  $controller = new sfThemeController(array('test' => 'foo'));
  $t->is($controller->getOption('test', 'ignore'), 'foo', '->getOption() returns existent options');
  $t->is($controller->getOption('fake', 'default'), 'default', '->getOption() returns the default for nonexistent options');


$t->info('2 - Test the createInstance() method');

  $controller = sfThemeController::createInstance();
  $t->is(get_class($controller), 'sfThemeTestController', 'Class is sfThemeTestController, as set in app.yml');
  $t->is($controller->getOption('default_theme'), 'app_test', 'Controller options were loaded correctly');