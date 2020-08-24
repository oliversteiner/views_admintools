<?php

namespace Drupal\views_admintools\Plugin\views\area;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\views\Plugin\views\area\TokenizeAreaPluginBase;
use Drupal\views_admintools\Controller\ViewsAdmintoolsController;

/**
 * Views area Admin Tools.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("vat_views_area_admin_tools")
 */
class ViewsAdminTools extends TokenizeAreaPluginBase
{
  /**
   * @return string[]
   */
  function getIconSetOption()
  {
    return ViewsAdmintoolsController::getIconSetOption();
  }

  /**
   * @return string[]
   */
  function getIconPrefixOption()
  {
    return ViewsAdmintoolsController::getIconPrefixOption();
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array
  {
    $options = parent::defineOptions();
    $config = \Drupal::config('views_admintools.settings');

    // Override defaults to from parent.
    $options['tokenize']['default'] = false;
    $options['empty']['default'] = true;

    // View Info
    $view_path = $this->view->getPath();
    $icon_prefix = $config->get('icon_prefix');


    // Read first View Row get Content Type
    $content_type = false;
    if ($this->view && $this->view->display_handler->getOption('filters')) {
      $option_filters = $this->view->display_handler->getOption('filters');
      if (isset($option_filters['type']) && $option_filters['type']['value']) {
        $option_filters_types = $option_filters['type']['value'];
        $content_type = array_keys($option_filters_types)[0];
      }
    }

    $options['content_type']['default'] = $content_type;

    // Title
    $options['title_text']['default'] = '';

    // Generate 10 Buttons
    for ($i = 1; $i <= 10; $i++) {
      $options['button_b' . $i . '_active']['default'] = false;
      $options['button_b' . $i . '_label']['default'] = '';
      $options['button_b' . $i . '_icon_prefix']['default'] = '';
      $options['button_b' . $i . '_icon']['default'] = '';
      $options['button_b' . $i . '_link']['default'] = '';
      $options['button_b' . $i . '_destination']['default'] = '';
      $options['button_b' . $i . '_modal']['default'] = 0;
    }

    // Set defaults for first 3 Buttons as "New", "Sort", "Back"

    // Button new
    $options['button_b1_active']['default'] = true;
    $options['button_b1_label']['default'] = $this->t('New');
    $options['button_b1_icon']['default'] = 'plus';
    $options['button_b1_icon_prefix']['default'] = $icon_prefix;
    $options['button_b1_link']['default'] = '/node/add/' . $content_type;
    $options['button_b1_destination']['default'] = '/' . $view_path;
    $options['button_b1_modal']['default'] = 1;

    // Button sort
    $options['button_b2_active']['default'] = false;
    $options['button_b2_label']['default'] = $this->t('Sort');
    $options['button_b2_icon']['default'] = 'sort';
    $options['button_b2_icon_prefix']['default'] = $icon_prefix;
    $options['button_b2_link']['default'] = '/' . $view_path . '/sort';
    $options['button_b2_destination']['default'] = '/' . $view_path;
    $options['button_b2_modal']['default'] = 0;

    // Button back
    $options['button_b3_active']['default'] = false;
    $options['button_b3_label']['default'] = $this->t('Back');
    $options['button_b3_icon']['default'] = 'chevron-left';
    $options['button_b3_icon_prefix']['default'] = $icon_prefix;
    $options['button_b3_link']['default'] = '/' . $view_path;
    $options['button_b3_destination']['default'] = '';
    $options['button_b2_modal']['default'] = 0;

    // Look
    $options['look_show_label']['default'] = true;
    $options['look_show_icon']['default'] = true;
    $options['look_show_as']['default'] = 'Button';
    $options['look_icon_size']['default'] = 0; // normal


    // Vocabulary
    $options['look_separator']['default'] = false;

    // Generate 6 Vocabularies
    for ($i = 1; $i <= 6; $i++) {
      $options['vocabulary_' . $i]['default'] = '';
    }

    // Modal
    $options['use_modal']['default'] = true;
    $options['modal_width']['default'] = 800;
    $options['modal_height'] = ['default' => '90%'];

    // Role
    $role_objects = Role::loadMultiple();

    foreach ($role_objects as $role) {
      $option_name = 'roles-' . $role->id();
      $options[$option_name]['default'] = false; // normal
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state)
  {
    parent::buildOptionsForm($form, $form_state);

    $config = \Drupal::config('views_admintools.settings');

    // Prepare Options for Select Form
    // -------------------------------

    //  Options Nodes Types
    // -------------------------------
    $types = NodeType::loadMultiple();

    // Add Nodetypes to Options Array
    $options_node_type = [];
    foreach ($types as $key => $type) {
      $options_node_type[$key] = $type->label();
    }

    //  Options Roles
    // -------------------------------
    $roles = Role::loadMultiple();

    // Add Roles to Options Array
    $options_roles = [];
    $i = 0;
    foreach ($roles as $role) {
      $options_roles[$i]['id'] = $role->id();
      $options_roles[$i]['label'] = $role->label();
      $i++;
    }

    // Options Taxonomy
    // -------------------------------
    $types = Vocabulary::loadMultiple();

    // Add Taxonomy to Options Array
    $options_taxonomy = [];
    $options_taxonomy[''] = ''; // Leave the first Entry empty.

    foreach ($types as $key => $type) {
      $options_taxonomy[$key] = $type->label();
    }

    // Options Icons - Size
    // -------------------------------
    $options_icon_size = [
      $this->t('Default'),
      $this->t('Small'),
      $this->t('Large'),
    ];


    // Options Icon Prefix
    // -------------------------------
    $options_icon_prefix = $this->getIconPrefixOption();

    // Build Form
    // -------------------------------

    // Add CSS and JS
    $form['#attached']['library'][] = 'views_admintools/views_admintools.admin';

    // Title Text / Heading
    $form['title_text'] = [
      '#title' => $this->t('Title'),
      '#type' => 'textfield',
      '#size' => 60,
      '#default_value' => $this->options['title_text'],
    ];

    // Which Node Type ?
    $form['content_type'] = [
      '#title' => $this->t('Content Type'),
      '#type' => 'select',
      '#default_value' => $this->options['content_type'],
      '#options' => $options_node_type,
    ];

    // Info , Help
    $form['info'] = [
      '#markup' =>
        '<div class="vat-options-info">' .
        $this->t('Add icon names without prefix (fa-).') .
        '</div>',
    ];

    // Warning: Default Drupal Fieldset don't work with $options['fieldset']['field']['default']
    // Also create Filesets manuel

    $form['button_fieldset_start'] = [
      '#type' => 'html_tag',
      '#tag' => 'label',
      '#value' => 'Buttons',
      '#prefix' => '<fieldset id="vat-buttons-list">',
    ];

    for ($i = 1; $i <= 10; $i++) {
      // Button Default
      // ------------------------------
      if ($this->options['button_b' . $i . '_label'] == '') {
        $visibility = 'hide';
      } else {
        $visibility = 'show';
      }

      // Row start
      $form['button_b' . $i . '_fieldset_start'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => '',
        '#prefix' =>
          '<div class="vat-options-button-row ' .
          $visibility .
          '" id="vat-options-button-row-' .
          $i .
          '">',
      ];

      // Activate Button
      $form['button_b' . $i . '_active'] = [
        '#title' => '',
        '#type' => 'checkbox',
        '#default_value' => $this->options['button_b' . $i . '_active'],
        '#prefix' =>
          '<span class="vat-options-button-inline vat-options-button-active">',
        '#suffix' => '</span>',
      ];


      // Render Icon if Font Awesome Module is installed
      if (
        $this->options['fontawesome'] &&
        $this->options['button_b' . $i . '_icon']
      ) {
        $icon_prefix = $this->options['button_b' . $i . '_icon_prefix'];
        $fontawesome_prefix = $options_icon_prefix[$icon_prefix];
        $fontawesome_icon = $this->options['button_b' . $i . '_icon'];
        $class_icon = $fontawesome_prefix . ' ' . $fontawesome_icon;
        $form['button_b' . $i . 'fa'] = array(
          '#theme' => 'fontawesomeicon',
          '#tag' => 'span',
          '#name' => $fontawesome_prefix . ' fa-' . $fontawesome_icon,
          '#settings' => null,
          '#transforms' => '2x',
          '#mask' => null,
          '#prefix' =>
            '<span class="vat-options-button-inline vat-options-button-fa">',
          '#suffix' => '</span>',
        );
      } else {
        $class_icon = $this->options['button_b' . $i . '_icon'];

        $form['button_b' . $i . 'no_fa'] = [
          '#type' => 'html',
          '#value' => '<i class="' . $class_icon . '"></i>',
          '#prefix' =>
            '<span class="vat-options-button-inline vat-options-button-fa">',
          '#suffix' => '</span>',
        ];
      }

      // Icon Prefix
      $form['button_b' . $i . '_icon_prefix'] = [
        '#title' => $this->labelFirstRow($i, $this->t('Prefix')),
        '#type' => 'select',
        '#default_value' => $this->options['button_b' . $i . '_icon_prefix'],
        '#options' => $options_icon_prefix,
        '#prefix' => '<span class="vat-options-button-inline">',
        '#suffix' => '</span>',
      ];

      // Icon
      $form['button_b' . $i . '_icon'] = [
        '#title' => $this->labelFirstRow($i, $this->t('Icon')),
        '#type' => 'textfield',
        '#size' => 10,
        '#default_value' => $this->options['button_b' . $i . '_icon'],
        '#prefix' => '<span class="vat-options-button-inline">',
        '#suffix' => '</span>',
      ];

      // Label
      $form['button_b' . $i . '_label'] = [
        '#title' => $this->labelFirstRow($i, $this->t('Label')),
        '#type' => 'textfield',
        '#size' => 20,
        '#default_value' => $this->options['button_b' . $i . '_label'],
        '#prefix' => '<span class="vat-options-button-inline">',
        '#suffix' => '</span>',
      ];

      // Link
      $form['button_b' . $i . '_link'] = [
        '#title' => $this->labelFirstRow($i, $this->t('Link')),
        '#type' => 'textfield',
        '#size' => 20,
        '#default_value' => $this->options['button_b' . $i . '_link'],
        '#prefix' => '<span class="vat-options-button-inline">',
        '#suffix' => '</span>',
      ];

      // Destination after submit
      $form['button_b' . $i . '_destination'] = [
        '#title' => $this->labelFirstRow(
          $i,
          $this->t('Destination after submit')
        ),
        '#type' => 'textfield',
        '#size' => 20,
        '#default_value' => $this->options['button_b' . $i . '_destination'],
        '#prefix' => '<span class="vat-options-button-inline">',
        '#suffix' => '</span>',
      ];

      // Use Modal ?
      $form['button_b' . $i . '_modal'] = [
        '#title' => '',
        '#type' => 'checkbox',
        '#default_value' => $this->options['button_b' . $i . '_modal'],
        '#prefix' =>
          '<span class="vat-options-button-inline vat-options-button-modal">',
        '#suffix' => '</span>',
      ];

      // Manual Modal Label on first Row
      if ($i == 1) {
        $form['button_b' . $i . '_modal']['#prefix'] =
          '<span class="vat-options-button-inline vat-options-button-modal"><label class="modal-label">Modal</label>';
        $form['button_b' . $i . '_modal']['#suffix'] = '</span>';
      }

      // Row end
      $form['button_b' . $i . '_fieldset_end'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => '',
        '#suffix' => '</div>',
      ];
    }

    // Add more Rows
    // Add a submit button that handles the submission of the form.
    $form['actions_add_more_rows'] = [
      '#type' => 'html_tag',
      '#tag' => 'a',
      '#value' => $this->t('Add more buttons'),
      '#attributes' => [
        'class' => ['button', 'add-more-buttons'],
        'role' => 'button',
      ],
    ];

    $form['button_fieldset_end'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => '',
      '#suffix' => '</fieldset>',
    ];

    // Look
    // ------------------------------

    // Fieldset
    $form['look_fieldset_start'] = [
      '#type' => 'html_tag',
      '#tag' => 'label',
      '#value' => $this->t('Button Look'),
      '#prefix' => '<fieldset class="vat-options-group">',
    ];

    // Label
    $form['look_show_label'] = [
      '#title' => $this->t('Label'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['look_show_label'],
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>',
    ];

    // Icon
    $form['look_show_icon'] = [
      '#title' => $this->t('Icon'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['look_show_icon'],
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>',
    ];

    // Link or Button
    $options = ['Button', 'Link'];
    $form['look_show_as'] = [
      '#title' => $this->t('Show as'),
      '#type' => 'select',
      '#default_value' => $this->options['look_show_as'],
      '#options' => $options,
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>',
    ];


    // Icon Size
    $form['look_icon_size'] = [
      '#title' => $this->t('Icon Size'),
      '#type' => 'select',
      '#default_value' => $this->options['look_icon_size'],
      '#options' => $options_icon_size,
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>',
    ];

    // Fieldset End
    $form['look_fieldset_end'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => '',
      '#suffix' => '</fieldset>',
    ];

    // Vocabulary
    // ------------------------------

    // Fieldset
    $form['vocabularies_fieldset_start'] = [
      '#type' => 'html_tag',
      '#tag' => 'label',
      '#value' => $this->t('Vocabularies'),
      '#prefix' => '<fieldset class="vat-options-group">',
    ];

    for ($i = 1; $i <= 6; $i++) {
      // add 4 taxonomy vocabulary dropdowns
      $form['vocabulary_' . $i] = [
        //    '#title' => $this->t('Taxonomy ' . $i),
        '#type' => 'select',
        '#default_value' => $this->options['vocabulary_' . $i],
        '#options' => $options_taxonomy,
        '#prefix' => '<span class="vat-options-inline">',
        '#suffix' => '</span>',
      ];
    }
    // Separator between Buttons and Taxonomy
    $form['look_separator'] = [
      '#title' => $this->t('Separator between Buttons and Taxonomy'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['look_separator'],
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>',
    ];

    // Fieldset End
    $form['vocabularies_fieldset_end'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => '',
      '#suffix' => '</fieldset>',
    ];

    // Modal
    // ------------------------------

    // Fieldset
    $form['modal_fieldset_start'] = [
      '#type' => 'html_tag',
      '#tag' => 'label',
      '#value' => $this->t('Modal Dialog'),
      '#prefix' => '<fieldset class="vat-options-group">',
    ];

    // Modal
    $form['use_modal'] = [
      '#title' => $this->t('Use Modal Dialog?'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['use_modal'],
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>',
    ];

    // Modal Width
    $form['modal_width'] = [
      '#title' => $this->t('Modal Width'),
      '#type' => 'textfield',
      '#size' => 10,
      '#default_value' => $this->options['modal_width'],
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>',
    ];

    // Modal height
    $form['modal_height'] = [
      '#title' => $this->t('Modal Height'),
      '#type' => 'textfield',
      '#attributes' => array('maxlength' => 10, 'size' => 10),
      '#default_value' => $this->options['modal_height'],
      '#prefix' => '<span class="vat-options-inline">',
      '#suffix' => '</span>',
    ];

    // Fieldset End
    $form['modal_fieldset_end'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => '',
      '#suffix' => '</fieldset>',
    ];

    // User Roles
    // ------------------------------

    // Fieldset
    $form['roles_fieldset_start'] = [
      '#type' => 'html_tag',
      '#tag' => 'label',
      '#value' => $this->t('Which roles are allowed to see the Buttons?'),
      '#prefix' => '<fieldset class="vat-options-group">',
    ];

    foreach ($options_roles as $role) {
      $role_form_name = 'roles-' . $role['id'];
      $form[$role_form_name] = [
        '#title' => $this->t($role['label']),
        '#type' => 'checkbox',
        '#default_value' => $this->options[$role_form_name],
      ];
    }

    // Fieldset End
    $form['roles_fieldset_end'] = [
      '#type' => 'html_tag',
      '#tag' => 'span',
      '#value' => '',
      '#suffix' => '</fieldset>',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($empty = false)
  {
    $config = \Drupal::config('views_admintools.settings');

    if (!$empty || !empty($this->options['empty'])) {
      $view_path = $this->view->getPath();

      // Content
      // -------------------------------
      $content = false;

      if ($this->options['content_type']) {
        $content['type'] = $this->options['content_type'];
      }

      // Look
      // -------------------------------
      $look['separator'] = false;
      $look['icon'] = false;
      $look['label'] = false;

      // Separator
      if ($this->options['look_separator']) {
        $look['separator'] = true;
      }

      // Icon
      if ($this->options['look_show_icon']) {
        $look['icon'] = true;
      }

      // Label
      if ($this->options['look_show_label']) {
        $look['label'] = true;
      }

      //  Access
      // ----------------------------------------------------
      $access = false;
      $access_roles = [];
      $role_objects = Role::loadMultiple();

      foreach ($role_objects as $role) {
        $role_id = $role->id();
        $option_name = 'roles-' . $role_id;
        if ($this->options[$option_name]) {
          $access_roles[] = $role_id;
        }
      }

      // Get User Roles
      $current_user = \Drupal::currentUser();
      $user_roles = $current_user->getRoles();
      $user_id = $current_user->id();

      // is user roles in access roles ?
      // If User is Admin
      if ($user_id == 1) {
        $access = true;
      } else {
        // Check user roles
        foreach ($user_roles as $user_role) {
          if (in_array($user_role, $access_roles)) {
            $access = true;
            break;
          }
        }
      }

      //  Icon Set
      // ----------------------------------------------------
      $icon_set = $config->get('icon_set');
      $icon_prefix_1 = $config->get('icon_prefix');

      $icon_vocabulary = $config->get('icon_vocabulary');


      switch ($icon_set) {

        case 'font_awesome': // 'Font Awesome 5'
          $icon_prefix = 'fa-';
          $icon_taxonomy = $icon_prefix_1 . ' fa-' . $icon_vocabulary;
          break;

        case 'bootstrap_3': // 'Bootstrap 3'
          $icon_prefix = 'glyphicon glyphicon-';
          $icon_taxonomy = "glyphicon glyphicon-" . $icon_vocabulary;
          break;

        default: // 'drupal' is default
          $icon_prefix = 'ui-icon ui-icon-';
          $icon_taxonomy = "ui-icon ui-icon-" . $icon_vocabulary;
          break;
      }

      // Look
      // ----------------------------------------------------

      switch ($this->options['look_show_as']) {
        case 0: // Button:
          if (\Drupal::moduleHandler()->moduleExists('bootstrap_library')) {
            // Add Bootstrap 4 Classes
            $button_classes = 'btn btn-primary vat-button vat-button-primary';
          } else {
            $button_classes = 'vat-button vat-button-primary';
          }

          break;

        case 1: // Link:
          $button_classes = 'vat-link';
          break;

        default:
          $button_classes = 'vat-button vat-button-primary';
          break;
      }

      // Size
      switch ($this->options['look_icon_size']) {
        case 1: // small
          $css_class_size = 'btn-sm';
          break;

        case 2: // large
          $css_class_size = 'btn-lg';
          break;

        default:
          $css_class_size = '';
          break;
      }

      // Combine Classes
      $button_classes = $button_classes . ' ' . $css_class_size;

      // Modal
      // -------------------------------
      $modal = false;

      if ($this->options['use_modal']) {
        $modal = [
          'width' => $this->options['modal_width'],
          'height' => $this->options['modal_height']
        ];
      }

      // Options Icon Prefix
      // -------------------------------
      $options_icon_prefix = $this->getIconPrefixOption();

      // Buttons
      // -------------------------------
      $buttons = [];
      $button_attributes = [
        'active',
        'label',
        'icon',
        'link',
        'destination',
        'class',
        'modal',
      ];

      for ($i = 1; $i <= 10; $i++) {
        $attr = [];
        $button_name = 'button_b' . $i;

        // Use Modal ?
        $use_modal = $this->options[$button_name . '_modal'];
        if ($use_modal != 0) {
          $classes = $button_classes . ' use-ajax';
        } else {
          $classes = $button_classes;
        }

        $fp = $this->options[$button_name . '_icon_prefix'];

        if (empty($fp)) {
          $fp = 0;
        }
        $fontawesome_prefix = $fp;

        foreach ($button_attributes as $button_attribute) {
          $option_name = $button_name . '_' . $button_attribute;
          $attribute = '';
          switch ($button_attribute) {
            case 'icon':
              if ($this->options[$option_name]) {
                $attribute =
                  $fontawesome_prefix .
                  ' ' .
                  $icon_prefix .
                  $this->options[$option_name];
              }
              break;

            case 'class':
              $attribute = $classes;
              break;

            case 'link':
              $attribute = $this->buildHref($button_name);
              $attr['href'] = $attribute;
              break;

            default:
              $attribute = $this->options[$option_name];
              break;
          }

          $attr[$button_attribute] = $attribute;
        }

        $buttons[$i] = $attr;
      }

      // Vocabularies
      // -------------------------------
      $vocabularies = [];

      for ($i = 1; $i <= 6; $i++) {
        $attr = [];
        $attr['active'] = false;

        $vocabulary_name = 'vocabulary_' . $i;

        if ($this->options[$vocabulary_name] !== '') {
          $machine_name = $this->options[$vocabulary_name];
          $label = '';
          $voc = Vocabulary::load($machine_name);
          if (!empty($voc)) {
            $label = $voc->label();
          }

          $attr['active'] = true;
          $attr['icon'] = $icon_taxonomy;
          $attr['label'] = $label;
          $attr['$machine_name'] = $machine_name;

          // href
          $href = "/admin/structure/taxonomy/manage/$machine_name/overview?destination=$view_path";
          $attr['href'] = $href;

          // class
          $attr['class'] = $button_classes;

          // modal
          $attr['modal'] = false;
        }
        $vocabularies[$i] = $attr;
      }

      return [
        '#theme' => 'vat_area',
        '#access' => $access,
        '#buttons' => $buttons,
        '#vocabularies' => $vocabularies,
        '#modal' => $modal,
        '#content' => $content,
        '#look' => $look,
      ];
    }
    return [];
  }

  /**
   * Render a text area with \Drupal\Component\Utility\Xss::filterAdmin().
   */
  public function renderTextField($value)
  {
    if ($value) {
      return $this->sanitizeValue($this->tokenizeValue($value), 'xss_admin');
    }
    return '';
  }

  public function buildHref($button_name)
  {
    $target = $this->options[$button_name . '_link'];
    $destination = $this->options[$button_name . '_destination'];

    if ($destination) {
      $link = $target . '?destination=' . $destination;
    } else {
      $link = $target;
    }

    return $link;
  }

  private function labelFirstRow($row_number, $label)
  {
    $output = '';

    if ($row_number === 1) {
      $output = $label;
    }
    return $output;
  }

}
