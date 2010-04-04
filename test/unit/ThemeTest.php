<?php

$app = 'frontend';
require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(10);

$pluginConfig = $configuration->getPluginConfiguration('sfSympalThemePlugin');
$sympalContext = sfSympalContext::getInstance();
$themeManager = $sympalContext->getService('theme_manager');
$dir = $pluginConfig->getRootDir();

// Default sympal theme
$t->info('1 - Test on the default sympal theme');
$theme = $themeManager->getTheme('sympal');

$t->is($theme->getLayoutPath(), $dir.'/templates/sympal.php', '->getLayoutPath() returns the correct path to sympal.php');
$t->is($theme->getStylesheets(), array('/sfSympalThemePlugin/css/sympal.css'), '->getStylesheets() returns the correct stylesheets');
$t->is($theme->getJavascripts(), array(), '->getJavascripts() returns no javascripts in this case');
$t->is($theme->getName(), 'sympal', '->getName() returns the correct name of the theme');

// Theme from another plugin
$t->info('2 - Test on a theme that lives inside a plugin');
$theme = $themeManager->getTheme('test_theme');

$t->is($theme->getLayoutPath(), $dir.'/test/fixtures/project/plugins/sfSympalThemeTestPlugin/templates/layout_for_testing.php', '->getLayoutPath() returns the custom layout name in the correct path');
$t->is($theme->getStylesheets(), array('/sfSympalThemeTestPlugin/css/test_theme.css'), '->getStylesheets() automatically finds a css file matching the name of the theme');
$t->is($theme->getJavascripts(), array('/sfSympalThemeTestPlugin/js/testing.js'), '->getJavascripts() returns the correct javascript');

// Theme from application
$t->info('3 - Test a plugin that lives inside the app');
$theme = $themeManager->getTheme('app_test');

$t->is($theme->getLayoutPath(), $dir.'/test/fixtures/project/apps/frontend/templates/app_test_layout.php', '->getLayoutPath() returns "app_test_layout" - the custom layout name given');
$t->is($theme->getStylesheets(), array('app_test'), '->getStylesheets() finds "app_test" by default as a css to include');
$t->is($theme->getJavascripts(), array(), '->getJavascripts() returns an empty array');

