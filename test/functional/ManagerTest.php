<?php

require dirname(__FILE__).'/../bootstrap/functional.php';

// Test the forwarding from module to module

$browser = new sfTestFunctional(new sfBrowser());

/*
 * This addresses an issue where if fwd'ed from one action to another with
 * the same theme, the manager sees that the same theme is being loaded
 * and stops executing the "switch theme". This is correct for stylesheets
 * and javascripts, avoiding extra work, but the layout still needs to be
 * set for the new module/action
 */
$browser->info('1 - Surf to an action that forwards to another action with the same theme')

  ->get('/manager/forward_to_same_theme')
  
  ->with('request')->begin()
    ->isParameter('module', 'manager')
    ->isParameter('action', 'forwardToSameTheme')
  ->end()
  
  ->isForwardedTo('manager', 'otherAction')
  
  ->with('response')->begin()
    ->isStatusCode(200)
    ->info('  1.1 - The forwarded action has the correct layout')
    ->checkElement('h1', '/Plugin Test Layout/')
  ->end()
;
