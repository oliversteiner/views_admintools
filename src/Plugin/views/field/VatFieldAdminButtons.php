<?php
  /**
   * @file
   * Definition of
   * Drupal\views_admintools\Plugin\views\field\VatFieldAdminButtons
   */

  namespace Drupal\views_admintools\Plugin\views\field;

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\views\Plugin\views\field\FieldPluginBase;
  use Drupal\Core\Url;
  use Drupal\views\ResultRow;

  /**
   * Field handler to add Edit and Delete Buttons.
   *
   * @ingroup views_field_handlers
   *
   * @ViewsField("vat_field_admin_buttons")
   */
  class VatFieldAdminButtons extends FieldPluginBase {

    /**
     * @{inheritdoc}
     */
    public function query() {
      // Leave empty to avoid a query on this field.
    }

    /**
     * Define the available options
     *
     * @return array
     */
    protected function defineOptions() {
      $options = parent::defineOptions();
      $options['row_button_edit'] = ['default' => TRUE];
      $options['row_button_delete'] = ['default' => TRUE];
      $options['row_button_label'] = ['default' => FALSE];
      $options['row_button_icon'] = ['default' => TRUE];
      $options['row_button_tag'] = ['default' => 'button'];
      $options['row_button_class'] = ['default' => FALSE];
      $options['destination_options'] = ['default' => 1]; // active View
      $options['destination_other'] = ['default' => '']; // active View
      $options['icon_set'] = ['default' => 0];    // Automatic
      $options['icon_size'] = ['default' => 1];  // normal


      return $options;
    }

    /**
     * Provide the options form.
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {

      //
      // ------------------------------
      $form['group_buttons'] = [
        '#markup' => '<div class="vat-views-option-group">' . $this->t('Show Buttons') . '</div>',
      ];


      $form['row_button_edit'] = [
        '#title' => $this->t('Edit'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['row_button_edit'],
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];

      $form['row_button_delete'] = [
        '#title' => $this->t('delete'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['row_button_delete'],
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',

      ];


      // Destination
      // ------------------------------
      $form['group_destination'] = [
        '#markup' => '<div class="vat-views-option-group">' . $this->t('Chose Destination') . '</div>',
      ];

      $options_destination = [
        'Show Content',
        'this view',
        '<content_type>_admin',
        'other view',
      ];

      $form['destination_options'] = [
        '#title' => $this->t('Chose destination after save'),
        '#type' => 'select',
        '#default_value' => $this->options['destination_options'],
        '#options' => $options_destination,
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',

      ];

      $form['destination_other'] = [
        '#title' => $this->t('Destination View id'),
        '#type' => 'textfield',
        '#default_value' => '',
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];

      // Button Look
      // ------------------------------

      $form['group_elements'] = [
        '#markup' => '<div class="vat-views-option-group">' . $this->t('Show') . '</div>',
      ];

      $form['row_button_label'] = [
        '#title' => $this->t('label'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['row_button_label'],
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];

      $form['row_button_icon'] = [
        '#title' => $this->t('icon'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['row_button_icon'],
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];


      // Design
      // ------------------------------

      $form['group_design'] = [
        '#markup' => '<div class="vat-views-option-group">' . $this->t('Design:') . '</div>',
      ];


      // Link or Button

      $options = ['button', 'link'];
      $form['row_button_tag'] = [
        '#title' => $this->t('Display as'),
        '#type' => 'select',
        '#default_value' => $this->options['row_button_tag'],
        '#options' => $options,
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];

      // Icon Set

      $options_icon_set = [
        'automatic',
        'Font Awesome',
        'Twitter Bootstrap',
        'Drupal / jQuery Ui',
      ];

      $form['icon_set'] = [
        '#title' => $this->t('Icon Set'),
        '#type' => 'select',
        '#default_value' => $this->options['icon_set'],
        '#options' => $options_icon_set,
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];


      // Icon Size
      // ------------------------------


      $options_icon_size = [
        'Large',
        'Normal',
        'Small',
      ];

      $form['icon_size'] = [
        '#title' => $this->t('Icon Size'),
        '#type' => 'select',
        '#default_value' => $this->options['icon_size'],
        '#options' => $options_icon_size,
        '#prefix' => '<div class="vat-views-option-inline">',
        '#suffix' => '</div>',
      ];

      $form['group_end'] = [
        '#markup' => '<div class="vat-views-option-group"></div>',
      ];


      parent::buildOptionsForm($form, $form_state);
    }

    /**
     * @{inheritdoc}
     */
    public function render(ResultRow $values) {

      $node = $values->_entity;
      $bundle = $node->bundle();
      $nid = $values->_entity->id();
      $display_path = $this->displayHandler->getPath();
      $buttons = ['edit', 'delete'];
      $elements = [];

      /*
       *  Destination View Options
       *     0) Show Content
       *     1) this view
       *     2) content_type>_admin
       *     3) other view
      */

      kint($this->options);

      switch ($this->options['destination_options']) {
        case 1:
          // this view
          $destination = '?destination=' . $display_path;
          break;
        case 2:
          // <content_type>_admin
          $destination = '?destination=' . $bundle . '_admin';
          break;
        case 3:
          // other view
          $path_other = $this->options['destination_other'];
          $destination = '?destination=' . $path_other;
          break;
        default:
          //Show Content
          $destination = '';
          break;
      }


      // Options Class
      $option_class = $this->options['row_button_class'];

      // Size Class
      switch ($this->options['icon_size']) {
        case 0:
          $option_class[] = 'vat-button-sm';
          break;
        case 1:
          $option_class[] = 'vat-button-md';
          break;
        case 2:
          $option_class[] = 'vat-button-lg';
          break;
        default:
          $option_class[] = '';
          break;

      }


      // All Classes
      $class = ['use-ajax', 'vat-button', $option_class];


      foreach ($buttons as $button_name) {

        // if Button selected
        $options_button_active = 'row_button_' . $button_name;

        if ($this->options[$options_button_active]) {


          $icon = '<span></span>';
          $label = '';


          // Link
          switch ($button_name) {

            case 'edit':
              $link = 'node/' . $nid . '/edit' . $destination;
              $icon_name['font_awesome'] = 'pencil';
              $icon_name['twitter_bootstrap'] = 'pencil';
              $icon_name['drupal'] = 'pencil';

              $class[] = 'vat-button-edit';
              break;

            case 'delete':
              $link = 'node/' . $nid . '/delete' . $destination;
              $icon_name['font_awesome'] = 'trash';
              $icon_name['twitter_bootstrap'] = 'trash';
              $icon_name['drupal'] = 'trash';

              $class[] = 'vat-button-delete';
              break;

            default:
              $link = 'node/' . $nid;
              $icon_name = FALSE;
              break;

          }


          // Options Icon
          if ($this->options['row_button_icon']) {

            /*
                        0)  'automatic'
                        1)  'Font Awesome'
                        2)  'Bootstrap'
                        3)  'Drupal / jQuery Ui'
            */

            $icon_elem['font_awesome'] = '<i class="fa fa-' . $icon_name['font_awesome'] . '" aria-hidden="true"></i>';
            $icon_elem['twitter_bootstap'] = '<span class="glyphicon glyphicon-' . $icon_name['twitter_bootstrap'] . '" aria-hidden="true"></span>';
            $icon_elem['drupal'] = '<span class="ui-icon ui-icon-' . $icon_name['drupal'] . '" aria-hidden="true"></span>';


            switch ($this->options['icon_set']) {

              // 'Font Awesome'
              case 1:
                $icon = $icon_elem['font_awesome'];
                break;

              // 'Bootstrap'
              case 2:
                $icon = $icon_elem['twitter_bootstrap'];
                break;

              // 'Drupal / jQuery Ui'
              case 3:
                $icon = $icon_elem['drupal'];
                break;

              //'automatic'
              default:

                // Font Awesome
                //
                if (\Drupal::moduleHandler()->moduleExists('fontawesome')) {
                  $icon = $icon_elem['font_awesome'];
                }
                // Twitter Bootstap 3
                elseif (\Drupal::moduleHandler()
                  ->moduleExists('bootstrap_library')) {
                  $icon = $icon_elem['twitter_bootstap'];
                }
                // Drupal Default / jQuery UI Icons
                else {
                  $icon = $icon_elem['drupal'];
                }
                break;

            }

          }

          // Options Label
          if ($this->options['row_button_label']) {
            $label = $this->t($button_name);
          }


          // Options display
          switch ($this->options['row_button_tag']) {

            case 'link':
              $class[] = 'link';
              break;

            default:
              $class[] = 'btn';
              $class[] = 'btn-sm';
              $class[] = 'btn-default';
              break;
          }
          // kint($icon);

          $title = ['#markup' => $icon . $label];

          $elements[$button_name] = [
            '#title' => $title,
            '#type' => 'link',
            '#url' => Url::fromUri('internal:/' . $link),
            '#attributes' => [
              'class' => $class,
              'data-dialog-type' => 'modal',
            ],
          ];
        }
      }

      $elements['#attached']['library'][] = 'views_admintools/views_admintools.enable';

      return $elements;

    }
  }
