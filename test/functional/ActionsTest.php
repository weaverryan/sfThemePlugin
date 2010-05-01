<?php

require dirname(__FILE__).'/../bootstrap/functional.php';

// tests the Actions extension class

/*
 * We're not testing here to see that everything is perfect and that the
 * correct themes are loaded. That will be done elsewhere. We just want
 * to make sure that the extended methods in the Actions class work
 */

$browser = new sfTestFunctional(new sfBrowser());

$browser->info('1 - Test the extended functions on the actiosn class')
  
  ->info('  1.1 - Test the ->loadTheme() function')
  ->get('/themes/test_theme')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/Plugin Test Layout/')
  ->end()
  
  ->info('  1.2 - Test the ->loadDefaultTheme function')
  ->get('/set_default_theme')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->checkElement('h1', '/Application Test Layout/')
  ->end()
  
;