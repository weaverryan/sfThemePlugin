<?php

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(6);

$configuration = array(
  'layout'      => 'my_layout',
  'stylesheets' => array('main.css'),
  'javascripts'  => array('main.js'),
);

$theme = new sfTheme($configuration);

$t->is($theme->getLayout(), $configuration['layout'], '->getLayout() return my_layout');
$t->is($theme->getStylesheets(), $configuration['stylesheets'], '->getStyleseets() returns correct value');
$t->is($theme->getJavascripts(), $configuration['javascripts'], '->getJavascripts() returns correct value');
$t->is($theme->getCallables(), array(), '->getCallables() returns correct value');
$t->is($theme->getConfig('layout', 'ignored'), $configuration['layout'], '->getConfig() returns correct value for existing config value');
$t->is($theme->getConfig('fake', 'test_default'), 'test_default', '->getConfig() returns default value for non-existant config value');