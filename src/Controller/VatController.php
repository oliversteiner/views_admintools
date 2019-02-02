<?php
/**
 * Created by PhpStorm.
 * User: ost
 * Date: 06.11.17
 * Time: 20:21
 */

namespace Drupal\views_admintools\Controller;

use Drupal\Core\Controller\ControllerBase;

class VatController extends ControllerBase
{
  /**
   * {@inheritdoc}
   */
  protected function getModuleName()
  {
    return 'views_admintools';
  }

  /**
   * @return array
   */
  public function sandboxPage()
  {
    $output = 'Hallo World';

    //  kint($output);

    $form['list'] = [
      '#markup' =>
        '<p>Sandbox</p>' .
          '<hr>' .
          '<pre>All' .
          dpm($output) .
          '</pre>' .
          '<hr>',
    ];

    return $form;
  }
}
