<?php

$app = 'frontend';
require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

//$configuration = ProjectConfiguration::getApplicationConfiguration('sympal', 'test', true);
//$contexct = sfContext::createInstance($configuration)->dispatch();

$t = new lime_test(4);

$themeManager = new sfSympalThemeManager($context);


$t->info('1 - Test getThemes(), getAvailableThemes()');
$themes = $themeManager->getThemes(); 
$t->is(isset($themes['unavailable_theme']), true, '->getThemes() includes unavailable_theme (but enabled) theme'); 
$t->is(count($themes), 4, '->getThemes() should return 4 themes (3 from the plugin, 1 non-disabled from the project)');

$availableThemes = $themeManager->getAvailableThemes(); 
$t->is(isset($availableThemes['unavailable_theme']), false, '->getAvailableThemes() does not include unavailable_theme theme'); 
$t->is(count($availableThemes), 3, '->getAvailableThemes() returns 3 themes - the 2 in the app are not available');
