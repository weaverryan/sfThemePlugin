<?php

// Bring in functional so we have an application configuration
// This will help us to find layouts etc that are in the application
require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../bootstrap/functional.php');

$t = new lime_test(5);

// Setup a cache driver
$cachePath = '/tmp/theme_tookit';
sfToolkit::clearDirectory($cachePath);
$cacheDriver = new sfFileCache(array(
  'cache_dir' => $cachePath,
));

$toolkit = new sfThemeToolkit($configuration);
$toolkit->setCacheDriver($cacheDriver);
$t->is($toolkit->getCacheDriver(), $cacheDriver, '->getCacheDriver() returns the correct object');


$t->info('1 - Test getLayouts()');

  $layouts = array(
    'plugins/sfThemeTestPlugin/templates/plugin_test_layout.php' => 'plugin_test_layout',
    'apps/frontend/templates/app_test_layout.php' => 'app_test_layout',
    'apps/frontend/templates/layout.php' => 'layout',
  );
  $t->is($toolkit->getLayouts(), $layouts, '->getLayouts() returns 2 layouts from the app and 1 from a plugin');

  $t->info('  1.1 - Mutate the cache, it should return the mutated version');
  $cacheDriver->set('theme.configuration.layouts', serialize(array('/path/to/layout.php' => 'layout')));
  
  // Need to create a new toolkit class, because the layous are "cached" as a property on the object
  $toolkit = new sfThemeToolkit($configuration);
  $toolkit->setCacheDriver($cacheDriver);
  $t->is($toolkit->getLayouts(), array('/path/to/layout.php' => 'layout'), '->getLayouts() returns from cache');


$t->info('2 - Test the createInstance() static method');

  $toolkit = sfThemeToolkit::createInstance($configuration);
  $t->is(get_class($toolkit), 'sfThemeTestToolkit', 'The toolkit has the class defined in app.yml');
  $t->is(get_class($toolkit->getCacheDriver()), 'sfFileCache', 'The cache driver is set correctly');
  
  
  
  
  
  
  
  
  
  
  