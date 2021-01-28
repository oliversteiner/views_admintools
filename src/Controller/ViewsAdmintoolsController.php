<?php

/**
 * @file
 * Contains \Drupal\views_admintools\Controller\ViewsAdmintoolsController.
 */

namespace Drupal\views_admintools\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Config\Config;
use Drupal\mollo_utils\Utility\Helper;

class ViewsAdmintoolsController
{
  public function content()
  {
    return array(
      '#type' => 'markup',
      '#markup' => 'Hello, World!'
    );
  }

  public static function getIconVariantOption()
  {
    return [
      '' => 'None',
      'fas' => 'fas',
      'far' => 'far',
      'fal' => 'fal',
      'fa' => 'fa',
      'glyphicon' => 'glyphicon',
      'ui-icon' => 'ui-icon'
    ];
  }

  public static function getIconSetOption()
  {
    return [
      'drupal' => 'Drupal / jQuery Ui',
      'fontawesome' => 'Font Awesome 5',
      'bootstrap_3' => 'Twitter Bootstrap 3'
    ];
  }

  /**
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function publishToggle($nid)
  {
    $config = \Drupal::config('views_admintools.settings');
    $icon_set = $config->get('icon_set');
    $icon_variant = $config->get('icon_variant');

    switch ($icon_set) {
      case 'fontawesome': // 'Font Awesome 5'
        $icon_pre = $icon_variant . ' fa-';
        break;

      case 'bootstrap_3': // 'Bootstrap 3'
        $icon_pre = $icon_variant . ' glyphicon-';
        break;

      default:
        // 'drupal' is default
        $icon_pre = $icon_variant . ' ui-icon-';
        break;
    }

    $response = new AjaxResponse();

    $status = Helper::publishToggle($nid);

    if ($status) {
      $message = t('This Article is Published');
      $icon =
        '  <span class="mollo-button-publish-' .
        $nid .
        '"><i class="' .
        $icon_pre .
        $config->get('icon_publish') .
        '"></i></span>';
    } else {
      $message = t('This Article is not Published');
      $icon =
        '  <span class="mollo-button-publish-' .
        $nid .
        '"><i class="' .
        $icon_pre .
        $config->get('icon_unpublish') .
        '"></i></span>';
    }

    // Article
    $response->addCommand(
      new InvokeCommand('.mollo-article-' . $nid, 'toggleClass', [
        'is-unpublished'
      ])
    );

    // Button icon replace
    $response->addCommand(
      new ReplaceCommand('.mollo-button-publish-' . $nid.' a span', $icon)
    );

    // Button icon replace FontAwesome
    $response->addCommand(
      new ReplaceCommand('.mollo-button-publish-' . $nid.' a svg', $icon)
    );

    // Message
    $response->addCommand(
      new ReplaceCommand(
        '.ajax-container-' . $nid . '',
        '<div class="mollo-message-' .
          $nid .
          ' ajax-message-ok">' .
          $message .
          '</div>'
      )
    );
    return $response;
  }

  public static function getIconSets()
  {
    return [
      'fontawesome_regular' => [
        'name' => 'Font Awesome',
        'machine_name' => 'fontawesome_regular',
        'set' => 'fontawesome',
        'variant_name' => 'Regular',
        'variant' => 'far',
        'prefix' => 'fa-',
        'icons' => [
          'new' => [
            'name' => 'new',
            'icon' => 'plus'
          ],
          'sort' => [
            'name' => 'sort',
            'icon' => 'sort'
          ],
          'search' => [
            'name' => 'search',
            'icon' => 'search'
          ],
          'edit' => [
            'name' => 'edit',
            'icon' => 'pencil'
          ],
          'publish' => [
            'name' => 'publish',
            'icon' => 'eye'
          ],
          'unpublish' => [
            'name' => 'unpublish',
            'icon' => 'eye-slash'
          ],
          'delete' => [
            'name' => 'delete',
            'icon' => 'trash'
          ],
          'vocabulary' => [
            'name' => 'vocabulary',
            'icon' => 'list'
          ],
          'back' => [
            'name' => 'back',
            'icon' => 'chevron-left'
          ],
          'forward' => [
            'name' => 'forward',
            'icon' => 'chevron-right'
          ]
        ]
      ],
      'fontawesome_solid' => [
        'name' => 'Font Awesome',
        'machine_name' => 'fontawesome_solid',
        'set' => 'fontawesome',
        'variant_name' => 'Solid',
        'variant' => 'fas',
        'prefix' => 'fa-',
        'icons' => [
          'new' => [
            'name' => 'new',
            'icon' => 'plus'
          ],
          'sort' => [
            'name' => 'sort',
            'icon' => 'sort'
          ],
          'search' => [
            'name' => 'search',
            'icon' => 'search'
          ],
          'edit' => [
            'name' => 'edit',
            'icon' => 'pencil'
          ],
          'publish' => [
            'name' => 'publish',
            'icon' => 'eye'
          ],
          'unpublish' => [
            'name' => 'unpublish',
            'icon' => 'eye-slash'
          ],
          'delete' => [
            'name' => 'delete',
            'icon' => 'trash'
          ],
          'vocabulary' => [
            'name' => 'vocabulary',
            'icon' => 'list'
          ],
          'back' => [
            'name' => 'back',
            'icon' => 'chevron-left'
          ],
          'forward' => [
            'name' => 'forward',
            'icon' => 'chevron-right'
          ]
        ]
      ],
      'fontawesome_light' => [
        'name' => 'Font Awesome',
        'machine_name' => 'fontawesome_light',
        'set' => 'fontawesome',
        'variant_name' => 'Light',
        'variant' => 'fal',
        'prefix' => 'fa-',
        'icons' => [
          'new' => [
            'name' => 'new',
            'icon' => 'plus'
          ],
          'sort' => [
            'name' => 'sort',
            'icon' => 'sort'
          ],
          'search' => [
            'name' => 'search',
            'icon' => 'search'
          ],
          'edit' => [
            'name' => 'edit',
            'icon' => 'pencil'
          ],
          'publish' => [
            'name' => 'publish',
            'icon' => 'eye'
          ],
          'unpublish' => [
            'name' => 'unpublish',
            'icon' => 'eye-slash'
          ],
          'delete' => [
            'name' => 'delete',
            'icon' => 'pencil'
          ],
          'vocabulary' => [
            'name' => 'vocabulary',
            'icon' => 'list'
          ],
          'back' => [
            'name' => 'back',
            'icon' => 'chevron-left'
          ],
          'forward' => [
            'name' => 'forward',
            'icon' => 'chevron-right'
          ]
        ]
      ],
      'drupal' => [
        'name' => 'Drupal',
        'machine_name' => 'drupal',
        'set' => 'drupal',
        'variant_name' => 'jQuery UI',
        'variant' => 'ui-icon',
        'prefix' => 'ui-icon-',
        'icons' => [
          'new' => [
            'name' => 'new',
            'icon' => 'plus'
          ],
          'sort' => [
            'name' => 'sort',
            'icon' => 'arrow-2-n-s'
          ],
          'search' => [
            'name' => 'search',
            'icon' => 'search'
          ],
          'edit' => [
            'name' => 'edit',
            'icon' => 'pencil'
          ],
          'publish' => [
            'name' => 'publish',
            'icon' => 'bullet'
          ],
          'unpublish' => [
            'name' => 'unpublish',
            'icon' => 'radio-off'
          ],
          'delete' => [
            'name' => 'delete',
            'icon' => 'trash'
          ],
          'vocabulary' => [
            'name' => 'vocabulary',
            'icon' => 'calculator'
          ],
          'back' => [
            'name' => 'back',
            'icon' => 'caret-1-w'
          ],
          'forward' => [
            'name' => 'forward',
            'icon' => 'caret-1-e'
          ]
        ]
      ],
      'bootstrap_3' => [
        'name' => 'Twitter Bootstrap',
        'machine_name' => 'bootstrap_3',
        'set' => 'bootstrap_3',
        'variant_name' => '3',
        'variant' => 'glyphicon',
        'prefix' => 'glyphicon-',
        'icons' => [
          'new' => [
            'name' => 'new',
            'icon' => 'plus'
          ],
          'sort' => [
            'name' => 'sort',
            'icon' => 'sort'
          ],
          'search' => [
            'name' => 'search',
            'icon' => 'search'
          ],
          'edit' => [
            'name' => 'edit',
            'icon' => 'pencil'
          ],
          'publish' => [
            'name' => 'publish',
            'icon' => 'eye-open'
          ],
          'unpublish' => [
            'name' => 'unpublish',
            'icon' => 'eye-close'
          ],
          'delete' => [
            'name' => 'delete',
            'icon' => 'trash'
          ],
          'vocabulary' => [
            'name' => 'vocabulary',
            'icon' => 'th-list'
          ],
          'back' => [
            'name' => 'back',
            'icon' => 'menu-left'
          ],
          'forward' => [
            'name' => 'forward',
            'icon' => 'menu-right'
          ]
        ]
      ]
    ];
  }

  public static function getIconList()
  {
    $icon_sets = self::getIconSets();

    return $icon_sets['drupal']['icons'];
  }

  public static function getViewsDefaults()
  {
    $config = \Drupal::config('views_admintools.settings');

    $list = [
      'set',
      'variant',
      'new',
      'sort',
      'search',
      'edit',
      'publish',
      'unpublish',
      'delete',
      'vocabulary',
      'back',
      'forward'
    ];

    foreach ($list as $item) {
      $output[$item] = $config->get('icon_' . $item);
    }
    return $output;
  }
}
