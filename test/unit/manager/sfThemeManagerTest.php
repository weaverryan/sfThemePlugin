<?php

// bootstrap in the functional configuration since the theme manager is context-dependent
require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../bootstrap/functional.php');

$t = new lime_test(28);


$t->info('1 - Test some basics of getting themes, theme objects');

  // we'll use this to test the theme object
  $themeConfig = array('layout' => 'testing');
  $theme = new sfTheme($themeConfig);

  $manager = new sfThemeManager($context);
  $t->is($manager->getThemes(), array(), '->getThemes() returns an empty array to start.');

  $manager = new sfThemeManager($context, array('test_theme' =>$themeConfig));
  $t->is($manager->getThemes(), array('test_theme' => $themeConfig), 'Themes can be set via the constructor.');

  $manager = new sfThemeManager($context);
  $manager->addTheme('test_theme', $themeConfig);
  $t->is($manager->getThemes(), array('test_theme' => $themeConfig), 'Themes can be set via addTheme() passing in an array.');
  $t->is($manager->getThemeObject('test_theme')->getConfig(), $theme->getConfig(), '->getThemeObject() returns the correct theme object');

  $manager = new sfThemeManager($context);
  $manager->addTheme('test_theme', $theme);
  $t->is($manager->getThemes(), array('test_theme' => $themeConfig), 'Themes can be set via addTheme() passing in an sfTheme object.');
  $t->is($manager->getThemeObject('test_theme'), $theme, '->getThemeObject() returns the correct theme object');

  $t->info('  1.1 - Trying to retrieve a non-existent theme object throws an exception');
  try
  {
    $manager->getThemeObject('fake');
    $t->fail('No exception thrown');
  }
  catch (sfException $e)
  {
    $t->pass($e->getMessage());
  }

  $t->info('  1.2 - Play with the current theme');
  $t->is($manager->getCurrentTheme(), false, '->getCurrentTheme() returns false when there is no theme set');
  $t->is($manager->getCurrentThemeObject(), false, '->getCurrentThemeObject() return false when there is no theme set');



$t->info('2 - Set some themes and see what happens');

  $manager = new sfThemeManager($context);
  $themeConfig = array(
    'layout'      => 'app_test_layout', // located in the app's templates dir
    'stylesheets' => array('main'),
    'javascripts' => array('main.js'),
  );

  $t->info('  2.1 - Setting a non-existent theme as current throws an exception');
    try
    {
      $manager->setCurrentTheme('fake');
      $t->fail('Exception not thrown');
    }
    catch (sfException $e)
    {
      $t->pass($e->getMessage());
    }


  $t->info('  2.2 - Set a real theme with a bad layout name throws InvalidArgumentException');
    $badThemeConfig = $themeConfig;
    $badThemeConfig['layout'] = 'non_existent';
    $manager->addTheme('bad_theme', $badThemeConfig);
  
    try
    {
      $manager->setCurrentTheme('bad_theme');
      $t->fail('No exception thrown');
    }
    catch (InvalidArgumentException $e)
    {
      $t->pass($e->getMessage());
    }


  $t->info('  2.3 - Set a real, valid theme and view the results');
    $manager->addTheme('good_theme', $themeConfig);
    $manager->setCurrentTheme('good_theme');
    
    $t->is($manager->getCurrentTheme(), 'good_theme', '->getCurrentTheme() returns "good_theme"');
    $t->is($manager->getCurrentThemeObject()->getLayout(), 'app_test_layout', '->getCurrentThemeObject() returns the right theme object');
    
    // The module & action will be seen as default/index
    $layoutPath = sfConfig::get('sf_root_dir').'/apps/frontend/templates/app_test_layout';
    $t->is(sfConfig::get('symfony.view.default_index_layout'), $layoutPath, 'The sfConfig variables to change the layout were set correctly.');
    
    // test the secure and error 404 layouts as well
    $t->is(sfConfig::get('symfony.view.default_error404_layout'), $layoutPath, 'Layout also set on the error404 module/action');
    $t->is(sfConfig::get('symfony.view.default_secure_layout'), $layoutPath, 'Layout also set on the secure module/action');
    
    $t->is($context->getResponse()->getStylesheets(), array('main' => array()), 'The stylesheets were set correctly on the response');
    $t->is($context->getResponse()->getJavascripts(), array('main.js' => array()), 'The javascripts were set correctly on the response');


  $t->info('  2.4 - Check that trying to make the theme current again does nothing');
    $t->info('  2.4.1 - Remove a stylesheet from the theme, see that it was not put back');
    // This isn't something we'd normally do, just a clever way to see what's going on behind the scenes.
    $context->getResponse()->removeStylesheet('main');

    /*
     * To test, we need to "fake" switching outside of the test environment
     * to get around a hack in the Manager class to help in the testing env
     */
    sfConfig::set('sf_environment', 'dev');
    $manager->setCurrentTheme('good_theme');
    $t->is(
      $context->getResponse()->getStylesheets(),
      array(),
      'The theme was not re-set, which we can see because the stylesheet was not replaced'
    );
    sfConfig::set('sf_environment', 'test');


  $t->info('  2.5 - Make a different theme current, see that everything switches correctly');
    $t->info('  2.5.1 - Put the main css back and add an additional css file');
    $context->getResponse()->addStylesheet('main');
    $context->getResponse()->addStylesheet('non_theme_css');
  
    $otherThemeConfig = $themeConfig;
    $otherThemeConfig['stylesheets'] = array('other.css');
    $otherThemeConfig['javascripts'] = array();
    $manager->addTheme('other_theme', $otherThemeConfig);
    $manager->setCurrentTheme('other_theme');
    $t->is($manager->getCurrentTheme(), 'other_theme', '->getCurrentTheme() returns "other_theme"');
  
    $t->is(sfConfig::get('symfony.view.default_index_layout'), $layoutPath, 'The layout is still set correctly (did not change)');
    
    $t->is(
      $context->getResponse()->getStylesheets(),
      array('non_theme_css' => array(), 'other.css' => array()),
      'The old theme\'s css was removed, but the non_theme_css remained. The new theme\'s css was added'
    );
    $t->is(
      $context->getResponse()->getJavascripts(),
      array(),
      'The old theme\'s js was removed, the javascripts array is empty since this theme has none'
    );

$t->info('3 - Test the createInstance() method');
  
  $manager = sfThemeManager::createInstance($context);
  
  $t->is(get_class($manager), 'sfThemeTestManager', 'The class of the created object is sfThemeTestManager, as set in app.yml');
  $t->is(array_keys($manager->getThemes()), array(
    'test_theme',
    'unavailable_theme',
    'app_test',
  ), 'All the themes loaded correctly from app.yml - the disabled theme is NOT loaded');
  
  $t->is(get_class($manager->getThemeObject('app_test')), 'sfTestTheme', 'The theme objects have class sfTestTheme from app.yml');
  
  $t->info('  3.1 - Test ->getAvailableThemes() while I\'m here');
  $availableThemes = $manager->getAvailableThemes(); 
  $t->is(isset($availableThemes['unavailable_theme']), false, '->getAvailableThemes() does not include unavailable_theme theme'); 
  $t->is(count($availableThemes), 2, '->getAvailableThemes() returns 2 themes (1 from other plugin, 1 from app)');
