<?php

$app = 'frontend';
require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

//$configuration = ProjectConfiguration::getApplicationConfiguration('sympal', 'test', true);
//$contexct = sfContext::createInstance($configuration)->dispatch();

$t = new lime_test(5);

$themeManager = new sfSympalThemeManager($context);


$t->info('1 - Test getThemes(), getAvailableThemes()');
$themes = $themeManager->getThemes(); 
$t->is(isset($themes['unavailable_theme']), true, '->getThemes() includes unavailable_theme (but enabled) theme'); 
$t->is(count($themes), 3, '->getThemes() should return 4 themes (1 from plugin, 2 non-disabled from the project)');

$availableThemes = $themeManager->getAvailableThemes(); 
$t->is(isset($availableThemes['unavailable_theme']), false, '->getAvailableThemes() does not include unavailable_theme theme'); 
$t->is(count($availableThemes), 2, '->getAvailableThemes() returns 4 themes (1 from plugin, 1 from app)');

$theme = $themeManager->getTheme('app_test');
try {
  $theme = $themeManager->getTheme('test2');
  $t->fail('Should have thrown exception');
}
catch (Exception $e)
{
  $t->pass('Exception thrown');
}