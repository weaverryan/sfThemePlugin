<?php

require dirname(__FILE__).'/../bootstrap/functional.php';

// Test for all the different variables that can determine which theme should be set

$browser = new sfTestFunctional(new sfBrowser());

$headers = array(
  'app_test'    => 'Application Test Layout',
  'test_theme'  => 'Plugin Test Layout',
);

$browser->info('1 - Test a few straightforward ways of setting themes')
  
  ->info('  1.1 - Do nothing special, end up with the default theme')
  ->get('/controller/default_theme')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/'.$headers['app_test'].'/')
  ->end()

  ->info('  1.2 - Go to an action that explicitly request the test_theme')
  ->get('/controller/explicit_test_theme')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/'.$headers['test_theme'].'/')
  ->end()
;

$browser->info('2 - Test forwarding')
  
  ->info('  2.1 Start at an action with test_theme route then forward to one using the default theme')
  ->get('/controller/test_theme_forward_default_theme')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->info('  2.1.1 - The default theme, the final spot, should win out')
    ->checkElement('h1', '/'.$headers['app_test'].'/')
  ->end()

  ->info('  2.2 Start at an action with the default theme then forward to the test theme')
  ->get('/controller/default_theme_forward_test_theme')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->info('  2.2.1 - The test theme, the final spot, should win out')
    ->checkElement('h1', '/'.$headers['test_theme'].'/')
  ->end()
;

$browser->info('3 - Test the event listening method of setting a theme')

  ->info('  3.1 - Goto a module/action that should use the default theme')
  ->get('/controller/event_listener')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->info('  3.1.1 - Listener in frontendConfiguration set the theme to test_theme')
    ->checkElement('h1', '/'.$headers['test_theme'].'/')
  ->end()
;

$browser->info('4 - Change the theme via a request parameter')

  ->info('  4.1 - Goto the default theme but with a request parameter to set test_theme')
  ->get('/controller/default_theme?sf_theme=test_theme')

  ->with('response')->begin()
    ->isStatusCode(200)
    ->info('  4.2 - See that the test_theme is used')
    ->checkElement('h1', '/'.$headers['test_theme'].'/')
  ->end()
  
  ->with('user')->begin()
    ->info('  4.3 - A user attribute is set for the theme')
    ->isAttribute('current_theme', 'test_theme')
  ->end()
  
  ->info('  4.4 - The theme should be sticky - goto another default theme, should be test theme still')
  
  ->get('/controller/test_theme_forward_default_theme')
  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/'.$headers['test_theme'].'/')
  ->end()
  
  ->info('  4.5 - Set the theme back to app_test with a request parameter')
  ->get('/controller/test_theme_forward_default_theme?sf_theme=app_test')
  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/'.$headers['app_test'].'/')
  ->end()

  ->info('  4.6 - The user theme is not used if overridden explicitly')
  ->get('/controller/explicit_test_theme')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->info('  4.6.1 - The theme is test_theme, since it was explicitly set')
    ->checkElement('h1', '/'.$headers['test_theme'].'/')
  ->end()

  ->info('  4.7 - Using an invalid theme name does nothing, unsets user attribute')
  ->get('/controller/default_theme?sf_theme=fake')
  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/'.$headers['app_test'].'/')
  ->end()

  ->with('user')->begin()
    ->info('  4.8 - A user attribute is actually unset')
    ->isAttribute('current_theme', false)
  ->end()
  

;

// Restart the browser to clear the session
$browser->restart();

$browser->info('5 - Test the modules and routes method of setting themes')

  ->info('  5.1 - Goto a module that is setup in app.yml to use test_theme')
  ->get('/test_theme_module/index')

  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/'.$headers['test_theme'].'/')
  ->end()

  ->info('  5.2 - Goto a route that is setup in app.yml to use test_theme')
  ->get('/controller/test_theme_route')

  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/'.$headers['test_theme'].'/')
  ->end()
;
