<?php

$app = 'frontend';
require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

//$configuration = ProjectConfiguration::getApplicationConfiguration('sympal', 'test', true);
//$contexct = sfContext::createInstance($configuration)->dispatch();

$t = new lime_test(24);

$themeManager = new sfSympalThemeManager($context);

$themes = $sympalConfiguration->getThemes(); 
$t->is(isset($themes['default']), true, '->getThemes() includes default theme'); 

$availableThemes = $sympalConfiguration->getAvailableThemes(); 
$t->is(isset($themes['admin']), 'admin', '->getAvailableThemes() does not include admin theme'); 
