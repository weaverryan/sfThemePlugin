<?php
$app = 'no_theme';
require dirname(__FILE__).'/../bootstrap/functional.php';

// Test for themeing being disabled

$browser = new sfTestFunctional(new sfBrowser());

$browser->info('1 - Goto the homepage of the no_theme app where theming is disabled')

  ->get('/')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->info('  1.1 - The "No theme" layout should be used')
    ->checkElement('h1', '/No theme/')
  ->end()
;
