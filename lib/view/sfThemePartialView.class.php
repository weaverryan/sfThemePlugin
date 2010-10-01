<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A View to render partials in a sfTheme.
 *
 * @package    symfony
 * @subpackage view
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPartialView.class.php 23985 2009-11-15 20:09:20Z FabianLange $
 */
class sfThemePartialView extends sfPartialView
{

  /**
   * Configures template for this view.
   */
  public function configure()
  {
    $this->setDecorator(false);
    $this->setTemplate($this->actionName.$this->getExtension());

    $template_dir = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR .
                    $this->context->getConfiguration()
                      ->getPluginConfiguration('sfThemePlugin')
                      ->getThemeManager()
                      ->getCurrentThemeObject()
                      ->getConfig('templates_dir', 'sfThemePlugin/templates/');
    $this->setDirectory($template_dir);
  }

}
