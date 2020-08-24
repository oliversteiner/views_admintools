<?php

/**
 * @file
 * Contains \Drupal\views_admintools\Controller\ViewsAdmintoolsController.
 */
namespace Drupal\views_admintools\Controller;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\mollo_utils\Utility\Helper;

class ViewsAdmintoolsController
{
  public function content()
  {
    return array(
      '#type' => 'markup',
      '#markup' => 'Hello, World!',
    );
  }
  public static function getIconPrefixOption()
  {

    return ['' => 'None', 'fas' => 'fas', 'far' => 'far', 'fal' => 'fal', 'fa' => 'fa'];
  }

  public static function getIconSetOption()
  {
    return [
      'drupal' => 'Drupal / jQuery Ui',
      'font_awesome' => 'Font Awesome 5',
      'bootstrap_3' => 'Twitter Bootstrap 3',
    ];
  }
  /**
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function publishToggle($nid)
  {
    $config = \Drupal::config('views_admintools.settings');
    $icon_set = $config->get('icon_set');
    $icon_prefix = $config->get('icon_prefix');

    switch ($icon_set) {
      case 'font_awesome': // 'Font Awesome 5'
        $icon_pre = $icon_prefix . ' fa-';
        break;

      case 'bootstrap_3': // 'Bootstrap 3'
        $icon_pre = 'glyphicon glyphicon-';
        break;

      default: // 'drupal' is default
        $icon_pre = 'ui-icon ui-icon-';
        break;
    }


    $response = new AjaxResponse();

    $status = Helper::publishToggle($nid);

    if ($status) {
      $message = t('This Article is Published');
      $icon = '  <span class="mollo-button-publish-'.$nid.'"><i class="'.$icon_pre . $config->get('icon_publish').'"></i></span>';

    } else {
      $message = t('This Article is not Published');
      $icon = '  <span class="mollo-button-publish-'.$nid.'"><i class="'.$icon_pre . $config->get('icon_unpublish').'"></i></span>';
    }

    // Article
    $response->addCommand(new InvokeCommand('.mollo-article-' . $nid, 'toggleClass', ['is-unpublished']));

    // Button
    $response->addCommand(new ReplaceCommand('.mollo-button-publish-' . $nid, $icon));

    // Message
    $response->addCommand(new ReplaceCommand('.ajax-container-' . $nid . '', '<div class="mollo-message-' . $nid . ' ajax-message-ok">' . $message . '</div>'));
    return $response;

  }
}
